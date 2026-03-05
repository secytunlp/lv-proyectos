<?php

/**
 * Excepción para indicar que una clase
 * requerida no fue encontrada.
 * 
 * @author bernardo
 * @since 04-03-2010
 */
class ClassNotFoundException extends GenericException{
	
	public function __construct($ds_class_name){
		//TODO ver cómo manejar los códigos.
		$cod = 0;
		parent::__construct("Clase no encontrada: " . $ds_class_name, $cod);
	}
}
