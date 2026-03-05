<?php 

/**
 * Acci�n para visualizar un CdtUserGroupFunction.
 *  
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
class ViewCdtUserGroupFunctionAction extends CdtOutputAction{

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
		
		$cd_CdtUserGroupFunction = CdtUtils::getParam('id');
			
		if (!empty( $cd_CdtUserGroupFunction )) {

			
			$oCriteria = new CdtSearchCriteria();
			$oCriteria->addFilter('cd_usergroup_function', $cd_CdtUserGroupFunction, '=');
			
			$manager = new CdtUserGroupFunctionManager();
			$oCdtUserGroupFunction = $manager->getCdtUserGroupFunction( $oCriteria );
			
		}else{
		
			$oCdtUserGroupFunction = parent::getEntity();
		
		}
		
		//parseamos CdtUserGroupFunction.
		$this->parseEntity( $xtpl, $oCdtUserGroupFunction );
			
		$xtpl->assign ( 'title', $this->getOutputTitle() );
		$xtpl->parse ( 'main' );
		return $xtpl->text ( 'main' );
	}

	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getOutputTitle();
	 */
	protected function getOutputTitle(){
		return CDT_SECURE_MSG_CDTUSERGROUPFUNCTION_TITLE_VIEW;
	}

	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getXTemplate();
	 */
	public function getXTemplate(){ 
		return new XTemplate ( CDT_SECURE_TEMPLATE_CDTUSERGROUPFUNCTION_VIEW );
	}
	

	/**
	 * parseamos la entity en el template
	 * @param XTemplate $xtpl template donde parsear la entity
	 * @param object $oCdtUserGroupFunction entity a parsear
	 */
	public function parseEntity(XTemplate $xtpl, $oCdtUserGroupFunction){ 

				
		$xtpl->assign ( 'cd_usergroup_function', stripslashes ( $oCdtUserGroupFunction->getCd_usergroup_function () ) );
		$xtpl->assign ( 'cd_usergroup_function_label', CDT_SECURE_LBL_CDTUSERGROUPFUNCTION_CD_USERGROUP_FUNCTION );
				
		$xtpl->assign ( 'cd_usergroup', stripslashes ( $oCdtUserGroupFunction->getDs_usergroup () ) );
		$xtpl->assign ( 'cd_usergroup_label', CDT_SECURE_LBL_CDTUSERGROUPFUNCTION_CD_USERGROUP );
				
		$xtpl->assign ( 'cd_function', stripslashes ( $oCdtUserGroupFunction->getDs_function () ) );
		$xtpl->assign ( 'cd_function_label', CDT_SECURE_LBL_CDTUSERGROUPFUNCTION_CD_FUNCTION );
		
		
	}
}
