<?php

/**
 * Acción para exportar a xls una colección ItemCollection.
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 03-06-2010
 *
 */
abstract class CdtExportXlsCollectionAction extends CdtListAction{

	/**
	 * nombre del archivo xls a generar.
	 * @return  string
	 */
	protected abstract function getFileName();

	protected function parseResults( XTemplate $xtpl, CdtPaginator $oPaginator, ItemCollection $entities, CdtSearchCriteria $oCriteria, $query_string){



		$this->parsePaginator( $xtpl, $oPaginator );
			
		//header del listado.
		$this->parseHeader( $xtpl, $entities, $oCriteria );

		//encabezados (ths) de la tabla.
		$this->parseTHs( $xtpl, $query_string );

		//se parsean los elementos a mostrar
		$this->parseRows( $xtpl , $entities);

		//footer del listado.
		$this->parseFooter( $xtpl, $entities, $oCriteria );
			
	}

	/**
	 * (non-PHPdoc)
	 * @see CdtListAction::getLayout();
	 */
	protected function getLayout(){
		$oLayout = new CdtLayoutXls();
		$oLayout->setFileName( $this->getFileName() );
		return $oLayout;
	}

	/**
	 * (non-PHPdoc)
	 * @see CdtListAction::getXTemplate();
	 */
	protected function getXTemplate(){
		return new XTemplate( CDT_UI_TEMPLATE_XLS);
	}

	/**
	 * (non-PHPdoc)
	 * @see CdtListAction::getCdtSearchCriteria();
	 */
	protected function getCdtSearchCriteria(){

		//armamos el criteria
		$oCriteria = parent::getCdtSearchCriteria();

		//quitamos la paginación.
		$oCriteria->setPage( null );
		$oCriteria->setRowPerPage( null );

		return $oCriteria;
	}

	protected function getActionList(){}
}