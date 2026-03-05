<?php

/**
 * Acción para eliminar CdtRegistration.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
class DeleteCdtRegistrationAction extends CdtEditAction {

	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getEntity();
	 */
	protected function getEntity(){
		
		//se obtiene el id de la entidad a eliminar.
		return CdtUtils::getParam('id');
		
	}

	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::edit();
	 */
	protected function edit( $oEntity ){
		$manager = new CdtRegistrationManager();
		$manager->deleteCdtRegistration( $oEntity );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getForwardSuccess();
	 */
	protected function getForwardSuccess(){
		$this->setDs_forward_params("search=1");
		return 'delete_cdtregistration_success';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getForwardError();
	 */
	protected function getForwardError(){
		$_GET["search"] = 1;
		return 'delete_cdtregistration_error';
	}


}
