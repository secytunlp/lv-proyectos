<?php

/**
 * input field checkbox de un formulario.
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 26-02-2013
 *
 */
class CMPFormInputCheckbox extends CMPFormInput{

	private $isChecked;
	
	public function __construct($id, $name, $requiredMessage="", $value="", $size=0){
		
		parent::__construct($id, $name, $requiredMessage, $value, $size);
		$this->setRenderer( new InputCheckboxRenderer() );
		
	}
	
	public function setInputValue($value){
	    parent::setInputValue($value);
	    
	    if( $value == 1 || $value == true )
	    	$this->setIsChecked(true);
			
	}
	
	public function getIsChecked()
	{
	    return $this->isChecked;
	}

	public function setIsChecked($isChecked)
	{
	    $this->isChecked = $isChecked;
	}
	
	public function getType(){
		return "checkbox";
	}
	

	public function fillEntityValue( $entity, $formMethodType ){
	
	
		$inputName = $this->getProperty("name");

		//si tiene puntos (tipoDoc.oid), reemplazamos por "_".
		$inputNameForm = str_replace(".", "_", $inputName);

		//para los checkboxes lo que se evalúa es si el parámetro viene o no.
		//si viene se setea como TRUE sino FALSE.
		
		$inputValue = ( $formMethodType == "POST")? isset($_POST[$inputNameForm]) : isset($_GET[$inputNameForm]);
				
		CdtUtils::log("setting...type  $inputName = $inputValue ", __CLASS__, LoggerLevel::getLevelDebug());
				
		CdtReflectionUtils::doSetter( $entity, $inputName, $inputValue );
				
		CdtUtils::log("getter "   . CdtReflectionUtils::doGetter( $entity, $inputName ) , __CLASS__, LoggerLevel::getLevelDebug());
		
	}
}