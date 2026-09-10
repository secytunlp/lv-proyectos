<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

/**
 * Exporta las becas de investigador_becas a un Excel, para ver que hay cargado
 * realmente. Por defecto: institucion UNLP y vigentes hoy.
 *
 * Usage:
 *   php artisan exportar:becas-unlp-excel
 *   php artisan exportar:becas-unlp-excel --fecha=2026-04-01
 *   php artisan exportar:becas-unlp-excel --anio=2026
 *   php artisan exportar:becas-unlp-excel --institucion= --todas
 *   php artisan exportar:becas-unlp-excel --salida=storage/app/becas.xlsx
 */
class ExportarBecasUnlpExcel extends Command
{
    protected $signature = 'exportar:becas-unlp-excel
                        {--institucion=UNLP : Filtra por institucion. Vacio = todas}
                        {--fecha= : Vigentes a esta fecha (Y-m-d). Por defecto hoy}
                        {--anio= : En vez de una fecha, las que solapan este anio}
                        {--todas : Sin filtro de vigencia}
                        {--salida= : Ruta del archivo .xlsx de salida}';

    protected $description = 'Exporta a Excel las becas de investigador_becas (por defecto UNLP vigentes hoy)';

    private const HEADERS = [
        'Beca ID', 'Investigador ID', 'Apellido', 'Nombre', 'Documento', 'CUIL',
        'Institucion', 'Beca', 'Desde', 'Hasta', 'UNLP', 'Tiene resumen', 'Obs', 'Resumen',
    ];

    public function handle(): int
    {
        $institucion = trim((string) $this->option('institucion'));
        $anio        = $this->option('anio');
        $todas       = (bool) $this->option('todas');
        $fecha       = $this->option('fecha');

        if ($fecha === null || $fecha === '') {
            $fecha = date('Y-m-d');
        }

        $query = DB::table('investigador_becas as b')
            ->leftJoin('investigadors as i', 'i.id', '=', 'b.investigador_id')
            ->leftJoin('personas as p', 'p.id', '=', 'i.persona_id')
            ->select(
                'b.id as beca_id',
                'b.investigador_id',
                'p.apellido',
                'p.nombre',
                'p.documento',
                'p.cuil',
                'b.institucion',
                'b.beca',
                'b.desde',
                'b.hasta',
                'b.unlp',
                'b.resumen'
            );

        if ($institucion !== '') {
            $query->where('b.institucion', $institucion);
        }

        if (!$todas) {
            if ($anio !== null && $anio !== '') {
                $query->where('b.desde', '<=', $anio.'-12-31')
                      ->where('b.hasta', '>=', $anio.'-01-01');
                $criterio = 'solapan '.$anio;
            } else {
                $query->where('b.desde', '<=', $fecha)
                      ->where('b.hasta', '>=', $fecha);
                $criterio = 'vigentes al '.$fecha;
            }
        } else {
            $criterio = 'sin filtro de vigencia';
        }

        $rows = $query->orderBy('p.apellido')->orderBy('p.nombre')->orderBy('b.desde')->get();

        $this->info('Criterio: '.$criterio.($institucion !== '' ? ', institucion '.$institucion : ', todas las instituciones'));

        if ($rows->isEmpty()) {
            $this->warn('No se encontraron becas para los filtros indicados.');
            return self::FAILURE;
        }

        $salida = $this->option('salida')
            ?: storage_path('app/becas_'.($institucion !== '' ? strtolower($institucion).'_' : '').date('Ymd_His').'.xlsx');

        $this->ensureDir(dirname($salida));
        $this->info('Encontradas '.$rows->count().' beca(s). Generando: '.$salida);

        $this->writeXlsx($rows, $salida);
        $this->resumenConsola($rows);

        $this->info('Listo.');
        return self::SUCCESS;
    }

    /** Contadores utiles para ver de un vistazo que hay cargado */
    private function resumenConsola($rows): void
    {
        $conResumen = 0;
        $porAnio    = [];
        $porBeca    = [];

        foreach ($rows as $row) {
            if (trim((string) $row->resumen) !== '') {
                $conResumen++;
            }

            $anio = substr((string) $row->desde, 0, 4);
            if ($anio === '' || $anio === '0000') {
                $anio = '(sin fecha)';
            }
            if (!array_key_exists($anio, $porAnio)) {
                $porAnio[$anio] = 0;
            }
            $porAnio[$anio]++;

            $beca = trim((string) $row->beca);
            if ($beca === '') {
                $beca = '(vacio)';
            }
            if (!array_key_exists($beca, $porBeca)) {
                $porBeca[$beca] = 0;
            }
            $porBeca[$beca]++;
        }

        $this->newLine();
        $this->line('Con resumen cargado: '.$conResumen.' de '.$rows->count());

        krsort($porAnio);
        $tablaAnios = [];
        foreach ($porAnio as $anio => $cantidad) {
            $tablaAnios[] = [$anio, $cantidad];
        }
        $this->newLine();
        $this->line('Por anio de inicio:');
        $this->table(['Anio desde', 'Becas'], $tablaAnios);

        arsort($porBeca);
        $tablaBecas = [];
        foreach (array_slice($porBeca, 0, 20, true) as $beca => $cantidad) {
            $tablaBecas[] = [$beca, $cantidad];
        }
        $this->newLine();
        $this->line('Por tipo de beca:');
        $this->table(['Beca', 'Becas'], $tablaBecas);
    }

    private function writeXlsx($rows, string $path): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Becas');

        foreach (self::HEADERS as $i => $header) {
            $sheet->setCellValueByColumnAndRow($i + 1, 1, $header);
        }

        $r = 2;
        foreach ($rows as $row) {
            $resumen = trim((string) $row->resumen);

            $obs = '';
            $desde = substr((string) $row->desde, 0, 10);
            if ($desde !== '' && $desde <= '1970-01-02') {
                $obs = 'fecha epoch';
            }
            if ($row->apellido === null && $row->nombre === null) {
                $obs = trim($obs.' sin persona');
            }

            $sheet->setCellValueByColumnAndRow(1, $r, $row->beca_id);
            $sheet->setCellValueByColumnAndRow(2, $r, $row->investigador_id);
            $sheet->setCellValueByColumnAndRow(3, $r, $row->apellido);
            $sheet->setCellValueByColumnAndRow(4, $r, $row->nombre);
            $sheet->setCellValueExplicitByColumnAndRow(5, $r, (string) $row->documento, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicitByColumnAndRow(6, $r, (string) $row->cuil, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueByColumnAndRow(7, $r, $row->institucion);
            $sheet->setCellValueByColumnAndRow(8, $r, $row->beca);
            $sheet->setCellValueByColumnAndRow(9, $r, $this->formatDate($row->desde));
            $sheet->setCellValueByColumnAndRow(10, $r, $this->formatDate($row->hasta));
            $sheet->setCellValueByColumnAndRow(11, $r, $row->unlp ? 'SI' : 'NO');
            $sheet->setCellValueByColumnAndRow(12, $r, $resumen !== '' ? 'SI' : 'NO');
            $sheet->setCellValueByColumnAndRow(13, $r, $obs);
            $sheet->setCellValueExplicitByColumnAndRow(14, $r, mb_substr($resumen, 0, 32000), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $r++;
        }

        $this->styleSheet($sheet);

        (new Xlsx($spreadsheet))->save($path);
        $this->line('    Escrito: '.$path.' ('.count($rows).' filas)');

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
    }

    private function styleSheet($sheet): void
    {
        $lastCol = Coordinate::stringFromColumnIndex(count(self::HEADERS));

        $sheet->getStyle('A1:'.$lastCol.'1')->getFont()->setBold(true);
        $sheet->getStyle('A1:'.$lastCol.'1')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->freezePane('A2');

        // Autosize salvo la ultima columna (Resumen), que es texto largo
        for ($c = 1; $c < count(self::HEADERS); $c++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($c))
                ->setAutoSize(true);
        }
        $sheet->getColumnDimension($lastCol)->setWidth(80);
    }

    private function formatDate($value): string
    {
        if ($value === null || $value === '' || str_starts_with((string) $value, '0000-00-00')) {
            return '';
        }

        $ts = strtotime((string) $value);
        return $ts === false ? '' : date('j/n/Y', $ts);
    }

    private function ensureDir(string $dir): void
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}
