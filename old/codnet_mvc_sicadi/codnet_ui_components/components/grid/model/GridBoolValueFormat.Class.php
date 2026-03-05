<?php

/**
 * Formato para renderizar un valor de la grilla
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 14-12-2011
 *
 */
class GridBoolValueFormat extends GridValueFormat{

	private $img;
	
	public function __construct( $img = "" ){
		
		parent::__construct();
		
	}
	
	public function format( $value, $item=null ){
            if($value){
				$response = "<img src='".WEB_PATH."css/grid/chk_on.png'/>"; 
			}else{
				$response = "<img src='".WEB_PATH."css/grid/chk_off.png'/>"; 
			}       
        return $response;
	}	
}