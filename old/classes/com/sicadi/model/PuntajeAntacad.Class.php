<?php

/**
 * PuntajeAntacad
 *  
 * @author Marcos
 * @since 23-05-2014
 */


class PuntajeAntacad  extends Entity{

    //variables de instancia.
    
	private $modeloPlanilla;
	
	private $evaluacion;
    
    private $antacadMaximo;
    
    
    
    private $nu_puntaje;
    

    public function __construct(){
    	
    	$this->modeloPlanilla = new ModeloPlanilla();
    	
    	$this->evaluacion = new Evaluacion();
    	
    	$this->antacadMaximo = new AntacadMaximo();
    	
    	
    	
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

	public function getAntacadMaximo()
	{
	    return $this->antacadMaximo;
	}

	public function setAntacadMaximo($antacadMaximo)
	{
	    $this->antacadMaximo = $antacadMaximo;
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