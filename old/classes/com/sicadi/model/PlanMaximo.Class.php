<?php

/**
 * PlanMaximo
 *  
 * @author Marcos
 * @since 05-12-2013
 */


class PlanMaximo  extends Entity{

    //variables de instancia.
    
	private $modeloPlanilla;
	
	
    
    private $nu_max;
    

    public function __construct(){
    	
    	$this->modeloPlanilla = new ModeloPlanilla();
    	
    	
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
}
?>