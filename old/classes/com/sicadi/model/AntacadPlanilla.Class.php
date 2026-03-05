<?php

/**
 * AntacadPlanilla
 *  
 * @author Marcos
 * @since 23-05-2014
 */


class AntacadPlanilla  extends Entity{

    //variables de instancia.
    
	private $ds_antacadplanilla;
	
	
    
    private $ds_antacadpdf;
    

    public function __construct(){
    	
    	$this->ds_antacadplanilla = "";
    	
    	
    	$this->ds_antacadpdf = "";
    }
    
    
   

	

	public function getDs_antacadplanilla()
	{
	    return $this->ds_antacadplanilla;
	}

	public function setDs_antacadplanilla($ds_antacadplanilla)
	{
	    $this->ds_antacadplanilla = $ds_antacadplanilla;
	}

	public function getDs_antacadpdf()
	{
	    return $this->ds_antacadpdf;
	}

	public function setDs_antacadpdf($ds_antacadpdf)
	{
	    $this->ds_antacadpdf = $ds_antacadpdf;
	}
}
?>