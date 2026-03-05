<?php 

/**
 * Acción para dar de alta un CdtMenuOption.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
class AddCdtMenuOptionAction extends EditCdtMenuOptionAction{


	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::edit();
	 */
	protected function edit( $oEntity ){
		$manager = new CdtMenuOptionManager();
		$manager->addCdtMenuOption( $oEntity );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getForwardSuccess();
	 */
	protected function getForwardSuccess(){
		return 'add_cdtmenuoption_success';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getForwardError();
	 */
	protected function getForwardError(){
		return 'add_cdtmenuoption_error';
	}
		
	
}
