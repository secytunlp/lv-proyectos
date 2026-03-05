<?php

/**
 * findObject para un formulario.
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 26-02-2013
 *
 */
class CMPFormFindObject extends CMPFormInput{


	private $findObject;
	
	public function __construct(CMPFindObject $findObject){

		$this->setFindObject($findObject);
		parent::__construct($findObject->getInputId(), $findObject->getInputName());
		
		$this->setRenderer(new InputFindObjectRenderer() );
	}
/*
	public function show( ){

		return $this->getFindObject()->show();
	}
*/
	public function getFindObject()
	{
	    return $this->findObject;
	}

	public function setFindObject($findObject)
	{
	    $this->findObject = $findObject;
	}
	
	public function addProperty( $name, $value ){
		parent::addProperty($name, $value);
		if($name=="value"){
			$this->findObject->findItem( $value );
		}
	}
	
	public function setInputValue($value)
	{
	    parent::setInputValue($value);
	    $this->findObject->setInputCodeValue($value);
	    
	}
	
}