<?php

/**
 * Equivalencia
 *  
 * @author Marcos
 * @since 10-04-2023
 */


class Equivalencia  extends Entity{

    //variables de instancia.

    private $ds_equivalencia;
    

    public function __construct(){
    	
    	$this->ds_equivalencia = "";
    }
    
    
    public function getDs_equivalencia()
    {
        return $this->ds_equivalencia;
    }

    public function setDs_equivalencia($ds_equivalencia)
    {
        $this->ds_equivalencia = $ds_equivalencia;
    }
    
	public function __toString(){
		
		return $this->getDs_equivalencia();
	}

}
?>