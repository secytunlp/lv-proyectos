<?php
/**
 * se definen los messages  del módulo de componentes.
 * 
 * @author bernardo
 * @since 19-12-2011
 * 
 */


if( defined( 'CDT_UI_LANGUAGE' ) ){
	include_once('messages_'. CDT_UI_LANGUAGE . '.php');
	
}else{
	
	include_once('messages_en.php');		
}
?>