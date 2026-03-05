<?php
/**
 * se definen la configuración para la conexión
 * a la base de datos.
 *
 * @author Marcos
 * @since 16/10/2013
 *
 */



/* DESARROLLO */
//define('DB_CLASS', 'AuditMySQL');
define('DB_CLASS', 'MySQL');
define('DB_HOST', '163.10.8.206');
define('DB_USER', 'us_cyt_incentivos');
define('DB_PASSWORD', '1G56JdvaxN7herasD4-inc');
define('DB_NAME', 'incentivos');
define('ROW_PER_PAGE', 10);
define('DB_DEFAULT_DATE_FORMAT', "Y-m-d");
define('DB_DEFAULT_DATETIME_FORMAT', "Y-m-d H:i:s");
define('DB_FILE_DATETIME_FORMAT', "YmdHis");

/* PREPRODUCCIÓN
 define('DB_CLASS', 'MySQL');
 define('DB_HOST', '192.168.1.121');
 define('DB_USER', 'root');
 define('DB_PASSWORD', 'codnet');
 define('DB_NAME', 'cpiq_matriculados');
 define('ROW_PER_PAGE', 15);
 */

/* PRODUCCIÓN
 define('DB_CLASS', 'MySQL');
 define('DB_HOST', '192.168.1.121');
 define('DB_USER', 'root');
 define('DB_PASSWORD', 'codnet');
 define('DB_NAME', 'cpiq_matriculados');
 define('ROW_PER_PAGE', 15);
 */
?>