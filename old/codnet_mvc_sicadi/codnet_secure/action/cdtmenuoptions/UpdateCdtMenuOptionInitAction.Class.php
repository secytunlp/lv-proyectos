<?php 

/**
 * Acción para inicializar el contexto para modificar
 * un CdtMenuOption.
 *  
 * @author codnet archetype builder
 * @since 29-12-2011
 *  
 */
class UpdateCdtMenuOptionInitAction extends EditCdtMenuOptionInitAction{

	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::getEntity();
	 */
	protected function getEntity(){

		$oCdtMenuOption = null;

		//recuperamos dado su identifidor.
		$cd_CdtMenuOption = CdtUtils::getParam('id');
			
		if (!empty( $cd_CdtMenuOption )) {
						
			$oCriteria = new CdtSearchCriteria();
			$oCriteria->addFilter('cd_menuoption', $cd_CdtMenuOption, '=');
			
			$manager = new CdtMenuOptionManager();
			$oCdtMenuOption = $manager->getCdtMenuOption( $oCriteria );
			
		}else{
		
			$oCdtMenuOption = parent::getEntity();
		
		}
		return $oCdtMenuOption ;
	}

	/**
	 * (non-PHPdoc)
	 * @see EditCdtMenuOptionInitAction::getSubmitAction();
	 */
	protected function getSubmitAction(){
		return "update_cdtmenuoption";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::getOutputTitle();
	 */
	protected function getOutputTitle(){
		return CDT_SECURE_MSG_CDTMENUOPTION_TITLE_UPDATE;
	}

}
