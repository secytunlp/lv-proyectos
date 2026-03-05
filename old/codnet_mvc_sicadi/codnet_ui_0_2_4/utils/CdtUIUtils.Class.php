<?php

/**
 * Utilidades para codnet_ui
 * 
 * @author bernardo
 * @since 26/09/2012
 *
 */
class CdtUIUtils{

	/**
	 * realiza el encode de caracteres 
	 * @param unknown_type $value
	 */
	public static function encodeCharacters( $value ){

		if( CDT_UI_UTF8_ENCODE )
			$value =  utf8_encode( $value );
			
		return $value;
	}
	
	/**
	 * decodifica los caracteres
	 * @param unknown_type $value
	 */
	public static function decodeCharacters( $value ){
		
		if( CDT_UI_UTF8_ENCODE )
			$value =  utf8_decode( $value );
			
		return $value;
		
	}
}
?>