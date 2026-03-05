<?php 

/**
 * Acci�n para visualizar un CdtMenuOption.
 *  
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
class ViewCdtMenuOptionAction extends CdtOutputAction{

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
		
		$cd_CdtMenuOption = CdtUtils::getParam('id');
			
		if (!empty( $cd_CdtMenuOption )) {

			
			$oCriteria = new CdtSearchCriteria();
			$oCriteria->addFilter('cd_menuoption', $cd_CdtMenuOption, '=');
			
			$manager = new CdtMenuOptionManager();
			$oCdtMenuOption = $manager->getCdtMenuOption( $oCriteria );
			
		}else{
		
			$oCdtMenuOption = parent::getEntity();
		
		}
		
		//parseamos CdtMenuOption.
		$this->parseEntity( $xtpl, $oCdtMenuOption );
			
		$xtpl->assign ( 'title', $this->getOutputTitle() );
		$xtpl->parse ( 'main' );
		return $xtpl->text ( 'main' );
	}

	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getOutputTitle();
	 */
	protected function getOutputTitle(){
		return CDT_SECURE_MSG_CDTMENUOPTION_TITLE_VIEW;
	}

	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getXTemplate();
	 */
	public function getXTemplate(){ 
		return new XTemplate ( CDT_SECURE_TEMPLATE_CDTMENUOPTION_VIEW );
	}
	

	/**
	 * parseamos la entity en el template
	 * @param XTemplate $xtpl template donde parsear la entity
	 * @param object $oCdtMenuOption entity a parsear
	 */
	public function parseEntity(XTemplate $xtpl, $oCdtMenuOption){ 

				
		$xtpl->assign ( 'cd_menuoption', stripslashes ( $oCdtMenuOption->getCd_menuoption () ) );
		$xtpl->assign ( 'cd_menuoption_label', CDT_SECURE_LBL_CDTMENUOPTION_CD_MENUOPTION );
				
		$xtpl->assign ( 'ds_name', stripslashes ( $oCdtMenuOption->getDs_name () ) );
		$xtpl->assign ( 'ds_name_label', CDT_SECURE_LBL_CDTMENUOPTION_DS_NAME );
				
		$xtpl->assign ( 'ds_href', stripslashes ( $oCdtMenuOption->getDs_href () ) );
		$xtpl->assign ( 'ds_href_label', CDT_SECURE_LBL_CDTMENUOPTION_DS_HREF );
				
		$xtpl->assign ( 'cd_function', stripslashes ( $oCdtMenuOption->getDs_function () ) );
		$xtpl->assign ( 'cd_function_label', CDT_SECURE_LBL_CDTMENUOPTION_CD_FUNCTION );
				
		$xtpl->assign ( 'nu_order', stripslashes ( $oCdtMenuOption->getNu_order () ) );
		$xtpl->assign ( 'nu_order_label', CDT_SECURE_LBL_CDTMENUOPTION_NU_ORDER );
				
		$xtpl->assign ( 'cd_menugroup', stripslashes ( $oCdtMenuOption->getDs_menugroup () ) );
		$xtpl->assign ( 'cd_menugroup_label', CDT_SECURE_LBL_CDTMENUOPTION_CD_MENUGROUP );
				
		$xtpl->assign ( 'ds_cssclass', stripslashes ( $oCdtMenuOption->getDs_cssclass () ) );
		$xtpl->assign ( 'ds_cssclass_label', CDT_SECURE_LBL_CDTMENUOPTION_DS_CSSCLASS );
				
		$xtpl->assign ( 'ds_description', stripslashes ( $oCdtMenuOption->getDs_description () ) );
		$xtpl->assign ( 'ds_description_label', CDT_SECURE_LBL_CDTMENUOPTION_DS_DESCRIPTION );
		
		
	}
}
