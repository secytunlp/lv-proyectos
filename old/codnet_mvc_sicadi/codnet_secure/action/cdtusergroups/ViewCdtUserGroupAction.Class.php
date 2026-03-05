<?php 

/**
 * Acci�n para visualizar un CdtUserGroup.
 *  
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
class ViewCdtUserGroupAction extends CdtOutputAction{

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
		
		$cd_CdtUserGroup = CdtUtils::getParam('id');
			
		if (!empty( $cd_CdtUserGroup )) {

			
			$oCriteria = new CdtSearchCriteria();
			$oCriteria->addFilter('cd_usergroup', $cd_CdtUserGroup, '=');
			
			$manager = new CdtUserGroupManager();
			$oCdtUserGroup = $manager->getCdtUserGroup( $oCriteria );
			
		}else{
		
			$oCdtUserGroup = parent::getEntity();
		
		}
		
		//parseamos CdtUserGroup.
		$this->parseEntity( $xtpl, $oCdtUserGroup );
			
		$xtpl->assign ( 'title', $this->getOutputTitle() );
		$xtpl->parse ( 'main' );
		return $xtpl->text ( 'main' );
	}

	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getOutputTitle();
	 */
	protected function getOutputTitle(){
		return CDT_SECURE_MSG_CDTUSERGROUP_TITLE_VIEW;
	}

	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getXTemplate();
	 */
	public function getXTemplate(){ 
		return new XTemplate ( CDT_SECURE_TEMPLATE_CDTUSERGROUP_VIEW );
	}
	

	/**
	 * parseamos la entity en el template
	 * @param XTemplate $xtpl template donde parsear la entity
	 * @param object $oCdtUserGroup entity a parsear
	 */
	public function parseEntity(XTemplate $xtpl, $oCdtUserGroup){ 

				
		$xtpl->assign ( 'cd_usergroup', stripslashes ( $oCdtUserGroup->getCd_usergroup () ) );
		$xtpl->assign ( 'cd_usergroup_label', CDT_SECURE_LBL_CDTUSERGROUP_CD_USERGROUP );
				
		$xtpl->assign ( 'ds_usergroup', stripslashes ( $oCdtUserGroup->getDs_usergroup () ) );
		$xtpl->assign ( 'ds_usergroup_label', CDT_SECURE_LBL_CDTUSERGROUP_DS_USERGROUP );
		
		
	}
}
