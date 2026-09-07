<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

/**
 * Exporta a .xlsx las filas de `cargos_alfabetico` con escalafon Docente y/o
 * Docente Preuniversitario cuyo documento NO aparece en `solicitud_sicadis`.
 *
 * Sale TODO: una fila por registro del alfabetico, sin filtrar situaciones ni
 * dependencias. Si una persona tiene tres cargos, salen los tres. Elegir con
 * cual quedarse es una decision posterior, no la toma este comando.
 *
 * El cruce es por documento: se normaliza a digitos sin ceros a la izquierda,
 * de los dos lados. De `solicitud_sicadis` se toma `documento` y, ademas, el DNI
 * que esta adentro del `cuil` (posiciones 3..10), asi tambien matchean las
 * solicitudes que quedaron con el documento vacio o mal cargado.
 *
 * Uso:
 *   php artisan exportar:docentes-sin-solicitud
 *   php artisan exportar:docentes-sin-solicitud --convocatoria=5
 *   php artisan exportar:docentes-sin-solicitud --facultad=170 --facultad=181
 *   php artisan exportar:docentes-sin-solicitud --salida=storage/app/faltantes.xlsx
 */
class ExportarDocentesSinSolicitud extends Command
{
    protected $signature = 'exportar:docentes-sin-solicitud
        {--escalafon=* : Escalafones a incluir. Por defecto "Docente" y "Docente Preuniversitario"}
        {--convocatoria= : Id de convocatoria. Si se indica, solo cuentan las solicitudes de esa convocatoria}
        {--facultad=* : Filtra por cd_facultad. Vacio = todas}
        {--salida= : Ruta del .xlsx de salida}';

    protected $description = 'Exporta a Excel los cargos docentes de cargos_alfabetico cuyo DNI no figura en solicitud_sicadis';

    /** Escalafones que se toman si no se pasa --escalafon */
    private const ESCALAFONES = ['Docente', 'Docente Preuniversitario'];

    private const DEDDOC = [
        1 => 'Exclusiva',
        2 => 'Semi Exclusiva',
        3 => 'Simple',
    ];

    private const HEADERS = [
        'DNI', 'Apellido y Nombres', 'Nacimiento', 'Escalafon', 'Dependencia',
        'cd_facultad', 'Cargo', 'Clase', 'Dedicacion', 'Funcion', 'Situacion', 'Desde',
    ];

    /** Columnas que van centradas */
    private const CENTRADAS = ['A', 'C', 'F', 'H', 'L'];

    public function handle(): int
    {
        $escalafones = $this->option('escalafon');
        if (empty($escalafones)) {
            $escalafones = self::ESCALAFONES;
        }

        $this->info('=== Cargos docentes sin solicitud en solicitud_sicadis ===');
        $this->line('Escalafones: ' . implode(' | ', $escalafones));

        $conSolicitud = $this->documentosConSolicitud();
        if ($conSolicitud === null) {
            return self::FAILURE;
        }

        $filas = $this->cargosDocentes($escalafones);
        if ($filas->isEmpty()) {
            $this->warn('No hay filas en cargos_alfabetico con esos escalafones.');
            $this->mostrarEscalafonesDisponibles();
            return self::FAILURE;
        }
        $this->line('Filas de cargos_alfabetico: ' . $filas->count());

        $sinDni = 0;
        $faltantes = $filas->filter(function ($c) use ($conSolicitud, &$sinDni) {
            $clave = $this->claveDoc($c->dni);
            if ($clave === '') {
                $sinDni++;
                return false;
            }
            return !isset($conSolicitud[$clave]);
        })->values();

        if ($sinDni > 0) {
            $this->warn($sinDni . ' fila(s) con documento vacio o no numerico: quedan afuera.');
        }

        $personasTotal    = $this->personasDistintas($filas);
        $personasFaltante = $this->personasDistintas($faltantes);

        $this->newLine();
        $this->info('Personas distintas en el alfabetico: ' . $personasTotal);
        $this->info('Sin solicitud en solicitud_sicadis:  ' . $personasFaltante
            . ' personas / ' . $faltantes->count() . ' cargos');

        if ($faltantes->isEmpty()) {
            $this->warn('No hay nada para exportar.');
            return self::SUCCESS;
        }

        $salida = $this->option('salida') ?: storage_path(
            'app/docentes_sin_solicitud_' . date('Ymd') . '.xlsx'
        );
        $this->ensureDir(dirname($salida));

        $this->escribir($faltantes, $salida);
        $this->resumen($faltantes);

        $this->newLine();
        $this->info('Listo: ' . $salida);
        return self::SUCCESS;
    }

    // -------------------------------------------------------------------------
    // Lados del cruce
    // -------------------------------------------------------------------------

    /**
     * Set de documentos normalizados que YA tienen solicitud. Devuelve null si
     * la tabla no se puede leer.
     */
    private function documentosConSolicitud(): ?array
    {
        // `documento` esta en el $fillable del modelo pero no existe en todas
        // las bases; el CUIL si. Se usa solo si la columna esta.
        $hayDocumento = Schema::hasColumn('solicitud_sicadis', 'documento');

        $columnas = $hayDocumento ? ['cuil', 'documento'] : ['cuil'];
        $query = DB::table('solicitud_sicadis')->select($columnas);

        $this->line($hayDocumento
            ? 'Cruce por: cuil + documento'
            : 'Cruce por: cuil (la tabla no tiene columna documento)');

        $convocatoria = $this->option('convocatoria');
        if ($convocatoria !== null && $convocatoria !== '') {
            $query->where('convocatoria_id', $convocatoria);
            $this->line('Convocatoria: solo la ' . $convocatoria);
        } else {
            $this->line('Convocatoria: todas');
        }

        try {
            $solicitudes = $query->get();
        } catch (\Exception $e) {
            $this->error('No pude leer solicitud_sicadis: ' . $e->getMessage());
            return null;
        }

        $set = [];
        $sinDocumento = 0;
        $porCuil = 0;

        // Se agregan las dos claves, la del documento y la del CUIL: si estan
        // cargadas distintas, cualquiera de las dos vale como "ya se presento".
        foreach ($solicitudes as $s) {
            $doc  = $hayDocumento ? $this->claveDoc($s->documento) : '';
            $cuil = $this->dniDesdeCuil($s->cuil);

            if ($doc !== '') {
                $set[$doc] = true;
            }
            if ($cuil !== '') {
                $set[$cuil] = true;
                if ($doc === '') {
                    $porCuil++;
                }
            }
            if ($doc === '' && $cuil === '') {
                $sinDocumento++;
            }
        }

        $this->line('Solicitudes leidas: ' . $solicitudes->count()
            . ' -> ' . count($set) . ' documentos distintos');
        if ($hayDocumento && $porCuil > 0) {
            $this->line('  ' . $porCuil . ' identificada(s) solo por el CUIL (documento vacio)');
        }
        if ($sinDocumento > 0) {
            $this->warn('  ' . $sinDocumento . ' solicitud(es) sin CUIL usable'
                . ($hayDocumento ? ' ni documento' : '') . ': no se pueden cruzar'
                . ' (esa gente puede aparecer en el listado aunque se haya presentado).');
        }

        return $set;
    }

    /**
     * Filas de cargos_alfabetico. Sin filtro de situacion: entran tambien
     * Renuncia, Jubilacion y las licencias.
     */
    private function cargosDocentes(array $escalafones)
    {
        $query = DB::table('cargos_alfabetico')->whereIn('escalafon', $escalafones);

        $facultades = $this->option('facultad');
        if (!empty($facultades)) {
            $query->whereIn('cd_facultad', $facultades);
            $this->line('Dependencias: ' . implode(', ', $facultades));
        } else {
            $this->line('Dependencias: todas');
        }
        $this->line('Situaciones: todas (no se excluye ninguna)');

        return $query
            ->orderBy('investigador')
            ->orderBy('dni')
            ->orderBy('cd_deddoc')
            ->orderBy('cd_cargo')
            ->get();
    }

    // -------------------------------------------------------------------------
    // Escritura
    // -------------------------------------------------------------------------

    private function escribir($faltantes, string $path): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Sin solicitud');

        foreach (self::HEADERS as $i => $h) {
            $sheet->setCellValueByColumnAndRow($i + 1, 1, $h);
        }

        $r = 2;
        foreach ($faltantes as $c) {
            $sheet->setCellValueExplicitByColumnAndRow(
                1, $r, (string) $c->dni, DataType::TYPE_STRING
            );
            $sheet->setCellValueByColumnAndRow(2,  $r, trim((string) $c->investigador));
            $sheet->setCellValueByColumnAndRow(3,  $r, $this->fechaCorta($c->nacimiento));
            $sheet->setCellValueByColumnAndRow(4,  $r, (string) $c->escalafon);
            $sheet->setCellValueByColumnAndRow(5,  $r, (string) $c->ds_facultad);
            $sheet->setCellValueByColumnAndRow(6,  $r, (string) $c->cd_facultad);
            $sheet->setCellValueByColumnAndRow(7,  $r, (string) $c->ds_cargo);
            $sheet->setCellValueByColumnAndRow(8,  $r, (string) $c->clase);
            $sheet->setCellValueByColumnAndRow(9,  $r, $this->deddoc($c->cd_deddoc));
            $sheet->setCellValueByColumnAndRow(10, $r, (string) $c->funcion);
            $sheet->setCellValueByColumnAndRow(11, $r, (string) $c->situacion);
            $sheet->setCellValueByColumnAndRow(12, $r, $this->fechaCorta($c->dt_fecha));
            $r++;
        }

        $this->estilar($sheet, $r - 1);
        (new Xlsx($spreadsheet))->save($path);
        $this->line('Escrito: ' . $path . ' (' . count($faltantes) . ' filas)');

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

        foreach (self::CENTRADAS as $col) {
            $sheet->getStyle("{$col}2:{$col}{$lastRow}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }
    }

    // -------------------------------------------------------------------------
    // Resumen por consola
    // -------------------------------------------------------------------------

    private function resumen($faltantes): void
    {
        $this->newLine();
        $this->info('Por escalafon:');
        foreach ($faltantes->groupBy('escalafon')->sortKeys() as $k => $g) {
            $this->line(sprintf('  %-32s %5d', $k === '' ? '(vacio)' : $k, $g->count()));
        }

        $this->newLine();
        $this->info('Por situacion:');
        $porSit = $faltantes->groupBy('situacion')->map(function ($g) {
            return $g->count();
        })->sortDesc();
        foreach ($porSit as $sit => $n) {
            $this->line(sprintf('  %-32s %5d', $sit === '' ? '(vacia)' : $this->corta($sit, 30), $n));
        }

        $this->newLine();
        $this->info('Por dependencia (top 20):');
        $porDep = $faltantes->groupBy('ds_facultad')->map(function ($g) {
            return $g->count();
        })->sortDesc()->take(20);
        foreach ($porDep as $dep => $n) {
            $this->line(sprintf('  %-52s %5d', $this->corta($dep, 50), $n));
        }

        $conVarios = $faltantes->groupBy(function ($c) {
            return $this->claveDoc($c->dni);
        })->filter(function ($g) {
            return $g->count() > 1;
        })->count();
        if ($conVarios > 0) {
            $this->newLine();
            $this->line($conVarios . ' persona(s) aparecen con mas de un cargo: salen todas sus filas.');
        }
    }

    private function mostrarEscalafonesDisponibles(): void
    {
        $vals = DB::table('cargos_alfabetico')
            ->select('escalafon', DB::raw('COUNT(*) as n'))
            ->groupBy('escalafon')->orderByDesc('n')->get();

        if ($vals->isEmpty()) {
            return;
        }
        $this->newLine();
        $this->line('Escalafones que hay en la tabla:');
        foreach ($vals as $v) {
            $this->line(sprintf('  %-32s %6d', $v->escalafon, $v->n));
        }
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function personasDistintas($filas): int
    {
        $set = [];
        foreach ($filas as $c) {
            $k = $this->claveDoc($c->dni);
            if ($k !== '') {
                $set[$k] = true;
            }
        }
        return count($set);
    }

    /** Documento normalizado: solo digitos, sin ceros a la izquierda. */
    private function claveDoc($v): string
    {
        $d = ltrim(preg_replace('/\D/', '', (string) $v), '0');
        return strlen($d) >= 6 ? $d : '';
    }

    /** DNI que esta adentro de un CUIL de 11 digitos (posiciones 3 a 10). */
    private function dniDesdeCuil($v): string
    {
        $d = preg_replace('/\D/', '', (string) $v);
        if (strlen($d) !== 11) {
            return '';
        }
        return $this->claveDoc(substr($d, 2, 8));
    }

    private function deddoc($v): string
    {
        $k = (int) $v;
        return isset(self::DEDDOC[$k]) ? self::DEDDOC[$k] : '';
    }

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

    private function fechaCorta($value): string
    {
        $iso = $this->fechaIso($value);
        if ($iso === '') {
            return '';
        }
        $ts = strtotime($iso);
        return $ts === false ? '' : date('j/n/Y', $ts);
    }

    private function corta($v, int $n): string
    {
        $v = (string) $v;
        return mb_strlen($v) > $n ? mb_substr($v, 0, $n - 1) . '.' : $v;
    }

    private function ensureDir(string $dir): void
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}
