<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Integrante;
use App\Models\Proyecto;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

/**
 * Exporta los integrantes de los proyectos acreditados que estan en ejecucion
 * durante un anio dado, con las mismas columnas que la planilla
 * "integrantes_proyectos_AAAA.xlsx".
 *
 * Uso:
 *   php artisan exportar:integrantes-excel --anio=2027
 *   php artisan exportar:integrantes-excel --anio=2027 --tipo=I+D
 *   php artisan exportar:integrantes-excel --anio=2027 --incluir-altas-pendientes
 *   php artisan exportar:integrantes-excel --anio=2027 --salida=storage/app/integrantes_2027.xlsx
 */
class ExportarIntegrantesExcel extends Command
{
    protected $signature = 'exportar:integrantes-excel
        {--anio=2027 : Anio de ejecucion. Entran los proyectos cuyo periodo inicio..fin se superpone con ese anio}
        {--estado=Acreditado : Estado del proyecto. "todos" para no filtrar}
        {--tipo= : Filtra por tipo de proyecto (I+D, PPID, PIIT-AP, PIO). Vacio = todos}
        {--facultad= : Filtra por id de facultad del proyecto. Vacio = todas}
        {--incluir-altas-pendientes : Suma los integrantes con estado "Alta Creada" / "Alta Recibida"}
        {--solo-firmes : Deja unicamente los integrantes sin tramite abierto (estado NULL o vacio)}
        {--salida= : Ruta del .xlsx de salida}';

    protected $description = 'Exporta los integrantes de proyectos acreditados en ejecucion en un anio dado a un .xlsx';

    /**
     * Estados de `integrantes` que representan a alguien que YA es integrante
     * del proyecto: aprobado (NULL / ''), con baja pedida pero todavia sin
     * aprobar, o con un cambio de horas / tipo en tramite.
     *
     * Se corresponden con los cd_estado del sistema viejo 3, 4, 5, 6, 7, 8, 9,
     * 10 y 11 (ver el CASE de SyncIntegrantes).
     */
    private const ESTADOS_INTEGRANTE = [
        '',                      // 3  aprobado
        'Baja Creada',           // 4
        'Baja Recibida',         // 5
        'Cambio Creado',         // 6
        'Cambio Recibido',       // 7
        'Cambio Hs. Creado',     // 8
        'Cambio Hs. Recibido',   // 9
        'Cambio Tipo Creado',    // 10
        'Cambio Tipo Recibido',  // 11
    ];

    /** Altas todavia no aprobadas: cd_estado 1 y 2. Fuera del listado salvo --incluir-altas-pendientes. */
    private const ESTADOS_ALTA_PENDIENTE = [
        'Alta Creada',    // 1
        'Alta Recibida',  // 2
    ];

    private const HEADERS = [
        'Investigador', 'CUIL', 'Proyecto', 'Tipo_Proyecto', 'Tipo', 'Estado',
        'Alta', 'Baja', 'Cargo', 'Ded_Doc', 'Categoria', 'SICADI',
        'becario', 'carrera', 'hs',
    ];

    /** Cache id => codigo de la tabla organismos (para la columna "carrera"). */
    private $organismos = null;

    public function handle(): int
    {
        $anio  = (int) $this->option('anio');
        $desde = sprintf('%04d-01-01', $anio);
        $hasta = sprintf('%04d-12-31', $anio);

        $proyectoIds = $this->resolverProyectos($desde, $hasta);

        if ($proyectoIds->isEmpty()) {
            $this->warn("No hay proyectos en ejecucion en {$anio} con los filtros indicados.");
            return self::FAILURE;
        }

        $this->info("Proyectos en ejecucion en {$anio}: {$proyectoIds->count()}");

        $integrantes = $this->resolverIntegrantes($proyectoIds, $desde, $hasta);

        if ($integrantes->isEmpty()) {
            $this->warn('No hay integrantes que cumplan los filtros.');
            return self::FAILURE;
        }

        $salida = $this->option('salida')
            ?: storage_path("app/integrantes_proyectos_{$anio}.xlsx");
        $this->ensureDir(dirname($salida));

        $this->escribirXlsx($integrantes, $anio, $salida);
        $this->resumen($integrantes, $proyectoIds);

        $this->info('Listo.');
        return self::SUCCESS;
    }

    // -------------------------------------------------------------------------
    // Seleccion de proyectos
    // -------------------------------------------------------------------------

    /**
     * Proyectos acreditados cuyo periodo de ejecucion se superpone con el anio:
     * empiezan el 31/12 del anio o antes y terminan el 1/1 del anio o despues.
     */
    private function resolverProyectos(string $desde, string $hasta)
    {
        $query = Proyecto::query()
            ->whereNotNull('inicio')
            ->whereNotNull('fin')
            ->where('inicio', '<=', $hasta)
            ->where('fin', '>=', $desde);

        $estado = $this->option('estado');
        if ($estado && strtolower($estado) !== 'todos') {
            $query->where('estado', $estado);
        }

        if ($tipo = $this->option('tipo')) {
            $query->where('tipo', $tipo);
        }

        if ($facultad = $this->option('facultad')) {
            $query->where('facultad_id', $facultad);
        }

        return $query->pluck('id');
    }

    // -------------------------------------------------------------------------
    // Seleccion de integrantes
    // -------------------------------------------------------------------------

    /**
     * Integrantes de esos proyectos que efectivamente forman parte durante el anio:
     *
     *  - estado: aprobados y tramites en curso (ver ESTADOS_INTEGRANTE).
     *  - baja:   sin baja, o con baja del 1/1 del anio en adelante. Una baja
     *            anterior significa que la persona ya no estaba en el proyecto.
     *  - alta:   sin alta cargada, o alta hasta el 31/12 del anio.
     *  - se descartan las filas con alta == baja (alta y baja el mismo dia).
     */
    private function resolverIntegrantes($proyectoIds, string $desde, string $hasta)
    {
        $estados = self::ESTADOS_INTEGRANTE;

        if ($this->option('solo-firmes')) {
            $estados = [''];
        } elseif ($this->option('incluir-altas-pendientes')) {
            $estados = array_merge($estados, self::ESTADOS_ALTA_PENDIENTE);
        }

        $query = Integrante::with([
                'proyecto',
                'investigador.persona',
                'cargo',
                'categoria',
                'sicadi',
                'carrerainv',
            ])
            ->whereIn('proyecto_id', $proyectoIds)
            ->where(function ($q) use ($estados) {
                $q->whereIn('estado', $estados)->orWhereNull('estado');
            })
            // Bajas: nunca las anteriores al anio pedido.
            ->where(function ($q) use ($desde) {
                $q->whereNull('baja')
                    ->orWhere('baja', '=', '0000-00-00')
                    ->orWhere('baja', '>=', $desde);
            })
            // Altas: nunca las posteriores al anio pedido.
            ->where(function ($q) use ($hasta) {
                $q->whereNull('alta')
                    ->orWhere('alta', '=', '0000-00-00')
                    ->orWhere('alta', '<=', $hasta);
            });

        $filas = $query->get()->filter(function (Integrante $i) {
            $alta = $this->fechaIso($i->alta);
            $baja = $this->fechaIso($i->baja);
            // Alta y baja el mismo dia: no hubo participacion real.
            return !($alta !== '' && $alta === $baja);
        });

        return $filas->sort(function ($a, $b) {
            $pa = $a->investigador ? $a->investigador->persona : null;
            $pb = $b->investigador ? $b->investigador->persona : null;
            $cmp = strcasecmp($this->nombreCompleto($pa), $this->nombreCompleto($pb));
            if ($cmp === 0) {
                $ca = $a->proyecto ? (string) $a->proyecto->codigo : '';
                $cb = $b->proyecto ? (string) $b->proyecto->codigo : '';
                $cmp = strnatcasecmp($ca, $cb);
            }
            return $cmp;
        })->values();
    }

    // -------------------------------------------------------------------------
    // Escritura del .xlsx
    // -------------------------------------------------------------------------

    private function escribirXlsx($integrantes, int $anio, string $path): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Integrantes ' . $anio);

        foreach (self::HEADERS as $i => $header) {
            $sheet->setCellValueByColumnAndRow($i + 1, 1, $header);
        }

        $r = 2;
        foreach ($integrantes as $i) {
            $investigador = $i->investigador;
            $persona = $investigador ? $investigador->persona : null;

            $sheet->setCellValueByColumnAndRow(1,  $r, $this->nombreCompleto($persona));
            $sheet->setCellValueExplicitByColumnAndRow(
                2, $r, (string) ($persona ? $persona->cuil : ''),
                \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
            );
            $sheet->setCellValueByColumnAndRow(3,  $r, $i->proyecto ? $i->proyecto->codigo : '');
            $sheet->setCellValueByColumnAndRow(4,  $r, $this->tipoProyecto($i->proyecto));
            $sheet->setCellValueByColumnAndRow(5,  $r, $this->mayus($i->tipo));
            $sheet->setCellValueByColumnAndRow(6,  $r, (string) $i->estado);
            $sheet->setCellValueByColumnAndRow(7,  $r, $this->fechaCorta($i->alta));
            $sheet->setCellValueByColumnAndRow(8,  $r, $this->fechaCorta($i->baja));
            $sheet->setCellValueByColumnAndRow(9,  $r, $i->cargo ? $i->cargo->nombre : '');
            $sheet->setCellValueByColumnAndRow(10, $r, (string) $i->deddoc);
            $sheet->setCellValueByColumnAndRow(11, $r, $i->categoria ? $i->categoria->nombre : '');
            $sheet->setCellValueByColumnAndRow(12, $r, $i->sicadi ? $i->sicadi->nombre : '');
            $sheet->setCellValueByColumnAndRow(13, $r, $this->becario($i));
            $sheet->setCellValueByColumnAndRow(14, $r, $this->carrera($i));
            $sheet->setCellValueByColumnAndRow(15, $r, $i->horas === null ? '' : (int) $i->horas);
            $r++;
        }

        $this->estilar($sheet, $r - 1);

        (new Xlsx($spreadsheet))->save($path);
        $this->line('    Escrito: ' . $path . ' (' . count($integrantes) . ' filas)');

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
    }

    private function estilar($sheet, int $lastRow): void
    {
        $lastCol = Coordinate::stringFromColumnIndex(count(self::HEADERS));

        $sheet->getStyle("A1:{$lastCol}1")->getFont()->setBold(true);
        $sheet->getStyle("A1:{$lastCol}1")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A1:{$lastCol}1")->getFill()
            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9E1F2');

        $sheet->freezePane('A2');
        $sheet->setAutoFilter("A1:{$lastCol}{$lastRow}");

        for ($c = 1; $c <= count(self::HEADERS); $c++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($c))->setAutoSize(true);
        }

        // Fechas, categorias y horas centradas
        foreach (['G', 'H', 'K', 'L', 'O'] as $col) {
            $sheet->getStyle("{$col}2:{$col}{$lastRow}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }
    }

    // -------------------------------------------------------------------------
    // Resumen por consola
    // -------------------------------------------------------------------------

    private function resumen($integrantes, $proyectoIds): void
    {
        $this->newLine();
        $this->info('Integrantes: ' . count($integrantes)
            . ' en ' . $integrantes->pluck('proyecto_id')->unique()->count()
            . ' de los ' . $proyectoIds->count() . ' proyectos.');

        $this->newLine();
        $this->info('Por estado:');
        $porEstado = $integrantes->groupBy(function ($i) {
            return $i->estado === null || $i->estado === '' ? '(aprobado)' : $i->estado;
        })->sortKeys();
        foreach ($porEstado as $estado => $g) {
            $this->line(sprintf('  %-24s %5d', $estado, $g->count()));
        }

        $this->newLine();
        $this->info('Por tipo de integrante:');
        $porTipo = $integrantes->groupBy(function ($i) {
            return $this->mayus($i->tipo) ?: '(sin tipo)';
        })->sortKeys();
        foreach ($porTipo as $tipo => $g) {
            $this->line(sprintf('  %-28s %5d', $tipo, $g->count()));
        }

        $conBaja = $integrantes->filter(function ($i) {
            return $this->fechaIso($i->baja) !== '';
        })->count();
        if ($conBaja > 0) {
            $this->newLine();
            $this->warn("  {$conBaja} integrante(s) con fecha de baja dentro del periodo o posterior (se listan, ver columna Baja).");
        }

        $sinPersona = $integrantes->filter(function ($i) {
            return !$i->investigador || !$i->investigador->persona;
        })->count();
        if ($sinPersona > 0) {
            $this->warn("  {$sinPersona} integrante(s) sin persona asociada (Investigador y CUIL vacios).");
        }
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function nombreCompleto($persona): string
    {
        if (!$persona) {
            return '';
        }
        return trim(trim((string) $persona->apellido) . ', ' . trim((string) $persona->nombre), ', ');
    }

    /** "Proyectos I+D", "Proyectos PPID", ... igual que la planilla original. */
    private function tipoProyecto($proyecto): string
    {
        if (!$proyecto || $proyecto->tipo === null || $proyecto->tipo === '') {
            return '';
        }
        return 'Proyectos ' . $proyecto->tipo;
    }

    /** Columna "becario": beca + organismo de la beca, p.ej. "Beca doctoral-CONICET". */
    private function becario(Integrante $i): string
    {
        $beca = trim((string) $i->beca);
        $inst = trim((string) $i->institucion);

        if ($beca === '' && $inst === '') {
            return '';
        }
        if ($beca === '') {
            return $inst;
        }
        if ($inst === '') {
            return $beca;
        }
        return $beca . '-' . $inst;
    }

    /** Columna "carrera": carrera del investigador + organismo, p.ej. "INVESTIGADOR ADJUNTO-CONICET". */
    private function carrera(Integrante $i): string
    {
        $nombre = $i->carrerainv ? trim((string) $i->carrerainv->nombre) : '';
        $org = $this->organismoCodigo($i->organismo_id);

        if ($nombre === '' && $org === '') {
            return '';
        }
        if ($org === '') {
            return $nombre;
        }
        if ($nombre === '') {
            return $org;
        }
        return $nombre . '-' . $org;
    }

    private function organismoCodigo($id): string
    {
        if (!$id) {
            return '';
        }
        if ($this->organismos === null) {
            $this->organismos = DB::table('organismos')->pluck('codigo', 'id')->toArray();
        }
        return isset($this->organismos[$id]) ? trim((string) $this->organismos[$id]) : '';
    }

    private function mayus(?string $value): string
    {
        return $value === null ? '' : mb_strtoupper(trim($value));
    }

    /** Fecha como 'Y-m-d' o '' si es nula / cero. Sirve para comparar. */
    private function fechaIso($value): string
    {
        if ($value === null || $value === '' || strpos((string) $value, '0000-00-00') === 0) {
            return '';
        }
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }
        return substr((string) $value, 0, 10);
    }

    /** Fecha como j/n/Y (1/1/2025), igual que la planilla original. */
    private function fechaCorta($value): string
    {
        $iso = $this->fechaIso($value);
        if ($iso === '') {
            return '';
        }
        $ts = strtotime($iso);
        return $ts === false ? '' : date('j/n/Y', $ts);
    }

    private function ensureDir(string $dir): void
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}
