<?php

/**
 * Metadata para una property que hace referencia
 * a otra entity
 *  
 * @author Bernardo
 * @since 08-03-2013
 */

abstract class PropertyRelatedMetadata{

	/**
	 * columna donde se mapea la entidad.
	 * @var string
	 */
	private $columnName;
	
	/**
	 * nombre de la property
	 * @var string
	 */
	private $propertyName;
	
	/**
	 * metadata de la entidad relacionada.
	 * @var EntityMetadata
	 */
	private $metadata;
	


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

	public function getMetadata()
	{
	    return $this->metadata;
	}

	public function setMetadata($metadata)
	{
	    $this->metadata = $metadata;
	}
}
?>