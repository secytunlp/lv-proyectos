<?php 

/**
 * Acción para inicializar el contexto para dar de alta
 * un CdtFunction.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
class AddCdtFunctionInitAction extends EditCdtFunctionInitAction{

	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getOutputTitle();
	 */
	protected function getOutputTitle(){
		return CDT_SECURE_MSG_CDTFUNCTION_TITLE_ADD;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see EditCdtFunctionInitAction::getSubmitAction();
	 */
	protected function getSubmitAction(){
		return "add_cdtfunction";
	}

	
}
