<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

/**
 * Carga en investigador_becas las becas otorgadas de un llamado, desde el Excel
 * de SeCyT (una fila por beca).
 *
 * Columnas esperadas (se reconocen sin acentos y en cualquier orden):
 *   TIPO DE BECA, APELLIDO, Nombres, DNI, FACULTAD, INICIO, FIN,
 *   Finaliza Beca, Observacion, Tema beca
 *
 * Mapeo a investigador_becas:
 *   investigador_id <- DNI contra personas.documento -> investigadors
 *   institucion     <- --institucion (UNLP por defecto), unlp = 1
 *   beca            <- TIPO DE BECA: Doctorado/Maestria/Posdoctoral
 *   desde           <- INICIO
 *   hasta           <- Finaliza Beca (fin real) o FIN, segun --fin
 *   resumen         <- NO se toca. Va despues con becas:importar-resumenes,
 *                      que trae el resumen largo de eva. "Tema beca" es el
 *                      titulo del tema, no el resumen, y no tiene columna.
 *
 * No crea personas ni investigadores: si no existen, la fila se reporta.
 * No inserta si ya hay una beca con el mismo investigador + beca + desde.
 *
 * Dry-run por defecto. Para persistir: --commit
 */
class ImportarBecasNuevas extends Command
{
    protected $signature = 'becas:importar-nuevas
        {archivo : Ruta del .xlsx (o .csv) con las becas otorgadas}
        {--institucion=UNLP : Institucion a grabar}
        {--fin=finaliza : Que fecha va en hasta: "finaliza" (fin real) o "fin" (plazo otorgado)}
        {--limite= : Procesar solo las primeras N filas}
        {--salida= : Ruta del informe CSV}
        {--commit : Persistir (por defecto es dry-run)}';

    protected $description = 'Importa a investigador_becas las becas otorgadas de un llamado, desde el Excel de SeCyT';

    /** TIPO DE BECA del Excel -> valor valido de investigador_becas.beca */
    private $tipos = [
        'doctorado'    => 'Beca doctoral',
        'maestria'     => 'Beca maestría',
        'posdoctoral'  => 'Beca posdoctoral',
        'postdoctoral' => 'Beca posdoctoral',
    ];

    private $columnas = [
        'tipo'      => ['tipo_de_beca', 'tipo', 'tipo_beca'],
        'apellido'  => ['apellido', 'apellidos'],
        'nombres'   => ['nombres', 'nombre'],
        'dni'       => ['dni', 'documento', 'numero_documento'],
        'facultad'  => ['facultad'],
        'inicio'    => ['inicio', 'desde', 'fecha_inicio'],
        'fin'       => ['fin', 'hasta', 'fecha_fin'],
        'finaliza'  => ['finaliza_beca', 'finaliza'],
        'obs'       => ['observacion', 'observaciones', 'obs'],
        'tema'      => ['tema_beca', 'tema'],
    ];

    private function soloDigitos($valor)
    {
        return preg_replace('/\D/', '', (string) $valor);
    }

    private function normalizar($texto)
    {
        $texto = (string) $texto;
        $texto = preg_replace('/^\xEF\xBB\xBF/', '', $texto);
        $texto = str_replace("\xC2\xA0", ' ', $texto); // NBSP
        if (!mb_check_encoding($texto, 'UTF-8')) {
            $texto = mb_convert_encoding($texto, 'UTF-8', 'ISO-8859-1');
        }
        $texto = mb_strtolower(trim($texto), 'UTF-8');
        $desde = ['á','é','í','ó','ú','ü','ñ','à','è','ì','ò','ù','â','ê','î','ô','û'];
        $hasta = ['a','e','i','o','u','u','n','a','e','i','o','u','a','e','i','o','u'];
        $texto = str_replace($desde, $hasta, $texto);
        $texto = preg_replace('/[^a-z0-9]+/', '_', $texto);
        return trim($texto, '_');
    }

    /** Devuelve Y-m-d desde una celda que puede ser DateTime, serial de Excel o texto */
    private function aFecha($valor)
    {
        if ($valor === null || $valor === '') {
            return null;
        }
        if ($valor instanceof \DateTimeInterface) {
            return $valor->format('Y-m-d');
        }
        if (is_numeric($valor)) {
            // Serial de Excel
            $dt = ExcelDate::excelToDateTimeObject((float) $valor);
            return $dt->format('Y-m-d');
        }
        $texto = trim((string) $valor);
        if ($texto === '') {
            return null;
        }
        // d/m/Y o d-m-Y
        if (preg_match('#^(\d{1,2})[/-](\d{1,2})[/-](\d{4})#', $texto, $m)) {
            return sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
        }
        $ts = strtotime($texto);
        return $ts === false ? null : date('Y-m-d', $ts);
    }

    /** Lee el archivo y devuelve filas asociativas */
    private function leerArchivo($ruta, &$error)
    {
        $error = null;

        if (!is_file($ruta) || !is_readable($ruta)) {
            $error = 'No se puede leer el archivo: '.$ruta;
            return [];
        }

        try {
            $reader = IOFactory::createReaderForFile($ruta);
            $hoja = $reader->load($ruta)->getActiveSheet();
        } catch (\Exception $e) {
            $error = 'No se pudo abrir el archivo: '.$e->getMessage();
            return [];
        }

        $indices = [];
        $filas   = [];
        $primera = true;

        foreach ($hoja->getRowIterator() as $fila) {
            $celdas = [];
            $iterador = $fila->getCellIterator();
            $iterador->setIterateOnlyExistingCells(false);
            foreach ($iterador as $celda) {
                $valor = $celda->getValue();
                if (ExcelDate::isDateTime($celda) && is_numeric($valor)) {
                    $valor = ExcelDate::excelToDateTimeObject((float) $valor);
                }
                $celdas[] = $valor;
            }

            if ($primera) {
                $primera = false;
                foreach ($celdas as $pos => $titulo) {
                    $norm = $this->normalizar($titulo);
                    foreach ($this->columnas as $clave => $alias) {
                        if (in_array($norm, $alias, true)) {
                            $indices[$clave] = $pos;
                            break;
                        }
                    }
                }
                $faltan = [];
                foreach (['tipo', 'dni', 'inicio'] as $obligatoria) {
                    if (!array_key_exists($obligatoria, $indices)) {
                        $faltan[] = $obligatoria;
                    }
                }
                if (!empty($faltan)) {
                    $error = 'Faltan columnas: '.implode(', ', $faltan);
                    return [];
                }
                continue;
            }

            // Saltear filas totalmente vacias
            $vacia = true;
            foreach ($celdas as $valor) {
                if ($valor !== null && trim((string) $valor) !== '') {
                    $vacia = false;
                    break;
                }
            }
            if ($vacia) {
                continue;
            }

            $registro = [];
            foreach ($this->columnas as $clave => $alias) {
                $pos = array_key_exists($clave, $indices) ? $indices[$clave] : null;
                $registro[$clave] = ($pos !== null && array_key_exists($pos, $celdas)) ? $celdas[$pos] : null;
            }
            $filas[] = $registro;
        }

        return $filas;
    }

    public function handle()
    {
        $commit      = (bool) $this->option('commit');
        $institucion = trim((string) $this->option('institucion'));
        $cualFin     = strtolower(trim((string) $this->option('fin')));
        $limite      = $this->option('limite');
        $ruta        = $this->argument('archivo');

        if ($institucion === '') {
            $this->error('--institucion no puede quedar vacio: es lo que se graba en la fila.');
            return 1;
        }
        if ($cualFin !== 'finaliza' && $cualFin !== 'fin') {
            $this->error('--fin debe ser "finaliza" o "fin".');
            return 1;
        }

        $filas = $this->leerArchivo($ruta, $error);
        if ($error !== null) {
            $this->error($error);
            return 1;
        }
        if (empty($filas)) {
            $this->error('El archivo no tiene filas de datos.');
            return 1;
        }
        if ($limite !== null && $limite !== '') {
            $filas = array_slice($filas, 0, (int) $limite);
        }

        $this->info('Filas leidas: '.count($filas));
        $this->line('Institucion a grabar: '.$institucion.' (unlp = '.(strcasecmp($institucion, 'UNLP') === 0 ? '1' : '0').')');
        $this->line('Campo "hasta": '.($cualFin === 'finaliza' ? 'Finaliza Beca (fin real)' : 'FIN (plazo otorgado)'));

        // ---- Resolucion en bloque

        $documentos = [];
        foreach ($filas as $fila) {
            $doc = ltrim($this->soloDigitos($fila['dni']), '0');
            if ($doc !== '') {
                $documentos[$doc] = true;
            }
        }
        $documentos = array_keys($documentos);

        $personasPorDoc = [];
        if (!empty($documentos)) {
            $personas = DB::table('personas')->select('id', 'documento')
                ->whereIn('documento', $documentos)->get();
            foreach ($personas as $persona) {
                $clave = ltrim($this->soloDigitos($persona->documento), '0');
                if (!array_key_exists($clave, $personasPorDoc)) {
                    $personasPorDoc[$clave] = [];
                }
                $personasPorDoc[$clave][] = (int) $persona->id;
            }
        }

        $personaIds = [];
        foreach ($personasPorDoc as $ids) {
            foreach ($ids as $id) {
                $personaIds[] = $id;
            }
        }

        $investigadoresPorPersona = [];
        $investigadorIds = [];
        if (!empty($personaIds)) {
            $investigadores = DB::table('investigadors')->select('id', 'persona_id')
                ->whereIn('persona_id', $personaIds)->get();
            foreach ($investigadores as $inv) {
                $pid = (int) $inv->persona_id;
                if (!array_key_exists($pid, $investigadoresPorPersona)) {
                    $investigadoresPorPersona[$pid] = [];
                }
                $investigadoresPorPersona[$pid][] = (int) $inv->id;
                $investigadorIds[] = (int) $inv->id;
            }
        }

        $becasPorInvestigador = [];
        if (!empty($investigadorIds)) {
            $becas = DB::table('investigador_becas')
                ->select('id', 'investigador_id', 'institucion', 'beca', 'desde', 'hasta')
                ->whereIn('investigador_id', $investigadorIds)->get();
            foreach ($becas as $beca) {
                $iid = (int) $beca->investigador_id;
                if (!array_key_exists($iid, $becasPorInvestigador)) {
                    $becasPorInvestigador[$iid] = [];
                }
                $becasPorInvestigador[$iid][] = $beca;
            }
        }

        // ---- Clasificacion

        $informe   = [];
        $aInsertar = [];

        foreach ($filas as $fila) {
            $registro = [
                'apellido'    => trim((string) $fila['apellido']),
                'nombres'     => trim((string) $fila['nombres']),
                'dni'         => '',
                'facultad'    => trim((string) $fila['facultad']),
                'tipo_excel'  => trim((string) $fila['tipo']),
                'beca'        => '',
                'desde'       => '',
                'hasta'       => '',
                'observacion' => trim((string) $fila['obs']),
                'investigador_id' => '',
                'accion'      => '',
                'detalle'     => '',
            ];

            $doc = ltrim($this->soloDigitos($fila['dni']), '0');
            $registro['dni'] = $doc;
            if ($doc === '') {
                $registro['accion'] = 'SIN DNI';
                $informe[] = $registro;
                continue;
            }

            $tipoNorm = $this->normalizar($fila['tipo']);
            if (!array_key_exists($tipoNorm, $this->tipos)) {
                $registro['accion']  = 'TIPO DESCONOCIDO';
                $registro['detalle'] = 'no se sabe a que valor de beca mapear';
                $informe[] = $registro;
                continue;
            }
            $beca = $this->tipos[$tipoNorm];
            $registro['beca'] = $beca;

            $desde = $this->aFecha($fila['inicio']);
            $hasta = $cualFin === 'finaliza'
                ? $this->aFecha($fila['finaliza'])
                : $this->aFecha($fila['fin']);
            if ($hasta === null) {
                $hasta = $this->aFecha($fila['fin']);
            }
            $registro['desde'] = (string) $desde;
            $registro['hasta'] = (string) $hasta;

            if ($desde === null || $hasta === null) {
                $registro['accion']  = 'SIN FECHAS';
                $registro['detalle'] = 'inicio o fin no interpretables';
                $informe[] = $registro;
                continue;
            }
            if ($hasta < $desde) {
                $registro['accion']  = 'FECHAS INVALIDAS';
                $registro['detalle'] = 'hasta < desde';
                $informe[] = $registro;
                continue;
            }

            if (!array_key_exists($doc, $personasPorDoc)) {
                $registro['accion']  = 'SIN PERSONA';
                $registro['detalle'] = 'hay que dar de alta la persona primero';
                $informe[] = $registro;
                continue;
            }
            if (count($personasPorDoc[$doc]) > 1) {
                $registro['accion']  = 'PERSONA AMBIGUA';
                $registro['detalle'] = 'personas: '.implode(',', $personasPorDoc[$doc]);
                $informe[] = $registro;
                continue;
            }

            $personaId = $personasPorDoc[$doc][0];
            if (!array_key_exists($personaId, $investigadoresPorPersona)) {
                $registro['accion']  = 'SIN INVESTIGADOR';
                $registro['detalle'] = 'persona '.$personaId.' existe pero no es investigador';
                $informe[] = $registro;
                continue;
            }
            if (count($investigadoresPorPersona[$personaId]) > 1) {
                $registro['accion']  = 'INVESTIGADOR AMBIGUO';
                $registro['detalle'] = 'investigadores: '.implode(',', $investigadoresPorPersona[$personaId]);
                $informe[] = $registro;
                continue;
            }

            $invId = $investigadoresPorPersona[$personaId][0];
            $registro['investigador_id'] = $invId;

            // Ya existe? mismo investigador + beca + desde
            $existente = null;
            if (array_key_exists($invId, $becasPorInvestigador)) {
                foreach ($becasPorInvestigador[$invId] as $fila2) {
                    if (strcasecmp(trim((string) $fila2->beca), $beca) === 0
                        && substr((string) $fila2->desde, 0, 10) === $desde) {
                        $existente = $fila2;
                        break;
                    }
                }
            }
            if ($existente !== null) {
                $hastaExistente = substr((string) $existente->hasta, 0, 10);
                $registro['accion']  = 'YA EXISTE';
                $registro['detalle'] = 'beca '.$existente->id
                    .($hastaExistente !== $hasta ? ' (hasta difiere: '.$hastaExistente.')' : '');
                $informe[] = $registro;
                continue;
            }

            $registro['accion'] = 'INSERTAR';
            if ($registro['observacion'] !== '') {
                $registro['detalle'] = 'obs: '.$registro['observacion'];
            }
            $informe[] = $registro;

            $aInsertar[] = [
                'investigador_id' => $invId,
                'institucion'     => $institucion,
                'beca'            => $beca,
                'unlp'            => strcasecmp($institucion, 'UNLP') === 0 ? 1 : 0,
                'desde'           => $desde,
                'hasta'           => $hasta,
            ];
        }

        // ---- Salida

        $conteo = [];
        foreach ($informe as $registro) {
            $accion = $registro['accion'];
            if (!array_key_exists($accion, $conteo)) {
                $conteo[$accion] = 0;
            }
            $conteo[$accion]++;
        }
        arsort($conteo);
        $tabla = [];
        foreach ($conteo as $accion => $cantidad) {
            $tabla[] = [$accion, $cantidad];
        }
        $this->newLine();
        $this->table(['Accion', 'Filas'], $tabla);

        $problemas = [];
        foreach ($informe as $registro) {
            if ($registro['accion'] !== 'INSERTAR') {
                $problemas[] = [
                    $registro['apellido'].', '.$registro['nombres'],
                    $registro['dni'],
                    $registro['beca'],
                    $registro['accion'],
                    mb_substr($registro['detalle'], 0, 55, 'UTF-8'),
                ];
            }
        }
        if (!empty($problemas)) {
            $this->newLine();
            $this->line('Filas que no se insertan ('.count($problemas).'):');
            $this->table(['Apellido, Nombres', 'DNI', 'Beca', 'Accion', 'Detalle'], $problemas);
        }

        $salida = $this->option('salida');
        if ($salida === null || $salida === '') {
            $salida = storage_path('app/becas_nuevas_'.date('Ymd_His').'.csv');
        }
        $fh = fopen($salida, 'w');
        if ($fh !== false) {
            fwrite($fh, "\xEF\xBB\xBF");
            fputcsv($fh, array_keys($informe[0]), ';');
            foreach ($informe as $registro) {
                fputcsv($fh, array_values($registro), ';');
            }
            fclose($fh);
            $this->newLine();
            $this->info('Informe completo: '.$salida);
        }

        if (empty($aInsertar)) {
            $this->newLine();
            $this->info('No hay becas para insertar.');
            return 0;
        }

        if (!$commit) {
            $this->newLine();
            $this->warn('DRY-RUN: se insertarian '.count($aInsertar).' beca(s). Volver a correr con --commit.');
            return 0;
        }

        // ---- Insercion, con un unico created_at para poder revertir el lote

        $sello = date('Y-m-d H:i:s');
        foreach ($aInsertar as $i => $nada) {
            $aInsertar[$i]['created_at'] = $sello;
            $aInsertar[$i]['updated_at'] = $sello;
        }

        DB::transaction(function () use ($aInsertar) {
            foreach (array_chunk($aInsertar, 200) as $lote) {
                DB::table('investigador_becas')->insert($lote);
            }
        });

        $this->newLine();
        $this->info('Becas insertadas: '.count($aInsertar).' con created_at = '.$sello);
        $this->line('Para revertir el lote completo:');
        $this->line("  DELETE FROM investigador_becas WHERE created_at = '".$sello."';");
        $this->newLine();
        $this->line('Siguiente paso: cargar los resumenes con becas:importar-resumenes.');

        return 0;
    }
}
