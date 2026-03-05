<?php

/**
 * Ambito
 *
 * @author Marcos
 * @since 09-05-2014
 */

class Rango extends Entity{

	//variables de instancia.
	
	
	
	
	private $dt_desde;
	
	private $dt_hasta;
	
	
	


	public function __construct(){
		 
			
			
		$this->dt_desde = "";
		
		$this->dt_hasta = "";
		
		
	}




	

	 

	

	

	public function getDt_desde()
	{
	    return $this->dt_desde;
	}

	public function setDt_desde($dt_desde)
	{
	    $this->dt_desde = $dt_desde;
	}

	public function getDt_hasta()
	{
	    return $this->dt_hasta;
	}

	public function setDt_hasta($dt_hasta)
	{
	    $this->dt_hasta = $dt_hasta;
	}
}
?>