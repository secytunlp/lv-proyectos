<?php 

/**
 * Acci�n para inicializar el contexto para editar CdtMenuGroup.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
abstract class EditCdtMenuGroupInitAction  extends CdtEditInitAction{

	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::getXTemplate();
	 */
	protected function getXTemplate(){
		return new XTemplate ( CDT_SECURE_TEMPLATE_CDTMENUGROUP_EDIT );		
	}

	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::getEntity();
	 */
	protected function getEntity(){
		
		//se construye el CdtMenuGroup a modificar.
		$oCdtMenuGroup = new CdtMenuGroup ( );
	
				
		$oCdtMenuGroup->setCd_menugroup ( CdtUtils::getParamPOST('cd_menugroup') );	
				
		$oCdtMenuGroup->setNu_order ( CdtUtils::getParamPOST('nu_order') );	
				
		$oCdtMenuGroup->setNu_width ( CdtUtils::getParamPOST('nu_width') );	
				
		$oCdtMenuGroup->setDs_name ( CdtUtils::getParamPOST('ds_name') );	
				
		$oCdtMenuGroup->setDs_action ( CdtUtils::getParamPOST('ds_action') );	
				
		$oCdtMenuGroup->setDs_cssclass ( CdtUtils::getParamPOST('ds_cssclass') );	
		
		
		return $oCdtMenuGroup;
	}	
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::parseEntity();
	 */
	protected function parseEntity($entity, XTemplate $xtpl){
		
		$oCdtMenuGroup = CdtFormatUtils::ifEmpty($entity, new CdtMenuGroup());

		//parseamos la entity
		
				
		$xtpl->assign ( 'ds_action', stripslashes ( $oCdtMenuGroup->getDs_action () ) );
		$xtpl->assign ( 'ds_action_label', CDT_SECURE_LBL_CDTMENUGROUP_DS_ACTION );
				
		$xtpl->assign ( 'ds_cssclass', stripslashes ( $oCdtMenuGroup->getDs_cssclass () ) );
		$xtpl->assign ( 'ds_cssclass_label', CDT_SECURE_LBL_CDTMENUGROUP_DS_CSSCLASS );
		
		
				
		$xtpl->assign ( 'cd_menugroup', stripslashes ( $oCdtMenuGroup->getCd_menugroup () ) );
		$xtpl->assign ( 'cd_menugroup_label', CDT_SECURE_LBL_CDTMENUGROUP_CD_MENUGROUP );
		$xtpl->assign ( 'cd_menugroup_required', '*' );
		$xtpl->assign ( 'cd_menugroup_required_msg', CDT_SECURE_MSG_CDTMENUGROUP_CD_MENUGROUP_REQUIRED );
				
		$xtpl->assign ( 'nu_order', stripslashes ( $oCdtMenuGroup->getNu_order () ) );
		$xtpl->assign ( 'nu_order_label', CDT_SECURE_LBL_CDTMENUGROUP_NU_ORDER );
		$xtpl->assign ( 'nu_order_required', '*' );
		$xtpl->assign ( 'nu_order_required_msg', CDT_SECURE_MSG_CDTMENUGROUP_NU_ORDER_REQUIRED );
				
		$xtpl->assign ( 'nu_width', stripslashes ( $oCdtMenuGroup->getNu_width () ) );
		$xtpl->assign ( 'nu_width_label', CDT_SECURE_LBL_CDTMENUGROUP_NU_WIDTH );
		$xtpl->assign ( 'nu_width_required', '*' );
		$xtpl->assign ( 'nu_width_required_msg', CDT_SECURE_MSG_CDTMENUGROUP_NU_WIDTH_REQUIRED );
				
		$xtpl->assign ( 'ds_name', stripslashes ( $oCdtMenuGroup->getDs_name () ) );
		$xtpl->assign ( 'ds_name_label', CDT_SECURE_LBL_CDTMENUGROUP_DS_NAME );
		$xtpl->assign ( 'ds_name_required', '*' );
		$xtpl->assign ( 'ds_name_required_msg', CDT_SECURE_MSG_CDTMENUGROUP_DS_NAME_REQUIRED );
		
		
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
