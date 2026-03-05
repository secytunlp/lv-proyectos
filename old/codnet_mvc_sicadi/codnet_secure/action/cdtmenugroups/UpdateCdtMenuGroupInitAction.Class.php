<?php 

/**
 * Acción para inicializar el contexto para modificar
 * un CdtMenuGroup.
 *  
 * @author codnet archetype builder
 * @since 29-12-2011
 *  
 */
class UpdateCdtMenuGroupInitAction extends EditCdtMenuGroupInitAction{

	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::getEntity();
	 */
	protected function getEntity(){

		$oCdtMenuGroup = null;

		//recuperamos dado su identifidor.
		$cd_CdtMenuGroup = CdtUtils::getParam('id');
			
		if (!empty( $cd_CdtMenuGroup )) {
						
			$oCriteria = new CdtSearchCriteria();
			$oCriteria->addFilter('cd_menugroup', $cd_CdtMenuGroup, '=');
			
			$manager = new CdtMenuGroupManager();
			$oCdtMenuGroup = $manager->getCdtMenuGroup( $oCriteria );
			
		}else{
		
			$oCdtMenuGroup = parent::getEntity();
		
		}
		return $oCdtMenuGroup ;
	}

	/**
	 * (non-PHPdoc)
	 * @see EditCdtMenuGroupInitAction::getSubmitAction();
	 */
	protected function getSubmitAction(){
		return "update_cdtmenugroup";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::getOutputTitle();
	 */
	protected function getOutputTitle(){
		return CDT_SECURE_MSG_CDTMENUGROUP_TITLE_UPDATE;
	}

}
