<?php 

/**
 * Acción para dar de alta un CdtUserGroup.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
class AddCdtUserGroupAction extends EditCdtUserGroupAction{


	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::edit();
	 */
	protected function edit( $oEntity ){
		$manager = new CdtUserGroupManager();
		$manager->addCdtUserGroup( $oEntity );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getForwardSuccess();
	 */
	protected function getForwardSuccess(){
		return 'add_cdtusergroup_success';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getForwardError();
	 */
	protected function getForwardError(){
		return 'add_cdtusergroup_error';
	}
		
	
}
