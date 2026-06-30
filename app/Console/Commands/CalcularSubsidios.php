<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Subsidy calculation pipeline (CIU polynomial, 2020 reform: hours-based).
 *
 * SP  = 1.3 * SUM(Hi for I,II,III) + 0.8 * SUM(Hi for IV) + 0.5 * SUM(Hi for V/uncat) + Ord*Nd
 * ST  = SUM(SP) over all projects
 * m   = MT / ST
 * MP  = m * SP
 *
 * Hi  = hours each integrante dedicates to that project (intproy_AAAA.in_dedi)
 * Ord = 8 (Lab/Centro/Instituto) or 2 (UPID/default), per project (dirproy_AAAA.ord)
 * Nd  = director's number of projects in the same approved unit (dirproy_AAAA.numdirfac)
 *
 * The calculation tables are per-year: dirproy_AAAA / intproy_AAAA (kept as history).
 * Reads from SICADI core tables, writes ONLY to scratch tables:
 *   subsidio_integrantes, subsidio_proyectos, dirproy_AAAA, intproy_AAAA.
 * Never writes to subsidio_informes (imported from SIGEVA) or
 * subsidio_proyecto_renuncias (maintained by hand).
 */
class CalcularSubsidios extends Command
{
    protected $signature = 'subsidios:calcular
        {--anio= : Period year, e.g. 2026. Defines dirproy_AAAA / intproy_AAAA. Required.}
        {--mt= : Monto Total (MT) to distribute this period. Required.}
        {--periodo= : Viajes period id for the approved-units filter (#5). Required unless --skip-extraction.}
        {--fecha-corte= : Cutoff date (Y-m-d). Defaults to {anio-1}-12-31.}
        {--hasta-inicio= : Exclude projects with inicio >= this date (Y-m-d). For replaying a past period.}
        {--ord-dividir : Use ord/numdirfac (original controller) instead of ord*numdirfac (document). For comparison.}
        {--skip-extraction : Skip #4/#5, run only on already-populated subsidio_* tables.}
        {--dry-run : Run everything inside a transaction and roll back at the end.}';

    protected $description = 'Calculates the direct research subsidies (CIU hours-based polynomial).';

    /**
     * carrerainv_id values that count as "investigador de carrera".
     * Legacy ids were 1,2,3,4,5,6,8,9,10,12,13. TODO: confirm for the new schema.
     *
     * @var int[]
     */
    protected $idsCarreraInv = [1, 2, 3, 4, 5, 6, 12];

    /** @var int */
    protected $anio;
    /** @var int */
    protected $periodo;
    /** @var string|null */
    protected $hastaInicio;
    /** @var string */
    protected $tablaDir;   // dirproy_AAAA
    /** @var string */
    protected $tablaInt;   // intproy_AAAA

    public function handle(): int
    {
        set_time_limit(0);

        $this->anio = (int) $this->option('anio');
        if ($this->anio < 2000) {
            $this->error('Falta --anio (ej. 2026).');
            return self::FAILURE;
        }
        $this->tablaDir = "dirproy_{$this->anio}";
        $this->tablaInt = "intproy_{$this->anio}";

        $mt = (float) $this->option('mt');
        if ($mt <= 0) {
            $this->error('Falta --mt o es inválido.');
            return self::FAILURE;
        }

        $fechaCorte = $this->option('fecha-corte') ?: ($this->anio - 1).'-12-31';

        $this->hastaInicio = $this->option('hasta-inicio') ?: null;

        $this->periodo = (int) $this->option('periodo');
        if (! $this->option('skip-extraction') && $this->periodo <= 0) {
            $this->error('Falta --periodo (id del período de viajes para la #5).');
            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');

        // CREATE TABLE is DDL (implicit commit), so it must run BEFORE the
        // transaction, otherwise it would close it and break --dry-run/rollback.
        $this->crearTablasSiNoExisten();

        DB::beginTransaction();
        try {
            $this->vaciarTablas();

            if (! $this->option('skip-extraction')) {
                $this->poblarSubsidioIntegrantes($fechaCorte);
                $this->poblarSubsidioProyectos($fechaCorte);
            } else {
                $this->warn('Extracción salteada: usando subsidio_* tal como están.');
            }

            $this->limpiarPendientes();
            $this->poblarIntproy($fechaCorte);
            $this->poblarDirproy();

            $this->calculo1();
            $resumen = $this->calculo2($mt);
            $this->calculo3();

            $this->mostrarResumen($resumen);

            if ($dryRun) {
                DB::rollBack();
                $this->warn('DRY-RUN: todos los cambios fueron revertidos.');
            } else {
                DB::commit();
                $this->info("Cálculo {$this->anio} confirmado y guardado.");
            }

            return self::SUCCESS;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Error, se revirtió todo: '.$e->getMessage());
            return self::FAILURE;
        }
    }

    /**
     * Create this year's calculation tables if they don't exist yet.
     * Remove this method (and the call) if you prefer to handle it via migration.
     */
    protected function crearTablasSiNoExisten(): void
    {
        DB::statement("
            CREATE TABLE IF NOT EXISTS `{$this->tablaDir}` (
                `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                `pr_codigo` VARCHAR(255) NULL DEFAULT NULL,
                `pr_dirpr` VARCHAR(255) NULL DEFAULT NULL,
                `numdirfac` INT(11) NULL DEFAULT 0,
                `monto` DECIMAL(15,2) NULL DEFAULT 0.00,
                `total` DECIMAL(15,2) NULL DEFAULT 0.00,
                `spond` DECIMAL(15,2) NULL DEFAULT NULL,
                `divi` DECIMAL(15,2) NULL DEFAULT NULL,
                `dir_id` INT(11) NULL DEFAULT NULL,
                `pr_id` INT(11) NULL DEFAULT NULL,
                `fac_id` INT(11) NULL DEFAULT NULL,
                `unidad_id` INT(11) NULL DEFAULT NULL,
                `ord` INT(11) NULL DEFAULT NULL,
                `created_at` TIMESTAMP NULL DEFAULT NULL,
                `updated_at` TIMESTAMP NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) COLLATE='utf8mb4_unicode_ci' ENGINE=InnoDB
        ");

        DB::statement("
            CREATE TABLE IF NOT EXISTS `{$this->tablaInt}` (
                `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                `pr_codigo` VARCHAR(255) NULL DEFAULT NULL,
                `in_nombre` VARCHAR(255) NULL DEFAULT NULL,
                `in_cainv` VARCHAR(255) NULL DEFAULT NULL,
                `in_esdir` TINYINT(4) NULL DEFAULT 0,
                `marca` VARCHAR(50) NULL DEFAULT '0',
                `dedi` VARCHAR(50) NULL DEFAULT '0',
                `in_dedi` VARCHAR(255) NULL DEFAULT NULL,
                `numproy` INT(11) NULL DEFAULT 0,
                `pr_id` INT(11) NULL DEFAULT 0,
                `in_id` INT(11) NULL DEFAULT 0,
                `fac_id` INT(11) NULL DEFAULT 0,
                `created_at` TIMESTAMP NULL DEFAULT NULL,
                `updated_at` TIMESTAMP NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) COLLATE='utf8mb4_unicode_ci' ENGINE=InnoDB
        ");
    }

    /**
     * Reset the scratch tables that get fully rebuilt each run.
     *
     * Uses DELETE (not TRUNCATE) on purpose: TRUNCATE causes an implicit COMMIT
     * in MySQL/MariaDB, which would close the transaction opened in handle() and
     * break both --dry-run and the rollback-on-error.
     *
     * NOTE: this clears subsidio_integrantes/subsidio_proyectos because the new
     * in-DB flow rebuilds them from SICADI. If you still maintain manual state on
     * subsidio_integrantes across runs (the old import workflow), do NOT clear it
     * here and rely on limpiarPendientes() instead.
     */
    protected function vaciarTablas(): void
    {
        $this->info("Vaciando {$this->tablaDir} / {$this->tablaInt}...");
        DB::table($this->tablaDir)->delete();
        DB::table($this->tablaInt)->delete();
        DB::table('subsidio_integrantes')->delete();
        DB::table('subsidio_proyectos')->delete();
        // subsidio_informes  -> imported from SIGEVA, NOT touched
        // subsidio_proyecto_renuncias -> maintained by hand, NOT touched
    }

    /**
     * #4 — Integrantes para subsidios automáticos (SICADI nuevo).
     * TODO: confirm personas.documento, proyectos.facultad_id.
     */
    protected function poblarSubsidioIntegrantes(string $fechaCorte): void
    {
        $this->info('Poblando subsidio_integrantes (#4)...');

        // Build the "investigador de carrera" branch only if ids are configured.
        $carreraWhen = '';
        if (! empty($this->idsCarreraInv)) {
            $ids = implode(',', array_map('intval', $this->idsCarreraInv));
            $carreraWhen = "WHEN i.carrerainv_id IN ($ids) THEN '1'";
        }

        // Optional: exclude projects starting on/after a date (replay a past period).
        $hastaInicioSql = '';
        $bindings = [$fechaCorte, $fechaCorte];
        if ($this->hastaInicio) {
            $hastaInicioSql = ' AND p.inicio < ?';
            $bindings[] = $this->hastaInicio;
        }

        $sql = "
            INSERT INTO subsidio_integrantes
                (proyecto, inicio, fin, documento, director, alta, baja,
                 categoria, dedicacion, universidad_id, integrante, horas,
                 investigador_id, proyecto_id, facultad_id)
            SELECT
                p.codigo,
                p.inicio,
                p.fin,
                per.documento,
                CASE WHEN i.tipo = 'Director' THEN '1' ELSE '0' END,
                CASE WHEN i.alta = '0000-00-00' THEN '' ELSE i.alta END,
                CASE WHEN i.baja = '0000-00-00' THEN '' ELSE i.baja END,
                -- Best category = lowest id within {6,7,8,9,10}; resolved against
                -- categorias so the controller always gets 'I'..'V' (never 'D2').
                cat_min.nombre,
                -- Dedication 1/2/3: downstream filter only (value arbitrary, must be non-NULL).
                CASE
                    WHEN (i.alta_beca IS NULL OR i.alta_beca <= CURDATE())
                         AND (i.baja_beca IS NULL OR i.baja_beca > ?) THEN '1'
                    $carreraWhen
                    WHEN i.deddoc = 'Exclusiva'     THEN '1'
                    WHEN i.deddoc = 'Semiexclusiva' THEN '2'
                    WHEN i.deddoc = 'Simple'        THEN '3'
                    ELSE NULL
                END,
                i.universidad_id,
                CONCAT(per.apellido, ', ', per.nombre),
                i.horas,
                inv.id,
                p.id,
                p.facultad_id
            FROM integrantes i
                JOIN investigadors inv ON i.investigador_id = inv.id
                JOIN personas per      ON inv.persona_id = per.id
                JOIN proyectos p       ON i.proyecto_id = p.id
                LEFT JOIN categorias cat_min ON cat_min.id =
                    CASE
                        WHEN inv.categoria_id IN (6,7,8,9,10) AND inv.sicadi_id IN (6,7,8,9,10)
                             THEN LEAST(inv.categoria_id, inv.sicadi_id)
                        WHEN inv.categoria_id IN (6,7,8,9,10) THEN inv.categoria_id
                        WHEN inv.sicadi_id    IN (6,7,8,9,10) THEN inv.sicadi_id
                        ELSE NULL
                    END
            WHERE
                i.tipo <> 'Colaborador'
              AND p.tipo = 'I+D'
              AND p.estado = 'Acreditado'
              AND p.fin > ?
              AND (i.estado IS NULL OR i.estado = '')
              AND (i.baja IS NULL OR i.baja = '0000-00-00' OR i.alta <> i.baja)
              $hastaInicioSql
        ";

        DB::insert($sql, $bindings);
    }

    /**
     * #5 — Proyectos para subsidios automáticos (SICADI nuevo).
     *
     * ord per project:
     *   - unit NOT approved (Ord 284) for this period -> 2
     *   - unit approved                               -> 8
     *
     * NOTE: the UPID-specific case (approved UPID -> 2) was dropped on request;
     * approved units now all get 8.
     */
    protected function poblarSubsidioProyectos(string $fechaCorte): void
    {
        $this->info('Poblando subsidio_proyectos (#5)...');

        // Optional: exclude projects starting on/after a date (replay a past period).
        $hastaInicioSql = '';
        $bindings = [$this->periodo, $fechaCorte];
        if ($this->hastaInicio) {
            $hastaInicioSql = ' AND p.inicio < ?';
            $bindings[] = $this->hastaInicio;
        }

        DB::insert("
            INSERT INTO subsidio_proyectos
                (proyecto, inicio, fin, documento, director,
                 investigador_id, facultad_id, unidad_id, ord, proyecto_id)
            SELECT
                p.codigo,
                p.inicio,
                p.fin,
                per.documento,
                CONCAT(per.apellido, ', ', per.nombre),
                inv.id,
                p.facultad_id,
                p.unidad_id,
                CASE WHEN ua.unidad_id IS NULL THEN 2 ELSE 8 END,
                p.id
            FROM integrantes i
                JOIN investigadors inv ON i.investigador_id = inv.id
                JOIN personas per      ON inv.persona_id = per.id
                JOIN proyectos p       ON i.proyecto_id = p.id
                LEFT JOIN viaje_evaluacion_unidad_aprobadas ua
                    ON ua.unidad_id = p.unidad_id AND ua.periodo_id = ?
            WHERE
                i.tipo = 'Director'
              AND p.tipo = 'I+D'
              AND p.estado = 'Acreditado'
              AND p.fin > ?
              $hastaInicioSql
        ", $bindings);
    }

    /**
     * Old import-workflow cleanup. May be a no-op in the new in-DB flow where
     * subsidio_integrantes is rebuilt from scratch. Kept for parity; review.
     */
    protected function limpiarPendientes(): void
    {
        DB::table('subsidio_integrantes')->whereIn('estado_id', [1, 2, 6, 7])->delete();
        DB::table('subsidio_integrantes')->whereIn('estado_id', [4, 5])->update(['baja' => null]);
    }

    /**
     * Populate intproy_AAAA from subsidio_integrantes (hours-based variant).
     * Two inserts: informed projects + current projects.
     *
     * NOTE: the period thresholds are derived from $fechaCorte but the legacy
     * query used several different offsets. Review per period.
     */
    protected function poblarIntproy(string $fechaCorte): void
    {
        $this->info("Poblando {$this->tablaInt}...");

        $year = (int) substr($fechaCorte, 0, 4); // e.g. 2025 from '2025-12-31'
        $iniInformado = ($year).'-01-01';         // informed: inicio < this
        $bajaCorte    = ($year + 1).'-01-01';     // baja > this (or null)
        $altaNuevos   = ($year - 1).'-09-01';     // alta after this counts as new
        $iniActual    = ($year - 1).'-12-31';     // current: inicio > this
        $finActual    = ($year + 1).'-01-01';     // current: fin > this

        $cols = 'pr_codigo,in_nombre,in_cainv,in_esdir,in_dedi,pr_id,in_id,fac_id';
        $select = "
            SELECT %s
                IF(LEFT(si.proyecto, 3) = '11/', SUBSTRING(si.proyecto, 4, 10), si.proyecto),
                si.integrante,
                si.categoria,
                si.director,
                si.horas,
                si.proyecto_id,
                si.investigador_id,
                si.facultad_id
            FROM subsidio_integrantes si
                INNER JOIN subsidio_proyectos sp ON si.proyecto_id = sp.proyecto_id
                LEFT JOIN subsidio_informes inf
                    ON inf.proyecto_id = si.proyecto_id AND inf.documento = si.documento
            WHERE si.dedicacion IN (1,2,3)
              AND (si.baja IS NULL OR si.baja > ? OR si.baja = '0000-00-00')
              AND (si.universidad_id = 11 OR si.universidad_id = 0 OR si.universidad_id IS NULL)
              AND NOT EXISTS (
                  SELECT r.id FROM subsidio_proyecto_renuncias r
                  WHERE r.proyecto_id = si.proyecto_id
              )
        ";

        // Informed projects (running & reported): satisfactory evaluation, holder, or recent addition.
        DB::insert(
            "INSERT INTO `{$this->tablaInt}` ($cols) ".sprintf($select, 'DISTINCT ')."
              AND sp.inicio < ? AND sp.fin > ?
              AND (inf.evaluacion = 'Satisfactorio' OR inf.rol = 'Titular' OR si.alta > ?)",
            [$bajaCorte, $iniInformado, $iniInformado, $altaNuevos]
        );

        // Current projects (newly started).
        DB::insert(
            "INSERT INTO `{$this->tablaInt}` ($cols) ".sprintf($select, '')."
              AND sp.inicio > ? AND sp.fin > ?",
            [$bajaCorte, $iniActual, $finActual]
        );
    }

    /**
     * Populate dirproy_AAAA from subsidio_proyectos (projects in execution),
     * excluding projects with a renuncia.
     */
    protected function poblarDirproy(): void
    {
        $this->info("Poblando {$this->tablaDir}...");

        DB::insert("
            INSERT INTO `{$this->tablaDir}` (pr_codigo, pr_dirpr, dir_id, pr_id, fac_id, unidad_id, ord)
            SELECT
                IF(LEFT(sp.proyecto, 3) = '11/', SUBSTRING(sp.proyecto, 4, 10), sp.proyecto),
                sp.director,
                sp.investigador_id,
                sp.proyecto_id,
                sp.facultad_id,
                sp.unidad_id,
                sp.ord
            FROM subsidio_proyectos sp
            WHERE NOT EXISTS (
                SELECT r.id FROM subsidio_proyecto_renuncias r
                WHERE r.proyecto_id = sp.proyecto_id
            )
        ");
    }

    /**
     * calculo1 — numdirfac = director's number of projects within the same unit.
     * Grouped AND written per (dir_id, unidad_id), updating by pr_id so two
     * different units never overwrite each other.
     */
    protected function calculo1(): void
    {
        $this->info('calculo1: numdirfac...');

        $dirproys = DB::table($this->tablaDir)
            ->orderBy('dir_id')->orderBy('unidad_id')->get();

        $currentDir = null;
        $currentUni = null;
        $count = 0;
        $groupPrIds = [];

        $flush = function (array $prIds, int $count): void {
            if ($count > 0 && $prIds) {
                DB::table($this->tablaDir)->whereIn('pr_id', $prIds)
                    ->update(['numdirfac' => $count]);
            }
        };

        foreach ($dirproys as $d) {
            if ($currentDir !== $d->dir_id || $currentUni !== $d->unidad_id) {
                $flush($groupPrIds, $count);
                $currentDir = $d->dir_id;
                $currentUni = $d->unidad_id;
                $count = 1;
                $groupPrIds = [$d->pr_id];
            } else {
                $count++;
                $groupPrIds[] = $d->pr_id;
            }
        }
        $flush($groupPrIds, $count); // last group
    }

    /**
     * calculo2 — the core polynomial.
     *
     * Corrections vs the original controller:
     *  - Ord*Nd (multiply by numdirfac) instead of Ord/Nd, per the CIU document.
     *  - numproy / 1÷numproy removed (dead code under the hours-based model).
     *  - $mt comes in as a parameter.
     *
     * @return array{Nd: float, St: float, M: float, totalMonto: float, Mt: float, diff: float}
     */
    protected function calculo2(float $mt): array
    {
        $this->info('calculo2: polinomio...');

        // Ord per project = ord_base * Nd (Nd = numdirfac from calculo1), per the
        // CIU document formula. With --ord-dividir it divides instead (original
        // controller behaviour), for comparison. Only matters for directors with
        // numdirfac >= 2; identical for everyone else.
        $op = $this->option('ord-dividir') ? 'ord / numdirfac' : 'ord * numdirfac';
        DB::table($this->tablaDir)->where('ord', '!=', 0)
            ->update(['ord' => DB::raw($op)]);

        // Nd term of ST = sum of every project's Ord*Nd.
        $Nd = (float) DB::table($this->tablaDir)->sum('ord');

        // ST: weighted hours across all integrantes, by category bracket.
        $totA = $totB = $totC = 0.0; // A: I/II/III, B: IV, C: V/uncat
        foreach (DB::table($this->tablaInt)->get() as $r) {
            switch ($r->in_cainv) {
                case 'I':
                case 'II':
                case 'III':
                    $totA += (float) $r->in_dedi;
                    break;
                case 'IV':
                    $totB += (float) $r->in_dedi;
                    break;
                default:
                    $totC += (float) $r->in_dedi;
                    break;
            }
        }

        $St = (1.3 * $totA) + (0.8 * $totB) + (0.5 * $totC) + $Nd;

        if ($St <= 0) {
            throw new \RuntimeException(
                'ST = 0: no hay integrantes/proyectos cargados para calcular '.
                '(¿corriste con --skip-extraction y las subsidio_* vacías?).'
            );
        }

        // Round M to 2 decimals, exactly as the original controller did.
        $M = round($mt / $St, 2);

        $this->line("  ST = 1.3*$totA + 0.8*$totB + 0.5*$totC + Nd($Nd) = $St");
        $this->line("  m  = $mt / $St = $M");

        // Per project: SP and MP.
        $intproys = DB::table($this->tablaInt)->orderBy('pr_id')->get();
        $i = 0;
        $n = $intproys->count();

        while ($i < $n) {
            $mProy = $intproys[$i]->pr_id;
            $a = $b = $c = 0.0;

            while ($i < $n && $intproys[$i]->pr_id === $mProy) {
                switch ($intproys[$i]->in_cainv) {
                    case 'I':
                    case 'II':
                    case 'III':
                        $a += (float) $intproys[$i]->in_dedi;
                        break;
                    case 'IV':
                        $b += (float) $intproys[$i]->in_dedi;
                        break;
                    default:
                        $c += (float) $intproys[$i]->in_dedi;
                        break;
                }
                $i++;
            }

            $dirproy = DB::table($this->tablaDir)->where('pr_id', $mProy)->first();
            if ($dirproy) {
                $ordNd = (float) $dirproy->ord;
                $Sp = (1.3 * $a) + (0.8 * $b) + (0.5 * $c) + $ordNd;
                DB::table($this->tablaDir)->where('pr_id', $mProy)->update([
                    'divi'  => $ordNd,
                    'monto' => round($Sp * $M, 2),
                    'spond' => $Sp,
                ]);
            }
        }

        $totalMonto = (float) DB::table($this->tablaDir)->sum('monto');

        return [
            'Nd'         => $Nd,
            'St'         => $St,
            'M'          => $M,
            'totalMonto' => $totalMonto,
            'Mt'         => $mt,
            'diff'       => round($mt - $totalMonto, 2),
        ];
    }

    /**
     * calculo3 — total per director (sum of monto across their projects).
     * Total written on the LAST row of each director's group (legacy report
     * convention). TODO: confirm whether it should be on every row instead.
     */
    protected function calculo3(): void
    {
        $this->info('calculo3: totales por director...');

        $dirproys = DB::table($this->tablaDir)
            ->orderBy('dir_id')->orderBy('pr_id')->get();
        if ($dirproys->isEmpty()) {
            return;
        }

        $currentDir = $dirproys[0]->dir_id;
        $acc = 0.0;
        $lastId = null;

        foreach ($dirproys as $d) {
            if ($currentDir === $d->dir_id) {
                $acc += (float) $d->monto;
            } else {
                if ($lastId !== null) {
                    DB::table($this->tablaDir)->where('id', $lastId)->update(['total' => $acc]);
                }
                $currentDir = $d->dir_id;
                $acc = (float) $d->monto;
            }
            $lastId = $d->id;
        }
        if ($lastId !== null) { // last group
            DB::table($this->tablaDir)->where('id', $lastId)->update(['total' => $acc]);
        }
    }

    protected function mostrarResumen(array $r): void
    {
        $this->newLine();
        $this->table(
            ['Concepto', 'Valor'],
            [
                ['Año', $this->anio],
                ['Nd (suma Ord*Nd)', number_format($r['Nd'], 2)],
                ['ST', number_format($r['St'], 2)],
                ['m (módulo)', number_format($r['M'], 2)],
                ['MT (objetivo)', number_format($r['Mt'], 2)],
                ['Monto calculado', number_format($r['totalMonto'], 2)],
                ['Diferencia', number_format($r['diff'], 2)],
            ]
        );
    }
}
