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
 * Listado definitivo de subsidios por facultad (montos), formato igual al
 * Subsidios_AAAA.xlsx: título + secciones por facultad, columnas
 * ID / Director / Codirector / Monto. Sale de dirproy_AAAA (cálculo confirmado).
 *
 * Uso:
 *   php artisan subsidios:montos --anio=2026
 *   php artisan subsidios:montos --anio=2026 --solo-con-monto
 */
class ExportarMontosSubsidios extends Command
{
    protected $signature = 'subsidios:montos
        {--anio= : Año (usa dirproy_AAAA). Requerido.}
        {--solo-con-monto : Excluye los proyectos con monto 0.}
        {--debajo-de= : Sólo proyectos con monto MENOR a este valor (ej. 100000).}
        {--salida= : Carpeta de salida.}';

    protected $description = 'Genera el listado de subsidios por facultad (ID/Director/Codirector/Monto) desde dirproy_AAAA.';

    public function handle(): int
    {
        $anio = (int) $this->option('anio');
        if ($anio < 2000) {
            $this->error('Falta --anio (ej. 2026).');
            return self::FAILURE;
        }
        $tabla = "dirproy_{$anio}";
        if (! DB::getSchemaBuilder()->hasTable($tabla)) {
            $this->error("No existe la tabla {$tabla} (¿corriste subsidios:calcular?).");
            return self::FAILURE;
        }

        $bajaCod = $anio . '-01-01'; // codirector activo: baja NULL/0000/> este

        $q = DB::table($tabla . ' as d')
            ->leftJoin('facultads as f', 'f.id', '=', 'd.fac_id')
            ->select('f.nombre as facultad', 'd.pr_codigo', 'd.pr_dirpr', 'd.monto', 'd.pr_id')
            ->selectRaw("(
                SELECT GROUP_CONCAT(CONCAT(per.apellido, ', ', per.nombre) SEPARATOR ' / ')
                FROM integrantes i
                    JOIN investigadors inv ON i.investigador_id = inv.id
                    JOIN personas per      ON inv.persona_id = per.id
                WHERE i.proyecto_id = d.pr_id
                  AND i.tipo = 'Codirector'
                  AND (i.baja IS NULL OR i.baja = '0000-00-00' OR i.baja > ?)
            ) AS codirector", [$bajaCod]);

        if ($this->option('solo-con-monto')) {
            $q->where('d.monto', '>', 0);
        }

        $debajoDe = $this->option('debajo-de');
        $filtroDebajo = ($debajoDe !== null && $debajoDe !== '');
        if ($filtroDebajo) {
            $q->where('d.monto', '<', (float) $debajoDe);
        }

        $rows = $q->get();
        if ($rows->isEmpty()) {
            $this->warn("No hay filas en {$tabla}.");
            return self::FAILURE;
        }

        // Agrupar por facultad; orden natural del código dentro de cada una.
        $porFac = [];
        foreach ($rows as $r) {
            $porFac[(string) $r->facultad][] = $r;
        }
        ksort($porFac);
        foreach ($porFac as &$lista) {
            usort($lista, function ($a, $b) {
                return strnatcmp((string) $a->pr_codigo, (string) $b->pr_codigo);
            });
        }
        unset($lista);

        $salida = $this->option('salida') ?: storage_path("app/subsidios_{$anio}");
        if (! is_dir($salida)) {
            mkdir($salida, 0755, true);
        }
        $suf = $filtroDebajo ? ' (debajo de ' . number_format((float) $debajoDe, 0, ',', '.') . ')' : '';
        $file = rtrim($salida, '/\\') . DIRECTORY_SEPARATOR . "Subsidios {$anio}{$suf}.xlsx";
        $this->escribir($porFac, $anio, $file, $filtroDebajo ? (float) $debajoDe : null);

        $total = (float) $rows->sum('monto');
        $this->info("Subsidios {$anio}{$suf}: {$rows->count()} proyecto(s) en " . count($porFac) . " facultad(es). Total: " . number_format($total, 0, ',', '.'));
        $this->line('  ->  ' . $file);
        return self::SUCCESS;
    }

    private function escribir(array $porFac, int $anio, string $path, $debajoDe = null): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Monto de Proyectos');

        // Título
        $titulo = "SUBSIDIOS {$anio} PARA PROYECTOS DE INVESTIGACIÓN Y DESARROLLO I+D y PPID";
        if ($debajoDe !== null) {
            $titulo .= ' — proyectos con monto menor a ' . number_format((float) $debajoDe, 0, ',', '.');
        }
        $sheet->setCellValue('A1', $titulo);
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $r = 3;
        $granTotal = 0.0;
        foreach ($porFac as $facultad => $lista) {
            // Nombre de facultad
            $sheet->setCellValue("A{$r}", $facultad !== '' ? $facultad : 'SIN FACULTAD');
            $sheet->mergeCells("A{$r}:D{$r}");
            $sheet->getStyle("A{$r}")->getFont()->setBold(true);
            $sheet->getStyle("A{$r}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9E1F2');
            $r++;

            // Encabezado
            $hr = $r;
            foreach (['ID', 'DIRECTOR', 'CODIRECTOR', 'MONTO'] as $i => $h) {
                $sheet->setCellValueByColumnAndRow($i + 1, $r, $h);
            }
            $sheet->getStyle("A{$r}:D{$r}")->getFont()->setBold(true);
            $r++;

            $subtotal = 0.0;
            $start = $r;
            foreach ($lista as $row) {
                $sheet->setCellValueByColumnAndRow(1, $r, $row->pr_codigo);
                $sheet->setCellValueByColumnAndRow(2, $r, $row->pr_dirpr);
                $sheet->setCellValueByColumnAndRow(3, $r, $row->codirector);
                $sheet->setCellValueByColumnAndRow(4, $r, (int) $row->monto);
                $subtotal += (float) $row->monto;
                $r++;
            }

            // Subtotal facultad
            $sheet->setCellValue("C{$r}", 'Subtotal');
            $sheet->setCellValueByColumnAndRow(4, $r, (int) $subtotal);
            $sheet->getStyle("C{$r}:D{$r}")->getFont()->setBold(true);
            $sheet->getStyle("A{$hr}:D{$r}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $granTotal += $subtotal;
            $r += 2; // fila en blanco entre facultades
        }

        // Gran total
        $sheet->setCellValue("C{$r}", 'TOTAL GENERAL');
        $sheet->setCellValueByColumnAndRow(4, $r, (int) $granTotal);
        $sheet->getStyle("C{$r}:D{$r}")->getFont()->setBold(true)->setSize(12);

        // Anchos
        $anchos = ['A' => 14, 'B' => 38, 'C' => 38, 'D' => 16];
        foreach ($anchos as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }
        $sheet->getStyle("D1:D{$r}")->getNumberFormat()->setFormatCode('#,##0');

        (new Xlsx($spreadsheet))->save($path);
        $spreadsheet->disconnectWorksheets();
    }
}
