<?php

/**
 * PosgradoMaximo
 *  
 * @author Marcos
 * @since 22-05-2014
 */


class PosgradoMaximo  extends Entity{

    //variables de instancia.
    
	private $modeloPlanilla;
	
	private $posgradoPlanilla;
	
	private $puntajeGrupo;
    
    private $nu_max;
    
   
    

    public function __construct(){
    	
    	$this->modeloPlanilla = new ModeloPlanilla();
    	
    	$this->posgradoPlanilla = new PosgradoPlanilla();
    	
    	$this->puntajeGrupo = new PuntajeGrupo();
    	
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

	public function getPosgradoPlanilla()
	{
	    return $this->posgradoPlanilla;
	}

	public function setPosgradoPlanilla($posgradoPlanilla)
	{
	    $this->posgradoPlanilla = $posgradoPlanilla;
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

	
}
?>