<?php

/**
 * se definen los mensajes del sistema en ingl�s.
 * 
 * @author modelBuilder
 * 
 */

//excepciones
define('CDT_SECURE_MSG_EXCEPTION_CANNOT_DELETE_ITEMS_RELATED', "Delete item is unavailable. <br/> One or more items are related to another entities", true);

//mensajes para login
define('CDT_SECURE_MSG_INVALID_PASSWORD', 'Invalid password - You have $1 more chances to try', true);
define('CDT_SECURE_MSG_INVALID_PASSWORD_LAST_CHANCE', 'Invalid password - Last chance: if you put the wrong password again, your user are going to be blocked.', true);
define('CDT_SECURE_MSG_INVALID_USER', 'Invalid user', true);
define('CDT_SECURE_MSG_INVALID_IP', 'You are trying to logged in from an invalid IP', true); 
define('CDT_SECURE_MSG_USER_BLOCKED', 'Your user was blocked because you tried to logged in more than the maximum attemps available', true);

define( "CDT_SECURE_MSG_LOGIN_REQUIRED",  'Login required', true );
define( "CDT_SECURE_MSG_PERMISSION_DENIED",  'Permission denied', true );

//login web.
define( "CDT_SECURE_MSG_BTN_LOGIN_WEB", 'Sign in', true);
define( "CDT_SECURE_MSG_LOGIN_WEB_TITULO", 'Sign in', true);
define( "CDT_SECURE_MSG_LINK_REGISTRARSE", "New user? Welcome! Sign up <a href='" .WEB_PATH . "doAction?action=signup_init' >here</a>&nbsp;&nbsp;", true);
define( "CDT_SECURE_MSG_INGRESE_USERNAME", "Fill username", true);
define( "CDT_SECURE_MSG_INGRESE_PASSWORD", "Fill password", true);
define( "CDT_SECURE_MSG_RECUPERAR_PASSWORD", "Forgot your password?", true);

//solicitar clave.
//define( "CDT_SECURE_MSG_SOLICITAR_CLAVE", "If you have forgotten your password and would like to change it, enter your email address and we'll send you a new password reset request.", true);
define( "CDT_SECURE_MSG_FORGOT_PASSWORD_TITLE", "Forgot password", true);
define( "CDT_SECURE_MSG_FORGOT_PASSWORD", "If you have forgotten your password and would like to change it, enter your username or email address and we'll send you a new password reset request", true);
define( "CDT_SECURE_LBL_FORGOT_PASSWORD_EMAIL", "Username or email", true);
define( "CDT_SECURE_MSG_FORGOT_PASSWORD_FILL_EMAIL", "Username or email required", true);
define( "CDT_SECURE_BTN_FORGOT_PASSWORD_RESETEAR", "Reset password", true);
define( "CDT_SECURE_MSG_FORGOT_PASSWORD_NEW_PASSWORD_SENT", "A new password has been sent to your email account", true);
         
define( "CDT_SECURE_MSG_FORGOT_PASSWORD_MAIL_SUBJECT", "[". CDT_MVC_APP_TITLE . '] - New password request', true);

//nuevos usuarios
define( "CDT_SECURE_MSG_NEW_USER_MAIL_SUBJECT",  "[". CDT_MVC_APP_TITLE . '] - New User', true);

//registraci�n.
define( "CDT_SECURE_MSG_SECURITYCODE", 'Security code', true);
define( "CDT_SECURE_MSG_REPEAT_PASSWORD", 'Repeat password', true);
define( "CDT_SECURE_MSG_READ_TERMS", 'Accept terms & conditions', true);

define( "CDT_SECURE_MSG_USERNAME_REQUIRED", 'Fill username', true);
define( "CDT_SECURE_MSG_PASSWORD_REQUIRED", 'Fill password', true);
define( "CDT_SECURE_MSG_SECURITYCODE_REQUIRED", 'Fill security code', true);

define( "CDT_SECURE_MSG_EMAIL_INVALID", 'Invalid e-mail', true);
define( "CDT_SECURE_MSG_PASSWORDS_INVALID", 'Invalid passwords', true);
define( "CDT_SECURE_MSG_RELOAD_IMAGE", 'Reload image', true);
define( "CDT_SECURE_LBL_BTN_REGISTER", 'Register', true);
define( 'CDT_SECURE_MSG_REGISTRATION_SIGNUP_TITLE', 'Sign up', true);
define( "CDT_SECURE_MSG_REGISTRATION_SIGNUP_SUCCESS", 'Thanks for registering!  You will receive an email with instructions on how to activate your account in a few minutes.', true);
define( "CDT_SECURE_MSG_ACTIVATE_REGISTRATION_SUCCESS", "Your account has been activated successfully.");
define( "CDT_SECURE_MSG_ACTIVATION_CODE_INVALID",  "The activation code is not valid", true );
define( "CDT_SECURE_MSG_ACTIVATION_CODE_EXPIRED",  '"The activation code has been expired"', true );
define( "CDT_SECURE_MSG_REGISTRATION_EMAIL_SUBJECT",  'Sign up', true );
define( "CDT_SECURE_MSG_REGISTRATION_USERNAME_DUPLICATED",  'The username already exist', true );
define( "CDT_SECURE_MSG_REGISTRATION_EMAIL_DUPLICATED",  'The email already exist', true );

define( "CDT_SECURE_MSG_REGISTRATION_TERMS_TITLE", 'Terms & Conditions', true);
define( "CDT_SECURE_MSG_REGISTRATION_TERMS_ACCEPT", 'I accept terms & conditions', true);


//mensajes comunes.
define('CDT_SECURE_LBL_BACK', 'Back', true);
define('CDT_SECURE_LBL_SAVE', 'Save', true);
define('CDT_SECURE_LBL_CANCEL', 'Cancel', true);
define('CDT_SECURE_LBL_YES', 'Yes', true);
define('CDT_SECURE_LBL_NO', 'No', true);
define('CDT_SECURE_LBL_SELECT', 'Select', true);
define('CDT_SECURE_MSG_REQUIRED_FIELDS', 'Required fields', true);


define('CDT_SECURE_MSG_CONFIRM_UNBLOCK_QUESTION', 'Do you confirm unblock selected user?', true);
define('CDT_SECURE_MSG_CONFIRM_UNBLOCK_TITLE', 'Unblock Confirmation', true);

define('CDT_SECURE_MSG_CONFIRM_DELETE_QUESTION', 'Do you confirm delete?', true);
define('CDT_SECURE_MSG_CONFIRM_DELETE_TITLE', 'Confirmation', true);

//t�tulos de las actions de actionfunction
define('CDT_SECURE_MSG_CDTACTIONFUNCTION_TITLE_LIST', 'List Action-Functions', true);
define('CDT_SECURE_MSG_CDTACTIONFUNCTION_TITLE_ADD', 'Add Action-function', true);
define('CDT_SECURE_MSG_CDTACTIONFUNCTION_TITLE_UPDATE', 'Update Action-function', true);
define('CDT_SECURE_MSG_CDTACTIONFUNCTION_TITLE_VIEW', 'View Action-function', true);

define('CDT_SECURE_MSG_CDTACTIONFUNCTION_CD_ACTIONFUNCTION_REQUIRED', 'Code is required', true);
define('CDT_SECURE_MSG_CDTACTIONFUNCTION_CD_FUNCTION_REQUIRED', 'Function is required', true);
define('CDT_SECURE_MSG_CDTACTIONFUNCTION_DS_ACTION_REQUIRED', 'Action is required', true);

//t�tulos de las actions de function
define('CDT_SECURE_MSG_CDTFUNCTION_TITLE_LIST', 'List Functions', true);
define('CDT_SECURE_MSG_CDTFUNCTION_TITLE_ADD', 'Add Function', true);
define('CDT_SECURE_MSG_CDTFUNCTION_TITLE_UPDATE', 'Update Function', true);
define('CDT_SECURE_MSG_CDTFUNCTION_TITLE_VIEW', 'View Function', true);

define('CDT_SECURE_MSG_CDTFUNCTION_CD_FUNCTION_REQUIRED', 'Code is required', true);
define('CDT_SECURE_MSG_CDTFUNCTION_DS_FUNCTION_REQUIRED', 'Function is required', true);

//t�tulos de las actions de menugroup
define('CDT_SECURE_MSG_CDTMENUGROUP_TITLE_LIST', 'List Menu groups', true);
define('CDT_SECURE_MSG_CDTMENUGROUP_TITLE_ADD', 'Add Menu group', true);
define('CDT_SECURE_MSG_CDTMENUGROUP_TITLE_UPDATE', 'Update Menu group', true);
define('CDT_SECURE_MSG_CDTMENUGROUP_TITLE_VIEW', 'View Menu group', true);

define('CDT_SECURE_MSG_CDTMENUGROUP_CD_MENUGROUP_REQUIRED', 'Code is required', true);
define('CDT_SECURE_MSG_CDTMENUGROUP_NU_ORDER_REQUIRED', 'Order is required', true);
define('CDT_SECURE_MSG_CDTMENUGROUP_NU_WIDTH_REQUIRED', 'Width is required', true);
define('CDT_SECURE_MSG_CDTMENUGROUP_DS_NAME_REQUIRED', 'Name is required', true);
define('CDT_SECURE_MSG_CDTMENUGROUP_DS_ACTION_REQUIRED', 'Action is required', true);
define('CDT_SECURE_MSG_CDTMENUGROUP_DS_CSSCLASS_REQUIRED', 'Style is required', true);

//t�tulos de las actions de menuoption
define('CDT_SECURE_MSG_CDTMENUOPTION_TITLE_LIST', 'List Menu options', true);
define('CDT_SECURE_MSG_CDTMENUOPTION_TITLE_ADD', 'Add Menu option', true);
define('CDT_SECURE_MSG_CDTMENUOPTION_TITLE_UPDATE', 'Update Menu option', true);
define('CDT_SECURE_MSG_CDTMENUOPTION_TITLE_VIEW', 'View Menu option', true);

define('CDT_SECURE_MSG_CDTMENUOPTION_CD_MENUOPTION_REQUIRED', 'Code is required', true);
define('CDT_SECURE_MSG_CDTMENUOPTION_DS_NAME_REQUIRED', 'Name is required', true);
define('CDT_SECURE_MSG_CDTMENUOPTION_DS_HREF_REQUIRED', 'Href is required', true);
define('CDT_SECURE_MSG_CDTMENUOPTION_CD_FUNCTION_REQUIRED', 'Function is required', true);
define('CDT_SECURE_MSG_CDTMENUOPTION_NU_ORDER_REQUIRED', 'Order is required', true);
define('CDT_SECURE_MSG_CDTMENUOPTION_CD_MENUGROUP_REQUIRED', 'Menu group is required', true);
define('CDT_SECURE_MSG_CDTMENUOPTION_DS_CSSCLASS_REQUIRED', 'Style is required', true);
define('CDT_SECURE_MSG_CDTMENUOPTION_DS_DESCRIPTION_REQUIRED', 'Description is required', true);

//t�tulos de las actionsde registration
define('CDT_SECURE_MSG_CDTREGISTRATION_TITLE_LIST', 'List Registrations', true);
define('CDT_SECURE_MSG_CDTREGISTRATION_TITLE_ADD', 'Add Registration', true);
define('CDT_SECURE_MSG_CDTREGISTRATION_TITLE_UPDATE', 'Update Registration', true);
define('CDT_SECURE_MSG_CDTREGISTRATION_TITLE_VIEW', 'View Registration', true);

define('CDT_SECURE_MSG_CDTREGISTRATION_CD_REGISTRATION_REQUIRED', 'Code is required', true);
define('CDT_SECURE_MSG_CDTREGISTRATION_DS_ACTIVATIONCODE_REQUIRED', 'Activation code is required', true);
define('CDT_SECURE_MSG_CDTREGISTRATION_DT_DATE_REQUIRED', 'Date is required', true);
define('CDT_SECURE_MSG_CDTREGISTRATION_DS_USERNAME_REQUIRED', 'Username is required', true);
define('CDT_SECURE_MSG_CDTREGISTRATION_DS_NAME_REQUIRED', 'Name is required', true);
define('CDT_SECURE_MSG_CDTREGISTRATION_DS_EMAIL_REQUIRED', 'E-mail is required', true);
define('CDT_SECURE_MSG_CDTREGISTRATION_DS_PASSWORD_REQUIRED', 'Password is required', true);
define('CDT_SECURE_MSG_CDTREGISTRATION_DS_PHONE_REQUIRED', 'Phone is required', true);
define('CDT_SECURE_MSG_CDTREGISTRATION_DS_ADDRESS_REQUIRED', 'Address is required', true);

//t�tulos de las actions de user
define('CDT_SECURE_MSG_CDTUSER_TITLE_LIST', 'List Users', true);
define('CDT_SECURE_MSG_CDTUSER_TITLE_ADD', 'Add User', true);
define('CDT_SECURE_MSG_CDTUSER_TITLE_UPDATE', 'Update User', true);
define('CDT_SECURE_MSG_CDTUSER_TITLE_VIEW', 'View User', true);
define('CDT_SECURE_MSG_CDTUSERPROFILE_TITLE_UPDATE', 'Update My Profile', true);
define('CDT_SECURE_MSG_CDTUSER_TITLE_UPDATE', 'Update User', true);
define('CDT_SECURE_MSG_CDTUSER_TITLE_UNBLOCK', 'Unblock User', true);

define('CDT_SECURE_MSG_CDTUSER_CD_USER_REQUIRED', 'Code is required', true);
define('CDT_SECURE_MSG_CDTUSER_DS_USERNAME_REQUIRED', 'Username is required', true);
define('CDT_SECURE_MSG_CDTUSER_DS_USERNAME_DUPLICATED', 'Username is duplicated', true);
define('CDT_SECURE_MSG_CDTUSER_DS_NAME_REQUIRED', 'Name is required', true);
define('CDT_SECURE_MSG_CDTUSER_DS_EMAIL_REQUIRED', 'E-mail is required', true);
define('CDT_SECURE_MSG_CDTUSER_DS_EMAIL_DUPLICATED', 'E-mail is duplicated', true);
define('CDT_SECURE_MSG_CDTUSER_DS_PASSWORD_REQUIRED', 'Password is required', true);
define('CDT_SECURE_MSG_CDTUSER_CD_USERGROUP_REQUIRED', 'User group is required', true);
define('CDT_SECURE_MSG_CDTUSER_DS_PHONE_REQUIRED', 'Phone is required', true);
define('CDT_SECURE_MSG_CDTUSER_DS_ADDRESS_REQUIRED', 'Address is required', true);

define('CDT_SECURE_MSG_CDTUSER_NEW_USER_PASSWORD', 'You must send this credentials to the new user: <b>$1 / $2</b>', true);
define('CDT_SECURE_MSG_CDTUSER_NO_IP', 'There is not any IP defined', true );
define('CDT_SECURE_MSG_CDTUSER_IP_INVALID', 'Invalid IP format', true );
define('CDT_SECURE_MSG_CDTUSER_IP_TITLE', 'Limit user by IP', true );
define('CDT_SECURE_MSG_CDTUSER_IP_ADD', 'Add IP', true );
define('CDT_SECURE_MSG_CDTUSER_IP_DELETE', 'Delete IP', true );
define('CDT_SECURE_MSG_CDTUSER_IP_NOT_EXISTS', 'The IP $1 not exists', true );
define('CDT_SECURE_MSG_CDTUSER_IP_ALREADY_EXISTS', 'The IP $1 already exists', true );
define('CDT_SECURE_MSG_CDTUSER_IP_DELETE_CONFIRM', 'Do you confirm delete selected IP?', true );

//t�tulos de las actions de usergroup
define('CDT_SECURE_MSG_CDTUSERGROUP_TITLE_LIST', 'List User groups', true);
define('CDT_SECURE_MSG_CDTUSERGROUP_TITLE_ADD', 'Add User group', true);
define('CDT_SECURE_MSG_CDTUSERGROUP_TITLE_UPDATE', 'Update User group', true);
define('CDT_SECURE_MSG_CDTUSERGROUP_TITLE_VIEW', 'View User group', true);

define('CDT_SECURE_MSG_CDTUSERGROUP_CD_USERGROUP_REQUIRED', 'Code is required', true);
define('CDT_SECURE_MSG_CDTUSERGROUP_DS_USERGROUP_REQUIRED', 'User group is required', true);

define('CDT_SECURE_MSG_USERGROUPFUNCTION_TITLE_ASSIGN', 'User group / Assign functions', true);

//t�tulos de las actions
define('CDT_SECURE_MSG_CDTUSERGROUPFUNCTION_TITLE_LIST', 'List User group-Functions', true);
define('CDT_SECURE_MSG_CDTUSERGROUPFUNCTION_TITLE_ADD', 'Add User group-function', true);
define('CDT_SECURE_MSG_CDTUSERGROUPFUNCTION_TITLE_UPDATE', 'Update User group-function', true);
define('CDT_SECURE_MSG_CDTUSERGROUPFUNCTION_TITLE_VIEW', 'View User group-function', true);

define('CDT_SECURE_MSG_CDTUSERGROUPFUNCTION_CD_USERGROUP_FUNCTION_REQUIRED', 'Code is required', true);
define('CDT_SECURE_MSG_CDTUSERGROUPFUNCTION_CD_USERGROUP_REQUIRED', 'User group is required', true);
define('CDT_SECURE_MSG_CDTUSERGROUPFUNCTION_CD_FUNCTION_REQUIRED', 'Function is required', true);

define('CDT_SECURE_LBL_USERGROUP_FUNCTIONS_EDIT', 'Assign Functions', true);


include ('messages_labels_en.php');

?>
