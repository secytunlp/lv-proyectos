<?php

/**
 * se definen los mensajes del sistema .
 * 
 * @author modelBuilder
 * 
 */

//mensajes comunes.
if( defined( 'CDT_UI_LANGUAGE' ) ){
	include_once('messages_'. CDT_UI_LANGUAGE . '.php');
}else{
	include_once('messages_en.php');
}

?>
