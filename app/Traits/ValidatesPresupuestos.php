<?php

namespace App\Traits;

use App\Constants;
use Illuminate\Http\Request;

trait ValidatesPresupuestos
{
    /**
     * Free-text inputs that end up inside the "detalle" column of joven_presupuestos /
     * viaje_presupuestos, with the cap that applies to each one.
     *
     * "detalles" is the whole column (tipo_presupuesto_id <> 2). The rest are the pieces
     * of the "concepto|campo1|campo2" string built for tipo_presupuesto_id = 2, so they
     * get the smaller cap: two of them plus the concepto still have to fit the column.
     */
    private static $camposDetallePresupuesto = [
        'detalles'    => Constants::MAX_DETALLE_PRESUPUESTO,
        'lugar'       => Constants::MAX_CAMPO_PRESUPUESTO,
        'destino'     => Constants::MAX_CAMPO_PRESUPUESTO,
        'inscripcion' => Constants::MAX_CAMPO_PRESUPUESTO,
        'otros'       => Constants::MAX_CAMPO_PRESUPUESTO,
    ];

    /**
     * Add a length rule for every presupuesto input present in the request.
     *
     * The input names are built at runtime from the ids of tipo_presupuestos
     * ("presupuesto1detalles", "presupuesto2lugar", ...), so the rules are derived from
     * the request itself instead of being hardcoded per tipo.
     *
     * @return void
     */
    protected function reglasDetallePresupuesto(Request $request, array &$rules, array &$messages)
    {
        foreach (array_keys($request->all()) as $input) {
            if (!preg_match('/^presupuesto\d+([a-z]+)$/', $input, $partes)) {
                continue;
            }

            $campo = $partes[1];

            if (!isset(self::$camposDetallePresupuesto[$campo])) {
                continue;
            }

            $tope = self::$camposDetallePresupuesto[$campo];

            $rules[$input . '.*'] = 'nullable|max:' . $tope;
            $messages[$input . '.*.max'] = 'El detalle de un presupuesto no puede superar los ' . $tope . ' caracteres.';
        }
    }

    /**
     * Last-resort cap applied right before the insert.
     *
     * The validation above is what the user sees; this only exists so that a value
     * reaching the insert by any other path truncates instead of throwing
     * SQLSTATE[22001] (1406 Data too long) and returning a 500.
     *
     * @param  mixed  $detalle
     * @return string
     */
    protected function recortarDetalle($detalle)
    {
        return mb_substr((string) $detalle, 0, Constants::MAX_DETALLE_PRESUPUESTO);
    }
}
