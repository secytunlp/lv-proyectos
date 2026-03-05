<?php 

/**
 * Acción para modificar CdtFunction.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 *  
 */
class UpdateCdtFunctionAction extends EditCdtFunctionAction{


	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::edit();
	 */
	protected function edit($oEntity){
		$manager = new CdtFunctionManager();
		$manager->updateCdtFunction( $oEntity );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getForwardSuccess();
	 */
	protected function getForwardSuccess(){
		return 'update_cdtfunction_success';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getForwardError();
	 */
	protected function getForwardError(){
		return 'update_cdtfunction_error';
	}

	
}
