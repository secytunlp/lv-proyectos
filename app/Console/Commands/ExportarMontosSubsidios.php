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
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

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

        // Agrupar por facultad.
        $porFac = [];
        foreach ($rows as $r) {
            $porFac[(string) $r->facultad][] = $r;
        }
        // Orden interno de cada facultad: natural por código (I+D y luego PPID,
        // igual que el Excel definitivo: A361..A390, PPID/A024..).
        foreach ($porFac as &$lista) {
            usort($lista, function ($a, $b) {
                return strnatcmp((string) $a->pr_codigo, (string) $b->pr_codigo);
            });
        }
        unset($lista);
        // Orden de facultades: por la LETRA del código (A, B, E, F, G, H, I, J,
        // M, N, O, P, S, T, U, V, X), que es el orden del Excel definitivo
        // (NO alfabético por nombre: Agrarias antes que Artes).
        $letras = [];
        foreach ($porFac as $nombre => $lista) {
            $letras[$nombre] = $this->letraFacultad($lista[0]->pr_codigo);
        }
        asort($letras, SORT_STRING);
        $ordenado = [];
        foreach ($letras as $nombre => $l) {
            $ordenado[$nombre] = $porFac[$nombre];
        }
        $porFac = $ordenado;

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

    /**
     * Letra de facultad a partir del código (quita prefijos PPID/ o NN/).
     * Define el orden de facultades (A, B, E, F, G, H, I, J, M, N, O, P, S, T,
     * U, V, X) igual que el Excel definitivo.
     */
    private function letraFacultad($codigo): string
    {
        $c = preg_replace('#^(PPID/|\d+/)#i', '', (string) $codigo);
        $c = ltrim($c);
        $l = strtoupper(substr($c, 0, 1));
        return ($l >= 'A' && $l <= 'Z') ? $l : 'ZZ';
    }

    /**
     * Formato del Subsidios_AAAA.xlsx definitivo, con el TÍTULO y el LOGO en la
     * CABECERA de impresión (se repiten en cada página) y un SALTO DE PÁGINA
     * después de cada facultad:
     *  - columnas desde C: ID / DIRECTOR / CODIRECTOR / MONTO
     *  - encabezados grises con bordes; datos con bordes
     *  - subtotales por facultad y total general CON FÓRMULAS (=SUM / =a+b+..)
     */
    private function escribir(array $porFac, int $anio, string $path, $debajoDe = null): void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Monto de Proyectos');

        $titulo = "SUBSIDIOS {$anio} PARA PROYECTOS DE INVESTIGACIÓN Y DESARROLLO I+D y PPID";
        if ($debajoDe !== null) {
            $titulo .= ' — proyectos con monto menor a ' . number_format((float) $debajoDe, 0, ',', '.');
        }

        $logo = public_path('images/subsidios_logo.jpg');
        $hayLogo = is_file($logo);

        $r = 1;
        $subtotales = [];
        $saltos = [];            // filas de subtotal donde cortar página
        $nFac = count($porFac);
        $i = 0;
        foreach ($porFac as $facultad => $lista) {
            $i++;

            // Cabecera de la facultad (igual que el Subsidios_AAAA.xlsx original):
            // logo (imagen flotante) + "ANEXO" + título. Se repite arriba de cada
            // facultad (= arriba de cada página por el salto).
            $ar = $r;                                   // fila ANEXO
            $sheet->setCellValue("C{$ar}", 'ANEXO');
            $sheet->mergeCells("C{$ar}:F{$ar}");
            $sheet->getStyle("C{$ar}")->getFont()->setName('Arial')->setSize(12)->setBold(true);
            $sheet->getStyle("C{$ar}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getRowDimension($ar)->setRowHeight(30);
            $r++;

            $tr = $r;                                   // fila título
            $sheet->setCellValue("C{$tr}", $titulo);
            $sheet->mergeCells("C{$tr}:F{$tr}");
            $sheet->getStyle("C{$tr}")->getFont()->setName('Arial')->setSize(10)->setBold(true);
            $sheet->getStyle("C{$tr}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_BOTTOM)
                ->setWrapText(true);
            $sheet->getRowDimension($tr)->setRowHeight(52);
            if ($hayLogo) {
                $dib = new Drawing();
                $dib->setName('Logo');
                $dib->setPath($logo);
                $dib->setResizeProportional(true);
                $dib->setHeight(56);
                $dib->setCoordinates("C{$ar}");       // ancla en ANEXO, abarca ambas filas
                $dib->setOffsetX(3);
                $dib->setOffsetY(2);
                $dib->setWorksheet($sheet);
            }
            $r++;

            // Fila en blanco
            $r++;

            // Nombre de facultad
            $sheet->setCellValue("C{$r}", $facultad !== '' ? $facultad : 'SIN FACULTAD');
            $sheet->getStyle("C{$r}")->getFont()->setName('Arial')->setSize(10)->setBold(true);
            $r++;

            // Encabezados
            $hr = $r;
            $sheet->setCellValue("C{$r}", 'ID');
            $sheet->setCellValue("D{$r}", 'DIRECTOR');
            $sheet->setCellValue("E{$r}", 'CODIRECTOR');
            $sheet->setCellValue("F{$r}", 'MONTO');
            $sheet->getStyle("C{$r}:F{$r}")->getFont()->setBold(true);
            $sheet->getStyle("C{$r}:F{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C{$r}:F{$r}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('C0C0C0');
            $r++;

            // Datos
            $dataStart = $r;
            foreach ($lista as $row) {
                $sheet->setCellValue("C{$r}", $row->pr_codigo);
                $sheet->setCellValue("D{$r}", $row->pr_dirpr);
                $sheet->setCellValue("E{$r}", $row->codirector);
                $sheet->setCellValue("F{$r}", (int) $row->monto);
                $r++;
            }
            $dataEnd = $r - 1;

            // Bordes en encabezados + datos
            $sheet->getStyle("C{$hr}:F{$dataEnd}")
                ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            // Subtotal (fórmula, en la columna de monto; sin etiqueta, como el original)
            $sheet->setCellValue("F{$r}", "=SUM(F{$dataStart}:F{$dataEnd})");
            $sheet->getStyle("F{$r}")->getFont()->setBold(true);
            $sheet->getStyle("F{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("F{$r}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('BFBFBF');
            $subtotales[] = "F{$r}";

            // Salto de página después del subtotal de cada facultad (menos la última).
            if ($i < $nFac) {
                $saltos[] = $r;
            }
            $r++;
        }

        // Fila en blanco + Total general (fórmula = suma de subtotales)
        $r++;
        $sheet->setCellValue("E{$r}", 'Total');
        $sheet->getStyle("E{$r}")->getFont()->setBold(true);
        $sheet->getStyle("E{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue("F{$r}", '=' . implode('+', $subtotales));
        $sheet->getStyle("F{$r}")->getFont()->setBold(true);

        // Formato numérico de la columna de monto en todo el rango
        $sheet->getStyle("F1:F{$r}")->getNumberFormat()->setFormatCode('#,##0');

        // Anchos (D/E = director/codirector; C = id; F = monto)
        $sheet->getColumnDimension('C')->setWidth(10.1);
        $sheet->getColumnDimension('D')->setWidth(34.9);
        $sheet->getColumnDimension('E')->setWidth(34.9);
        $sheet->getColumnDimension('F')->setWidth(12);

        // Saltos de página (después del subtotal de cada facultad).
        foreach ($saltos as $sr) {
            $sheet->setBreak("C{$sr}", Worksheet::BREAK_ROW);
        }

        // Impresión: vertical, A4. Escala FIJA (NO fitToPage): "Ajustar a una
        // página" hace que Excel IGNORE los saltos de página manuales, por eso se
        // usa una escala fija para que entren las columnas y se respeten los saltos.
        $ps = $sheet->getPageSetup();
        $ps->setOrientation(PageSetup::ORIENTATION_PORTRAIT);
        $ps->setPaperSize(PageSetup::PAPERSIZE_A4);
        $ps->setScale(80);
        $ps->setPrintArea("C1:F{$r}");

        (new Xlsx($spreadsheet))->save($path);
        $spreadsheet->disconnectWorksheets();

        // PhpSpreadsheet escribe los saltos de fila como <brk id="N" man="1"/> SIN
        // el atributo max, por lo que Excel los ignora para el contenido fuera de la
        // columna A. Se inyecta max="16383" (ancho completo) como en el original.
        $this->arreglarSaltos($path);
    }

    /**
     * Agrega max="16383" a cada <brk> de rowBreaks del .xlsx ya guardado, para
     * que Excel respete los saltos de página manuales en todo el ancho.
     */
    private function arreglarSaltos(string $path): void
    {
        if (! class_exists('ZipArchive')) {
            return;
        }
        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            return;
        }
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (! preg_match('#^xl/worksheets/sheet\d+\.xml$#', (string) $name)) {
                continue;
            }
            $xml = $zip->getFromName($name);
            if ($xml === false || strpos($xml, '<rowBreaks') === false) {
                continue;
            }
            $nuevo = preg_replace_callback('#<rowBreaks\b.*?</rowBreaks>#s', function ($m) {
                return preg_replace('#<brk id="(\d+)" man="1"\s*/>#', '<brk id="$1" max="16383" man="1"/>', $m[0]);
            }, $xml);
            if ($nuevo !== null && $nuevo !== $xml) {
                $zip->deleteName($name);
                $zip->addFromString($name, $nuevo);
            }
        }
        $zip->close();
    }
}
