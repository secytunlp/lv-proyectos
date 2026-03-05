<?php 

/**
 * Acci�n para inicializar el contexto para editar CdtMenuOption.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
abstract class EditCdtMenuOptionInitAction  extends CdtEditInitAction{

	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::getXTemplate();
	 */
	protected function getXTemplate(){
		return new XTemplate ( CDT_SECURE_TEMPLATE_CDTMENUOPTION_EDIT );		
	}

	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::getEntity();
	 */
	protected function getEntity(){
		
		//se construye el CdtMenuOption a modificar.
		$oCdtMenuOption = new CdtMenuOption ( );
	
				
		$oCdtMenuOption->setCd_menuoption ( CdtUtils::getParamPOST('cd_menuoption') );	
				
		$oCdtMenuOption->setDs_name ( CdtUtils::getParamPOST('ds_name') );	
				
		$oCdtMenuOption->setDs_href ( CdtUtils::getParamPOST('ds_href') );	
				
		$oCdtMenuOption->setCd_function ( CdtUtils::getParamPOST('cd_function') );	
				
		$oCdtMenuOption->setNu_order ( CdtUtils::getParamPOST('nu_order') );	
				
		$oCdtMenuOption->setCd_menugroup ( CdtUtils::getParamPOST('cd_menugroup') );	
				
		$oCdtMenuOption->setDs_cssclass ( CdtUtils::getParamPOST('ds_cssclass') );	
				
		$oCdtMenuOption->setDs_description ( CdtUtils::getParamPOST('ds_description') );	
		
		
		return $oCdtMenuOption;
	}	
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::parseEntity();
	 */
	protected function parseEntity($entity, XTemplate $xtpl){
		
		$oCdtMenuOption = CdtFormatUtils::ifEmpty($entity, new CdtMenuOption());

		//parseamos la entity
		
				
		$xtpl->assign ( 'nu_order', stripslashes ( $oCdtMenuOption->getNu_order () ) );
		$xtpl->assign ( 'nu_order_label', CDT_SECURE_LBL_CDTMENUOPTION_NU_ORDER );
				
		$xtpl->assign ( 'ds_cssclass', stripslashes ( $oCdtMenuOption->getDs_cssclass () ) );
		$xtpl->assign ( 'ds_cssclass_label', CDT_SECURE_LBL_CDTMENUOPTION_DS_CSSCLASS );
				
		$xtpl->assign ( 'ds_description', stripslashes ( $oCdtMenuOption->getDs_description () ) );
		$xtpl->assign ( 'ds_description_label', CDT_SECURE_LBL_CDTMENUOPTION_DS_DESCRIPTION );
		
		
				
		$xtpl->assign ( 'cd_menuoption', stripslashes ( $oCdtMenuOption->getCd_menuoption () ) );
		$xtpl->assign ( 'cd_menuoption_label', CDT_SECURE_LBL_CDTMENUOPTION_CD_MENUOPTION );
		$xtpl->assign ( 'cd_menuoption_required', '*' );
		$xtpl->assign ( 'cd_menuoption_required_msg', CDT_SECURE_MSG_CDTMENUOPTION_CD_MENUOPTION_REQUIRED );
				
		$xtpl->assign ( 'ds_name', stripslashes ( $oCdtMenuOption->getDs_name () ) );
		$xtpl->assign ( 'ds_name_label', CDT_SECURE_LBL_CDTMENUOPTION_DS_NAME );
		$xtpl->assign ( 'ds_name_required', '*' );
		$xtpl->assign ( 'ds_name_required_msg', CDT_SECURE_MSG_CDTMENUOPTION_DS_NAME_REQUIRED );
				
		$xtpl->assign ( 'ds_href', stripslashes ( $oCdtMenuOption->getDs_href () ) );
		$xtpl->assign ( 'ds_href_label', CDT_SECURE_LBL_CDTMENUOPTION_DS_HREF );
		$xtpl->assign ( 'ds_href_required', '*' );
		$xtpl->assign ( 'ds_href_required_msg', CDT_SECURE_MSG_CDTMENUOPTION_DS_HREF_REQUIRED );
		
		
		//parseamos las relaciones de la entity
		
		$xtpl->assign ( 'cd_function_label', CDT_SECURE_LBL_CDTMENUOPTION_CD_FUNCTION );
		$selected =  $oCdtMenuOption->getCdtFunction();
		
		$oFindObject = CdtSecureComponentsFactory::getFindObjectCdtFunction( $selected, 'cd_function', false,  CDT_SECURE_MSG_CDTMENUOPTION_CD_FUNCTION_REQUIRED  );

		$xtpl->assign('CdtFunction_find', $oFindObject->show() );
		
		
		$xtpl->assign ( 'cd_menugroup_label', CDT_SECURE_LBL_CDTMENUOPTION_CD_MENUGROUP );
		$selected =  $oCdtMenuOption->getCdtMenuGroup();
		
		$oFindObject = CdtSecureComponentsFactory::getFindObjectCdtMenuGroup( $selected, 'cd_menugroup', false,  CDT_SECURE_MSG_CDTMENUOPTION_CD_MENUGROUP_REQUIRED  );

		$xtpl->assign('CdtMenuGroup_find', $oFindObject->show() );
		
		
		
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
	
	protected function parseCdtMenuGroup($selected, XTemplate $xtpl ){
	
		$manager = new CdtMenuGroupManager();
		$oCriteria = new CdtSearchCriteria();
		$cdtmenugroups = $manager->getCdtMenuGroups( $oCriteria );
		
		$xtpl->assign ( 'lbl_select', CDT_SECURE_LBL_SELECT );
		
		foreach($cdtmenugroups as $key => $oCdtMenuGroup) {
		
			$xtpl->assign ( 'ds_CdtMenuGroup', $oCdtMenuGroup->getCd_menugroup() );
			$xtpl->assign ( 'cd_CdtMenuGroup', FormatUtils::selected($oCdtMenuGroup->getCd_menugroup(), $selected ) );
			
			$xtpl->parse ( 'main.cdtmenugroups_option' );
		}	
	}
	

}
