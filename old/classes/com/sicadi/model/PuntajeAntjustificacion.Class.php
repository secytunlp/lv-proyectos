<?php

/**
 * PuntajeAntjustificacion
 *  
 * @author Marcos
 * @since 28-05-2014
 */


class PuntajeAntjustificacion  extends Entity{

    //variables de instancia.
    
	private $modeloPlanilla;
	
	private $evaluacion;
    
    private $antjustificacionMaximo;
    
    
    
    private $nu_puntaje;
    

    public function __construct(){
    	
    	$this->modeloPlanilla = new ModeloPlanilla();
    	
    	$this->evaluacion = new Evaluacion();
    	
    	$this->antjustificacionMaximo = new AntjustificacionMaximo();
    	
    	
    	
    	$this->nu_puntaje="";
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

	public function getAntjustificacionMaximo()
	{
	    return $this->antjustificacionMaximo;
	}

	public function setAntjustificacionMaximo($antjustificacionMaximo)
	{
	    $this->antjustificacionMaximo = $antjustificacionMaximo;
	}

	public function getNu_puntaje()
	{
	    return $this->nu_puntaje;
	}

	public function setNu_puntaje($nu_puntaje)
	{
	    $this->nu_puntaje = $nu_puntaje;
	}
}
?>