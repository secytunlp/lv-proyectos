<?php 

/**
 * Acción para editar CdtRegistration.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
abstract class EditCdtRegistrationAction extends CdtEditAction{

	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getEntity();
	 */
	protected function getEntity(){
		
		//se construye el CdtRegistration a modificar.
		$oCdtRegistration = new CdtRegistration ( );
		
				
		$oCdtRegistration->setCd_registration ( CdtUtils::getParamPOST('cd_registration') );	
				
		$oCdtRegistration->setDs_activationcode ( CdtUtils::getParamPOST('ds_activationcode') );	
				
		$oCdtRegistration->setDt_date ( CdtUtils::getParamPOST('dt_date') );	
				
		$oCdtRegistration->setDs_username ( CdtUtils::getParamPOST('ds_username') );	
				
		$oCdtRegistration->setDs_name ( CdtUtils::getParamPOST('ds_name') );	
				
		$oCdtRegistration->setDs_email ( CdtUtils::getParamPOST('ds_email') );	
				
		$oCdtRegistration->setDs_password ( CdtUtils::getParamPOST('ds_password') );	
				
		$oCdtRegistration->setDs_phone ( CdtUtils::getParamPOST('ds_phone') );	
				
		$oCdtRegistration->setDs_address ( CdtUtils::getParamPOST('ds_address') );	
		
					
		return $oCdtRegistration;
	}
	
		
}
