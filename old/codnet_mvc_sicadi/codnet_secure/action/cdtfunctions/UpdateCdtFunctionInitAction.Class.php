<?php 

/**
 * Acción para inicializar el contexto para modificar
 * un CdtFunction.
 *  
 * @author codnet archetype builder
 * @since 29-12-2011
 *  
 */
class UpdateCdtFunctionInitAction extends EditCdtFunctionInitAction{

	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::getEntity();
	 */
	protected function getEntity(){

		$oCdtFunction = null;

		//recuperamos dado su identifidor.
		$cd_CdtFunction = CdtUtils::getParam('id');
			
		if (!empty( $cd_CdtFunction )) {
						
			$oCriteria = new CdtSearchCriteria();
			$oCriteria->addFilter('cd_function', $cd_CdtFunction, '=');
			
			$manager = new CdtFunctionManager();
			$oCdtFunction = $manager->getCdtFunction( $oCriteria );
			
		}else{
		
			$oCdtFunction = parent::getEntity();
		
		}
		return $oCdtFunction ;
	}

	/**
	 * (non-PHPdoc)
	 * @see EditCdtFunctionInitAction::getSubmitAction();
	 */
	protected function getSubmitAction(){
		return "update_cdtfunction";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::getOutputTitle();
	 */
	protected function getOutputTitle(){
		return CDT_SECURE_MSG_CDTFUNCTION_TITLE_UPDATE;
	}

}
