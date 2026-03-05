<?php

/**
 * para manejar un grupo de radios en un formulario.
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 26-02-2013
 *
 */
class CMPFormInputRadios extends CMPFormInput{

	private $radios;

	public function __construct($name, $requiredMessage="", $value="", $size=0){
		
		parent::__construct($name, $name, $requiredMessage, $value, $size);
		$this->radios = array();
	}
	
	public function show( ){
	
		$result = "";
		foreach ($this->getRadios() as $radio) {
			
			$result .= $radio->getLabel();
			$result .= $radio->getInput()->show();
		}
		return $result;
	}

	public function setInputValue($value)
	{
	    parent::setInputValue($value);
	    
	    if(is_array($this->radios))
		    //recorremos los radio buttons para asignar el checked.
			foreach ($this->radios as $radio) {
				
				$input = $radio->getInput();
				$input->setIsChecked($input->getInputValue() == $value);
				
			}
	}
	
	public function addRadio( $label, CMPFormInputRadio $radio){
	
		$this->radios[] = new FormField($label, $radio);
		
	}	
	


	public function getRadios()
	{
	    return $this->radios;
	}

	public function setRadios($radios)
	{
	    $this->radios = $radios;
	}
	

	public function setIsEditable($isEditable)
	{
		parent::setIsEditable($isEditable);
	
		foreach ($this->radios as $radio) {
	
			$input = $radio->getInput();
			$input->setIsEditable( $isEditable );
		}
	}
}