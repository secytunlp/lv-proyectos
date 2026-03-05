<?php 

/**
 * Acción para dar de alta un CdtActionFunction.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
class AddCdtActionFunctionAction extends EditCdtActionFunctionAction{


	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::edit();
	 */
	protected function edit( $oEntity ){
		$manager = new CdtActionFunctionManager();
		$manager->addCdtActionFunction( $oEntity );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getForwardSuccess();
	 */
	protected function getForwardSuccess(){
		return 'add_cdtactionfunction_success';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getForwardError();
	 */
	protected function getForwardError(){
		return 'add_cdtactionfunction_error';
	}
		
	
}
