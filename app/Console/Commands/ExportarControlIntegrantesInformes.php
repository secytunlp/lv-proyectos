<?php

namespace App\Console\Commands;

use App\Models\Integrante;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * "Control Integrantes Informes AAAA": por facultad, cada proyecto que presenta
 * informe ese año (cohortes de config/informes.php) con la lista de sus
 * integrantes: categoría, tipo, alta, baja y estado del trámite.
 *
 * Reemplaza el .rtf por facultad que generaba el sistema viejo.
 *
 * Uso:
 *   php artisan informes:control-integrantes --anio=2025
 *   php artisan informes:control-integrantes --anio=2025 --juntos
 *   php artisan informes:control-integrantes --anio=2025 --facultad=1
 *   php artisan informes:control-integrantes --anio=2025 --solo-anio
 */
class ExportarControlIntegrantesInformes extends Command
{
    protected $signature = 'informes:control-integrantes
        {--anio=2025 : Año del informe (debe estar en config/informes.php)}
        {--estado=Acreditado : Estado del proyecto. "todos" para no filtrar}
        {--facultad= : Id de facultad del proyecto. Vacío = todas}
        {--tipo= : Programa (I+D, PPID). Vacío = todos los del año}
        {--incluir-altas-pendientes : Suma los integrantes en "Alta Creada" / "Alta Recibida"}
        {--solo-anio : Excluye las bajas anteriores al 1/1 del año del informe}
        {--juntos : Un único .xlsx con una hoja por facultad}
        {--salida= : Carpeta de salida}';

    protected $description = 'Control de integrantes por proyecto para los informes del año, un Excel por facultad';

    /** Mismos estados que exportar:integrantes-excel (cd_estado 3..11 del sistema viejo). */
    private const ESTADOS_INTEGRANTE = [
        '', 'Baja Creada', 'Baja Recibida', 'Cambio Creado', 'Cambio Recibido',
        'Cambio Hs. Creado', 'Cambio Hs. Recibido', 'Cambio Tipo Creado', 'Cambio Tipo Recibido',
    ];

    private const ESTADOS_ALTA_PENDIENTE = ['Alta Creada', 'Alta Recibida'];

    /** Tipos de integrante que muestran el email, como en el listado viejo. */
    private const TIPOS_CON_EMAIL = ['DIRECTOR', 'CODIRECTOR'];

    private const COLS = ['Integrante', 'Email', 'CAT', 'SICADI', 'Tipo', 'Alta', 'Baja', 'Estado'];

    private const AZUL = '1F3F99';

    /** Proyectos cuyo Director tiene alta posterior al año del informe (para el resumen). */
    private $directoresPosteriores = [];

    private const NOMBRE_CORTO = [
        'FACULTAD DE CIENCIAS AGRARIAS Y FORESTALES'         => 'Agrarias',
        'FACULTAD DE CIENCIAS VETERINARIAS'                  => 'Veterinarias',
        'FACULTAD DE ARQUITECTURA Y URBANISMO'               => 'Arquitectura',
        'FACULTAD DE INGENIERIA'                             => 'Ingenieria',
        'FACULTAD DE CIENCIAS EXACTAS'                       => 'Exactas',
        'FACULTAD DE CIENCIAS ASTRONOMICAS Y GEOFISICAS'     => 'Astronomicas',
        'FACULTAD DE CIENCIAS ECONOMICAS'                    => 'Economicas',
        'FACULTAD DE CIENCIAS JURIDICAS Y SOCIALES'          => 'Juridicas',
        'FACULTAD DE PERIODISMO Y COMUNICACION SOCIAL'       => 'Periodismo',
        'FACULTAD DE HUMANIDADES Y CIENCIAS DE LA EDUCACION' => 'Humanidades',
        'FACULTAD DE BELLAS ARTES'                           => 'Artes',
        'FACULTAD DE ARTES'                                  => 'Artes',
        'FACULTAD DE CIENCIAS MEDICAS'                       => 'Medicas',
        'FACULTAD DE TRABAJO SOCIAL'                         => 'Trabajo Social',
        'FACULTAD DE ODONTOLOGIA'                            => 'Odontologia',
        'FACULTAD DE CIENCIAS NATURALES Y MUSEO'             => 'Naturales',
        'FACULTAD DE INFORMATICA'                            => 'Informatica',
        'FACULTAD DE PSICOLOGIA'                             => 'Psicologia',
    ];

    public function handle(): int
    {
        $anio = (int) $this->option('anio');
        $rangos = config('informes.rangos', []);

        if (!isset($rangos[$anio])) {
            $this->error("No hay rangos definidos para {$anio}. Agregalos en config/informes.php.");
            return self::FAILURE;
        }

        $mapa = $rangos[$anio];
        if ($tipo = $this->option('tipo')) {
            $mapa = array_intersect_key($mapa, [$tipo => true]);
            if (!$mapa) {
                $this->error("El programa {$tipo} no presenta informes en {$anio}.");
                return self::FAILURE;
            }
        }

        $proyectos = $this->proyectos($mapa);
        if ($proyectos->isEmpty()) {
            $this->warn('Ningún proyecto cae en las cohortes del año.');
            return self::FAILURE;
        }

        $integrantes = $this->integrantes($proyectos->pluck('id'), $anio)->groupBy('proyecto_id');

        $salida = rtrim($this->option('salida') ?: storage_path("app/control_integrantes_{$anio}"), '/\\');
        if (!is_dir($salida)) {
            mkdir($salida, 0755, true);
        }

        $porFacultad = $proyectos->groupBy(function ($p) {
            return (string) $p->facultad;
        })->sortKeys();

        if ($this->option('juntos')) {
            $book = new Spreadsheet();
            $book->removeSheetByIndex(0);
            foreach ($porFacultad as $facultad => $proys) {
                $sheet = $book->createSheet();
                $sheet->setTitle(mb_substr($this->nombreCorto($facultad), 0, 31));
                $this->escribirHoja($sheet, $facultad, $proys, $integrantes, $anio);
            }
            $book->setActiveSheetIndex(0);
            $file = "{$salida}/Control Integrantes Informes {$anio} - TODAS.xlsx";
            (new Xlsx($book))->save($file);
            $book->disconnectWorksheets();
            $this->line('  -> ' . basename($file));
        } else {
            foreach ($porFacultad as $facultad => $proys) {
                $book = new Spreadsheet();
                $sheet = $book->getActiveSheet();
                $sheet->setTitle('Informes ' . $anio);
                $this->escribirHoja($sheet, $facultad, $proys, $integrantes, $anio);
                $file = "{$salida}/Control Integrantes Informes {$anio} - " . $this->nombreCorto($facultad) . '.xlsx';
                (new Xlsx($book))->save($file);
                $book->disconnectWorksheets();
                $n = $proys->sum(function ($p) use ($integrantes) {
                    return count($integrantes[$p->id] ?? []);
                });
                $this->line(sprintf('  %-16s %3d proyectos  %4d integrantes  -> %s',
                    $this->nombreCorto($facultad), $proys->count(), $n, basename($file)));
            }
        }

        $this->resumen($proyectos, $integrantes);
        $this->info("Listo. Archivos en: {$salida}");
        return self::SUCCESS;
    }

    // -------------------------------------------------------------------------
    // Datos
    // -------------------------------------------------------------------------

    /** Proyectos del año con su tipo de informe según la cohorte, y el director. */
    private function proyectos(array $mapa)
    {
        $q = DB::table('proyectos as p')
            ->leftJoin('facultads as f', 'p.facultad_id', '=', 'f.id')
            ->whereIn('p.tipo', array_keys($mapa))
            ->whereNotNull('p.inicio')
            ->whereNotNull('p.fin')
            ->select('p.id', 'p.tipo', 'p.codigo', 'p.titulo', 'p.inicio', 'p.fin', 'f.nombre as facultad');

        $estado = $this->option('estado');
        if ($estado && strtolower($estado) !== 'todos') {
            $q->where('p.estado', $estado);
        }
        if ($fac = $this->option('facultad')) {
            $q->where('p.facultad_id', $fac);
        }

        return $q->get()->map(function ($p) use ($mapa) {
            $clave = date('Y', strtotime($p->inicio)) . '-' . date('Y', strtotime($p->fin));
            $p->informe = $mapa[$p->tipo][$clave] ?? null;
            return $p;
        })->filter(function ($p) {
            return $p->informe !== null;
        })->sort(function ($a, $b) {
            return strcmp($a->tipo, $b->tipo) ?: strnatcmp((string) $a->codigo, (string) $b->codigo);
        })->values();
    }

    /**
     * Integrantes de esos proyectos que participaron en el período del informe:
     * alta hasta el 31/12 del año (salvo el Director actual, que entra siempre); bajas de cualquier fecha (es el historial del
     * proyecto, como en el control 2024) salvo --solo-anio. Se descartan las
     * filas con alta == baja.
     */
    private function integrantes($proyectoIds, int $anio)
    {
        $estados = self::ESTADOS_INTEGRANTE;
        if ($this->option('incluir-altas-pendientes')) {
            $estados = array_merge($estados, self::ESTADOS_ALTA_PENDIENTE);
        }
        $hasta = "{$anio}-12-31";
        $desde = "{$anio}-01-01";

        $q = Integrante::with(['investigador.persona', 'categoria', 'sicadi'])
            ->whereIn('proyecto_id', $proyectoIds)
            ->where(function ($w) use ($estados) {
                $w->whereIn('estado', $estados)->orWhereNull('estado');
            })
            // El Director entra aunque su alta sea posterior al año: es quien
            // presenta el informe (caso 11/V325, cambio de director en 2026).
            ->where(function ($w) use ($hasta) {
                $w->whereNull('alta')->orWhere('alta', '0000-00-00')->orWhere('alta', '<=', $hasta)
                    ->orWhere('tipo', 'Director');
            });

        if ($this->option('solo-anio')) {
            $q->where(function ($w) use ($desde) {
                $w->whereNull('baja')->orWhere('baja', '0000-00-00')->orWhere('baja', '>=', $desde);
            });
        }

        return $q->get()->filter(function ($i) {
            $alta = $this->iso($i->alta);
            return !($alta !== '' && $alta === $this->iso($i->baja));
        })->sort(function ($a, $b) {
            return strcasecmp($this->nombre($a), $this->nombre($b));
        })->values();
    }

    // -------------------------------------------------------------------------
    // Hoja
    // -------------------------------------------------------------------------

    private function escribirHoja($sheet, string $facultad, $proyectos, $integrantes, int $anio): void
    {
        $sheet->setCellValue('A1', "Control Integrantes Informes {$anio}");
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setItalic(true)->setSize(16)
            ->getColor()->setRGB(self::AZUL);

        $sheet->setCellValue('A2', $facultad ?: '(sin facultad)');
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A2')->getFont()->setSize(12);

        $r = 4;
        foreach ($proyectos as $p) {
            // Cabecera del proyecto
            $sheet->setCellValue("A{$r}", "Proyecto  {$p->codigo}   ({$p->tipo})");
            $sheet->setCellValue("B{$r}", 'Inicio  ' . $this->corta($p->inicio));
            $sheet->setCellValue("D{$r}", 'Fin  ' . $this->corta($p->fin));
            $sheet->mergeCells("D{$r}:E{$r}");
            $sheet->setCellValue("F{$r}", 'Informe');
            $sheet->setCellValue("G{$r}", mb_strtoupper($p->informe));
            $sheet->mergeCells("G{$r}:H{$r}");
            $sheet->getStyle("A{$r}:H{$r}")->getFont()->setBold(true)->setItalic(true)
                ->getColor()->setRGB(self::AZUL);
            $sheet->getStyle("A{$r}:H{$r}")->getFill()->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('E8ECF7');
            $sheet->getStyle("A{$r}:H{$r}")->getBorders()->getTop()->setBorderStyle(Border::BORDER_MEDIUM);
            $r++;

            if ($p->titulo) {
                $sheet->setCellValue("A{$r}", $p->titulo);
                $sheet->mergeCells("A{$r}:H{$r}");
                $sheet->getStyle("A{$r}")->getFont()->setItalic(true)->setSize(9);
                $sheet->getStyle("A{$r}")->getAlignment()->setWrapText(true);
                $sheet->getRowDimension($r)->setRowHeight(mb_strlen($p->titulo) > 120 ? 26 : 13);
                $r++;
            }

            // Encabezado de integrantes
            foreach (self::COLS as $c => $h) {
                $sheet->setCellValueByColumnAndRow($c + 1, $r, $h);
            }
            $sheet->getStyle("A{$r}:H{$r}")->getFont()->setBold(true)->setItalic(true)
                ->getColor()->setRGB(self::AZUL);
            $sheet->getStyle("A{$r}:H{$r}")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
            $r++;

            $lista = $integrantes[$p->id] ?? collect();
            if ($lista->isEmpty()) {
                $sheet->setCellValue("A{$r}", '(sin integrantes)');
                $sheet->getStyle("A{$r}")->getFont()->setItalic(true);
                $r++;
            }
            $ini = $r;
            foreach ($lista as $i) {
                $tipo = mb_strtoupper(trim((string) $i->tipo));
                $persona = $i->investigador ? $i->investigador->persona : null;
                $email = in_array($tipo, self::TIPOS_CON_EMAIL, true)
                    ? trim((string) ($i->email ?: ($persona->email ?? '')))
                    : '';

                $sheet->setCellValue("A{$r}", $this->nombre($i));
                $sheet->setCellValueExplicit("B{$r}", $email, DataType::TYPE_STRING);
                $sheet->setCellValue("C{$r}", $this->cat($i->categoria));
                $sheet->setCellValue("D{$r}", $this->cat($i->sicadi));
                $sheet->setCellValue("E{$r}", $tipo);
                $sheet->setCellValue("F{$r}", $this->corta($i->alta));
                $sheet->setCellValue("G{$r}", $this->corta($i->baja));
                $sheet->setCellValue("H{$r}", (string) $i->estado);

                // Director con alta posterior al año del informe: se marca en la fila.
                $alta = $this->iso($i->alta);
                if ($tipo === 'DIRECTOR' && $alta !== '' && $alta > "{$anio}-12-31") {
                    $nota = "Director posterior al informe {$anio} (alta " . $this->corta($i->alta) . ')';
                    $estado = trim((string) $i->estado);
                    $sheet->setCellValue("H{$r}", $estado !== '' ? "{$estado} · {$nota}" : $nota);
                    $sheet->getStyle("A{$r}:H{$r}")->getFill()->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('FFF2CC');
                    $sheet->getStyle("F{$r}")->getFont()->setBold(true);
                    $sheet->getStyle("H{$r}")->getAlignment()->setWrapText(true);
                    $this->directoresPosteriores[] = "{$p->codigo} ({$this->nombre($i)}, alta {$this->corta($i->alta)})";
                }
                $r++;
            }
            if ($r > $ini) {
                $sheet->getStyle("C{$ini}:D" . ($r - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("F{$ini}:G" . ($r - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A{$ini}:H" . ($r - 1))->getFont()->setSize(9);
            }
            $r++; // fila en blanco entre proyectos
        }

        foreach (['A' => 36, 'B' => 28, 'C' => 6, 'D' => 7, 'E' => 30, 'F' => 11, 'G' => 11, 'H' => 18] as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }
        $sheet->getStyle("A4:H{$r}")->getAlignment()->setVertical(Alignment::VERTICAL_TOP);

        // Listo para imprimir: A4 horizontal, ajustado a una página de ancho.
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_A4)
            ->setFitToWidth(1)->setFitToHeight(0);
        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 2);
        $sheet->getPageMargins()->setLeft(0.4)->setRight(0.4)->setTop(0.5)->setBottom(0.5);
    }

    private function resumen($proyectos, $integrantes): void
    {
        $this->newLine();
        $this->info('Por tipo de informe:');
        foreach ($proyectos->groupBy('informe') as $tipo => $g) {
            $this->line(sprintf('  %-18s %4d', $tipo, $g->count()));
        }

        $sinIntegrantes = $proyectos->filter(function ($p) use ($integrantes) {
            return empty($integrantes[$p->id]) || count($integrantes[$p->id]) === 0;
        });
        if ($sinIntegrantes->count()) {
            $this->warn("  {$sinIntegrantes->count()} proyecto(s) sin integrantes: "
                . $sinIntegrantes->pluck('codigo')->implode(', '));
        }

        $sinDirector = $proyectos->filter(function ($p) use ($integrantes) {
            return !collect($integrantes[$p->id] ?? [])->contains(function ($i) {
                return mb_strtoupper(trim((string) $i->tipo)) === 'DIRECTOR';
            });
        });
        if ($sinDirector->count()) {
            $this->warn("  {$sinDirector->count()} proyecto(s) sin Director en el listado: "
                . $sinDirector->pluck('codigo')->implode(', '));
        }

        if ($this->directoresPosteriores) {
            $this->warn('  ' . count($this->directoresPosteriores)
                . ' proyecto(s) con Director de alta posterior al año (marcados en amarillo): '
                . implode('; ', $this->directoresPosteriores));
        }

        $sinPersona = $integrantes->flatten(1)->filter(function ($i) {
            return !$i->investigador || !$i->investigador->persona;
        })->count();
        if ($sinPersona) {
            $this->warn("  {$sinPersona} integrante(s) sin persona asociada (nombre vacío).");
        }
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function nombre($i): string
    {
        $p = $i->investigador ? $i->investigador->persona : null;
        return $p ? trim(trim((string) $p->apellido) . ', ' . trim((string) $p->nombre), ', ') : '';
    }

    /** s/c, '-' y vacío se muestran todos como s/c. */
    private function cat($modelo): string
    {
        $n = $modelo ? trim((string) $modelo->nombre) : '';
        return ($n === '' || $n === '-') ? 's/c' : $n;
    }

    private function iso($v): string
    {
        if ($v === null || $v === '' || strpos((string) $v, '0000-00-00') === 0) {
            return '';
        }
        return $v instanceof \DateTimeInterface ? $v->format('Y-m-d') : substr((string) $v, 0, 10);
    }

    /** dd/mm/aaaa, como el listado viejo. */
    private function corta($v): string
    {
        $iso = $this->iso($v);
        return $iso === '' ? '' : date('d/m/Y', strtotime($iso));
    }

    private function nombreCorto(?string $facultad): string
    {
        $facultad = (string) $facultad;
        if (isset(self::NOMBRE_CORTO[$facultad])) {
            return self::NOMBRE_CORTO[$facultad];
        }
        $corto = preg_replace('/^FACULTAD DE\s+/i', '', $facultad);
        $corto = str_replace(['/', '\\', ':', '*', '?', '[', ']'], '-', $corto);
        return $corto !== '' ? ucwords(mb_strtolower($corto)) : 'SinFacultad';
    }
}
