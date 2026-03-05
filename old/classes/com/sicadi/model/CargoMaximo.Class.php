<?php

/**
 * CargoMaximo
 *  
 * @author Marcos
 * @since 05-12-2013
 */


class CargoMaximo  extends Entity{

    //variables de instancia.
    
	private $modeloPlanilla;
	
	private $cargoPlanilla;
    
    private $nu_max;
    

    public function __construct(){
    	
    	$this->modeloPlanilla = new ModeloPlanilla();
    	
    	$this->cargoPlanilla = new CargoPlanilla();
    	
    	$this->nu_max = "";
    }
    
    
   

	public function getModeloPlanilla()
	{
	    return $this->modeloPlanilla;
	}

	public function setModeloPlanilla($modeloPlanilla)
	{
	    $this->modeloPlanilla = $modeloPlanilla;
	}

	public function getNu_max()
	{
	    return $this->nu_max;
	}

	public function setNu_max($nu_max)
	{
	    $this->nu_max = $nu_max;
	}

	public function getCargoPlanilla()
	{
	    return $this->cargoPlanilla;
	}

	public function setCargoPlanilla($cargoPlanilla)
	{
	    $this->cargoPlanilla = $cargoPlanilla;
	}
}
?>