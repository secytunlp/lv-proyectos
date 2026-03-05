<?php

/**
 * Utilidades para codnet_ui
 * 
 * @author bernardo
 * @since 26/09/2012
 *
 */
class CdtUIUtils{

	public static function setCharset( $contentType="text/plain"){
		
		$charset = "";
		if( CDT_UI_UTF8_ENCODE )
			$charset = "charset=UTF-8";
        else 	
			$charset = "charset=ISO-8859-1";
        
		//simplemente se retorna el content.
		header("Content-Type: $contentType; $charset");
		
	}
	
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
	 * realiza el encode de caracteres para pdf 
	 * @param unknown_type $value
	 */
	public static function encodeCharactersPDF($value) {
		if (is_string($value) && $value !== "") {
			return iconv("UTF-8", "ISO-8859-1", $value);
		} else {
			// Handle the case where $value is not a valid string or is empty.
			// You can return an error message or handle it as needed.
			return ""; // Change this to your desired behavior.
		}
	}

	
	/**
	 * realiza el encode de caracteres para xls 
	 * @param unknown_type $value
	 */
	public static function encodeCharactersXls( $value ){

		if (is_string($value) && $value !== "") {
			return iconv("UTF-8", "ISO-8859-1", $value);
		} else {
			// Handle the case where $value is not a valid string or is empty.
			// You can return an error message or handle it as needed.
			return ""; // Change this to your desired behavior.
		}
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
	
	public static function isUTF8($string) {
    	return (utf8_encode(utf8_decode($string)) == $string);
	}

	
	public static function formatCharacters($str){
		
		if (self::isUTF8($str)) { 
    		return $str; 
		}
		else{
    		return iconv("ISO-8859-1", "UTF-8//TRANSLIT", $str);
		}
		
	}
	
}
?>