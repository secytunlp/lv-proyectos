<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Purga interactiva de los ACTUAL DUPLICADO de investigador_carreras.
 *
 * Los detecta carreras:verificar-pivot. Vienen del bug 7 (bandera $esActual
 * inicializada fuera del loop en InvestigadorController) y del bug 2
 * (updateOrInsert con actual = 1 sin desmarcar las anteriores en
 * IntegranteController). Los dos estan corregidos desde el 2026-09-04, asi que
 * esto es limpieza de una vez.
 *
 * Por que interactivo y no un UPDATE masivo: el criterio no es "lo que dice
 * investigadors". La carrera de investigador es ASCENDENTE — nadie baja de
 * cargo — asi que el vigente es el cargo superior, y investigadors puede haber
 * quedado con el anterior si lo escribio un sync o una aprobacion de integrante.
 * El comando sugiere el superior cuando puede deducirlo, pero la decision es
 * de quien lo corre.
 *
 * Que hace al confirmar un caso:
 *   - actual = 0 en todas las filas del investigador
 *   - actual = 1 en la fila elegida
 *   - si el par (cargo, institucion) elegido difiere de investigadors, ofrece
 *     actualizar tambien investigadors.carrerainv_id + organismo_id
 *
 * Nunca borra filas: las anteriores de la progresion quedan como historial.
 */
class PurgarActualesCarreras extends Command
{
    protected $signature = 'carreras:purgar-actuales
        {--cuil= : Procesar solo este CUIL}
        {--saltear-n=0 : Saltear los primeros N casos (para retomar)}
        {--auto-superior : No preguntar cuando el cargo superior se puede deducir sin ambiguedad}
        {--dry-run : Mostrar que haria, sin escribir nada}
        {--resumen : Clasificar todos los casos sin preguntar nada, para decidir la estrategia}';

    protected $description = 'Purga interactiva de los actual duplicados en investigador_carreras';

    /**
     * Jerarquia del escalafon, por palabra clave del nombre. Es la misma para
     * Investigador y para Profesional:
     *   Asistente < Adjunto < Independiente < Principal < Superior
     *
     * No se usa la columna `orden` de carrerainvs: esta incompleta (hay filas en
     * NULL) y mezclarla con esta escala daba sugerencias invertidas.
     */
    private $rangos = array(
        'ASISTENTE'     => 1,
        'ADJUNTO'       => 2,
        'INDEPENDIENTE' => 3,
        'PRINCIPAL'     => 4,
        'SUPERIOR'      => 5,
    );

    private function cuilNorm($col)
    {
        return "REPLACE(REPLACE(REPLACE(".$col.", '-', ''), '.', ''), ' ', '')";
    }

    /** Escalafon: Investigador y Profesional son carreras distintas, no comparables */
    private function escalafon($nombre)
    {
        $n = strtoupper((string) $nombre);
        if (strpos($n, 'INVESTIGADOR') !== false) {
            return 'INVESTIGADOR';
        }
        if (strpos($n, 'PROFESIONAL') !== false) {
            return 'PROFESIONAL';
        }
        return 'OTRO';
    }

    /** Rango dentro del escalafon, o null si no se puede deducir */
    private function rango($nombre)
    {
        $n = strtoupper((string) $nombre);
        foreach ($this->rangos as $palabra => $peso) {
            if (strpos($n, $palabra) !== false) {
                return $peso;
            }
        }
        return null;
    }

    /**
     * Devuelve el indice de la fila con el cargo superior, o null si no se puede
     * deducir: escalafones distintos, algun nombre sin rango reconocible, o empate.
     */
    private function sugerir($filas)
    {
        $escalafones = array();
        $pesos       = array();

        foreach ($filas as $i => $f) {
            $escalafones[] = $this->escalafon($f->cargo_nombre);

            $r = $this->rango($f->cargo_nombre);
            if ($r === null) {
                return null;
            }
            $pesos[$i] = $r;
        }

        if (count(array_unique($escalafones)) > 1) {
            return null;   // Investigador vs Profesional: no son comparables
        }

        arsort($pesos);
        $orden = array_keys($pesos);
        $mejor = $orden[0];
        $segundo = $orden[1];

        if ($pesos[$mejor] === $pesos[$segundo]) {
            return null;   // empate: mismo cargo en dos instituciones, decide la persona
        }

        return $mejor;
    }

    private function etiqueta($f)
    {
        return trim(
            ($f->cargo_nombre === null ? 'CARGO?('.$f->carrerainv_id.')' : $f->cargo_nombre)
            .' | '.($f->organismo_nombre === null ? 'SIN INSTITUCION' : $f->organismo_nombre)
            .' | ingreso '.($f->ingreso === null ? 's/i' : $f->ingreso)
            .($f->actual ? ' | actual' : '')
        );
    }

    private function cabecera($invId)
    {
        return DB::selectOne(
            'SELECT i.id, p.cuil, '.
            "TRIM(CONCAT(COALESCE(p.apellido, ''), ', ', COALESCE(p.nombre, ''))) AS persona, ".
            'f.nombre AS ua, i.carrerainv_id, i.organismo_id, '.
            'ci.nombre AS inv_cargo, oi.codigo AS inv_organismo '.
            'FROM investigadors i '.
            'JOIN personas p ON p.id = i.persona_id '.
            'LEFT JOIN carrerainvs ci ON ci.id = i.carrerainv_id '.
            'LEFT JOIN organismos  oi ON oi.id = i.organismo_id '.
            'LEFT JOIN facultads   f  ON f.id  = i.facultad_id '.
            'WHERE i.id = ?',
            array($invId)
        );
    }

    private function filasDe($invId)
    {
        return DB::select(
            'SELECT ic.id, ic.carrerainv_id, ic.organismo_id, ic.ingreso, ic.actual, '.
            'cv.nombre AS cargo_nombre, '.
            'og.codigo AS organismo_nombre '.
            'FROM investigador_carreras ic '.
            'LEFT JOIN carrerainvs cv ON cv.id = ic.carrerainv_id '.
            'LEFT JOIN organismos  og ON og.id = ic.organismo_id '.
            'WHERE ic.investigador_id = ? '.
            'ORDER BY ic.ingreso IS NULL, ic.ingreso, ic.id',
            array($invId)
        );
    }

    /**
     * Clasifica todos los casos sin preguntar nada, para decidir la estrategia:
     *   A) sugerencia clara y coincide con investigadors  -> --auto-superior sin riesgo
     *   B) sugerencia clara pero difiere de investigadors -> auto tambien corrige investigadors
     *   C) sin sugerencia                                 -> a mano
     */
    private function resumir($casos)
    {
        $a = array();
        $b = array();
        $c = array();

        foreach ($casos as $caso) {
            $invId = (int) $caso->investigador_id;
            $cab   = $this->cabecera($invId);
            $filas = $this->filasDe($invId);
            $sug   = $this->sugerir($filas);

            if ($sug === null) {
                $c[] = array($invId, $this->corta($cab->persona, 30), $this->detalleFilas($filas));
                continue;
            }

            $f = $filas[$sug];
            $coincide = ((int) $f->carrerainv_id === (int) $cab->carrerainv_id
                && (int) $f->organismo_id === (int) $cab->organismo_id);

            if ($coincide) {
                $a[] = $invId;
            } else {
                $b[] = array(
                    $invId,
                    $this->corta($cab->persona, 28),
                    $this->corta(($cab->inv_cargo === null ? '(sin)' : $cab->inv_cargo)
                        .' '.($cab->inv_organismo === null ? '' : $cab->inv_organismo), 26),
                    $this->corta($f->cargo_nombre.' '.$f->organismo_nombre, 26),
                );
            }
        }

        $this->table(
            array('Grupo', 'Casos', 'Que pasa con --auto-superior'),
            array(
                array('A) superior = investigadors', count($a), 'Solo desmarca las otras filas'),
                array('B) superior <> investigadors', count($b), 'Ademas corrige investigadors'),
                array('C) sin sugerencia', count($c), 'Los saltea, quedan para vos'),
            )
        );

        if (count($b) > 0) {
            $this->line('');
            $this->warn('B) El cargo superior no es el que tiene investigadors ('.count($b).'):');
            $this->table(array('Inv.', 'Persona', 'investigadors dice', 'Superior del pivot'), $b);
        }

        if (count($c) > 0) {
            $this->line('');
            $this->warn('C) Sin sugerencia, hay que decidir a mano ('.count($c).'):');
            $this->table(array('Inv.', 'Persona', 'Filas actuales'), $c);
        }

        $this->line('');
        $this->line('Si B esta bien, "carreras:purgar-actuales --auto-superior" resuelve A y B');
        $this->line('y deja los de C para revisar de a uno.');

        return 0;
    }

    private function detalleFilas($filas)
    {
        $p = array();
        foreach ($filas as $f) {
            if (!$f->actual) {
                continue;
            }
            $p[] = ($f->cargo_nombre === null ? '?' : $f->cargo_nombre)
                .' '.($f->organismo_nombre === null ? '-' : $f->organismo_nombre);
        }
        return $this->corta(implode(' | ', $p), 55);
    }

    private function corta($v, $n)
    {
        $v = (string) $v;
        return mb_strlen($v) > $n ? mb_substr($v, 0, $n - 1).'.' : $v;
    }

    public function handle()
    {
        $cuil      = $this->option('cuil');
        $saltearN  = (int) $this->option('saltear-n');
        $auto      = (bool) $this->option('auto-superior');
        $dryRun    = (bool) $this->option('dry-run');

        $this->info('=== PURGA INTERACTIVA DE ACTUALES DUPLICADOS (CARRERAS) ===');
        $this->line('');
        $this->line('Jerarquia: Asistente < Adjunto < Independiente < Principal < Superior,');
        $this->line('la misma para Investigador y para Profesional.');
        $this->line('Investigador y Profesional son escalafones distintos: ahi no se sugiere nada.');
        if ($dryRun) {
            $this->warn('DRY-RUN: no se escribe nada.');
        }
        $this->line('');

        // investigadores con mas de una fila actual
        $sqlIds =
            'SELECT ic.investigador_id, COUNT(*) AS n '.
            'FROM investigador_carreras ic '.
            'JOIN investigadors i ON i.id = ic.investigador_id '.
            'JOIN personas p ON p.id = i.persona_id '.
            'WHERE ic.actual = 1 ';
        $bind = array();
        if ($cuil !== null && $cuil !== '') {
            $sqlIds .= 'AND '.$this->cuilNorm('p.cuil').' = '.$this->cuilNorm('?').' ';
            $bind[] = $cuil;
        }
        $sqlIds .= 'GROUP BY ic.investigador_id HAVING COUNT(*) > 1 ORDER BY ic.investigador_id';

        $casos = DB::select($sqlIds, $bind);

        if (count($casos) === 0) {
            $this->info('No hay investigadores con mas de una fila actual. Nada que hacer.');
            return 0;
        }

        $this->info('Casos a revisar: '.count($casos));
        $this->line('');

        if ($this->option('resumen')) {
            return $this->resumir($casos);
        }

        if (!$dryRun) {
            $ts = date('YmdHis');
            $bkCarreras = 'investigador_carreras_backup_'.$ts;
            $bkInv      = 'investigadors_backup_carreras_'.$ts;

            $this->line('Backup...');
            DB::statement("CREATE TABLE $bkCarreras LIKE investigador_carreras");
            DB::statement("INSERT INTO $bkCarreras SELECT * FROM investigador_carreras");
            DB::statement("CREATE TABLE $bkInv LIKE investigadors");
            DB::statement("INSERT INTO $bkInv SELECT * FROM investigadors");
            $this->line('   '.$bkCarreras);
            $this->line('   '.$bkInv);
            $this->line('');
        }

        $procesados = 0;
        $saltados   = 0;
        $automaticos = 0;

        foreach ($casos as $idx => $caso) {
            if ($idx < $saltearN) {
                $saltados++;
                continue;
            }

            $invId = (int) $caso->investigador_id;

            $cab   = $this->cabecera($invId);
            $filas = $this->filasDe($invId);

            $this->line('');
            $this->info('['.($idx + 1).'/'.count($casos).'] '.$cab->persona.'  (inv '.$invId.', CUIL '.$cab->cuil.')');
            $this->line('   U. Acad.: '.($cab->ua === null ? '-' : $cab->ua));
            $this->line('   investigadors dice: '
                .($cab->inv_cargo === null ? '(sin cargo)' : $cab->inv_cargo)
                .' | '.($cab->inv_organismo === null ? 'sin institucion' : $cab->inv_organismo));

            $rows = array();
            foreach ($filas as $i => $f) {
                $coincide = ((int) $f->carrerainv_id === (int) $cab->carrerainv_id
                    && (int) $f->organismo_id === (int) $cab->organismo_id) ? 'si' : '';
                $rows[] = array(
                    $i + 1,
                    $f->cargo_nombre === null ? 'CARGO?('.$f->carrerainv_id.')' : $f->cargo_nombre,
                    $f->organismo_nombre === null ? '-' : $f->organismo_nombre,
                    $f->ingreso === null ? 's/i' : $f->ingreso,
                    $f->actual ? '*' : '',
                    $coincide,
                );
            }
            $this->table(array('#', 'Cargo', 'Institucion', 'Ingreso', 'Actual', '= inv.'), $rows);

            $sug = $this->sugerir($filas);
            if ($sug !== null) {
                $this->line('   Sugerido (cargo superior): #'.($sug + 1).'  '.$filas[$sug]->cargo_nombre);
            } else {
                $this->warn('   Sin sugerencia: escalafones distintos, empate, o cargo no reconocido.');
            }

            // eleccion
            $elegido = null;

            if ($auto && $sug !== null) {
                $elegido = $sug;
                $automaticos++;
                $this->line('   --auto-superior: se toma #'.($sug + 1));
            } else {
                $opciones = array();
                foreach ($filas as $i => $f) {
                    $opciones[] = ($i + 1).') '.$this->etiqueta($f);
                }
                $opciones[] = 'Saltear este caso';
                $opciones[] = 'Cancelar y salir';

                $default = $sug !== null ? $opciones[$sug] : $opciones[count($opciones) - 2];

                $resp = $this->choice('   Cual queda como actual?', $opciones, $default);

                if (strpos($resp, 'Cancelar') === 0) {
                    $this->line('');
                    $this->warn('Cancelado. Procesados: '.$procesados.', saltados: '.$saltados.'.');
                    return 0;
                }
                if (strpos($resp, 'Saltear') === 0) {
                    $saltados++;
                    continue;
                }

                $elegido = (int) substr($resp, 0, strpos($resp, ')')) - 1;
            }

            $fila = $filas[$elegido];

            $difiere = ((int) $fila->carrerainv_id !== (int) $cab->carrerainv_id
                || (int) $fila->organismo_id !== (int) $cab->organismo_id);

            $tocarInv = false;
            if ($difiere) {
                $this->warn('   El elegido NO coincide con investigadors.');
                if ($auto) {
                    $tocarInv = true;
                    $this->line('   --auto-superior: se actualiza investigadors tambien.');
                } else {
                    $tocarInv = $this->confirm('   Actualizar tambien investigadors a este cargo/institucion?', true);
                }
            }

            if ($dryRun) {
                $this->line('   [dry-run] quedaria actual la fila #'.($elegido + 1)
                    .($tocarInv ? ' + investigadors actualizado' : ''));
                $procesados++;
                continue;
            }

            DB::beginTransaction();
            try {
                DB::table('investigador_carreras')
                    ->where('investigador_id', $invId)
                    ->where('actual', 1)
                    ->update(array('actual' => 0, 'updated_at' => now()));

                DB::table('investigador_carreras')
                    ->where('id', $fila->id)
                    ->update(array('actual' => 1, 'updated_at' => now()));

                if ($tocarInv) {
                    DB::table('investigadors')
                        ->where('id', $invId)
                        ->update(array(
                            'carrerainv_id' => $fila->carrerainv_id,
                            'organismo_id'  => $fila->organismo_id,
                            'updated_at'    => now(),
                        ));
                }

                DB::commit();
                $procesados++;
                $this->line('   OK: queda actual '.$this->etiqueta($fila));
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error('   ERROR, no se toco nada en este caso: '.$e->getMessage());
            }
        }

        $this->line('');
        $this->info('Listo. Procesados: '.$procesados.', saltados: '.$saltados
            .($auto ? ', automaticos: '.$automaticos : '').'.');
        $this->line('Volve a correr carreras:verificar-pivot para confirmar.');

        return 0;
    }
}
