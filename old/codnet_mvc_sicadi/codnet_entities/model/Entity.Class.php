<?php


/**
 * Entidad genérica
 *  
 * @author Bernardo
 * @since 27-02-2013
 */


class Entity {

    //variables de instancia.

	/**
	 * @var string
	 */
    private $oid;
    
	/**
	 * @var string
	 */
    private $version;
    
	    
	public function __construct(){
    	
    	$this->oid = null;
    }
    
	public function getOid()
	{
	    return $this->oid;
	}

	public function setOid($oid)
	{
	    $this->oid = $oid;
	}

	public function getVersion()
	{
	    return $this->version;
	}

	public function setVersion($version)
	{
	    $this->version = $version;
	}
	
}
?>