<?php

/**
 * input field radio de un formulario.
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 26-02-2013
 *
 */
class CMPFormInputRadio extends CMPFormInputCheckbox{

	public function __construct($id, $name, $requiredMessage="", $value="", $size=0){
		
		parent::__construct($id, $name, $requiredMessage, $value, $size);
		$this->setRenderer( new InputRadioRenderer() );
		
	}
	
	public function getType(){
		return "radio";
	}
}