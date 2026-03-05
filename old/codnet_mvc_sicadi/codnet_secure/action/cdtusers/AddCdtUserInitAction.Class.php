<?php 

/**
 * Acción para inicializar el contexto para dar de alta
 * un CdtUser.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
class AddCdtUserInitAction extends EditCdtUserInitAction{

	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getOutputTitle();
	 */
	protected function getOutputTitle(){
		return CDT_SECURE_MSG_CDTUSER_TITLE_ADD;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see EditCdtUserInitAction::getSubmitAction();
	 */
	protected function getSubmitAction(){
		return "add_cdtuser";
	}

	
}
