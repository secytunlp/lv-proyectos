<?php 

/**
 * Acción para modificar CdtMenuOption.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 *  
 */
class UpdateCdtMenuOptionAction extends EditCdtMenuOptionAction{


	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::edit();
	 */
	protected function edit($oEntity){
		$manager = new CdtMenuOptionManager();
		$manager->updateCdtMenuOption( $oEntity );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getForwardSuccess();
	 */
	protected function getForwardSuccess(){
		return 'update_cdtmenuoption_success';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getForwardError();
	 */
	protected function getForwardError(){
		return 'update_cdtmenuoption_error';
	}

	
}
