<?php 

//login
define('CDT_SECURE_LOGIN_MAX_ATTEMPS', 5);

define("CDT_SECURE_LOGIN_ACTION", 'doAction?action=login');
define("CDT_SECURE_LOGIN_INIT_ACTION", 'login_init');
define("CDT_SECURE_ACCESS_DENIED_ACTION", 'login_init');

define('CDT_SECURE_LOGIN_TITLE', CDT_MVC_APP_TITLE);
define('CDT_SECURE_LOGIN_SUBTITLE', CDT_MVC_APP_SUBTITLE);


//registraciones
//define('CDT_SECURE_REGISTRATION_ENABLED', true);
define('CDT_SECURE_REGISTER_TITLE', CDT_MVC_APP_TITLE);
define('CDT_SECURE_REGISTER_SUBTITLE', CDT_MVC_APP_SUBTITLE);
/*define("CDT_SECURE_REGISTRATION_LAYOUT", 'CdtLayoutBasic');
define("CDT_SECURE_ACTIVATE_REGISTRATION_ACTION", 'doAction?action=activate_registration');
define("CDT_SECURE_USERGROUP_DEFAULT_ID", 1);
define("CDT_SECURE_REGISTRATION_TERMS_LAYOUT", "CdtLayoutBasicAjax");*/

//recuperar clave
/*define("CDT_SECURE_FORGOT_PASSWORD_LAYOUT", 'LayoutSmileLogin');
define("CDT_SECURE_FORGOT_PASSWORD_INIT_ACTION", 'doAction?action=forgot_password_init');
define("CDT_SECURE_FORGOT_PASSWORD_ACTION", 'doAction?action=forgot_password');*/


?>