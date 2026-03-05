<?php

/**
 * SubGrupo
 *  
 * @author Marcos
 * @since 27-05-2014
 */


class SubGrupo  extends Entity{

    //variables de instancia.
    
	private $ds_subgrupo;
	
	
    
    private $ds_pdf;
    

    public function __construct(){
    	
    	$this->ds_subgrupo = "";
    	
    	
    	$this->ds_pdf = "";
    }
    
    
   


	public function getDs_subgrupo()
	{
	    return $this->ds_subgrupo;
	}

	public function setDs_subgrupo($ds_subgrupo)
	{
	    $this->ds_subgrupo = $ds_subgrupo;
	}

	public function getDs_pdf()
	{
	    return $this->ds_pdf;
	}

	public function setDs_pdf($ds_pdf)
	{
	    $this->ds_pdf = $ds_pdf;
	}
}
?>