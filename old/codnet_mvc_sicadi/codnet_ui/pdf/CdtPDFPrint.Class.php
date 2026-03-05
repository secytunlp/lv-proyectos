<?php
require_once( CDT_EXTERNAL_LIB_PATH . 'fpdf17/fpdf.php' );

/**
 * Clase para exportar a PDF.
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 *
 */
class CdtPDFPrint extends FPDF {

	var $widths;
	var $aligns;
	
	function SetWidths($w)
	{
	    //Set the array of column widths
	    $this->widths=$w;
	}
	
	function SetAligns($a)
	{
	    //Set the array of column alignments
	    $this->aligns=$a;
	}
	
	function Row($data,$fill=0)
	{
	    //Calculate the height of the row
	    $nb=0;
	    for($i=0;$i<count($data);$i++){
	        $texto = $data[$i];
	        $image = $this->getImage( $texto );
	        if(! $image )	        
	        	
	    		$nb=max($nb, $this->NbLines($this->widths[$i], $data[$i]));
	    }
	    //FIXME tomar el maximo de las imagenes
	    $h=5*$nb;
	    //Issue a page break first if needed
	    $this->CheckPageBreak($h);
	    //Draw the cells of the row
	    for($i=0;$i<count($data);$i++)
	    {
	        $w=$this->widths[$i];
	        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
	        //Save the current position
	        $x=$this->GetX();
	        $y=$this->GetY();
	        //Draw the border
	        $this->Rect($x, $y, $w, $h);
	        //Print the text
	        $texto = $data[$i];

	        //si el texto es un tag de imagen, imprimimos la imagen.
	        $image = $this->getImage( $texto );
	        if( $image )	        
	        	$this->Image( $image );
	        else
	        	$this->MultiCell($w, 5, $texto, 0, $a,$fill);
	        
	        
	        //Put the position to the right of the cell
	        $this->SetXY($x+$w, $y);
	    }
	    //Go to the next line
	    $this->Ln($h);
	}
	
	/**
	 * se chequea si el texto es un tag html de imagen.
	 * en ese caso se retorna la url sino retorna false.
	 * @param $texto
	 */
	function getImage( $texto ){
		
				
		$buscaSrcImagenes = '/<img.*?src=["\'](.*?)["\']/s';

		if (preg_match_all($buscaSrcImagenes , $texto, $imagenes)) 
		
			return $imagenes[1][0];
			
		else
		
			return false;
		
	}
	
	function CheckPageBreak($h)
	{
	    //If the height h would cause an overflow, add a new page immediately
	    if($this->GetY()+$h>$this->PageBreakTrigger)
	        $this->AddPage($this->CurOrientation);
	}
	
	function NbLines($w, $txt)
	{
	    //Computes the number of lines a MultiCell of width w will take
	    $cw=&$this->CurrentFont['cw'];
	    if($w==0)
	        $w=$this->w-$this->rMargin-$this->x;
	    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
	    $s=str_replace("\r", '', $txt);
	    $nb=strlen($s);
	    if($nb>0 and $s[$nb-1]=="\n")
	        $nb--;
	    $sep=-1;
	    $i=0;
	    $j=0;
	    $l=0;
	    $nl=1;
	    while($i<$nb)
	    {
	        $c=$s[$i];
	        if($c=="\n")
	        {
	            $i++;
	            $sep=-1;
	            $j=$i;
	            $l=0;
	            $nl++;
	            continue;
	        }
	        if($c==' ')
	            $sep=$i;
	        $l+=$cw[$c];
	        if($l>$wmax)
	        {
	            if($sep==-1)
	            {
	                if($i==$j)
	                    $i++;
	            }
	            else
	                $i=$sep+1;
	            $sep=-1;
	            $j=$i;
	            $l=0;
	            $nl++;
	        }
	        else
	            $i++;
	    }
	    return $nl;
	}
	/**
	 * (non-PHPdoc)
	 * @see fpdf/FPDF#Header()
	 */
	function Header(){
	}

	/**
	 * (non-PHPdoc)
	 * @see fpdf/FPDF#Footer()
	 */
	function Footer(){
	}

	
	
	/**
	 * parsea la colecci�n dentro del pdf.
	 * utiliza el descriptor para obtener datos de la colecci�n.
	 * @param ItemCollection $items elementos a imprimir en el pdf
	 * @param ICdtTableModel $tableModel tableModel asociado a los items
	 * @return unknown_type
	 */
	function collectionToPDF(ItemCollection $items, ICdtTableModel $tableModel){

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
			$arrayAlign[]='L';
			
			//$this->Cell( $headerWidth , 6, $value , 'LR' , 0 , 'L' ,$fill);
			//$this->Cell( $headerWidth , 6, $value , 1 , 0 , 'L' ,$fill);
			
			
		}
		//$this->Ln();
		
		$this->SetWidths($arrayWidth);
		$this->SetAligns($arrayAlign);
		foreach ($items as $anObject) {
			
			for( $columnIndex=0 ; $columnIndex< $columnCount ; $columnIndex++ ){
				
				$value = $tableModel->getValue($anObject, $columnIndex);
				
				$arrayRow[]=$value;
				//$this->Cell( $headerWidth , 6, $value , 'LR' , 0 , 'L' ,$fill);
				//$this->Cell( $headerWidth , 6, $value , 1 , 0 , 'L' ,$fill);
				
				
			}
			$this->row($arrayRow);	
			$arrayRow = array();
		}	
		
		
		$this->Cell( $tableWidth , 0 , '' , 'T' );
	}

	function tableHeader($columnCount, ICdtTableModel $tableModel){
		//Colores, ancho de l�nea y fuente en negrita		
		$this->SetFillColor(218,218,218);
		$this->SetTextColor(1,77,137);
		$this->SetDrawColor(192,192,192);
		$this->SetLineWidth(.1);
		
		//Cabecera
		for( $columnIndex=0 ; $columnIndex< $columnCount ; $columnIndex++ ){
			$headerDesc = $this->encodeCharacters($tableModel->getColumnLabel($columnIndex));
			$headerWidth = $tableModel->getColumnWidth($columnIndex);
			$this->Cell( $headerWidth , 7 , $headerDesc , 1 , 0 , 'C' , 1 );
		}
	}

	/**
	 * retoran el ancho total de la tabla.
	 * @param ICollectionDescriptor $descriptor
	 * @param unknown_type $columnCount
	 * @return unknown_type
	 */
	protected function getTableWidth(ICdtTableModel $tableModel, $columnCount){
		$width=0;
		for( $columnIndex=0 ; $columnIndex< $columnCount ; $columnIndex++ ){
			$width += $tableModel->getColumnWidth($columnIndex);
		}
		return $width;	
	} 

	function Rotate($angle, $x=-1, $y=-1){
	    if($x==-1)
	        $x=$this->x;
	    if($y==-1)
	        $y=$this->y;
	    if($this->angle!=0)
	        $this->_out('Q');
	    $this->angle=$angle;
	    if($angle!=0){
	        $angle*=M_PI/180;
	        $c=cos($angle);
	        $s=sin($angle);
	        $cx=$x*$this->k;
	        $cy=($this->h-$y)*$this->k;
	        $this->_out(sprintf('q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm', $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy));
	    }
	}

	function RotatedText($x, $y, $txt, $angle){
	    //Text rotated around its origin
	    $this->Rotate($angle, $x, $y);
	    $this->Text($x, $y, $txt);
	    $this->Rotate(0);
	}

	function RotatedCell($x, $y, $txt, $angle, $w, $h, $border=1, $align="C"){
	    //Text rotated around its origin
	    $this->Rotate($angle, $x, $y);
	    $currentX = $this->x;
	    $currentY = $this->y;
	    $this->SetX( $x );
	    $this->SetY( $y );
	    $this->Cell($w,$h, $txt,$border,0,$align);
	    $this->SetX( $currentX );
	    $this->SetY( $currentY );
	    $this->Rotate(0);
	}
	
	function LabelValue($w,$h,$label, $value, $border=0, $align="L"){
		$this->SetFont('Arial','B',8);
		   
	    $this->Cell($w,$h, $this->encodeCharacters($label),$border,0,$align);
	   	$this->SetFont('Arial','',8);
		$this->Cell($w,$h, $this->encodeCharacters($value),$border,0,$align);
	    $this->Ln();
	}
	
	function LabelMulticell($w,$h,$label, $value, $border=0, $align="L"){
		$this->SetFont('Arial','B',8);
		   
	    $this->Cell($w,$h, $this->encodeCharacters($label),$border,0,$align);
	   	$this->SetFont('Arial','',8);
		$this->MultiCell($w,$h, $this->encodeCharacters($value),$border,$align);
	    //$this->Ln();
	}
	

	function encodeCharacters($value){
		return CdtUIUtils::encodeCharactersPDF($value);
	}
}
