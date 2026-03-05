<?php 

/**
 * Acción para inicializar el contexto para modificar
 * un CdtUserGroupFunction.
 *  
 * @author codnet archetype builder
 * @since 29-12-2011
 *  
 */
class UpdateCdtUserGroupFunctionInitAction extends EditCdtUserGroupFunctionInitAction{

	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::getEntity();
	 */
	protected function getEntity(){

		$oCdtUserGroupFunction = null;

		//recuperamos dado su identifidor.
		$cd_CdtUserGroupFunction = CdtUtils::getParam('id');
			
		if (!empty( $cd_CdtUserGroupFunction )) {
						
			$oCriteria = new CdtSearchCriteria();
			$oCriteria->addFilter('cd_usergroup_function', $cd_CdtUserGroupFunction, '=');
			
			$manager = new CdtUserGroupFunctionManager();
			$oCdtUserGroupFunction = $manager->getCdtUserGroupFunction( $oCriteria );
			
		}else{
		
			$oCdtUserGroupFunction = parent::getEntity();
		
		}
		return $oCdtUserGroupFunction ;
	}

	/**
	 * (non-PHPdoc)
	 * @see EditCdtUserGroupFunctionInitAction::getSubmitAction();
	 */
	protected function getSubmitAction(){
		return "update_cdtusergroupfunction";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::getOutputTitle();
	 */
	protected function getOutputTitle(){
		return CDT_SECURE_MSG_CDTUSERGROUPFUNCTION_TITLE_UPDATE;
	}

}
