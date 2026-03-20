<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DetectarInvestigadoresSimilares extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'detect:investigadores-similares';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Detecta investigadores con nombre y apellido similar';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $letra = $this->option('desde');

        $investigadores = DB::table('investigadors as i')
            ->join('personas as p', 'p.id', '=', 'i.persona_id')
            ->select('i.id', 'p.apellido', 'p.nombre', 'p.documento');

        if ($letra) {
            $investigadores->where('p.apellido', 'LIKE', "$letra%");
        }

        $investigadores = $investigadores->orderBy('p.apellido')->get();

        $usados = [];

        foreach ($investigadores as $inv1) {

            if (in_array($inv1->id, $usados)) {
                continue;
            }

            $similares = collect();

            foreach ($investigadores as $inv2) {

                if ($inv1->id == $inv2->id) {
                    continue;
                }

                if (in_array($inv2->id, $usados)) {
                    continue;
                }

                if ($this->esSimilar($inv1, $inv2)) {
                    $similares->push($inv2);
                }
            }

            if ($similares->count()) {

                $this->warn("\nPosibles duplicados:");

                $this->line("{$inv1->id} - {$inv1->apellido}, {$inv1->nombre} - DNI: {$inv1->documento}");

                foreach ($similares as $s) {
                    $this->line("{$s->id} - {$s->apellido}, {$s->nombre} - DNI: {$s->documento}");
                }

                if ($this->confirm('¿Querés fusionarlos?')) {

                    $ids = collect([$inv1->id])
                        ->merge($similares->pluck('id'))
                        ->toArray();

                    $mantener = $this->choice(
                        '¿Qué ID querés conservar?',
                        $ids
                    );

                    foreach ($ids as $id) {

                        if ($id != $mantener) {

                            $this->fusionarInvestigadores($mantener, $id);

                            $this->info("Investigador {$id} fusionado");
                        }
                    }
                }

                $usados = array_merge($usados, $similares->pluck('id')->toArray());
                $usados[] = $inv1->id;

                $this->line('--------------------------');
            }
        }

        $this->info('Proceso finalizado');
    }

    private function esSimilar($a, $b)
    {
        $ape1 = strtolower($a->apellido);
        $ape2 = strtolower($b->apellido);

        $nom1 = strtolower($a->nombre);
        $nom2 = strtolower($b->nombre);

        similar_text($ape1, $ape2, $simApe);
        similar_text($nom1, $nom2, $simNom);

        return $simApe > 85 && $simNom > 85;
    }

    private function fusionarInvestigadores($mantener, $eliminar)
    {
        DB::transaction(function () use ($mantener, $eliminar) {

            // =========================
            // HIJOS DIRECTOS
            // =========================

            DB::table('jovens')
                ->where('investigador_id', $eliminar)
                ->update(['investigador_id' => $mantener]);

            DB::table('viajes')
                ->where('investigador_id', $eliminar)
                ->update(['investigador_id' => $mantener]);

            $this->mergePivot(
                'integrantes',
                $mantener,
                $eliminar,
                ['proyecto_id']
            );

            $this->mergePivot(
                'investigador_becas',
                $mantener,
                $eliminar,
                ['institucion', 'beca', 'desde', 'hasta']
            );




            // =========================
            // PIVOT TITULOS
            // =========================

            $this->mergePivot('investigador_titulos', $mantener, $eliminar, ['titulo_id']);


            // =========================
            // PIVOT TITULOPOST
            // =========================

            $this->mergePivot('investigador_tituloposts', $mantener, $eliminar, ['titulopost_id']);

            // =========================
            // CARGOS
            // =========================

            $this->mergePivot('investigador_cargos', $mantener, $eliminar, ['cargo_id']);

            // =========================
            // CARRERA INVESTIGADOR
            // =========================

            $this->mergePivot('investigador_carreras', $mantener, $eliminar, ['carrerainv_id']);

            // =========================
            // CATEGORIAS
            // =========================

            $this->mergePivot('investigador_categorias', $mantener, $eliminar, ['categoria_id', 'year']);

            // =========================
            // SICADI
            // =========================

            $this->mergePivot('investigador_sicadis', $mantener, $eliminar, ['sicadi_id']);


            // =========================
            // FINAL
            // =========================

            // =========================
// PERSONA (antes de eliminar investigador)
// =========================

// Obtener persona_id del investigador a eliminar
            $persona = DB::table('investigadors')
                ->where('id', $eliminar)
                ->value('persona_id');

// Verificar si esa persona está en uso por otro investigador
            $enUso = DB::table('investigadors')
                ->where('persona_id', $persona)
                ->where('id', '!=', $eliminar)
                ->exists();

// =========================
// FINAL
// =========================

// Eliminar investigador
            DB::table('investigadors')
                ->where('id', $eliminar)
                ->delete();

// Si la persona no está en uso → eliminarla
            if (!$enUso && $persona) {
                DB::table('personas')
                    ->where('id', $persona)
                    ->delete();
            }

        });
    }

    private function mergePivot($table, $mantener, $eliminar, $keys)
    {
        $rows = DB::table($table)
            ->where('investigador_id', $eliminar)
            ->get();

        foreach ($rows as $row) {

            $query = DB::table($table)
                ->where('investigador_id', $mantener);

            foreach ($keys as $key) {
                $query->where($key, $row->$key);
            }

            $exists = $query->exists();

            $deleteQuery = DB::table($table)
                ->where('investigador_id', $eliminar);

            foreach ($keys as $key) {
                $deleteQuery->where($key, $row->$key);
            }

            if ($exists) {
                // ya existe → eliminar duplicado
                $deleteQuery->delete();
            } else {
                // no existe → mover
                $deleteQuery->update(['investigador_id' => $mantener]);
            }
        }
    }
}
