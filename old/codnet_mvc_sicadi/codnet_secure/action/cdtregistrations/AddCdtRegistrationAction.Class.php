<?php 

/**
 * Acción para dar de alta un CdtRegistration.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
class AddCdtRegistrationAction extends EditCdtRegistrationAction{


	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::edit();
	 */
	protected function edit( $oEntity ){
		$manager = new CdtRegistrationManager();
		$manager->addCdtRegistration( $oEntity );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getForwardSuccess();
	 */
	protected function getForwardSuccess(){
		return 'add_cdtregistration_success';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getForwardError();
	 */
	protected function getForwardError(){
		return 'add_cdtregistration_error';
	}
		
	
}
