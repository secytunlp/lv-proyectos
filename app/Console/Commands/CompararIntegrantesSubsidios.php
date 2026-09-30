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
 * Compara los integrantes tenidos en cuenta (intproy) entre dos cálculos,
 * para los proyectos que coinciden en ambos. No compara montos.
 *
 * Genera un .xlsx con dos hojas:
 *   - Resumen por proyecto: cantidad de integrantes viejo vs nuevo (+ dif y
 *     cuántos cambiaron), con estado OK / REVISAR.
 *   - Detalle: por integrante, categoría y horas (in_dedi) de cada año, y estado
 *     igual / CAMBIO / SOLO NUEVO / SOLO VIEJO.
 *
 * Nota: en intproy sólo hay categoría (in_cainv) y horas (in_dedi); la
 * dedicación 1/2/3 es un filtro previo y no queda guardada.
 *
 * Uso:
 *   php artisan subsidios:comparar-integrantes --nuevo=intproy_2026 --viejo=intproy_2025_ppid
 */
class CompararIntegrantesSubsidios extends Command
{
    protected $signature = 'subsidios:comparar-integrantes
        {--nuevo= : Tabla intproy nueva (ej. intproy_2026). Requerido.}
        {--viejo= : Tabla intproy vieja (ej. intproy_2025_ppid). Requerido.}
        {--solo-diferencias : En el detalle, excluye las filas "igual".}
        {--salida= : Carpeta de salida.}';

    protected $description = 'Compara integrantes (cantidad, categoría y horas) de intproy entre dos años, en los proyectos que coinciden.';

    public function handle(): int
    {
        $nuevo = (string) $this->option('nuevo');
        $viejo = (string) $this->option('viejo');
        if ($nuevo === '' || $viejo === '') {
            $this->error('Faltan --nuevo y --viejo (ej. --nuevo=intproy_2026 --viejo=intproy_2025_ppid).');
            return self::FAILURE;
        }
        foreach ([$nuevo, $viejo] as $t) {
            if (! DB::getSchemaBuilder()->hasTable($t)) {
                $this->error("No existe la tabla {$t}.");
                return self::FAILURE;
            }
        }

        $selN = DB::table($nuevo . ' as t')->leftJoin('facultads as f', 'f.id', '=', 't.fac_id')
            ->select('t.pr_id', 't.pr_codigo', 't.in_id', 't.in_nombre', 't.in_cainv', 't.in_dedi', 'f.nombre as facultad')->get();
        $selV = DB::table($viejo . ' as t')->leftJoin('facultads as f', 'f.id', '=', 't.fac_id')
            ->select('t.pr_id', 't.pr_codigo', 't.in_id', 't.in_nombre', 't.in_cainv', 't.in_dedi', 'f.nombre as facultad')->get();

        $mapN = [];
        $proyN = [];
        $cntN = [];
        foreach ($selN as $r) {
            $mapN[$r->pr_id . '|' . $r->in_id] = $r;
            $proyN[$r->pr_id] = $r;
            $cntN[$r->pr_id] = (isset($cntN[$r->pr_id]) ? $cntN[$r->pr_id] : 0) + 1;
        }
        $mapV = [];
        $proyV = [];
        $cntV = [];
        foreach ($selV as $r) {
            $mapV[$r->pr_id . '|' . $r->in_id] = $r;
            $proyV[$r->pr_id] = $r;
            $cntV[$r->pr_id] = (isset($cntV[$r->pr_id]) ? $cntV[$r->pr_id] : 0) + 1;
        }

        $coinciden = array_intersect_key($proyN, $proyV);

        // Detalle por integrante + acumulado de cambios por proyecto.
        $keys = [];
        foreach ($mapN as $k => $r) { if (isset($coinciden[$r->pr_id])) { $keys[$k] = true; } }
        foreach ($mapV as $k => $r) { if (isset($coinciden[$r->pr_id])) { $keys[$k] = true; } }

        $detalle = [];
        $cambiosPorProy = [];
        foreach (array_keys($keys) as $k) {
            $n = isset($mapN[$k]) ? $mapN[$k] : null;
            $v = isset($mapV[$k]) ? $mapV[$k] : null;
            $base = $n ? $n : $v;

            $catN = $n ? $n->in_cainv : null;
            $hN   = $n ? $n->in_dedi : null;
            $catV = $v ? $v->in_cainv : null;
            $hV   = $v ? $v->in_dedi : null;

            if (! $v) {
                $estado = 'SOLO NUEVO';
            } elseif (! $n) {
                $estado = 'SOLO VIEJO';
            } elseif ((float) $hN !== (float) $hV || $this->normCat($catN) !== $this->normCat($catV)) {
                $estado = 'CAMBIO';
            } else {
                $estado = 'igual';
            }

            if ($estado !== 'igual') {
                $cambiosPorProy[$base->pr_id] = (isset($cambiosPorProy[$base->pr_id]) ? $cambiosPorProy[$base->pr_id] : 0) + 1;
            }

            if (! ($this->option('solo-diferencias') && $estado === 'igual')) {
                $detalle[] = [
                    'facultad'   => $base->facultad,
                    'proyecto'   => $base->pr_codigo,
                    'integrante' => $base->in_nombre,
                    'cat_v'      => $catV,
                    'horas_v'    => $hV,
                    'cat_n'      => $catN,
                    'horas_n'    => $hN,
                    'estado'     => $estado,
                ];
            }
        }

        // Resumen por proyecto.
        $resumen = [];
        foreach ($coinciden as $prId => $base) {
            $cv = isset($cntV[$prId]) ? $cntV[$prId] : 0;
            $cn = isset($cntN[$prId]) ? $cntN[$prId] : 0;
            $camb = isset($cambiosPorProy[$prId]) ? $cambiosPorProy[$prId] : 0;
            $resumen[] = [
                'facultad'    => $base->facultad,
                'proyecto'    => $base->pr_codigo,
                'cant_v'      => $cv,
                'cant_n'      => $cn,
                'dif'         => $cn - $cv,
                'cambios'     => $camb,
                'estado'      => ($cv === $cn && $camb === 0) ? 'OK' : 'REVISAR',
            ];
        }

        $ord = function ($a, $b) {
            $c = strcmp((string) $a['facultad'], (string) $b['facultad']);
            if ($c !== 0) { return $c; }
            $c = strnatcmp((string) $a['proyecto'], (string) $b['proyecto']);
            if ($c !== 0) { return $c; }
            return strcmp((string) (isset($a['integrante']) ? $a['integrante'] : ''), (string) (isset($b['integrante']) ? $b['integrante'] : ''));
        };
        usort($resumen, $ord);
        usort($detalle, $ord);

        $salida = $this->option('salida') ?: storage_path('app/comparacion_subsidios');
        if (! is_dir($salida)) { mkdir($salida, 0755, true); }
        $file = rtrim($salida, '/\\') . DIRECTORY_SEPARATOR . "Comparacion {$nuevo} vs {$viejo}.xlsx";

        $ss = new Spreadsheet();
        $ss->removeSheetByIndex(0);
        $this->hojaResumen($ss, $resumen, $nuevo, $viejo);
        $this->hojaDetalle($ss, $detalle, $nuevo, $viejo);
        (new Xlsx($ss))->save($file);
        $ss->disconnectWorksheets();

        $revisar = 0;
        foreach ($resumen as $r) { if ($r['estado'] === 'REVISAR') { $revisar++; } }
        $this->info(sprintf('Proyectos coincidentes: %d | a revisar: %d | filas detalle: %d', count($coinciden), $revisar, count($detalle)));
        $this->line('  ->  ' . $file);
        return self::SUCCESS;
    }

    private function hojaResumen(Spreadsheet $ss, array $rows, string $nuevo, string $viejo): void
    {
        $headers = ['N°', 'Facultad', 'Proyecto', "Integr. {$viejo}", "Integr. {$nuevo}", 'Dif', 'Con cambios', 'Estado'];
        $sheet = $ss->createSheet();
        $sheet->setTitle('Resumen por proyecto');
        $this->cabecera($sheet, "Resumen por proyecto: {$nuevo} vs {$viejo}", $headers);
        $r = 4; $n = 1;
        foreach ($rows as $row) {
            $sheet->setCellValueByColumnAndRow(1, $r, $n++);
            $sheet->setCellValueByColumnAndRow(2, $r, $row['facultad']);
            $sheet->setCellValueByColumnAndRow(3, $r, $row['proyecto']);
            $sheet->setCellValueByColumnAndRow(4, $r, $row['cant_v']);
            $sheet->setCellValueByColumnAndRow(5, $r, $row['cant_n']);
            $sheet->setCellValueByColumnAndRow(6, $r, $row['dif']);
            $sheet->setCellValueByColumnAndRow(7, $r, $row['cambios']);
            $sheet->setCellValueByColumnAndRow(8, $r, $row['estado']);
            $r++;
        }
        $this->cerrar($sheet, count($headers), $r - 1);
    }

    private function hojaDetalle(Spreadsheet $ss, array $rows, string $nuevo, string $viejo): void
    {
        $headers = ['N°', 'Facultad', 'Proyecto', 'Integrante', "Cat {$viejo}", "Horas {$viejo}", "Cat {$nuevo}", "Horas {$nuevo}", 'Estado'];
        $sheet = $ss->createSheet();
        $sheet->setTitle('Detalle');
        $this->cabecera($sheet, "Detalle integrantes: {$nuevo} vs {$viejo}", $headers);
        $r = 4; $n = 1;
        foreach ($rows as $row) {
            $sheet->setCellValueByColumnAndRow(1, $r, $n++);
            $sheet->setCellValueByColumnAndRow(2, $r, $row['facultad']);
            $sheet->setCellValueByColumnAndRow(3, $r, $row['proyecto']);
            $sheet->setCellValueByColumnAndRow(4, $r, $row['integrante']);
            $sheet->setCellValueByColumnAndRow(5, $r, $row['cat_v']);
            $sheet->setCellValueByColumnAndRow(6, $r, $row['horas_v']);
            $sheet->setCellValueByColumnAndRow(7, $r, $row['cat_n']);
            $sheet->setCellValueByColumnAndRow(8, $r, $row['horas_n']);
            $sheet->setCellValueByColumnAndRow(9, $r, $row['estado']);
            $r++;
        }
        $this->cerrar($sheet, count($headers), $r - 1);
    }

    private function cabecera($sheet, string $titulo, array $headers): void
    {
        $lastCol = Coordinate::stringFromColumnIndex(count($headers));
        $sheet->setCellValue('A1', $titulo);
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        foreach ($headers as $i => $h) {
            $sheet->setCellValueByColumnAndRow($i + 1, 3, $h);
        }
    }

    private function cerrar($sheet, int $cols, int $lastRow): void
    {
        $lastCol = Coordinate::stringFromColumnIndex($cols);
        $lastRow = max($lastRow, 3);
        $sheet->getStyle("A3:{$lastCol}3")->getFont()->setBold(true);
        $sheet->getStyle("A3:{$lastCol}3")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9E1F2');
        $sheet->getStyle("A3:{$lastCol}{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        for ($c = 1; $c <= $cols; $c++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($c))->setAutoSize(true);
        }
        $sheet->freezePane('A4');
    }

    /**
     * Normaliza la categoría: 's/c', '-', vacío y NULL son "sin categoría"
     * (mismo peso 0,5), no un cambio real. I..V quedan igual.
     */
    private function normCat($c): string
    {
        $c = strtoupper(trim((string) $c));
        if ($c === '' || $c === 'S/C' || $c === '-') {
            return 'SC';
        }
        return $c;
    }
}
