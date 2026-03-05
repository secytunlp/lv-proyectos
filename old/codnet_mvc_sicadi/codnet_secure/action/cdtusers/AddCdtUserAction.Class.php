<?php 

/**
 * Acción para dar de alta un CdtUser.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
class AddCdtUserAction extends EditCdtUserAction{


	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::edit();
	 */
	protected function edit( $oEntity ){
		
		$manager = new CdtUserManager();
		$newPassword = $manager->addCdtUser( $oEntity, true );
		
		//le asignamos la clave sin codificar para que pueda leerla la primera vez.
		$this->setDs_forward_params("id=" . $oEntity->getCd_user() . "&newPassword=$newPassword");
		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getForwardSuccess();
	 */
	protected function getForwardSuccess(){
		return 'add_cdtuser_success';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getForwardError();
	 */
	protected function getForwardError(){
		return 'add_cdtuser_error';
	}
		
	
}
