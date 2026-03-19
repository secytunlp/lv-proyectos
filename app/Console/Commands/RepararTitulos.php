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

        $ids = collect()
            ->merge(DB::table('investigadors')->pluck('titulo_id'))
            ->merge(DB::table('investigadors')->pluck('titulopost_id'))
            ->merge(DB::table('integrantes')->pluck('titulo_id'))
            ->merge(DB::table('integrantes')->pluck('titulopost_id'))
            ->merge(DB::table('investigador_titulos')->pluck('titulo_id'))
            ->merge(DB::table('investigador_tituloposts')->pluck('titulo_id'))
            ->filter(function ($v) {
                return !is_null($v);
            })
            ->unique();

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

            // DB externa
            $tituloViejo = DB::connection('mysql_testing')
                ->table('titulos as t')
                ->join('universidads as u', 'u.id', '=', 't.universidad_id')
                ->where('t.id', $id)
                ->select('t.nombre as titulo', 'u.nombre as universidad', 't.universidad_id')
                ->first();

            if (!$tituloViejo) {
                $this->error("No existe en DB externa ⚠️");
                continue;
            }

            $this->info("Título: {$tituloViejo->titulo}");
            $this->info("Universidad: {$tituloViejo->universidad}");

            // 🔥 MATCHING REAL (tu lógica)
            $titulos = DB::table('titulos')
                ->where('universidad_id', $tituloViejo->universidad_id)
                ->get();

            $nombreBase = $this->limpiarTitulo($tituloViejo->titulo);

            $matches = [];

            foreach ($titulos as $t) {

                $nombreActual = $this->limpiarTitulo($t->nombre);

                // orientación (tu lógica)
                if (str_contains($nombreBase, 'orientacion') && str_contains($nombreActual, 'orientacion')) {

                    $o1 = trim(explode('orientacion', $nombreBase)[1]);
                    $o2 = trim(explode('orientacion', $nombreActual)[1]);

                    if ($o1 !== $o2) {
                        continue;
                    }
                }

                similar_text($nombreBase, $nombreActual, $porcentaje);

                if ($porcentaje >= 70) {
                    $matches[] = [
                        'id' => $t->id,
                        'nombre' => $t->nombre,
                        'score' => $porcentaje
                    ];
                }
            }

            if (empty($matches)) {

                $this->warn("No se encontraron similares en esta universidad ⚠️");

                // 👇 igual seguimos, pero sin opción "fusionar"
                $accion = $this->choice(
                    '¿Qué hacer?',
                    ['fusionar manual', 'crear', 'null'],
                    0
                );

            } else {


                // ordenar por mejor match
                usort($matches, function ($a, $b) {
                    return $b['score'] <=> $a['score'];
                });

                $this->info("Posibles matches:");

                foreach ($matches as $m) {
                    $this->line("[{$m['id']}] {$m['nombre']} ({$m['score']}%)");
                }

                // acción
                $accion = $this->choice(
                    '¿Qué hacer?',
                    ['fusionar', 'fusionar manual', 'crear', 'null', 'skip'],
                    0
                );
            }

            if ($accion === 'skip') {
                continue;
            }

            DB::transaction(function () use ($id, $accion, $tituloViejo, $matches) {

                $nuevoId = null;

                if ($accion === 'fusionar') {
                    $opciones = array_column($matches, 'id');
                    $nuevoId = $this->choice('Elegí ID destino', $opciones);
                }

                if ($accion === 'fusionar manual') {
                    $nuevoId = $this->ask('ID destino manual');
                }

                if ($accion === 'crear') {
                    $nuevoId = DB::table('titulos')->insertGetId([
                        'nombre' => $tituloViejo->titulo,
                        'universidad_id' => $tituloViejo->universidad_id
                    ]);

                    $this->info("Creado nuevo título ID {$nuevoId}");
                }

                // VALIDACIÓN
                if (!is_null($nuevoId) && !DB::table('titulos')->where('id', $nuevoId)->exists()) {
                    $this->error("El ID {$nuevoId} no existe ❌");
                    return;
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

    // 👇 reutilizás tu lógica
    private function limpiarTitulo($texto)
    {
        $texto = strtolower($texto);

        // quitar acentos
        $texto = iconv('UTF-8', 'ASCII//TRANSLIT', $texto);

        // reemplazos importantes
        $texto = str_replace('/', ' ', $texto);
        $texto = str_replace(['.', ',', '-'], ' ', $texto);

        // 🔥 normalizar variantes de licenciado
        $texto = str_replace([
            'licenciatura',
            'licenciado',
            'licenciada',
            'lic'
        ], '', $texto);

        // otros títulos
        $texto = str_replace([
            'profesor',
            'profesora',
            'prof',
            'doctor',
            'doctora',
            'magister',
            'ingenieria',
            'ingeniero',
            'ingeniera',
            'tecnicatura',
            'tecnico',
            'tecnica'
        ], '', $texto);

        // palabras basura
        $texto = str_replace([' en ', ' y ', ' de '], ' ', $texto);

        // espacios múltiples
        $texto = preg_replace('/\s+/', ' ', $texto);

        return trim($texto);
    }
}
