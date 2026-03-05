<?php

/**
 * ModeloPlanilla
 *  
 * @author Marcos
 * @since 22-05-2014
 */


class ModeloPlanilla  extends Entity{

    //variables de instancia.
    
	
	
	private $periodo;
	
	

    private $ds_modelo;
    
    private $nu_max;
    

    public function __construct(){
    	
    	
    	
    	$this->periodo = new Periodo();
    	
    	
    	
    	$this->ds_modelo = "";
    	$this->nu_max = "";
    }
    
    
   

    

   

	public function getPeriodo()
	{
	    return $this->periodo;
	}

	public function setPeriodo($periodo)
	{
	    $this->periodo = $periodo;
	}

	public function getDs_modelo()
	{
	    return $this->ds_modelo;
	}

	public function setDs_modelo($ds_modelo)
	{
	    $this->ds_modelo = $ds_modelo;
	}

	public function getNu_max()
	{
	    return $this->nu_max;
	}

	public function setNu_max($nu_max)
	{
	    $this->nu_max = $nu_max;
	}
}
?>