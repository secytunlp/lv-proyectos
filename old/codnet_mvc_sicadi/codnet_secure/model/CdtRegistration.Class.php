<?php 

/** 
 * CdtRegistration class 
 *  
 * @author codnet archetype builder
 * @since 09-11-2011
 */ 
class CdtRegistration { 
	
	//variables de instancia.
	
	private $cd_registration;
	
	private $ds_activationcode;
	
	private $dt_date;
	
	private $ds_username;
	
	private $ds_name;
	
	private $ds_email;
	
	private $ds_password;
	
	private $ds_phone;
	
	private $ds_address;
	

	//Constructor.
	public function CdtRegistration() { 
		//inicializar variables.
		
		
		$this->cd_registration = '';
		
		$this->ds_activationcode = '';
		
		$this->dt_date = '';
		
		$this->ds_username = '';
		
		$this->ds_name = '';
		
		$this->ds_email = '';
		
		$this->ds_password = '';
		
		$this->ds_phone = '';
		
		$this->ds_address = '';
		
		
	}

	//Getters	
		
	public function getCd_registration() { 
		return $this->cd_registration;
	}
		
	public function getDs_activationcode() { 
		return $this->ds_activationcode;
	}
		
	public function getDt_date() { 
		return $this->dt_date;
	}
		
	public function getDs_username() { 
		return $this->ds_username;
	}
		
	public function getDs_name() { 
		return $this->ds_name;
	}
		
	public function getDs_email() { 
		return $this->ds_email;
	}
		
	public function getDs_password() { 
		return $this->ds_password;
	}
		
	public function getDs_phone() { 
		return $this->ds_phone;
	}
		
	public function getDs_address() { 
		return $this->ds_address;
	}
	
	

	//Setters
	
	public function setCd_registration( $value=null ) { 
		$this->cd_registration = $value;
	}
	
	public function setDs_activationcode( $value=null ) { 
		$this->ds_activationcode = $value;
	}
	
	public function setDt_date( $value=null ) { 
		$this->dt_date = $value;
	}
	
	public function setDs_username( $value=null ) { 
		$this->ds_username = $value;
	}
	
	public function setDs_name( $value=null ) { 
		$this->ds_name = $value;
	}
	
	public function setDs_email( $value=null ) { 
		$this->ds_email = $value;
	}
	
	public function setDs_password( $value=null ) { 
		$this->ds_password = $value;
	}
	
	public function setDs_phone( $value=null ) { 
		$this->ds_phone = $value;
	}
	
	public function setDs_address( $value=null ) { 
		$this->ds_address = $value;
	}
	

	public function createCdtUser(){
		
		$oUser = new CdtUser();
		$oUser->setDs_username( $this->getDs_username() );
		$oUser->setDs_password( $this->getDs_password() );
		$oUser->setDs_email( $this->getDs_email() );
		
		return $oUser;
	}
} 
?>
