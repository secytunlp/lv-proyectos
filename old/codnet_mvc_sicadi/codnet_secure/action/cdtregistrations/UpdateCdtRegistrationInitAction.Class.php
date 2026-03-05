<?php 

/**
 * Acción para inicializar el contexto para modificar
 * un CdtRegistration.
 *  
 * @author codnet archetype builder
 * @since 29-12-2011
 *  
 */
class UpdateCdtRegistrationInitAction extends EditCdtRegistrationInitAction{

	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::getEntity();
	 */
	protected function getEntity(){

		$oCdtRegistration = null;

		//recuperamos dado su identifidor.
		$cd_CdtRegistration = CdtUtils::getParam('id');
			
		if (!empty( $cd_CdtRegistration )) {
						
			$oCriteria = new CdtSearchCriteria();
			$oCriteria->addFilter('cd_registration', $cd_CdtRegistration, '=');
			
			$manager = new CdtRegistrationManager();
			$oCdtRegistration = $manager->getCdtRegistration( $oCriteria );
			
		}else{
		
			$oCdtRegistration = parent::getEntity();
		
		}
		return $oCdtRegistration ;
	}

	/**
	 * (non-PHPdoc)
	 * @see EditCdtRegistrationInitAction::getSubmitAction();
	 */
	protected function getSubmitAction(){
		return "update_cdtregistration";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::getOutputTitle();
	 */
	protected function getOutputTitle(){
		return CDT_SECURE_MSG_CDTREGISTRATION_TITLE_UPDATE;
	}

}
