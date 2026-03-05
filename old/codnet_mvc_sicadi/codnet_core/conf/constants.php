<?php 	

//para almacenar las clases. 
//0 => en el archivo hashClassMap.php
//1 => en sesi�n
//define("CLASS_LOADER_FROM_SESSION", 1);

//para loguear las sentencias sql
define("LOG_SQL",0);


//para manejar los errores de la pantalla.


//para loguear en los archivos de php
//ini_set('log_errors', 1);
//ini_set('error_log', dirname(__FILE__) . '/error_log.txt'); 

//para loguear el debug => CdtUtils::log_debug(...);
//define("CDT_DEBUG_LOG", 1);
//para loguear errores => CdtUtils::log_error(...);
//define("CDT_ERROR_LOG", 1);
//para loguear mensajes => CdtUtils::log_message(...);
//define("CDT_MESSAGE_LOG", 1);


//seteamos el timezone.
date_default_timezone_set('UTC');


/*define('APP_NAME', 'My App');
define('CDT_PDF_APP_NAME', APP_NAME);
define('CDT_MVC_APP_TITLE', 'My App title');
define('CDT_MVC_APP_SUBTITLE', 'my app subtitle');*/

define('CDT_EXTERNAL_LIB_PATH', APP_PATH . '/libs/');

?>