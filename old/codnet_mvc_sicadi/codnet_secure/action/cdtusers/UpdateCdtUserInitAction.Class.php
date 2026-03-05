<?php 

/**
 * Acción para inicializar el contexto para modificar
 * un CdtUser.
 *  
 * @author codnet archetype builder
 * @since 29-12-2011
 *  
 */
class UpdateCdtUserInitAction extends EditCdtUserInitAction{

	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::getEntity();
	 */
	protected function getEntity(){

		$oCdtUser = null;

		//recuperamos dado su identifidor.
		$cd_CdtUser = CdtUtils::getParam('id');
			
		if (!empty( $cd_CdtUser )) {
						
			$oCriteria = new CdtSearchCriteria();
			$oCriteria->addFilter('cd_user', $cd_CdtUser, '=');
			
			$manager = new CdtUserManager();
			$oCdtUser = $manager->getCdtUser( $oCriteria );
			
		}else{
		
			$oCdtUser = parent::getEntity();
		
		}
		return $oCdtUser ;
	}

	/**
	 * (non-PHPdoc)
	 * @see EditCdtUserInitAction::getSubmitAction();
	 */
	protected function getSubmitAction(){
		return "update_cdtuser";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::getOutputTitle();
	 */
	protected function getOutputTitle(){
		return CDT_SECURE_MSG_CDTUSER_TITLE_UPDATE;
	}

}
