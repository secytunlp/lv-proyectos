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
        {--piso= : Sube a este valor a los que estén por debajo. Sin él, propone el piso máximo que entra en la reserva (MT bruto − total repartido).}
        {--crudo : Exporta una tabla plana (Facultad/ID/Director/Codirector/Monto), sin logo, títulos, secciones ni subtotales.}
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

        // --- Piso: subir a los que están por debajo ---
        $pisoOpt = $this->option('piso');
        $pisoVal = ($pisoOpt !== null && $pisoOpt !== '') ? (float) $pisoOpt : null;

        // Reserva = MT bruto − total repartido. El MT bruto queda guardado por el
        // cálculo en el comentario de dirproy_AAAA: "MT=<repartido> (<pct>% de <bruto>)".
        $mtBruto   = $this->mtBrutoDesdeComentario($tabla);
        $repartido = (float) DB::table($tabla)->sum('monto');
        $reserva   = ($mtBruto !== null) ? ($mtBruto - $repartido) : null;

        $pisoNota = null;
        if ($reserva !== null || $pisoVal !== null) {
            // Todos los montos > 0 (sin los filtros de display) para sugerir y costear.
            $montosAll = DB::table($tabla)->where('monto', '>', 0)->pluck('monto')
                ->map(function ($m) { return (float) $m; })->all();

            if ($reserva !== null && $reserva > 0) {
                $sugerido = $this->pisoMaximo($montosAll, $reserva);
                $this->info('MT bruto = ' . number_format($mtBruto, 0, ',', '.')
                    . ' | repartido = ' . number_format($repartido, 0, ',', '.')
                    . ' | reserva = ' . number_format($reserva, 0, ',', '.'));
                $this->info('Piso máximo sugerido = ' . number_format($sugerido, 0, ',', '.')
                    . ($pisoVal === null ? ' (redondealo y pasalo con --piso=)' : ''));
                if ($pisoVal === null) {
                    $pisoVal = $sugerido;
                }
            } elseif ($reserva !== null) {
                $this->warn('No hay reserva (MT bruto = total repartido). Usá --piso= si querés igualar igual.');
            }

            if ($pisoVal !== null) {
                $costo = 0.0;
                $suben = 0;
                foreach ($montosAll as $m) {
                    if ($m < $pisoVal) {
                        $costo += $pisoVal - $m;
                        $suben++;
                    }
                }
                $this->info('Piso aplicado = ' . number_format($pisoVal, 0, ',', '.')
                    . ' | sube ' . $suben . ' proyecto(s) | costo = ' . number_format($costo, 0, ',', '.'));
                if ($reserva !== null && $reserva > 0) {
                    if ($costo > $reserva) {
                        $this->warn('  OJO: se pasa de la reserva por ' . number_format($costo - $reserva, 0, ',', '.'));
                    } else {
                        $this->info('  Sobran ' . number_format($reserva - $costo, 0, ',', '.') . ' de la reserva.');
                    }
                }
                // Igualar: subir a los que están por debajo del piso (monto > 0).
                foreach ($rows as $row) {
                    if ((float) $row->monto > 0 && (float) $row->monto < $pisoVal) {
                        $row->monto = $pisoVal;
                    }
                }
                $pisoNota = 'igualados a un piso de ' . number_format($pisoVal, 0, ',', '.');
            }
        }

        // Agrupar por facultad.
        $porFac = [];
        foreach ($rows as $r) {
            $porFac[(string) $r->facultad][] = $r;
        }
        // Orden interno de cada facultad: I+D primero y PPID al final, cada grupo
        // en orden natural (igual que el Excel definitivo: A361..A390, PPID/A024..;
        // Exactas X926.. y después PPID/X080..). NO alcanza strnatcmp plano porque
        // intercala los PPID en facultades con letra > P (S, T, U, V, X).
        foreach ($porFac as &$lista) {
            usort($lista, function ($a, $b) {
                $pa = $this->esPPID($a->pr_codigo) ? 1 : 0;
                $pb = $this->esPPID($b->pr_codigo) ? 1 : 0;
                if ($pa !== $pb) {
                    return $pa - $pb;
                }
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
        $suf = '';
        $notas = [];
        if ($filtroDebajo) {
            $suf .= ' (debajo de ' . number_format((float) $debajoDe, 0, ',', '.') . ')';
            $notas[] = 'proyectos con monto menor a ' . number_format((float) $debajoDe, 0, ',', '.');
        }
        if ($pisoNota !== null) {
            $suf .= ' (piso ' . number_format((float) $pisoVal, 0, ',', '.') . ')';
            $notas[] = $pisoNota;
        }
        if ($this->option('crudo')) {
            $file = rtrim($salida, '/\\') . DIRECTORY_SEPARATOR . "Subsidios {$anio}{$suf} (crudo).xlsx";
            $this->escribirCrudo($porFac, $file);
        } else {
            $file = rtrim($salida, '/\\') . DIRECTORY_SEPARATOR . "Subsidios {$anio}{$suf}.xlsx";
            $this->escribir($porFac, $anio, $file, $notas ? implode(' — ', $notas) : null);
        }

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
    /**
     * MT bruto guardado por el cálculo en el comentario de dirproy_AAAA:
     * "Subsidios AAAA | MT=<repartido> (<pct>% de <bruto>) | ...". Devuelve el
     * bruto, o null si no se puede leer (p.ej. se calculó al 100%).
     */
    private function mtBrutoDesdeComentario(string $tabla): ?float
    {
        $row = DB::selectOne("
            SELECT TABLE_COMMENT AS c
            FROM information_schema.TABLES
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?
        ", [$tabla]);
        if (! $row || empty($row->c)) {
            return null;
        }
        if (preg_match('/MT=[\d.]+\s*\([\d.]+%\s*de\s*([\d.]+)\)/', $row->c, $m)) {
            return (float) $m[1];
        }
        return null;
    }

    /**
     * Piso máximo (entero) al que se puede igualar a los de abajo sin pasarse de
     * la reserva: busca el mayor X tal que SUM(X - monto | monto < X) <= reserva.
     *
     * @param float[] $montos
     */
    private function pisoMaximo(array $montos, float $reserva): float
    {
        if (empty($montos) || $reserva <= 0) {
            return 0.0;
        }
        $lo = min($montos);
        $hi = max($montos);
        for ($it = 0; $it < 100; $it++) {
            $mid = ($lo + $hi) / 2;
            $costo = 0.0;
            foreach ($montos as $m) {
                if ($m < $mid) {
                    $costo += $mid - $m;
                }
            }
            if ($costo <= $reserva) {
                $lo = $mid;
            } else {
                $hi = $mid;
            }
        }
        return floor($lo);
    }

    /**
     * Tabla PLANA, sin formato: encabezado + una fila por proyecto
     * (Facultad / ID / Director / Codirector / Monto). Respeta el orden de
     * facultades y el orden interno (I+D y luego PPID), y el piso si se aplicó.
     */
    private function escribirCrudo(array $porFac, string $path): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Subsidios');

        $headers = ['Facultad', 'ID', 'Director', 'Codirector', 'Monto'];
        foreach ($headers as $i => $h) {
            $sheet->setCellValueByColumnAndRow($i + 1, 1, $h);
        }
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);

        $r = 2;
        foreach ($porFac as $facultad => $lista) {
            foreach ($lista as $row) {
                $sheet->setCellValue("A{$r}", $facultad);
                $sheet->setCellValue("B{$r}", $row->pr_codigo);
                $sheet->setCellValue("C{$r}", $row->pr_dirpr);
                $sheet->setCellValue("D{$r}", $row->codirector);
                $sheet->setCellValue("E{$r}", (int) $row->monto);
                $r++;
            }
        }
        $lastRow = max($r - 1, 1);
        $sheet->getStyle("E2:E{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');
        foreach (['A', 'B', 'C', 'D', 'E'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->freezePane('A2');

        (new Xlsx($spreadsheet))->save($path);
        $spreadsheet->disconnectWorksheets();
    }

    /** Un proyecto es PPID si su código contiene "PPID" (p.ej. "PPID/A024"). */
    private function esPPID($codigo): bool
    {
        return stripos((string) $codigo, 'PPID') !== false;
    }

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
    private function escribir(array $porFac, int $anio, string $path, $nota = null): void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Monto de Proyectos');

        $titulo = "SUBSIDIOS {$anio} PARA PROYECTOS DE INVESTIGACIÓN Y DESARROLLO I+D y PPID";
        if ($nota !== null && $nota !== '') {
            $titulo .= ' — ' . $nota;
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
