<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RepararTitulos extends Command
{
    protected $signature = 'titulos:reparar';
    protected $description = 'Repara títulos huérfanos usando DB externa';

    public function handle()
    {
        $this->info('Buscando títulos huérfanos...');

        // 1. Obtener IDs usados
        $ids = collect()
            ->merge(DB::table('investigadors')->pluck('titulo_id'))
            ->merge(DB::table('investigadors')->pluck('titulopost_id'))
            ->merge(DB::table('integrantes')->pluck('titulo_id'))
            ->merge(DB::table('integrantes')->pluck('titulopost_id'))
            ->merge(DB::table('investigador_titulos')->pluck('titulo_id'))
            ->merge(DB::table('investigador_tituloposts')->pluck('titulo_id'))
            ->filter()
            ->unique();

        // 2. Filtrar inválidos
        $invalidos = $ids->filter(function ($id) {
            return !DB::table('titulos')->where('id', $id)->exists();
        });

        if ($invalidos->isEmpty()) {
            $this->info('No hay huérfanos 🎉');
            return;
        }

        foreach ($invalidos as $id) {

            $this->warn("\n==============================");
            $this->warn("ID huérfano: {$id}");

            // 3. Buscar nombre en DB externa
            $nombreViejo = DB::connection('mysql_testing')
                ->table('titulos')
                ->where('id', $id)
                ->value('nombre');

            if (!$nombreViejo) {
                $this->error("No existe en DB externa ⚠️");
                $nombreViejo = '(desconocido)';
            } else {
                $this->info("Nombre original: {$nombreViejo}");
            }

            // 4. Buscar similares
            $similares = DB::table('titulos')
                ->where('nombre', 'LIKE', "%{$nombreViejo}%")
                ->orWhere('nombre', 'LIKE', "%" . substr($nombreViejo, 0, 5) . "%")
                ->limit(10)
                ->get();

            if ($similares->isNotEmpty()) {
                $this->info("Posibles matches:");
                foreach ($similares as $t) {
                    $this->line("{$t->id} - {$t->nombre}");
                }
            } else {
                $this->warn("No se encontraron similares");
            }

            // 5. Acción
            $accion = $this->choice(
                '¿Qué hacer?',
                ['fusionar', 'crear', 'null', 'skip'],
                0
            );

            if ($accion === 'skip') {
                continue;
            }

            DB::transaction(function () use ($id, $accion, $nombreViejo) {

                $nuevoId = null;

                if ($accion === 'fusionar') {
                    $nuevoId = $this->ask('ID destino');
                }

                if ($accion === 'crear') {
                    $nuevoId = DB::table('titulos')->insertGetId([
                        'nombre' => $nombreViejo
                    ]);

                    $this->info("Creado nuevo título ID {$nuevoId}");
                }

                // === ACTUALIZACIONES ===

                DB::table('investigadors')
                    ->where('titulo_id', $id)
                    ->update(['titulo_id' => $nuevoId]);

                DB::table('investigadors')
                    ->where('titulopost_id', $id)
                    ->update(['titulopost_id' => $nuevoId]);

                DB::table('integrantes')
                    ->where('titulo_id', $id)
                    ->update(['titulo_id' => $nuevoId]);

                DB::table('integrantes')
                    ->where('titulopost_id', $id)
                    ->update(['titulopost_id' => $nuevoId]);

                DB::table('jovens')
                    ->where('titulo_id', $id)
                    ->update(['titulo_id' => $nuevoId]);

                DB::table('jovens')
                    ->where('titulopost_id', $id)
                    ->update(['titulopost_id' => $nuevoId]);

                DB::table('viajes')
                    ->where('titulo_id', $id)
                    ->update(['titulo_id' => $nuevoId]);

                // Pivot simples (sin lógica anti-duplicado por ahora)
                DB::table('investigador_titulos')
                    ->where('titulo_id', $id)
                    ->update(['titulo_id' => $nuevoId]);

                DB::table('investigador_tituloposts')
                    ->where('titulo_id', $id)
                    ->update(['titulo_id' => $nuevoId]);
            });

            $this->info("✔ Procesado ID {$id}");
        }

        $this->info("\nProceso finalizado 🚀");
    }
}
