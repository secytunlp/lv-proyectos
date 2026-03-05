<?php 

/** 
 * CdtActionFunction class 
 *  
 * @author codnet archetype builder
 * @since 09-11-2011
 */ 
class CdtActionFunction { 
	
	//variables de instancia.
	
	private $cd_actionfunction;
	
	private $oCdtFunction;
	
	private $ds_action;
	

	//Constructor.
	public function __construct() {
		//inicializar variables.
		
		
		$this->cd_actionfunction = '';
		
		$this->ds_action = '';
		
		
		$this->oCdtFunction = new CdtFunction();
		
	}

	//Getters	
		
	public function getCd_actionfunction() { 
		return $this->cd_actionfunction;
	}
		
	public function getCdtFunction() { 
		return $this->oCdtFunction;
	}

	public function getDs_function() { 
		return $this->oCdtFunction->getDs_function();
	}
	
	public function getDs_action() { 
		return $this->ds_action;
	}
	
		
	public function getCd_function() { 
		return $this->oCdtFunction->getCd_function();
	}
	

	//Setters
	
	public function setCd_actionfunction( $value ) { 
		$this->cd_actionfunction = $value;
	}
	
	public function setCdtFunction( $value ) { 
		$this->oCdtFunction = $value;
	}
	
	public function setDs_action( $value ) { 
		$this->ds_action = $value;
	}
	
	
	public function setCd_function( $value ) { 
		$this->oCdtFunction->setCd_function( $value );
	}
	
} 
?>
