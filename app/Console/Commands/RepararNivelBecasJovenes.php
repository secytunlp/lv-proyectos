<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Repone el nivel de beca (joven_becas.beca) que quedó en blanco.
 *
 * El <select> de nivel de las becas anteriores se armaba con
 * BecaHelper::obtenerOpcionesBecaPorInstitucionAnterior(), que buscaba la institución con
 * las claves de la beca actual ("CIC PBA", "AGENCIA i+D+i") mientras el formulario mandaba
 * las suyas ("CIC", "ANPCyT"). Para esas dos instituciones el select salía sin opciones, y
 * al guardar posteaba vacío y pisaba el nivel que venía precargado del investigador. El
 * helper ya está arreglado; esto es para lo que quedó escrito en blanco.
 *
 * El nivel se busca, en este orden:
 *   1. investigador_becas del mismo investigador, misma institución y mismo "desde";
 *   2. investigador_becas del mismo investigador, misma institución, períodos solapados;
 *   3. otra fila de joven_becas del mismo investigador (otra solicitud), misma institución
 *      y mismo "desde", con el nivel cargado.
 *
 * Dry-run por defecto. Escribe sólo con --commit.
 */
class RepararNivelBecasJovenes extends Command
{
    protected $signature = 'jovens:reparar-nivel-becas
        {--anio= : Periodo de las solicitudes. Vacio = todos}
        {--cuil= : Una sola persona (los guiones se ignoran)}
        {--joven= : Una sola solicitud, por joven_id}
        {--commit : Escribe los cambios. Sin esto solo informa}';

    protected $description = 'Repone el nivel de beca en blanco de joven_becas, desde investigador_becas';

    /**
     * Las dos grafías con que se escribe la misma institución en el sistema.
     */
    private static $equivalencias = [
        'CIC PBA'       => 'CIC',
        'AGENCIA I+D+I' => 'ANPCYT',
    ];

    private function normalizar($institucion)
    {
        $institucion = strtoupper(trim((string) $institucion));

        return isset(self::$equivalencias[$institucion])
            ? self::$equivalencias[$institucion]
            : $institucion;
    }

    private function fecha($valor)
    {
        $valor = substr((string) $valor, 0, 10);

        return ($valor === '' || strpos($valor, '0000-00-00') === 0) ? '' : $valor;
    }

    private function vacio($beca)
    {
        $beca = trim((string) $beca);

        return ($beca === '' || $beca === '0');
    }

    public function handle(): int
    {
        $anio   = trim((string) $this->option('anio'));
        $cuil   = preg_replace('/\D/', '', (string) $this->option('cuil'));
        $jovenId = trim((string) $this->option('joven'));
        $commit = (bool) $this->option('commit');

        $query = DB::table('joven_becas as jb')
            ->join('jovens as j', 'j.id', '=', 'jb.joven_id')
            ->leftJoin('periodos as pe', 'pe.id', '=', 'j.periodo_id')
            ->leftJoin('investigadors as i', 'i.id', '=', 'j.investigador_id')
            ->leftJoin('personas as p', 'p.id', '=', 'i.persona_id')
            ->select(
                'jb.id', 'jb.joven_id', 'jb.institucion', 'jb.beca', 'jb.desde', 'jb.hasta',
                'jb.actual', 'jb.agregada', 'j.investigador_id', 'j.estado',
                'pe.nombre as periodo', 'p.apellido', 'p.nombre as nombre_persona', 'p.cuil'
            )
            ->where(function ($q) {
                $q->whereNull('jb.beca')
                    ->orWhere('jb.beca', '')
                    ->orWhere('jb.beca', '0');
            });

        if ($anio !== '') {
            $query->where('pe.nombre', $anio);
        }
        if ($cuil !== '') {
            $query->whereRaw("REPLACE(REPLACE(p.cuil, '-', ''), ' ', '') = ?", [$cuil]);
        }
        if ($jovenId !== '') {
            $query->where('jb.joven_id', (int) $jovenId);
        }

        $filas = $query->orderBy('p.apellido')->orderBy('jb.id')->get();

        $this->info('Filas de joven_becas sin nivel'
            .($anio !== '' ? ', periodo '.$anio : '')
            .': '.$filas->count());

        if ($filas->isEmpty()) {
            $this->line('Nada para reparar.');
            return self::SUCCESS;
        }

        $invIds = [];
        foreach ($filas as $fila) {
            if ($fila->investigador_id !== null) {
                $invIds[(int) $fila->investigador_id] = true;
            }
        }
        $invIds = array_keys($invIds);

        // Fuente 1 y 2: las becas del investigador
        $becasInv = [];
        if (!empty($invIds)) {
            $rows = DB::table('investigador_becas')
                ->select('id', 'investigador_id', 'institucion', 'beca', 'desde', 'hasta')
                ->whereIn('investigador_id', $invIds)
                ->get();
            foreach ($rows as $row) {
                if ($this->vacio($row->beca)) {
                    continue;
                }
                $becasInv[(int) $row->investigador_id][] = $row;
            }
        }

        // Fuente 3: lo que la misma persona declaró en otras solicitudes
        $becasJoven = [];
        if (!empty($invIds)) {
            $rows = DB::table('joven_becas as jb')
                ->join('jovens as j', 'j.id', '=', 'jb.joven_id')
                ->select('jb.id', 'j.investigador_id', 'jb.institucion', 'jb.beca', 'jb.desde', 'jb.hasta')
                ->whereIn('j.investigador_id', $invIds)
                ->get();
            foreach ($rows as $row) {
                if ($this->vacio($row->beca)) {
                    continue;
                }
                $becasJoven[(int) $row->investigador_id][] = $row;
            }
        }

        $informe   = [];
        $aEscribir = [];

        foreach ($filas as $fila) {
            $iid         = (int) $fila->investigador_id;
            $institucion = $this->normalizar($fila->institucion);
            $desde       = $this->fecha($fila->desde);
            $hasta       = $this->fecha($fila->hasta);

            $candidatos = [];   // nivel => origen

            $fuentes = [
                'investigador_becas' => isset($becasInv[$iid]) ? $becasInv[$iid] : [],
                'otra solicitud'     => isset($becasJoven[$iid]) ? $becasJoven[$iid] : [],
            ];

            // 1) misma institucion + mismo desde
            foreach ($fuentes as $origen => $rows) {
                foreach ($rows as $row) {
                    if ($this->normalizar($row->institucion) !== $institucion) {
                        continue;
                    }
                    if ($desde !== '' && $this->fecha($row->desde) === $desde) {
                        $candidatos[trim((string) $row->beca)] = $origen.' (mismo desde)';
                    }
                }
                if (!empty($candidatos)) {
                    break;
                }
            }

            // 2) misma institucion + periodos solapados, solo en investigador_becas
            if (empty($candidatos) && $desde !== '' && $hasta !== '') {
                foreach ($fuentes['investigador_becas'] as $row) {
                    if ($this->normalizar($row->institucion) !== $institucion) {
                        continue;
                    }
                    $rDesde = $this->fecha($row->desde);
                    $rHasta = $this->fecha($row->hasta);
                    if ($rDesde === '' || $rHasta === '') {
                        continue;
                    }
                    if ($rDesde <= $hasta && $rHasta >= $desde) {
                        $candidatos[trim((string) $row->beca)] = 'investigador_becas (solapada)';
                    }
                }
            }

            $niveles = array_keys($candidatos);

            if (count($niveles) === 1) {
                $nivel  = $niveles[0];
                $origen = $candidatos[$nivel];
                $aEscribir[] = ['id' => (int) $fila->id, 'beca' => $nivel];
            } elseif (count($niveles) > 1) {
                $nivel  = '';
                $origen = 'AMBIGUO: '.implode(' | ', $niveles);
            } else {
                $nivel  = '';
                $origen = 'SIN CANDIDATO';
            }

            $informe[] = [
                $fila->id,
                $fila->joven_id,
                $fila->periodo,
                trim($fila->apellido.', '.$fila->nombre_persona),
                (string) $fila->cuil,
                $fila->institucion,
                $desde.' → '.$hasta,
                ($fila->actual ? 'actual' : 'anterior').($fila->agregada ? ' (agregada)' : ''),
                ($nivel !== '' ? $nivel : '—'),
                $origen,
            ];
        }

        $this->newLine();
        $this->table(
            ['jb.id', 'Joven', 'Periodo', 'Apellido, Nombre', 'CUIL', 'Institucion', 'Periodo beca', 'Tipo', 'Nivel a escribir', 'Origen'],
            $informe
        );

        $this->newLine();
        $this->line('Con nivel recuperable : '.count($aEscribir));
        $this->line('Sin candidato/ambiguo : '.(count($informe) - count($aEscribir)));

        if (!$commit) {
            $this->newLine();
            $this->warn('Dry-run: no se escribió nada. Volvé a correrlo con --commit para aplicar.');
            return self::SUCCESS;
        }

        if (empty($aEscribir)) {
            $this->warn('No hay nada que escribir.');
            return self::SUCCESS;
        }

        $escritas = 0;
        DB::beginTransaction();
        try {
            foreach ($aEscribir as $cambio) {
                $escritas += DB::table('joven_becas')
                    ->where('id', $cambio['id'])
                    ->update(['beca' => $cambio['beca'], 'updated_at' => now()]);
            }
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            $this->error('No se escribió nada: '.$ex->getMessage());
            return self::FAILURE;
        }

        $this->info('Filas actualizadas: '.$escritas);

        return self::SUCCESS;
    }
}
