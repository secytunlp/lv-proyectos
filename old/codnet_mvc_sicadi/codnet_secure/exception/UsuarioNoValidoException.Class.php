<?php

/**
 * Excepción para indicar que/o password no es correcto/a.
 * 
 * @author bernardo
 * @since 16-03-2010
 */
class UsuarioNoValidoException extends GenericException{
	
	public function UsuarioNoValidoException(){
		$cod = 0;
		parent::__construct("Usuario incorrecto");
	}
}
