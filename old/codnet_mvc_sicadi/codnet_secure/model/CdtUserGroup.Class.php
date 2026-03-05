<?php 

/** 
 * CdtUserGroup class 
 *  
 * @author codnet archetype builder
 * @since 09-11-2011
 */ 
class CdtUserGroup { 
	
	//variables de instancia.
	
	private $cd_usergroup;
	
	private $ds_usergroup;
	

	//Constructor.
	public function __construct() {
		//inicializar variables.
		
		
		$this->cd_usergroup = '';
		
		$this->ds_usergroup = '';
		
		
	}

	//Getters	
		
	public function getCd_usergroup() { 
		return $this->cd_usergroup;
	}
		
	public function getDs_usergroup() { 
		return $this->ds_usergroup;
	}
	
	

	//Setters
	
	public function setCd_usergroup( $value ) { 
		$this->cd_usergroup = $value;
	}
	
	public function setDs_usergroup( $value ) { 
		$this->ds_usergroup = $value;
	}
	
	
} 
?>
