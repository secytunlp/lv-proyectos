<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Actualiza la categoria SICADI (DI) por recurso segun la evaluacion 2023.
 * Toca: investigadors.sicadi_id, integrantes.sicadi_id (proyectos en
 * ejecucion 2024) + integrante_estados (nuevo snapshot, motivos = cambio
 * de SICADI), y el pivot investigador_sicadis.
 *
 * Dry-run por defecto (hace rollback). Para persistir: --commit
 */
class ActualizarSicadi2023 extends Command
{
    protected $signature = 'sicadi:actualizar-2023 {--commit : Persistir los cambios (por defecto es dry-run)}';
    protected $description = 'Actualiza la categoria SICADI por recurso (evaluacion 2023)';

    // Autor del cambio registrado en integrante_estados.user_id
    private $userId = 2;

    // cuil => categoria DI (evaluacion 2023)
    private $recursos = array(
        '20-24866687-1' => 'DI2',
        '20-18369706-5' => 'DI2',
        '20-28519872-1' => 'DI3',
        '20-13713567-2' => 'DI1',
        '27-17620520-8' => 'DI3',
        '23-25131607-4' => 'DI3',
        '27-14890109-6' => 'DI2',
        '27-28409985-6' => 'DI3',
        '27-24040603-4' => 'DI2',
        '27-29230070-6' => 'DI2',
        '27-17645649-9' => 'DI3',
        '27-13908634-7' => 'DI2',
        '27-21520734-5' => 'DI3',
        '27-27204946-2' => 'DI2',
        '27-18070672-6' => 'DI4',
        '27-36498752-3' => 'DI3',
        '27-24524149-1' => 'DI3',
        '20-22851838-8' => 'DI4',
        '27-28054577-0' => 'DI2',
        '27-24327544-5' => 'DI3',
        '27-29311974-6' => 'DI2',
        '27-28409822-1' => 'DI3',
        '20-28052894-4' => 'DI3',
        '20-25022024-4' => 'DI4',
        '20-20826453-3' => 'DI2',
        '27-26211933-0' => 'DI3',
        '27-16300852-7' => 'DI1',
        '27-32690882-2' => 'DI3',
        '20-30463809-6' => 'DI2',
        '23-24421786-9' => 'DI1',
        '27-26250951-1' => 'DI4',
        '27-23907864-3' => 'DI2',
        '23-13327169-4' => 'DI4',
        '27-18283466-7' => 'DI3',
        '20-24835471-3' => 'DI3',
        '20-23668138-7' => 'DI3',
        '20-25887648-3' => 'DI2',
        '27-28129305-8' => 'DI3',
        '23-25731432-4' => 'DI4',
        '27-33591551-3' => 'DI3',
        '27-24095976-9' => 'DI2',
        '27-28365040-0' => 'DI3',
        '27-30001026-7' => 'DI4',
        '20-23829953-6' => 'DI1',
        '27-21056083-7' => 'DI1',
        '20-26794207-3' => 'DI3',
        '20-16261651-0' => 'DI1',
        '20-23431357-7' => 'DI2',
        '20-25063044-2' => 'DI1',
        '23-25312775-9' => 'DI3',
        '27-25042158-9' => 'DI2',
        '20-16148592-7' => 'DI1',
        '27-94784749-5' => 'DI4',
        '20-22427130-2' => 'DI3',
        '27-33334837-9' => 'DI4',
        '27-23829517-9' => 'DI2',
        '20-16925870-9' => 'DI2',
        '20-24704350-1' => 'DI2',
    );

    public function handle()
    {
        $commit = $this->option('commit');

        DB::beginTransaction();
        try {
            // =============================================================
            // Datos de entrada
            // =============================================================
            DB::statement('DROP TEMPORARY TABLE IF EXISTS tmp_recursos');
            DB::statement(
                'CREATE TEMPORARY TABLE tmp_recursos ('.
                'cuil VARCHAR(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, '.
                'nueva_cat VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci)'
            );
            $filas = array();
            foreach ($this->recursos as $cuil => $cat) {
                $filas[] = array('cuil' => $cuil, 'nueva_cat' => $cat);
            }
            DB::table('tmp_recursos')->insert($filas);

            // =============================================================
            // Snapshots (antes de modificar) con detalle para el informe
            // =============================================================
            DB::statement('DROP TEMPORARY TABLE IF EXISTS tmp_inv');
            DB::statement(
                'CREATE TEMPORARY TABLE tmp_inv AS '.
                'SELECT i.id AS investigador_id, p.cuil AS cuil, '.
                "CONCAT(p.apellido, ', ', p.nombre) AS investigador, ".
                'so.nombre AS cat_anterior, sn.nombre AS cat_nueva, sn.id AS nuevo_sicadi_id '.
                'FROM investigadors i '.
                'JOIN personas p ON p.id = i.persona_id '.
                'JOIN tmp_recursos t ON t.cuil = p.cuil '.
                'JOIN sicadis sn ON sn.nombre = t.nueva_cat '.
                'LEFT JOIN sicadis so ON so.id = i.sicadi_id '.
                'WHERE i.sicadi_id <> sn.id OR i.sicadi_id IS NULL'
            );

            DB::statement('DROP TEMPORARY TABLE IF EXISTS tmp_integ');
            DB::statement(
                'CREATE TEMPORARY TABLE tmp_integ AS '.
                'SELECT ig.id AS integrante_id, p.cuil AS cuil, '.
                "CONCAT(p.apellido, ', ', p.nombre) AS investigador, ".
                'pr.id AS proyecto_id, pr.inicio AS inicio, pr.fin AS fin, '.
                'so.nombre AS cat_anterior, sn.nombre AS cat_nueva, sn.id AS nuevo_sicadi_id '.
                'FROM integrantes ig '.
                'JOIN investigadors i ON i.id = ig.investigador_id '.
                'JOIN personas p ON p.id = i.persona_id '.
                'JOIN tmp_recursos t ON t.cuil = p.cuil '.
                'JOIN sicadis sn ON sn.nombre = t.nueva_cat '.
                'LEFT JOIN sicadis so ON so.id = ig.sicadi_id '.
                'JOIN proyectos pr ON pr.id = ig.proyecto_id '.
                "WHERE pr.inicio <= '2024-12-31' AND pr.fin >= '2024-01-01' ".
                'AND (ig.sicadi_id <> sn.id OR ig.sicadi_id IS NULL)'
            );

            // =============================================================
            // INFORME: que se va a modificar
            // =============================================================
            $this->line('');
            $this->line('============================================================');
            $this->info($commit ? 'MODO: COMMIT (se persiste)' : 'MODO: DRY-RUN (no se guarda nada)');
            $this->line('============================================================');

            // --- Recursos no encontrados (se omiten) ---
            $noEnc = DB::select(
                'SELECT t.cuil, t.nueva_cat, '.
                "CASE WHEN p.id IS NULL THEN 'sin persona' ELSE 'sin investigador' END AS motivo ".
                'FROM tmp_recursos t '.
                'LEFT JOIN personas p ON p.cuil = t.cuil '.
                'LEFT JOIN investigadors i ON i.persona_id = p.id '.
                'WHERE p.id IS NULL OR i.id IS NULL'
            );
            if (count($noEnc) > 0) {
                $this->line('');
                $this->warn('Recursos no encontrados (OMITIDOS): '.count($noEnc));
                $rows = array();
                foreach ($noEnc as $r) {
                    $rows[] = array($r->cuil, $r->nueva_cat, $r->motivo);
                }
                $this->table(array('CUIL', 'Cat. 2023', 'Motivo'), $rows);
            }

            // --- Investigadores que cambian ---
            $detInv = DB::select('SELECT cuil, investigador, cat_anterior, cat_nueva FROM tmp_inv ORDER BY investigador');
            $this->line('');
            $this->info('Investigadores con cambio de categoria: '.count($detInv));
            if (count($detInv) > 0) {
                $rows = array();
                foreach ($detInv as $r) {
                    $rows[] = array($r->cuil, $r->investigador, $r->cat_anterior === null ? '(sin)' : $r->cat_anterior, $r->cat_nueva);
                }
                $this->table(array('CUIL', 'Investigador', 'Actual', '2023'), $rows);
            }

            // --- Integrantes (ejecucion 2024) que cambian ---
            $detInteg = DB::select('SELECT cuil, investigador, proyecto_id, inicio, fin, cat_anterior, cat_nueva FROM tmp_integ ORDER BY investigador, proyecto_id');
            $this->line('');
            $this->info('Integrantes en ejecucion 2024 con cambio: '.count($detInteg));
            if (count($detInteg) > 0) {
                $rows = array();
                foreach ($detInteg as $r) {
                    $periodo = $r->inicio.' -> '.$r->fin;
                    $rows[] = array($r->cuil, $r->investigador, $r->proyecto_id, $periodo, $r->cat_anterior === null ? '(sin)' : $r->cat_anterior, $r->cat_nueva);
                }
                $this->table(array('CUIL', 'Investigador', 'Proy.', 'Periodo', 'Actual', '2023'), $rows);
            }

            // =============================================================
            // APLICAR (con conteo real de filas afectadas)
            // =============================================================
            $rInvest = DB::affectingStatement(
                'UPDATE investigadors i '.
                'JOIN tmp_inv a ON a.investigador_id = i.id '.
                'SET i.sicadi_id = a.nuevo_sicadi_id'
            );

            $rIntegrante = DB::affectingStatement(
                'UPDATE integrantes ig '.
                'JOIN tmp_integ a ON a.integrante_id = ig.id '.
                'SET ig.sicadi_id = a.nuevo_sicadi_id'
            );

            $rEstadoCerrado = DB::affectingStatement(
                'UPDATE integrante_estados ie '.
                'JOIN tmp_integ a ON a.integrante_id = ie.integrante_id '.
                'SET ie.hasta = NOW(), ie.updated_at = NOW() '.
                'WHERE ie.hasta IS NULL'
            );

            $rEstadoNuevo = DB::affectingStatement(
                'INSERT INTO integrante_estados '.
                '(integrante_id, user_id, tipo, alta, baja, cambio, horas, estado, '.
                'consecuencias, motivos, reduccion, categoria_id, sicadi_id, deddoc, '.
                'cargo_id, facultad_id, unidad_id, carrerainv_id, organismo_id, '.
                'institucion, beca, desde, hasta, comentarios, created_at, updated_at) '.
                'SELECT ig.id, ?, ig.tipo, ig.alta, ig.baja, ig.cambio, ig.horas, ig.estado, '.
                'ig.consecuencias, ig.motivos, ig.reduccion, ig.categoria_id, a.nuevo_sicadi_id, ig.deddoc, '.
                'ig.cargo_id, ig.facultad_id, ig.unidad_id, ig.carrerainv_id, ig.organismo_id, '.
                'ig.institucion, ig.beca, NOW(), NULL, ?, NOW(), NOW() '.
                'FROM tmp_integ a JOIN integrantes ig ON ig.id = a.integrante_id',
                array($this->userId, 'Cambio de SICADI por recurso')
            );

            $rPivotDesmarca = DB::affectingStatement(
                'UPDATE investigador_sicadis isd '.
                'JOIN tmp_inv a ON a.investigador_id = isd.investigador_id '.
                'SET isd.actual = 0 '.
                'WHERE isd.actual = 1 AND isd.sicadi_id <> a.nuevo_sicadi_id'
            );
            $rPivotNuevo = DB::affectingStatement(
                'INSERT INTO investigador_sicadis (investigador_id, sicadi_id, notificacion, actual, year) '.
                'SELECT a.investigador_id, a.nuevo_sicadi_id, NULL, 1, 2023 '.
                'FROM tmp_inv a '.
                'WHERE NOT EXISTS ('.
                '  SELECT 1 FROM investigador_sicadis x '.
                '  WHERE x.investigador_id = a.investigador_id AND x.sicadi_id = a.nuevo_sicadi_id'.
                ')'
            );
            $rPivotMarca = DB::affectingStatement(
                'UPDATE investigador_sicadis isd '.
                'JOIN tmp_inv a ON a.investigador_id = isd.investigador_id '.
                '                AND isd.sicadi_id = a.nuevo_sicadi_id '.
                'SET isd.actual = 1, isd.year = 2023'
            );

            // =============================================================
            // RESUMEN de filas afectadas por operacion
            // =============================================================
            $this->line('');
            $this->info('Filas afectadas por operacion:');
            $this->table(
                array('Operacion', 'Filas'),
                array(
                    array('investigadors.sicadi_id',                  $rInvest),
                    array('integrantes.sicadi_id (ejec. 2024)',       $rIntegrante),
                    array('integrante_estados cerrados (hasta=NOW)',  $rEstadoCerrado),
                    array('integrante_estados nuevos',                $rEstadoNuevo),
                    array('investigador_sicadis desmarcados',         $rPivotDesmarca),
                    array('investigador_sicadis insertados (2023)',   $rPivotNuevo),
                    array('investigador_sicadis re-marcados (2023)',  $rPivotMarca),
                )
            );

            if ($commit) {
                DB::commit();
                $this->line('');
                $this->info('COMMIT: cambios persistidos.');
            } else {
                DB::rollBack();
                $this->line('');
                $this->warn('DRY-RUN: nada se guardo. Volve a correr con --commit para grabar.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error (rollback): '.$e->getMessage());
            return 1;
        }

        return 0;
    }
}
