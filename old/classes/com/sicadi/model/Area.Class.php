<?php

/**
 * Area
 *  
 * @author Marcos
 * @since 26-06-2023
 */


class Area  extends Entity{

    //variables de instancia.

    private $ds_Area;
    

    public function __construct(){
    	
    	$this->ds_Area = "";
    }
    
    
    public function getDs_Area()
    {
        return $this->ds_Area;
    }

    public function setDs_Area($ds_Area)
    {
        $this->ds_Area = $ds_Area;
    }
    
	public function __toString(){
		
		return $this->getDs_Area();
	}

}
?>