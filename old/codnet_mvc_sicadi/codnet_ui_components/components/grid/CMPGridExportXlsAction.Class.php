<?php
/**
 * acci�n para los request del componente grilla
 * 
 * para cada grilla espec�fica tenemos que crear una subclase
 * que define el entitymanager y el columnmodel.
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 15-12-2011
 *
 */
class CMPGridExportXlsAction extends CMPGridAction{

	public function execute(){

		$oGrid = $this->buildGrid();
		CdtUIUtils::setCharset("application/vnd.ms-excel");
		$oGrid->show();
		
		return null;
	}

	protected function getGridRenderer( $oGrid ){
		
		return new GridXlsRenderer();
		
	}
	
	
}
?>