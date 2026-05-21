<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Universidad;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class DetectarUniversidadesSimilares extends Command
{
    protected $signature = 'detect:universidades {--percent=70} {--dry-run}';
    protected $description = 'Detecta universidades con nombres similares';

    public function handle()
    {
        $universidades = Universidad::all();
        $threshold = (int) $this->option('percent');
        $dryRun = (bool) $this->option('dry-run');

        foreach ($universidades as $u1) {

            foreach ($universidades as $u2) {

                if ($u1->id >= $u2->id) {
                    continue;
                }

                $motivo = $this->coincide($u1, $u2, $threshold);

                if ($motivo === null) {
                    continue;
                }

                $this->warn("\nCandidato: {$motivo}");
                $this->line("{$u1->id} - {$u1->nombre}");
                $this->line("{$u2->id} - {$u2->nombre}");

                if ($dryRun) {
                    $this->line("--------------------------------");
                    continue;
                }

                if ($this->confirm('¿Fusionar estas universidades?')) {

                    $opciones = [
                        "{$u1->id} - {$u1->nombre}",
                        "{$u2->id} - {$u2->nombre}",
                    ];

                    $seleccion = $this->choice('¿Con cuál querés quedarte?', $opciones);

                    $mantener = str_starts_with($seleccion, (string) $u1->id) ? $u1->id : $u2->id;
                    $eliminar = $mantener == $u1->id ? $u2->id : $u1->id;

                    $this->fusionarUniversidades($mantener, $eliminar);

                    $this->info('Fusionadas correctamente.');
                }

                $this->line("--------------------------------");
            }
        }

        $this->info("\nDetección finalizada.");
    }

    /**
     * Decide whether two universities are candidates for merging.
     * Returns a human-readable reason, or null if they don't match.
     * Broad on purpose: prefer false positives the user can discard.
     */
    private function coincide($u1, $u2, int $threshold): ?string
    {
        // 1) Same acronym
        $sigla1 = $this->extraerSigla($u1->nombre);
        $sigla2 = $this->extraerSigla($u2->nombre);

        if ($sigla1 && $sigla2 && $sigla1 === $sigla2) {
            return "sigla coincidente ({$sigla1})";
        }

        // Significant words only (drops "universidad", "nacional", "de", etc.)
        $claves1 = $this->palabrasClave($u1->nombre);
        $claves2 = $this->palabrasClave($u2->nombre);

        // 2) Text similarity over the SIGNIFICANT part of the name only,
        //    so a shared "universidad de" prefix doesn't inflate the score.
        $nucleo1 = implode(' ', $claves1);
        $nucleo2 = implode(' ', $claves2);

        if ($nucleo1 !== '' && $nucleo2 !== '') {
            similar_text($nucleo1, $nucleo2, $percent);
            if ($percent >= $threshold) {
                return 'similitud ' . round($percent) . '%';
            }
        }

        // 3) Shared significant words (e.g. both mention "plata")
        $comunes = array_intersect($claves1, $claves2);

        if (!empty($comunes)) {
            return 'palabras en comun (' . implode(', ', $comunes) . ')';
        }

        return null;
    }

    /**
     * Extract acronym: from parentheses "(UNLP)" or a bare all-caps token.
     */
    private function extraerSigla($texto): ?string
    {
        if (preg_match('/\(([^)]+)\)/', $texto, $m)) {
            return strtolower(trim($m[1]));
        }

        $limpio = trim($texto);
        if (!str_contains($limpio, ' ') && mb_strlen($limpio) <= 8) {
            return strtolower($limpio);
        }

        return null;
    }

    /**
     * Significant words remaining after removing filler and generic terms.
     */
    private function palabrasClave($texto): array
    {
        $texto = $this->normalizar($texto);

        $stopwords = [
            'universidad', 'nacional', 'de', 'del', 'la', 'las', 'los', 'y',
            'faculty', 'facultad', 'instituto', 'superior', 'formacion',
            'docente', 'licenciada', 'licenciado', 'en',
        ];

        $palabras = array_filter(
            explode(' ', $texto),
            fn ($p) => $p !== '' && mb_strlen($p) > 2 && !in_array($p, $stopwords, true)
        );

        return array_values(array_unique($palabras));
    }

    private function normalizar($texto): string
    {
        $texto = Str::ascii($texto);
        $texto = strtolower($texto);

        // Remove parenthetical acronyms so they don't pollute the name
        $texto = preg_replace('/\([^)]*\)/', ' ', $texto);

        // Keep only letters/numbers/spaces
        $texto = preg_replace('/[^a-z0-9 ]/', ' ', $texto);

        // Collapse spaces
        return trim(preg_replace('/\s+/', ' ', $texto));
    }

    private function fusionarUniversidades($mantener, $eliminar): void
    {
        // Tables to reassign, with the columns (besides universidad_id) that
        // make a row unique. If a row in $eliminar would collide with an
        // existing row in $mantener on these columns, it's a duplicate and
        // gets deleted instead of reassigned.
        $tablas = [
            'investigadors'           => [],
            'titulos'                 => ['nombre', 'nivel'],
            'integrantes'             => [],
            'investigador_cargos'     => [],
            'investigador_categorias' => [],
        ];

        DB::transaction(function () use ($mantener, $eliminar, $tablas) {

            foreach ($tablas as $tabla => $clavesUnicas) {

                if (!empty($clavesUnicas)) {
                    // Delete rows in $eliminar that already have an equivalent
                    // row in $mantener (would violate the unique index).
                    DB::table("{$tabla} as e")
                        ->where('e.universidad_id', $eliminar)
                        ->whereExists(function ($q) use ($tabla, $mantener, $clavesUnicas) {
                            $q->select(DB::raw(1))
                                ->from("{$tabla} as m")
                                ->where('m.universidad_id', $mantener);

                            foreach ($clavesUnicas as $col) {
                                $q->whereColumn("m.{$col}", "e.{$col}");
                            }
                        })
                        ->delete();
                }

                // Reassign the remaining (non-colliding) rows.
                DB::table($tabla)
                    ->where('universidad_id', $eliminar)
                    ->update(['universidad_id' => $mantener]);
            }

            DB::table('universidads')
                ->where('id', $eliminar)
                ->delete();
        });
    }
}
