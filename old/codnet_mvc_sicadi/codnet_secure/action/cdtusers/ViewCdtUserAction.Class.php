<?php 

/**
 * Acci�n para visualizar un CdtUser.
 *  
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
class ViewCdtUserAction extends CdtOutputAction{

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
		
		$cd_CdtUser = CdtUtils::getParam('id');
			
		if (!empty( $cd_CdtUser )) {

			
			$oCriteria = new CdtSearchCriteria();
			$oCriteria->addFilter('cd_user', $cd_CdtUser, '=');
			
			$manager = new CdtUserManager();
			$oCdtUser = $manager->getCdtUserWithUserGroup( $oCriteria );
			
		}else{
		
			$oCdtUser = parent::getEntity();
		
		}
		
		//parseamos CdtUser.
		$this->parseEntity( $xtpl, $oCdtUser );
			
		$xtpl->assign ( 'title', $this->getOutputTitle() );
		$xtpl->parse ( 'main' );
		return $xtpl->text ( 'main' );
	}

	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getOutputTitle();
	 */
	protected function getOutputTitle(){
		return CDT_SECURE_MSG_CDTUSER_TITLE_VIEW;
	}

	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getXTemplate();
	 */
	public function getXTemplate(){ 
		return new XTemplate ( CDT_SECURE_TEMPLATE_CDTUSER_VIEW );
	}
	

	/**
	 * parseamos la entity en el template
	 * @param XTemplate $xtpl template donde parsear la entity
	 * @param object $oCdtUser entity a parsear
	 */
	public function parseEntity(XTemplate $xtpl, $oCdtUser){ 

				
		$xtpl->assign ( 'cd_user', stripslashes ( $oCdtUser->getCd_user () ) );
		$xtpl->assign ( 'cd_user_label', CDT_SECURE_LBL_CDTUSER_CD_USER );
				
		$xtpl->assign ( 'ds_username', stripslashes ( $oCdtUser->getDs_username () ) );
		$xtpl->assign ( 'ds_username_label', CDT_SECURE_LBL_CDTUSER_DS_USERNAME );
				
		$xtpl->assign ( 'ds_name', stripslashes ( $oCdtUser->getDs_name () ) );
		$xtpl->assign ( 'ds_name_label', CDT_SECURE_LBL_CDTUSER_DS_NAME );
				
		$xtpl->assign ( 'ds_email', stripslashes ( $oCdtUser->getDs_email () ) );
		$xtpl->assign ( 'ds_email_label', CDT_SECURE_LBL_CDTUSER_DS_EMAIL );
				
		$xtpl->assign ( 'ds_password', stripslashes ( $oCdtUser->getDs_password () ) );
		$xtpl->assign ( 'ds_password_label', CDT_SECURE_LBL_CDTUSER_DS_PASSWORD );
				
		$xtpl->assign ( 'cd_usergroup', stripslashes ( $oCdtUser->getDs_usergroup () ) );
		$xtpl->assign ( 'cd_usergroup_label', CDT_SECURE_LBL_CDTUSER_CD_USERGROUP );
				
		$xtpl->assign ( 'ds_phone', stripslashes ( $oCdtUser->getDs_phone () ) );
		$xtpl->assign ( 'ds_phone_label', CDT_SECURE_LBL_CDTUSER_DS_PHONE );
				
		$xtpl->assign ( 'ds_address', stripslashes ( $oCdtUser->getDs_address () ) );
		$xtpl->assign ( 'ds_address_label', CDT_SECURE_LBL_CDTUSER_DS_ADDRESS );
		
		$xtpl->assign ( 'ds_ips', stripslashes ( $oCdtUser->getDs_ips () ) );
		$xtpl->assign ( 'ds_ips_label', CDT_SECURE_LBL_CDTUSER_DS_IPS );
	}
}
