<?php 

/**
 * Acci�n para inicializar el contexto para editar CdtRegistration.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
abstract class EditCdtRegistrationInitAction  extends CdtEditInitAction{

	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::getXTemplate();
	 */
	protected function getXTemplate(){
		return new XTemplate ( CDT_SECURE_TEMPLATE_CDTREGISTRATION_EDIT );		
	}

	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::getEntity();
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
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::parseEntity();
	 */
	protected function parseEntity($entity, XTemplate $xtpl){
		
		$oCdtRegistration = CdtFormatUtils::ifEmpty($entity, new CdtRegistration());

		//parseamos la entity
		
				
		$xtpl->assign ( 'ds_name', stripslashes ( $oCdtRegistration->getDs_name () ) );
		$xtpl->assign ( 'ds_name_label', CDT_SECURE_LBL_CDTREGISTRATION_DS_NAME );
				
		$xtpl->assign ( 'ds_email', stripslashes ( $oCdtRegistration->getDs_email () ) );
		$xtpl->assign ( 'ds_email_label', CDT_SECURE_LBL_CDTREGISTRATION_DS_EMAIL );
		$xtpl->assign ( 'ds_mail_mail_msg', CDT_SECURE_MSG_EMAIL_INVALID );
				
		$xtpl->assign ( 'ds_phone', stripslashes ( $oCdtRegistration->getDs_phone () ) );
		$xtpl->assign ( 'ds_phone_label', CDT_SECURE_LBL_CDTREGISTRATION_DS_PHONE );
				
		$xtpl->assign ( 'ds_address', stripslashes ( $oCdtRegistration->getDs_address () ) );
		$xtpl->assign ( 'ds_address_label', CDT_SECURE_LBL_CDTREGISTRATION_DS_ADDRESS );
		
		
				
		$xtpl->assign ( 'cd_registration', stripslashes ( $oCdtRegistration->getCd_registration () ) );
		$xtpl->assign ( 'cd_registration_label', CDT_SECURE_LBL_CDTREGISTRATION_CD_REGISTRATION );
		$xtpl->assign ( 'cd_registration_required', '*' );
		$xtpl->assign ( 'cd_registration_required_msg', CDT_SECURE_MSG_CDTREGISTRATION_CD_REGISTRATION_REQUIRED );
				
		$xtpl->assign ( 'ds_activationcode', stripslashes ( $oCdtRegistration->getDs_activationcode () ) );
		$xtpl->assign ( 'ds_activationcode_label', CDT_SECURE_LBL_CDTREGISTRATION_DS_ACTIVATIONCODE );
		$xtpl->assign ( 'ds_activationcode_required', '*' );
		$xtpl->assign ( 'ds_activationcode_required_msg', CDT_SECURE_MSG_CDTREGISTRATION_DS_ACTIVATIONCODE_REQUIRED );
				
		$xtpl->assign ( 'dt_date', stripslashes ( $oCdtRegistration->getDt_date () ) );
		$xtpl->assign ( 'dt_date_label', CDT_SECURE_LBL_CDTREGISTRATION_DT_DATE );
		$xtpl->assign ( 'dt_date_required', '*' );
		$xtpl->assign ( 'dt_date_required_msg', CDT_SECURE_MSG_CDTREGISTRATION_DT_DATE_REQUIRED );
				
		$xtpl->assign ( 'ds_username', stripslashes ( $oCdtRegistration->getDs_username () ) );
		$xtpl->assign ( 'ds_username_label', CDT_SECURE_LBL_CDTREGISTRATION_DS_USERNAME );
		$xtpl->assign ( 'ds_username_required', '*' );
		$xtpl->assign ( 'ds_username_required_msg', CDT_SECURE_MSG_CDTREGISTRATION_DS_USERNAME_REQUIRED );
				
		$xtpl->assign ( 'ds_password', stripslashes ( $oCdtRegistration->getDs_password () ) );
		$xtpl->assign ( 'ds_password_label', CDT_SECURE_LBL_CDTREGISTRATION_DS_PASSWORD );
		$xtpl->assign ( 'ds_password_required', '*' );
		$xtpl->assign ( 'ds_password_required_msg', CDT_SECURE_MSG_CDTREGISTRATION_DS_PASSWORD_REQUIRED );
		
		
		//parseamos las relaciones de la entity
		
		
		//parseamos el action submit.
		$xtpl->assign('submit',  $this->getSubmitAction() );
		
		$xtpl->assign ( 'lbl_save', CDT_SECURE_LBL_SAVE);
		$xtpl->assign ( 'lbl_cancel', CDT_SECURE_LBL_CANCEL);
		$xtpl->assign ( 'msg_required_fields', CDT_SECURE_MSG_REQUIRED_FIELDS);
		
	}

	/**
	 * retorna el action para el submit.
	 * @return string
	 */
	protected abstract function getSubmitAction();
	
	

}
