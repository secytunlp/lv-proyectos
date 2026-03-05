<?php 

/** 
 * Factory para CdtRegistration
 *  
 * @author codnet archetype builder
 * @since 09-11-2011
 */ 
class CdtRegistrationFactory extends CdtGenericFactory{ 

	public function build($next) { 
		
		$this->setClassName('CdtRegistration');
		$oCdtRegistration = parent::build($next);
		
		 //TODO foreign keys 
		 
		return $oCdtRegistration;
	}
} 
?>
