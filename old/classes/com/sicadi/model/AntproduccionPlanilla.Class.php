<?php

/**
 * AntproduccionPlanilla
 *  
 * @author Marcos
 * @since 28-05-2014
 */


class AntproduccionPlanilla  extends Entity{

    //variables de instancia.
    
	private $ds_antproduccionplanilla;
	
	private $ds_antproduccionpdf;
    
    private $subgrupo;

    public function __construct(){
    	
    	$this->ds_antproduccionplanilla = "";
    	
    	
    	$this->ds_antproduccionpdf = "";
    	
    	$this->subgrupo = new SubGrupo();
    }
    
    
   

	

	public function getDs_antproduccionplanilla()
	{
	    return $this->ds_antproduccionplanilla;
	}

	public function setDs_antproduccionplanilla($ds_antproduccionplanilla)
	{
	    $this->ds_antproduccionplanilla = $ds_antproduccionplanilla;
	}

	public function getDs_antproduccionpdf()
	{
	    return $this->ds_antproduccionpdf;
	}

	public function setDs_antproduccionpdf($ds_antproduccionpdf)
	{
	    $this->ds_antproduccionpdf = $ds_antproduccionpdf;
	}

	public function getSubgrupo()
	{
	    return $this->subgrupo;
	}

	public function setSubgrupo($subgrupo)
	{
	    $this->subgrupo = $subgrupo;
	}
}
?>