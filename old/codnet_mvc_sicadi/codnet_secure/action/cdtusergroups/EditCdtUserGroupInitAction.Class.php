<?php 

/**
 * Acci�n para inicializar el contexto para editar CdtUserGroup.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
abstract class EditCdtUserGroupInitAction  extends CdtEditInitAction{

	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::getXTemplate();
	 */
	protected function getXTemplate(){
		return new XTemplate ( CDT_SECURE_TEMPLATE_CDTUSERGROUP_EDIT );		
	}

	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::getEntity();
	 */
	protected function getEntity(){
		
		//se construye el CdtUserGroup a modificar.
		$oCdtUserGroup = new CdtUserGroup ( );
	
				
		$oCdtUserGroup->setCd_usergroup ( CdtUtils::getParamPOST('cd_usergroup') );	
				
		$oCdtUserGroup->setDs_usergroup ( CdtUtils::getParamPOST('ds_usergroup') );	
		
		
		return $oCdtUserGroup;
	}	
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::parseEntity();
	 */
	protected function parseEntity($entity, XTemplate $xtpl){
		
		$oCdtUserGroup = CdtFormatUtils::ifEmpty($entity, new CdtUserGroup());

		//parseamos la entity
		
				
		$xtpl->assign ( 'ds_usergroup', stripslashes ( $oCdtUserGroup->getDs_usergroup () ) );
		$xtpl->assign ( 'ds_usergroup_label', CDT_SECURE_LBL_CDTUSERGROUP_DS_USERGROUP );
		
		
				
		$xtpl->assign ( 'cd_usergroup', stripslashes ( $oCdtUserGroup->getCd_usergroup () ) );
		$xtpl->assign ( 'cd_usergroup_label', CDT_SECURE_LBL_CDTUSERGROUP_CD_USERGROUP );
		$xtpl->assign ( 'cd_usergroup_required', '*' );
		$xtpl->assign ( 'cd_usergroup_required_msg', CDT_SECURE_MSG_CDTUSERGROUP_CD_USERGROUP_REQUIRED );
		
		
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
