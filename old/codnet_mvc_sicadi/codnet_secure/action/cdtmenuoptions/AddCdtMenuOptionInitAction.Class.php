<?php 

/**
 * Acción para inicializar el contexto para dar de alta
 * un CdtMenuOption.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
class AddCdtMenuOptionInitAction extends EditCdtMenuOptionInitAction{

	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getOutputTitle();
	 */
	protected function getOutputTitle(){
		return CDT_SECURE_MSG_CDTMENUOPTION_TITLE_ADD;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see EditCdtMenuOptionInitAction::getSubmitAction();
	 */
	protected function getSubmitAction(){
		return "add_cdtmenuoption";
	}

	
}
