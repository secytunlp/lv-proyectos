<?php

namespace App;

class Constants
{
    const YEAR = '2026';
    const CIERRE = '2026-12-01';

    const MAIL_PROYECTOS = 'marcosp@presi.unlp.edu.ar';
    const NOMBRE_PROYECTOS = 'Gestión de Proyectos - SECyT - UNLP';
    const PROYECTO_INICIO = '-01-01';

    const PROYECTO_FIN = '-12-31';

    const EMERITOS_CONSULTOS = '12,13';

    const CARRERAS_INVESTIGACION = '1,2,3,6,8,9,10,12,13';

    const PERSONAL_APOYO_CARRERA = '8,9,13';

    const MINIMO_INTEGRANTES = '3';

    const MINIMO_MAYOR_DEDICACION = '2';

    const ID_ADMIN_FACULTAD_PROYECTOS = '4';

    const ID_COORDINADOR = '7';
    const MAIL_JOVENES = 'marcosp@presi.unlp.edu.ar';
    const NOMBRE_JOVENES = 'Jóvenes Investigadores - SECyT - UNLP';
    const YEAR_JOVENES = '2026';
    const CIERRE_JOVENES = '2026-12-01';

    const MONTO_JOVENES = '167500';

    const RANGO_INI_JOVENES = '01/07/';

    const RANGO_FIN_JOVENES = '30/06/';

    const TOPE_EDAD_JOVENES = '35';

    const DIA_EDAD_JOVENES = '30';

    const MES_EDAD_JOVENES = '12';

    const ID_UNIDAD_UNLP = '1850';

    const ID_UNIDAD_UNLP_CONICET = '20419';

    const YEAR_INGRESO_ATRAS_JOVENES = '4';

    // Cuántas convocatorias hacia atrás empieza a exigirse la rendición.
    // Equivale a CYT_PERIODO_ANTERIORES_OTORGADOS del sistema viejo:
    // con YEAR_JOVENES = 2026 se exige desde 2024 (2025 todavía no vence).
    const PERIODOS_ANTERIORES_JOVENES = '2';

    const RANGO_INGRESO_JOVENES = '01/01/';

    const DIAS_YEAR = '365';

    const YEAR_PROYECTOS = '1';

    const YEAR_VIAJES = '2026';

    const CIERRE_VIAJES = '2026-12-31';

    const CATEGORIAS_FORMADOS = '6,7,8';

    const MONTOS_VIAJES = [
        'BA_CABA'       => 210000,
        'INTERIOR'      => 420000,
        'SUDAMERICA'    => 560000,
        'RESTO_MUNDO'   => 840000,
    ];

    const MONTOS_VIAJES_LABELS = [
        'BA_CABA'       => 'Prov. Buenos Aires y CABA',
        'INTERIOR'      => 'Todo el país excepto Prov. Bs. As.',
        'SUDAMERICA'    => 'Países de Sudamérica',
        'RESTO_MUNDO'   => 'Resto del mundo',
    ];


    const RANGO_INI_VIAJES = '01/09/';

    const RANGO_FIN_VIAJES = '31/08/';

    const MES_DESDE_VIAJES = 'Septiembre';

    const MES_HASTA_VIAJES = 'Agosto';

    const MAX_PALABRAS_RESUMEN_VIAJES = 300;

    const MAIL_VIAJES = 'marcosp@presi.unlp.edu.ar';
    const NOMBRE_VIAJES = 'Viajes/Estadías - SECyT - UNLP';

    // Presupuestos (jovenes y viajes). "detalle" is a one-line description stored in a
    // VARCHAR column, so it has to be capped before the insert.
    // MAX_DETALLE_PRESUPUESTO is the column width.
    // MAX_CAMPO_PRESUPUESTO caps each field of the "concepto|campo1|campo2" string used
    // by tipo_presupuesto_id = 2, so the assembled value can never overflow the column
    // (longest concepto = "Alojamiento", 11 + 2 separators + 2 * 110 = 233).
    const MAX_DETALLE_PRESUPUESTO = 255;

    const MAX_CAMPO_PRESUPUESTO = 110;

    const SICADI_ACTUAL = '5';

    const SICADI_SIN_PROYECTO = 1;

    const CIERRE_SICADI = '2026-12-31';

    const YEAR_SICADI = '2026';

    const MAIL_SICADI = 'marcosp@presi.unlp.edu.ar';

    const NOMBRE_SICADI = 'SICADI - SECyT - UNLP';
}

