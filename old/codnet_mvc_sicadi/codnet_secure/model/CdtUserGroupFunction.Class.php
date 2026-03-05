<?php 

/** 
 * CdtUserGroupFunction class 
 *  
 * @author codnet archetype builder
 * @since 09-11-2011
 */ 
class CdtUserGroupFunction { 
	
	//variables de instancia.
	
	private $cd_usergroup_function;
	
	private $oCdtUserGroup;
	
	private $oCdtFunction;
	

	//Constructor.
	public function CdtUserGroupFunction() { 
		//inicializar variables.
		
		
		$this->cd_usergroup_function = '';
		
		
		$this->oCdtUserGroup = new CdtUserGroup();
		
		$this->oCdtFunction = new CdtFunction();
		
	}

	//Getters	
		
	public function getCd_usergroup_function() { 
		return $this->cd_usergroup_function;
	}
		
	public function getCdtUserGroup() { 
		return $this->oCdtUserGroup;
	}
		
	public function getDs_usergroup() { 
		return $this->oCdtUserGroup->getDs_usergroup();
	}
		
	public function getCdtFunction() { 
		return $this->oCdtFunction;
	}
	
	public function getDs_function() { 
		return $this->oCdtFunction->getDs_function();
	}
	
		
	public function getCd_usergroup() { 
		return $this->oCdtUserGroup->getCd_usergroup();
	}
		
	public function getCd_function() { 
		return $this->oCdtFunction->getCd_function();
	}
	

	//Setters
	
	public function setCd_usergroup_function( $value ) { 
		$this->cd_usergroup_function = $value;
	}
	
	public function setCdtUserGroup( $value ) { 
		$this->oCdtUserGroup = $value;
	}
	
	public function setCdtFunction( $value ) { 
		$this->oCdtFunction = $value;
	}
	
	
	public function setCd_usergroup( $value ) { 
		$this->oCdtUserGroup->setCd_usergroup( $value );
	}
	
	public function setCd_function( $value ) { 
		$this->oCdtFunction->setCd_function( $value );
	}
	
} 
?>
