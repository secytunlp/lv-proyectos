<?php

/**
 * AntjustificacionPlanilla
 *  
 * @author Marcos
 * @since 28-05-2014
 */


class AntjustificacionPlanilla  extends Entity{

    //variables de instancia.
    
	private $ds_antjustificacionplanilla;
	
	private $ds_antjustificacionpdf;
    
    private $subgrupo;

    public function __construct(){
    	
    	$this->ds_antjustificacionplanilla = "";
    	
    	
    	$this->ds_antjustificacionpdf = "";
    	
    	$this->subgrupo = new SubGrupo();
    }
    
    
   

	

	public function getDs_antjustificacionplanilla()
	{
	    return $this->ds_antjustificacionplanilla;
	}

	public function setDs_antjustificacionplanilla($ds_antjustificacionplanilla)
	{
	    $this->ds_antjustificacionplanilla = $ds_antjustificacionplanilla;
	}

	public function getDs_antjustificacionpdf()
	{
	    return $this->ds_antjustificacionpdf;
	}

	public function setDs_antjustificacionpdf($ds_antjustificacionpdf)
	{
	    $this->ds_antjustificacionpdf = $ds_antjustificacionpdf;
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