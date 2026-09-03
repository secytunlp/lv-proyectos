<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Control de las categorias SPU (Programa de Incentivos), el equivalente de
 * sicadi:comparar-* / sicadi:verificar-pivot para el otro eje de categorias.
 *
 * Diferencia de fondo con SICADI: las categorias SPU NO tienen tabla de
 * solicitudes. La fuente de verdad es el sistema viejo (docente.cd_categoria en
 * mysql_origen), no una tabla de produccion. Por eso son dos controles y no
 * tres, y el criterio de desempate se invierte: si investigadors y el origen
 * difieren, gana el origen, porque SyncInvestigadors lo va a pisar en la
 * proxima corrida.
 *
 *   1) ORIGEN   investigadors.categoria_id  <->  docente.cd_categoria
 *   2) PIVOT    investigadors.categoria_id  <->  investigador_categorias
 *
 * SyncInvestigadors escribe categoria_id con `?: null`, asi que 0, '' y NULL
 * en el origen son todos "sin categoria". Ademas el catalogo `categorias` tiene
 * una fila `s/c` que significa lo mismo: la comparacion normaliza las tres
 * formas a "sin categoria", igual que los comandos de SICADI hacen con el
 * sicadi_id = 1. Sin esa normalizacion el control 2 devuelve ~12800 SIN PIVOT
 * que son simplemente gente que nunca se categorizo.
 * El pivot no lo sincroniza nadie.
 *
 * Solo lee: no modifica nada.
 */
class VerificarCategorias extends Command
{
    protected $signature = 'categorias:verificar
        {--cuil= : Filtrar por un CUIL puntual}
        {--solo= : Mostrar solo los diagnosticos que contengan este texto}
        {--sin-origen : Saltear el control contra el sistema viejo}
        {--sin-pivot : Saltear el control contra el pivot}
        {--incluir-sin-origen : Listar tambien los investigadores que no existen en docente}
        {--incluir-sin-year : Marcar tambien las filas de pivot sin year}
        {--incluir-sc : No tratar s/c como "sin categoria" (muestra el ruido)}
        {--limite=50 : Cortar cada listado en N filas (0 = sin limite)}';

    protected $description = 'Verifica las categorias SPU contra el sistema viejo y contra investigador_categorias';

    private $catNombres = array();
    private $scIds = array();
    private $tratarSc = true;

    /** 0, NULL y s/c son la misma cosa: "sin categoria" */
    private function norm($id)
    {
        $id = (int) $id;
        if ($id === 0) {
            return 0;
        }
        if ($this->tratarSc && in_array($id, $this->scIds, true)) {
            return 0;
        }
        return $id;
    }

    private function cuilNorm($col)
    {
        return "REPLACE(REPLACE(REPLACE(".$col.", '-', ''), '.', ''), ' ', '')";
    }

    private function cat($id)
    {
        $id = (int) $id;
        if ($id === 0) {
            return '(sin)';
        }
        return isset($this->catNombres[$id]) ? $this->catNombres[$id] : ('INEXISTENTE('.$id.')');
    }

    public function handle()
    {
        $cuil   = $this->option('cuil');
        $solo   = $this->option('solo');
        $limite = (int) $this->option('limite');

        $this->tratarSc = !$this->option('incluir-sc');

        $sinCat = array('S/C', 'SC', 'S/D', 'SIN CATEGORIA', 'SIN CATEGORÍA', '-', 'NO POSEE');
        foreach (DB::table('categorias')->select('id', 'nombre')->get() as $c) {
            $this->catNombres[(int) $c->id] = $c->nombre;
            if (in_array(strtoupper(trim($c->nombre)), $sinCat, true)) {
                $this->scIds[] = (int) $c->id;
            }
        }
        if ($this->tratarSc && count($this->scIds) > 0) {
            $etiquetas = array();
            foreach ($this->scIds as $id) {
                $etiquetas[] = $this->catNombres[$id].' (id '.$id.')';
            }
            $this->line('Tratando como "sin categoria": '.implode(', ', $etiquetas).'. Usa --incluir-sc para no hacerlo.');
        }

        $huboAlgo = false;

        if (!$this->option('sin-origen')) {
            $huboAlgo = $this->controlOrigen($cuil, $solo, $limite) || $huboAlgo;
        }
        if (!$this->option('sin-pivot')) {
            $huboAlgo = $this->controlPivot($cuil, $solo, $limite) || $huboAlgo;
        }

        if (!$huboAlgo) {
            $this->line('');
            $this->info('Sin diferencias en los controles ejecutados.');
        }

        return 0;
    }

    // ------------------------------------------------------------------
    // 1) investigadors.categoria_id  <->  docente.cd_categoria
    // ------------------------------------------------------------------
    private function controlOrigen($cuil, $solo, $limite)
    {
        $this->line('');
        $this->info('=== 1. Contra el sistema viejo (docente.cd_categoria) ===');

        $incluirSinOrigen = (bool) $this->option('incluir-sin-origen');

        $q = DB::table('investigadors as i')
            ->leftJoin('personas as p', 'p.id', '=', 'i.persona_id')
            ->leftJoin('facultads as f', 'f.id', '=', 'i.facultad_id')
            ->select('i.id', 'i.categoria_id', 'p.cuil', 'p.apellido', 'p.nombre', 'f.nombre as ua')
            ->orderBy('i.id');

        if ($cuil !== null && $cuil !== '') {
            $q->whereRaw($this->cuilNorm('p.cuil').' = '.$this->cuilNorm('?'), array($cuil));
        }

        $rows      = array();
        $resumen   = array();
        $revisados = 0;
        $conDif    = 0;
        $sinOrigen = 0;

        try {
            $q->chunk(1000, function ($chunk) use (
                &$rows, &$resumen, &$revisados, &$conDif, &$sinOrigen,
                $solo, $incluirSinOrigen
            ) {
                $ids = array();
                foreach ($chunk as $r) {
                    $ids[] = (int) $r->id;
                }

                $orig = array();
                $res = DB::connection('mysql_origen')
                    ->table('docente')
                    ->whereIn('cd_docente', $ids)
                    ->select('cd_docente', 'cd_categoria')
                    ->get();
                foreach ($res as $o) {
                    $orig[(int) $o->cd_docente] = (int) $o->cd_categoria;
                }

                foreach ($chunk as $r) {
                    $revisados++;
                    $id    = (int) $r->id;
                    $local = $this->norm($r->categoria_id);   // 0 = sin categoria

                    if (!array_key_exists($id, $orig)) {
                        $sinOrigen++;
                        if (!$incluirSinOrigen) {
                            continue;
                        }
                        $diag = 'NO EXISTE EN ORIGEN';
                        $origTxt = '-';
                    } else {
                        $remoto = $this->norm($orig[$id]);   // 0 = sin categoria
                        if ($remoto === $local) {
                            continue;
                        }
                        if ($local === 0) {
                            $diag = 'FALTA EN LOCAL';
                        } elseif ($remoto === 0) {
                            $diag = 'SOBRA EN LOCAL';
                        } else {
                            $diag = 'DISTINTA';
                        }
                        $origTxt = $this->cat($orig[$id]);
                    }

                    $conDif++;
                    if (!isset($resumen[$diag])) {
                        $resumen[$diag] = 0;
                    }
                    $resumen[$diag]++;

                    if ($solo !== null && $solo !== '' && stripos($diag, $solo) === false) {
                        continue;
                    }
                    if (count($rows) >= 2000) {
                        continue;   // tope de memoria; el resumen sigue contando
                    }

                    $rows[] = array(
                        $id,
                        $r->cuil,
                        $this->corta(trim($r->apellido.', '.$r->nombre), 30),
                        $this->corta($r->ua, 14),
                        $this->cat((int) $r->categoria_id),
                        $origTxt,
                        $diag,
                    );
                }
            });
        } catch (\Exception $e) {
            $this->error('No se pudo consultar mysql_origen: '.$e->getMessage());
            return false;
        }

        if ($conDif === 0) {
            $this->info($revisados.' investigadores revisados: categoria_id coincide con el origen en todos.');
            if ($sinOrigen > 0) {
                $this->line($sinOrigen.' no existen en docente (altas locales); usa --incluir-sin-origen para verlos.');
            }
            return false;
        }

        $this->imprimir(
            $rows,
            array('Inv.', 'CUIL', 'Persona', 'U. Acad.', 'Local', 'Origen', 'Diagnostico'),
            $limite
        );
        $this->resumir($resumen, $revisados.' investigadores', $conDif);

        $this->line('');
        $this->line('Gana el ORIGEN: SyncInvestigadors reescribe categoria_id en cada corrida.');
        $this->line('FALTA EN LOCAL / SOBRA EN LOCAL / DISTINTA se corrigen solas en el proximo sync;');
        $this->line('si el valor bueno es el local, hay que cargarlo en docente.cd_categoria, no aca.');
        if ($sinOrigen > 0 && !$this->option('incluir-sin-origen')) {
            $this->line($sinOrigen.' investigadores no existen en docente (altas locales), no listados.');
        }

        return true;
    }

    // ------------------------------------------------------------------
    // 2) investigadors.categoria_id  <->  investigador_categorias
    // ------------------------------------------------------------------
    private function controlPivot($cuil, $solo, $limite)
    {
        $this->line('');
        $this->info('=== 2. Contra el pivot (investigador_categorias) ===');

        $incluirSinYear = (bool) $this->option('incluir-sin-year');

        $sql =
            'SELECT '.
            '  i.id AS investigador_id, p.cuil AS cuil, '.
            "  TRIM(CONCAT(COALESCE(p.apellido, ''), ', ', COALESCE(p.nombre, ''))) AS persona, ".
            '  f.nombre AS ua, i.categoria_id AS inv_cat_id, ca.nombre AS inv_cat, '.
            '  pv.filas, pv.actuales, pv.sin_year, pv.years_distintos, pv.act_ids, pv.detalle '.
            'FROM investigadors i '.
            'JOIN personas p ON p.id = i.persona_id '.
            'LEFT JOIN categorias ca ON ca.id = i.categoria_id '.
            'LEFT JOIN facultads f  ON f.id = i.facultad_id '.
            'LEFT JOIN ( '.
            '   SELECT ic.investigador_id, '.
            '          COUNT(*) AS filas, '.
            '          SUM(ic.actual = 1) AS actuales, '.
            '          SUM(ic.year IS NULL) AS sin_year, '.
            '          COUNT(DISTINCT COALESCE(ic.year, 0)) AS years_distintos, '.
            '          GROUP_CONCAT(DISTINCT CASE WHEN ic.actual = 1 THEN ic.categoria_id END) AS act_ids, '.
            "          GROUP_CONCAT(CONCAT(COALESCE(cp.nombre, '?'), ' ', COALESCE(ic.year, 's/a'), ".
            "                              CASE WHEN ic.actual = 1 THEN '*' ELSE '' END) ".
            "                       ORDER BY ic.year DESC SEPARATOR ' | ') AS detalle ".
            '   FROM investigador_categorias ic '.
            '   LEFT JOIN categorias cp ON cp.id = ic.categoria_id '.
            '   GROUP BY ic.investigador_id '.
            ') pv ON pv.investigador_id = i.id '.
            'WHERE ( pv.investigador_id IS NOT NULL OR i.categoria_id IS NOT NULL ) ';

        $bind = array();
        if ($cuil !== null && $cuil !== '') {
            $sql .= 'AND '.$this->cuilNorm('p.cuil').' = '.$this->cuilNorm('?').' ';
            $bind[] = $cuil;
        }
        $sql .= 'ORDER BY persona';

        $filas = DB::select($sql, $bind);

        $rows      = array();
        $resumen   = array();
        $conDif    = 0;
        $revisados = 0;

        foreach ($filas as $f) {
            $revisados++;

            $invIdRaw = (int) $f->inv_cat_id;
            $invId    = $this->norm($invIdRaw);
            $invTiene = ($invId !== 0);
            $pvFilas  = (int) $f->filas;
            $actuales = (int) $f->actuales;

            $actIds = array();
            if ($f->act_ids !== null && $f->act_ids !== '') {
                foreach (explode(',', $f->act_ids) as $v) {
                    $n = $this->norm($v);
                    if ($n !== 0) {
                        $actIds[] = $n;
                    }
                }
            }
            $actuales = count($actIds) > 0 ? $actuales : 0;   // actual s/c = sin actual real

            $marcas = array();

            if ($invIdRaw !== 0 && !isset($this->catNombres[$invIdRaw])) {
                $marcas[] = 'CATEGORIA INEXISTENTE ('.$invIdRaw.')';
            }

            if ($pvFilas === 0) {
                if ($invTiene) {
                    $marcas[] = 'SIN PIVOT';
                }
            } elseif ($actuales === 0) {
                if ($invTiene) {
                    $marcas[] = 'SIN ACTUAL';
                }
            } elseif ($actuales > 1) {
                $marcas[] = 'ACTUAL DUPLICADO ('.$actuales.')';
                if ($invTiene && !in_array($invId, $actIds, true)) {
                    $marcas[] = 'INV <> PIVOT';
                }
            } else {
                $actId = $actIds[0];
                if ($invTiene) {
                    if ($actId !== $invId) {
                        $marcas[] = 'INV <> PIVOT';
                    }
                } else {
                    $marcas[] = 'PIVOT SIN INV';
                }
            }

            if ($pvFilas > (int) $f->years_distintos) {
                $marcas[] = 'ANIO DUPLICADO';
            }
            if ($incluirSinYear && (int) $f->sin_year > 0) {
                $marcas[] = 'SIN YEAR ('.(int) $f->sin_year.')';
            }

            if (count($marcas) === 0) {
                continue;
            }

            $diag = implode(' + ', $marcas);
            $conDif++;
            if (!isset($resumen[$diag])) {
                $resumen[$diag] = 0;
            }
            $resumen[$diag]++;

            if ($solo !== null && $solo !== '' && stripos($diag, $solo) === false) {
                continue;
            }

            $rows[] = array(
                (int) $f->investigador_id,
                $f->cuil,
                $this->corta($f->persona, 30),
                $this->corta($f->ua, 14),
                $this->cat($invIdRaw),
                $f->detalle === null ? '-' : $this->corta($f->detalle, 42),
                $diag,
            );
        }

        if ($conDif === 0) {
            $this->info('Pivot consistente: '.$revisados.' investigadores con categoria o con pivot, ninguna diferencia.');
            return false;
        }

        $this->imprimir(
            $rows,
            array('Inv.', 'CUIL', 'Persona', 'U. Acad.', 'Investigador', 'Pivot (* = actual)', 'Diagnostico'),
            $limite
        );
        $this->resumir($resumen, $revisados.' investigadores con categoria o con pivot', $conDif);

        $this->line('');
        $this->line('El pivot SPU no lo sincroniza nadie: es el unico lugar donde vive el historial,');
        $this->line('y por eso se desfasa de categoria_id, que el sync reescribe todos los dias.');
        $this->line('Antes de tocar el pivot, correr el control 1: si el origen manda otra categoria,');
        $this->line('cualquier arreglo local dura hasta la proxima corrida.');

        return true;
    }

    // ------------------------------------------------------------------

    private function imprimir($rows, $cab, $limite)
    {
        if (count($rows) === 0) {
            $this->info('Sin filas para mostrar con ese --solo.');
            return;
        }
        $recortado = false;
        if ($limite > 0 && count($rows) > $limite) {
            $rows = array_slice($rows, 0, $limite);
            $recortado = true;
        }
        $this->table($cab, $rows);
        if ($recortado) {
            $this->warn('Listado recortado a '.$limite.' filas (usa --limite=0 para verlas todas).');
        }
    }

    private function resumir($resumen, $universo, $conDif)
    {
        arsort($resumen);
        $res = array();
        foreach ($resumen as $d => $c) {
            $res[] = array($d, $c);
        }
        $this->line('');
        $this->info('Sobre '.$universo.':');
        $this->table(array('Diagnostico', 'Cantidad'), $res);
        $this->line('Con alguna diferencia: '.$conDif);
    }

    private function corta($v, $n)
    {
        $v = (string) $v;
        return mb_strlen($v) > $n ? mb_substr($v, 0, $n - 1).'.' : $v;
    }
}
