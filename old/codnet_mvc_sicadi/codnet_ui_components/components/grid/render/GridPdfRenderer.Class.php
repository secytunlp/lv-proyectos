<?php

/**
 * Encargado de renderizar la grilla en pdf 
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 23-12-2011
 *
 */
class GridPdfRenderer extends CdtPDFReport implements IGridRenderer {

	/**
	 * @var CMPGrid
	 */
	private $oGrid;
	
    public function render(CMPGrid $oGrid) {
    	$this->oGrid = $oGrid;
        $model = $oGrid->getGridModel();
        $this->renderResults($model, $oGrid);
    }

    protected function getCdtSearchCriteria(CMPGrid $oGrid, GridModel $oGridModel) {

        $oCriteria = $oGrid->getCriteria();
        $oCriteria->setPage(null);
        $oCriteria->setRowPerPage(null);
        return $oCriteria;
    }

    public function renderResults(IGridModel $model, CMPGrid $oGrid) {
        $tableModel = new GridTableModelWrapper($model);

        /*
        //armamos el pdf.
        $pdf = $this->getPDFReport();
        $pdf->title = $this->getPDFTitle($tableModel);
        $pdf->setTableModel($tableModel);
        $pdf->SetFont('Arial', '', $this->getFontSize());
        $pdf->AddPage();
        //$this->getCdtSearchCriteria($oGrid, $model)
        //recuperamos la entidades y su tableModel asociado.
        $entities = $model->getEntityManager()->getEntities ( $this->getCdtSearchCriteria($oGrid, $model) );

        $pdf->collectionToPDF($entities, $tableModel);
        //$pdf->Output( APP_PATH . "/tmp/" . $this->getPDFFile($tableModel) . ".pdf");
        $pdf->Output( $this->getPDFFile($tableModel) . ".pdf", "D");
        */
        
        //armamos el pdf.
        $this->title = $this->getPDFTitle($tableModel);
        $this->setTableModel($tableModel);
        $this->SetFont('Arial', '', $this->getFontSize());
        $this->AddPage("L");
        //$this->getCdtSearchCriteria($oGrid, $model)
        //recuperamos la entidades y su tableModel asociado.
        $entities = $model->getEntityManager()->getEntities ( $this->getCdtSearchCriteria($oGrid, $model) );

        $this->gridToPDF($entities, $tableModel, $model);
        //$pdf->Output( APP_PATH . "/tmp/" . $this->getPDFFile($tableModel) . ".pdf");
        //header ('Content-type: application/pdf; charset=utf-8');
        $this->Output( $this->getPDFFile($tableModel) . ".pdf", "D");
        
    }

    /*
    protected function getPDFReport() {
        return new CdtPDFReport($this->getOrientation());
    }
    */

    /**
     * orientaci�n de la hoja.
     *    P or Portrait
     *    L or Landscape
     * @return string
     */
    protected function getOrientation() {
        return "L";
    }

    /**
     * retorna el tama�o default  de la fuente del pdf
     * @return int
     */
    protected function getFontSize() {
        return 10;
    }

    protected function getPDFTitle($tableModel) {
        return $tableModel->getTitle();
    }
    
	protected function getPDFFile($tableModel) {
        return $tableModel->getExportTitle();
    }
    
	/**
	 * parsea la colecci�n dentro del pdf.
	 * utiliza el descriptor para obtener datos de la colecci�n.
	 * @param ItemCollection $items elementos a imprimir en el pdf
	 * @param ICdtTableModel $tableModel tableModel asociado a los items
	 * @return unknown_type
	 */
	function gridToPDF(ItemCollection $items, ICdtTableModel $tableModel, IGridModel $model){

		//obtenmos la cantidad de columnas a mostrar
		$columnCount = $tableModel->getColumnCount( $items );
		
		//obtenmos el ancho de la tabla.
		$tableWidth = $this->getTableWidth( $tableModel, $columnCount );
		
		$this->SetDrawColor(192,192,192);
		$this->SetLineWidth(.1);
				
		
		//Restauraci�n de colores y fuentes
		$this->SetFillColor(224,235,255);
		$this->SetTextColor(0);
		$this->SetFont('');
		
		//Datos
		$fill=false;
		$arrayWidth = array();
		$arrayAlign = array();
		$arrayRow = array();
				
		for( $columnIndex=0 ; $columnIndex< $columnCount ; $columnIndex++ ){
			$headerWidth = $tableModel->getColumnWidth($columnIndex);
			
			$arrayWidth[]=$headerWidth;
			
			$textAlign = $model->getColumnModel($columnIndex)->getTextAlign();
			if(!empty($textAlign)){
	        
	        	switch ($textAlign) {
	        		case CDT_CMP_GRID_TEXTALIGN_CENTER: $align = "C";
	        		;break;
	        		case CDT_CMP_GRID_TEXTALIGN_LEFT: $align = "L";
	        		;break;
	        		case CDT_CMP_GRID_TEXTALIGN_RIGHT: $align = "R";
	        		;break;
	        		default:$align = "C";
	        			;
	        		break;
	        	}
	        
	        }
			$arrayAlign[]=$align;
			
			//$this->Cell( $headerWidth , 6, $value , 'LR' , 0 , 'L' ,$fill);
			//$this->Cell( $headerWidth , 6, $value , 1 , 0 , 'L' ,$fill);
			
			
		}
		//$this->Ln();
		
		$this->SetWidths($arrayWidth);
		$this->SetAligns($arrayAlign);
		
		
		$this->renderRows( $items, $tableModel, $model, $columnCount );
				
		$this->Cell( $tableWidth , 0 , '' , 'T' );
	}
	
	
	public function renderRows($items, $tableModel, $model, $columnCount){
	
	
		foreach ($items as $anObject) {
			
			$this->renderRow($anObject, $tableModel, $model, $columnCount);
			
		}	
		
	}
	
	public function renderRow($anObject,$tableModel, $model, $columnCount){
	
		$arrayRow = array();
		for( $columnIndex=0 ; $columnIndex< $columnCount ; $columnIndex++ ){
				
				$value = $tableModel->getValue($anObject, $columnIndex);
				
				$arrayRow[]= $this->encodeCharacters($value);
		}
		$this->row($arrayRow);		
	}
	
}