<?php

/**
 * SubSubarea
 *  
 * @author Marcos
 * @since 26-06-2023
 */


class Subarea  extends Entity{

    //variables de instancia.

    private $ds_subarea;

    private $area;
    

    public function __construct(){
    	
    	$this->ds_subarea = "";
        $this->area = new Area();
    }
    
    
    public function getDs_subarea()
    {
        return $this->ds_subarea;
    }

    public function setDs_subarea($ds_subarea)
    {
        $this->ds_subarea = $ds_subarea;
    }
    
	public function __toString(){
		
		return $this->getDs_subarea();
	}

    /**
     * @return Area
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * @param Area $area
     */
    public function setArea($area)
    {
        $this->area = $area;
    }



}
?>