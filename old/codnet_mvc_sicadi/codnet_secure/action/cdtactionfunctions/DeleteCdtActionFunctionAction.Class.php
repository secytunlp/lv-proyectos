<?php

/**
 * Acción para eliminar CdtActionFunction.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
class DeleteCdtActionFunctionAction extends CdtEditAction {

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
		$manager = new CdtActionFunctionManager();
		$manager->deleteCdtActionFunction( $oEntity );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getForwardSuccess();
	 */
	protected function getForwardSuccess(){
		$this->setDs_forward_params("search=1");
		return 'delete_cdtactionfunction_success';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getForwardError();
	 */
	protected function getForwardError(){
		$_GET["search"] = 1;
		return 'delete_cdtactionfunction_error';
	}


}
