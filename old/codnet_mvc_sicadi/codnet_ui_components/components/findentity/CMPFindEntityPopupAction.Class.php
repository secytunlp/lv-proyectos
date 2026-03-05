<?php
/**
 * Action para obtener una entity consultando por popup.
 * 
 * @author bernardo
 * @since 31/05/2013
 */
class CMPFindEntityPopupAction extends CMPEntityGridAction {

	protected function getLayout(){
		$oLayout = new CdtLayoutBasicAjax();
		return $oLayout;
	}
	
	/*
	protected function getComponent(){
		
		$component = parent::getComponent();
		$gridId = CdtUtils::getParam("inputId", CdtUtils::getParamPOST("gridId", "") );
		$component->setId( $gridId );
		
		$fCallback = CdtUtils::getParam("callback", CdtUtils::getParamPOST("callback", "") );
		$component->setJCallback( $gridId );
		
		return $component;
	}	
	*/
	/*
	protected function getNewGrid(){
		$oGrid =  new CMPGrid();
		$inputId = CdtUtils::getParam("inputId", CdtUtils::getParam("gridId", ""));
		$oGrid->setId( $inputId );
		return $oGrid;	
	}
	
	
	protected function getGridModel( CMPGrid $oGrid ){
		$gridModelClazz  =  CdtUtils::getParam("model_" . $oGrid->getId()) ;
		return CdtReflectionUtils::newInstance( $gridModelClazz );
	}*/
}
?>