<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

/**
 * Exporta a .xlsx la gente de `cargos_alfabetico` con escalafon Docente y/o
 * Docente Preuniversitario que NO tiene ninguna solicitud en `solicitud_sicadis`.
 *
 * El cruce es por documento: se normaliza a digitos sin ceros a la izquierda,
 * de los dos lados. De `solicitud_sicadis` se toma `documento` y, ademas, el DNI
 * que esta adentro del `cuil` (posiciones 3..10), asi tambien matchean las
 * solicitudes que quedaron con el documento vacio o mal cargado.
 *
 * Uso:
 *   php artisan exportar:docentes-sin-solicitud
 *   php artisan exportar:docentes-sin-solicitud --convocatoria=5
 *   php artisan exportar:docentes-sin-solicitud --detalle
 *   php artisan exportar:docentes-sin-solicitud --facultad=170 --facultad=181
 *   php artisan exportar:docentes-sin-solicitud --todas-situaciones
 *   php artisan exportar:docentes-sin-solicitud --salida=storage/app/faltantes.xlsx
 */
class ExportarDocentesSinSolicitud extends Command
{
    protected $signature = 'exportar:docentes-sin-solicitud
        {--escalafon=* : Escalafones a incluir. Por defecto "Docente" y "Docente Preuniversitario"}
        {--convocatoria= : Id de convocatoria. Si se indica, solo cuentan las solicitudes de esa convocatoria}
        {--facultad=* : Filtra por cd_facultad. Vacio = todas}
        {--solo-facultades-validas : Deja solo las 17 dependencias que usa cargos:actualizar}
        {--todas-situaciones : No excluye Renuncia / Jubilacion / Licencia sin goce de sueldos}
        {--detalle : Una fila por cargo en vez de una fila por persona}
        {--salida= : Ruta del .xlsx de salida}';

    protected $description = 'Exporta a Excel los docentes de cargos_alfabetico que no figuran en solicitud_sicadis';

    /** Escalafones que se toman si no se pasa --escalafon */
    private const ESCALAFONES = ['Docente', 'Docente Preuniversitario'];

    /** Situaciones que se descartan salvo --todas-situaciones (mismo criterio que cargos:actualizar) */
    private const SITUACIONES_EXCLUIDAS = [
        'Licencia sin goce de sueldos',
        'Renuncia',
        'Jubilación',
    ];

    /** Las 17 dependencias que filtra cargos:actualizar */
    private const FACULTADES_VALIDAS = [
        165, 167, 168, 169, 170, 171, 172, 173, 174,
        175, 176, 177, 179, 180, 181, 187, 1220,
    ];

    private const DEDDOC = [
        1 => 'Exclusiva',
        2 => 'Semi Exclusiva',
        3 => 'Simple',
    ];

    private const HEADERS_PERSONA = [
        'DNI', 'Apellido y Nombres', 'Nacimiento', 'Escalafon', 'Dependencia',
        'Cargo', 'Dedicacion', 'Situacion', 'Desde', 'Cargos',
    ];

    private const HEADERS_DETALLE = [
        'DNI', 'Apellido y Nombres', 'Nacimiento', 'Escalafon', 'Dependencia',
        'cd_facultad', 'Cargo', 'Clase', 'Dedicacion', 'Funcion', 'Situacion', 'Desde',
    ];

    public function handle(): int
    {
        $escalafones = $this->option('escalafon');
        if (empty($escalafones)) {
            $escalafones = self::ESCALAFONES;
        }

        $this->info('=== Docentes de cargos_alfabetico sin solicitud en solicitud_sicadis ===');
        $this->line('Escalafones: ' . implode(' | ', $escalafones));

        $conSolicitud = $this->documentosConSolicitud();
        if ($conSolicitud === null) {
            return self::FAILURE;
        }

        $filas = $this->cargosDocentes($escalafones);
        if ($filas->isEmpty()) {
            $this->warn('No hay filas en cargos_alfabetico con esos filtros.');
            $this->mostrarEscalafonesDisponibles();
            return self::FAILURE;
        }
        $this->line('Filas de cargos_alfabetico que pasan los filtros: ' . $filas->count());

        $faltantes = $filas->filter(function ($c) use ($conSolicitud) {
            $clave = $this->claveDoc($c->dni);
            return $clave !== '' && !isset($conSolicitud[$clave]);
        })->values();

        $sinDni = $filas->count() - $filas->filter(function ($c) {
            return $this->claveDoc($c->dni) !== '';
        })->count();
        if ($sinDni > 0) {
            $this->warn($sinDni . ' fila(s) con documento vacio o no numerico: quedan afuera.');
        }

        $personasTotal    = $filas->pluck('dni')->map(function ($d) { return $this->claveDoc($d); })
                                  ->filter()->unique()->count();
        $personasFaltante = $faltantes->pluck('dni')->map(function ($d) { return $this->claveDoc($d); })
                                      ->filter()->unique()->count();

        $this->newLine();
        $this->info('Personas distintas en el alfabetico: ' . $personasTotal);
        $this->info('Sin solicitud en solicitud_sicadis:  ' . $personasFaltante
            . ' (' . $faltantes->count() . ' cargos)');

        if ($faltantes->isEmpty()) {
            $this->warn('No hay nadie para exportar.');
            return self::SUCCESS;
        }

        $salida = $this->option('salida') ?: storage_path(
            'app/docentes_sin_solicitud_' . date('Ymd') . '.xlsx'
        );
        $this->ensureDir(dirname($salida));

        if ($this->option('detalle')) {
            $this->escribirDetalle($faltantes, $salida);
        } else {
            $this->escribirPorPersona($faltantes, $salida);
        }

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
     * la tabla no existe.
     */
    private function documentosConSolicitud(): ?array
    {
        $query = DB::table('solicitud_sicadis')->select('documento', 'cuil');

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
            $doc  = $this->claveDoc($s->documento);
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
        if ($porCuil > 0) {
            $this->line('  ' . $porCuil . ' identificada(s) solo por el CUIL (documento vacio)');
        }
        if ($sinDocumento > 0) {
            $this->warn('  ' . $sinDocumento . ' solicitud(es) sin documento ni CUIL usable: no pueden cruzarse'
                . ' (esa gente puede aparecer en el listado aunque se haya presentado).');
        }

        return $set;
    }

    /** Filas de cargos_alfabetico que entran al listado. */
    private function cargosDocentes(array $escalafones)
    {
        $query = DB::table('cargos_alfabetico')->whereIn('escalafon', $escalafones);

        if (!$this->option('todas-situaciones')) {
            $query->whereNotIn('situacion', self::SITUACIONES_EXCLUIDAS);
            $this->line('Situaciones excluidas: ' . implode(' | ', self::SITUACIONES_EXCLUIDAS));
        } else {
            $this->line('Situaciones: todas');
        }

        $facultades = $this->option('facultad');
        if (!empty($facultades)) {
            $query->whereIn('cd_facultad', $facultades);
            $this->line('Dependencias: ' . implode(', ', $facultades));
        } elseif ($this->option('solo-facultades-validas')) {
            $query->whereIn('cd_facultad', self::FACULTADES_VALIDAS);
            $this->line('Dependencias: las 17 de cargos:actualizar');
        } else {
            $this->line('Dependencias: todas');
        }

        return $query->orderBy('investigador')->orderBy('dni')->get();
    }

    // -------------------------------------------------------------------------
    // Escritura
    // -------------------------------------------------------------------------

    /** Una fila por persona: los cargos se resumen en una celda cada uno. */
    private function escribirPorPersona($faltantes, string $path): void
    {
        $grupos = $faltantes->groupBy(function ($c) {
            return $this->claveDoc($c->dni);
        });

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Sin solicitud');

        foreach (self::HEADERS_PERSONA as $i => $h) {
            $sheet->setCellValueByColumnAndRow($i + 1, 1, $h);
        }

        $r = 2;
        foreach ($grupos as $lista) {
            $primero = $lista->first();

            $sheet->setCellValueExplicitByColumnAndRow(
                1, $r, (string) $primero->dni, DataType::TYPE_STRING
            );
            $sheet->setCellValueByColumnAndRow(2,  $r, trim((string) $primero->investigador));
            $sheet->setCellValueByColumnAndRow(3,  $r, $this->fechaCorta($primero->nacimiento));
            $sheet->setCellValueByColumnAndRow(4,  $r, $this->unicos($lista, 'escalafon'));
            $sheet->setCellValueByColumnAndRow(5,  $r, $this->unicos($lista, 'ds_facultad'));
            $sheet->setCellValueByColumnAndRow(6,  $r, $this->unicos($lista, 'ds_cargo'));
            $sheet->setCellValueByColumnAndRow(7,  $r, $this->dedicaciones($lista));
            $sheet->setCellValueByColumnAndRow(8,  $r, $this->unicos($lista, 'situacion'));
            $sheet->setCellValueByColumnAndRow(9,  $r, $this->fechaCorta($this->fechaMinima($lista)));
            $sheet->setCellValueByColumnAndRow(10, $r, $lista->count());
            $r++;
        }

        $this->estilar($sheet, self::HEADERS_PERSONA, $r - 1, ['A', 'C', 'I', 'J']);
        (new Xlsx($spreadsheet))->save($path);
        $this->line('Escrito: ' . $path . ' (' . $grupos->count() . ' personas)');

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
    }

    /** Una fila por cargo, tal cual esta en cargos_alfabetico. */
    private function escribirDetalle($faltantes, string $path): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Sin solicitud');

        foreach (self::HEADERS_DETALLE as $i => $h) {
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

        $this->estilar($sheet, self::HEADERS_DETALLE, $r - 1, ['A', 'C', 'F', 'H', 'L']);
        (new Xlsx($spreadsheet))->save($path);
        $this->line('Escrito: ' . $path . ' (' . count($faltantes) . ' filas)');

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
    }

    private function estilar($sheet, array $headers, int $lastRow, array $centradas): void
    {
        $lastCol = Coordinate::stringFromColumnIndex(count($headers));

        $sheet->getStyle("A1:{$lastCol}1")->getFont()->setBold(true);
        $sheet->getStyle("A1:{$lastCol}1")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A1:{$lastCol}1")->getFill()
            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9E1F2');

        $sheet->freezePane('A2');
        $sheet->setAutoFilter("A1:{$lastCol}{$lastRow}");

        for ($c = 1; $c <= count($headers); $c++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($c))->setAutoSize(true);
        }

        foreach ($centradas as $col) {
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
            $this->line(sprintf('  %-28s %5d', $k === '' ? '(vacio)' : $k, $g->count()));
        }

        $this->newLine();
        $this->info('Por dependencia (top 20):');
        $porDep = $faltantes->groupBy('ds_facultad')->map(function ($g) {
            return $g->count();
        })->sortDesc()->take(20);
        foreach ($porDep as $dep => $n) {
            $this->line(sprintf('  %-52s %5d', $this->corta($dep, 50), $n));
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

    private function dedicaciones($lista): string
    {
        $vals = [];
        foreach ($lista as $c) {
            $d = $this->deddoc($c->cd_deddoc);
            if ($d !== '' && !in_array($d, $vals, true)) {
                $vals[] = $d;
            }
        }
        return implode(' / ', $vals);
    }

    private function unicos($lista, string $campo): string
    {
        $vals = [];
        foreach ($lista as $c) {
            $v = trim((string) $c->$campo);
            if ($v !== '' && !in_array($v, $vals, true)) {
                $vals[] = $v;
            }
        }
        return implode(' / ', $vals);
    }

    private function fechaMinima($lista)
    {
        $min = null;
        foreach ($lista as $c) {
            $f = $this->fechaIso($c->dt_fecha);
            if ($f !== '' && ($min === null || $f < $min)) {
                $min = $f;
            }
        }
        return $min;
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
