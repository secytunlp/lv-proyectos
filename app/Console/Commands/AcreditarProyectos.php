<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AcreditarProyectos extends Command
{
    /**
     * Command signature and description.
     *
     * Usage:
     *   php artisan proyectos:acreditar
     *   php artisan proyectos:acreditar --dry-run
     *
     * Replace storage/app/proyectos/codigos_proyectos.csv each year before running.
     * CSV format (no header, semicolon-delimited): codigo ; tipo ; sigeva
     */
    protected $signature = 'proyectos:acreditar
                            {--dry-run : Preview changes without writing to the database}';

    protected $description = 'Yearly accreditation process: reads codigos_proyectos.csv, updates codigo/estado in proyectos, closes the previous estado (hasta = today), and inserts a full snapshot ProyectoEstado with comentarios = Codificado.';

    /** Path inside storage/app — replace the file each year, keep the name. */
    private const CSV_PATH = 'public/proyectos/codigos_proyectos.csv';

    /**
     * Fields copied from proyectos into the new proyecto_estados snapshot.
     * Matches the $fillable on ProyectoEstado minus proyecto_id, desde, hasta, comentarios
     * (those are set explicitly by this command).
     */
    private const SNAPSHOT_FIELDS = [
        'estado', 'tipo', 'sigeva', 'titulo', 'inicio', 'fin',
        'facultad_id', 'duracion', 'unidad_id', 'campo_id',
        'disciplina_id', 'especialidad_id', 'investigacion', 'linea',
        'resumen', 'clave1', 'clave2', 'clave3', 'clave4', 'clave5', 'clave6',
        'key1', 'key2', 'key3', 'key4', 'key5', 'key6',
    ];

    public function handle(): int
    {
        $dryRun  = $this->option('dry-run');
        $csvPath = Storage::path(self::CSV_PATH);

        // ── 1. Validate CSV exists ────────────────────────────────────────────
        if (!file_exists($csvPath)) {
            $this->error("CSV not found at: {$csvPath}");
            $this->line('Expected location: storage/app/' . self::CSV_PATH);
            return self::FAILURE;
        }

        // ── 2. Parse CSV ──────────────────────────────────────────────────────
        $rows = $this->parseCsv($csvPath);

        if (empty($rows)) {
            $this->error('CSV is empty or could not be parsed.');
            return self::FAILURE;
        }

        $this->info(sprintf('Loaded %d rows from CSV.', count($rows)));

        if ($dryRun) {
            $this->warn('DRY-RUN mode — no changes will be written.');
        }

        // ── 3. Process inside a single transaction ────────────────────────────
        $today     = now()->toDateString(); // YYYY-MM-DD
        $processed = 0;
        $skipped   = 0;
        $warnings  = [];

        DB::beginTransaction();

        try {
            $bar = $this->output->createProgressBar(count($rows));
            $bar->start();

            foreach ($rows as $index => $row) {
                $codigo = trim($row['codigo']);
                $sigeva = trim($row['sigeva']);
                $line   = $index + 1;

                if (empty($codigo) || empty($sigeva)) {
                    $warnings[] = "Line {$line}: empty codigo or sigeva — skipped.";
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                // Find proyecto by sigeva
                $proyecto = DB::table('proyectos')
                    ->where('sigeva', $sigeva)
                    ->first();

                if (!$proyecto) {
                    $warnings[] = "Line {$line}: sigeva '{$sigeva}' not found in proyectos — skipped.";
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                if (!$dryRun) {
                    // 3a. Close the current open estado (hasta IS NULL)
                    DB::table('proyecto_estados')
                        ->where('proyecto_id', $proyecto->id)
                        ->whereNull('hasta')
                        ->update([
                            'hasta'      => $today,
                            'updated_at' => now(),
                        ]);

                    // 3b. Build snapshot from current proyecto fields
                    $snapshot = [];
                    foreach (self::SNAPSHOT_FIELDS as $field) {
                        $snapshot[$field] = $proyecto->{$field} ?? null;
                    }

                    // 3c. Update proyectos: new codigo and estado = Acreditado
                    DB::table('proyectos')
                        ->where('id', $proyecto->id)
                        ->update([
                            'codigo'     => $codigo,
                            'estado'     => 'Acreditado',
                            'updated_at' => now(),
                        ]);

                    // 3d. Insert new ProyectoEstado with full snapshot
                    // Override snapshot fields that change with this accreditation
                    DB::table('proyecto_estados')->insert(array_merge($snapshot, [
                        'proyecto_id'  => $proyecto->id,
                        'codigo'       => $codigo,       // new codigo from CSV
                        'estado'       => 'Acreditado',  // override snapshot estado
                        'comentarios'  => 'Codificado',
                        'desde'        => $today,
                        'hasta'        => null,
                        'user_id'      => 2,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]));
                }

                $processed++;
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();

            if ($dryRun) {
                DB::rollBack();
                $this->warn('DRY-RUN: transaction rolled back. Nothing was saved.');
            } else {
                DB::commit();
                $this->info('Transaction committed successfully.');
            }

        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Unexpected error — transaction rolled back.');
            $this->error($e->getMessage());
            return self::FAILURE;
        }

        // ── 4. Summary ────────────────────────────────────────────────────────
        $this->newLine();
        $this->table(
            ['Result', 'Count'],
            [
                ['Processed', $processed],
                ['Skipped',   $skipped],
                ['Total rows', count($rows)],
            ]
        );

        if (!empty($warnings)) {
            $this->newLine();
            $this->warn('Warnings / skipped rows:');
            foreach ($warnings as $msg) {
                $this->line("  • {$msg}");
            }
        }

        return self::SUCCESS;
    }

    /**
     * Parse the semicolon-delimited CSV.
     * Expected columns (no header row): codigo ; tipo ; sigeva
     *
     * @return array<int, array{codigo: string, tipo: string, sigeva: string}>
     */
    private function parseCsv(string $path): array
    {
        $rows   = [];
        $handle = fopen($path, 'r');

        if ($handle === false) {
            return [];
        }

        while (($fields = fgetcsv($handle, 1000, ';')) !== false) {
            if (count($fields) < 3) {
                continue; // Skip malformed or empty lines
            }

            $rows[] = [
                'codigo' => $fields[0],
                'tipo'   => $fields[1],
                'sigeva' => $fields[2],
            ];
        }

        fclose($handle);

        return $rows;
    }
}
