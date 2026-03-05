<?php

/**
 * PuntajePosgrado
 *  
 * @author Marcos
 * @since 22-05-2014
 */


class PuntajePosgrado  extends Entity{

    //variables de instancia.
    
	private $modeloPlanilla;
	
	private $evaluacion;
    
    private $posgradoMaximo;
    
   
    

    public function __construct(){
    	
    	$this->modeloPlanilla = new ModeloPlanilla();
    	
    	$this->evaluacion = new Evaluacion();
    	
    	$this->posgradoMaximo = new PosgradoMaximo();
    	
    	
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

	

	public function getPosgradoMaximo()
	{
	    return $this->posgradoMaximo;
	}

	public function setPosgradoMaximo($posgradoMaximo)
	{
	    $this->posgradoMaximo = $posgradoMaximo;
	}

	
}
?>