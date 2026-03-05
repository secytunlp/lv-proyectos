<?php 

/**
 * Acción para modificar CdtUserGroupFunction.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 *  
 */
class UpdateCdtUserGroupFunctionAction extends EditCdtUserGroupFunctionAction{


	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::edit();
	 */
	protected function edit($oEntity){
		$manager = new CdtUserGroupFunctionManager();
		$manager->updateCdtUserGroupFunction( $oEntity );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getForwardSuccess();
	 */
	protected function getForwardSuccess(){
		return 'update_cdtusergroupfunction_success';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getForwardError();
	 */
	protected function getForwardError(){
		return 'update_cdtusergroupfunction_error';
	}

	
}
