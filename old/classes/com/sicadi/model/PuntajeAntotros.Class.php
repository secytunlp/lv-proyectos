<?php

/**
 * PuntajeAntotros
 *  
 * @author Marcos
 * @since 27-05-2014
 */


class PuntajeAntotros  extends Entity{

    //variables de instancia.
    
	private $modeloPlanilla;
	
	private $evaluacion;
    
    private $antotrosMaximo;
    
    
    
    private $nu_puntaje;
    

    public function __construct(){
    	
    	$this->modeloPlanilla = new ModeloPlanilla();
    	
    	$this->evaluacion = new Evaluacion();
    	
    	$this->antotrosMaximo = new AntotrosMaximo();
    	
    	
    	
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

	public function getAntotrosMaximo()
	{
	    return $this->antotrosMaximo;
	}

	public function setAntotrosMaximo($antotrosMaximo)
	{
	    $this->antotrosMaximo = $antotrosMaximo;
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