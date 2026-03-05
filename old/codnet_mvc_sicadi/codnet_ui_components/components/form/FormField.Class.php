<?php

/**
 * field del form.
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 26-02-2013
 *
 */
class FormField {

	/**
	 * label
	 * @var string
	 */
	private $label;
	
	/**
	 * input
	 * @var CMPFormInput
	 */
	private $input;
	
	/**
	 * Tamanio ancho mínimo para el field.
	 * (para evitar que salga en varias filas)
	 * @var string
	 */
	private $minWidth="";
	
	public function __construct( $label, $input ){
		
		$this->setLabel( $label );
		$this->setInput($input);
		
	}
	


	public function getLabel()
	{
	    return $this->label;
	}

	public function setLabel($label)
	{
	    $this->label = $label;
	}

	public function getInput()
	{
	    return $this->input;
	}

	public function setInput($input)
	{
	    $this->input = $input;
	}

	public function getMinWidth()
	{
	    return $this->minWidth;
	}

	public function setMinWidth($minWidth)
	{
	    $this->minWidth = $minWidth;
	}
}