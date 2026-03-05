<?php

/**
 * Excepción para indicar que las password es incorrecta.
 * 
 * @author bernardo
 * @since 16-03-2010
 */
class PasswordIncorrectaException extends GenericException{
	
	public function PasswordIncorrectaException(){
		$cod = 0;
		parent::__construct("La contraseña es incorrecta");
	}
}
