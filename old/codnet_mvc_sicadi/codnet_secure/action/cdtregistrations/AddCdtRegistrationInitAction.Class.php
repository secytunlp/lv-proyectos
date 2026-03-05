<?php 

/**
 * Acción para inicializar el contexto para dar de alta
 * un CdtRegistration.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
class AddCdtRegistrationInitAction extends EditCdtRegistrationInitAction{

	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getOutputTitle();
	 */
	protected function getOutputTitle(){
		return CDT_SECURE_MSG_CDTREGISTRATION_TITLE_ADD;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see EditCdtRegistrationInitAction::getSubmitAction();
	 */
	protected function getSubmitAction(){
		return "add_cdtregistration";
	}

	
}
