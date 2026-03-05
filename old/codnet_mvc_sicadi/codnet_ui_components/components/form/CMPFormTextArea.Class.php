<?php

/**
 * textarea de un formulario.
 * 
 * @author bernardo
 * @since 04/06/2013
 */
class CMPFormTextArea extends CMPFormInput{

	private $rows;
	private $cols;
	
	public function __construct($id, $name, $requiredMessage="", $value="", $rows=4, $cols=30){
		
		parent::__construct($id, $name, $requiredMessage, $value);
		$this->setRenderer( new InputTextAreaRenderer() );
		
		$this->rows = $rows;
		$this->cols = $cols;
	}
	

	public function getRows()
	{
	    return $this->rows;
	}

	public function setRows($rows)
	{
	    $this->rows = $rows;
	}

	public function getCols()
	{
	    return $this->cols;
	}

	public function setCols($cols)
	{
	    $this->cols = $cols;
	}
}