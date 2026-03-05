<?php

/**
 * AntacadMaximo
 *  
 * @author Marcos
 * @since 23-05-2014
 */


class AntacadMaximo  extends Entity{

    //variables de instancia.
    
	private $modeloPlanilla;
	
	private $antacadPlanilla;
	
	private $puntajeGrupo;
    
    private $nu_max;
    
    private $nu_min;
    
    private $nu_tope;
    

    public function __construct(){
    	
    	$this->modeloPlanilla = new ModeloPlanilla();
    	
    	$this->antacadPlanilla = new AntacadPlanilla();
    	
    	$this->puntajeGrupo = new PuntajeGrupo();
    	
    	$this->nu_max = "";
    	
    	$this->nu_min = "";
    	
    	$this->nu_tope = "";
    }
    
    
   

	

	public function getModeloPlanilla()
	{
	    return $this->modeloPlanilla;
	}

	public function setModeloPlanilla($modeloPlanilla)
	{
	    $this->modeloPlanilla = $modeloPlanilla;
	}

	public function getAntacadPlanilla()
	{
	    return $this->antacadPlanilla;
	}

	public function setAntacadPlanilla($antacadPlanilla)
	{
	    $this->antacadPlanilla = $antacadPlanilla;
	}

	public function getPuntajeGrupo()
	{
	    return $this->puntajeGrupo;
	}

	public function setPuntajeGrupo($puntajeGrupo)
	{
	    $this->puntajeGrupo = $puntajeGrupo;
	}

	public function getNu_max()
	{
	    return $this->nu_max;
	}

	public function setNu_max($nu_max)
	{
	    $this->nu_max = $nu_max;
	}

	public function getNu_min()
	{
	    return $this->nu_min;
	}

	public function setNu_min($nu_min)
	{
	    $this->nu_min = $nu_min;
	}

	public function getNu_tope()
	{
	    return $this->nu_tope;
	}

	public function setNu_tope($nu_tope)
	{
	    $this->nu_tope = $nu_tope;
	}
}
?>