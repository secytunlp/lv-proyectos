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
 * para los proyectos que coinciden en ambos. Muestra horas (in_dedi) y
 * categoría (in_cainv), y marca el estado: igual / cambió / sólo nuevo /
 * sólo viejo. No compara montos.
 *
 * Uso:
 *   php artisan subsidios:comparar-integrantes --nuevo=intproy_2026 --viejo=intproy_2025_ppid
 */
class CompararIntegrantesSubsidios extends Command
{
    protected $signature = 'subsidios:comparar-integrantes
        {--nuevo= : Tabla intproy nueva (ej. intproy_2026). Requerido.}
        {--viejo= : Tabla intproy vieja (ej. intproy_2025_ppid). Requerido.}
        {--solo-diferencias : Excluye las filas "igual" del Excel.}
        {--salida= : Carpeta de salida.}';

    protected $description = 'Compara integrantes (horas y categoría) de intproy entre dos años, en los proyectos que coinciden.';

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

        $filasNuevo = DB::table($nuevo)->select('pr_id', 'pr_codigo', 'in_id', 'in_nombre', 'in_cainv', 'in_dedi')->get();
        $filasViejo = DB::table($viejo)->select('pr_id', 'pr_codigo', 'in_id', 'in_nombre', 'in_cainv', 'in_dedi')->get();

        // Mapas por (pr_id|in_id) y conjuntos de proyectos.
        $mapN = [];
        $proyN = [];
        foreach ($filasNuevo as $r) {
            $mapN[$r->pr_id . '|' . $r->in_id] = $r;
            $proyN[$r->pr_id] = true;
        }
        $mapV = [];
        $proyV = [];
        foreach ($filasViejo as $r) {
            $mapV[$r->pr_id . '|' . $r->in_id] = $r;
            $proyV[$r->pr_id] = true;
        }

        // Proyectos que coinciden en ambos cálculos.
        $coinciden = array_intersect_key($proyN, $proyV);

        // Une todas las claves (integrante-proyecto) de proyectos coincidentes.
        $keys = [];
        foreach ($mapN as $k => $r) {
            if (isset($coinciden[$r->pr_id])) { $keys[$k] = true; }
        }
        foreach ($mapV as $k => $r) {
            if (isset($coinciden[$r->pr_id])) { $keys[$k] = true; }
        }

        $rows = [];
        $cont = ['igual' => 0, 'cambio' => 0, 'solo_nuevo' => 0, 'solo_viejo' => 0];
        foreach (array_keys($keys) as $k) {
            $n = isset($mapN[$k]) ? $mapN[$k] : null;
            $v = isset($mapV[$k]) ? $mapV[$k] : null;
            $base = $n ?: $v;

            $catN = $n ? $n->in_cainv : null;
            $hN   = $n ? $n->in_dedi : null;
            $catV = $v ? $v->in_cainv : null;
            $hV   = $v ? $v->in_dedi : null;

            if (! $v) {
                $estado = 'SOLO NUEVO';
                $cont['solo_nuevo']++;
            } elseif (! $n) {
                $estado = 'SOLO VIEJO';
                $cont['solo_viejo']++;
            } elseif ((float) $hN !== (float) $hV || (string) $catN !== (string) $catV) {
                $estado = 'CAMBIO';
                $cont['cambio']++;
            } else {
                $estado = 'igual';
                $cont['igual']++;
            }

            if ($this->option('solo-diferencias') && $estado === 'igual') {
                continue;
            }

            $rows[] = [
                'proyecto'   => $base->pr_codigo,
                'integrante' => $base->in_nombre,
                'cat_v'      => $catV,
                'horas_v'    => $hV,
                'cat_n'      => $catN,
                'horas_n'    => $hN,
                'estado'     => $estado,
            ];
        }

        // Orden natural por código + integrante.
        usort($rows, function ($a, $b) {
            $c = strnatcmp((string) $a['proyecto'], (string) $b['proyecto']);
            return $c !== 0 ? $c : strcmp((string) $a['integrante'], (string) $b['integrante']);
        });

        $salida = $this->option('salida') ?: storage_path('app/comparacion_subsidios');
        if (! is_dir($salida)) {
            mkdir($salida, 0755, true);
        }
        $file = rtrim($salida, '/\\') . DIRECTORY_SEPARATOR . "Comparacion {$nuevo} vs {$viejo}.xlsx";
        $this->escribir($rows, $nuevo, $viejo, $file);

        $this->info(sprintf(
            'Proyectos coincidentes: %d | filas: %d (igual %d, cambio %d, solo nuevo %d, solo viejo %d)',
            count($coinciden), count($rows), $cont['igual'], $cont['cambio'], $cont['solo_nuevo'], $cont['solo_viejo']
        ));
        $this->line('  ->  ' . $file);
        return self::SUCCESS;
    }

    private function escribir(array $rows, string $nuevo, string $viejo, string $path): void
    {
        $headers = ['N°', 'Proyecto', 'Integrante', "Cat {$viejo}", "Horas {$viejo}", "Cat {$nuevo}", "Horas {$nuevo}", 'Estado'];

        $ss = new Spreadsheet();
        $sheet = $ss->getActiveSheet();
        $sheet->setTitle('Comparacion');

        $lastCol = Coordinate::stringFromColumnIndex(count($headers));
        $sheet->setCellValue('A1', "Comparación integrantes: {$nuevo} vs {$viejo}");
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $hr = 3;
        foreach ($headers as $i => $h) {
            $sheet->setCellValueByColumnAndRow($i + 1, $hr, $h);
        }

        $r = $hr + 1;
        $n = 1;
        foreach ($rows as $row) {
            $sheet->setCellValueByColumnAndRow(1, $r, $n++);
            $sheet->setCellValueByColumnAndRow(2, $r, $row['proyecto']);
            $sheet->setCellValueByColumnAndRow(3, $r, $row['integrante']);
            $sheet->setCellValueByColumnAndRow(4, $r, $row['cat_v']);
            $sheet->setCellValueByColumnAndRow(5, $r, $row['horas_v']);
            $sheet->setCellValueByColumnAndRow(6, $r, $row['cat_n']);
            $sheet->setCellValueByColumnAndRow(7, $r, $row['horas_n']);
            $sheet->setCellValueByColumnAndRow(8, $r, $row['estado']);
            $r++;
        }
        $lastRow = max($r - 1, $hr);

        $sheet->getStyle("A{$hr}:{$lastCol}{$hr}")->getFont()->setBold(true);
        $sheet->getStyle("A{$hr}:{$lastCol}{$hr}")->getFill()
            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9E1F2');
        $sheet->getStyle("A{$hr}:{$lastCol}{$lastRow}")->getBorders()
            ->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        for ($c = 1; $c <= count($headers); $c++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($c))->setAutoSize(true);
        }
        $sheet->freezePane('A' . ($hr + 1));

        (new Xlsx($ss))->save($path);
        $ss->disconnectWorksheets();
    }
}
