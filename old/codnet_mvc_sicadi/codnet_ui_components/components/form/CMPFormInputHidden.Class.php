<?php

/**
 * input field hidden de un formulario.
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 27-02-2013
 *
 */
class CMPFormInputHidden extends CMPFormInput{

	public function __construct($id, $name, $value){
		//CdtUtils::log('token: '.$value);
		$this->setId($id);
		$this->addProperty("name", $name);
		$this->addProperty("value", $value);
		$this->setIsEditable(true);
	}
	 
	public function getType(){
		return "hidden";
	}
}