<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Proyecto;
use App\Models\Integrante;

class ExportarCsvBuscador extends Command
{
    /**
     * Usage:
     *   php artisan exportar:csv-buscador
     *   php artisan exportar:csv-buscador --modo=particulares
     *   php artisan exportar:csv-buscador --modo=particulares --codigos="11/H1050,11/H1053,PPID/J007"
     */
    protected $signature = 'exportar:csv-buscador
                            {--modo=activos : "activos" (fin > today, estado=Acreditado) or "particulares" (specific codes)}
                            {--codigos= : Comma-separated project codes for modo=particulares}
                            {--dir= : Output directory (default: storage/app/buscador)}';

    protected $description = 'Exports projects, keywords, people and participations as CSV files for Buscador import';

    // Proyecto estado that maps to the legacy cd_estado = 5 ("Acreditado")
    private const ESTADO_PROYECTO_ACTIVO = 'Acreditado';

    // Integrante estados considered valid for participations export.
    // Maps to legacy cd_estado IN (3, 4, 5, 8, 9):
    //   3 → '' (unnamed / blank in legacy table)
    //   4 → 'Baja Creada'
    //   5 → 'Baja Recibida'
    //   8 → 'Cambio Hs. Creado'
    //   9 → 'Cambio Hs. Recibido'
    private const ESTADOS_INTEGRANTE_VALIDOS = [
        '',                    // 3
        'Baja Creada',         // 4
        'Baja Recibida',       // 5
        'Cambio Hs. Creado',   // 8
        'Cambio Hs. Recibido', // 9
    ];

    public function handle(): int
    {
        $modo   = $this->option('modo');
        $outDir = $this->option('dir') ?: storage_path('app/buscador');

        if (!is_dir($outDir)) {
            mkdir($outDir, 0755, true);
        }

        // Resolve the set of project IDs to work with
        $proyectoIds = $this->resolveProyectoIds($modo);

        if ($proyectoIds->isEmpty()) {
            $this->warn('No projects found for the given criteria.');
            return self::FAILURE;
        }

        $this->info("Found {$proyectoIds->count()} project(s). Generating CSVs in: {$outDir}");

        $this->exportProjects($proyectoIds, $outDir);
        $this->exportKeywords($proyectoIds, $outDir);
        $this->exportPeople($proyectoIds, $outDir);
        $this->exportParticipations($proyectoIds, $outDir);

        $this->info('Done.');
        return self::SUCCESS;
    }

    // -------------------------------------------------------------------------
    // Project ID resolution
    // -------------------------------------------------------------------------

    private function resolveProyectoIds(string $modo)
    {
        $query = Proyecto::query();

        if ($modo === 'particulares') {
            $codigos = $this->resolveCodigosParticulares();
            if (empty($codigos)) {
                $this->error('--modo=particulares requires --codigos="CODE1,CODE2,..."');
                exit(self::FAILURE);
            }
            $query->whereIn('codigo', $codigos);
        } else {
            // Default: active projects not yet finished (legacy cd_estado = 5 → 'Acreditado')
            $query->where('fin', '>', now()->toDateString())
                ->where('estado', self::ESTADO_PROYECTO_ACTIVO);
        }

        return $query->pluck('id');
    }

    private function resolveCodigosParticulares(): array
    {
        $raw = $this->option('codigos') ?? '';
        if (!$raw) {
            return [];
        }
        return array_unique(array_map('trim', explode(',', $raw)));
    }

    // -------------------------------------------------------------------------
    // projects.csv
    // Columns: code, title, start_date, end_date, research_line, research_type,
    //          abstract, agreement_type_id, discipline_id, sub_discipline_id,
    //          research_unit_id, application_field_id, academic_unit_id
    // -------------------------------------------------------------------------

    private function exportProjects($proyectoIds, string $outDir): void
    {
        $this->info('  → projects.csv');

        $rows = Proyecto::with('unidad')
            ->whereIn('id', $proyectoIds)
            ->get()
            ->map(function (Proyecto $p) {
                // Build research_unit_id: "Nombre (Sigla)" or just "Nombre" when sigla is empty
                $unidadLabel = '';
                if ($p->unidad) {
                    $sigla = trim($p->unidad->sigla ?? '');
                    $nombre = $this->sanitize($p->unidad->nombre ?? '');
                    $unidadLabel = $sigla !== ''
                        ? "{$nombre} ({$sigla})"
                        : $nombre;
                }

                return [
                    'code'               => $p->codigo,
                    'title'              => $this->sanitize($p->titulo),
                    'start_date'         => $p->inicio,
                    'end_date'           => $p->fin,
                    'research_line'      => $this->sanitize($p->linea),
                    'research_type'      => $p->tipo,
                    'abstract'           => $this->sanitize($p->resumen),
                    'agreement_type_id'  => 1219,
                    'discipline_id'      => $p->disciplina_id,
                    'sub_discipline_id'  => $p->especialidad_id,
                    'research_unit_id'   => $unidadLabel,
                    'application_field_id' => $p->campo_id,
                    'academic_unit_id'   => $p->facultad_id,
                ];
            });

        $this->writeCsv($outDir . '/projects.csv', $rows->toArray());
    }

    // -------------------------------------------------------------------------
    // keywords.csv
    // Columns: ds_codigo, ds_clave1 ... ds_clave6
    // -------------------------------------------------------------------------

    private function exportKeywords($proyectoIds, string $outDir): void
    {
        $this->info('  → keywords.csv');

        $rows = Proyecto::whereIn('id', $proyectoIds)
            ->get()
            ->map(function (Proyecto $p) {
                return [
                    'ds_codigo' => $p->codigo,
                    'ds_clave1' => $p->clave1,
                    'ds_clave2' => $p->clave2,
                    'ds_clave3' => $p->clave3,
                    'ds_clave4' => $p->clave4,
                    'ds_clave5' => $p->clave5,
                    'ds_clave6' => $p->clave6,
                ];
            });

        $this->writeCsv($outDir . '/keywords.csv', $rows->toArray());
    }

    // -------------------------------------------------------------------------
    // people.csv
    // Columns: first_name, last_name, gender, birth_date, document_type_id,
    //          document_number, cuil
    // Distinct by document_number (mirrors the SQL DISTINCT).
    // -------------------------------------------------------------------------

    private function exportPeople($proyectoIds, string $outDir): void
    {
        $this->info('  → people.csv');

        $rows = Integrante::with('investigador.persona')
            ->whereIn('proyecto_id', $proyectoIds)
            ->whereIn('estado', self::ESTADOS_INTEGRANTE_VALIDOS)
            ->get()
            ->map(function (Integrante $i) {
                $investigador = $i->investigador;
                $persona = $investigador ? $investigador->persona : null;
                if (!$persona) {
                    return null;
                }

                return [
                    'document_number'  => $persona->documento,
                    'first_name'       => $persona->nombre,
                    'last_name'        => $persona->apellido,
                    'gender'           => $persona->genero,
                    'birth_date'       => $persona->nacimiento,
                    'document_type_id' => 'DNI',
                    'cuil'             => $persona->cuil,
                ];
            })
            ->filter()
            // Deduplicate by document_number (same as SQL DISTINCT on the join)
            ->unique('document_number')
            ->values()
            ->map(function ($r) {
                return collect($r)->toArray();
            });

        $this->writeCsv($outDir . '/people.csv', $rows->toArray());
    }

    // -------------------------------------------------------------------------
    // participations.csv
    // Columns: code, document_number, cd_tipoinvestigador, alta, baja
    //
    // Filters by integrante.estado directly — it already holds the current state.
    // -------------------------------------------------------------------------

    private function exportParticipations($proyectoIds, string $outDir): void
    {
        $this->info('  → participations.csv');

        $integrantes = Integrante::with(['proyecto', 'investigador.persona'])
            ->whereIn('proyecto_id', $proyectoIds)
            ->whereIn('estado', self::ESTADOS_INTEGRANTE_VALIDOS)
            ->get();

        $rows = $integrantes->map(function (Integrante $i) {
            $investigador = $i->investigador;
            $persona = $investigador ? $investigador->persona : null;
            if (!$persona) {
                return null;
            }

            $alta = ($i->alta === '0000-00-00' || !$i->alta) ? '' : $i->alta;
            $baja = ($i->baja === '0000-00-00' || !$i->baja) ? '' : $i->baja;

            return [
                'code'                => $i->proyecto ? $i->proyecto->codigo : '',
                'document_number'     => $persona->documento,
                'cd_tipoinvestigador' => $i->tipo,
                'alta'                => $alta,
                'baja'                => $baja,
            ];
        })->filter()->values();

        $this->writeCsv($outDir . '/participations.csv', $rows->toArray());
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Strips \r\n sequences from a string (mirrors SQL REPLACE(field,'\r\n',' ')).
     */
    private function sanitize(?string $value): string
    {
        if ($value === null) {
            return '';
        }
        return str_replace(["\r\n", "\r", "\n"], ' ', $value);
    }

    /**
     * Writes an array of associative rows to a CSV file.
     * Uses the keys of the first row as the header row.
     */
    private function writeCsv(string $path, array $rows): void
    {
        if (empty($rows)) {
            $this->warn("    No rows — writing empty file: {$path}");
            file_put_contents($path, '');
            return;
        }

        $handle = fopen($path, 'w');
        // UTF-8 BOM so Excel opens it correctly
        fwrite($handle, "\xEF\xBB\xBF");

        // Header
        fputcsv($handle, array_keys($rows[0]));

        foreach ($rows as $row) {
            fputcsv($handle, array_values($row));
        }

        fclose($handle);
        $this->line("    Written: {$path} (" . count($rows) . ' rows)');
    }
}
