<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

/**
 * Reemplaza produccion.cargos_alfabetico con el alfabetico que manda Personal.
 *
 * Lee el .xls / .xlsx / .csv tal cual llega y hace en el momento las tres
 * traducciones de codigos que antes se hacian a mano con 26 UPDATE despues de
 * importar:
 *
 *   cd_facultad  <- del NOMBRE de la dependencia, no del codigo. En el archivo
 *                   `Cod. Dependencia` = 10 es a la vez Ciencias Naturales,
 *                   Ciencia y Tecnica y Escuela de Oficios; por nombre cada una
 *                   va a donde corresponde.
 *   cd_cargo     <- prefijo de `Clase Grupo` (05, 06, 07, 08, 09, 10)
 *   cd_deddoc    <- ultima letra de `Clase Grupo` (E, S, X)
 *
 * Las dependencias que no estan en el mapa conservan el NOMBRE en cd_facultad,
 * igual que quedaban antes: no estan en la lista de 17 que filtra
 * cargos:actualizar, asi que quedan afuera igual.
 *
 * dt_fecha = Fecha Situacion (desde cuando tiene esa situacion), no Fecha
 * Ingreso UNLP. Es la que termina siendo el `ingreso` del cargo en el pivot.
 *
 * Vacia y recarga la tabla entera dentro de una transaccion, con backup previo.
 * Despues hay que correr cargos:actualizar, que es el que baja esto a
 * investigador_cargos e investigadors.
 */
class ImportarAlfabetico extends Command
{
    protected $signature = 'cargos:importar-alfabetico
        {archivo : Ruta del .xls, .xlsx o .csv}
        {--hoja= : Nombre de la hoja (por defecto, la primera)}
        {--commit : Escribir. Sin esto solo informa lo que haria}
        {--sin-backup : No crear la tabla de backup (no recomendado)}';

    protected $description = 'Reemplaza cargos_alfabetico con el archivo de Personal, traduciendo los codigos';

    /** Nombre de la dependencia -> cd_facultad que espera cargos:actualizar */
    private $facultades = array(
        'Escuela de RR.HH.'                                  => 177,
        'Hospital de Medicina'                               => 177,
        'Facultad de Ciencias Médicas'                       => 177,
        'Facultad de Trabajo Social'                          => 179,
        'Facultad de Psicología'                              => 1220,
        'Facultad de Periodismo y Comunicación Social'        => 174,
        'Facultad de Odontología'                             => 180,
        'Hospital Odontológico'                               => 180,
        'Facultad de Ingeniería'                              => 169,
        'Facultad de Informática'                             => 187,
        'Facultad de Humanidades y Ciencias de la Educación'  => 175,
        'Facultad de Ciencias Veterinarias'                   => 167,
        'Facultad de Ciencias Naturales'                      => 181,
        'Facultad de Ciencias Jurídicas y Sociales'           => 173,
        'Facultad de Ciencias Exactas'                        => 170,
        'Facultad de Ciencias Económicas'                     => 172,
        'Facultad de Ciencias Astronómicas y Geofísicas'      => 171,
        'Facultad de Ciencias Agrarias y Forestales'          => 165,
        'Facultad de Artes'                                   => 176,
        'Facultad de Arquitectura y Urbanismo'                => 168,
    );

    /** Prefijo de `Clase Grupo` -> cd_cargo */
    private $cargos = array('05' => 1, '07' => 2, '06' => 3, '08' => 4, '09' => 5, '10' => 14);

    /** Ultima letra de `Clase Grupo` -> cd_deddoc (1 Exclusiva, 2 Semi, 3 Simple) */
    private $dedic = array('E' => 1, 'S' => 2, 'X' => 3);

    /** Columnas del archivo que se usan, por su encabezado (sin distinguir espacios) */
    private $columnas = array(
        'documento'            => 'dni',
        'apellidos y nombres'  => 'investigador',
        'dependencia'          => 'ds_facultad',
        'escalafon'            => 'escalafon',
        'clase grupo'          => 'clase',
        'función'              => 'funcion',
        'situación'            => 'situacion',
        'fecha situación'      => 'dt_fecha',
        'fecha de nacimiento'  => 'nacimiento',
        'cargo'                => 'ds_cargo',
    );

    private function norm($v)
    {
        $v = str_replace("\xc2\xa0", ' ', (string) $v);       // nbsp
        return mb_strtolower(trim(preg_replace('/\s+/u', ' ', $v)), 'UTF-8');
    }

    /**
     * Fecha Situacion viene como 20090701; Fecha de Nacimiento como serial de
     * Excel o como fecha ya parseada. Devuelve Y-m-d o null.
     */
    private function fecha($v)
    {
        if ($v === null || $v === '') {
            return null;
        }
        if ($v instanceof \DateTimeInterface) {
            return $v->format('Y-m-d');
        }

        $s = trim((string) $v);
        $digitos = preg_replace('/\D/', '', $s);

        if (strlen($digitos) === 8) {                          // 20090701
            $y = (int) substr($digitos, 0, 4);
            $m = (int) substr($digitos, 4, 2);
            $d = (int) substr($digitos, 6, 2);
            return checkdate($m, $d, $y) ? sprintf('%04d-%02d-%02d', $y, $m, $d) : null;
        }

        if (is_numeric($s) && $s > 0 && $s < 100000) {         // serial de Excel
            try {
                return ExcelDate::excelToDateTimeObject((float) $s)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        $t = strtotime($s);
        return $t ? date('Y-m-d', $t) : null;
    }

    public function handle()
    {
        $archivo = $this->argument('archivo');
        $commit  = (bool) $this->option('commit');

        if (!is_readable($archivo)) {
            $this->error('No puedo leer el archivo: '.$archivo);
            return 1;
        }

        $this->info('=== Importacion del alfabetico -> cargos_alfabetico ===');
        $this->line('Archivo: '.$archivo);
        if (!$commit) {
            $this->warn('SIMULACION. Agrega --commit para escribir.');
        }
        $this->line('');
        $this->line('Leyendo...');

        $reader = IOFactory::createReaderForFile($archivo);
        $reader->setReadDataOnly(true);
        $libro = $reader->load($archivo);

        $hoja = $this->option('hoja')
            ? $libro->getSheetByName($this->option('hoja'))
            : $libro->getSheet(0);

        if (!$hoja) {
            $this->error('No encontre la hoja indicada.');
            return 1;
        }

        $filas = $hoja->toArray(null, true, false, false);
        $this->line('Filas leidas del archivo: '.count($filas));

        // El encabezado no siempre esta en la primera fila: se busca la que tenga
        // "Documento" y "Apellidos y Nombres".
        $idxCab = null;
        foreach ($filas as $i => $fila) {
            $vals = array_map(array($this, 'norm'), $fila);
            if (in_array('documento', $vals, true) && in_array('apellidos y nombres', $vals, true)) {
                $idxCab = $i;
                break;
            }
            if ($i > 20) {
                break;
            }
        }

        if ($idxCab === null) {
            $this->error('No encontre la fila de encabezados (con "Documento" y "Apellidos y Nombres").');
            return 1;
        }

        $pos = array();
        foreach ($filas[$idxCab] as $col => $titulo) {
            $t = $this->norm($titulo);
            if (isset($this->columnas[$t])) {
                $pos[$this->columnas[$t]] = $col;
            }
        }

        $faltan = array_diff(array_values($this->columnas), array_keys($pos));
        if (count($faltan) > 0) {
            $this->error('Faltan columnas en el archivo: '.implode(', ', $faltan));
            return 1;
        }
        $this->line('Encabezado en la fila '.($idxCab + 1).'.');
        $this->line('');

        $registros    = array();
        $sinFacultad  = array();
        $sinCargo     = 0;
        $vacias       = 0;

        for ($i = $idxCab + 1; $i < count($filas); $i++) {
            $f = $filas[$i];

            $dni = trim((string) (isset($f[$pos['dni']]) ? $f[$pos['dni']] : ''));
            if ($dni === '') {
                $vacias++;
                continue;
            }

            $dep   = trim(preg_replace('/\s+/u', ' ', (string) $f[$pos['ds_facultad']]));
            $clase = trim((string) $f[$pos['clase']]);

            $cdFacultad = isset($this->facultades[$dep]) ? (string) $this->facultades[$dep] : $dep;
            if (!isset($this->facultades[$dep])) {
                if (!isset($sinFacultad[$dep])) {
                    $sinFacultad[$dep] = 0;
                }
                $sinFacultad[$dep]++;
            }

            $pre = substr($clase, 0, 2);
            $fin = substr($clase, -1);
            $cdCargo  = isset($this->cargos[$pre]) ? $this->cargos[$pre] : null;
            $cdDeddoc = isset($this->dedic[$fin])  ? $this->dedic[$fin]  : null;
            if ($cdCargo === null) {
                $sinCargo++;
            }

            $registros[] = array(
                'dni'          => $dni,
                'investigador' => trim((string) $f[$pos['investigador']]),
                'cd_facultad'  => $cdFacultad,
                'escalafon'    => trim((string) $f[$pos['escalafon']]),
                'clase'        => $clase !== '' ? $clase : null,
                'funcion'      => trim((string) $f[$pos['funcion']]),
                'situacion'    => trim((string) $f[$pos['situacion']]),
                'dt_fecha'     => $this->fecha($f[$pos['dt_fecha']]),
                'nacimiento'   => $this->fecha($f[$pos['nacimiento']]),
                'ds_cargo'     => trim(preg_replace('/\s+/u', ' ', (string) $f[$pos['ds_cargo']])),
                'ds_facultad'  => $dep,
                'cd_cargo'     => $cdCargo === null ? null : (string) $cdCargo,
                'cd_deddoc'    => $cdDeddoc === null ? null : (string) $cdDeddoc,
            );
        }

        $this->info('Registros a cargar: '.count($registros));
        if ($vacias > 0) {
            $this->line('Filas sin documento, salteadas: '.$vacias);
        }
        $this->line('');

        // resumen de lo que va a ver cargos:actualizar
        $validas   = array(165,167,168,169,170,171,172,173,174,175,176,177,179,180,181,187,1220);
        $excluidas = array('Licencia sin goce de sueldos', 'Renuncia', 'Jubilación');
        $pasan = 0;
        foreach ($registros as $r) {
            if ($r['escalafon'] === 'Docente'
                && in_array((int) $r['cd_facultad'], $validas, true)
                && in_array((int) $r['cd_deddoc'], array(1,2,3), true)
                && !in_array($r['situacion'], $excluidas, true)) {
                $pasan++;
            }
        }
        $this->info('De esas, pasarian el filtro de cargos:actualizar: '.$pasan);

        if (count($sinFacultad) > 0) {
            $this->line('');
            $this->warn('Dependencias sin traducir (conservan el nombre en cd_facultad):');
            arsort($sinFacultad);
            $rows = array();
            foreach ($sinFacultad as $d => $n) {
                $rows[] = array($this->corta($d, 50), $n);
            }
            $this->table(array('Dependencia', 'Filas'), array_slice($rows, 0, 20));
            $this->line('Si alguna de estas deberia entrar, agregala al mapa $facultades.');
        }
        if ($sinCargo > 0) {
            $this->line('');
            $this->line('Filas sin cd_cargo (clase que no empieza con 05/06/07/08/09/10): '.$sinCargo);
            $this->line('Son los MONTO FIJO y similares, no son cargos. Entran con cd_cargo NULL.');
        }

        if (!$commit) {
            $this->line('');
            $this->warn('Simulacion: no se escribio nada. Agrega --commit.');
            return 0;
        }

        $this->line('');

        // El backup va FUERA de la transaccion a proposito: en MySQL un CREATE
        // TABLE hace commit implicito, asi que adentro romperia el rollback.
        $bk = null;
        if (!$this->option('sin-backup')) {
            $bk = 'cargos_alfabetico_backup_'.date('YmdHis');
            $this->line('Backup: '.$bk);
            DB::statement("CREATE TABLE `$bk` LIKE `cargos_alfabetico`");
            DB::statement("INSERT INTO `$bk` SELECT * FROM `cargos_alfabetico`");
        }

        DB::beginTransaction();
        try {
            $antes = DB::table('cargos_alfabetico')->count();
            DB::table('cargos_alfabetico')->delete();

            $barra = $this->output->createProgressBar(count($registros));
            foreach (array_chunk($registros, 500) as $lote) {
                DB::table('cargos_alfabetico')->insert($lote);
                $barra->advance(count($lote));
            }
            $barra->finish();

            DB::commit();

            $this->line('');
            $this->line('');
            $this->info('Listo. Antes: '.$antes.' filas, ahora: '.count($registros).'.');
            $this->line('Siguiente paso: php artisan cargos:actualizar');
            $this->line('(subi antes ActualizarCargosDocentes.php con el desempate por cargos.orden)');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->line('');
            $this->error('Fallo, no se modifico nada: '.$e->getMessage());
            if ($bk !== null) {
                $this->line('La tabla original sigue igual. El backup '.$bk.' quedo creado, se puede borrar.');
            }
            return 1;
        }

        return 0;
    }

    private function corta($v, $n)
    {
        $v = (string) $v;
        return mb_strlen($v) > $n ? mb_substr($v, 0, $n - 1).'.' : $v;
    }
}
