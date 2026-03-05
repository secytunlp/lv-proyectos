<?php 

/**
 * Acción para inicializar el contexto para modificar
 * un CdtUserGroup.
 *  
 * @author codnet archetype builder
 * @since 29-12-2011
 *  
 */
class UpdateCdtUserGroupInitAction extends EditCdtUserGroupInitAction{

	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::getEntity();
	 */
	protected function getEntity(){

		$oCdtUserGroup = null;

		//recuperamos dado su identifidor.
		$cd_CdtUserGroup = CdtUtils::getParam('id');
			
		if (!empty( $cd_CdtUserGroup )) {
						
			$oCriteria = new CdtSearchCriteria();
			$oCriteria->addFilter('cd_usergroup', $cd_CdtUserGroup, '=');
			
			$manager = new CdtUserGroupManager();
			$oCdtUserGroup = $manager->getCdtUserGroup( $oCriteria );
			
		}else{
		
			$oCdtUserGroup = parent::getEntity();
		
		}
		return $oCdtUserGroup ;
	}

	/**
	 * (non-PHPdoc)
	 * @see EditCdtUserGroupInitAction::getSubmitAction();
	 */
	protected function getSubmitAction(){
		return "update_cdtusergroup";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::getOutputTitle();
	 */
	protected function getOutputTitle(){
		return CDT_SECURE_MSG_CDTUSERGROUP_TITLE_UPDATE;
	}

}
