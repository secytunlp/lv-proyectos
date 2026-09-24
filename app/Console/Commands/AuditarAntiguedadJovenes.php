<?php

namespace App\Console\Commands;

use App\Constants;
use App\Models\Joven;
use App\Traits\CalculaAntiguedadJovenes;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

/**
 * Recalcula la antigüedad en investigación de las solicitudes de Jóvenes
 * Investigadores con el criterio corregido (App\Traits\CalculaAntiguedadJovenes) y
 * la compara contra el cálculo que estaba vigente cuando se enviaron.
 *
 * Lo que se mide es el TRAMO CONTINUO VIGENTE: el que sigue abierto en la fecha de corte.
 * Las columnas "Llega con un tramo no vigente" y "Llega sumando tramos" muestran qué
 * pasaría con criterios más laxos: si alguna dice SI y el diagnóstico no es OK, a esa
 * persona la deja afuera la discontinuidad, no la falta de tiempo acumulado.
 *
 *   DEJADA PASAR   la solicitud ya se envió y no llega al mínimo. Son las que
 *                  entraron por el bug: el cálculo viejo sumaba período futuro,
 *                  períodos simultáneos y participaciones cortadas.
 *   INSUFICIENTE   todavía está en Creada y no llega al mínimo: con el arreglo
 *                  puesto, el sistema no la va a dejar enviar.
 *   SIN DATOS      no hay ninguna beca UNLP ni proyecto con fechas utilizables.
 *   OK             cumple el mínimo con el criterio corregido.
 *
 * Solo lee. No modifica nada ni cambia estados.
 */
class AuditarAntiguedadJovenes extends Command
{
    use CalculaAntiguedadJovenes;

    protected $signature = 'jovens:auditar-antiguedad
        {--anio= : Periodo de las solicitudes. Por defecto Constants::YEAR_JOVENES}
        {--corte= : Fecha hasta la que se computa la antiguedad (Y-m-d). Por defecto Constants::CIERRE_JOVENES}
        {--estado= : Filtra por un estado puntual de la solicitud}
        {--cuil= : Audita una sola persona y muestra el detalle de su calculo (los guiones se ignoran)}
        {--incluir-creadas : Incluye las solicitudes que todavia no se enviaron}
        {--solo= : Muestra solo las filas cuyo diagnostico contenga este texto}
        {--sin-excel : No genera el .xlsx, solo la salida por consola}
        {--salida= : Ruta del .xlsx de salida}';

    protected $description = 'Audita la antiguedad en investigacion de las solicitudes de Jovenes Investigadores';

    private const HEADERS = [
        'Joven ID', 'Estado', 'Apellido', 'Nombre', 'Documento', 'CUIL', 'Facultad',
        'Egreso grado', 'Dias vigentes', 'Anios vigentes', 'Dias minimos',
        'Dias tramo mas largo', 'Llega con un tramo no vigente',
        'Dias sumando tramos', 'Llega sumando tramos',
        'Dias calculo anterior', 'Pasaba el control anterior',
        'Tramo vigente', 'Tramo continuo mas largo', 'Intervalos computados', 'Diagnostico',
    ];

    private const COL_DIAS_VIGENTES  = 8;
    private const COL_TRAMO_MAS_LARGO = 11;
    private const COL_DIAGNOSTICO    = 20;

    public function handle(): int
    {
        $anio = $this->option('anio');
        if ($anio === null || $anio === '') {
            $anio = Constants::YEAR_JOVENES;
        }

        $corteTxt = trim((string) $this->option('corte'));
        if ($corteTxt === '') {
            $corteTxt = substr((string) Constants::CIERRE_JOVENES, 0, 10);
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $corteTxt)) {
            $this->error('--corte debe tener formato Y-m-d.');
            return self::FAILURE;
        }
        $corte = Carbon::parse($corteTxt)->startOfDay();

        $estado         = trim((string) $this->option('estado'));
        $solo           = trim((string) $this->option('solo'));
        $incluirCreadas = (bool) $this->option('incluir-creadas');
        $cuil           = preg_replace('/\D/', '', (string) $this->option('cuil'));

        $diasMinimos = $this->diasMinimosAntiguedadJoven();

        $query = Joven::with(['becas', 'proyectos', 'investigador.persona'])
            ->whereHas('periodo', function ($q) use ($anio) {
                $q->where('nombre', $anio);
            });

        if ($cuil !== '') {
            // Un CUIL puntual se audita entero: no tiene sentido esconderle la solicitud
            // por el estado, que es justo lo que se está por mirar.
            $query->whereHas('investigador.persona', function ($q) use ($cuil) {
                $q->whereRaw("REPLACE(REPLACE(cuil, '-', ''), ' ', '') = ?", [$cuil]);
            });
        }

        if ($estado !== '') {
            $query->where('estado', $estado);
        } elseif (!$incluirCreadas && $cuil === '') {
            $query->where('estado', '<>', 'Creada');
        }

        $solicitudes = $query->get();

        $this->info('Periodo '.$anio.' — solicitudes: '.$solicitudes->count());
        $this->line('Antigüedad computada al '.$corte->format('d/m/Y').', mínimo '.$diasMinimos.' días continuos y vigentes');
        if ($cuil !== '') {
            $this->line('CUIL '.$cuil.', todos los estados');
        } elseif ($estado !== '') {
            $this->line('Estado: '.$estado);
        } else {
            $this->line($incluirCreadas ? 'Todos los estados' : 'Solo solicitudes ya enviadas (estado <> Creada)');
        }

        if ($solicitudes->isEmpty()) {
            $this->warn('No hay solicitudes para esos filtros.');
            return self::FAILURE;
        }

        $facultades = DB::table('facultads')->pluck('nombre', 'id');

        $informe = [];
        foreach ($solicitudes as $solicitud) {
            $intervalos   = $this->intervalosAntiguedadJoven($solicitud, $corte);
            $tramos       = $this->tramosAntiguedadJoven($solicitud, $corte);
            $tramoVigente = $this->tramoVigenteAntiguedadJoven($solicitud, $corte);
            $dias         = $this->diasAntiguedadJoven($solicitud, $corte);
            $diasLargo    = $this->diasAntiguedadJovenTramoMasLargo($solicitud, $corte);
            $diasSuma     = $this->diasAntiguedadJovenSumaTramos($solicitud, $corte);
            $diasAntes    = $this->diasAntiguedadJovenCalculoAnterior($solicitud);

            if (empty($intervalos)) {
                $diagnostico = 'SIN DATOS';
            } elseif ($dias >= $diasMinimos) {
                $diagnostico = 'OK';
            } elseif ($solicitud->estado === 'Creada') {
                $diagnostico = 'INSUFICIENTE';
            } else {
                $diagnostico = 'DEJADA PASAR';
            }

            if ($solo !== '' && stripos($diagnostico, $solo) === false) {
                continue;
            }

            $persona = null;
            if ($solicitud->investigador !== null) {
                $persona = $solicitud->investigador->persona;
            }

            $facultadId = $solicitud->facultadplanilla_id;
            $facultad   = ($facultadId !== null && isset($facultades[$facultadId])) ? $facultades[$facultadId] : '';

            $egreso = substr((string) $solicitud->egresogrado, 0, 10);
            if (strpos($egreso, '0000-00-00') === 0) {
                $egreso = '';
            }

            $informe[] = [
                $solicitud->id,
                $solicitud->estado,
                $persona ? $persona->apellido : '',
                $persona ? $persona->nombre : '',
                $persona ? (string) $persona->documento : '',
                $persona ? (string) $persona->cuil : '',
                $facultad,
                $egreso,
                $dias,
                round($dias / intval(Constants::DIAS_YEAR), 2),
                $diasMinimos,
                $diasLargo,
                ($diasLargo >= $diasMinimos && $dias < $diasMinimos) ? 'SI' : 'NO',
                $diasSuma,
                ($diasSuma >= $diasMinimos) ? 'SI' : 'NO',
                $diasAntes,
                ($diasAntes >= $diasMinimos) ? 'SI' : 'NO',
                $this->describirTramoJoven($tramoVigente),
                $this->describirTramoMasLargoJoven($tramos),
                $this->describirIntervalosAntiguedadJoven($intervalos),
                $diagnostico,
            ];
        }

        if (empty($informe)) {
            $this->warn('Nada que informar con esos filtros.');
            return self::SUCCESS;
        }

        // Detalle completo cuando se audita una sola persona
        if ($cuil !== '') {
            foreach ($informe as $fila) {
                $this->newLine();
                $this->info('Joven '.$fila[0].' — '.$fila[2].', '.$fila[3].' ('.$fila[5].') — estado '.$fila[1]);
                $this->line('  Diagnostico       : '.$fila[self::COL_DIAGNOSTICO]);
                $this->line('  Dias vigentes     : '.$fila[8].' (minimo '.$fila[10].')');
                $this->line('  Tramo vigente     : '.($fila[17] !== '' ? $fila[17] : '(ninguno abierto al corte)'));
                $this->line('  Tramo mas largo   : '.($fila[18] !== '' ? $fila[18] : '(ninguno)').' — llegaria: '.$fila[12]);
                $this->line('  Sumando tramos    : '.$fila[13].' — llegaria: '.$fila[14]);
                $this->line('  Calculo anterior  : '.$fila[15].' — pasaba: '.$fila[16]);
                $this->line('  Intervalos        : '.($fila[19] !== '' ? $fila[19] : '(ninguno computable)'));
            }
            $this->newLine();
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

        // Detalle de las que no cumplen
        $incumplen = [];
        foreach ($informe as $fila) {
            $diagnostico = $fila[self::COL_DIAGNOSTICO];
            if ($diagnostico === 'DEJADA PASAR' || $diagnostico === 'INSUFICIENTE' || $diagnostico === 'SIN DATOS') {
                $incumplen[] = [
                    $fila[0],
                    $fila[1],
                    $fila[2].', '.$fila[3],
                    $fila[5],
                    $fila[self::COL_DIAS_VIGENTES],
                    $fila[self::COL_TRAMO_MAS_LARGO],
                    $diagnostico,
                ];
            }
        }
        if (!empty($incumplen)) {
            $this->newLine();
            $this->line('Solicitudes que no llegan al mínimo (primeras 30 de '.count($incumplen).'):');
            $this->table(
                ['Joven', 'Estado', 'Apellido, Nombre', 'CUIL', 'Dias vigentes', 'Tramo mas largo', 'Diagnostico'],
                array_slice($incumplen, 0, 30)
            );
        }

        if (!$this->option('sin-excel')) {
            $salida = $this->option('salida')
                ?: storage_path('app/auditoria_antiguedad_jovenes_'.$anio.'_'.date('Ymd_His').'.xlsx');
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
        $sheet->setTitle('Antiguedad '.$anio);

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
