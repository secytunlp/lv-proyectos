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
 * Genera un .xlsx por facultad con los proyectos que deben presentar informe
 * en el año indicado, clasificando el tipo de informe (Final / Reducido /
 * Bienal / etc.) segun la cohorte (año de inicio - año de fin) del proyecto.
 *
 * Uso:
 *   php artisan informes:facultad
 *   php artisan informes:facultad --anio=2025
 *   php artisan informes:facultad --salida=storage/app/informes_2025
 */
class ExportarInformesFacultad extends Command
{
    protected $signature = 'informes:facultad
        {--anio=2025 : Año del informe. Define qué cohortes entran y cómo se clasifican}
        {--estado=Acreditado : Estado del proyecto a incluir. "todos" para no filtrar}
        {--salida= : Carpeta de salida para los .xlsx}';

    protected $description = 'Exporta un Excel por facultad con los proyectos que presentan informe y el tipo de informe según la cohorte';

    /**
     * Rangos (cohortes) por año de informe.
     *
     * Clave: "<año inicio>-<año fin>" del proyecto.
     * Valor: etiqueta del tipo de informe (columna "Informe").
     *
     * Fuente: rangos definidos por Secretaría para los informes de cada año.
     * Solo entran al listado las cohortes presentes en este mapa; el resto de
     * los proyectos (ya terminados, etc.) quedan afuera.
     */
    private const RANGOS = [
        2025 => [
            'I+D' => [
                '2022-2025' => 'Final',            // 4 años, cierra en 2025
                '2023-2026' => 'Reducido 3° año',
                '2024-2025' => 'Final',            // 2 años, cierra en 2025
                '2024-2027' => 'Bienal (24-25)',
                '2025-2026' => 'Reducido 1° año',  // aún sin proyectos cargados
                '2025-2028' => 'Reducido 1° año',  // aún sin proyectos cargados
            ],
            'PPID' => [
                '2024-2025' => 'Final',
            ],
        ],
    ];

    private const HEADERS = [
        'N°', 'Proyecto', 'Título', 'Inicio', 'Fin', 'Director', 'Informe',
    ];

    /** Nombre corto de facultad para el archivo (mapa opcional; si falta, se deriva). */
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

        if (!isset(self::RANGOS[$anio])) {
            $this->error("No hay rangos definidos para el año {$anio}. Agregalos en la constante RANGOS.");
            return self::FAILURE;
        }

        $mapa = self::RANGOS[$anio];
        $tipos = array_keys($mapa);

        $salida = $this->option('salida') ?: storage_path("app/informes_{$anio}");
        $this->ensureDir($salida);

        $rows = $this->fetchRows($tipos);

        // Clasifica cada fila según su cohorte; descarta las que no aplican.
        $rows = $rows->map(function ($r) use ($mapa) {
            $clave = ((int) date('Y', strtotime($r->inicio))) . '-' . ((int) date('Y', strtotime($r->fin)));
            $r->informe = $mapa[$r->tipo][$clave] ?? null;
            return $r;
        })->filter(function ($r) {
            return $r->informe !== null;
        })->values();

        if ($rows->isEmpty()) {
            $this->warn('No se encontraron proyectos que apliquen a los rangos indicados.');
            return self::FAILURE;
        }

        // Un archivo por facultad y por programa (I+D y PPID van separados).
        $grupos = $rows->groupBy(function ($r) {
            return $r->tipo . '||' . $r->facultad;
        });

        $this->info("Informes {$anio}: {$rows->count()} proyecto(s) en {$grupos->count()} archivo(s).");

        foreach ($grupos as $clave => $filas) {
            [$tipo, $facultad] = explode('||', $clave, 2);
            $tipoArchivo = str_replace(['/', '\\'], '-', $tipo); // "I+D" es seguro; PIIT-AP también
            $file = rtrim($salida, '/\\') . DIRECTORY_SEPARATOR
                . "Informes {$anio} - {$tipoArchivo} - " . $this->nombreCorto($facultad) . '.xlsx';
            $this->writeXlsx($filas->values(), $facultad, $tipo, $anio, $file);
            $this->line(sprintf('  %-5s %-14s %3d  ->  %s',
                $tipo, $this->nombreCorto($facultad), $filas->count(), basename($file)));
        }

        // Resumen por tipo de informe
        $this->newLine();
        $this->info('Resumen por tipo de informe:');
        foreach ($rows->groupBy('informe') as $tipo => $g) {
            $this->line(sprintf('  %-18s %d', $tipo, $g->count()));
        }

        $this->info("Listo. Archivos en: {$salida}");
        return self::SUCCESS;
    }

    private function fetchRows(array $tipos)
    {
        $query = DB::table('proyectos as p')
            ->leftJoin('integrantes as i', function ($join) {
                $join->on('p.id', '=', 'i.proyecto_id')
                    ->where('i.tipo', '=', 'Director');
            })
            ->leftJoin('investigadors as inv', 'i.investigador_id', '=', 'inv.id')
            ->leftJoin('personas as per', 'inv.persona_id', '=', 'per.id')
            ->leftJoin('facultads as f', 'p.facultad_id', '=', 'f.id')
            ->whereIn('p.tipo', $tipos)
            ->select([
                'p.id', 'p.tipo', 'p.codigo', 'p.titulo', 'p.inicio', 'p.fin',
                'per.apellido', 'per.nombre',
                'f.nombre as facultad',
            ])
            ->orderBy('f.nombre')
            ->orderBy('p.tipo')
            ->orderBy('p.codigo')
            ->orderBy('i.id');

        $estado = $this->option('estado');
        if ($estado && strtolower($estado) !== 'todos') {
            $query->where('p.estado', $estado);
        }

        // Una fila por proyecto (primer director encontrado).
        return $query->get()->unique('id')->values();
    }

    private function writeXlsx($filas, string $facultad, string $tipo, int $anio, string $path): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Informes ' . $anio);

        $lastCol = Coordinate::stringFromColumnIndex(count(self::HEADERS));

        // Título
        $sheet->setCellValue('A1', "Proyectos {$tipo} con informe {$anio}");
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->setCellValue('A2', $facultad);
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A2')->getFont()->setBold(true);
        $sheet->getStyle("A1:A2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Encabezados
        $headerRow = 4;
        foreach (self::HEADERS as $i => $h) {
            $sheet->setCellValueByColumnAndRow($i + 1, $headerRow, $h);
        }

        // Datos
        $r = $headerRow + 1;
        $n = 1;
        foreach ($filas as $row) {
            $director = trim(($row->apellido ?? '') . ', ' . ($row->nombre ?? ''), ', ');
            $sheet->setCellValueByColumnAndRow(1, $r, $n++);
            $sheet->setCellValueByColumnAndRow(2, $r, $row->codigo);
            $sheet->setCellValueByColumnAndRow(3, $r, $row->titulo);
            $sheet->setCellValueByColumnAndRow(4, $r, $this->formatDate($row->inicio));
            $sheet->setCellValueByColumnAndRow(5, $r, $this->formatDate($row->fin));
            $sheet->setCellValueByColumnAndRow(6, $r, $director);
            $sheet->setCellValueByColumnAndRow(7, $r, $row->informe);
            $r++;
        }

        $this->styleSheet($sheet, $headerRow, $r - 1, $lastCol);

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

        // Alineaciones
        $sheet->getStyle("A{$headerRow}:B{$lastRow}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("D{$headerRow}:E{$lastRow}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("C5:C{$lastRow}")->getAlignment()->setWrapText(true);

        // Anchos: N°, Proyecto, Título, Inicio, Fin, Director, Informe
        $anchos = ['A' => 5, 'B' => 12, 'C' => 55, 'D' => 12, 'E' => 12, 'F' => 28, 'G' => 16];
        foreach ($anchos as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        $sheet->freezePane('A' . ($headerRow + 1));
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
