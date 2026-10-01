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
 * Exporta los controles previos al cálculo de subsidios a un .xlsx (una hoja
 * por control), para imprimir/auditar. Corre sobre las tablas de tránsito
 * (subsidio_integrantes / subsidio_proyectos), así que hay que ejecutarlo
 * DESPUÉS de la extracción del año (subsidios:calcular --solo-extraccion).
 *
 * Controles:
 *   1. Sin informe        - integrantes que cuentan y no tienen informe que matchee.
 *   2. No evaluados        - NO titular con evaluación vacía (listado pero sin evaluar).
 *   3. No corresponde/otros- con informe cuya evaluación no es Satisfactorio/Titular/No corresponde.
 *
 * Uso:
 *   php artisan subsidios:controles --anio=2026
 */
class ExportarControlesSubsidios extends Command
{
    protected $signature = 'subsidios:controles
        {--anio= : Año del control. Requerido.}
        {--salida= : Carpeta de salida para el .xlsx.}';

    protected $description = 'Exporta a Excel los controles previos al cálculo (sin informe / no evaluados / no corresponde) para imprimir.';

    public function handle(): int
    {
        $anio = (int) $this->option('anio');
        if ($anio < 2000) {
            $this->error('Falta --anio (ej. 2026).');
            return self::FAILURE;
        }

        $inf   = "subsidio_informes_{$anio}";
        if (! DB::getSchemaBuilder()->hasTable($inf)) {
            $this->error("No existe la tabla {$inf}.");
            return self::FAILURE;
        }

        $ini      = ($anio - 1) . '-01-01';   // informado / alta
        // No controlar bajas <= (anio-2)-01-01 (ej. 2024-01-01 para 2026): ya se fueron.
        $bajaCtrl = ($anio - 2) . '-01-01';

        // Los incorporados a mano (subsidio_incorporados_AAAA) ya están resueltos:
        // se excluyen del control "sin informe" para que sólo muestre lo pendiente.
        $inc = "subsidio_incorporados_{$anio}";
        $incNotExists = DB::getSchemaBuilder()->hasTable($inc)
            ? "AND NOT EXISTS (SELECT 1 FROM {$inc} inc WHERE inc.proyecto_id = si.proyecto_id AND inc.documento = si.documento)"
            : '';

        // Datos mostrados (integrante/documento/categoría/alta/baja/estado) EN VIVO
        // desde integrantes+personas+investigadors (NO de subsidio_integrantes).
        // Los FILTROS siguen sobre si (snapshot de la extracción), pero NINGÚN dato
        // mostrado sale de si. Baja REAL (sin blanquear): el borrado de bajas
        // pendientes es sólo del cálculo; acá la fecha va junto al estado.
        $joinVivo = "
            LEFT JOIN integrantes ig ON ig.investigador_id = si.investigador_id AND ig.proyecto_id = si.proyecto_id
            LEFT JOIN investigadors inv ON inv.id = ig.investigador_id
            LEFT JOIN personas per ON per.id = inv.persona_id
            LEFT JOIN categorias cat ON cat.id = CASE
                WHEN inv.categoria_id IN (6,7,8,9,10) AND inv.sicadi_id IN (6,7,8,9,10)
                     THEN LEAST(inv.categoria_id, inv.sicadi_id)
                WHEN inv.categoria_id IN (6,7,8,9,10) THEN inv.categoria_id
                WHEN inv.sicadi_id    IN (6,7,8,9,10) THEN inv.sicadi_id
                ELSE NULL
            END
        ";
        $altaVivo = "CASE WHEN ig.alta='0000-00-00' THEN '' ELSE ig.alta END AS alta";
        $bajaVivo = "CASE WHEN ig.baja='0000-00-00' THEN '' ELSE ig.baja END AS baja";

        // --- Control 1: sin informe que matchee ---
        $c1 = DB::select("
            SELECT f.nombre AS facultad, si.proyecto, sp.director,
                   CONCAT(per.apellido, ', ', per.nombre) AS integrante,
                   cat.nombre AS categoria,
                   per.documento,
                   {$altaVivo}, {$bajaVivo}, ig.estado
            FROM subsidio_integrantes si
            JOIN subsidio_proyectos sp ON si.proyecto_id = sp.proyecto_id
            LEFT JOIN facultads f ON f.id = si.facultad_id
            {$joinVivo}
            WHERE si.dedicacion IN (1,2,3)
              AND sp.inicio < '{$ini}' AND sp.fin > '{$ini}'
              AND si.alta < '{$ini}'
              AND (si.baja IS NULL OR si.baja = '0000-00-00' OR si.baja > '{$bajaCtrl}')
              AND (si.universidad_id = 11 OR si.universidad_id = 0 OR si.universidad_id IS NULL)
              AND NOT EXISTS (SELECT 1 FROM {$inf} i WHERE i.proyecto_id = si.proyecto_id AND i.documento = si.documento)
              {$incNotExists}
            ORDER BY f.nombre, si.proyecto, si.integrante
        ");

        // --- Control 2: no evaluados = NO titular con evaluación vacía o
        // 'No corresponde' (la evaluación no aplica = no lo evaluaron). ---
        $c2 = DB::select("
            SELECT f.nombre AS facultad, si.proyecto, sp.director,
                   CONCAT(per.apellido, ', ', per.nombre) AS integrante,
                   per.documento,
                   inf.rol, inf.evaluacion,
                   {$altaVivo}, {$bajaVivo}, ig.estado
            FROM {$inf} inf
            JOIN subsidio_integrantes si ON inf.documento = si.documento AND inf.proyecto_id = si.proyecto_id
            JOIN subsidio_proyectos sp ON sp.proyecto_id = si.proyecto_id
            LEFT JOIN facultads f ON f.id = si.facultad_id
            {$joinVivo}
            WHERE inf.rol <> 'Titular'
              AND inf.rol <> 'Colaborador'
              AND (inf.evaluacion IS NULL OR inf.evaluacion = '' OR inf.evaluacion = 'No corresponde')
              AND si.dedicacion IN (1,2,3)
              AND sp.inicio < '{$ini}' AND sp.fin > '{$ini}'
              AND si.alta < '{$ini}'
              AND (si.baja IS NULL OR si.baja = '0000-00-00' OR si.baja > '{$bajaCtrl}')
              AND (si.universidad_id = 11 OR si.universidad_id = 0 OR si.universidad_id IS NULL)
            ORDER BY f.nombre, si.proyecto
        ");

        // --- Control 3: NO satisfactorio (se evaluaron y les fue mal). ---
        $c3 = DB::select("
            SELECT f.nombre AS facultad, si.proyecto, sp.director,
                   CONCAT(per.apellido, ', ', per.nombre) AS integrante,
                   per.documento,
                   inf.rol, inf.evaluacion,
                   {$altaVivo}, {$bajaVivo}, ig.estado
            FROM {$inf} inf
            JOIN subsidio_integrantes si ON inf.documento = si.documento AND inf.proyecto_id = si.proyecto_id
            JOIN subsidio_proyectos sp ON sp.proyecto_id = si.proyecto_id
            LEFT JOIN facultads f ON f.id = si.facultad_id
            {$joinVivo}
            WHERE inf.evaluacion = 'No satisfactorio'
              AND inf.rol <> 'Titular'
              AND inf.rol <> 'Colaborador'
              -- SIN filtro de dedicación: se auditan TODOS los 'No satisfactorio',
              -- cuenten o no para el subsidio (opción B).
              AND sp.inicio < '{$ini}' AND sp.fin > '{$ini}'
              AND si.alta < '{$ini}'
              AND (si.baja IS NULL OR si.baja = '0000-00-00' OR si.baja > '{$bajaCtrl}')
            ORDER BY f.nombre, si.proyecto, si.integrante
        ");

        // Orden natural del código (11/H999 antes que 11/H1000), por facultad.
        $c1 = $this->ordenar($c1);
        $c2 = $this->ordenar($c2);
        $c3 = $this->ordenar($c3);

        $salida = $this->option('salida') ?: storage_path("app/controles_{$anio}");
        $this->ensureDir($salida);
        $file = rtrim($salida, '/\\') . DIRECTORY_SEPARATOR . "Controles subsidios {$anio}.xlsx";

        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        $this->hoja($spreadsheet, "1 - Sin informe", "Control 1 - Integrantes sin informe ({$anio})",
            ['Proyecto', 'Director', 'Integrante', 'Categoría', 'Documento', 'Alta', 'Baja', 'Estado'], $c1,
            function ($r) { return [$r->proyecto, $r->director, $r->integrante, $r->categoria, $r->documento, $this->fecha($r->alta), $this->fecha($r->baja), $r->estado]; });

        $this->hoja($spreadsheet, "2 - No evaluados", "Control 2 - No evaluados ({$anio})",
            ['Proyecto', 'Director', 'Integrante', 'Documento', 'Rol', 'Evaluación', 'Alta', 'Baja', 'Estado'], $c2,
            function ($r) { return [$r->proyecto, $r->director, $r->integrante, $r->documento, $r->rol, $r->evaluacion, $this->fecha($r->alta), $this->fecha($r->baja), $r->estado]; });

        $this->hoja($spreadsheet, "3 - No satisfactorio", "Control 3 - No satisfactorio ({$anio})",
            ['Proyecto', 'Director', 'Integrante', 'Documento', 'Rol', 'Evaluación', 'Alta', 'Baja', 'Estado'], $c3,
            function ($r) { return [$r->proyecto, $r->director, $r->integrante, $r->documento, $r->rol, $r->evaluacion, $this->fecha($r->alta), $this->fecha($r->baja), $r->estado]; });

        (new Xlsx($spreadsheet))->save($file);
        $spreadsheet->disconnectWorksheets();

        $this->info("Controles {$anio}: #1=" . count($c1) . " | #2=" . count($c2) . " | #3=" . count($c3));
        $this->line('  ->  ' . $file);
        return self::SUCCESS;
    }

    /**
     * @param array  $rows
     * @param callable $map fila -> array de celdas
     */
    private function hoja(Spreadsheet $ss, string $titulo, string $encabezado, array $headers, array $rows, callable $map): void
    {
        $sheet = $ss->createSheet();
        $sheet->setTitle(substr($titulo, 0, 31));

        $allHeaders = array_merge(['N°'], $headers);
        $lastCol = Coordinate::stringFromColumnIndex(count($allHeaders));

        $sheet->setCellValue('A1', $encabezado);
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $headerRow = 3;
        foreach ($allHeaders as $i => $h) {
            $sheet->setCellValueByColumnAndRow($i + 1, $headerRow, $h);
        }

        $r = $headerRow + 1;
        $n = 1;
        foreach ($rows as $row) {
            $cells = $map($row);
            $sheet->setCellValueByColumnAndRow(1, $r, $n++);
            foreach ($cells as $ci => $val) {
                $sheet->setCellValueByColumnAndRow($ci + 2, $r, $val);
            }
            $r++;
        }
        $lastRow = max($r - 1, $headerRow);

        $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")->getFont()->setBold(true);
        $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")->getFill()
            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9E1F2');
        $sheet->getStyle("A{$headerRow}:{$lastCol}{$lastRow}")->getBorders()
            ->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        for ($c = 1; $c <= count($allHeaders); $c++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($c))->setAutoSize(true);
        }
        $sheet->freezePane('A' . ($headerRow + 1));
    }

    /**
     * Ordena por facultad y luego por código de proyecto en orden natural
     * (11/H999 antes que 11/H1000) y por integrante.
     *
     * @param array $rows
     * @return array
     */
    private function ordenar(array $rows): array
    {
        usort($rows, function ($a, $b) {
            $cf = strcmp((string) ($a->facultad ?? ''), (string) ($b->facultad ?? ''));
            if ($cf !== 0) {
                return $cf;
            }
            $cp = strnatcmp((string) ($a->proyecto ?? ''), (string) ($b->proyecto ?? ''));
            if ($cp !== 0) {
                return $cp;
            }
            return strcmp((string) ($a->integrante ?? ''), (string) ($b->integrante ?? ''));
        });
        return $rows;
    }

    private function fecha($value): string
    {
        if ($value === null || $value === '' || strpos((string) $value, '0000-00-00') === 0) {
            return '';
        }
        $ts = strtotime((string) $value);
        return $ts === false ? '' : date('j/n/Y', $ts);
    }

    private function ensureDir(string $dir): void
    {
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}
