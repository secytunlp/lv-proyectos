<?php

/**
 * PosgradoPlanilla
 *  
 * @author Marcos
 * @since 22-05-2014
 */


class PosgradoPlanilla  extends Entity{

    //variables de instancia.
    
	private $ds_posgradoplanilla;
	
	
    
    private $nu_orden;
    

    public function __construct(){
    	
    	$this->ds_posgradoplanilla = "";
    	
    	
    	$this->nu_orden = "";
    }
    
    
   

	

	public function getDs_posgradoplanilla()
	{
	    return $this->ds_posgradoplanilla;
	}

	public function setDs_posgradoplanilla($ds_posgradoplanilla)
	{
	    $this->ds_posgradoplanilla = $ds_posgradoplanilla;
	}

	public function getNu_orden()
	{
	    return $this->nu_orden;
	}

	public function setNu_orden($nu_orden)
	{
	    $this->nu_orden = $nu_orden;
	}
}
?>