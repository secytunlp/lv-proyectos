<?php

/**
 * se definen los mensajes del sistema en español.
 * 
 * @author modelBuilder
 * 
 */


define('CDT_SECURE_MSG_EXCEPTION_CANNOT_DELETE_ITEMS_RELATED', "No se puede eliminar. <br/> Existen otras entidades relacionadas.");
define('CDT_SECURE_MSG_EXCEPTION_DUPLICATE_DATA', "No se puede guardar.<br/> Existen datos duplicados.");

//mensajes para login
define('CDT_SECURE_MSG_INVALID_USER', 'Usuario incorrecto');
define('CDT_SECURE_MSG_INVALID_PASSWORD', 'Password incorrecta - Tienes $1 chances para seguir intentando');
define('CDT_SECURE_MSG_INVALID_PASSWORD_LAST_CHANCE', 'Password incorrecta - Última chacnce: si vuelves a ingresar mal la password, tu usuario será bloqueado.');
define('CDT_SECURE_MSG_INVALID_IP', 'La IP de la cual estás intentando loguearte no está registrada en el sistema');
define('CDT_SECURE_MSG_USER_BLOCKED', 'Tu usuario fue bloqueado ya que has intentado loguearte más de las veces permitidas');
define('CDT_SECURE_MSG_INVALID_PASSWORD_2', 'Password incorrecta');
define('CDT_SECURE_MSG_INVALID_CSRF', 'Token CSRF no válido. Esta solicitud no se puede procesar debido a un problema de seguridad');

define( "CDT_SECURE_MSG_LOGIN_REQUIRED",  'Login requerido' );
define( "CDT_SECURE_MSG_PERMISSION_DENIED",  'Acceso denegado' );

//login web.
define( "CDT_SECURE_MSG_BTN_LOGIN_WEB", 'Ingresar');
define( "CDT_SECURE_MSG_LOGIN_WEB_TITULO", 'Ingresar');
define( "CDT_SECURE_MSG_LINK_REGISTRARSE", "Nuevo usuario? Bienvenido! Reg&iacute;strese <a href='" .WEB_PATH . "doAction?action=signup_init' >aqu&iacute;</a>&nbsp;&nbsp;");
define( "CDT_SECURE_MSG_INGRESE_USERNAME", "Ingrese nombre de usuario");
define( "CDT_SECURE_MSG_INGRESE_PASSWORD", "Ingrese la password");
define( "CDT_SECURE_MSG_RECUPERAR_PASSWORD", "Olvidé mi contraseña");

//solicitar clave.
//define( "CDT_SECURE_MSG_SOLICITAR_CLAVE", "If you have forgotten your password and would like to change it, enter your email address and we'll send you a new password reset request.");
define( "CDT_SECURE_MSG_FORGOT_PASSWORD_TITLE", "Solicitar password");
//define( "CDT_SECURE_MSG_FORGOT_PASSWORD", "Si olvidó su password y quiere cambiarla, ingrese su nombre de usuario o e-mail y le enviaremos una nueva");
//define( "CDT_SECURE_LBL_FORGOT_PASSWORD_EMAIL", "Nombre de usuario o e-mail");
//define( "CDT_SECURE_MSG_FORGOT_PASSWORD_FILL_EMAIL", "Nombre de usuario o e-mail requerido");
define( "CDT_SECURE_BTN_FORGOT_PASSWORD_RESETEAR", "Resetear");
//define( "CDT_SECURE_MSG_FORGOT_PASSWORD_NEW_PASSWORD_SENT", "Te enviamos una nueva password a tu e-mail");
define('CDT_SECURE_MSG_CDTUSER_NO_IP', 'No hay ninguna IP definida' );
define('CDT_SECURE_MSG_CDTUSER_IP_INVALID', 'Formato de IP no válido' );
define('CDT_SECURE_MSG_CDTUSER_IP_TITLE', 'Limitar usuario por IP' );
define('CDT_SECURE_MSG_CDTUSER_IP_ADD', 'Agregar IP' );
define('CDT_SECURE_MSG_CDTUSER_IP_DELETE', 'Eliminar IP' );
define('CDT_SECURE_MSG_CDTUSER_IP_NOT_EXISTS', 'La IP $1 no existe' );
define('CDT_SECURE_MSG_CDTUSER_IP_ALREADY_EXISTS', 'La IP $1 ya existe' );
define('CDT_SECURE_MSG_CDTUSER_IP_DELETE_CONFIRM', 'Confirma eliminar la IP seleccionada?' );

define( "CDT_SECURE_MSG_FORGOT_PASSWORD_MAIL_SUBJECT",  CDT_MVC_APP_TITLE . ' - Solicitud de nueva password');

//nuevos usuarios.
define( "CDT_SECURE_MSG_NEW_USER_MAIL_SUBJECT",  CDT_MVC_APP_TITLE . ' - Nuevo Usuario');

//registración.
define( "CDT_SECURE_MSG_SECURITYCODE", 'Código de seguridad');
define( "CDT_SECURE_MSG_REPEAT_PASSWORD", 'Repetir password');
define( "CDT_SECURE_MSG_READ_TERMS", 'Aceptar t&eacute;rminos & condiciones');

//define( "CDT_SECURE_MSG_USERNAME_REQUIRED", 'Nombre de usuario requerido');
define( "CDT_SECURE_MSG_PASSWORD_REQUIRED", 'Password requerida');
define( "CDT_SECURE_MSG_SECURITYCODE_REQUIRED", 'Código de seguridad requerido');

define( "CDT_SECURE_MSG_EMAIL_INVALID", 'E-mail no v&aacute;lido');
define( "CDT_SECURE_MSG_PASSWORDS_INVALID", 'Las passwords no coinciden');
define( "CDT_SECURE_MSG_RELOAD_IMAGE", 'Cambiar imagen');
define( "CDT_SECURE_LBL_BTN_REGISTER", 'Registrarse');
define( 'CDT_SECURE_MSG_REGISTRATION_SIGNUP_TITLE', 'Registrarse');
//define( "CDT_SECURE_MSG_REGISTRATION_SIGNUP_SUCCESS", 'Gracias por registrarte! Recibir&aacute;s un e-mail con instrucciones para activar tu cuenta.');
//define( "CDT_SECURE_MSG_ACTIVATE_REGISTRATION_SUCCESS", "Tu cuenta ha sido activada!.");
define( "CDT_SECURE_MSG_ACTIVATION_CODE_INVALID",  "El código de activación no es v&aacute;lido" );
define( "CDT_SECURE_MSG_ACTIVATION_CODE_EXPIRED",  "El código de activación caducó" );
define( "CDT_SECURE_MSG_REGISTRATION_EMAIL_SUBJECT",  'Registración' );
//define( "CDT_SECURE_MSG_REGISTRATION_USERNAME_DUPLICATED",  'El nombre de usuario no est&aacute; disponible' );
define( "CDT_SECURE_MSG_REGISTRATION_EMAIL_DUPLICATED",  'El e-mail no est&aacute; disponible' );

define( "CDT_SECURE_MSG_REGISTRATION_TERMS_TITLE", 'T&eacute;rminos & Condiciones');
define( "CDT_SECURE_MSG_REGISTRATION_TERMS_ACCEPT", 'He le&iacute;do y acepto los t&eacute;rminos y condiciones');


//mensajes comunes.
define('CDT_SECURE_LBL_BACK', 'Volver');
define('CDT_SECURE_LBL_SAVE', 'Guardar');
define('CDT_SECURE_LBL_CANCEL', 'Cancelar');
define('CDT_SECURE_LBL_YES', 'Si');
define('CDT_SECURE_LBL_NO', 'No');
define('CDT_SECURE_LBL_SELECT', 'Seleccionar');
define('CDT_SECURE_MSG_REQUIRED_FIELDS', 'Campos requeridos');

define('CDT_SECURE_MSG_CONFIRM_DELETE_QUESTION', 'Confirma eliminar?');
define('CDT_SECURE_MSG_CONFIRM_DELETE_TITLE', 'Confirmar');

//títulos de las actions
define('CDT_SECURE_MSG_CDTACTIONFUNCTION_TITLE_LIST', 'Listar Acción-Funciones');
define('CDT_SECURE_MSG_CDTACTIONFUNCTION_TITLE_ADD', 'Agregar Acción-Función');
define('CDT_SECURE_MSG_CDTACTIONFUNCTION_TITLE_UPDATE', 'Modificar Acción-Función');
define('CDT_SECURE_MSG_CDTACTIONFUNCTION_TITLE_VIEW', 'Ver Acción-Función');

define('CDT_SECURE_MSG_CDTACTIONFUNCTION_CD_ACTIONFUNCTION_REQUIRED', 'Código requerido');
define('CDT_SECURE_MSG_CDTACTIONFUNCTION_CD_FUNCTION_REQUIRED', 'Función requerida');
define('CDT_SECURE_MSG_CDTACTIONFUNCTION_DS_ACTION_REQUIRED', 'Acción requerida');

//títulos de las actions
define('CDT_SECURE_MSG_CDTFUNCTION_TITLE_LIST', 'Listar Funciones');
define('CDT_SECURE_MSG_CDTFUNCTION_TITLE_ADD', 'Agregar Función');
define('CDT_SECURE_MSG_CDTFUNCTION_TITLE_UPDATE', 'Modificar Función');
define('CDT_SECURE_MSG_CDTFUNCTION_TITLE_VIEW', 'Visualizar Función');

define('CDT_SECURE_MSG_CDTFUNCTION_CD_FUNCTION_REQUIRED', 'Código requerido');
define('CDT_SECURE_MSG_CDTFUNCTION_DS_FUNCTION_REQUIRED', 'Función requerida');

//títulos de las actions
define('CDT_SECURE_MSG_CDTMENUGROUP_TITLE_LIST', 'Listar Grupos de Menú');
define('CDT_SECURE_MSG_CDTMENUGROUP_TITLE_ADD', 'Agregar Grupo de Menú');
define('CDT_SECURE_MSG_CDTMENUGROUP_TITLE_UPDATE', 'Modificar Grupo de Menú');
define('CDT_SECURE_MSG_CDTMENUGROUP_TITLE_VIEW', 'Visualizar Grupo de Menú');

define('CDT_SECURE_MSG_CDTMENUGROUP_CD_MENUGROUP_REQUIRED', 'Código requerido');
define('CDT_SECURE_MSG_CDTMENUGROUP_NU_ORDER_REQUIRED', 'Orden requerido');
define('CDT_SECURE_MSG_CDTMENUGROUP_NU_WIDTH_REQUIRED', 'Ancho requerido');
define('CDT_SECURE_MSG_CDTMENUGROUP_DS_NAME_REQUIRED', 'Nombre requerido');
define('CDT_SECURE_MSG_CDTMENUGROUP_DS_ACTION_REQUIRED', 'Acción requerida');
define('CDT_SECURE_MSG_CDTMENUGROUP_DS_CSSCLASS_REQUIRED', 'Estilo requerido');

//títulos de las actions
define('CDT_SECURE_MSG_CDTMENUOPTION_TITLE_LIST', 'Listar Opciones de Menú');
define('CDT_SECURE_MSG_CDTMENUOPTION_TITLE_ADD', 'Agregar Opción de Menú');
define('CDT_SECURE_MSG_CDTMENUOPTION_TITLE_UPDATE', 'Modificar Opción de Menú');
define('CDT_SECURE_MSG_CDTMENUOPTION_TITLE_VIEW', 'Visualizar Opción de Menú');

define('CDT_SECURE_MSG_CDTMENUOPTION_CD_MENUOPTION_REQUIRED', 'Código requerido');
define('CDT_SECURE_MSG_CDTMENUOPTION_DS_NAME_REQUIRED', 'Nombre requerido');
define('CDT_SECURE_MSG_CDTMENUOPTION_DS_HREF_REQUIRED', 'Href requerido');
define('CDT_SECURE_MSG_CDTMENUOPTION_CD_FUNCTION_REQUIRED', 'Función requerida');
define('CDT_SECURE_MSG_CDTMENUOPTION_NU_ORDER_REQUIRED', 'Orden requerido');
define('CDT_SECURE_MSG_CDTMENUOPTION_CD_MENUGROUP_REQUIRED', 'Grupo de Menú requerido');
define('CDT_SECURE_MSG_CDTMENUOPTION_DS_CSSCLASS_REQUIRED', 'Estilo requerido');
define('CDT_SECURE_MSG_CDTMENUOPTION_DS_DESCRIPTION_REQUIRED', 'Descripción requerida');

//títulos de las actions
define('CDT_SECURE_MSG_CDTREGISTRATION_TITLE_LIST', 'Listar Registraciones');
define('CDT_SECURE_MSG_CDTREGISTRATION_TITLE_ADD', 'Agregar Registración');
define('CDT_SECURE_MSG_CDTREGISTRATION_TITLE_UPDATE', 'Modificar Registración');
define('CDT_SECURE_MSG_CDTREGISTRATION_TITLE_VIEW', 'Visualizar Registración');

define('CDT_SECURE_MSG_CDTREGISTRATION_CD_REGISTRATION_REQUIRED', 'Código requerido');
define('CDT_SECURE_MSG_CDTREGISTRATION_DS_ACTIVATIONCODE_REQUIRED', 'Código de activación requerido');
define('CDT_SECURE_MSG_CDTREGISTRATION_DT_DATE_REQUIRED', 'Fecha requerida');
define('CDT_SECURE_MSG_CDTREGISTRATION_DS_USERNAME_REQUIRED', 'Nombre de usuario requerido');
define('CDT_SECURE_MSG_CDTREGISTRATION_DS_NAME_REQUIRED', 'Nombre requerido');
define('CDT_SECURE_MSG_CDTREGISTRATION_DS_EMAIL_REQUIRED', 'E-mail requerido');
define('CDT_SECURE_MSG_CDTREGISTRATION_DS_PASSWORD_REQUIRED', 'Password requerido');
define('CDT_SECURE_MSG_CDTREGISTRATION_DS_PHONE_REQUIRED', 'Teléfono requerido');
define('CDT_SECURE_MSG_CDTREGISTRATION_DS_ADDRESS_REQUIRED', 'Domicilio requerido');

//títulos de las actions
define('CDT_SECURE_MSG_CDTUSER_TITLE_LIST', 'Listar Usuarios');
define('CDT_SECURE_MSG_CDTUSER_TITLE_ADD', 'Agregar Usuario');
define('CDT_SECURE_MSG_CDTUSER_TITLE_UPDATE', 'Modificar Usuario');
define('CDT_SECURE_MSG_CDTUSER_TITLE_VIEW', 'Visualizar Usuario');
define('CDT_SECURE_MSG_CDTUSERPROFILE_TITLE_UPDATE', 'Actualizar mi cuenta');
define('CDT_SECURE_MSG_CDTUSER_TITLE_VIEW_NEW', 'Nuevo Usuario');

define('CDT_SECURE_MSG_CDTUSER_CD_USER_REQUIRED', 'Código requerido');
define('CDT_SECURE_MSG_CDTUSER_DS_USERNAME_REQUIRED', 'Nombre de usuario requerido');
define('CDT_SECURE_MSG_CDTUSER_DS_USERNAME_DUPLICATED', 'Nombre de usuario duplicado');
define('CDT_SECURE_MSG_CDTUSER_DS_NAME_REQUIRED', 'Nombre requerido');
define('CDT_SECURE_MSG_CDTUSER_DS_EMAIL_REQUIRED', 'E-mail requerido');
define('CDT_SECURE_MSG_CDTUSER_DS_EMAIL_DUPLICATED', 'E-mail duplicado');
define('CDT_SECURE_MSG_CDTUSER_DS_PASSWORD_REQUIRED', 'Password requerido');
define('CDT_SECURE_MSG_CDTUSER_CD_USERGROUP_REQUIRED', 'Perfil requerido');
define('CDT_SECURE_MSG_CDTUSER_DS_PHONE_REQUIRED', 'Teléfono requerido');
define('CDT_SECURE_MSG_CDTUSER_DS_ADDRESS_REQUIRED', 'Domicilio requerido');

define('CDT_SECURE_MSG_CDTUSER_NEW_USER_PASSWORD', 'Debe anotar y enviar la clave generada al nuevo usuario: <b>$1 / $2</b>');
//define('CDT_SECURE_MSG_CDTUSER_NO_IP', 'No hay ninguna IP definida' );
//define('CDT_SECURE_MSG_CDTUSER_IP_INVALID', 'Formato de IP incorrecto' );
//define('CDT_SECURE_MSG_CDTUSER_IP_TITLE', 'Limitar el acceso por IP' );
//define('CDT_SECURE_MSG_CDTUSER_IP_ADD', 'Agregar IP' );
//define('CDT_SECURE_MSG_CDTUSER_IP_DELETE', 'Quitar IP' );
//define('CDT_SECURE_MSG_CDTUSER_IP_NOT_EXISTS', 'La IP $1 no exist' );
//define('CDT_SECURE_MSG_CDTUSER_IP_ALREADY_EXISTS', 'La IP $1 ya existe' );
//define('CDT_SECURE_MSG_CDTUSER_IP_DELETE_CONFIRM', 'Confirma eliminar la IP seleccionada?' );

//títulos de las actions
define('CDT_SECURE_MSG_CDTUSERGROUP_TITLE_LIST', 'Listar Perfiles');
define('CDT_SECURE_MSG_CDTUSERGROUP_TITLE_ADD', 'Agregar Perfil');
define('CDT_SECURE_MSG_CDTUSERGROUP_TITLE_UPDATE', 'Modificar Perfil');
define('CDT_SECURE_MSG_CDTUSERGROUP_TITLE_VIEW', 'Visualizar Perfil');

define('CDT_SECURE_MSG_CDTUSERGROUP_CD_USERGROUP_REQUIRED', 'Código requerido');
define('CDT_SECURE_MSG_CDTUSERGROUP_DS_USERGROUP_REQUIRED', 'Perfil requerido');

define('CDT_SECURE_MSG_USERGROUPFUNCTION_TITLE_ASSIGN', 'Perfil / Asignar funciones');

//títulos de las actions
define('CDT_SECURE_MSG_CDTUSERGROUPFUNCTION_TITLE_LIST', 'Listar Funciones del Grupo de usuario');
define('CDT_SECURE_MSG_CDTUSERGROUPFUNCTION_TITLE_ADD', 'Agregar Función del Perfil');
define('CDT_SECURE_MSG_CDTUSERGROUPFUNCTION_TITLE_UPDATE', 'Modificar Función del Perfil');
define('CDT_SECURE_MSG_CDTUSERGROUPFUNCTION_TITLE_VIEW', 'Visualizar Función del Perfil');

define('CDT_SECURE_MSG_CDTUSERGROUPFUNCTION_CD_USERGROUP_FUNCTION_REQUIRED', 'Código requerido');
define('CDT_SECURE_MSG_CDTUSERGROUPFUNCTION_CD_USERGROUP_REQUIRED', 'Perfil requerido');
define('CDT_SECURE_MSG_CDTUSERGROUPFUNCTION_CD_FUNCTION_REQUIRED', 'Función requerida');

define('CDT_SECURE_LBL_USERGROUP_FUNCTIONS_EDIT', 'Asignar Funciones');

include ('messages_labels_es.php');
?>
