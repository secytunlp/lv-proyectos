<?php 

/**
 * Acción para exportar Localidades a excel .
 * 
 * @author lucrecia
 * @since 07-06-2010
 * 
 */
class ExcelLocalidadesAction extends ExportExcelCollectionAction{

	protected function getIListar(){
		return new LocalidadManager();
	}

	protected function getTableModel(ItemCollection $items){
		return new LocalidadTableModel($items);
	}

	protected function getCampoOrdenDefault(){
		return 'cd_localidad';
	}

	public function getTitulo(){
		return "Listado de Localidades";
	}

	public function getNombreArchivo(){
		return "Localidades";
	}

	
}