<?php 

/** 
 * CdtMenuOption class 
 *  
 * @author codnet archetype builder
 * @since 09-11-2011
 */ 
class CdtMenuOption { 
	
	//variables de instancia.
	
	private $cd_menuoption;
	
	private $ds_name;
	
	private $ds_href;
	
	private $oCdtFunction;
	
	private $nu_order;
	
	private $oCdtMenuGroup;
	
	private $ds_cssclass;
	
	private $ds_description;
	

	//Constructor.
	public function __construct() {
		//inicializar variables.
		
		
		$this->cd_menuoption = '';
		
		$this->ds_name = '';
		
		$this->ds_href = '';
		
		$this->nu_order = '';
		
		$this->ds_cssclass = '';
		
		$this->ds_description = '';
		
		
		$this->oCdtFunction = new CdtFunction();
		
		$this->oCdtMenuGroup = new CdtMenuGroup();
		
	}

	//Getters	
		
	public function getCd_menuoption() { 
		return $this->cd_menuoption;
	}
		
	public function getDs_name() { 
		return $this->ds_name;
	}
		
	public function getDs_href() { 
		return $this->ds_href;
	}
		
	public function getCdtFunction() { 
		return $this->oCdtFunction;
	}
		
	public function getDs_function() { 
		return $this->oCdtFunction->getDs_function();
	}
		
	public function getNu_order() { 
		return $this->nu_order;
	}
		
	public function getCdtMenuGroup() { 
		return $this->oCdtMenuGroup;
	}

	public function getDs_menugroup() { 
		return $this->oCdtMenuGroup->getDs_name();
	}
	
	public function getDs_cssclass() { 
		return $this->ds_cssclass;
	}
		
	public function getDs_description() { 
		return $this->ds_description;
	}
	
		
	public function getCd_function() { 
		return $this->oCdtFunction->getCd_function();
	}
		
	public function getCd_menugroup() { 
		return $this->oCdtMenuGroup->getCd_menugroup();
	}
	

	//Setters
	
	public function setCd_menuoption( $value ) { 
		$this->cd_menuoption = $value;
	}
	
	public function setDs_name( $value ) { 
		$this->ds_name = $value;
	}
	
	public function setDs_href( $value ) { 
		$this->ds_href = $value;
	}
	
	public function setCdtFunction( $value ) { 
		$this->oCdtFunction = $value;
	}
	
	public function setNu_order( $value ) { 
		$this->nu_order = $value;
	}
	
	public function setCdtMenuGroup( $value ) { 
		$this->oCdtMenuGroup = $value;
	}
	
	public function setDs_cssclass( $value ) { 
		$this->ds_cssclass = $value;
	}
	
	public function setDs_description( $value ) { 
		$this->ds_description = $value;
	}
	
	
	public function setCd_function( $value ) { 
		$this->oCdtFunction->setCd_function( $value );
	}
	
	public function setCd_menugroup( $value ) { 
		$this->oCdtMenuGroup->setCd_menugroup( $value );
	}
	
	public function hasPermission( CdtUser $oUser ){
		return CdtSecureUtils::hasPermission( $oUser, $this->getCd_function() );
	}
} 
?>
