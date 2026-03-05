<?php

/**
 * Categoria
 *  
 * @author Marcos
 * @since 07-06-2023
 */


class Categoriasicadi  extends Entity{

    //variables de instancia.

    private $ds_categoriasicadi;
    

    public function __construct(){
    	
    	$this->ds_categoriasicadi = "";
    }
    
    
    public function getDs_categoriasicadi()
    {
        return $this->ds_categoriasicadi;
    }

    public function setDs_categoriasicadi($ds_categoriasicadi)
    {
        $this->ds_categoriasicadi = $ds_categoriasicadi;
    }
    
	public function __toString(){
		
		return $this->getDs_categoriasicadi();
	}

}
?>