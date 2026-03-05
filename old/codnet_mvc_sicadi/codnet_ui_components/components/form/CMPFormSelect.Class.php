<?php

/**
 * select de un formulario.
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 26-02-2013
 *
 */
class CMPFormSelect extends CMPFormInput{

	/**
	 * valores posibles para el select (options).
	 * @var array
	 */
	private $options;

	private $selectedValue;
	
	public function __construct($id, $name, $requiredMessage="", $value="", $size=0){
		
		parent::__construct($id, $name, $requiredMessage, $value, $size);
		$this->setRenderer( new SelectRenderer() );
		$this->options = array();
	}
	
	public function addOption( $label, $value, $selected ){
	
		$this->options[$value] = $label;
		
		if( $selected )
			$this->selectedValue = $value;
	}	

	public function addProperty( $name, $value ){
		parent::addProperty($name, $value);
		if( $name == "value" )
			$this->selectedValue = $value;
	}
	
	public function getOptions()
	{
	    return $this->options;
	}

	public function setOptions($options)
	{
	    $this->options = $options;
	}

	public function getSelectedValue()
	{
	    return $this->selectedValue;
	}

	public function setSelectedValue($selectedValue)
	{
	    $this->selectedValue = $selectedValue;
	}

	public function getReadOnlyRenderer(){
		return new SelectReadOnlyRenderer();
	}
	
	public function setEmptyOptionLabel($label){
		$this->getRenderer()->setEmptyOptionLabel($label);
	}
}