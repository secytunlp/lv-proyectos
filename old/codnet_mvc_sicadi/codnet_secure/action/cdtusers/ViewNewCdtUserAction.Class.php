<?php 

/**
 * Acción para visualizar un nuevo CdtUser.
 *  
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 06-01-2012
 * 
 */
class ViewNewCdtUserAction extends ViewCdtUserAction{

	protected function getLayout(){
		$oLayout = CdtReflectionUtils::newInstance(DEFAULT_EDIT_LAYOUT);
		return $oLayout;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getOutputTitle();
	 */
	protected function getOutputTitle(){
		return CDT_SECURE_MSG_CDTUSER_TITLE_VIEW_NEW;
	}

	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getXTemplate();
	 */
	public function getXTemplate(){ 
		return new XTemplate ( CDT_SECURE_TEMPLATE_CDTUSER_VIEW_NEW );
	}
	

	/**
	 * parseamos la entity en el template
	 * @param XTemplate $xtpl template donde parsear la entity
	 * @param object $oCdtUser entity a parsear
	 */
	public function parseEntity(XTemplate $xtpl, $oCdtUser){ 

				
		parent::parseEntity( $xtpl, $oCdtUser );
				
		$newPassword = CdtUtils::getParam('newPassword');
				
		$params= array ( 0 => $oCdtUser->getDs_username(), 1 => $newPassword );
		$msg = CdtFormatUtils::formatMessage( CDT_SECURE_MSG_CDTUSER_NEW_USER_PASSWORD, $params);
		
		$xtpl->assign ( 'msg_new_user_password', stripslashes ( $msg ) );
		
		$xtpl->assign ( 'lbl_back', CDT_UI_LBL_BACK );
		
	}
}
