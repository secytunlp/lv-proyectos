<?php

class CMPFindPopupAction  extends CMPGridAction {

	protected function getLayout(){
		$oLayout = new CdtLayoutBasicAjax();
		return $oLayout;
	}
	
	protected function getGridRenderer( $oGrid ){
		
		$renderer = CdtReflectionUtils::newInstance( CDT_CMP_DEFAULT_POPUP_GRID_RENDERER );
		
		return $renderer;
		
	}
	
	protected function getNewGrid(){
		$oGrid =  new CMPGrid();
		$inputId = CdtUtils::getParam("inputId", CdtUtils::getParam("gridId", ""));
		$oGrid->setId( $inputId );
		return $oGrid;	
	}
	
}
?>