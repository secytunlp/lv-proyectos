<?php 

/**
 * Acción para inicializar el contexto para dar de alta
 * un CdtUserGroup.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
class AddCdtUserGroupInitAction extends EditCdtUserGroupInitAction{

	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getOutputTitle();
	 */
	protected function getOutputTitle(){
		return CDT_SECURE_MSG_CDTUSERGROUP_TITLE_ADD;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see EditCdtUserGroupInitAction::getSubmitAction();
	 */
	protected function getSubmitAction(){
		return "add_cdtusergroup";
	}

	
}
