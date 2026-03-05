<?php

/**
 * Autocomplete para un formulario.
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 24-05-2013
 *
 */
class CMPFormEntityAutocomplete extends CMPFormInput{


	private $autocomplete;
	
	public function __construct(CMPEntityAutocomplete $autocomplete){

		$this->setAutocomplete($autocomplete);
		parent::__construct($autocomplete->getInputId(), $autocomplete->getInputName(),$autocomplete->getRequiredMessage());
		
		$this->setRequiredMessage( $autocomplete->getRequiredMessage() );
		
		$this->setRenderer(new InputAutocompleteRenderer() );
	}
/*
	public function show( ){

		return $this->getFindObject()->show();
	}
*/
	public function addProperty( $name, $value ){
		parent::addProperty($name, $value);
		if($name=="value"){
			//$this->autocomplete->findItem( $value );
		}
	}
	

	public function getAutocomplete()
	{
	    return $this->autocomplete;
	}

	public function setAutocomplete($autocomplete)
	{
	    $this->autocomplete = $autocomplete;
	}
}