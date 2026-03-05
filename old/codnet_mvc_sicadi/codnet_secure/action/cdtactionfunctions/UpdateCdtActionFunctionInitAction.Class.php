<?php 

/**
 * Acción para inicializar el contexto para modificar
 * un CdtActionFunction.
 *  
 * @author codnet archetype builder
 * @since 29-12-2011
 *  
 */
class UpdateCdtActionFunctionInitAction extends EditCdtActionFunctionInitAction{

	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::getEntity();
	 */
	protected function getEntity(){

		$oCdtActionFunction = null;

		//recuperamos dado su identifidor.
		$cd_CdtActionFunction = CdtUtils::getParam('id');
			
		if (!empty( $cd_CdtActionFunction )) {
						
			$oCriteria = new CdtSearchCriteria();
			$oCriteria->addFilter('cd_actionfunction', $cd_CdtActionFunction, '=');
			
			$manager = new CdtActionFunctionManager();
			$oCdtActionFunction = $manager->getCdtActionFunction( $oCriteria );
			
		}else{
		
			$oCdtActionFunction = parent::getEntity();
		
		}
		return $oCdtActionFunction ;
	}

	/**
	 * (non-PHPdoc)
	 * @see EditCdtActionFunctionInitAction::getSubmitAction();
	 */
	protected function getSubmitAction(){
		return "update_cdtactionfunction";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::getOutputTitle();
	 */
	protected function getOutputTitle(){
		return CDT_SECURE_MSG_CDTACTIONFUNCTION_TITLE_UPDATE;
	}

}
