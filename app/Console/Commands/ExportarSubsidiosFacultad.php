<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

/**
 * Genera un .xlsx por facultad con los proyectos EN EJECUCION que entran al
 * subsidio del año indicado. Pensado para sacarse ANTES de calcular los
 * subsidios (subsidios:calcular): es el listado de proyectos candidatos.
 *
 * El criterio replica al de poblarSubsidioProyectos() en CalcularSubsidios:
 *   tipo = 'I+D', estado = 'Acreditado', fin > fecha de corte,
 *   y el proyecto tiene un integrante Director.
 *
 * Uso:
 *   php artisan subsidios:facultad --anio=2026
 *   php artisan subsidios:facultad --anio=2026 --juntos
 *   php artisan subsidios:facultad --anio=2026 --salida=storage/app/subsidios_2026
 */
class ExportarSubsidiosFacultad extends Command
{
    protected $signature = 'subsidios:facultad
        {--anio=2026 : Año del listado. Define la fecha de corte (fin > {anio-1}-12-31).}
        {--fecha-corte= : Fecha de corte (Y-m-d). Por defecto {anio-1}-12-31.}
        {--hasta-inicio= : Excluye proyectos con inicio >= esta fecha (Y-m-d). Opcional, para replicar un período pasado.}
        {--estado=Acreditado : Estado del proyecto a incluir. "todos" para no filtrar.}
        {--juntos : Genera un único .xlsx con todas las facultades juntas.}
        {--salida= : Carpeta de salida para los .xlsx.}';

    protected $description = 'Exporta un Excel por facultad con los proyectos I+D en ejecución que entran al subsidio (candidatos, antes de calcular).';

    private const HEADERS = [
        'N°', 'Proyecto', 'Inicio', 'Fin', 'Director',
    ];

    /** Cabeceras del archivo consolidado (todo junto). */
    private const HEADERS_JUNTOS = [
        'N°', 'Facultad', 'Proyecto', 'Inicio', 'Fin', 'Director',
    ];

    /** Nombre corto de facultad para el archivo (si falta, se deriva). */
    private const NOMBRE_CORTO = [
        'FACULTAD DE CIENCIAS AGRARIAS Y FORESTALES'          => 'Agrarias',
        'FACULTAD DE CIENCIAS VETERINARIAS'                   => 'Veterinarias',
        'FACULTAD DE ARQUITECTURA Y URBANISMO'                => 'Arquitectura',
        'FACULTAD DE INGENIERIA'                              => 'Ingenieria',
        'FACULTAD DE CIENCIAS EXACTAS'                        => 'Exactas',
        'FACULTAD DE CIENCIAS ASTRONOMICAS Y GEOFISICAS'      => 'Astronomicas',
        'FACULTAD DE CIENCIAS ECONOMICAS'                     => 'Economicas',
        'FACULTAD DE CIENCIAS JURIDICAS Y SOCIALES'           => 'Juridicas',
        'FACULTAD DE PERIODISMO Y COMUNICACION SOCIAL'        => 'Periodismo',
        'FACULTAD DE HUMANIDADES Y CIENCIAS DE LA EDUCACION'  => 'Humanidades',
        'FACULTAD DE BELLAS ARTES'                            => 'Artes',
        'FACULTAD DE CIENCIAS MEDICAS'                        => 'Medicas',
        'FACULTAD DE TRABAJO SOCIAL'                          => 'Trabajo Social',
        'FACULTAD DE ODONTOLOGIA'                             => 'Odontologia',
        'FACULTAD DE CIENCIAS NATURALES Y MUSEO'              => 'Naturales',
        'FACULTAD DE INFORMATICA'                             => 'Informatica',
        'FACULTAD DE PSICOLOGIA'                              => 'Psicologia',
    ];

    public function handle(): int
    {
        $anio = (int) $this->option('anio');
        if ($anio < 2000) {
            $this->error('Falta --anio (ej. 2026).');
            return self::FAILURE;
        }

        $fechaCorte = $this->option('fecha-corte') ?: ($anio - 1) . '-12-31';

        $salida = $this->option('salida') ?: storage_path("app/subsidios_{$anio}");
        $this->ensureDir($salida);

        $rows = $this->fetchRows($fechaCorte);

        if ($rows->isEmpty()) {
            $this->warn('No se encontraron proyectos en ejecución para los filtros indicados.');
            return self::FAILURE;
        }

        // Modo "todos juntos": un único archivo con todas las facultades.
        if ($this->option('juntos')) {
            $ordenados = $rows->sort(function ($a, $b) {
                $cmp = strcmp((string) $a->facultad, (string) $b->facultad);
                if ($cmp === 0) {
                    $cmp = strnatcmp((string) $a->codigo, (string) $b->codigo);
                }
                return $cmp;
            })->values();

            $file = rtrim($salida, '/\\') . DIRECTORY_SEPARATOR . "Subsidios {$anio} - TODOS.xlsx";
            $this->writeXlsxJuntos($ordenados, $anio, $file);
            $this->info("Proyectos a subsidiar {$anio}: {$rows->count()} en 1 archivo.");
            $this->line('  ->  ' . basename($file));
            $this->info("Listo. Archivo en: {$salida}");
            return self::SUCCESS;
        }

        // Un archivo por facultad.
        $grupos = $rows->groupBy(function ($r) {
            return (string) $r->facultad;
        });

        $this->info("Proyectos a subsidiar {$anio}: {$rows->count()} en {$grupos->count()} archivo(s).");

        foreach ($grupos as $facultad => $filas) {
            $filas = $filas->sort(function ($a, $b) {
                return strnatcmp((string) $a->codigo, (string) $b->codigo);
            })->values();

            $file = rtrim($salida, '/\\') . DIRECTORY_SEPARATOR
                . $this->nombreCorto($facultad) . '.xlsx';
            $this->writeXlsx($filas, $facultad, $anio, $file);
            $this->line(sprintf('  %-14s %3d  ->  %s',
                $this->nombreCorto($facultad), $filas->count(), basename($file)));
        }

        $this->info("Listo. Archivos en: {$salida}");
        return self::SUCCESS;
    }

    /**
     * Proyectos I+D en ejecución con director. Mismo criterio que el pipeline
     * de subsidios (poblarSubsidioProyectos): tipo I+D, estado, fin > corte.
     */
    private function fetchRows(string $fechaCorte)
    {
        $query = DB::table('proyectos as p')
            ->join('integrantes as i', function ($join) {
                $join->on('p.id', '=', 'i.proyecto_id')
                    ->where('i.tipo', '=', 'Director');
            })
            ->leftJoin('investigadors as inv', 'i.investigador_id', '=', 'inv.id')
            ->leftJoin('personas as per', 'inv.persona_id', '=', 'per.id')
            ->leftJoin('facultads as f', 'p.facultad_id', '=', 'f.id')
            ->where('p.tipo', 'I+D')
            ->where('p.fin', '>', $fechaCorte)
            ->select([
                'p.id', 'p.codigo', 'p.inicio', 'p.fin',
                'per.apellido', 'per.nombre',
                'f.nombre as facultad',
            ])
            ->orderBy('f.nombre')
            ->orderBy('p.codigo')
            ->orderBy('i.id');

        $estado = $this->option('estado');
        if ($estado && strtolower($estado) !== 'todos') {
            $query->where('p.estado', $estado);
        }

        if ($hastaInicio = $this->option('hasta-inicio')) {
            $query->where('p.inicio', '<', $hastaInicio);
        }

        // Una sola fila por proyecto (el primer director encontrado).
        return $query->get()->unique('id')->values();
    }

    private function writeXlsx($filas, string $facultad, int $anio, string $path): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Subsidios ' . $anio);

        $lastCol = Coordinate::stringFromColumnIndex(count(self::HEADERS));

        // Título
        $sheet->setCellValue('A1', "Proyectos I+D {$anio}");
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->setCellValue('A2', $this->nombreCorto($facultad));
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A2')->getFont()->setBold(true);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Encabezados
        $headerRow = 4;
        foreach (self::HEADERS as $i => $h) {
            $sheet->setCellValueByColumnAndRow($i + 1, $headerRow, $h);
        }

        // Datos
        $r = $headerRow + 1;
        $n = 1;
        foreach ($filas as $row) {
            $sheet->setCellValueByColumnAndRow(1, $r, $n++);
            $sheet->setCellValueByColumnAndRow(2, $r, $row->codigo);
            $sheet->setCellValueByColumnAndRow(3, $r, $this->formatDate($row->inicio));
            $sheet->setCellValueByColumnAndRow(4, $r, $this->formatDate($row->fin));
            $sheet->setCellValueByColumnAndRow(5, $r, $this->director($row));
            $r++;
        }

        $this->styleSheet($sheet, $headerRow, $r - 1, $lastCol);

        (new Xlsx($spreadsheet))->save($path);
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
    }

    private function writeXlsxJuntos($filas, int $anio, string $path): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Subsidios ' . $anio);

        $lastCol = Coordinate::stringFromColumnIndex(count(self::HEADERS_JUNTOS));

        // Título
        $sheet->setCellValue('A1', "Proyectos I+D {$anio} - Todas las facultades");
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Encabezados
        $headerRow = 3;
        foreach (self::HEADERS_JUNTOS as $i => $h) {
            $sheet->setCellValueByColumnAndRow($i + 1, $headerRow, $h);
        }

        // Datos
        $r = $headerRow + 1;
        $n = 1;
        foreach ($filas as $row) {
            $sheet->setCellValueByColumnAndRow(1, $r, $n++);
            $sheet->setCellValueByColumnAndRow(2, $r, $row->facultad);
            $sheet->setCellValueByColumnAndRow(3, $r, $row->codigo);
            $sheet->setCellValueByColumnAndRow(4, $r, $this->formatDate($row->inicio));
            $sheet->setCellValueByColumnAndRow(5, $r, $this->formatDate($row->fin));
            $sheet->setCellValueByColumnAndRow(6, $r, $this->director($row));
            $r++;
        }
        $lastRow = $r - 1;

        // Estilo cabecera + bordes
        $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")->getFont()->setBold(true);
        $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")->getFill()
            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9E1F2');
        $sheet->getStyle("A{$headerRow}:{$lastCol}{$lastRow}")->getBorders()
            ->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Alineaciones: N°, Facultad, Proyecto, Inicio, Fin, Director
        $sheet->getStyle("A{$headerRow}:A{$lastRow}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("C{$headerRow}:E{$lastRow}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("B4:B{$lastRow}")->getAlignment()->setWrapText(true);

        $anchos = ['A' => 5, 'B' => 32, 'C' => 12, 'D' => 12, 'E' => 12, 'F' => 34];
        foreach ($anchos as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        $sheet->freezePane('A' . ($headerRow + 1));

        (new Xlsx($spreadsheet))->save($path);
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
    }

    private function styleSheet($sheet, int $headerRow, int $lastRow, string $lastCol): void
    {
        // Encabezado
        $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")->getFont()->setBold(true);
        $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")->getFill()
            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9E1F2');

        // Bordes de toda la tabla
        $sheet->getStyle("A{$headerRow}:{$lastCol}{$lastRow}")->getBorders()
            ->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Alineaciones: N°, Proyecto, Inicio, Fin centrados; Director a la izquierda.
        $sheet->getStyle("A{$headerRow}:D{$lastRow}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Anchos: N°, Proyecto, Inicio, Fin, Director
        $anchos = ['A' => 5, 'B' => 12, 'C' => 12, 'D' => 12, 'E' => 34];
        foreach ($anchos as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        $sheet->freezePane('A' . ($headerRow + 1));
    }

    private function director($row): string
    {
        return trim(((string) ($row->apellido ?? '')) . ', ' . ((string) ($row->nombre ?? '')), ', ');
    }

    private function nombreCorto(?string $facultad): string
    {
        $facultad = (string) $facultad;
        if (isset(self::NOMBRE_CORTO[$facultad])) {
            return self::NOMBRE_CORTO[$facultad];
        }
        // Deriva: quita "FACULTAD DE " y limpia para nombre de archivo.
        $corto = preg_replace('/^FACULTAD DE\s+/i', '', $facultad);
        $corto = str_replace(['/', '\\', ':'], '-', $corto);
        return $corto !== '' ? ucwords(mb_strtolower($corto)) : 'SinFacultad';
    }

    /**
     * Fecha como j/n/Y (ej. 1/1/2020). Nulos / vacíos / '0000-00-00' -> ''.
     */
    private function formatDate($value): string
    {
        if ($value === null || $value === '' || strpos((string) $value, '0000-00-00') === 0) {
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
