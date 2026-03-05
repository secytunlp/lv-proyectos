<?php

/**
 * Acción para desbloquear un CdtUser.
 * 
 * @author Bernardo
 * @since 27-02-2013
 * 
 */
class UnblockCdtUserAction extends CdtEditAction {

	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getEntity();
	 */
	protected function getEntity(){
		
		//se obtiene el id del usuario a desbloquear.
		return CdtUtils::getParam('id');
		
	}

	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::edit();
	 */
	protected function edit( $oEntity ){
		$manager = new CdtUserManager();
		$manager->unblockCdtUser( $oEntity );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getForwardSuccess();
	 */
	protected function getForwardSuccess(){
		$this->setDs_forward_params("search=1");
		return 'unblock_cdtuser_success';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getForwardError();
	 */
	protected function getForwardError(){
		$_GET["search"] = 1;
		return 'unblock_cdtuser_error';
	}


}
