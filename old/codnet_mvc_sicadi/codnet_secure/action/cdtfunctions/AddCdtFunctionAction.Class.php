<?php 

/**
 * Acción para dar de alta un CdtFunction.
 * 
 * @author codnet archetype builder
 * @since 09-11-2011
 * 
 */
class AddCdtFunctionAction extends EditCdtFunctionAction{


	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::edit();
	 */
	protected function edit( $oEntity ){
		$manager = new CdtFunctionManager();
		$manager->addCdtFunction( $oEntity );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getForwardSuccess();
	 */
	protected function getForwardSuccess(){
		return 'add_cdtfunction_success';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getForwardError();
	 */
	protected function getForwardError(){
		return 'add_cdtfunction_error';
	}
		
	
}
