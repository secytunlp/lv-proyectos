<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Importa los resumenes de tema/periodo de los becarios desde un CSV exportado
 * de la base `eva` (SIGEVA) hacia investigador_becas.resumen.
 *
 * La base `eva` NO se ve desde el servidor, asi que el circuito es:
 *
 *   1. Correr database/sql/eva_resumenes_becas.sql en HeidiSQL (ajustando los
 *      ids de convocatoria de config/eva.php).
 *   2. Exportar el resultado a CSV y subirlo al servidor.
 *   3. php artisan becas:importar-resumenes storage/app/resumenes.csv
 *
 * Columnas esperadas en el CSV (se reconocen con o sin acentos y en cualquier
 * orden): codigo, denominacion, apellido, nombre, cuil, numero_documento,
 * resumen_tema_periodo.
 *
 * Cruce de persona: numero_documento contra personas.documento. Si el CSV trae
 * el documento vacio se usa el centro del CUIL (digitos 3 a 10).
 *
 * Fila destino: la beca de investigador_becas que solapa el anio del periodo.
 * Si hay 0 o mas de 1 candidata la fila se reporta y NO se toca.
 *
 * Dry-run por defecto. Para persistir: --commit (hace tabla de respaldo antes).
 */
class ImportarResumenesBecas extends Command
{
    protected $signature = 'becas:importar-resumenes
        {archivo : Ruta del CSV exportado de eva}
        {--anio= : Anio del periodo de la beca (por defecto se lee de la denominacion)}
        {--institucion=UNLP : Institucion de la beca destino. Vacio para no filtrar}
        {--limite= : Procesar solo las primeras N filas}
        {--salida= : Ruta del informe CSV (por defecto storage/app)}
        {--commit : Persistir los cambios (por defecto es dry-run)}';

    protected $description = 'Actualiza investigador_becas.resumen desde un CSV exportado de la base eva';

    /** Alias aceptados para cada columna, ya normalizados */
    private $columnas = [
        'codigo'       => ['codigo'],
        'denominacion' => ['denominacion'],
        'apellido'     => ['apellido'],
        'nombre'       => ['nombre'],
        'cuil'         => ['cuil'],
        'documento'    => ['numero_documento', 'documento', 'nro_documento', 'dni'],
        'resumen'      => ['resumen_tema_periodo', 'resumen'],
    ];

    private function soloDigitos($valor)
    {
        return preg_replace('/\D/', '', (string) $valor);
    }

    /** Baja a minusculas, saca acentos y deja solo [a-z0-9_] */
    private function normalizarCabecera($texto)
    {
        $texto = (string) $texto;
        $texto = preg_replace('/^\xEF\xBB\xBF/', '', $texto);
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

    private function aUtf8($valor)
    {
        $valor = (string) $valor;
        if ($valor !== '' && !mb_check_encoding($valor, 'UTF-8')) {
            $valor = mb_convert_encoding($valor, 'UTF-8', 'ISO-8859-1');
        }
        return $valor;
    }

    /** Adivina el separador mirando la primera linea */
    private function detectarSeparador($linea)
    {
        $candidatos = [';' => 0, ',' => 0, "\t" => 0, '|' => 0];
        foreach ($candidatos as $sep => $nada) {
            $candidatos[$sep] = substr_count($linea, $sep);
        }
        arsort($candidatos);
        $mejor = key($candidatos);
        return $candidatos[$mejor] > 0 ? $mejor : ',';
    }

    /** Lee el CSV y devuelve filas asociativas con las claves de $this->columnas */
    private function leerCsv($ruta, &$error)
    {
        $error = null;

        if (!is_file($ruta) || !is_readable($ruta)) {
            $error = 'No se puede leer el archivo: '.$ruta;
            return [];
        }

        $handle = fopen($ruta, 'r');
        if ($handle === false) {
            $error = 'No se pudo abrir el archivo: '.$ruta;
            return [];
        }

        $primera = fgets($handle);
        if ($primera === false) {
            fclose($handle);
            $error = 'El archivo esta vacio.';
            return [];
        }
        $separador = $this->detectarSeparador($primera);
        rewind($handle);

        $cabecera = fgetcsv($handle, 0, $separador);
        if ($cabecera === false) {
            fclose($handle);
            $error = 'No se pudo leer la cabecera.';
            return [];
        }

        // Mapea posicion de columna -> clave canonica
        $indices = [];
        foreach ($cabecera as $pos => $titulo) {
            $norm = $this->normalizarCabecera($titulo);
            foreach ($this->columnas as $clave => $alias) {
                if (in_array($norm, $alias, true)) {
                    $indices[$clave] = $pos;
                    break;
                }
            }
        }

        $faltan = [];
        foreach (['documento', 'cuil', 'resumen'] as $obligatoria) {
            if (!array_key_exists($obligatoria, $indices)) {
                $faltan[] = $obligatoria;
            }
        }
        if (!empty($faltan)) {
            fclose($handle);
            $error = 'Faltan columnas en el CSV: '.implode(', ', $faltan)
                .'. Cabecera leida: '.implode(' | ', $cabecera);
            return [];
        }

        $filas = [];
        while (($datos = fgetcsv($handle, 0, $separador)) !== false) {
            // Saltear lineas en blanco
            if (count($datos) === 1 && trim((string) $datos[0]) === '') {
                continue;
            }
            $fila = [];
            foreach ($this->columnas as $clave => $alias) {
                $pos = array_key_exists($clave, $indices) ? $indices[$clave] : null;
                $bruto = ($pos !== null && array_key_exists($pos, $datos)) ? $datos[$pos] : '';
                $fila[$clave] = trim($this->aUtf8($bruto));
            }
            $filas[] = $fila;
        }
        fclose($handle);

        return $filas;
    }

    /** Documento a usar para el cruce: el del CSV, o el centro del CUIL */
    private function documentoDeFila($fila, &$origen)
    {
        $doc = $this->soloDigitos($fila['documento']);
        $doc = ltrim($doc, '0');
        if ($doc !== '' && strlen($doc) >= 6) {
            $origen = 'documento';
            return $doc;
        }

        $cuil = $this->soloDigitos($fila['cuil']);
        if (strlen($cuil) === 11) {
            $origen = 'cuil';
            return ltrim(substr($cuil, 2, 8), '0');
        }

        $origen = null;
        return '';
    }

    /** Anio del periodo: la opcion, o el primer anio que aparezca en la denominacion */
    private function anioDeFila($fila, $anioOpcion)
    {
        if ($anioOpcion !== null && $anioOpcion !== '') {
            return (int) $anioOpcion;
        }
        if (preg_match('/(?:19|20)\d{2}/', $fila['denominacion'], $m)) {
            return (int) $m[0];
        }
        return 0;
    }

    public function handle()
    {
        $commit       = (bool) $this->option('commit');
        $anioOpcion   = $this->option('anio');
        $institucion  = trim((string) $this->option('institucion'));
        $limite       = $this->option('limite');
        $ruta         = $this->argument('archivo');

        $filas = $this->leerCsv($ruta, $error);
        if ($error !== null) {
            $this->error($error);
            return 1;
        }
        if (empty($filas)) {
            $this->error('El CSV no tiene filas de datos.');
            return 1;
        }
        if ($limite !== null && $limite !== '') {
            $filas = array_slice($filas, 0, (int) $limite);
        }

        $this->info('Filas leidas del CSV: '.count($filas));
        if ($institucion !== '') {
            $this->line('Filtrando becas con institucion = '.$institucion);
        }

        // ---- Resolucion en bloque: documentos -> personas -> investigadores -> becas

        $documentos = [];
        foreach ($filas as $fila) {
            $doc = $this->documentoDeFila($fila, $origen);
            if ($doc !== '') {
                $documentos[$doc] = true;
            }
        }
        $documentos = array_keys($documentos);

        $personasPorDoc = [];
        if (!empty($documentos)) {
            $personas = DB::table('personas')
                ->select('id', 'documento')
                ->whereIn('documento', $documentos)
                ->get();
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
            $investigadores = DB::table('investigadors')
                ->select('id', 'persona_id')
                ->whereIn('persona_id', $personaIds)
                ->get();
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
                ->select('id', 'investigador_id', 'institucion', 'beca', 'desde', 'hasta', 'resumen')
                ->whereIn('investigador_id', $investigadorIds)
                ->get();
            foreach ($becas as $beca) {
                $iid = (int) $beca->investigador_id;
                if (!array_key_exists($iid, $becasPorInvestigador)) {
                    $becasPorInvestigador[$iid] = [];
                }
                $becasPorInvestigador[$iid][] = $beca;
            }
        }

        // ---- Clasificacion fila por fila

        $informe   = [];
        $aCambiar  = [];
        $conteo    = [];

        foreach ($filas as $fila) {
            $registro = [
                'codigo'       => $fila['codigo'],
                'denominacion' => $fila['denominacion'],
                'apellido'     => $fila['apellido'],
                'nombre'       => $fila['nombre'],
                'documento'    => '',
                'origen_doc'   => '',
                'anio'         => '',
                'beca_id'      => '',
                'institucion'  => '',
                'beca'         => '',
                'desde'        => '',
                'hasta'        => '',
                'accion'       => '',
                'detalle'      => '',
            ];

            $resumen = trim($fila['resumen']);
            if ($resumen === '') {
                $registro['accion'] = 'SIN RESUMEN';
                $informe[] = $registro;
                continue;
            }

            $doc = $this->documentoDeFila($fila, $origen);
            $registro['documento']  = $doc;
            $registro['origen_doc'] = (string) $origen;
            if ($doc === '') {
                $registro['accion']  = 'SIN DOCUMENTO';
                $registro['detalle'] = 'ni documento ni CUIL utilizables';
                $informe[] = $registro;
                continue;
            }

            $anio = $this->anioDeFila($fila, $anioOpcion);
            $registro['anio'] = $anio !== 0 ? $anio : '';
            if ($anio === 0) {
                $registro['accion']  = 'SIN ANIO';
                $registro['detalle'] = 'la denominacion no tiene anio; usar --anio=';
                $informe[] = $registro;
                continue;
            }

            if (!array_key_exists($doc, $personasPorDoc)) {
                $registro['accion'] = 'SIN PERSONA';
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
                $registro['detalle'] = 'persona '.$personaId;
                $informe[] = $registro;
                continue;
            }

            $candidatas = [];
            $basura     = 0;
            $desdeAnio  = $anio.'-01-01';
            $hastaAnio  = $anio.'-12-31';

            foreach ($investigadoresPorPersona[$personaId] as $invId) {
                if (!array_key_exists($invId, $becasPorInvestigador)) {
                    continue;
                }
                foreach ($becasPorInvestigador[$invId] as $beca) {
                    if ($institucion !== '' && strcasecmp(trim((string) $beca->institucion), $institucion) !== 0) {
                        continue;
                    }
                    if (empty($beca->desde) || empty($beca->hasta)) {
                        continue;
                    }
                    $bDesde = substr((string) $beca->desde, 0, 10);
                    $bHasta = substr((string) $beca->hasta, 0, 10);

                    // Fechas epoch del import viejo: no sirven para ubicar el periodo
                    if ($bDesde <= '1970-01-02') {
                        $basura++;
                        continue;
                    }
                    if ($bDesde <= $hastaAnio && $bHasta >= $desdeAnio) {
                        $candidatas[] = $beca;
                    }
                }
            }

            if (empty($candidatas)) {
                $registro['accion']  = 'SIN BECA DEL PERIODO';
                $registro['detalle'] = $basura > 0
                    ? $basura.' fila(s) descartadas por fecha epoch'
                    : 'ninguna beca solapa '.$anio;
                $informe[] = $registro;
                continue;
            }
            if (count($candidatas) > 1) {
                $ids = [];
                foreach ($candidatas as $beca) {
                    $ids[] = $beca->id;
                }
                $registro['accion']  = 'BECA AMBIGUA';
                $registro['detalle'] = 'becas: '.implode(',', $ids);
                $informe[] = $registro;
                continue;
            }

            $beca = $candidatas[0];
            $registro['beca_id']     = $beca->id;
            $registro['institucion'] = $beca->institucion;
            $registro['beca']        = $beca->beca;
            $registro['desde']       = substr((string) $beca->desde, 0, 10);
            $registro['hasta']       = substr((string) $beca->hasta, 0, 10);

            if (trim((string) $beca->resumen) === $resumen) {
                $registro['accion'] = 'SIN CAMBIOS';
                $informe[] = $registro;
                continue;
            }

            $registro['accion']  = 'ACTUALIZAR';
            $registro['detalle'] = trim((string) $beca->resumen) === ''
                ? 'resumen vacio'
                : 'pisa resumen existente';
            $informe[] = $registro;

            $aCambiar[] = ['id' => (int) $beca->id, 'resumen' => $resumen];
        }

        foreach ($informe as $registro) {
            $accion = $registro['accion'];
            if (!array_key_exists($accion, $conteo)) {
                $conteo[$accion] = 0;
            }
            $conteo[$accion]++;
        }

        // ---- Salida

        $resumenTabla = [];
        arsort($conteo);
        foreach ($conteo as $accion => $cantidad) {
            $resumenTabla[] = [$accion, $cantidad];
        }
        $this->newLine();
        $this->table(['Accion', 'Filas'], $resumenTabla);

        $problemas = [];
        foreach ($informe as $registro) {
            if ($registro['accion'] !== 'ACTUALIZAR' && $registro['accion'] !== 'SIN CAMBIOS') {
                $problemas[] = [
                    $registro['codigo'],
                    $registro['apellido'].', '.$registro['nombre'],
                    $registro['documento'],
                    $registro['accion'],
                    mb_substr($registro['detalle'], 0, 60, 'UTF-8'),
                ];
            }
        }
        if (!empty($problemas)) {
            $this->newLine();
            $this->line('Filas que no se van a tocar (primeras 20 de '.count($problemas).'):');
            $this->table(
                ['Codigo', 'Apellido, Nombre', 'Doc', 'Accion', 'Detalle'],
                array_slice($problemas, 0, 20)
            );
        }

        $salida = $this->option('salida');
        if ($salida === null || $salida === '') {
            $salida = storage_path('app/becas_resumenes_'.date('Ymd_His').'.csv');
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
        } else {
            $this->warn('No se pudo escribir el informe en '.$salida);
        }

        if (empty($aCambiar)) {
            $this->newLine();
            $this->info('No hay resumenes para actualizar.');
            return 0;
        }

        if (!$commit) {
            $this->newLine();
            $this->warn('DRY-RUN: '.count($aCambiar).' fila(s) quedarian actualizadas. Volver a correr con --commit para persistir.');
            return 0;
        }

        // ---- Persistencia, con respaldo previo

        $ids = [];
        foreach ($aCambiar as $cambio) {
            $ids[] = $cambio['id'];
        }

        $respaldo = 'tmp_becas_resumen_'.date('Ymd_His');
        DB::statement(
            'CREATE TABLE `'.$respaldo.'` AS '.
            'SELECT id, resumen, updated_at FROM investigador_becas WHERE id IN ('.implode(',', $ids).')'
        );
        $this->info('Respaldo en `'.$respaldo.'` ('.count($ids).' filas).');

        $actualizadas = 0;
        DB::transaction(function () use ($aCambiar, &$actualizadas) {
            foreach ($aCambiar as $cambio) {
                $actualizadas += DB::table('investigador_becas')
                    ->where('id', $cambio['id'])
                    ->update([
                        'resumen'    => $cambio['resumen'],
                        'updated_at' => now(),
                    ]);
            }
        });

        $this->newLine();
        $this->info('Filas actualizadas: '.$actualizadas);
        $this->line('Para revertir:');
        $this->line('  UPDATE investigador_becas b');
        $this->line('  JOIN `'.$respaldo.'` t ON t.id = b.id');
        $this->line('  SET b.resumen = t.resumen, b.updated_at = t.updated_at;');

        return 0;
    }
}
