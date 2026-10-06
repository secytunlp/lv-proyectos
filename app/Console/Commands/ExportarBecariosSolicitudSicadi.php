<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

/**
 * Cruza una planilla de becarios (con columna DNI) contra `solicitud_sicadis`
 * y exporta, por cada becario, sus presentaciones SICADI (una fila por
 * solicitud). Los que no tienen ninguna salen igual, con una fila
 * "SIN SOLICITUD" y, si aparece, el posible match por apellido+nombre.
 *
 * Solo lectura: no escribe nada en la base.
 *
 *   php artisan exportar:becarios-solicitud-sicadi "storage/app/Becas_activas_9_2026.xlsx"
 *   php artisan exportar:becarios-solicitud-sicadi planilla.xlsx --convocatoria=5
 */
class ExportarBecariosSolicitudSicadi extends Command
{
    protected $signature = 'exportar:becarios-solicitud-sicadi
        {excel : Planilla de becarios (.xls/.xlsx/.csv) con columna DNI/CUIL/Documento}
        {--convocatoria= : Id de convocatoria. Si se indica, solo esa}
        {--out= : Ruta de salida (default storage/app/becarios_solicitud_sicadi_AAAAMMDD.xlsx)}';

    protected $description = 'Exporta las presentaciones en solicitud_sicadis de los becarios de una planilla';

    /** Columnas de solicitud_sicadis a mostrar, si existen en la tabla. */
    private const COLS_SOLICITUD = [
        'id', 'estado', 'fecha', 'apellido', 'nombre', 'cuil', 'documento',
        'categoria_spu', 'categoria_solicitada', 'categoria_asignada', 'mecanismo',
        'presentacion_ua', 'beca_tipo', 'beca_entidad', 'beca_inicio', 'beca_fin',
        'area', 'subarea', 'created_at',
    ];

    public function handle(): int
    {
        $ruta = $this->argument('excel');
        if (!is_readable($ruta)) {
            $this->error('No puedo leer: ' . $ruta);
            return 1;
        }

        $becarios = $this->leerBecarios($ruta);
        if ($becarios === null) {
            return 1;
        }
        $this->info('Becarios en planilla: ' . count($becarios));

        // --- solicitudes ----------------------------------------------------
        $existentes = Schema::getColumnListing('solicitud_sicadis');
        $cols = array_values(array_intersect(self::COLS_SOLICITUD, $existentes));
        $this->line('Cruce por: cuil' . (in_array('documento', $cols, true) ? ' + documento' : ' (sin columna documento)'));

        $q = DB::table('solicitud_sicadis as s')
            ->leftJoin('sicadi_convocatorias as c', 'c.id', '=', 's.convocatoria_id')
            ->select(array_merge(
                array_map(function ($c) { return 's.' . $c; }, $cols),
                ['s.convocatoria_id', 'c.tipo as conv_tipo', 'c.year as conv_year']
            ))
            ->orderBy('c.year')->orderBy('s.id');

        $conv = $this->option('convocatoria');
        if ($conv !== null && $conv !== '') {
            $q->where('s.convocatoria_id', $conv);
        }

        $porDni = [];
        $porNombre = ['exacto' => [], 'parcial' => []];
        foreach ($q->get() as $s) {
            $claves = [];
            $k = $this->dniDesdeCuil($s->cuil ?? '');
            if ($k !== '') {
                $claves[$k] = true;
            }
            if (isset($s->documento)) {
                $k = $this->claveDoc($s->documento);
                if ($k !== '') {
                    $claves[$k] = true;
                }
            }
            foreach (array_keys($claves) as $k) {
                $porDni[$k][] = $s;
            }

            $ap = $this->normNombre($s->apellido ?? '');
            $no = $this->normNombre($s->nombre ?? '');
            if ($ap !== '' && $no !== '') {
                $det = '#' . $s->id . ' ' . trim($s->apellido) . ', ' . trim($s->nombre)
                    . ' [cuil ' . ($s->cuil ?: 's/d') . '] ' . $s->conv_tipo . ' ' . $s->conv_year;
                $porNombre['exacto'][$ap . '|' . $no][] = $det;
                $porNombre['parcial'][$ap . '|' . explode(' ', $no)[0]][] = $det;
            }
        }

        // --- armado ---------------------------------------------------------
        $cab = ['DNI', 'Apellido', 'Nombres', 'Tipo beca (planilla)', 'Facultad (planilla)',
            'Cant. solicitudes', 'Solicitud id', 'Convocatoria', 'Año', 'Estado', 'Fecha'];
        $extra = array_values(array_diff($cols, ['id', 'estado', 'fecha', 'apellido', 'nombre', 'documento']));
        $cab = array_merge($cab, $extra, ['Match por nombre', 'Detalle match nombre']);

        $filas = [];
        $con = 0;
        $sinNombre = 0;
        foreach ($becarios as $b) {
            $sols = $porDni[$b['dni']] ?? [];
            $base = [$b['dni'], $b['apellido'], $b['nombres'], $b['tipo'], $b['facultad'], count($sols)];

            if (count($sols) > 0) {
                $con++;
                foreach ($sols as $s) {
                    $f = array_merge($base, [$s->id, $s->conv_tipo, $s->conv_year,
                        $s->estado ?? '', $this->fecha($s->fecha ?? '')]);
                    foreach ($extra as $c) {
                        $f[] = $this->fecha($s->$c);
                    }
                    $filas[] = array_merge($f, ['', '']);
                }
                continue;
            }

            $f = array_merge($base, ['', 'SIN SOLICITUD', '', '', '']);
            foreach ($extra as $c) {
                $f[] = '';
            }
            $ap = $this->normNombre($b['apellido']);
            $no = $this->normNombre($b['nombres']);
            $m = ['', ''];
            if (isset($porNombre['exacto'][$ap . '|' . $no])) {
                $m = ['EXACTO', implode(' ;; ', $porNombre['exacto'][$ap . '|' . $no])];
            } elseif ($no !== '' && isset($porNombre['parcial'][$ap . '|' . explode(' ', $no)[0]])) {
                $m = ['PARCIAL', implode(' ;; ', $porNombre['parcial'][$ap . '|' . explode(' ', $no)[0]])];
            }
            if ($m[0] !== '') {
                $sinNombre++;
            }
            $filas[] = array_merge($f, $m);
        }

        $this->info('Con al menos una solicitud: ' . $con);
        $this->info('Sin solicitud por DNI:      ' . (count($becarios) - $con)
            . ' (de esos, ' . $sinNombre . ' con posible match por nombre — revisar)');

        $out = $this->option('out') ?: storage_path('app/becarios_solicitud_sicadi_' . date('Ymd') . '.xlsx');
        $this->escribir($cab, $filas, $out);
        $this->info('Excel: ' . $out);
        return 0;
    }

    private function leerBecarios(string $ruta): ?array
    {
        try {
            $reader = IOFactory::createReaderForFile($ruta);
            $reader->setReadDataOnly(true);
            $filas = $reader->load($ruta)->getSheet(0)->toArray(null, true, false, false);
        } catch (\Exception $e) {
            $this->error('No pude abrir ' . $ruta . ': ' . $e->getMessage());
            return null;
        }

        $idx = null;
        $map = [];
        foreach ($filas as $i => $fila) {
            foreach ($fila as $col => $t) {
                $t = mb_strtolower(trim((string) $t), 'UTF-8');
                if (in_array($t, ['dni', 'cuil', 'documento'], true)) $map['dni'] = $col;
                if ($t === 'apellido') $map['apellido'] = $col;
                if ($t === 'nombres' || $t === 'nombre') $map['nombres'] = $col;
                if ($t === 'tipo de beca') $map['tipo'] = $col;
                if ($t === 'facultad') $map['facultad'] = $col;
            }
            if (isset($map['dni'])) {
                $idx = $i;
                break;
            }
            if ($i > 20) break;
        }
        if ($idx === null) {
            $this->error('No encontre columna DNI/CUIL/Documento en las primeras 20 filas.');
            return null;
        }

        $out = [];
        for ($i = $idx + 1; $i < count($filas); $i++) {
            $v = $filas[$i][$map['dni']] ?? null;
            $dni = $this->dniDesdeCuil($v);
            if ($dni === '') $dni = $this->claveDoc($v);
            if ($dni === '') continue;
            $g = function ($k) use ($filas, $i, $map) {
                return isset($map[$k]) ? trim((string) ($filas[$i][$map[$k]] ?? '')) : '';
            };
            $out[] = ['dni' => $dni, 'apellido' => $g('apellido'), 'nombres' => $g('nombres'),
                'tipo' => $g('tipo'), 'facultad' => $g('facultad')];
        }
        return $out;
    }

    private function escribir(array $cab, array $filas, string $path): void
    {
        $dir = dirname($path);
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $ss = new Spreadsheet();
        $sh = $ss->getActiveSheet();
        $sh->setTitle('Becarios SICADI');
        $sh->fromArray($cab, null, 'A1');
        $sh->fromArray($filas, null, 'A2', true);

        $last = Coordinate::stringFromColumnIndex(count($cab));
        $sh->getStyle('A1:' . $last . '1')->getFont()->setBold(true);
        $sh->getStyle('A1:' . $last . '1')->getFill()
            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9E1F2');
        $sh->freezePane('A2');
        $sh->setAutoFilter('A1:' . $last . (count($filas) + 1));
        for ($c = 1; $c <= count($cab); $c++) {
            $sh->getColumnDimension(Coordinate::stringFromColumnIndex($c))->setAutoSize(true);
        }
        // resaltar los sin solicitud
        foreach ($filas as $i => $f) {
            if ($f[7] === 'SIN SOLICITUD') {
                $sh->getStyle('A' . ($i + 2) . ':' . $last . ($i + 2))->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FCE4D6');
            }
        }
        (new Xlsx($ss))->save($path);
    }

    private function fecha($v)
    {
        if ($v === null) return '';
        $s = (string) $v;
        if (strpos($s, '0000-00-00') === 0) return '';
        if (preg_match('/^\d{4}-\d{2}-\d{2}( 00:00:00)?$/', $s)) return substr($s, 0, 10);
        return $s;
    }

    private function claveDoc($v): string
    {
        $d = ltrim(preg_replace('/\D/', '', (string) $v), '0');
        return strlen($d) >= 6 ? $d : '';
    }

    private function dniDesdeCuil($v): string
    {
        $d = preg_replace('/\D/', '', (string) $v);
        if (strlen($d) === 11) return $this->claveDoc(substr($d, 2, 8));
        if (strlen($d) >= 7 && strlen($d) <= 8) return $this->claveDoc($d);
        return '';
    }

    private function normNombre($v): string
    {
        $v = mb_strtoupper(trim((string) $v), 'UTF-8');
        $v = strtr($v, ['Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U', 'Ü' => 'U', 'Ñ' => 'N']);
        $v = preg_replace('/[^A-Z ]/', ' ', $v);
        return trim(preg_replace('/\s+/', ' ', $v));
    }
}
