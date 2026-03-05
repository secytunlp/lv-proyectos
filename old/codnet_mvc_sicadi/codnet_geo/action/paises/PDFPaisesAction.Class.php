<?php 

/**
 * Acción para exportar a pdf una colección de paises.
 * 
 * @author bernardo
 * @since 03-05-2010
 * 
 */
class PDFPaisesAction extends ExportPDFCollectionAction{

	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/pdf/ExportPDFCollectionAction#getIListar()
	 */
	protected function getIListar(){
		return new PaisManager();
	}
	 
	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/pdf/ExportPDFCollectionAction#getTableModel($items)
	 */
	protected function getTableModel(ItemCollection $items){
		return new PaisTableModel($items);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/pdf/ExportPDFCollectionAction#getCampoOrdenDefault()
	 */
	protected function getCampoOrdenDefault(){
		return 'ds_pais';
	}


	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/pdf/ExportPDFCollectionAction#getOrientacion()
	 */
	protected function getOrientacion(){
		return "L";
	}	
}