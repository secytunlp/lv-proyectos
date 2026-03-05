<?php

/**
 * SubanteriorPlanilla
 *  
 * @author Marcos
 * @since 31-08-2017
 */


class SubanteriorPlanilla  extends Entity{

    //variables de instancia.
    
	private $ds_subanteriorplanilla;
	
	private $ds_subanteriorpdf;
    
    private $subgrupo;

    public function __construct(){
    	
    	$this->ds_subanteriorplanilla = "";
    	
    	
    	$this->ds_subanteriorpdf = "";
    	
    	$this->subgrupo = new SubGrupo();
    }
    
    
   

	

	public function getDs_subanteriorplanilla()
	{
	    return $this->ds_subanteriorplanilla;
	}

	public function setDs_subanteriorplanilla($ds_subanteriorplanilla)
	{
	    $this->ds_subanteriorplanilla = $ds_subanteriorplanilla;
	}

	public function getDs_subanteriorpdf()
	{
	    return $this->ds_subanteriorpdf;
	}

	public function setDs_subanteriorpdf($ds_subanteriorpdf)
	{
	    $this->ds_subanteriorpdf = $ds_subanteriorpdf;
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