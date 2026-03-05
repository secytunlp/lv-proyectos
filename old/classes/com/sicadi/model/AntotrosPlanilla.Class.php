<?php

/**
 * AntotrosPlanilla
 *  
 * @author Marcos
 * @since 27-05-2014
 */


class AntotrosPlanilla  extends Entity{

    //variables de instancia.
    
	private $ds_antotrosplanilla;
	
	private $ds_antotrospdf;
    
    private $subgrupo;

    public function __construct(){
    	
    	$this->ds_antotrosplanilla = "";
    	
    	
    	$this->ds_antotrospdf = "";
    	
    	$this->subgrupo = new SubGrupo();
    }
    
    
   

	

	public function getDs_antotrosplanilla()
	{
	    return $this->ds_antotrosplanilla;
	}

	public function setDs_antotrosplanilla($ds_antotrosplanilla)
	{
	    $this->ds_antotrosplanilla = $ds_antotrosplanilla;
	}

	public function getDs_antotrospdf()
	{
	    return $this->ds_antotrospdf;
	}

	public function setDs_antotrospdf($ds_antotrospdf)
	{
	    $this->ds_antotrospdf = $ds_antotrospdf;
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