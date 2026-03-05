<?php

/**
 * Excepción para indicar no pudo ejecutarse un query
 * en la bbdd.
 * 
 * @author bernardo
 * @since 14-03-2010
 */
class DBException extends GenericException{
	
	public function __construct($error=null){
		$cod = 0;
		if($error==null)
			$msg = "No pudo realizarse la operación en la base de datos";
		else{
			if (is_array($error)){
			$msg = $error["message"];
			$cod = $error["code"];
			}
			else $msg = $error;
		}
		parent::__construct($msg, $cod);
	}
}
