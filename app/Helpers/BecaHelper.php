<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class BecaHelper
{
    public static function obtenerOpcionesBecaPorInstitucion($institucionSeleccionada)
    {
        //dd($institucionSeleccionada);

        switch ($institucionSeleccionada) {
            case 'AGENCIA i+D+i':
                return ['','Beca inicial'=>'Beca inicial', 'Beca superior'=>'Beca superior'];
            case 'CIC PBA':
                return ['', 'Beca de entrenamiento'=>'Beca de entrenamiento','Beca doctoral'=>'Beca doctoral', 'Beca posdoctoral'=>'Beca posdoctoral'];
            case 'CONICET':
                return ['','Beca doctoral'=>'Beca doctoral', 'Beca posdoctoral'=>'Beca posdoctoral','Beca finalización del doctorado'=>'Beca finalización del doctorado'];
            case 'UNLP':
                return ['','Beca doctoral'=>'Beca doctoral', 'Beca posdoctoral'=>'Beca posdoctoral','Beca maestría'=>'Beca maestría','Beca Cofinanciada (UNLP-CIC)'=>'Beca Cofinanciada (UNLP-CIC)'];
            case 'CIN':
                return ['','EVC'=>'EVC'];
            default:
                return ['']; // Opción por defecto
        }
    }

    public static function obtenerOpcionesBecaPorInstitucionAnterior($institucionSeleccionada)
    {
        switch ($institucionSeleccionada) {
            case 'AGENCIA i+D+i':
                return ['','Beca inicial'=>'Beca inicial', 'Beca superior'=>'Beca superior'];
            case 'CIC PBA':
                return ['', 'Beca de entrenamiento'=>'Beca de entrenamiento','Beca doctoral'=>'Beca doctoral', 'Beca posdoctoral'=>'Beca posdoctoral'];
            case 'CONICET':
                return ['','Beca doctoral'=>'Beca doctoral', 'Beca posdoctoral'=>'Beca posdoctoral','Beca finalización del doctorado'=>'Beca finalización del doctorado','TIPO I'=>'TIPO I','TIPO II'=>'TIPO II','CONICET 2'=>'CONICET 2'];
            case 'UNLP':
                return ['','Beca doctoral'=>'Beca doctoral', 'Beca posdoctoral'=>'Beca posdoctoral','Beca maestría'=>'Beca maestría','Beca Cofinanciada (UNLP-CIC)'=>'Beca Cofinanciada (UNLP-CIC)', 'Formación Superior'=>'Formación Superior','Iniciación'=>'Iniciación','TIPO A'=>'TIPO A','Tipo A - Maestría'=>'Tipo A - Maestría','Tipo A - Doctorado'=>'Tipo A - Doctorado','Especial de Maestría'=>'Especial de Maestría','TIPO B'=>'TIPO B','TIPO B (DOCTORADO)'=>'TIPO B (DOCTORADO)','TIPO B (MAESTRÍA)'=>'TIPO B (MAESTRÍA)','BECA DE PERFECCIONAMIENTO'=>'BECA DE PERFECCIONAMIENTO','RETENCION DE POSTGRADUADO'=>'RETENCION DE POSTGRADUADO'];
            case 'CIN':
                return ['','EVC'=>'EVC'];
            default:
                return ['']; // Opción por defecto
        }
    }
}

