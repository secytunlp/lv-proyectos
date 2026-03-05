<?php 

/**
 * Acción para inicializar el contexto para modificar
 * datos del usuario logueado.
 *  
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 06-01-2012
 *  
 */
class EditUserProfileInitAction extends EditCdtUserInitAction{

	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::getXTemplate();
	 */
	protected function getXTemplate(){
		return new XTemplate ( CDT_SECURE_TEMPLATE_CDTUSERPROFILE_EDIT );		
	}
	
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::getEntity();
	 */
	protected function getEntity(){

		$oCdtUser = null;

		//recuperamos el usuario logueado.
		$cd_CdtUser = CdtUtils::getParamPOST('cd_user');
			
		if (empty( $cd_CdtUser )) {

			$oCdtUser = CdtSecureUtils::getUserLogged();
			
			$oCriteria = new CdtSearchCriteria();
			$oCriteria->addFilter('cd_user', $oCdtUser->getCd_user(), '=');
			
			$manager = new CdtUserManager();
			$oCdtUser = $manager->getCdtUserWithUserGroup( $oCriteria );
			
		}else{
		
			//TODO tomamos los datos editados.
			$oCdtUser = parent::getEntity();
			
			
		
		}
		return $oCdtUser ;
	}

	/**
	 * (non-PHPdoc)
	 * @see EditCdtUserInitAction::getSubmitAction();
	 */
	protected function getSubmitAction(){
		return "edit_cdtuserprofile";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::getOutputTitle();
	 */
	protected function getOutputTitle(){
		return CDT_SECURE_MSG_CDTUSERPROFILE_TITLE_UPDATE;
	}

	protected function parseEntity($entity, XTemplate $xtpl){
		
		parent::parseEntity( $entity, $xtpl );
		
		$xtpl->assign ( 'ds_password_label', CDT_SECURE_LBL_CDTUSER_DS_PASSWORD );
		$xtpl->assign ( 'ds_new_password_label', CDT_SECURE_LBL_CDTUSER_DS_NEW_PASSWORD );
		$xtpl->assign ( 'ds_repeat_new_password_label', CDT_SECURE_LBL_CDTUSER_DS_REPEAT_NEW_PASSWORD );
		
		$xtpl->assign ( 'invalid_passwords', CDT_SECURE_MSG_PASSWORDS_INVALID );
		
		
	}
}
