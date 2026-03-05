<?php

/**
 * PuntajeCargo
 *  
 * @author Marcos
 * @since 05-12-2013
 */


class PuntajeCargo  extends Entity{

    //variables de instancia.
    
	private $modeloPlanilla;
	
	private $evaluacion;
    
    private $cargoMaximo;
    

    public function __construct(){
    	
    	$this->modeloPlanilla = new ModeloPlanilla();
    	
    	$this->evaluacion = new Evaluacion();
    	
    	$this->cargoMaximo = new CargoMaximo();
    }
    
    
   

	

	public function getModeloPlanilla()
	{
	    return $this->modeloPlanilla;
	}

	public function setModeloPlanilla($modeloPlanilla)
	{
	    $this->modeloPlanilla = $modeloPlanilla;
	}

	public function getEvaluacion()
	{
	    return $this->evaluacion;
	}

	public function setEvaluacion($evaluacion)
	{
	    $this->evaluacion = $evaluacion;
	}

	

	public function getCargoMaximo()
	{
	    return $this->cargoMaximo;
	}

	public function setCargoMaximo($cargoMaximo)
	{
	    $this->cargoMaximo = $cargoMaximo;
	}
}
?>