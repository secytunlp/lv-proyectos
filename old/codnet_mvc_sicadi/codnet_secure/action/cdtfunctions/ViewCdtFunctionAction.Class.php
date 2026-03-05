<?php 

/**
 * Acci�n para visualizar un CdtFunction.
 *  
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
class ViewCdtFunctionAction extends CdtOutputAction{

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
		
		$cd_CdtFunction = CdtUtils::getParam('id');
			
		if (!empty( $cd_CdtFunction )) {

			
			$oCriteria = new CdtSearchCriteria();
			$oCriteria->addFilter('cd_function', $cd_CdtFunction, '=');
			
			$manager = new CdtFunctionManager();
			$oCdtFunction = $manager->getCdtFunction( $oCriteria );
			
		}else{
		
			$oCdtFunction = parent::getEntity();
		
		}
		
		//parseamos CdtFunction.
		$this->parseEntity( $xtpl, $oCdtFunction );
			
		$xtpl->assign ( 'title', $this->getOutputTitle() );
		$xtpl->parse ( 'main' );
		return $xtpl->text ( 'main' );
	}

	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getOutputTitle();
	 */
	protected function getOutputTitle(){
		return CDT_SECURE_MSG_CDTFUNCTION_TITLE_VIEW;
	}

	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getXTemplate();
	 */
	public function getXTemplate(){ 
		return new XTemplate ( CDT_SECURE_TEMPLATE_CDTFUNCTION_VIEW );
	}
	

	/**
	 * parseamos la entity en el template
	 * @param XTemplate $xtpl template donde parsear la entity
	 * @param object $oCdtFunction entity a parsear
	 */
	public function parseEntity(XTemplate $xtpl, $oCdtFunction){ 

				
		$xtpl->assign ( 'cd_function', stripslashes ( $oCdtFunction->getCd_function () ) );
		$xtpl->assign ( 'cd_function_label', CDT_SECURE_LBL_CDTFUNCTION_CD_FUNCTION );
				
		$xtpl->assign ( 'ds_function', stripslashes ( $oCdtFunction->getDs_function () ) );
		$xtpl->assign ( 'ds_function_label', CDT_SECURE_LBL_CDTFUNCTION_DS_FUNCTION );
		
		
	}
}
