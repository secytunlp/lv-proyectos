<?php 

/**
 * Acción para inicializar el contexto para dar de alta
 * un CdtUserGroupFunction.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
class AddCdtUserGroupFunctionInitAction extends EditCdtUserGroupFunctionInitAction{

	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getOutputTitle();
	 */
	protected function getOutputTitle(){
		return CDT_SECURE_MSG_CDTUSERGROUPFUNCTION_TITLE_ADD;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see EditCdtUserGroupFunctionInitAction::getSubmitAction();
	 */
	protected function getSubmitAction(){
		return "add_cdtusergroupfunction";
	}

	
}
