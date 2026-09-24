<?php

namespace App\Console\Commands;

use App\Constants;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

/**
 * Audita el tope de edad de Jóvenes Investigadores.
 *
 * El control de enviar() comparaba la edad al momento del envío ($fechaNacimiento->age)
 * contra el tope, en lugar de la edad a la fecha límite (30/12 del año de la convocatoria),
 * que es lo que dice el reglamento y el propio mensaje de error. Resultado: pasó todo el que
 * cumplía el tope entre su fecha de envío y esa fecha límite. Ya está corregido; esto mide
 * a quién dejó pasar.
 *
 * El tope no se aplica a los becarios UNLP, y eso es del reglamento. Pero "becario UNLP"
 * sale del flag joven_becas.unlp, que es un dato guardado y no derivado de la institución:
 * una beca de otra institución con unlp = 1 se saltea el control sin corresponderle.
 *
 *   DEJADA PASAR (EDAD)  ya enviada, no es becaria UNLP y al 30/12 supera el tope.
 *   INSUFICIENTE (EDAD)  igual pero todavía en Creada: con el arreglo no va a poder enviar.
 *   EXENTO BECA UNLP     supera el tope pero es becaria UNLP: correcto.
 *   EXENTO FLAG DUDOSO   supera el tope y se salva por unlp = 1 con institución que no es
 *                        UNLP. Hay que mirarlo de a uno.
 *   SIN NACIMIENTO       sin fecha de nacimiento cargada: el control no corre.
 *   OK                   no llega al tope.
 *
 * Solo lee. No modifica nada.
 */
class AuditarEdadJovenes extends Command
{
    protected $signature = 'jovens:auditar-edad
        {--anio= : Periodo de las solicitudes. Por defecto Constants::YEAR_JOVENES}
        {--tope= : Edad tope. Por defecto Constants::TOPE_EDAD_JOVENES}
        {--cuil= : Una sola persona (los guiones se ignoran)}
        {--estado= : Filtra por un estado puntual de la solicitud}
        {--incluir-creadas : Incluye las solicitudes que todavia no se enviaron}
        {--solo= : Muestra solo las filas cuyo diagnostico contenga este texto}
        {--sin-excel : No genera el .xlsx, solo la salida por consola}
        {--salida= : Ruta del .xlsx de salida}';

    protected $description = 'Audita el tope de edad de las solicitudes de Jovenes Investigadores';

    private const HEADERS = [
        'Joven ID', 'Estado', 'Apellido', 'Nombre', 'Documento', 'CUIL', 'Facultad',
        'Nacimiento', 'Fecha de envio', 'Edad al enviar', 'Edad que cumple en el anio', 'Tope',
        'Institucion beca', 'Beca', 'Flag unlp', 'Diagnostico',
    ];

    private const COL_DIAGNOSTICO = 15;

    private function fecha($valor)
    {
        $valor = substr((string) $valor, 0, 10);

        return ($valor === '' || strpos($valor, '0000-00-00') === 0) ? '' : $valor;
    }

    public function handle(): int
    {
        $anio = trim((string) $this->option('anio'));
        if ($anio === '') {
            $anio = Constants::YEAR_JOVENES;
        }

        if (!ctype_digit((string) $anio)) {
            $this->error('--anio debe ser un año (AAAA).');
            return self::FAILURE;
        }

        $tope = trim((string) $this->option('tope'));
        $tope = ($tope === '') ? intval(Constants::TOPE_EDAD_JOVENES) : intval($tope);

        $estado         = trim((string) $this->option('estado'));
        $solo           = trim((string) $this->option('solo'));
        $incluirCreadas = (bool) $this->option('incluir-creadas');
        $cuil           = preg_replace('/\D/', '', (string) $this->option('cuil'));

        // Fecha real de envio: el primer pase a Recibida que quedo en joven_estados.
        $envios = DB::table('joven_estados')
            ->select('joven_id', DB::raw('MIN(desde) as enviado'))
            ->where('estado', 'Recibida')
            ->groupBy('joven_id');

        $query = DB::table('jovens as j')
            ->leftJoin('periodos as pe', 'pe.id', '=', 'j.periodo_id')
            ->leftJoin('investigadors as i', 'i.id', '=', 'j.investigador_id')
            ->leftJoin('personas as p', 'p.id', '=', 'i.persona_id')
            ->leftJoin('facultads as f', 'f.id', '=', 'j.facultadplanilla_id')
            ->leftJoin('joven_becas as jb', function ($join) {
                $join->on('jb.joven_id', '=', 'j.id')->where('jb.actual', '=', 1);
            })
            ->leftJoinSub($envios, 'env', function ($join) {
                $join->on('env.joven_id', '=', 'j.id');
            })
            ->select(
                'j.id', 'j.estado', 'j.nacimiento', 'env.enviado',
                'p.apellido', 'p.nombre', 'p.documento', 'p.cuil',
                'f.nombre as facultad',
                'jb.institucion', 'jb.beca', 'jb.unlp'
            )
            ->where('pe.nombre', $anio);

        if ($cuil !== '') {
            $query->whereRaw("REPLACE(REPLACE(p.cuil, '-', ''), ' ', '') = ?", [$cuil]);
        }

        if ($estado !== '') {
            $query->where('j.estado', $estado);
        } elseif (!$incluirCreadas && $cuil === '') {
            $query->where('j.estado', '<>', 'Creada');
        }

        $filas = $query->orderBy('p.apellido')->orderBy('p.nombre')->get();

        $this->info('Periodo '.$anio.' — solicitudes: '.$filas->count());
        $this->line('Tope por año calendario: no pueden presentarse quienes cumplan '.$tope.' años o más durante '.$anio);
        if ($cuil !== '') {
            $this->line('CUIL '.$cuil.', todos los estados');
        } elseif ($estado !== '') {
            $this->line('Estado: '.$estado);
        } else {
            $this->line($incluirCreadas ? 'Todos los estados' : 'Solo solicitudes ya enviadas (estado <> Creada)');
        }

        if ($filas->isEmpty()) {
            $this->warn('No hay solicitudes para esos filtros.');
            return self::FAILURE;
        }

        $informe = [];
        foreach ($filas as $fila) {
            $nacimiento = $this->fecha($fila->nacimiento);
            $enviado    = $this->fecha($fila->enviado);

            $esBecarioUNLP = (bool) $fila->unlp;
            $institucion   = trim((string) $fila->institucion);

            $edadAnio  = '';
            $edadEnvio = '';

            if ($nacimiento !== '') {
                $fechaNacimiento = Carbon::parse($nacimiento);
                // Edad que cumple durante el año de la convocatoria: año calendario, no
                // una fecha límite. Es la regla que aplica el controlador.
                $edadAnio = intval($anio) - intval($fechaNacimiento->format('Y'));
                if ($enviado !== '') {
                    $edadEnvio = $fechaNacimiento->diffInYears(Carbon::parse($enviado));
                }
            }

            if ($nacimiento === '') {
                $diagnostico = 'SIN NACIMIENTO';
            } elseif ($edadAnio < $tope) {
                $diagnostico = 'OK';
            } elseif ($esBecarioUNLP) {
                $diagnostico = (strcasecmp($institucion, 'UNLP') === 0)
                    ? 'EXENTO BECA UNLP'
                    : 'EXENTO FLAG DUDOSO';
            } elseif ($fila->estado === 'Creada') {
                $diagnostico = 'INSUFICIENTE (EDAD)';
            } else {
                $diagnostico = 'DEJADA PASAR (EDAD)';
            }

            if ($solo !== '' && stripos($diagnostico, $solo) === false) {
                continue;
            }

            $informe[] = [
                $fila->id,
                $fila->estado,
                $fila->apellido,
                $fila->nombre,
                (string) $fila->documento,
                (string) $fila->cuil,
                $fila->facultad,
                $nacimiento,
                $enviado,
                $edadEnvio,
                $edadAnio,
                $tope,
                $institucion,
                trim((string) $fila->beca),
                $esBecarioUNLP ? 'SI' : 'NO',
                $diagnostico,
            ];
        }

        if (empty($informe)) {
            $this->warn('Nada que informar con esos filtros.');
            return self::SUCCESS;
        }

        // Resumen por diagnostico
        $conteo = [];
        foreach ($informe as $fila) {
            $diagnostico = $fila[self::COL_DIAGNOSTICO];
            if (!array_key_exists($diagnostico, $conteo)) {
                $conteo[$diagnostico] = 0;
            }
            $conteo[$diagnostico]++;
        }
        arsort($conteo);
        $tabla = [];
        foreach ($conteo as $diagnostico => $cantidad) {
            $tabla[] = [$diagnostico, $cantidad];
        }
        $this->newLine();
        $this->table(['Diagnostico', 'Solicitudes'], $tabla);

        // Detalle de todo lo que no es OK
        $revisar = [];
        foreach ($informe as $fila) {
            if ($fila[self::COL_DIAGNOSTICO] === 'OK') {
                continue;
            }
            $revisar[] = [
                $fila[0],
                $fila[1],
                $fila[2].', '.$fila[3],
                $fila[5],
                $fila[9],
                $fila[10],
                $fila[12].($fila[14] === 'SI' ? ' (unlp=1)' : ''),
                $fila[self::COL_DIAGNOSTICO],
            ];
        }
        if (!empty($revisar)) {
            $this->newLine();
            $this->line('Solicitudes a revisar (primeras 40 de '.count($revisar).'):');
            $this->table(
                ['Joven', 'Estado', 'Apellido, Nombre', 'CUIL', 'Edad al enviar', 'Edad en el anio', 'Beca', 'Diagnostico'],
                array_slice($revisar, 0, 40)
            );
        }

        if (!$this->option('sin-excel')) {
            $salida = $this->option('salida')
                ?: storage_path('app/auditoria_edad_jovenes_'.$anio.'_'.date('Ymd_His').'.xlsx');
            $this->escribirXlsx($informe, $salida, $anio);
        }

        return self::SUCCESS;
    }

    private function escribirXlsx(array $informe, string $path, string $anio): void
    {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Edad '.$anio);

        foreach (self::HEADERS as $i => $header) {
            $sheet->setCellValueByColumnAndRow($i + 1, 1, $header);
        }

        $r = 2;
        foreach ($informe as $fila) {
            foreach ($fila as $c => $valor) {
                // Documento y CUIL como texto, para que Excel no los rompa
                if ($c === 4 || $c === 5) {
                    $sheet->setCellValueExplicitByColumnAndRow($c + 1, $r, (string) $valor, DataType::TYPE_STRING);
                } else {
                    $sheet->setCellValueByColumnAndRow($c + 1, $r, $valor);
                }
            }
            $r++;
        }

        $lastCol = Coordinate::stringFromColumnIndex(count(self::HEADERS));
        $sheet->getStyle('A1:'.$lastCol.'1')->getFont()->setBold(true);
        $sheet->getStyle('A1:'.$lastCol.'1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:'.$lastCol.'1');
        for ($c = 1; $c <= count(self::HEADERS); $c++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($c))->setAutoSize(true);
        }

        (new Xlsx($spreadsheet))->save($path);
        $this->newLine();
        $this->info('Informe: '.$path.' ('.count($informe).' filas)');

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
    }
}
