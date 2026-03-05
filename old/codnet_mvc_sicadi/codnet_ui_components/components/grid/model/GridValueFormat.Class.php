<?php

/**
 * Formato para renderizar un valor de la grilla
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 14-12-2011
 *
 */

class GridValueFormat{

	
	public function __construct(){
	}
	
	public function format( $value, $item=null ){
		return $value;
	}		
	
	public function getPattern(){
		return "";
	}
	
}