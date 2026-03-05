<?php
/**
 * se define el esquema de la bbdd.
 * 
 * @author bernardo
 * @since 12-04-2011
 * 
 */

/* cdt_action_function */
define( 'CDT_SECURE_TABLE_CDTACTIONFUNCTION', 'cdt_action_function');
define( 'CDT_SECURE_TABLE_CDTACTIONFUNCTION_CD_ACTIONFUNCTION', 'cd_actionfunction');
define( 'CDT_SECURE_TABLE_CDTACTIONFUNCTION_CD_FUNCTION', 'cd_function');
define( 'CDT_SECURE_TABLE_CDTACTIONFUNCTION_DS_ACTION', 'ds_action');

/* cdt_user */
define( 'CDT_SECURE_TABLE_CDTUSER',  'cdt_user');
define( 'CDT_SECURE_TABLE_CDTUSER_CD_USER',  'cd_user');
define( 'CDT_SECURE_TABLE_CDTUSER_DS_USERNAME',  'ds_username' );
define( 'CDT_SECURE_TABLE_CDTUSER_DS_NAME',  'ds_name' );
define( 'CDT_SECURE_TABLE_CDTUSER_DS_EMAIL',  'ds_email' );
define( 'CDT_SECURE_TABLE_CDTUSER_DS_PASSWORD',  'ds_password' );
define( 'CDT_SECURE_TABLE_CDTUSER_CD_USERGROUP',  'cd_usergroup' );
define( 'CDT_SECURE_TABLE_CDTUSER_DS_PHONE',  'ds_phone' );
define( 'CDT_SECURE_TABLE_CDTUSER_DS_ADDRESS',  'ds_address' );
define( 'CDT_SECURE_TABLE_CDTUSER_DS_IPS',  'ds_ips' );
define( 'CDT_SECURE_TABLE_CDTUSER_NU_ATTEMPS',  'nu_attemps' );

/* cdt_usergroup */
define( 'CDT_SECURE_TABLE_CDTUSERGROUP', 'cdt_usergroup');
define( 'CDT_SECURE_TABLE_CDTUSERGROUP_CD_USERGROUP', 'cd_usergroup');
define( 'CDT_SECURE_TABLE_CDTUSERGROUP_DS_USERGROUP', 'ds_usergroup');

/* cdt_function */
define( 'CDT_SECURE_TABLE_CDTFUNCTION', 'cdt_function');
define( 'CDT_SECURE_TABLE_CDTFUNCTION_CD_FUNCTION', 'cd_function');
define( 'CDT_SECURE_TABLE_CDTFUNCTION_DS_FUNCTION', 'ds_function');

/* cdt_usergroup_function */
define( 'CDT_SECURE_TABLE_CDTUSERGROUPFUNCTION', 'cdt_usergroup_function');
define( 'CDT_SECURE_TABLE_CDTUSERGROUPFUNCTION_CD_USERGROUPFUNCTION', 'cd_usergroup_function');
define( 'CDT_SECURE_TABLE_CDTUSERGROUPFUNCTION_CD_USERGROUP', 'cd_usergroup');
define( 'CDT_SECURE_TABLE_CDTUSERGROUPFUNCTION_CD_FUNCTION', 'cd_function');

/* cdt_menugroup */
define( 'CDT_SECURE_TABLE_CDTMENUGROUP', 'cdt_menugroup');
define( 'CDT_SECURE_TABLE_CDTMENUGROUP_CD_MENUGROUP', 'cd_menugroup');
define( 'CDT_SECURE_TABLE_CDTMENUGROUP_DS_NAME', 'ds_name');
define( 'CDT_SECURE_TABLE_CDTMENUGROUP_NU_ORDER', 'nu_order');
define( 'CDT_SECURE_TABLE_CDTMENUGROUP_NU_WIDTH', 'nu_width');
define( 'CDT_SECURE_TABLE_CDTMENUGROUP_DS_ACTION', 'ds_action');
define( 'CDT_SECURE_TABLE_CDTMENUGROUP_DS_CSSCLASS', 'ds_cssclass');

/* cdt_menuoption */
define( 'CDT_SECURE_TABLE_CDTMENUOPTION', 'cdt_menuoption');
define( 'CDT_SECURE_TABLE_CDTMENUOPTION_CD_MENUOPTION', 'cd_menuoption');
define( 'CDT_SECURE_TABLE_CDTMENUOPTION_DS_NAME', 'ds_name');
define( 'CDT_SECURE_TABLE_CDTMENUOPTION_DS_HREF', 'ds_href');
define( 'CDT_SECURE_TABLE_CDTMENUOPTION_CD_FUNCTION', 'cd_function');
define( 'CDT_SECURE_TABLE_CDTMENUOPTION_NU_ORDER', 'nu_order');
define( 'CDT_SECURE_TABLE_CDTMENUOPTION_CD_MENUGROUP', 'cd_menugroup');
define( 'CDT_SECURE_TABLE_CDTMENUOPTION_DS_CSSCLASS', 'ds_cssclass');
define( 'CDT_SECURE_TABLE_CDTMENUOPTION_DS_DESCRIPTION', 'ds_description');

/* cdt_registration */
//define( 'CDT_SECURE_TABLE_CDTREGISTRATION', 'cdt_registration');
define( 'CDT_SECURE_TABLE_CDTREGISTRATION_CD_REGISTRATION', 'cd_registration');
define( 'CDT_SECURE_TABLE_CDTREGISTRATION_DS_ACTIVATIONCODE', 'ds_activationcode');
define( 'CDT_SECURE_TABLE_CDTREGISTRATION_DT_DATE', 'dt_date');
define( 'CDT_SECURE_TABLE_CDTREGISTRATION_DS_USERNAME', 'ds_username');
define( 'CDT_SECURE_TABLE_CDTREGISTRATION_DS_NAME', 'ds_name');
define( 'CDT_SECURE_TABLE_CDTREGISTRATION_DS_EMAIL', 'ds_email');
define( 'CDT_SECURE_TABLE_CDTREGISTRATION_DS_PASSWORD', 'ds_password');
define( 'CDT_SECURE_TABLE_CDTREGISTRATION_DS_PHONE', 'ds_phone');
define( 'CDT_SECURE_TABLE_CDTREGISTRATION_DS_ADDRESS', 'ds_address');

?>