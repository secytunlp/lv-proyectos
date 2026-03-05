<?php
/**
 * Clase para construir reportes en PDF.
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 *
 */
class CdtPDFReport extends CdtPDFPrint {

	private $lblPage = CDT_UI_PDF_LBL_PAGE;
	private $tableModel;
	
	/**
	 * (non-PHPdoc)
	 * @see CdtPDFPrint#Header()
	 */
	function Header(){
		
		//T�tulo
		$title = $this->encodeCharacters(CDT_UI_PDF_APP_NAME." / " . $this->title);
		$this->Cell($tableWidth,10, $title ,0,0,'C');
		$this->Ln();
		
		//obtenmos la cantidad de columnas a mostrar
		$columnCount = $this->tableModel->getColumnCount( $this->tableModel->getRowCount());
		
		//obtenmos el ancho de la tabla.
		$tableWidth = $this->getTableWidth( $this->tableModel, $columnCount );
		
		$this->tableHeader($columnCount, $this->tableModel);
		$this->Ln();
		
	
	}

	/**
	 * (non-PHPdoc)
	 * @see CdtPDFPrint#Footer()
	 */
	function Footer(){
		//Posici�n: a 1,5 cm del final
		$this->SetY(-15);
		//Arial italic 8
		$this->SetFont('Arial','I',8);
		//t�tulo del listado
		$this->Cell(10,10, $this->encodeCharacters(CDT_UI_PDF_APP_NAME." / " . $this->title) ,0,0,'L');
		//$this->Cell(0,10, $this->lblPage.' '.$this->PageNo() ,0,0,'C');
		//N�mero de p�gina
		$this->Cell(0,10, $this->lblPage.' '.$this->PageNo() ,0,0,'R');
	}

	
	public function setTableModel(ICdtTableModel $tableModel){
		$this->tableModel = $tableModel;
	}
}
