<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Gestiona la tabla de renuncias por año que consume el cálculo de subsidios:
 *   subsidio_proyecto_renuncias_{anio}
 *
 * Los proyectos con renuncia se EXCLUYEN del reparto del subsidio (ver
 * CalcularSubsidios::poblarIntproy / poblarDirproy), pero NO de los listados
 * de proyectos en ejecución (subsidios:facultad no mira esta tabla).
 *
 * En el proceso anterior esta tabla se mantenía a mano (subsidio_proyecto_renuncias,
 * sin sufijo de año). Este comando la reemplaza por la versión por año y permite
 * cargarla / consultarla / vaciarla desde Laravel.
 *
 * Uso:
 *   php artisan subsidios:renuncias --anio=2026
 *   php artisan subsidios:renuncias --anio=2026 --agregar=11/N1013,EB008
 *   php artisan subsidios:renuncias --anio=2026 --quitar=EB008
 *   php artisan subsidios:renuncias --anio=2026 --vaciar
 *   php artisan subsidios:renuncias --anio=2026 --clonar-de-base
 */
class GestionarRenunciasSubsidios extends Command
{
    protected $signature = 'subsidios:renuncias
        {--anio= : Año de la tabla subsidio_proyecto_renuncias_{anio}. Requerido.}
        {--agregar= : Códigos de proyecto a marcar como renuncia, separados por coma.}
        {--quitar= : Códigos de proyecto a sacar de la tabla, separados por coma.}
        {--vaciar : Borra todas las renuncias del año (pide confirmación).}
        {--clonar-de-base : Copia las filas de la tabla vieja subsidio_proyecto_renuncias (sin sufijo).}
        {--desde-anio= : Arrastra las renuncias de subsidio_proyecto_renuncias_{ese_anio} que sigan en ejecución.}
        {--fecha-corte= : Corte para "sigue en ejecución" (fin > corte). Por defecto {anio-1}-12-31.}
        {--incluir-terminados : Al arrastrar de otro año, copiar también proyectos ya terminados.}';

    protected $description = 'Administra la tabla de renuncias por año que usa el cálculo de subsidios (crear/listar/agregar/quitar/vaciar).';

    /** @var string */
    protected $tabla;

    public function handle(): int
    {
        $anio = (int) $this->option('anio');
        if ($anio < 2000) {
            $this->error('Falta --anio (ej. 2026).');
            return self::FAILURE;
        }
        $this->tabla = "subsidio_proyecto_renuncias_{$anio}";

        $this->crearTablaSiNoExiste();

        $fechaCorte = $this->option('fecha-corte') ?: ($anio - 1) . '-12-31';

        $huboAccion = false;

        if ($this->option('clonar-de-base')) {
            $this->clonarDeBase();
            $huboAccion = true;
        }

        if ($desdeAnio = (int) $this->option('desde-anio')) {
            $this->clonarDeAnio($desdeAnio, $fechaCorte);
            $huboAccion = true;
        }

        if ($cods = $this->parseCodigos($this->option('agregar'))) {
            $this->agregar($cods);
            $huboAccion = true;
        }

        if ($cods = $this->parseCodigos($this->option('quitar'))) {
            $this->quitar($cods);
            $huboAccion = true;
        }

        if ($this->option('vaciar')) {
            $n = DB::table($this->tabla)->count();
            if ($n === 0) {
                $this->info("La tabla {$this->tabla} ya está vacía.");
            } elseif ($this->confirm("¿Vaciar {$this->tabla} ({$n} renuncia(s))?", false)) {
                DB::table($this->tabla)->delete();
                $this->info("Tabla {$this->tabla} vaciada.");
            } else {
                $this->line('Cancelado, no se borró nada.');
            }
            $huboAccion = true;
        }

        // Sin acciones, o después de cualquiera de ellas, mostramos el estado.
        $this->listar();

        if (! $huboAccion) {
            $this->newLine();
            $this->line('Acciones: --agregar=COD1,COD2 | --quitar=COD | --vaciar | --clonar-de-base');
        }

        return self::SUCCESS;
    }

    protected function crearTablaSiNoExiste(): void
    {
        DB::statement("
            CREATE TABLE IF NOT EXISTS `{$this->tabla}` (
                `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                `proyecto_id` INT(11) NULL DEFAULT NULL,
                `codigo` VARCHAR(50) NULL DEFAULT NULL,
                `director` VARCHAR(255) NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) COLLATE='utf8mb4_unicode_ci' ENGINE=InnoDB
        ");
    }

    /** @return string[] */
    protected function parseCodigos($opt): array
    {
        if (! $opt) {
            return [];
        }
        $cods = array_map('trim', explode(',', (string) $opt));
        $cods = array_filter($cods, function ($c) {
            return $c !== '';
        });
        return array_values(array_unique($cods));
    }

    /**
     * @param string[] $codigos
     */
    protected function agregar(array $codigos): void
    {
        foreach ($codigos as $codigo) {
            $proy = $this->buscarProyecto($codigo);
            if (! $proy) {
                $this->warn("  No existe proyecto con código {$codigo}, se saltea.");
                continue;
            }

            $yaEsta = DB::table($this->tabla)
                ->where('proyecto_id', $proy->proyecto_id)
                ->exists();
            if ($yaEsta) {
                $this->line("  {$codigo} ya estaba cargado.");
                continue;
            }

            DB::table($this->tabla)->insert([
                'proyecto_id' => $proy->proyecto_id,
                'codigo'      => $proy->codigo,
                'director'    => $this->director($proy),
            ]);
            $this->info("  + {$proy->codigo}  ({$this->director($proy)})");
        }
    }

    /**
     * @param string[] $codigos
     */
    protected function quitar(array $codigos): void
    {
        $n = DB::table($this->tabla)->whereIn('codigo', $codigos)->delete();
        $this->info("Quitadas {$n} renuncia(s): " . implode(', ', $codigos));
    }

    protected function clonarDeBase(): void
    {
        if (! DB::getSchemaBuilder()->hasTable('subsidio_proyecto_renuncias')) {
            $this->warn('No existe la tabla base subsidio_proyecto_renuncias, no hay nada que clonar.');
            return;
        }

        $insertados = 0;
        $base = DB::table('subsidio_proyecto_renuncias')->get();
        foreach ($base as $r) {
            $existe = DB::table($this->tabla)
                ->where('proyecto_id', $r->proyecto_id)
                ->exists();
            if ($existe) {
                continue;
            }
            DB::table($this->tabla)->insert([
                'proyecto_id' => $r->proyecto_id,
                'codigo'      => $r->codigo,
                'director'    => $r->director,
            ]);
            $insertados++;
        }
        $this->info("Clonadas {$insertados} renuncia(s) desde subsidio_proyecto_renuncias.");
    }

    /**
     * Arrastra las renuncias de otro año (tabla por año), copiando sólo los
     * proyectos que siguen en ejecución para el año destino (fin > corte),
     * salvo que se pase --incluir-terminados.
     */
    protected function clonarDeAnio(int $anioOrigen, string $fechaCorte): void
    {
        $tablaOrigen = "subsidio_proyecto_renuncias_{$anioOrigen}";
        if (! DB::getSchemaBuilder()->hasTable($tablaOrigen)) {
            $this->warn("No existe la tabla {$tablaOrigen}, no hay de dónde arrastrar.");
            return;
        }
        if ($tablaOrigen === $this->tabla) {
            $this->warn('El año de origen es el mismo que el destino, se saltea.');
            return;
        }

        $incluirTerminados = (bool) $this->option('incluir-terminados');
        $insertados = 0;
        $existentes = 0;
        $terminados = 0;

        foreach (DB::table($tablaOrigen)->get() as $r) {
            if (DB::table($this->tabla)->where('proyecto_id', $r->proyecto_id)->exists()) {
                $existentes++;
                continue;
            }

            if (! $incluirTerminados && ! $this->sigueEnEjecucion($r->proyecto_id, $fechaCorte)) {
                $terminados++;
                $this->line("  - {$r->codigo}: terminado (fin <= {$fechaCorte}), no se arrastra.");
                continue;
            }

            DB::table($this->tabla)->insert([
                'proyecto_id' => $r->proyecto_id,
                'codigo'      => $r->codigo,
                'director'    => $r->director,
            ]);
            $this->info("  + {$r->codigo}  ({$r->director})");
            $insertados++;
        }

        $this->info(sprintf(
            'Arrastre desde %s: %d nueva(s), %d ya estaban, %d terminada(s) omitida(s).',
            $anioOrigen, $insertados, $existentes, $terminados
        ));
    }

    /** El proyecto sigue en ejecución para el corte dado (fin > corte). */
    protected function sigueEnEjecucion($proyectoId, string $fechaCorte): bool
    {
        $p = DB::table('proyectos')->where('id', $proyectoId)->select('fin')->first();
        if (! $p || ! $p->fin) {
            return false;
        }
        $fin = strtotime((string) $p->fin);
        $corte = strtotime($fechaCorte);
        return $fin !== false && $corte !== false && $fin > $corte;
    }

    protected function listar(): void
    {
        $filas = DB::table($this->tabla)->orderBy('codigo')->get();
        $this->newLine();
        $this->info("{$this->tabla}: {$filas->count()} renuncia(s).");

        if ($filas->isEmpty()) {
            return;
        }

        $rows = [];
        foreach ($filas as $f) {
            $rows[] = [$f->proyecto_id, $f->codigo, $f->director];
        }
        $this->table(['proyecto_id', 'codigo', 'director'], $rows);
    }

    /**
     * Resuelve proyecto_id, código y director (primer integrante Director) por código.
     */
    protected function buscarProyecto(string $codigo)
    {
        return DB::table('proyectos as p')
            ->leftJoin('integrantes as i', function ($join) {
                $join->on('p.id', '=', 'i.proyecto_id')
                    ->where('i.tipo', '=', 'Director');
            })
            ->leftJoin('investigadors as inv', 'i.investigador_id', '=', 'inv.id')
            ->leftJoin('personas as per', 'inv.persona_id', '=', 'per.id')
            ->where('p.codigo', $codigo)
            ->select([
                'p.id as proyecto_id',
                'p.codigo',
                'per.apellido',
                'per.nombre',
            ])
            ->orderBy('i.id')
            ->first();
    }

    protected function director($proy): string
    {
        return trim(((string) ($proy->apellido ?? '')) . ', ' . ((string) ($proy->nombre ?? '')), ', ');
    }
}
