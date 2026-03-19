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
        $investigadores = DB::table('investigadors as i')
            ->join('personas as p', 'p.id', '=', 'i.persona_id')
            ->select('i.id','p.apellido','p.nombre','p.documento')
            ->orderBy('p.apellido')
            ->get();

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

            DB::table('integrantes')
                ->where('investigador_id', $eliminar)
                ->update(['investigador_id' => $mantener]);

            DB::table('investigador_becas')
                ->where('investigador_id', $eliminar)
                ->update(['investigador_id' => $mantener]);


            // =========================
            // TITULO GRADO FK
            // =========================

            DB::table('investigadors')
                ->where('titulo_id', $eliminar)
                ->update(['titulo_id' => $mantener]);

            DB::table('investigadors')
                ->where('titulopost_id', $eliminar)
                ->update(['titulopost_id' => $mantener]);


            // =========================
            // PIVOT TITULOS
            // =========================

            $pivots = DB::table('investigador_titulos')
                ->where('investigador_id', $eliminar)
                ->get();

            foreach ($pivots as $pivot) {

                $exists = DB::table('investigador_titulos')
                    ->where('investigador_id', $mantener)
                    ->where('titulo_id', $pivot->titulo_id)
                    ->exists();

                if ($exists) {

                    DB::table('investigador_titulos')
                        ->where('investigador_id', $eliminar)
                        ->where('titulo_id', $pivot->titulo_id)
                        ->delete();

                } else {

                    DB::table('investigador_titulos')
                        ->where('investigador_id', $eliminar)
                        ->where('titulo_id', $pivot->titulo_id)
                        ->update(['investigador_id' => $mantener]);
                }
            }


            // =========================
            // PIVOT TITULOPOST
            // =========================

            DB::table('investigador_tituloposts')
                ->where('investigador_id', $eliminar)
                ->update(['investigador_id' => $mantener]);


            // =========================
            // CARGOS
            // =========================

            DB::table('investigador_cargos')
                ->where('investigador_id', $eliminar)
                ->update(['investigador_id' => $mantener]);

            // =========================
            // CARRERA INVESTIGADOR
            // =========================

            DB::table('investigador_carreras')
                ->where('investigador_id', $eliminar)
                ->update(['investigador_id' => $mantener]);

            // =========================
            // CATEGORIAS
            // =========================

            DB::table('investigador_categorias')
                ->where('investigador_id', $eliminar)
                ->update(['investigador_id' => $mantener]);

            // =========================
            // SICADI
            // =========================

            DB::table('investigador_sicadis')
                ->where('investigador_id', $eliminar)
                ->update(['investigador_id' => $mantener]);


            // =========================
            // FINAL
            // =========================

            DB::table('investigadors')
                ->where('id', $eliminar)
                ->delete();

        });
    }
}
