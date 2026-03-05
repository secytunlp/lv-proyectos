<?php 

/**
 * Acci�n para visualizar un CdtMenuGroup.
 *  
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
class ViewCdtMenuGroupAction extends CdtOutputAction{

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
		
		$cd_CdtMenuGroup = CdtUtils::getParam('id');
			
		if (!empty( $cd_CdtMenuGroup )) {

			
			$oCriteria = new CdtSearchCriteria();
			$oCriteria->addFilter('cd_menugroup', $cd_CdtMenuGroup, '=');
			
			$manager = new CdtMenuGroupManager();
			$oCdtMenuGroup = $manager->getCdtMenuGroup( $oCriteria );
			
		}else{
		
			$oCdtMenuGroup = parent::getEntity();
		
		}
		
		//parseamos CdtMenuGroup.
		$this->parseEntity( $xtpl, $oCdtMenuGroup );
			
		$xtpl->assign ( 'title', $this->getOutputTitle() );
		$xtpl->parse ( 'main' );
		return $xtpl->text ( 'main' );
	}

	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getOutputTitle();
	 */
	protected function getOutputTitle(){
		return CDT_SECURE_MSG_CDTMENUGROUP_TITLE_VIEW;
	}

	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getXTemplate();
	 */
	public function getXTemplate(){ 
		return new XTemplate ( CDT_SECURE_TEMPLATE_CDTMENUGROUP_VIEW );
	}
	

	/**
	 * parseamos la entity en el template
	 * @param XTemplate $xtpl template donde parsear la entity
	 * @param object $oCdtMenuGroup entity a parsear
	 */
	public function parseEntity(XTemplate $xtpl, $oCdtMenuGroup){ 

				
		$xtpl->assign ( 'cd_menugroup', stripslashes ( $oCdtMenuGroup->getCd_menugroup () ) );
		$xtpl->assign ( 'cd_menugroup_label', CDT_SECURE_LBL_CDTMENUGROUP_CD_MENUGROUP );
				
		$xtpl->assign ( 'nu_order', stripslashes ( $oCdtMenuGroup->getNu_order () ) );
		$xtpl->assign ( 'nu_order_label', CDT_SECURE_LBL_CDTMENUGROUP_NU_ORDER );
				
		$xtpl->assign ( 'nu_width', stripslashes ( $oCdtMenuGroup->getNu_width () ) );
		$xtpl->assign ( 'nu_width_label', CDT_SECURE_LBL_CDTMENUGROUP_NU_WIDTH );
				
		$xtpl->assign ( 'ds_name', stripslashes ( $oCdtMenuGroup->getDs_name () ) );
		$xtpl->assign ( 'ds_name_label', CDT_SECURE_LBL_CDTMENUGROUP_DS_NAME );
				
		$xtpl->assign ( 'ds_action', stripslashes ( $oCdtMenuGroup->getDs_action () ) );
		$xtpl->assign ( 'ds_action_label', CDT_SECURE_LBL_CDTMENUGROUP_DS_ACTION );
				
		$xtpl->assign ( 'ds_cssclass', stripslashes ( $oCdtMenuGroup->getDs_cssclass () ) );
		$xtpl->assign ( 'ds_cssclass_label', CDT_SECURE_LBL_CDTMENUGROUP_DS_CSSCLASS );
		
		
	}
}
