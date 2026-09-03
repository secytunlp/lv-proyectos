<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Fusiona dos investigadores duplicados en uno solo.
 *
 * Estrategia de fusión:
 * - Mantiene el investigador con ID MENOR (más antiguo)
 * - Mueve TODOS los integrantes/becas/etc del segundo al primero
 * - Toma datos no vacíos del segundo si el primero los tiene vacíos
 * - Maneja users.cuil con cuidado (UNIQUE constraint)
 * - Genera backup antes de ejecutar
 *
 * Uso: php artisan investigadores:fusionar 3428 98374 --confirmar
 */
class FusionarInvestigadores extends Command
{
    protected $signature = 'investigadores:fusionar
        {id_keep : ID del investigador a mantener (será el destino)}
        {id_remove : ID del investigador a eliminar (redundante)}
        {--confirmar : Ejecutar la fusión (sin esto, solo simula)}
        {--cuil-destino= : Qué CUIL usar (keep|remove|preguntar) - default: keep}';

    protected $description = 'Fusiona dos investigadores duplicados. El segundo se elimina, sus datos van al primero.';

    private $backup_tables = [
        'investigadors', 'personas', 'integrantes', 'investigador_becas',
        'investigador_titulos', 'investigador_tituloposts', 'investigador_cargos',
        'investigador_categorias', 'investigador_carreras', 'investigador_sicadis',
        'users'
    ];

    public function handle()
    {
        $id_keep   = (int) $this->argument('id_keep');
        $id_remove = (int) $this->argument('id_remove');
        $confirmar = $this->option('confirmar');
        $cuil_dest = $this->option('cuil-destino') ?: 'keep';

        if ($id_keep === $id_remove) {
            $this->error('Los dos IDs son iguales.');
            return 1;
        }

        // --- Validar que existan ---
        $inv_keep = DB::table('investigadors')->find($id_keep);
        $inv_remove = DB::table('investigadors')->find($id_remove);

        if (!$inv_keep) {
            $this->error("Investigador $id_keep no existe.");
            return 1;
        }
        if (!$inv_remove) {
            $this->error("Investigador $id_remove no existe.");
            return 1;
        }

        // --- Cargar datos ---
        $persona_keep = DB::table('personas')->find($inv_keep->persona_id);
        $persona_remove = DB::table('personas')->find($inv_remove->persona_id);

        $user_keep = DB::table('users')->where('cuil', $persona_keep->cuil)->first();
        $user_remove = DB::table('users')->where('cuil', $persona_remove->cuil)->first();

        $integrantes_remove = DB::table('integrantes')
            ->where('investigador_id', $id_remove)->count();
        $becas_remove = DB::table('investigador_becas')
            ->where('investigador_id', $id_remove)->count();

        // --- Mostrar resumen ---
        $this->info("=== FUSIÓN DE INVESTIGADORES ===");
        $this->line("MANTENER:  ID $id_keep ({$persona_keep->apellido}, {$persona_keep->nombre})");
        $this->line("           CUIL: {$persona_keep->cuil}");
        if ($user_keep) {
            $this->line("           User: {$user_keep->email} (ID {$user_keep->id})");
        }
        $this->line("");
        $this->line("ELIMINAR:  ID $id_remove ({$persona_remove->apellido}, {$persona_remove->nombre})");
        $this->line("           CUIL: {$persona_remove->cuil}");
        if ($user_remove) {
            $this->line("           User: {$user_remove->email} (ID {$user_remove->id})");
        }
        $this->line("");
        $this->line("IMPACTO:   {$integrantes_remove} integrantes + {$becas_remove} becas se moverán");
        $this->line("           Persona #{$inv_remove->persona_id} se eliminará");
        $this->line("");

        // --- Decidir CUIL destino ---
        if ($cuil_dest === 'preguntar') {
            $choice = $this->choice('¿Qué CUIL mantener?', [
                "{$persona_keep->cuil} (investigador KEEP)",
                "{$persona_remove->cuil} (investigador REMOVE)"
            ]);
            $cuil_final = strpos($choice, 'KEEP') !== false ? $persona_keep->cuil : $persona_remove->cuil;
        } elseif ($cuil_dest === 'remove') {
            $cuil_final = $persona_remove->cuil;
        } else {
            $cuil_final = $persona_keep->cuil;
        }

        $this->warn("CUIL final: $cuil_final");

        // --- Simular o ejecutar ---
        if (!$confirmar) {
            $this->warn("SIMULACIÓN (usa --confirmar para ejecutar)");
            $this->info("Pasos que se ejecutarían:");
            $this->line("1. Backup de tablas afectadas");
            $this->line("2. Actualizar integrantes: investigador_id $id_remove → $id_keep");
            $this->line("3. Actualizar investigador_becas: investigador_id $id_remove → $id_keep");
            $this->line("4. Actualizar pivotar: investigador_id $id_remove → $id_keep");
            $this->line("5. Copiar datos no-vacíos de persona_remove a persona_keep");
            $this->line("6. Actualizar users.cuil si es necesario");
            $this->line("7. Eliminar investigador $id_remove");
            $this->line("8. Eliminar persona #{$inv_remove->persona_id}");
            return 0;
        }

        // --- EJECUTAR CON TRANSACCIÓN ---
        return DB::transaction(function () use ($id_keep, $id_remove, $persona_keep, $persona_remove, $user_keep, $user_remove, $cuil_final) {
            try {
                // 1. Backup
                $this->line("📦 Haciendo backup...");
                $timestamp = date('YmdHis');
                foreach ($this->backup_tables as $table) {
                    $count = DB::table($table)->count();
                    DB::statement("CREATE TABLE {$table}_backup_{$timestamp} LIKE $table");
                    DB::statement("INSERT INTO {$table}_backup_{$timestamp} SELECT * FROM $table");
                    $this->line("   - $table ($count filas)");
                }

                // 2. Actualizar integrantes
                $this->line("🔗 Moviendo integrantes...");
                $count = DB::table('integrantes')
                    ->where('investigador_id', $id_remove)
                    ->update(['investigador_id' => $id_keep]);
                $this->line("   ✓ $count integrantes actualizados");

                // 3. Actualizar investigador_becas
                $this->line("🎓 Moviendo becas...");
                $count = DB::table('investigador_becas')
                    ->where('investigador_id', $id_remove)
                    ->update(['investigador_id' => $id_keep]);
                $this->line("   ✓ $count becas actualizadas");

                // 4. Actualizar pivots
                $this->line("🏛️  Actualizando relaciones...");
                $pivots = [
                    'investigador_titulos',
                    'investigador_tituloposts',
                    'investigador_cargos',
                    'investigador_categorias',
                    'investigador_carreras',
                    'investigador_sicadis',
                ];
                foreach ($pivots as $pivot) {
                    $count = DB::table($pivot)
                        ->where('investigador_id', $id_remove)
                        ->update(['investigador_id' => $id_keep]);
                    if ($count > 0) {
                        $this->line("   - $pivot: $count registros");
                    }
                }

                // 5. Copiar datos no-vacíos de persona_remove a persona_keep
                $this->line("👤 Fusionando datos de persona...");
                $campos_fusion = [
                    'email', 'telefono', 'calle', 'nro', 'piso', 'depto',
                    'localidad', 'cp', 'observaciones', 'tipoDocumento',
                    'documento', 'genero', 'foto', 'fallecimiento'
                ];
                $actualizados = 0;
                $update_data = [];
                foreach ($campos_fusion as $campo) {
                    $valor_keep = $persona_keep->{$campo};
                    $valor_remove = $persona_remove->{$campo};
                    if (($valor_keep === null || $valor_keep === '') && $valor_remove !== null && $valor_remove !== '') {
                        $update_data[$campo] = $valor_remove;
                        $actualizados++;
                    }
                }
                if (!empty($update_data)) {
                    DB::table('personas')
                        ->where('id', $persona_keep->id)
                        ->update($update_data);
                    $this->line("   ✓ $actualizados campos completados");
                }

                // 6. Manejar CUIL en users
                $this->line("🔐 Actualizando acceso de usuarios...");
                if ($cuil_final !== $persona_keep->cuil) {
                    // Cambiar CUIL en persona_keep
                    DB::table('personas')
                        ->where('id', $persona_keep->id)
                        ->update(['cuil' => $cuil_final]);
                    $this->line("   ✓ CUIL persona actualizado: $cuil_final");
                }

                if ($user_keep && $cuil_final !== $user_keep->cuil) {
                    DB::table('users')
                        ->where('id', $user_keep->id)
                        ->update(['cuil' => $cuil_final]);
                    $this->line("   ✓ CUIL usuario actualizado: $cuil_final");
                }

                // 7. Eliminar usuario del investigador removido (si existe y no es usado)
                if ($user_remove) {
                    DB::table('users')->where('id', $user_remove->id)->delete();
                    $this->line("   ✓ Usuario duplicado eliminado: {$user_remove->email}");
                }

                // 8. Eliminar investigador redundante
                $this->line("🗑️  Eliminando investigador redundante...");
                DB::table('investigadors')->where('id', $id_remove)->delete();
                $this->line("   ✓ Investigador $id_remove eliminado");

                // 9. Eliminar persona redundante
                DB::table('personas')->where('id', $persona_remove->id)->delete();
                $this->line("   ✓ Persona #{$persona_remove->id} eliminada");

                $this->info("✅ FUSIÓN COMPLETADA");
                $this->line("Backup disponible: tablas_*_backup_{$timestamp}");
                return 0;

            } catch (\Exception $e) {
                $this->error("❌ ERROR: ".$e->getMessage());
                Log::error("FusionarInvestigadores error", [
                    'id_keep' => $id_keep,
                    'id_remove' => $id_remove,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e; // Rollback automático
            }
        });
    }
}
