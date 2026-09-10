<?php

namespace App\Console\Commands;

use App\Constants;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

/**
 * Cruza las solicitudes de Jovenes Investigadores de un periodo contra
 * investigador_becas, para detectar los casos que salen mal en el PDF:
 *
 *   FALTA INVESTIGADOR_BECA  el joven declara beca en la solicitud pero el
 *                            investigador no tiene la beca cargada -> el PDF
 *                            muestra el bloque BECARIO pero RESUMEN vacio.
 *   SIN RESUMEN              la beca esta cargada pero sin resumen -> idem.
 *   DESACUERDO               las dos existen pero difieren en tipo o fechas.
 *   SOLO INVESTIGADOR_BECA   el investigador tiene beca del periodo y la
 *                            solicitud no la declara.
 *   SIN BECA                 no hay beca por ningun lado (puede ser correcto:
 *                            no todos los jovenes son becarios).
 *   OK                       beca declarada, cargada y con resumen.
 *
 * Solo lee. No modifica nada.
 */
class AuditarBecasJovenes extends Command
{
    protected $signature = 'jovens:auditar-becas
        {--anio= : Periodo de las solicitudes. Por defecto Constants::YEAR_JOVENES}
        {--fecha-referencia= : Beca vigente a esta fecha (Y-m-d). Por defecto <anio>-04-01}
        {--estado= : Filtra por estado de la solicitud. Vacio = todos}
        {--solo= : Muestra solo las filas cuyo diagnostico contenga este texto}
        {--salida= : Ruta del .xlsx de salida}';

    protected $description = 'Audita las becas de los jovenes de un periodo contra investigador_becas';

    private const HEADERS = [
        'Joven ID', 'Estado', 'Apellido', 'Nombre', 'Documento', 'CUIL', 'Facultad',
        'Solicitud: institucion', 'Solicitud: beca', 'Solicitud: desde', 'Solicitud: hasta',
        'Investigador: beca id', 'Investigador: institucion', 'Investigador: beca',
        'Investigador: desde', 'Investigador: hasta', 'Tiene resumen',
        'Diagnostico', 'Detalle',
    ];

    private function fecha($valor)
    {
        $valor = substr((string) $valor, 0, 10);
        return ($valor === '' || strpos($valor, '0000-00-00') === 0) ? '' : $valor;
    }

    public function handle(): int
    {
        $anio = $this->option('anio');
        if ($anio === null || $anio === '') {
            $anio = Constants::YEAR_JOVENES;
        }

        $fechaRef = trim((string) $this->option('fecha-referencia'));
        if ($fechaRef === '') {
            $fechaRef = $anio.'-04-01';
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaRef)) {
            $this->error('--fecha-referencia debe tener formato Y-m-d.');
            return self::FAILURE;
        }

        $estado = trim((string) $this->option('estado'));
        $solo   = trim((string) $this->option('solo'));

        $query = DB::table('jovens as j')
            ->leftJoin('periodos as pe', 'pe.id', '=', 'j.periodo_id')
            ->leftJoin('investigadors as i', 'i.id', '=', 'j.investigador_id')
            ->leftJoin('personas as p', 'p.id', '=', 'i.persona_id')
            ->leftJoin('facultads as f', 'f.id', '=', 'j.facultadplanilla_id')
            ->select(
                'j.id as joven_id', 'j.estado', 'j.investigador_id',
                'p.apellido', 'p.nombre', 'p.documento', 'p.cuil',
                'f.nombre as facultad'
            )
            ->where('pe.nombre', $anio);

        if ($estado !== '') {
            $query->where('j.estado', $estado);
        }

        $jovenes = $query->orderBy('p.apellido')->orderBy('p.nombre')->get();

        $this->info('Periodo '.$anio.($estado !== '' ? ', estado '.$estado : '').' — solicitudes: '.$jovenes->count());
        $this->line('Beca vigente al '.$fechaRef);

        if ($jovenes->isEmpty()) {
            $this->warn('No hay solicitudes para ese periodo.');
            return self::FAILURE;
        }

        $jovenIds = [];
        $invIds   = [];
        foreach ($jovenes as $joven) {
            $jovenIds[] = (int) $joven->joven_id;
            if ($joven->investigador_id !== null) {
                $invIds[] = (int) $joven->investigador_id;
            }
        }

        // Beca declarada en la solicitud
        $becasJoven = [];
        if (!empty($jovenIds)) {
            $filas = DB::table('joven_becas')
                ->select('joven_id', 'institucion', 'beca', 'desde', 'hasta', 'unlp')
                ->whereIn('joven_id', $jovenIds)
                ->where('actual', 1)
                ->get();
            foreach ($filas as $fila) {
                $becasJoven[(int) $fila->joven_id] = $fila;
            }
        }

        // Becas del investigador vigentes a la fecha de referencia
        $becasInv = [];
        if (!empty($invIds)) {
            $filas = DB::table('investigador_becas')
                ->select('id', 'investigador_id', 'institucion', 'beca', 'desde', 'hasta', 'resumen')
                ->whereIn('investigador_id', $invIds)
                ->where('desde', '<=', $fechaRef)
                ->where('hasta', '>=', $fechaRef)
                ->get();
            foreach ($filas as $fila) {
                $iid = (int) $fila->investigador_id;
                if (!array_key_exists($iid, $becasInv)) {
                    $becasInv[$iid] = [];
                }
                $becasInv[$iid][] = $fila;
            }
        }

        $informe = [];
        foreach ($jovenes as $joven) {
            $jid = (int) $joven->joven_id;
            $iid = (int) $joven->investigador_id;

            $bj = array_key_exists($jid, $becasJoven) ? $becasJoven[$jid] : null;
            $candidatas = array_key_exists($iid, $becasInv) ? $becasInv[$iid] : [];

            // Si el joven declara beca, preferimos la del investigador del mismo tipo
            $bi = null;
            if (!empty($candidatas)) {
                if ($bj !== null) {
                    foreach ($candidatas as $c) {
                        if (strcasecmp(trim((string) $c->beca), trim((string) $bj->beca)) === 0) {
                            $bi = $c;
                            break;
                        }
                    }
                }
                if ($bi === null) {
                    $bi = $candidatas[0];
                }
            }

            $diag = '';
            $detalle = '';

            if ($bj === null && $bi === null) {
                $diag = 'SIN BECA';
            } elseif ($bj === null) {
                $diag = 'SOLO INVESTIGADOR_BECA';
                $detalle = 'la solicitud no declara beca actual';
            } elseif ($bi === null) {
                $diag = 'FALTA INVESTIGADOR_BECA';
                $detalle = 'declara '.trim((string) $bj->beca).' ('.trim((string) $bj->institucion).')';
            } else {
                $difs = [];
                if (strcasecmp(trim((string) $bi->beca), trim((string) $bj->beca)) !== 0) {
                    $difs[] = 'tipo';
                }
                if (strcasecmp(trim((string) $bi->institucion), trim((string) $bj->institucion)) !== 0) {
                    $difs[] = 'institucion';
                }
                if ($this->fecha($bi->desde) !== $this->fecha($bj->desde)) {
                    $difs[] = 'desde';
                }
                if ($this->fecha($bi->hasta) !== $this->fecha($bj->hasta)) {
                    $difs[] = 'hasta';
                }

                if (!empty($difs)) {
                    $diag = 'DESACUERDO';
                    $detalle = 'difieren: '.implode(', ', $difs);
                } elseif (trim((string) $bi->resumen) === '') {
                    $diag = 'SIN RESUMEN';
                } else {
                    $diag = 'OK';
                }

                if (count($candidatas) > 1) {
                    $ids = [];
                    foreach ($candidatas as $c) {
                        $ids[] = $c->id;
                    }
                    $detalle = trim($detalle.' | varias becas vigentes: '.implode(',', $ids));
                }
            }

            if ($solo !== '' && stripos($diag, $solo) === false) {
                continue;
            }

            $informe[] = [
                $joven->joven_id,
                $joven->estado,
                $joven->apellido,
                $joven->nombre,
                (string) $joven->documento,
                (string) $joven->cuil,
                $joven->facultad,
                $bj ? trim((string) $bj->institucion) : '',
                $bj ? trim((string) $bj->beca) : '',
                $bj ? $this->fecha($bj->desde) : '',
                $bj ? $this->fecha($bj->hasta) : '',
                $bi ? $bi->id : '',
                $bi ? trim((string) $bi->institucion) : '',
                $bi ? trim((string) $bi->beca) : '',
                $bi ? $this->fecha($bi->desde) : '',
                $bi ? $this->fecha($bi->hasta) : '',
                $bi ? (trim((string) $bi->resumen) !== '' ? 'SI' : 'NO') : '',
                $diag,
                $detalle,
            ];
        }

        if (empty($informe)) {
            $this->warn('Nada que informar con esos filtros.');
            return self::SUCCESS;
        }

        // Resumen por diagnostico
        $conteo = [];
        foreach ($informe as $fila) {
            $diag = $fila[17];
            if (!array_key_exists($diag, $conteo)) {
                $conteo[$diag] = 0;
            }
            $conteo[$diag]++;
        }
        arsort($conteo);
        $tabla = [];
        foreach ($conteo as $diag => $cantidad) {
            $tabla[] = [$diag, $cantidad];
        }
        $this->newLine();
        $this->table(['Diagnostico', 'Solicitudes'], $tabla);

        // Detalle de los casos que rompen el PDF
        $rotos = [];
        foreach ($informe as $fila) {
            if (in_array($fila[17], ['FALTA INVESTIGADOR_BECA', 'SIN RESUMEN', 'DESACUERDO'], true)) {
                $rotos[] = [
                    $fila[0],
                    $fila[2].', '.$fila[3],
                    $fila[4],
                    $fila[8],
                    $fila[17],
                    mb_substr((string) $fila[18], 0, 45, 'UTF-8'),
                ];
            }
        }
        if (!empty($rotos)) {
            $this->newLine();
            $this->line('Casos que salen mal en el PDF (primeros 25 de '.count($rotos).'):');
            $this->table(
                ['Joven', 'Apellido, Nombre', 'Doc', 'Beca declarada', 'Diagnostico', 'Detalle'],
                array_slice($rotos, 0, 25)
            );
        }

        $salida = $this->option('salida')
            ?: storage_path('app/auditoria_becas_jovenes_'.$anio.'_'.date('Ymd_His').'.xlsx');

        $this->escribirXlsx($informe, $salida, $anio);

        return self::SUCCESS;
    }

    private function escribirXlsx(array $informe, string $path, string $anio): void
    {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Jovenes '.$anio);

        foreach (self::HEADERS as $i => $header) {
            $sheet->setCellValueByColumnAndRow($i + 1, 1, $header);
        }

        $r = 2;
        foreach ($informe as $fila) {
            foreach ($fila as $c => $valor) {
                // Documento y CUIL como texto, para que Excel no los rompa
                if ($c === 4 || $c === 5) {
                    $sheet->setCellValueExplicitByColumnAndRow($c + 1, $r, (string) $valor, DataType::TYPE_STRING);
                } else {
                    $sheet->setCellValueByColumnAndRow($c + 1, $r, $valor);
                }
            }
            $r++;
        }

        $lastCol = Coordinate::stringFromColumnIndex(count(self::HEADERS));
        $sheet->getStyle('A1:'.$lastCol.'1')->getFont()->setBold(true);
        $sheet->getStyle('A1:'.$lastCol.'1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:'.$lastCol.'1');
        for ($c = 1; $c <= count(self::HEADERS); $c++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($c))->setAutoSize(true);
        }

        (new Xlsx($spreadsheet))->save($path);
        $this->newLine();
        $this->info('Informe: '.$path.' ('.count($informe).' filas)');

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
    }
}
