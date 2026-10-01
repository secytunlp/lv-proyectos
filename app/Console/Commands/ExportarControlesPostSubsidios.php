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
 * Controles POSTERIORES al cálculo de subsidios, a .xlsx (una hoja por control)
 * para imprimir/auditar. Corre sobre las tablas del cálculo del año
 * (dirproy_AAAA / intproy_AAAA) y la de tránsito subsidio_proyectos, así que
 * hay que ejecutarlo DESPUÉS de subsidios:calcular.
 *
 * Controles (equivalen a los #4/#5/#6 del sistema viejo):
 *   4. Integrantes en más de 2 proyectos.
 *   5. Proyectos NO subsidiados (monto 0/null y sin renuncia).
 *   6. Resumen: total repartido y conteos.
 *
 * Uso:
 *   php artisan subsidios:controles-post --anio=2026
 */
class ExportarControlesPostSubsidios extends Command
{
    protected $signature = 'subsidios:controles-post
        {--anio= : Año del cálculo (usa dirproy_AAAA / intproy_AAAA). Requerido.}
        {--salida= : Carpeta de salida para el .xlsx.}';

    protected $description = 'Exporta a Excel los controles posteriores al cálculo (integrantes en +2 proyectos / no subsidiados / resumen).';

    public function handle(): int
    {
        $anio = (int) $this->option('anio');
        if ($anio < 2000) {
            $this->error('Falta --anio (ej. 2026).');
            return self::FAILURE;
        }

        $dir = "dirproy_{$anio}";
        $int = "intproy_{$anio}";
        $ren = "subsidio_proyecto_renuncias_{$anio}";
        foreach ([$dir, $int] as $t) {
            if (! DB::getSchemaBuilder()->hasTable($t)) {
                $this->error("No existe la tabla {$t} (¿corriste subsidios:calcular --anio={$anio}?).");
                return self::FAILURE;
            }
        }
        $tieneRen = DB::getSchemaBuilder()->hasTable($ren);

        // --- Control 4: integrantes en más de 2 proyectos ---
        // Mismo formato/columnas que los controles pre-cálculo: se cruza intproy
        // (los contados) con subsidio_integrantes / subsidio_proyectos para traer
        // director, documento, alta y baja. Agrupado por integrante.
        // TODOS los datos mostrados salen EN VIVO de integrantes + personas +
        // investigadors (NO de subsidio_integrantes). Se muestra la baja REAL
        // (incluida la de 'Baja Creada/Recibida') junto al estado para auditarla:
        // el borrado de la baja es SÓLO del cálculo, acá no se blanquea nada.
        $c4 = DB::select("
            SELECT f.nombre AS facultad,
                   p.codigo AS proyecto,
                   sp.director,
                   CONCAT(per.apellido, ', ', per.nombre) AS integrante,
                   cat.nombre AS categoria,
                   per.documento,
                   CASE WHEN ig.alta = '0000-00-00' THEN '' ELSE ig.alta END AS alta,
                   CASE WHEN ig.baja = '0000-00-00' THEN '' ELSE ig.baja END AS baja,
                   ig.estado
            FROM `{$int}` t
            JOIN (
                SELECT in_id, COUNT(DISTINCT pr_id) AS cant
                FROM `{$int}`
                GROUP BY in_id
                HAVING COUNT(DISTINCT pr_id) > 2
            ) sub ON t.in_id = sub.in_id
            JOIN integrantes ig ON ig.investigador_id = t.in_id AND ig.proyecto_id = t.pr_id
            JOIN investigadors inv ON inv.id = ig.investigador_id
            JOIN personas per ON per.id = inv.persona_id
            JOIN proyectos p ON p.id = ig.proyecto_id
            LEFT JOIN categorias cat ON cat.id = CASE
                WHEN inv.categoria_id IN (6,7,8,9,10) AND inv.sicadi_id IN (6,7,8,9,10)
                     THEN LEAST(inv.categoria_id, inv.sicadi_id)
                WHEN inv.categoria_id IN (6,7,8,9,10) THEN inv.categoria_id
                WHEN inv.sicadi_id    IN (6,7,8,9,10) THEN inv.sicadi_id
                ELSE NULL
            END
            LEFT JOIN subsidio_proyectos sp ON sp.proyecto_id = t.pr_id
            LEFT JOIN facultads f ON f.id = p.facultad_id
            ORDER BY integrante, p.codigo
        ");
        // Orden natural del código dentro de cada integrante.
        usort($c4, function ($a, $b) {
            $c = strcmp((string) $a->integrante, (string) $b->integrante);
            if ($c !== 0) {
                return $c;
            }
            return strnatcmp((string) $a->proyecto, (string) $b->proyecto);
        });

        // --- Control 5: proyectos NO subsidiados (monto 0/null y sin renuncia) ---
        $renNotExists = $tieneRen
            ? "AND NOT EXISTS (SELECT 1 FROM `{$ren}` r WHERE r.proyecto_id = sp.proyecto_id)"
            : '';
        $c5 = DB::select("
            SELECT sp.proyecto, sp.director, sp.inicio, sp.fin, f.nombre AS facultad
            FROM subsidio_proyectos sp
            LEFT JOIN `{$dir}` d ON sp.proyecto_id = d.pr_id
            LEFT JOIN facultads f ON f.id = sp.facultad_id
            WHERE (d.monto IS NULL OR d.monto = 0)
              {$renNotExists}
            GROUP BY sp.proyecto_id, sp.proyecto, sp.director, sp.inicio, sp.fin, f.nombre
            ORDER BY f.nombre, sp.proyecto
        ");
        foreach ($c5 as $r) {
            $r->codigo = preg_replace('#^11/#', '', (string) $r->proyecto);
        }
        usort($c5, function ($a, $b) {
            $cf = strcmp((string) ($a->facultad ?? ''), (string) ($b->facultad ?? ''));
            if ($cf !== 0) {
                return $cf;
            }
            return strnatcmp((string) $a->codigo, (string) $b->codigo);
        });

        // --- Control 6: resumen ---
        $total  = (float) DB::table($dir)->sum('monto');
        $subsid = (int) DB::table($dir)->where('monto', '>', 0)->count();
        $cantMas2 = (int) DB::table($int)
            ->select('in_id')
            ->groupBy('in_id')
            ->havingRaw('COUNT(DISTINCT pr_id) > 2')
            ->get()->count();
        $resumen = [
            (object) ['concepto' => 'Total repartido (SUM monto)', 'valor' => number_format($total, 0, ',', '.')],
            (object) ['concepto' => 'Proyectos subsidiados (monto > 0)', 'valor' => (string) $subsid],
            (object) ['concepto' => 'Proyectos NO subsidiados', 'valor' => (string) count($c5)],
            (object) ['concepto' => 'Integrantes en más de 2 proyectos', 'valor' => (string) $cantMas2],
        ];

        $salida = $this->option('salida') ?: storage_path("app/controles_{$anio}");
        $this->ensureDir($salida);
        $file = rtrim($salida, '/\\') . DIRECTORY_SEPARATOR . "Controles post subsidios {$anio}.xlsx";

        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        $this->hoja($spreadsheet, "4 - En +2 proyectos", "Control 4 - Integrantes en más de 2 proyectos ({$anio})",
            ['Proyecto', 'Director', 'Integrante', 'Categoría', 'Documento', 'Alta', 'Baja', 'Estado'], $c4,
            function ($r) {
                return [$r->proyecto, $r->director, $r->integrante, $r->categoria, $r->documento, $this->fecha($r->alta), $this->fecha($r->baja), $r->estado];
            });

        $this->hoja($spreadsheet, "5 - No subsidiados", "Control 5 - Proyectos no subsidiados ({$anio})",
            ['Proyecto', 'Director', 'Facultad', 'Inicio', 'Fin'], $c5,
            function ($r) {
                return [$r->codigo, $r->director, $r->facultad, $this->fecha($r->inicio), $this->fecha($r->fin)];
            });

        $this->hoja($spreadsheet, "6 - Resumen", "Control 6 - Resumen ({$anio})",
            ['Concepto', 'Valor'], $resumen,
            function ($r) {
                return [$r->concepto, $r->valor];
            });

        (new Xlsx($spreadsheet))->save($file);
        $spreadsheet->disconnectWorksheets();

        $this->info("Controles post {$anio}: #4=" . count($c4) . " filas ({$cantMas2} integrantes) | #5=" . count($c5) . " proyectos | total=" . number_format($total, 0, ',', '.'));
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
