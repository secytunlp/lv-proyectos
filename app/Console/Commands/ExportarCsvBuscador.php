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
                        {--modo=activos : "activos", "particulares" o "por_inicio"}
                        {--codigos= : Comma-separated project codes for modo=particulares}
                        {--fecha-inicio= : Start date (Y-m-d) for modo=por_inicio}
                        {--dir= : Output directory (default: storage/app/buscador)}';

    protected $description = 'Exports projects, keywords, people and participations as CSV files for Buscador import';

    // Proyecto estado that maps to the legacy cd_estado = 5 ("Acreditado")
    private const ESTADO_PROYECTO_ACTIVO = 'Acreditado';

    // Integrante estados considered valid for participations export.
    // Maps to legacy cd_estado IN (3, 4, 5, 8, 9):
    //   3 → '' or NULL (unnamed / blank in legacy table)
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

    // Maps integrante.tipo (string) to the legacy cd_tipoinvestigador integer
// expected by the Buscador import.
    private const TIPO_INVESTIGADOR_MAP = [
        'DIRECTOR'                  => 1,
        'CODIRECTOR'                => 2,
        'INVESTIGADOR FORMADO'      => 3,
        'INVESTIGADOR EN FORMACIÓN' => 4,  // ← con tilde, igual que la base
        'BECARIO, TESISTA'          => 5,
        'COLABORADOR'               => 6,
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
        } elseif ($modo === 'por_inicio') {
            $fechaInicio = $this->option('fecha-inicio');
            if (!$fechaInicio) {
                $this->error('--modo=por_inicio requires --fecha-inicio="YYYY-MM-DD"');
                exit(self::FAILURE);
            }
            $query->where('inicio', $fechaInicio);
        } else {
            // Default: active projects not yet finished
            $query->where('fin', '>', now()->toDateString());
        }

        // Always export only "Acreditado" projects, regardless of mode
        $query->where('estado', self::ESTADO_PROYECTO_ACTIVO);

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
                    'start_date'         => $this->formatDate($p->inicio),
                    'end_date'           => $this->formatDate($p->fin),
                    'research_line'      => $this->sanitize($p->linea),
                    'research_type'      => $this->firstLetter($p->investigacion),
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
            ->where(function ($q) {
                $q->whereIn('estado', self::ESTADOS_INTEGRANTE_VALIDOS)
                    ->orWhereNull('estado');
            })
            ->get()
            ->map(function (Integrante $i) {
                $investigador = $i->investigador;
                $persona = $investigador ? $investigador->persona : null;
                if (!$persona) {
                    return null;
                }

                return [
                    'first_name'       => $persona->nombre,
                    'last_name'        => $persona->apellido,
                    'gender'           => $persona->genero,
                    'birth_date'       => $this->formatDate($persona->nacimiento),
                    'document_type_id' => 'DNI',
                    'document_number'  => $persona->documento,
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
            ->where(function ($q) {
                $q->whereIn('estado', self::ESTADOS_INTEGRANTE_VALIDOS)
                    ->orWhereNull('estado');
            })
            ->get();

        $rows = $integrantes->map(function (Integrante $i) {
            $investigador = $i->investigador;
            $persona = $investigador ? $investigador->persona : null;
            if (!$persona) {
                return null;
            }

            $alta = ($i->alta === '0000-00-00' || !$i->alta) ? '' : $i->alta;
            $baja = ($i->baja === '0000-00-00' || !$i->baja) ? '' : $i->baja;

            $altaFmt = $this->formatDate($alta);
            $bajaFmt = $this->formatDate($baja);

            // Skip participations with the same alta and baja date
            // (added and removed on the same day → no real participation).
            if ($altaFmt !== '' && $altaFmt === $bajaFmt) {
                return null;
            }


            return [
                'code'                => $i->proyecto ? $i->proyecto->codigo : '',
                'document_number'     => $persona->documento,
                'cd_tipoinvestigador' => $this->mapTipoInvestigador($i->tipo),
                'alta'                => $this->formatDate($alta),
                'baja'                => $this->formatDate($baja),
            ];
        })->filter()->values();

        $this->writeCsv($outDir . '/participations.csv', $rows->toArray());
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Returns the first letter of a string, uppercased.
     * Used for fields that store the full word (e.g. "Aplicada")
     * but must be exported as a single-letter code (e.g. "A").
     */
    private function firstLetter(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }
        return mb_strtoupper(mb_substr(trim($value), 0, 1));
    }


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
     * Returns a date as 'Y-m-d', regardless of whether the model casts it
     * as Carbon/DateTime or returns it as a raw string with time.
     * Empty / null / '0000-00-00' values become ''.
     */
    private function formatDate($value): string
    {
        if ($value === null || $value === '' || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
            return '';
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        // String case: take the date portion before any space
        return substr((string) $value, 0, 10);
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

        foreach ($rows as $row) {
            fwrite($handle, $this->formatRow(array_values($row)) . "\n");
        }

        fclose($handle);
        $this->line("    Written: {$path} (" . count($rows) . ' rows)');
    }

    /**
     * Formats a row with every field wrapped in double quotes,
     * separated by '|'. Embedded quotes are escaped by doubling them ("").
     */
    private function formatRow(array $values): string
    {
        $escaped = array_map(function ($v) {
            $v = (string) ($v ?? '');
            return '"' . str_replace('"', '""', $v) . '"';
        }, $values);

        return implode('|', $escaped);
    }

    /**
     * Maps a tipo investigador string (e.g. "DIRECTOR") to its
     * legacy integer code (e.g. 1). Returns '' if not mapped.
     */
    private function mapTipoInvestigador(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }
        $key = mb_strtoupper(trim($value));
        return (string) (self::TIPO_INVESTIGADOR_MAP[$key] ?? '');
    }
}
