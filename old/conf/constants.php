<?php


//envío de emails.


//desarrollo.
define('CDT_POP_MAIL_FROM', 'categorizacion1@presi.unlp.edu.ar');
define('CDT_POP_MAIL_FROM_NAME', 'SICADI');
define('CDT_POP_MAIL_HOST', 'apps.presi.unlp.edu.ar');
define('CDT_POP_MAIL_PORT', '465');
define('CDT_POP_MAIL_SMTP_SECURE', 'ssl');
define('CDT_POP_MAIL_USERNAME', 'secyt');
define('CDT_POP_MAIL_PASSWORD', 'S9rg*sdf5g!fh67');
define('CDT_MAIL_ENVIO_POP', true);


define("CDT_TEST_MODE", false);



define('CYT_DATE_FORMAT', 'd/m/Y');
define('CYT_DATETIME_FORMAT', 'd/m/Y H:i:s');
define('CYT_DATETIME_FORMAT_STRING', 'YmdHis');



//lista de permisos
define('CYT_FUNCTION_AGREGAR_SOLICITUD', '60');
define('CYT_FUNCTION_LISTAR_ESTADO', '65');
define('CYT_FUNCTION_VER_PUNTAJE', '66');
define('CYT_FUNCTION_VER_ANTERIORES', '67');
define('CYT_FUNCTION_ENVIAR_SOLICITUD', '68');
define('CYT_FUNCTION_ADMITIR_SOLICITUD', '69');
define('CYT_FUNCTION_RECHAZAR_SOLICITUD', '70');
define('CYT_FUNCTION_VER_EVALUACION', '76');
define('CYT_FUNCTION_EVALUAR_SOLICITUD', '74');
define('CYT_FUNCTION_ENVIO_DEFINITIVO', '81');

define('CYT_PERIODO_INICIAL', '2010');

define('CYT_PERIODO_YEAR', '2023');
define('CYT_RANGO_INI', '01/07/');
define('CYT_RANGO_FIN', '30/06/');
define('CYT_RANGO_INGRESO', '01/01/');
define('CYT_YEAR_INGRESO_ATRAS', '4');
define('CYT_DIA_MES_BECA', '-03-31');
define('CYT_DIA_MES_EDAD', '-12-30');
define('CYT_FIN_EDAD', '31/12/');
define('CYT_EDAD_TOPE', 35);
define('CYT_DIFERENCIA', 15);
define('CYT_PERIODO_ANTERIORES_OTORGADOS', '2');
define('CYT_MONTO_MAXIMO', 9000);
define('CYT_RESUMEN_PALABRAS_MAXIMO', 300);
define('CYT_DIAS_YEAR', 360);
define('CYT_YEARS_PROYECTOS', 1);


define('CYT_TIPO_INVESTIGADOR_MOSTRADOS', '3,4');

define('CYT_FECHA_CIERRE', '2023-12-18');
define('CYT_PROYECTO_RANGO_INI', '01/01/1994');
define('CYT_PROYECTO_RANGO_FIN', '31/12/2009');

define('CYT_BECA_RANGO_FIN', '01/04/2011');

define('CYT_LONGITUD_EVALUACION_LINEA', 105);

define('CYT_CD_OTROS', 'Otros');

define('CYT_CD_GROUPS_MOSTRAR', '3,18,19');

define('CYT_EXTENSIONES_PERMITIDAS_IMG', 'png,jpeg,jpg,bmp,gif');

define('CYT_EQUIVALENCIA_SPU', 1);
define('CYT_EQUIVALENCIA_SUPERIOR', 2);
define('CYT_EQUIVALENCIA_INDEPENDIENTE', 3);
define('CYT_EQUIVALENCIA_ADJUNTO', 4);
define('CYT_EQUIVALENCIA_ASISTENTE_3', 5);
define('CYT_EQUIVALENCIA_ASISTENTE_CPA', 6);
define('CYT_EQUIVALENCIA_BECARIO_POSTDOCTORAL', 7);
define('CYT_EQUIVALENCIA_BECARIO_DOCTORAL', 8);
define('CYT_EQUIVALENCIA_EMERITO', 9);

define('CYT_EQUIVALENCIA_DS_SPU', 'DI-PRINUAR');
define('CYT_EQUIVALENCIA_DS_PRINCIPAL', 'Investigador/a Principal o Superior');
define('CYT_EQUIVALENCIA_DS_SUPERIOR', 'Investigador Superior');
define('CYT_EQUIVALENCIA_DS_INDEPENDIENTE', 'Investigador/a Independiente');
define('CYT_EQUIVALENCIA_DS_POSTDOCTORAL', 'Becario/a Posdoctoral');
define('CYT_EQUIVALENCIA_DS_ADJUNTO', 'Investigador/a Adjunto/a');
define('CYT_EQUIVALENCIA_DS_ASISTENTE', 'Investigador/a Asistente 3+ informes');
define('CYT_EQUIVALENCIA_DS_CPA', 'Investigador/a Asistente o CPA');
define('CYT_EQUIVALENCIA_DS_DOCTORAL', 'Becario/a Doctoral');
define('CYT_EQUIVALENCIA_DS_EMERITO', 'Profesor emérito');



define('CYT_CATS_SPU', '6,7,8,9,10');
define('CYT_CATS_SUPERIOR', '6');
define('CYT_CATS_INDEPENDIENTE', '7');
define('CYT_CATS_ADJUNTO', '8');
define('CYT_CATS_ASISTENTE', '9');
define('CYT_CATS_DOCTORAL', '10');

define('CYT_EQUIVALENCIA_CATS_SUPERIOR', 'DI1');
define('CYT_EQUIVALENCIA_CATS_INDEPENDIENTE', 'DI2');
define('CYT_EQUIVALENCIA_CATS_ADJUNTO', 'DI3');
define('CYT_EQUIVALENCIA_CATS_ASISTENTE', 'DI4');
define('CYT_EQUIVALENCIA_CATS_DOCTORAL', 'DI5');

?>