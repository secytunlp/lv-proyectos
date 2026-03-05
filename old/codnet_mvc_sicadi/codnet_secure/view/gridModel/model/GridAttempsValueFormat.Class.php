<?php

/**
 * Formato para renderizar los attemps de los usuarios.
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 27-02-2013
 *
 */
class GridAttempsValueFormat extends GridValueFormat{

	private $img;
	
	public function __construct( $img = "" ){
		
		parent::__construct();
		
	}
	
	public function format( $value, $item=null ){
            if($value == CDT_SECURE_LOGIN_MAX_ATTEMPS ){
				$response = "<img src='".WEB_PATH."css/grid/blocked.png'/>";
				$response = "<div style='width:100%;background-color:red;'/>";
				 
			}else{
				$response = "<img src='".WEB_PATH."css/grid/unblocked.png'/>"; 
				$response = "<div style='width:100%;background-color:green;'>&nbsp;</div>";
			}       
        return $response;
	}	
}