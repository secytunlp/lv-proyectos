<?php 

/**
 * Acción para dar de alta un CdtMenuGroup.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
class AddCdtMenuGroupAction extends EditCdtMenuGroupAction{


	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::edit();
	 */
	protected function edit( $oEntity ){
		$manager = new CdtMenuGroupManager();
		$manager->addCdtMenuGroup( $oEntity );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getForwardSuccess();
	 */
	protected function getForwardSuccess(){
		return 'add_cdtmenugroup_success';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getForwardError();
	 */
	protected function getForwardError(){
		return 'add_cdtmenugroup_error';
	}
		
	
}
