<?php 

/**
 * Acci�n para visualizar un CdtRegistration.
 *  
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
class ViewCdtRegistrationAction extends CdtOutputAction{

	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getLayout();
	 */
	protected function getLayout(){
		$oLayout = new CdtLayoutBasicAjax();
		return $oLayout;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getOutputContent();
	 */
	protected function getOutputContent(){
			
		$xtpl = $this->getXTemplate ();
		
		$cd_CdtRegistration = CdtUtils::getParam('id');
			
		if (!empty( $cd_CdtRegistration )) {

			
			$oCriteria = new CdtSearchCriteria();
			$oCriteria->addFilter('cd_registration', $cd_CdtRegistration, '=');
			
			$manager = new CdtRegistrationManager();
			$oCdtRegistration = $manager->getCdtRegistration( $oCriteria );
			
		}else{
		
			$oCdtRegistration = parent::getEntity();
		
		}
		
		//parseamos CdtRegistration.
		$this->parseEntity( $xtpl, $oCdtRegistration );
			
		$xtpl->assign ( 'title', $this->getOutputTitle() );
		$xtpl->parse ( 'main' );
		return $xtpl->text ( 'main' );
	}

	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getOutputTitle();
	 */
	protected function getOutputTitle(){
		return CDT_SECURE_MSG_CDTREGISTRATION_TITLE_VIEW;
	}

	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getXTemplate();
	 */
	public function getXTemplate(){ 
		return new XTemplate ( CDT_SECURE_TEMPLATE_CDTREGISTRATION_VIEW );
	}
	

	/**
	 * parseamos la entity en el template
	 * @param XTemplate $xtpl template donde parsear la entity
	 * @param object $oCdtRegistration entity a parsear
	 */
	public function parseEntity(XTemplate $xtpl, $oCdtRegistration){ 

				
		$xtpl->assign ( 'cd_registration', stripslashes ( $oCdtRegistration->getCd_registration () ) );
		$xtpl->assign ( 'cd_registration_label', CDT_SECURE_LBL_CDTREGISTRATION_CD_REGISTRATION );
				
		$xtpl->assign ( 'ds_activationcode', stripslashes ( $oCdtRegistration->getDs_activationcode () ) );
		$xtpl->assign ( 'ds_activationcode_label', CDT_SECURE_LBL_CDTREGISTRATION_DS_ACTIVATIONCODE );
				
		$xtpl->assign ( 'dt_date', stripslashes ( $oCdtRegistration->getDt_date () ) );
		$xtpl->assign ( 'dt_date_label', CDT_SECURE_LBL_CDTREGISTRATION_DT_DATE );
				
		$xtpl->assign ( 'ds_username', stripslashes ( $oCdtRegistration->getDs_username () ) );
		$xtpl->assign ( 'ds_username_label', CDT_SECURE_LBL_CDTREGISTRATION_DS_USERNAME );
				
		$xtpl->assign ( 'ds_name', stripslashes ( $oCdtRegistration->getDs_name () ) );
		$xtpl->assign ( 'ds_name_label', CDT_SECURE_LBL_CDTREGISTRATION_DS_NAME );
				
		$xtpl->assign ( 'ds_email', stripslashes ( $oCdtRegistration->getDs_email () ) );
		$xtpl->assign ( 'ds_email_label', CDT_SECURE_LBL_CDTREGISTRATION_DS_EMAIL );
				
		$xtpl->assign ( 'ds_password', stripslashes ( $oCdtRegistration->getDs_password () ) );
		$xtpl->assign ( 'ds_password_label', CDT_SECURE_LBL_CDTREGISTRATION_DS_PASSWORD );
				
		$xtpl->assign ( 'ds_phone', stripslashes ( $oCdtRegistration->getDs_phone () ) );
		$xtpl->assign ( 'ds_phone_label', CDT_SECURE_LBL_CDTREGISTRATION_DS_PHONE );
				
		$xtpl->assign ( 'ds_address', stripslashes ( $oCdtRegistration->getDs_address () ) );
		$xtpl->assign ( 'ds_address_label', CDT_SECURE_LBL_CDTREGISTRATION_DS_ADDRESS );
		
		
	}
}
