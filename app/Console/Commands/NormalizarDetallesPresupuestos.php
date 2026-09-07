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
     *
     * Criterio conservador: sólo se tocan las filas que efectivamente pueden romper
     * la vista, es decir las de conceptos que usan las 3 posiciones (Viaticos,
     * Alojamiento, Pasajes) y que llegaron con menos de 3. Todo lo demás
     * (Inscripcion, Otros, conceptos vacíos, y cualquier fila que ya tenga 3 o más
     * posiciones) se deja exactamente como está: nunca se fusionan campos.
     */
    private function normalizar(string $detalle): array
    {
        // Detalle vacío: no hay nada que normalizar
        if (trim($detalle) === '') {
            return [$detalle, false];
        }

        $partes = explode('|', $detalle);

        // Ya tiene las 3 posiciones (o más): la vista lo lee sin problema, no lo toco
        if (count($partes) >= 3) {
            return [$detalle, false];
        }

        $concepto = trim($partes[0]);
        $resto = array_values(array_slice($partes, 1));

        // Sólo Viaticos/Alojamiento/Pasajes necesitan la posición 2; el resto no rompe
        if (!in_array($concepto, ['Viaticos', 'Alojamiento', 'Pasajes'], true)) {
            return [$detalle, false];
        }

        // Falta todo salvo el concepto
        if (count($resto) === 0) {
            return [implode('|', [$concepto, '', '']), false];
        }

        // Falta un campo: hay que deducir en qué posición va el que sí está
        $valor = trim($resto[0]);

        if ($concepto === 'Pasajes') {
            // Si es un medio de transporte va en la posición 1, si no es el destino
            $campos = in_array($valor, self::MEDIOS, true)
                ? [$concepto, $valor, '']
                : [$concepto, '', $valor];
        } else {
            // Viaticos / Alojamiento: si es numérico lo tomo como días/noches, si no como lugar
            $campos = is_numeric($valor)
                ? [$concepto, $valor, '']
                : [$concepto, '', $valor];
        }

        return [implode('|', $campos), true];
    }
}
