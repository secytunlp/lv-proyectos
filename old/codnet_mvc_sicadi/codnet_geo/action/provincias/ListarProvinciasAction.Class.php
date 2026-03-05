<?php 

/**
 * Acción listar provincias.
 * 
 * @author bernardo
 * @since 18-03-2010
 * 
 */
class ListarProvinciasAction extends ListarAction{

	/**
	 * (non-PHPdoc)
	 * @see clases/com/bulfon/bulfon/action/ListarAction#getListarTableModel($items)
	 */
	protected function getListarTableModel( ItemCollection $items ){
		return new ProvinciaTableModel($items);
	}

	/**
	 * (non-PHPdoc)
	 * @see clases/com/bulfon/bulfon/action/ListarAction#getOpciones()
	 */
	protected function getOpciones(){
		$opciones[]= $this->buildOpcion('altaprovincia', 'Agregar Provincia', 'alta_provincia_init');
		return $opciones;
	}

	/**
	 * (non-PHPdoc)
	 * @see clases/com/bulfon/bulfon/action/ListarAction#getFiltros()
	 */
	protected function getFiltros(){
		$filtros[]= $this->buildFiltro('ds_provincia', $this->tableModel->getColumnName(1));
		return $filtros;
	}


	/**
	 * (non-PHPdoc)
	 * @see clases/com/bulfon/bulfon/action/ListarAction#parseAcciones($xtpl, $item)
	 */
	protected function parseAcciones(XTemplate $xtpl, $item){

		$this->parseAccionesDefault( $xtpl, $item, $item->getCd_provincia(), 'provincia', 'provincia' );
	}


	/**
	 * (non-PHPdoc)
	 * @see clases/com/bulfon/bulfon/action/ListarAction#getEntidadManager()
	 */
	protected function getEntidadManager(){
		return new ProvinciaManager();
	}

	/**
	 * (non-PHPdoc)
	 * @see clases/com/bulfon/bulfon/action/ListarAction#getCampoOrdenDefault()
	 */
	protected function getCampoOrdenDefault(){
		return 'ds_provincia';
	}

	/**
	 * (non-PHPdoc)
	 * @see clases/com/bulfon/bulfon/action/ListarAction#getMsjError1()
	 */
	protected function getMsjError1(){
		return 'No se pudo eliminar la Provincia. Verifique que no existan datos relacionados';
	}

	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/action/generic/SecureOutputAction#getTitulo()
	 */
	protected function getTitulo(){
		return 'Administración de Provincias';
	}

	/**
	 * (non-PHPdoc)
	 * @see clases/com/bulfon/bulfon/action/ListarAction#getCartelEliminar($entidad)
	 */
	protected function getCartelEliminar($entidad){
		$xtpl = new XTemplate ( CDT_GEO_TEMPLATE_BAJA_PROVINCIA );
		$xtpl->assign ( 'cd_provincia', $entidad->getCd_provincia() );
		$xtpl->assign ( 'ds_provincia', stripslashes( $entidad->getDs_provincia() ) );
		$xtpl->parse('main');
		return FormatUtils::quitarEnters( $xtpl->text('main') );
	}

	/**
	 * (non-PHPdoc)
	 * @see clases/com/bulfon/bulfon/action/ListarAction#getUrlAccionListar()
	 */
	protected function getUrlAccionListar(){
		return 'listar_provincias';
	}

	/**
	 * (non-PHPdoc)
	 * @see clases/com/bulfon/bulfon/action/ListarAction#getUrlAccionExportarPdf()
	 */
	protected function getUrlAccionExportarPdf(){
		return 'pdf_provincias';
	}

	/**
	 * (non-PHPdoc)
	 * @see clases/com/bulfon/bulfon/action/ListarAction#getForwardError()
	 */
	protected function getForwardError(){
		return 'listar_provincias_error';
	}

	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/action/generic/SecureOutputAction#getMenuActivo()
	 */
	protected function getMenuActivo(){
		return "Provincias";
	}



}