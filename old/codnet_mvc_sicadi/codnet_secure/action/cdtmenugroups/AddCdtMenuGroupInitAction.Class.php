<?php 

/**
 * Acción para inicializar el contexto para dar de alta
 * un CdtMenuGroup.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
class AddCdtMenuGroupInitAction extends EditCdtMenuGroupInitAction{

	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getOutputTitle();
	 */
	protected function getOutputTitle(){
		return CDT_SECURE_MSG_CDTMENUGROUP_TITLE_ADD;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see EditCdtMenuGroupInitAction::getSubmitAction();
	 */
	protected function getSubmitAction(){
		return "add_cdtmenugroup";
	}

	
}
