<?php 

/**
 * Acción para modificar CdtActionFunction.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 *  
 */
class UpdateCdtActionFunctionAction extends EditCdtActionFunctionAction{


	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::edit();
	 */
	protected function edit($oEntity){
		$manager = new CdtActionFunctionManager();
		$manager->updateCdtActionFunction( $oEntity );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getForwardSuccess();
	 */
	protected function getForwardSuccess(){
		return 'update_cdtactionfunction_success';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getForwardError();
	 */
	protected function getForwardError(){
		return 'update_cdtactionfunction_error';
	}

	
}
