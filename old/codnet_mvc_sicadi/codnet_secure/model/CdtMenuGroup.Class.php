<?php 

/** 
 * CdtMenuGroup class 
 *  
 * @author codnet archetype builder
 * @since 09-11-2011
 */ 
class CdtMenuGroup { 
	
	//variables de instancia.
	
	private $cd_menugroup;
	
	private $nu_order;
	
	private $nu_width;
	
	private $ds_name;
	
	private $ds_action;
	
	private $ds_cssclass;
	
	private $options;

	//Constructor.
	public function __construct() {
		//inicializar variables.
		
		
		$this->cd_menugroup = '';
		
		$this->nu_order = '';
		
		$this->nu_width = '';
		
		$this->ds_name = '';
		
		$this->ds_action = '';
		
		$this->ds_cssclass = '';
		
		$this->setOptions( new ItemCollection() );
		
	}

	//Getters	
		
	public function getCd_menugroup() { 
		return $this->cd_menugroup;
	}
		
	public function getNu_order() { 
		return $this->nu_order;
	}
		
	public function getNu_width() { 
		return $this->nu_width;
	}
		
	public function getDs_name() { 
		return $this->ds_name;
	}
		
	public function getDs_action() { 
		return $this->ds_action;
	}
		
	public function getDs_cssclass() { 
		return $this->ds_cssclass;
	}
	
	

	//Setters
	
	public function setCd_menugroup( $value ) { 
		$this->cd_menugroup = $value;
	}
	
	public function setNu_order( $value ) { 
		$this->nu_order = $value;
	}
	
	public function setNu_width( $value ) { 
		$this->nu_width = $value;
	}
	
	public function setDs_name( $value ) { 
		$this->ds_name = $value;
	}
	
	public function setDs_action( $value ) { 
		$this->ds_action = $value;
	}
	
	public function setDs_cssclass( $value ) { 
		$this->ds_cssclass = $value;
	}
	
	

	public function getOptions()
	{
	    return $this->options;
	}

	public function setOptions($options)
	{
	    $this->options = $options;
	}

	public function hasPermission( CdtUser $oUser ){
		
		$has = false;
		
		foreach($this->getOptions() as $option){

			if( $option->hasPermission( $oUser ) ){
				$has = true;
				break;
			}
			
		}
		return $has;
	}
} 
?>
