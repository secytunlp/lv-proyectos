<?php

/**
 * Metadata para una entity
 *  
 * @author Bernardo
 * @since 08-03-2013
 */

abstract class EntityMetadata{

	/**
	 * tabla donde se mapea la entidad.
	 * @var string
	 */
	private $tableName;
	
	/**
	 * metadata de las properties de la entity
	 * @var ItemCollection
	 */
	private $properties;
	


	public function getTableName()
	{
	    return $this->tableName;
	}

	public function setTableName($tableName)
	{
	    $this->tableName = $tableName;
	}

	public function getProperties()
	{
	    return $this->properties;
	}

	public function setProperties($properties)
	{
	    $this->properties = $properties;
	}
}
?>