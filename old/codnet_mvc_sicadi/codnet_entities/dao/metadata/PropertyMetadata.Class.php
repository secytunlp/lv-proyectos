<?php

/**
 * Metadata para una property
 *  
 * @author Bernardo
 * @since 08-03-2013
 */

abstract class PropertyMetadata{

	/**
	 * columna donde se mapea la propiedad.
	 * @var string
	 */
	private $columnName;
	
	/**
	 * nombre de la property
	 * @var string
	 */
	private $propertyName;
	


	public function getColumnName()
	{
	    return $this->columnName;
	}

	public function setColumnName($columnName)
	{
	    $this->columnName = $columnName;
	}

	public function getPropertyName()
	{
	    return $this->propertyName;
	}

	public function setPropertyName($propertyName)
	{
	    $this->propertyName = $propertyName;
	}
}
?>