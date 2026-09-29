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

    /**
     * Cuántos campos del detalle tiene que traer cargados cada concepto del tipo 2.
     *
     * El detalle del tipo 2 se guarda como "concepto|campo1|campo2" y las vistas lo leen
     * por posición. Según el concepto, el formulario muestra uno o dos campos; los dos que
     * muestra son obligatorios.
     */
    private static $camposRequeridosConcepto = [
        'Viaticos'    => 2, // días y lugar
        'Alojamiento' => 2, // noches y lugar
        'Pasajes'     => 2, // pasaje y destino
        'Inscripcion' => 1, // descripción
        'Otros'       => 1, // descripción
    ];

    /**
     * ¿La fila de presupuesto quedó sin descripción?
     *
     * El guardado acepta la fila con que esté el concepto o el importe, y el envío sólo
     * miraba el total, así que se podía mandar un "Otros - Descripción:" en blanco, que en
     * el PDF sale como el esqueleto vacío.
     *
     * @param  object  $presupuesto  fila de joven_presupuestos / viaje_presupuestos
     * @return bool
     */
    protected function presupuestoSinDescripcion($presupuesto)
    {
        $detalle = trim((string) $presupuesto->detalle);

        if (intval($presupuesto->tipo_presupuesto_id) !== 2) {
            return ($detalle === '');
        }

        $partes   = array_pad(explode('|', $detalle), 3, '');
        $concepto = trim($partes[0]);

        if ($concepto === '') {
            return true;
        }

        $requeridos = isset(self::$camposRequeridosConcepto[$concepto])
            ? self::$camposRequeridosConcepto[$concepto]
            : 1;

        for ($i = 1; $i <= $requeridos; $i++) {
            if (trim($partes[$i]) === '') {
                return true;
            }
        }

        return false;
    }

    /**
     * Cómo nombrar una fila de presupuesto en un mensaje de error, para que el solicitante
     * la encuentre en la pantalla.
     *
     * @param  object  $presupuesto
     * @return string
     */
    protected function describirFilaPresupuesto($presupuesto)
    {
        $detalle = trim((string) $presupuesto->detalle);
        $partes  = array_pad(explode('|', $detalle), 3, '');

        if (intval($presupuesto->tipo_presupuesto_id) === 2) {
            $concepto = trim($partes[0]);
            $texto = ($concepto !== '') ? $concepto : 'fila sin concepto';
        } else {
            $texto = ($detalle !== '') ? mb_substr($detalle, 0, 40) : 'fila sin descripción';
        }

        $fecha = substr((string) $presupuesto->fecha, 0, 10);
        if ($fecha !== '' && strpos($fecha, '0000-00-00') !== 0) {
            $texto .= ' del '.date('d/m/Y', strtotime($fecha));
        }

        return $texto.' por $'.number_format((float) $presupuesto->monto, 2, ',', '.');
    }
}
