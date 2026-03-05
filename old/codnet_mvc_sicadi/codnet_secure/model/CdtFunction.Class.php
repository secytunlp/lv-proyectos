<?php 

/** 
 * CdtFunction class 
 *  
 * @author codnet archetype builder
 * @since 09-11-2011
 */ 
class CdtFunction { 
	
	//variables de instancia.
	
	private $cd_function;
	
	private $ds_function;
	

	//Constructor.
	public function __construct() {
		//inicializar variables.
		
		
		$this->cd_function = '';
		
		$this->ds_function = '';
		
		
	}

	//Getters	
		
	public function getCd_function() { 
		return $this->cd_function;
	}
		
	public function getDs_function() { 
		return $this->ds_function;
	}
	
	

	//Setters
	
	public function setCd_function( $value ) { 
		$this->cd_function = $value;
	}
	
	public function setDs_function( $value ) { 
		$this->ds_function = $value;
	}
	
	
} 
?>
