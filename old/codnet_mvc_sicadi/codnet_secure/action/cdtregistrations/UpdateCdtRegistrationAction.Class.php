<?php 

/**
 * Acción para modificar CdtRegistration.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 *  
 */
class UpdateCdtRegistrationAction extends EditCdtRegistrationAction{


	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::edit();
	 */
	protected function edit($oEntity){
		$manager = new CdtRegistrationManager();
		$manager->updateCdtRegistration( $oEntity );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getForwardSuccess();
	 */
	protected function getForwardSuccess(){
		return 'update_cdtregistration_success';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getForwardError();
	 */
	protected function getForwardError(){
		return 'update_cdtregistration_error';
	}

	
}
