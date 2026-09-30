<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Importa el export de informes de SIGEVA a subsidio_informes_{anio}, que
 * consume el cálculo (CalcularSubsidios::poblarIntproy usa evaluacion/rol).
 *
 * Reemplaza el paso manual de "cambiar el código SIGEVA por el propio": el
 * código de proyecto de SIGEVA se resuelve a proyecto_id con un JOIN contra
 * proyectos.sigeva (igual que el INNER JOIN proyecto del script viejo).
 *
 * La consulta de SIGEVA se corre a mano en la base 'eva' y se exporta a CSV.
 * Columnas esperadas (encabezados, sin acentos):
 *   CODIGO PROYECTO, DIRECTOR, INICIO, FIN, APELLIDO INTEGRANTE,
 *   NOMBRE INTEGRANTE, CUIL INTEGRANTE, DNI INTEGRANTE, ROL INTEGRANTE,
 *   EVALUACION, fecha_alta, fecha_baja, fecha_fin_vigencia
 *
 * Uso:
 *   php artisan subsidios:informes --anio=2026 --archivo=storage/app/informes_sigeva.csv
 *   php artisan subsidios:informes --anio=2026 --archivo=... --sep=; --reemplazar
 */
class ImportarInformesSubsidios extends Command
{
    protected $signature = 'subsidios:informes
        {--anio= : Año destino (subsidio_informes_{anio}). Requerido.}
        {--archivo= : Ruta del export de SIGEVA (.csv). Requerido.}
        {--sep=; : Separador de columnas del CSV (por defecto ;).}
        {--reemplazar : Vacía la tabla del año antes de importar.}';

    protected $description = 'Importa el export de informes de SIGEVA a subsidio_informes_{anio}, resolviendo proyecto_id por proyectos.sigeva.';

    /** Encabezado normalizado => campo destino. */
    private const MAP = [
        'CODIGO PROYECTO'     => 'codigo',
        'DIRECTOR'            => 'director',
        'INICIO'              => 'inicio',
        'FIN'                 => 'fin',
        'APELLIDO INTEGRANTE' => 'apellido',
        'NOMBRE INTEGRANTE'   => 'nombre',
        'CUIL INTEGRANTE'     => 'cuil',
        'DNI INTEGRANTE'      => 'documento',
        'ROL INTEGRANTE'      => 'rol',
        'EVALUACION'          => 'evaluacion',
        'FECHA ALTA'          => 'alta',
        'FECHA BAJA'          => 'baja',
        'FECHA FIN VIGENCIA'  => 'vigencia',
    ];

    /** @var string */
    protected $tabla;

    public function handle(): int
    {
        $anio = (int) $this->option('anio');
        if ($anio < 2000) {
            $this->error('Falta --anio (ej. 2026).');
            return self::FAILURE;
        }
        $this->tabla = "subsidio_informes_{$anio}";

        $archivo = $this->option('archivo');
        if (! $archivo || ! is_file($archivo)) {
            $this->error("No encuentro el archivo: {$archivo}");
            return self::FAILURE;
        }

        $this->crearTablaSiNoExiste();

        // Mapa sigeva => proyecto_id (una sola consulta, evita 11k lookups).
        $mapProy = [];
        foreach (DB::table('proyectos')->whereNotNull('sigeva')->where('sigeva', '<>', '')->select('id', 'sigeva')->get() as $p) {
            $mapProy[trim($p->sigeva)] = $p->id;
        }
        $this->info(count($mapProy) . ' proyectos con código SIGEVA cargados para el cruce.');

        if ($this->option('reemplazar')) {
            $n = DB::table($this->tabla)->count();
            DB::table($this->tabla)->delete();
            $this->warn("Tabla {$this->tabla} vaciada ({$n} fila(s) previas).");
        }

        $fh = fopen($archivo, 'r');
        if ($fh === false) {
            $this->error('No pude abrir el archivo.');
            return self::FAILURE;
        }

        $sep = (string) $this->option('sep');
        if ($sep === '') {
            $sep = ';';
        }

        // Encabezados
        $headers = fgetcsv($fh, 0, $sep, '"');
        if ($headers === false) {
            fclose($fh);
            $this->error('El archivo está vacío.');
            return self::FAILURE;
        }
        $idx = $this->mapearColumnas($headers);
        if (! isset($idx['codigo'])) {
            fclose($fh);
            $this->error('No encontré la columna "CODIGO PROYECTO" en el archivo.');
            return self::FAILURE;
        }

        $leidas = 0;
        $insertadas = 0;
        $noVigentes = 0;
        $sinMatch = [];
        $buffer = [];

        while (($row = fgetcsv($fh, 0, $sep, '"')) !== false) {
            // Saltar líneas vacías
            if ($row === [null] || (count($row) === 1 && trim((string) $row[0]) === '')) {
                continue;
            }
            $leidas++;

            $codigo = $this->col($row, $idx, 'codigo');
            if ($codigo === '') {
                continue;
            }
            if (! isset($mapProy[$codigo])) {
                $sinMatch[$codigo] = true;
                continue; // igual que el INNER JOIN proyecto: sin match no entra
            }

            // Sólo la fila VIGENTE: fecha_fin_vigencia vacía. Las que tienen fecha
            // fueron reemplazadas (superseded) y no valen. "Siempre vale una".
            $vig = $this->col($row, $idx, 'vigencia');
            if ($vig !== '' && strpos($vig, '0000-00-00') !== 0) {
                $noVigentes++;
                continue;
            }

            $apellido = $this->col($row, $idx, 'apellido');
            $nombre   = $this->col($row, $idx, 'nombre');

            $buffer[] = [
                'proyecto'    => $codigo,
                'proyecto_id' => $mapProy[$codigo],
                'director'    => $this->utf8($this->col($row, $idx, 'director')) ?: null,
                'inicio'      => $this->fecha($this->col($row, $idx, 'inicio')),
                'fin'         => $this->fecha($this->col($row, $idx, 'fin')),
                'integrante'  => $this->utf8(trim($apellido . ', ' . $nombre, ', ')) ?: null,
                'cuil'        => $this->col($row, $idx, 'cuil') ?: null,
                'documento'   => $this->documento($this->col($row, $idx, 'documento')),
                'rol'         => $this->utf8($this->col($row, $idx, 'rol')) ?: null,
                'evaluacion'  => $this->utf8($this->col($row, $idx, 'evaluacion')) ?: null,
                'alta'        => $this->fecha($this->col($row, $idx, 'alta')),
                'baja'        => $this->fecha($this->col($row, $idx, 'baja')),
                'vigencia'    => $this->fecha($this->col($row, $idx, 'vigencia')),
            ];

            if (count($buffer) >= 1000) {
                DB::table($this->tabla)->insert($buffer);
                $insertadas += count($buffer);
                $buffer = [];
            }
        }
        if ($buffer) {
            DB::table($this->tabla)->insert($buffer);
            $insertadas += count($buffer);
        }
        fclose($fh);

        $this->newLine();
        $this->info("Leídas: {$leidas} | Insertadas: {$insertadas} | No vigentes salteadas: {$noVigentes} | Total en {$this->tabla}: " . DB::table($this->tabla)->count());

        if ($sinMatch) {
            $cods = array_keys($sinMatch);
            $this->warn(count($cods) . ' código(s) SIGEVA sin proyecto en la base (no se cargaron):');
            foreach (array_slice($cods, 0, 30) as $c) {
                $this->line('  ' . $c);
            }
            if (count($cods) > 30) {
                $this->line('  ... y ' . (count($cods) - 30) . ' más.');
            }
        }

        return self::SUCCESS;
    }

    protected function crearTablaSiNoExiste(): void
    {
        DB::statement("
            CREATE TABLE IF NOT EXISTS `{$this->tabla}` (
                `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                `proyecto` VARCHAR(20) NULL DEFAULT NULL,
                `proyecto_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
                `director` VARCHAR(255) NULL DEFAULT NULL,
                `inicio` DATETIME NULL DEFAULT NULL,
                `fin` DATETIME NULL DEFAULT NULL,
                `integrante` VARCHAR(255) NULL DEFAULT NULL,
                `cuil` VARCHAR(20) NULL DEFAULT NULL,
                `documento` BIGINT(20) NULL DEFAULT NULL,
                `rol` VARCHAR(50) NULL DEFAULT NULL,
                `evaluacion` VARCHAR(20) NULL DEFAULT NULL,
                `alta` DATETIME NULL DEFAULT NULL,
                `baja` DATETIME NULL DEFAULT NULL,
                `vigencia` DATETIME NULL DEFAULT NULL,
                `created_at` TIMESTAMP NULL DEFAULT NULL,
                `updated_at` TIMESTAMP NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                INDEX `idx_proy_doc` (`proyecto_id`, `documento`)
            ) COLLATE='utf8mb4_unicode_ci' ENGINE=InnoDB
        ");

        // Auto-actualiza una tabla vieja donde documento quedó como INT:
        // hay documentos de 9+ dígitos que no entran en INT.
        $tipo = DB::selectOne("
            SELECT DATA_TYPE AS data_type
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = ?
              AND COLUMN_NAME = 'documento'
        ", [$this->tabla]);
        if ($tipo && strtolower($tipo->data_type) !== 'bigint') {
            DB::statement("ALTER TABLE `{$this->tabla}` MODIFY `documento` BIGINT(20) NULL DEFAULT NULL");
        }
    }

    /**
     * @param string[] $headers
     * @return array<string,int> campo => índice de columna
     */
    protected function mapearColumnas(array $headers): array
    {
        $idx = [];
        foreach ($headers as $i => $h) {
            $norm = $this->normalizar((string) $h);
            if (isset(self::MAP[$norm])) {
                $idx[self::MAP[$norm]] = $i;
            }
        }
        return $idx;
    }

    protected function normalizar(string $h): string
    {
        $h = strtoupper(trim($h));
        $h = preg_replace('/[^A-Z0-9]+/', ' ', $h);
        return trim($h);
    }

    /**
     * @param string[] $row
     * @param array<string,int> $idx
     */
    protected function col(array $row, array $idx, string $campo): string
    {
        if (! isset($idx[$campo])) {
            return '';
        }
        $i = $idx[$campo];
        return isset($row[$i]) ? trim((string) $row[$i]) : '';
    }

    /** Fecha válida o null. '0000-00-00' y vacío -> null. */
    protected function fecha(string $v)
    {
        if ($v === '' || strpos($v, '0000-00-00') === 0) {
            return null;
        }
        return $v;
    }

    /**
     * Documento: sólo dígitos (los pasaportes/no numéricos quedan null).
     * Devuelve string numérico (no int) para no romper con valores largos en 32-bit.
     */
    protected function documento(string $v)
    {
        $d = preg_replace('/\D+/', '', $v);
        return $d === '' ? null : $d;
    }

    /**
     * Normaliza a UTF-8. El export de HeidiSQL suele venir en Windows-1252;
     * si el valor ya es UTF-8 válido se deja igual (evita doble codificación).
     */
    protected function utf8(string $v): string
    {
        if ($v === '' || mb_check_encoding($v, 'UTF-8')) {
            return $v;
        }
        return mb_convert_encoding($v, 'UTF-8', 'Windows-1252');
    }
}
