<?php


/**
 * se configuran los módulos de Centros.
 *
 * @author codnet archetype builder
 * @since
 *
 */

define('CLASS_LOADER_FROM_SESSION', 1);
define('CACHE_ID', "SICADI");
define('APP_NAME', 'sicadi');

define('CDT_EXTERNAL_LIB_PATH_2', APP_PATH . '/libs/');
//configuramos log4php
define( "CDT_LOG4PHP_PATH", CDT_EXTERNAL_LIB_PATH_2 . "log4php") ;
define( "CDT_LOG4PHP_CONFIG_FILE", APP_PATH . "/conf/log4php.xml");
require_once( CDT_LOG4PHP_PATH . '/Logger.php' );

//configuramos esapi
define( "CDT_ESAPI_PATH", CDT_EXTERNAL_LIB_PATH_2 . "esapi/src") ;

require_once( CDT_ESAPI_PATH . '/ESAPI.php' );

define('CYT_PATH', APP_PATH . '/classes/com/sicadi/');
define('CYT_PATH_TEST', APP_PATH . '/unit_test/');



/* mvc */
define('MVC_CYT_PATH', $_SERVER ['DOCUMENT_ROOT'] . '/cyt_mvc_sicadi/');
define('CYT_SECURE_PATH', MVC_CYT_PATH .   'cyt_secure/');
include_once (CYT_SECURE_PATH . 'conf/init.php');

define('MVC_PATH', $_SERVER ['DOCUMENT_ROOT'] . '/codnet_mvc_sicadi/');


define('CDT_CORE_PATH', MVC_PATH .   'codnet_core/');
//define('CDT_CORE_PATH', MVC_PATH .   'codnet_core_2_0_6/');
include_once (CDT_CORE_PATH . 'conf/init.php');

define('DEFAULT_MENU', 'Menu');

define('CDT_MVC_APP_TITLE', 'SeCyT');
define('CDT_MVC_APP_SUBTITLE', 'SICADI');


//para manejar los errores de la pantalla.

define("CDT_DEBUG_LOG", 1);
define("CDT_ERROR_LOG", 1);
define("CDT_MESSAGE_LOG", 1);




//restore_error_handler();
//seteamos el timezone.


/* ui */
//define('CDT_UI_PATH', MVC_PATH . 'codnet_ui_0_2_3/');
define('CDT_UI_PATH', MVC_PATH . 'codnet_ui/');
include_once (CDT_UI_PATH . 'conf/init.php');


/* seguridad */
//define('CDT_SECURE_PATH', MVC_PATH . 'codnet_secure_2_0_3/');
define('CDT_SECURE_PATH', MVC_PATH . 'codnet_secure/');
include_once (CDT_SECURE_PATH . 'conf/init.php');

//define('CDT_SECURE_LOGIN_TITLE', CDT_MVC_APP_TITLE);
//define('CDT_SECURE_LOGIN_SUBTITLE', CDT_MVC_APP_SUBTITLE);
//define('CDT_SECURE_REGISTER_TITLE', CDT_MVC_APP_TITLE);
//define('CDT_SECURE_REGISTER_SUBTITLE', CDT_MVC_APP_SUBTITLE);

//define("CDT_SECURE_ACCESS_DENIED_ACTION", 'home');

//login
//define("CDT_SECURE_LOGIN_ACTION", 'doAction?action=login');


//registraciones
define("CDT_SECURE_REGISTRATION_LAYOUT", 'LayoutSmileSignup');
define("CDT_SECURE_ACTIVATE_REGISTRATION_ACTION", 'doAction?action=activate_registration');
define("CDT_SECURE_USERGROUP_DEFAULT_ID", 3);
define("CDT_SECURE_REGISTRATION_TERMS_LAYOUT", "CdtLayoutBasicAjax");
define('CDT_SECURE_REGISTRATION_ENABLED', true);

//recuperar clave
define("CDT_SECURE_FORGOT_PASSWORD_LAYOUT", 'LayoutCYTLogin');
define("CDT_SECURE_FORGOT_PASSWORD_INIT_ACTION", 'doAction?action=forgot_password_init');
define("CDT_SECURE_FORGOT_PASSWORD_ACTION", 'doAction?action=forgot_password');


/* theme */
//define('CDT_UI_THEME_SOFT_PATH', MVC_PATH  . 'codnet_ui_theme_soft/');
//define('CDT_UI_THEME_WEB_PATH', WEB_PATH . 'css/soft/');
//include_once (CDT_UI_THEME_SOFT_PATH . 'conf/init.php');


/* layout */
//define('CDT_UI_SMILE_PATH', MVC_PATH  . 'codnet_ui_smile_0_2_2/');
define('CDT_UI_SMILE_PATH', MVC_PATH  . 'codnet_ui_smile/');
include_once (CDT_UI_SMILE_PATH . 'conf/init.php');

define ( 'DEFAULT_LAYOUT', 'LayoutCYT' );
define ( 'DEFAULT_PANEL_LAYOUT', 'LayoutCYT');
define ( 'DEFAULT_EDIT_LAYOUT', 'LayoutCYT' );
define ( 'DEFAULT_LOGIN_LAYOUT', 'LayoutCYTLogin' );
define ( 'DEFAULT_POPUP_LAYOUT', 'LayoutSmilePopup' );

//define ( 'CDT_UI_SMILE_CHARSET', 'UTF-8' );
define ( 'CDT_UI_UTF8_ENCODE', false );


/*
 define('CDT_UI_SOFT_PATH', MVC_PATH  . 'codnet_ui_soft/');
 include_once (CDT_UI_SOFT_PATH . 'conf/init.php');
 define ( 'DEFAULT_LAYOUT', 'LayoutSoft' );
 define ( 'DEFAULT_PANEL_LAYOUT', 'LayoutSoft');
 define ( 'DEFAULT_EDIT_LAYOUT', 'LayoutSoft' );
 define ( 'DEFAULT_LOGIN_LAYOUT', 'LayoutSoftLogin' );
 define ( 'DEFAULT_POPUP_LAYOUT', 'LayoutSoftPopup' );
 */

/* components */
//define('CDT_CMP_PATH', MVC_PATH  . 'codnet_ui_components_2_0_5/');
define('CDT_CMP_PATH', MVC_PATH  . 'codnet_ui_components/');
include_once (CDT_CMP_PATH . 'conf/init.php');
//define ( 'CMP_GRID_DEFAULT_LAYOUT', DEFAULT_PANEL_LAYOUT );

//define ( 'CDT_CMP_GRID_RICH_STYLE_CSS', WEB_PATH . "css/soft/grid.css" );
define ( 'CDT_CMP_GRID_RICH_STYLE_CSS', WEB_PATH . "css/entitygrid/entitygrid.css" );


/* entities */
define('CDT_ENTITIES_PATH', MVC_PATH  . 'codnet_entities/');
include_once (CDT_ENTITIES_PATH . 'conf/init.php');
date_default_timezone_set("America/Argentina/Buenos_Aires");

if (!defined('CLASS_PATH')) {
	$classpath = array();
	$classpath[] = CDT_CORE_PATH;
	$classpath[] = CDT_UI_PATH;
	$classpath[] = CDT_SECURE_PATH;
	$classpath[] = CYT_SECURE_PATH;
	$classpath[] = CDT_UI_SMILE_PATH;
	//$classpath[] = CDT_UI_THEME_SOFT_PATH;
	$classpath[] = CDT_CMP_PATH;
	$classpath[] = CDT_ENTITIES_PATH;
	//$classpath[] = CDT_DOCTRINE_PATH;
	$classpath[] = CYT_PATH;
	// $classpath[] = MDC_PATH_TEST;
	define('CLASS_PATH', implode(",", $classpath));
}


//para optimizar el class_path.
if (!defined('CLASS_PATH_EXCLUDE')) {
	$exclude = array();
	$exclude[] = CDT_CORE_PATH . 'view/templates';
	$exclude[] = CDT_UI_PATH . 'view/templates';
	$exclude[] = CDT_SECURE_PATH . 'view/templates';
	$exclude[] = CDT_UI_SMILE_PATH . 'view/templates';
	$exclude[] = CDT_ENTITIES_PATH . 'view/templates';
	$exclude[] = CYT_PATH . 'view/templates';
	$exclude[] = '.svn';
	define('CLASS_PATH_EXCLUDE', implode(",", $exclude));
}
?>