<?php

/**
 * fieldset del form.
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 26-02-2013
 *
 */
class FormFieldset {

	/**
	 * legend
	 * @var string
	 */
	private $legend;
	
	/**
	 * fields del fieldset
	 * @var array
	 */
	private $fields;
	
	/**
	 * los fields ordenados en columns
	 * @var array
	 */
	private $fieldsColumns;
	
	public function __construct( $legend="" ){
		$this->legend = $legend;
		$this->fields = array();
		$this->fieldsColumns = array();
	}
	
	

	public function getLegend()
	{
	    return $this->legend;
	}

	public function setLegend($legend)
	{
	    $this->legend = $legend;
	}

	public function getFields()
	{
	    return $this->fields;
	}

	public function setFields($fields)
	{
	    $this->fields = $fields;
	}

	public function addField( FormField $field, $column=1 ){
		$this->fields[$field->getInput()->getId()] = $field;
		$this->fieldsColumns[$column][$field->getInput()->getId()] = $field;
	}
	
	public function getField( $inputId ){
		return $this->fields[$inputId];
	}
	
	public function getFieldsColumns()
	{
	    return $this->fieldsColumns;
	}
}