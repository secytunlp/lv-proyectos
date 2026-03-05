<?php

/**
 * para manejar un grupo de checkboxes en un formulario.
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 26-02-2013
 *
 */
class CMPFormInputCheckboxes extends CMPFormInput{

	private $checkboxes;

	public function __construct($name, $requiredMessage="", $value="", $size=0){
		
		parent::__construct($name, $name, $requiredMessage, $value, $size);
		$this->checkboxes = array();
	}
	
	public function show( ){
	
		$result = "";
		foreach ($this->getCheckboxes() as $checkbox) {
			
			$result .= $checkbox->getLabel();
			$result .= $checkbox->getInput()->show();
		}
		return $result;
	}
	
	public function setInputValue($value)
	{
	    parent::setInputValue($value);
	    
	    if(is_array($this->checkboxes))
		    //recorremos los radio buttons para asignar el checked.
			foreach ($this->checkboxes as $checkbox) {
				
				$input = $checkbox->getInput();
				
				if(is_array( $value) ){
				
					foreach ($value as $v) {
						
						if($input->getInputValue() == $v)
							$input->setIsChecked(true);
						
					}
				}
				
				
				
			}
	}
	
	public function addCheckbox( $label, CMPFormInputCheckbox $checkbox ){
	
		$this->checkboxes[] = new FormField($label, $checkbox);
		
	}	
	
	public function getCheckboxes()
	{
	    return $this->checkboxes;
	}

	public function setCheckboxes($checkboxes)
	{
	    $this->checkboxes = $checkboxes;
	}
	
	public function setIsEditable($isEditable)
	{
		parent::setIsEditable($isEditable);
		
		foreach ($this->checkboxes as $checkbox) {
		
			$input = $checkbox->getInput();
			$input->setIsEditable( $isEditable );
		}		
	}
	
	
}