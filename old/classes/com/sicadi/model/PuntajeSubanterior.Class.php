<?php

/**
 * PuntajeSubanterior
 *  
 * @author Marcos
 * @since 31-08-2017
 */


class PuntajeSubanterior  extends Entity{

    //variables de instancia.
    
	private $modeloPlanilla;
	
	private $evaluacion;
    
    private $subanteriorMaximo;
    
    
    
    private $nu_puntaje;
    

    public function __construct(){
    	
    	$this->modeloPlanilla = new ModeloPlanilla();
    	
    	$this->evaluacion = new Evaluacion();
    	
    	$this->subanteriorMaximo = new SubanteriorMaximo();
    	
    	
    	
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

	public function getSubanteriorMaximo()
	{
	    return $this->subanteriorMaximo;
	}

	public function setSubanteriorMaximo($subanteriorMaximo)
	{
	    $this->subanteriorMaximo = $subanteriorMaximo;
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