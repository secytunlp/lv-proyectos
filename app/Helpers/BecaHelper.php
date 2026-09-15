<?php

namespace App\Helpers;

class BecaHelper
{
    /**
     * Instituciones que se escriben distinto según la pantalla.
     *
     * La beca actual toma las instituciones de config/becaEntidades.php ("CIC PBA",
     * "AGENCIA i+D+i") y las anteriores de una lista propia del formulario ("CIC",
     * "ANPCyT"). Las dos grafías apuntan a la misma institución, así que cualquiera de
     * las dos tiene que encontrar sus niveles de beca.
     */
    private static $equivalencias = [
        'CIC'           => 'CIC PBA',
        'CIC PBA'       => 'CIC',
        'ANPCyT'        => 'AGENCIA i+D+i',
        'AGENCIA i+D+i' => 'ANPCyT',
    ];

    /**
     * Niveles de beca de la beca actual, según la institución.
     *
     * @param  string|null  $institucionSeleccionada
     * @return array
     */
    public static function obtenerOpcionesBecaPorInstitucion($institucionSeleccionada)
    {
        return self::opciones('becas', $institucionSeleccionada);
    }

    /**
     * Niveles de beca de las becas anteriores, según la institución.
     *
     * @param  string|null  $institucionSeleccionada
     * @return array
     */
    public static function obtenerOpcionesBecaPorInstitucionAnterior($institucionSeleccionada)
    {
        return self::opciones('becasAnteriores', $institucionSeleccionada);
    }

    /**
     * Busca los niveles en el config y los devuelve como valor => etiqueta.
     *
     * Antes cada método tenía su propio switch, duplicando los configs que ya usa el JS
     * del formulario. Esa copia se desalineó: el switch de las anteriores esperaba
     * "CIC PBA" / "AGENCIA i+D+i" mientras el select mandaba "CIC" / "ANPCyT", así que
     * esas dos instituciones se quedaban sin opciones y el formulario guardaba el nivel
     * en blanco.
     *
     * Los config son listas planas; Form::select necesita valor => etiqueta, porque con
     * una lista plana el valor de cada opción termina siendo su índice numérico (la
     * opción vacía valía "0").
     *
     * @param  string  $config
     * @param  string|null  $institucion
     * @return array
     */
    private static function opciones($config, $institucion)
    {
        $institucion = trim((string) $institucion);
        $todas = config($config, []);

        $niveles = null;

        if ($institucion !== '' && array_key_exists($institucion, $todas)) {
            $niveles = $todas[$institucion];
        } elseif ($institucion !== '' && isset(self::$equivalencias[$institucion])) {
            $equivalente = self::$equivalencias[$institucion];
            if (array_key_exists($equivalente, $todas)) {
                $niveles = $todas[$equivalente];
            }
        }

        if ($niveles === null) {
            return ['' => ''];
        }

        $opciones = ['' => ''];
        foreach ($niveles as $nivel) {
            $nivel = (string) $nivel;
            if ($nivel !== '') {
                $opciones[$nivel] = $nivel;
            }
        }

        return $opciones;
    }
}
