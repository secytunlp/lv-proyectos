<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Normaliza el campo `detalle` de los presupuestos de tipo 2 (viáticos/pasajes/etc).
 *
 * El detalle se guarda como "concepto|campo1|campo2" y las vistas lo leen por
 * posición ($detalles[0], [1] y [2]). Los registros guardados antes del fix del
 * controlador pueden tener menos de 3 posiciones (se filtraban los vacíos), lo
 * que rompía la edición con "Undefined array key 1".
 *
 * Por defecto SIMULA: no toca la base hasta que se pasa --apply.
 */
class NormalizarDetallesPresupuestos extends Command
{
    protected $signature = 'presupuestos:normalizar-detalles
                            {--apply : Aplica los cambios en la base (sin esta opción sólo simula)}
                            {--tabla=todas : joven | viaje | todas}';

    protected $description = 'Normaliza el campo detalle de los presupuestos tipo 2 a 3 posiciones fijas separadas por "|"';

    /** Medios de transporte válidos para el concepto Pasajes */
    private const MEDIOS = ['Aereo', 'Omnibus', 'Automovil'];

    public function handle()
    {
        $apply = (bool) $this->option('apply');
        $tabla = strtolower((string) $this->option('tabla'));

        $tablas = [
            'joven' => 'joven_presupuestos',
            'viaje' => 'viaje_presupuestos',
        ];

        if ($tabla !== 'todas') {
            if (!isset($tablas[$tabla])) {
                $this->error('La opción --tabla admite: joven, viaje o todas.');
                return 1;
            }
            $tablas = [$tabla => $tablas[$tabla]];
        }

        if (!$apply) {
            $this->warn('MODO SIMULACIÓN: no se modifica nada. Volvé a correr con --apply para aplicar.');
        }

        $totalCambios = 0;
        $totalDudosos = 0;

        foreach ($tablas as $etiqueta => $nombreTabla) {
            $this->line('');
            $this->info("=== {$nombreTabla} ===");

            $registros = DB::table($nombreTabla)
                ->where('tipo_presupuesto_id', 2)
                ->orderBy('id')
                ->get();

            $filas = [];

            foreach ($registros as $registro) {
                $original = (string) $registro->detalle;
                [$nuevo, $dudoso] = $this->normalizar($original);

                if ($nuevo === $original) {
                    continue;
                }

                $filas[] = [
                    $registro->id,
                    $original,
                    $nuevo,
                    $dudoso ? 'revisar' : '',
                ];

                $totalCambios++;
                if ($dudoso) {
                    $totalDudosos++;
                }

                if ($apply) {
                    DB::table($nombreTabla)
                        ->where('id', $registro->id)
                        ->update([
                            'detalle' => $nuevo,
                            'updated_at' => now(),
                        ]);
                }
            }

            if (empty($filas)) {
                $this->line('Sin registros para corregir.');
                continue;
            }

            $this->table(['id', 'detalle actual', 'detalle normalizado', 'obs'], $filas);
        }

        $this->line('');
        $this->info("Registros a corregir: {$totalCambios}" . ($apply ? ' (aplicados)' : ' (simulados)'));

        if ($totalDudosos > 0) {
            $this->warn("{$totalDudosos} registro(s) marcados como 'revisar': faltaba un campo y se dedujo la posición por heurística. Conviene mirarlos a mano.");
        }

        return 0;
    }

    /**
     * Devuelve [detalleNormalizado, esDudoso].
     */
    private function normalizar(string $detalle): array
    {
        $partes = explode('|', $detalle);
        $concepto = trim($partes[0] ?? '');
        $resto = array_values(array_slice($partes, 1));
        $dudoso = false;

        // Si ya viene con más de 3 posiciones, junto el sobrante en la última
        if (count($resto) > 2) {
            $resto = [$resto[0], implode(' ', array_slice($resto, 1))];
        }

        switch ($concepto) {
            case 'Viaticos':
            case 'Alojamiento': // concepto legacy, misma estructura (cantidad|lugar)
                if (count($resto) === 1) {
                    // Un solo dato: si es numérico lo tomo como días/noches, si no como lugar
                    if (is_numeric(trim($resto[0]))) {
                        $campos = [$concepto, trim($resto[0]), ''];
                    } else {
                        $campos = [$concepto, '', trim($resto[0])];
                    }
                    $dudoso = true;
                } else {
                    $campos = [$concepto, trim($resto[0] ?? ''), trim($resto[1] ?? '')];
                }
                break;

            case 'Pasajes':
                if (count($resto) === 1) {
                    // Un solo dato: si es un medio de transporte va en la posición 1, si no es el destino
                    if (in_array(trim($resto[0]), self::MEDIOS, true)) {
                        $campos = [$concepto, trim($resto[0]), ''];
                    } else {
                        $campos = [$concepto, '', trim($resto[0])];
                    }
                    $dudoso = true;
                } else {
                    $campos = [$concepto, trim($resto[0] ?? ''), trim($resto[1] ?? '')];
                }
                break;

            case 'Inscripcion':
            case 'Otros':
                // Sólo usan la posición 1 (descripción); la 2 queda vacía
                $campos = [$concepto, trim(implode(' ', $resto)), ''];
                break;

            default:
                // Concepto vacío o desconocido: conservo lo que haya en las 3 posiciones
                $campos = [$concepto, trim($resto[0] ?? ''), trim($resto[1] ?? '')];
                break;
        }

        return [implode('|', $campos), $dudoso];
    }
}
