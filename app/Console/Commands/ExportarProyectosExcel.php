<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ExportarProyectosExcel extends Command
{
    /**
     * Usage:
     *   php artisan exportar:proyectos-excel
     *   php artisan exportar:proyectos-excel --estado=Acreditado
     *   php artisan exportar:proyectos-excel --tipo=PPID
     *   php artisan exportar:proyectos-excel --inicio=2023-01-01
     *   php artisan exportar:proyectos-excel --salida=storage/app/proyectos.xlsx
     */
    protected $signature = 'exportar:proyectos-excel
                        {--estado=Acreditado : Filtra por estado del proyecto. Usar "todos" para no filtrar}
                        {--ejecucion=1 : Solo proyectos en ejecución hoy (inicio <= hoy <= fin). 0 para incluir todos}
                        {--tipo= : Filtra por tipo (I+D, PPID, PIIT-AP, PIO). Vacío = todos}
                        {--inicio= : Filtra por fecha de inicio exacta (Y-m-d), opcional}
                        {--salida= : Ruta del archivo .xlsx de salida}';

    protected $description = 'Exporta los proyectos a un Excel (.xlsx) con columnas Tipo, Proyecto, Titulo, Inicio, Fin, Director, Email, Facultad';

    /** Etiquetas de tipo tal como se muestran en la planilla original. */
    private const TIPO_LABELS = [
    ];

    private const HEADERS = [
        'Tipo', 'Proyecto', 'Titulo', 'Inicio', 'Fin', 'Director', 'Email', 'Facultad',
    ];

    public function handle(): int
    {
        $rows = $this->fetchRows();

        if ($rows->isEmpty()) {
            $this->warn('No se encontraron proyectos para los filtros indicados.');
            return self::FAILURE;
        }

        $salida = $this->option('salida')
            ?: storage_path('app/proyectos_' . date('Ymd_His') . '.xlsx');

        $this->ensureDir(dirname($salida));

        $this->info("Encontrados {$rows->count()} proyecto(s). Generando: {$salida}");

        $this->writeXlsx($rows, $salida);

        $this->info('Listo.');
        return self::SUCCESS;
    }

    /**
     * Trae una fila por proyecto con su director. Si un proyecto tuviera
     * más de un integrante "Director", se conserva el primero (menor id).
     */
    private function fetchRows()
    {
        $query = DB::table('proyectos')
            ->leftJoin('integrantes', function ($join) {
                $join->on('proyectos.id', '=', 'integrantes.proyecto_id')
                    ->where('integrantes.tipo', '=', 'Director');
            })
            ->leftJoin('investigadors', 'integrantes.investigador_id', '=', 'investigadors.id')
            ->leftJoin('personas', 'investigadors.persona_id', '=', 'personas.id')
            ->leftJoin('facultads', 'proyectos.facultad_id', '=', 'facultads.id')
            ->select([
                'proyectos.id',
                'proyectos.tipo',
                'proyectos.codigo',
                'proyectos.titulo',
                'proyectos.inicio',
                'proyectos.fin',
                'personas.apellido',
                'personas.nombre',
                DB::raw('COALESCE(NULLIF(personas.email, ""), integrantes.email) as email'),
                'facultads.nombre as facultad',
            ])
            ->orderBy('facultads.nombre')
            ->orderBy('proyectos.codigo')
            ->orderBy('integrantes.id');

        $estado = $this->option('estado');
        if ($estado && strtolower($estado) !== 'todos') {
            $query->where('proyectos.estado', $estado);
        }

        // En ejecución: hoy está dentro del rango inicio..fin
        if ((string) $this->option('ejecucion') === '1') {
            $hoy = now()->toDateString();
            $query->where('proyectos.inicio', '<=', $hoy)
                ->where('proyectos.fin', '>=', $hoy);
        }

        if ($tipo = $this->option('tipo')) {
            $query->where('proyectos.tipo', $tipo);
        }

        if ($inicio = $this->option('inicio')) {
            $query->where('proyectos.inicio', $inicio);
        }

        // Dedup: una sola fila por proyecto (el primer director encontrado).
        return $query->get()->unique('id')->values();
    }

    private function writeXlsx($rows, string $path): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Proyectos');

        // Encabezados
        foreach (self::HEADERS as $i => $header) {
            $sheet->setCellValueByColumnAndRow($i + 1, 1, $header);
        }

        // Datos
        $r = 2;
        foreach ($rows as $row) {
            $director = trim(($row->apellido ?? '') . ', ' . ($row->nombre ?? ''), ', ');

            $sheet->setCellValueByColumnAndRow(1, $r, $this->tipoLabel($row->tipo));
            $sheet->setCellValueByColumnAndRow(2, $r, $row->codigo);
            $sheet->setCellValueByColumnAndRow(3, $r, $row->titulo);
            $sheet->setCellValueByColumnAndRow(4, $r, $this->formatDate($row->inicio));
            $sheet->setCellValueByColumnAndRow(5, $r, $this->formatDate($row->fin));
            $sheet->setCellValueByColumnAndRow(6, $r, $director);
            $sheet->setCellValueByColumnAndRow(7, $r, $row->email);
            $sheet->setCellValueByColumnAndRow(8, $r, $row->facultad);
            $r++;
        }

        $this->styleSheet($sheet, count($rows));

        (new Xlsx($spreadsheet))->save($path);
        $this->line('    Escrito: ' . $path . ' (' . count($rows) . ' filas)');

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
    }

    private function styleSheet($sheet, int $dataRows): void
    {
        $lastCol = Coordinate::stringFromColumnIndex(count(self::HEADERS));

        // Encabezado en negrita y centrado
        $sheet->getStyle("A1:{$lastCol}1")->getFont()->setBold(true);
        $sheet->getStyle("A1:{$lastCol}1")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Fila de encabezado congelada
        $sheet->freezePane('A2');

        // Anchos de columna automáticos
        for ($c = 1; $c <= count(self::HEADERS); $c++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($c))
                ->setAutoSize(true);
        }
    }

    private function tipoLabel(?string $tipo): string
    {
        $tipo = (string) $tipo;
        return self::TIPO_LABELS[$tipo] ?? $tipo;
    }

    /**
     * Devuelve la fecha como j/n/Y (ej. 1/1/2019), igual que la planilla original.
     * Valores nulos / vacíos / '0000-00-00' quedan en blanco.
     */
    private function formatDate($value): string
    {
        if ($value === null || $value === '' || str_starts_with((string) $value, '0000-00-00')) {
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
