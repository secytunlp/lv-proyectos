<?php

/**
 * CargoPlanilla
 *  
 * @author Marcos
 * @since 05-12-2013
 */


class CargoPlanilla  extends Entity{

    //variables de instancia.

    private $ds_cargoplanilla;
    
   
    

    public function __construct(){
    	
    	$this->ds_cargoplanilla = "";
    }
    
    
    


    

	public function getDs_cargoplanilla()
	{
	    return $this->ds_cargoplanilla;
	}

	public function setDs_cargoplanilla($ds_cargoplanilla)
	{
	    $this->ds_cargoplanilla = $ds_cargoplanilla;
	}
}
?>