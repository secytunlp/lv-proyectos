<?php 

/**
 * Acción para inicializar el contexto para dar de alta
 * un CdtActionFunction.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
class AddCdtActionFunctionInitAction extends EditCdtActionFunctionInitAction{

	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getOutputTitle();
	 */
	protected function getOutputTitle(){
		return CDT_SECURE_MSG_CDTACTIONFUNCTION_TITLE_ADD;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see EditCdtActionFunctionInitAction::getSubmitAction();
	 */
	protected function getSubmitAction(){
		return "add_cdtactionfunction";
	}

	
}
