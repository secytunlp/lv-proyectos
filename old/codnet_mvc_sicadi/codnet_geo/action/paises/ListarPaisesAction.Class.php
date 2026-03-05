<?php 

/**
 * Acción listar paises.
 * 
 * @author bernardo
 * @since 18-03-2010
 * 
 */
class ListarPaisesAction extends ListarAction{

	/**
	 * (non-PHPdoc)
	 * @see clases/com/bulfon/bulfon/action/ListarAction#getListarTableModel($items)
	 */
	protected function getListarTableModel( ItemCollection $items ){
		return new PaisTableModel($items);
	}

	/**
	 * (non-PHPdoc)
	 * @see clases/com/bulfon/bulfon/action/ListarAction#getOpciones()
	 */
	protected function getOpciones(){
		$opciones[]= $this->buildOpcion('altapais', 'Agregar Pais', 'alta_pais_init');
		return $opciones;
	}

	/**
	 * (non-PHPdoc)
	 * @see clases/com/bulfon/bulfon/action/ListarAction#getFiltros()
	 */
	protected function getFiltros(){
		$filtros[]= $this->buildFiltro('ds_pais', $this->tableModel->getColumnName(1));
		return $filtros;
	}


	/**
	 * (non-PHPdoc)
	 * @see clases/com/bulfon/bulfon/action/ListarAction#parseAcciones($xtpl, $item)
	 */
	protected function parseAcciones(XTemplate $xtpl, $item){

		$this->parseAccionesDefault( $xtpl, $item, $item->getCd_pais(), 'pais', 'pais' );
	}

	/**
	 * (non-PHPdoc)
	 * @see clases/com/bulfon/bulfon/action/ListarAction#getEntidadManager()
	 */
	protected function getEntidadManager(){
		return new PaisManager();
	}

	/**
	 * (non-PHPdoc)
	 * @see clases/com/bulfon/bulfon/action/ListarAction#getCampoOrdenDefault()
	 */
	protected function getCampoOrdenDefault(){
		return 'ds_pais';
	}

	/**
	 * (non-PHPdoc)
	 * @see clases/com/bulfon/bulfon/action/ListarAction#getMsjError1()
	 */
	protected function getMsjError1(){
		return 'No se pudo eliminar el Pais. Verifique que no existan datos relacionados';
	}

	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/action/generic/SecureOutputAction#getTitulo()
	 */
	protected function getTitulo(){
		return 'Administración de Paises';
	}

	/**
	 * (non-PHPdoc)
	 * @see clases/com/bulfon/bulfon/action/ListarAction#getCartelEliminar($entidad)
	 */
	protected function getCartelEliminar($entidad){
		$xtpl = new XTemplate ( CDT_GEO_TEMPLATE_BAJA_PAIS );
		$xtpl->assign ( 'ds_pais', stripslashes( $entidad->getDs_pais() ) );
		$xtpl->parse('main');
		return FormatUtils::quitarEnters( $xtpl->text('main') );
	}

	/**
	 * (non-PHPdoc)
	 * @see clases/com/bulfon/bulfon/action/ListarAction#getUrlAccionListar()
	 */
	protected function getUrlAccionListar(){
		return 'listar_paises';
	}

	/**
	 * (non-PHPdoc)
	 * @see clases/com/bulfon/bulfon/action/ListarAction#getUrlAccionExportarPdf()
	 */
	protected function getUrlAccionExportarPdf(){
		return 'pdf_paises';
	}

	/**
	 * (non-PHPdoc)
	 * @see clases/com/bulfon/bulfon/action/ListarAction#getForwardError()
	 */
	protected function getForwardError(){
		return 'listar_paises_error';
	}

	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/action/generic/SecureOutputAction#getMenuActivo()
	 */
	protected function getMenuActivo(){
		return "Paises";
	}



}