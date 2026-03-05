<?php

/**
 * Excepción genérica.
 * 
 * @author bernardo
 * @since 14-03-2010
 */
class GenericException extends Exception{
	
	public function __construct($msg=null, $cod=0){
		if($msg==null)
			$msg = "No pudo realizarse la operación";
		
		parent::__construct($msg, $cod);
	}
	
}
