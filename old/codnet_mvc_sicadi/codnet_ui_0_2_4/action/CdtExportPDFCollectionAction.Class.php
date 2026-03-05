<?php

/**
 * Acción para exportar a pdf una colección ItemCollection.
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 20-04-2010
 *
 */
abstract class CdtExportPDFCollectionAction extends CdtListAction{

	
	/**
	 * orientación de la hoja.
	 *    P or Portrait
	 *    L or Landscape
	 * @return string
	 */
	protected function getOrientation(){
		return "L";
	}
	
	/**
	 * retorna el tamaño default  de la fuente del pdf
	 * @return int
	 */
	protected function getFontSize(){
		return 10;
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

	/**
	 * (non-PHPdoc)
	 * @see CdtListAction::getActionList();
	 */
	protected  function getActionList(){
		return "";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtAction::execute()
	 */
	public function execute(){

		//recuperamos la entidades y su tableModel asociado.
		$entities = $this->getEntityManager()->getEntities ( $this->getCdtSearchCriteria() );
		$tableModel = $this->getEntitiesTableModel( $entities );

		//armamos el pdf.
		$pdf = $this->getPDFReport();
		$pdf->title = $this->getPDFTitle( $tableModel );
		$pdf->setTableModel( $tableModel );
		$pdf->SetFont('Arial','', $this->getFontSize());
		$pdf->AddPage();
		$pdf->collectionToPDF( $entities, $tableModel );
		$this->getPdfFooter($pdf, $this->getCdtSearchCriteria() );
		$pdf->Output();

		//para que no haga el forward.
		$forward = null;

		return $forward;
	}

	protected function getPdfFooter(  $pdf, CdtSearchCriteria $oCriteria ){
		"";
	}

	protected function getPDFReport(){
		return new CdtPDFReport( $this->getOrientation() );
	}

	protected function getPDFTitle( $tableModel ){
		return $tableModel->getTitle();
	}
	
	protected function getOutputTitle(){
		return $this->getPDFTitle( $this->tableModel );
	}
}