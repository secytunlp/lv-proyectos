<?php

/**
 * PuntajeAntproduccion
 *  
 * @author Marcos
 * @since 28-05-2014
 */


class PuntajeAntproduccion  extends Entity{

    //variables de instancia.
    
	private $modeloPlanilla;
	
	private $evaluacion;
    
    private $antproduccionMaximo;
    
    private $nu_puntaje;
    
    private $nu_cant;
    

    public function __construct(){
    	
    	$this->modeloPlanilla = new ModeloPlanilla();
    	
    	$this->evaluacion = new Evaluacion();
    	
    	$this->antproduccionMaximo = new AntproduccionMaximo();
    	
    	$this->nu_cant="";
    	
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

	public function getAntproduccionMaximo()
	{
	    return $this->antproduccionMaximo;
	}

	public function setAntproduccionMaximo($antproduccionMaximo)
	{
	    $this->antproduccionMaximo = $antproduccionMaximo;
	}

	public function getNu_puntaje()
	{
	    return $this->nu_puntaje;
	}

	public function setNu_puntaje($nu_puntaje)
	{
	    $this->nu_puntaje = $nu_puntaje;
	}

	public function getNu_cant()
	{
	    return $this->nu_cant;
	}

	public function setNu_cant($nu_cant)
	{
	    $this->nu_cant = $nu_cant;
	}
}
?>