<?php 

/**
 * Acci�n para inicializar el contexto para editar CdtUserGroupFunction.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
abstract class EditCdtUserGroupFunctionInitAction  extends CdtEditInitAction{

	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::getXTemplate();
	 */
	protected function getXTemplate(){
		return new XTemplate ( CDT_SECURE_TEMPLATE_CDTUSERGROUPFUNCTION_EDIT );		
	}

	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::getEntity();
	 */
	protected function getEntity(){
		
		//se construye el CdtUserGroupFunction a modificar.
		$oCdtUserGroupFunction = new CdtUserGroupFunction ( );
	
				
		$oCdtUserGroupFunction->setCd_usergroup_function ( CdtUtils::getParamPOST('cd_usergroup_function') );	
				
		$oCdtUserGroupFunction->setCd_usergroup ( CdtUtils::getParamPOST('cd_usergroup') );	
				
		$oCdtUserGroupFunction->setCd_function ( CdtUtils::getParamPOST('cd_function') );	
		
		
		return $oCdtUserGroupFunction;
	}	
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::parseEntity();
	 */
	protected function parseEntity($entity, XTemplate $xtpl){
		
		$oCdtUserGroupFunction = CdtFormatUtils::ifEmpty($entity, new CdtUserGroupFunction());

		//parseamos la entity
		
		
		
				
		$xtpl->assign ( 'cd_usergroup_function', stripslashes ( $oCdtUserGroupFunction->getCd_usergroup_function () ) );
		$xtpl->assign ( 'cd_usergroup_function_label', CDT_SECURE_LBL_CDTUSERGROUPFUNCTION_CD_USERGROUP_FUNCTION );
		$xtpl->assign ( 'cd_usergroup_function_required', '*' );
		$xtpl->assign ( 'cd_usergroup_function_required_msg', CDT_SECURE_MSG_CDTUSERGROUPFUNCTION_CD_USERGROUP_FUNCTION_REQUIRED );
		
		
		//parseamos las relaciones de la entity
		
		$xtpl->assign ( 'cd_usergroup_label', CDT_SECURE_LBL_CDTUSERGROUPFUNCTION_CD_USERGROUP );
		$selected =  $oCdtUserGroupFunction->getCdtUserGroup();
		
		$oFindObject = CdtSecureComponentsFactory::getFindObjectCdtUserGroup( $selected, 'cd_usergroup', true,  CDT_SECURE_MSG_CDTUSERGROUPFUNCTION_CD_USERGROUP_REQUIRED  );

		$xtpl->assign('CdtUserGroup_find', $oFindObject->show() );
		
		
		$xtpl->assign ( 'cd_function_label', CDT_SECURE_LBL_CDTUSERGROUPFUNCTION_CD_FUNCTION );
		$selected =  $oCdtUserGroupFunction->getCdtFunction();
		
		$oFindObject = CdtSecureComponentsFactory::getFindObjectCdtFunction( $selected, 'cd_function', true,  CDT_SECURE_MSG_CDTUSERGROUPFUNCTION_CD_FUNCTION_REQUIRED  );

		$xtpl->assign('CdtFunction_find', $oFindObject->show() );
		
		
		
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
	
	
	protected function parseCdtUserGroup($selected, XTemplate $xtpl ){
	
		$manager = new CdtUserGroupManager();
		$oCriteria = new CdtSearchCriteria();
		$cdtusergroups = $manager->getCdtUserGroups( $oCriteria );
		
		$xtpl->assign ( 'lbl_select', CDT_SECURE_LBL_SELECT );
		
		foreach($cdtusergroups as $key => $oCdtUserGroup) {
		
			$xtpl->assign ( 'ds_CdtUserGroup', $oCdtUserGroup->getCd_usergroup() );
			$xtpl->assign ( 'cd_CdtUserGroup', FormatUtils::selected($oCdtUserGroup->getCd_usergroup(), $selected ) );
			
			$xtpl->parse ( 'main.cdtusergroups_option' );
		}	
	}
	
	protected function parseCdtFunction($selected, XTemplate $xtpl ){
	
		$manager = new CdtFunctionManager();
		$oCriteria = new CdtSearchCriteria();
		$cdtfunctions = $manager->getCdtFunctions( $oCriteria );
		
		$xtpl->assign ( 'lbl_select', CDT_SECURE_LBL_SELECT );
		
		
		foreach($cdtfunctions as $key => $oCdtFunction) {
		
			$xtpl->assign ( 'ds_CdtFunction', $oCdtFunction->getCd_function() );
			$xtpl->assign ( 'cd_CdtFunction', FormatUtils::selected($oCdtFunction->getCd_function(), $selected ) );
			
			$xtpl->parse ( 'main.cdtfunctions_option' );
		}	
	}
	

}
