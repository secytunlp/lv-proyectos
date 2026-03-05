<?php 

/**
 * Acci�n para visualizar un CdtActionFunction.
 *  
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
class ViewCdtActionFunctionAction extends CdtOutputAction{

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
		
		$cd_CdtActionFunction = CdtUtils::getParam('id');
			
		if (!empty( $cd_CdtActionFunction )) {

			
			$oCriteria = new CdtSearchCriteria();
			$oCriteria->addFilter('cd_actionfunction', $cd_CdtActionFunction, '=');
			
			$manager = new CdtActionFunctionManager();
			$oCdtActionFunction = $manager->getCdtActionFunction( $oCriteria );
			
		}else{
		
			$oCdtActionFunction = parent::getEntity();
		
		}
		
		//parseamos CdtActionFunction.
		$this->parseEntity( $xtpl, $oCdtActionFunction );
			
		$xtpl->assign ( 'title', $this->getOutputTitle() );
		$xtpl->parse ( 'main' );
		return $xtpl->text ( 'main' );
	}

	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getOutputTitle();
	 */
	protected function getOutputTitle(){
		return CDT_SECURE_MSG_CDTACTIONFUNCTION_TITLE_VIEW;
	}

	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getXTemplate();
	 */
	public function getXTemplate(){ 
		return new XTemplate ( CDT_SECURE_TEMPLATE_CDTACTIONFUNCTION_VIEW );
	}
	

	/**
	 * parseamos la entity en el template
	 * @param XTemplate $xtpl template donde parsear la entity
	 * @param object $oCdtActionFunction entity a parsear
	 */
	public function parseEntity(XTemplate $xtpl, $oCdtActionFunction){ 

				
		$xtpl->assign ( 'cd_actionfunction', stripslashes ( $oCdtActionFunction->getCd_actionfunction () ) );
		$xtpl->assign ( 'cd_actionfunction_label', CDT_SECURE_LBL_CDTACTIONFUNCTION_CD_ACTIONFUNCTION );
				
		$xtpl->assign ( 'cd_function', stripslashes ( $oCdtActionFunction->getDs_function () ) );
		$xtpl->assign ( 'cd_function_label', CDT_SECURE_LBL_CDTACTIONFUNCTION_CD_FUNCTION );
				
		$xtpl->assign ( 'ds_action', stripslashes ( $oCdtActionFunction->getDs_action () ) );
		$xtpl->assign ( 'ds_action_label', CDT_SECURE_LBL_CDTACTIONFUNCTION_DS_ACTION );
		
		
	}
}
