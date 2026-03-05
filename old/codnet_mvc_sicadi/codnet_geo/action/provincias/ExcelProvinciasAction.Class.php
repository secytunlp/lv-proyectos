<?php 

/**
 * Acción para exportar Provincias a excel .
 * 
 * @author lucrecia
 * @since 04-06-2010
 * 
 */
class ExcelProvinciasAction extends ExportExcelCollectionAction{

	protected function getIListar(){
		return new ProvinciaManager();
	}

	protected function getTableModel(ItemCollection $items){
		return new ProvinciaTableModel($items);
	}

	protected function getCampoOrdenDefault(){
		return 'cd_provincia';
	}


	public function getTitulo(){
		return "Listado de Provincias";
	}

	public function getNombreArchivo(){
		return "Provincias";
	}

	
}