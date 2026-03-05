<?php 
require('fpdf.php');
//require_once('lib/fpdf.inc.php');
require_once('color.inc.php');
require_once('htmlparser.inc.php');
//if (!defined('PARAGRAPH_STRING')) define('PARAGRAPH_STRING', '~~~');

function hex2dec($couleur = "#000000"){
    $R = substr($couleur, 1, 2);
    $rouge = hexdec($R);
    $V = substr($couleur, 3, 2);
    $vert = hexdec($V);
    $B = substr($couleur, 5, 2);
    $bleu = hexdec($B);
    $tbl_couleur = array();
    $tbl_couleur['R']=$rouge;
    $tbl_couleur['G']=$vert;
    $tbl_couleur['B']=$bleu;
    return $tbl_couleur;
}

//conversion pixel -> millimeter in 72 dpi
function px2mm($px){
    return $px*25.4/72;
}

function txtentities($html){
    $trans = get_html_translation_table(HTML_ENTITIES);
    $trans = array_flip($trans);
    return strtr($html, $trans);
}
define('FHR',0.58);
define('PDFTABLE_VERSION','1.9');
$PDF_ALIGN  = array('left'=>'L','center'=>'C','right'=>'R','justify'=>'J');
$PDF_VALIGN = array('top'=>'T','middle'=>'M','bottom'=>'B');
class fpdfhtmlHelper extends FPDF {
	var $B;
	var $I;
	var $U;
	var $HREF;
	var $fontList;
	var $issetfont;
	var $issetcolor;
	var $left;			//Toa do le trai cua trang
	var $right;			//Toa do le phai cua trang
	var $top;			//Toa do le tren cua trang
	var $bottom;		//Toa do le duoi cua trang
	var $width;			//Width of writable zone of page
	var $height;		//Height of writable zone of page
	var $defaultFontFamily ;
	var $defaultFontStyle;
	var $defaultFontSize;
	var $isNotYetSetFont;
	var $headerTable, $footerTable;
	var $paddingCell = 1;//(mm)
	var $paddingCell2 = 2;//2*$paddingCell
	var $spacingLine = 0;//(mm)
	var $spacingParagraph = 0;//(mm)
	
	function PDF($orientation='P', $unit='mm', $format='A4')
	{
	    //Call parent constructor
	    $this->FPDF($orientation, $unit, $format);
	    //Initialization
	    $this->B=0;
	    $this->I=0;
	    $this->U=0;
	    $this->HREF='';
	
	    $this->tableborder=0;
	    $this->tdbegin=false;
	    $this->tdwidth=0;
	    $this->tdheight=0;
	    $this->tdalign="L";
	    $this->tdbgcolor=false;
	
	    $this->oldx=0;
	    $this->oldy=0;
	
	    $this->fontlist=array("arial", "times", "courier", "helvetica", "symbol");
	    $this->issetfont=false;
	    $this->issetcolor=false;
	    $this->SetMargins(20,20,20);
		$this->SetAuthor('Pham Minh Dung');
		$this->_makePageSize();
		$this->isNotYetSetFont = true;
		$this->headerTable = $this->footerTable = '';
	}
	
function trimespecialchar($html){

		//Reemplazo de caracteres especiales
		$html=str_replace("&aacute;",'á',$html);
		$html=str_replace("&eacute;",'é',$html);
		$html=str_replace("&iacute;",'í',$html);
		$html=str_replace("&oacute;",'ó',$html);
		$html=str_replace("&uacute;",'ú',$html);
		$html=str_replace("&Aacute;",'Á',$html);
		$html=str_replace("&Eacute;",'É',$html);
		$html=str_replace("&Iacute;",'Í',$html);
		$html=str_replace("&Oacute;",'Ó',$html);
		$html=str_replace("&Uacute;",'Ú',$html);
		$html=str_replace("&ordm;",'º',$html);
		$html=str_replace("&ntilde;",'ñ',$html);
		$html=str_replace("&Ntilde;",'Ñ',$html);
		$html=str_replace("&nbsp;",' ',$html);
		$html=str_replace("&lt;",'<',$html);
		$html=str_replace("&gt;",'>',$html);
		$html=str_replace("&amp;",'&',$html);
		$html=str_replace("&quot;",'"',$html);
		$html=str_replace("&ldquo;",'"',$html);
		$html=str_replace("&rdquo;",'"',$html);
		$html=str_replace("&Agrave;",'À',$html);
		$html=str_replace("&Egrave;",'È',$html);
		$html=str_replace("&Igrave;",'Ì',$html);
		$html=str_replace("&Ograve;",'Ò',$html);
		$html=str_replace("&Ugrave;",'Ù',$html);
		$html=str_replace("&agrave;",'à',$html);
		$html=str_replace("&egrave;",'è',$html);
		$html=str_replace("&igrave;",'ì',$html);
		$html=str_replace("&ograve;",'ò',$html);
		$html=str_replace("&ugrave; ",'ù',$html);
		$html=str_replace("&Auml;",'Ä',$html);
		$html=str_replace("&Euml;",'Ë',$html);
		$html=str_replace("&Iuml;",'Ï',$html);
		$html=str_replace("&Ouml;",'Ö',$html);
		$html=str_replace("&Uuml;",'Ü',$html);
		$html=str_replace("&Acirc;",'Â',$html);
		$html=str_replace("&Ecirc;",'Ê',$html);
		$html=str_replace("&Icirc;",'Î',$html);
		$html=str_replace("&Ocirc;",'Ô',$html);
		$html=str_replace("&Ucirc;",'Û',$html);
		$html=str_replace("&acirc;",'â',$html);
		$html=str_replace("&ecirc;",'ê',$html);
		$html=str_replace("&icirc;",'î',$html);
		$html=str_replace("&ocirc;",'ô',$html);
		$html=str_replace("&ucirc;",'û',$html);
		$html=str_replace("&auml;",'ä',$html);
		$html=str_replace("&euml;",'ë',$html);
		$html=str_replace("&iuml;",'ï',$html);
		$html=str_replace("&ouml;",'ö',$html);
		$html=str_replace("&uuml;",'ü',$html);
		$html=str_replace("&Atilde;",'Ã',$html);
		$html=str_replace("&Otilde;",'Õ',$html);
		$html=str_replace("&atilde;",'ã',$html);
		$html=str_replace("&otilde;",'õ',$html);
		$html=str_replace("&aring;",'å',$html);
		$html=str_replace("&Aring;",'Å',$html);
		$html=str_replace("&Ccedil;",'Ç',$html);
		$html=str_replace("&ccedil;",'ç',$html);
		$html=str_replace("&Yacute;",'Ý',$html);
		$html=str_replace("&yacute;",'ý',$html);
		$html=str_replace("&yuml;",'ÿ',$html);
		$html=str_replace("&iquest;",'¿',$html);
		$html=str_replace("&ndash;",'-',$html);
		$html=str_replace("&iexcl;",'¡',$html);
		$html=str_replace("&middot;",'·',$html);
		$html=str_replace("&hellip;",'...',$html);
		$html=str_replace("&rsquo;","'",$html);
		$html=str_replace("&acute;","'",$html);
		$html=str_replace("&lsquo;","'",$html);
		$html=str_replace("&#39;","'",$html);
		$html=str_replace("&mu;","µ",$html);
		$html=str_replace("&micro;","µ",$html);
		$html=str_replace("&deg;","°",$html);
		$html=str_replace("&prime;","'",$html); 
		$html=str_replace("&times;","×",$html); 
		$html=str_replace("&oslash;","ø",$html); 
		$html=str_replace("&reg;","®",$html); 
		$html=str_replace("&beta;","ß",$html); 
		$html=str_replace("&alpha;","a",$html); 
		$html=str_replace("&laquo;","«",$html); 
		$html=str_replace("&mdash;","-",$html);
		$html=str_replace("&rarr;","->",$html);
		$html=str_replace("&raquo;","»",$html); 
		$html=str_replace("&ugrave;","ù",$html); 

		
		return $html;
	}

	function WriteHTML2($html)
{
    //HTML parser
    $html=str_replace("\n", ' ', $html);
    $a=preg_split('/<(.*)>/U', $html, -1, PREG_SPLIT_DELIM_CAPTURE);

    foreach($a as $i=>$e)
    {

		if($i%2==0)
        {
            //Text
            if($this->HREF)
                $this->PutLink($this->HREF, $e);
            else
                $this->Write(5, $e);
        }
        else
        {
            //Tag
            if($e[0]=='/')
                $this->CloseTag(strtoupper(substr($e, 1)));
            else
            {
                //Extract attributes
               /* $e =str_replace('style="','', $e);
                $e =str_replace(': ','=', $e);
                $e =str_replace(';"','', $e);*/
            	$attr=array();
                $heightpos = stripos($e,'height:');
                if($heightpos){
                	$heightSTR=substr($e,$heightpos+8);
                	$heightfin = strpos($heightSTR,';');
                	$cant=strlen($heightSTR)-$heightfin;
                	$attr['HEIGHT']=substr($heightSTR,0,-$cant);
                	
                }
               
                
                $widthpos = stripos($e,'width:');
                if($widthpos){
                	$widthSTR=substr($e,$widthpos+7);
                	$widthfin = strpos($widthSTR,';');
                	$cant=strlen($widthSTR)-$widthfin;
                	$attr['WIDTH']=substr($widthSTR,0,-$cant);
                }
               
                
                
              //   $e =str_replace('background-color','bgcolor', $e);
                $a2=explode(' ', $e);
                $tag=strtoupper(array_shift($a2));
              
                foreach($a2 as $v)
                    if(preg_match('/^([^=]*)=["\']?([^"\']*)["\']?$/', $v, $a3))
                        $attr[strtoupper($a3[1])]=$a3[2];
                 
                $this->OpenTag($tag, $attr);
            }
        }
    }
}

function OpenTag($tag, $attr)
{
    //Opening tag
	
    switch($tag){

        case 'SUP':
           // if($attr['SUP'] != '') {    
                //Set current font to: Bold, 6pt     
                $this->SetFont('', '', 6);
                //Start 125cm plus width of cell to the right of left margin         
                //Superscript "1"
                //$this->Cell(2, 2, $attr['SUP'], 0, 0, 'L');
            //}
            break;
		case 'LI':
                $this->Ln(5);
                //$this->SetTextColor(190, 0, 0);
                $this->Write(5, '     » ');
               // $this->mySetTextColor(-1);
                break;
        case 'TABLE': // TABLE-BEGIN
            $this->Ln(10);
        	if( $attr['BORDER'] != '' ) $this->tableborder=$attr['BORDER'];
            else $this->tableborder=0;
            break;
        case 'TR': //TR-BEGIN
            break;
        case 'TD': // TD-BEGIN
            if( $attr['WIDTH'] != '' ) $this->tdwidth=($attr['WIDTH']/4);
            else $this->tdwidth=20; // SET to your own width if you need bigger fixed cells
            if( $attr['HEIGHT'] != '') $this->tdheight=($attr['HEIGHT']/6);
            else $this->tdheight=6; // SET to your own height if you need bigger fixed cells
            if( $attr['ALIGN'] != '' ) {
                $align=$attr['ALIGN'];        
                if($align=="LEFT") $this->tdalign="L";
                if($align=="CENTER") $this->tdalign="C";
                if($align=="RIGHT") $this->tdalign="R";
            }
            else $this->tdalign="L"; // SET to your own
            if( $attr['BGCOLOR'] != '' ) {
                if (strchr($attr['BGCOLOR'],'rgb')){
                	$attr['BGCOLOR'] = str_replace('rgb(','',$attr['BGCOLOR']);
                	$attr['BGCOLOR'] = str_replace(')','',$attr['BGCOLOR']);
                	$coul = explode(',',$attr['BGCOLOR']);
                	$this->SetFillColor($coul[0], $coul[1], $coul[2]);
                }
                else{
            		$coul=hex2dec($attr['BGCOLOR']);
            		$this->SetFillColor($coul['R'], $coul['G'], $coul['B']);
                }
                    
                    $this->tdbgcolor=true;
                }
            $this->tdbegin=true;
            break;

        case 'HR':
            if( $attr['WIDTH'] != '' )
                $Width = $attr['WIDTH'];
            else
                $Width = $this->w - $this->lMargin-$this->rMargin;
            $x = $this->GetX();
            $y = $this->GetY();
            $this->SetLineWidth(0.2);
            $this->Line($x, $y, $x+$Width, $y);
            $this->SetLineWidth(0.2);
            $this->Ln(1);
            break;
        case 'STRONG':
            $this->SetStyle('B', true);
            break;
        case 'EM':
            $this->SetStyle('I', true);
            break;
        case 'B':
        case 'I':
        case 'U':
            $this->SetStyle($tag, true);
            break;
        case 'A':
            $this->HREF=$attr['HREF'];
            break;
        case 'IMG':
            if(isset($attr['SRC']) and (isset($attr['WIDTH']) or isset($attr['HEIGHT']))) {
                if(!isset($attr['WIDTH']))
                    $attr['WIDTH'] = 0;
                if(!isset($attr['HEIGHT']))
                    $attr['HEIGHT'] = 0;
                $attr['SRC']=str_replace(WEB_PATH,APP_PATH,$attr['SRC']);    
                if (is_file($attr['SRC'])) {
                	 $this->Image($attr['SRC'], $this->GetX(), $this->GetY(), px2mm($attr['WIDTH']), px2mm($attr['HEIGHT']));
                	 $this->Ln(px2mm($attr['HEIGHT']));
                }
                   
            }
            break;
        //case 'TR':
        case 'BLOCKQUOTE':
        case 'BR':
            $this->Ln(5);
            break;
        case 'P':
            $this->Ln(5);
            break;
        case 'FONT':
            if (isset($attr['COLOR']) and $attr['COLOR']!='') {
                $coul=hex2dec($attr['COLOR']);
                $this->SetTextColor($coul['R'], $coul['G'], $coul['B']);
                $this->issetcolor=true;
            }
            if (isset($attr['FACE']) and in_array(strtolower($attr['FACE']), $this->fontlist)) {
                $this->SetFont(strtolower($attr['FACE']));
                $this->issetfont=true;
            }
            if (isset($attr['FACE']) and in_array(strtolower($attr['FACE']), $this->fontlist) and isset($attr['SIZE']) and $attr['SIZE']!='') {
                $this->SetFont(strtolower($attr['FACE']), '', $attr['SIZE']);
                $this->issetfont=true;
            }
            break;
    }
}

function CloseTag($tag)
{
    //Closing tag
    if($tag=='SUP') {
		$this->SetFont ( 'times', '', 12 );
    }

    if($tag=='TD') { // TD-END
        $this->tdbegin=false;
        $this->tdwidth=0;
        $this->tdheight=0;
        $this->tdalign="L";
        $this->tdbgcolor=false;
    }
    if($tag=='TR') { // TR-END
        $this->Ln();
    }
    if($tag=='TABLE') { // TABLE-END
        //$this->Ln();
        $this->tableborder=0;
    }

    if($tag=='STRONG')
        $tag='B';
    if($tag=='EM')
        $tag='I';
    if($tag=='B' or $tag=='I' or $tag=='U')
        $this->SetStyle($tag, false);
    if($tag=='A')
        $this->HREF='';
    if($tag=='FONT'){
        if ($this->issetcolor==true) {
            $this->SetTextColor(0);
        }
        if ($this->issetfont) {
            $this->SetFont('arial');
            $this->issetfont=false;
        }
    }
}

function SetStyle($tag, $enable)
{
	//Modify style and select corresponding font
	$this->$tag+=($enable ? 1 : -1);
	$style='';
	foreach(array('B','I','U') as $s)
		if($this->$s>0)
			$style.=$s;
	$this->SetFont('',$style);
}

function PutLink($URL, $txt)
{
	//Put a hyperlink
	$this->SetTextColor(0,0,255);
	$this->SetStyle('U',true);
	$this->Write(5,$txt,$URL);
	$this->SetStyle('U',false);
	$this->SetTextColor(0);
}

// Esta función establece el color de relleno a partir de un código hexadecimal
    public function SetFillColorFromHex($hexColor)
    {
        $hexColor = ltrim($hexColor, '#');
        $r = hexdec(substr($hexColor, 0, 2));
        $g = hexdec(substr($hexColor, 2, 2));
        $b = hexdec(substr($hexColor, 4, 2));
        $this->SetFillColor($r, $g, $b);
    }

function WriteTable($data, $totalWidth)
{
    $this->SetLineWidth(.3);
    $this->SetFillColor(255, 255, 255);
    $this->SetTextColor(0);
    $this->SetFont('');

    foreach ($data as $row) {
        $nb = 0;
        $columnWidths = array(); // Arreglo para almacenar los anchos de las columnas en unidades absolutas

        foreach ($row as $cell) {
			
            // Convertir porcentaje a valor absoluto
            $widthPercentage = floatval($cell['attributes']['width']); // Porcentaje en el atributo 'width'
			
            $columnWidths[] = ($widthPercentage / 100) * $totalWidth; // Convertir a unidades absolutas
            $nb = max($nb, $this->NbLines($columnWidths[count($columnWidths) - 1], trim($cell['content'])));
        }

        $h = 5 * $nb;
        $this->CheckPageBreak($h);

        $x = $this->GetX();
        $y = $this->GetY();

        foreach ($row as $key => $cell) {
            $bgcolor = isset($cell['attributes']['bgcolor']) ? $cell['attributes']['bgcolor'] : '';
            $align = 'L';

            $this->SetFillColorFromHex($bgcolor);
            $this->Rect($x, $y, $columnWidths[$key], $h, 'F');
            $this->MultiCell($columnWidths[$key], 5, trim($cell['content']), 0, $align);
            $x += $columnWidths[$key];
            $this->SetXY($x, $y);
        }

        $this->Ln($h);
    }
}





function NbLines($w, $txt)
{
	//Computes the number of lines a MultiCell of width w will take
	$cw=$this->CurrentFont['cw'];
	if($w==0)
		$w=$this->w-$this->rMargin-$this->x;
	$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
	$s=str_replace("\r",'',$txt);
	$nb=strlen($s);
	if($nb>0 && $s[$nb-1]=="\n")
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

function CheckPageBreak($h)
{
	//If the height h would cause an overflow, add a new page immediately
	if($this->GetY()+$h>$this->PageBreakTrigger)
		$this->AddPage($this->CurOrientation);
}

function ReplaceHTML($html)
{
	$html = str_replace( '<li>', "\n<br> - " , $html );
	$html = str_replace( '<LI>', "\n - " , $html );
	$html = str_replace( '</ul>', "\n\n" , $html );
	$html = str_replace( '<strong>', "<b>" , $html );
	$html = str_replace( '</strong>', "</b>" , $html );
	$html = str_replace( '&#160;', "\n" , $html );
	$html = str_replace( '&nbsp;', " " , $html );
	$html = str_replace( '&quot;', "\"" , $html ); 
	$html = str_replace( '&#39;', "'" , $html );
	return $html;
}

function ParseTable($Table)
{
	CYTSecureUtils::logObject($Table);
    $data = array();
    $htmlText = $Table;
    $parser = new HtmlParser($htmlText);

    $currentRow = array(); // Inicializar el arreglo para la fila actual

    while ($parser->parse()) {
        $nodeName = strtolower($parser->iNodeName);

        if ($nodeName == 'table') {
            // Inicio de la tabla
        }

        if ($nodeName == 'tr') {
            if ($parser->iNodeType == NODE_TYPE_ENDELEMENT) {
                // Fin de la fila
                $data[] = $currentRow; // Agrega la fila actual a los datos
                $currentRow = array(); // Inicializa el arreglo para la nueva fila
            } else {
                // Inicio de la fila
            }
        }

        if ($nodeName == 'td') {
            if ($parser->iNodeType == NODE_TYPE_ENDELEMENT) {
                // Fin de la celda
                $cellContent = implode('', $currentCell['content']); // Contenido de la celda
                $currentRow[] = array(
                    'content' => $cellContent,
                    'attributes' => $currentCell['attributes'] // Atributos de la celda
                );
                $currentCell = array(); // Reinicia la celda actual
            } else {
                // Inicio de la celda
                $currentCell = array(
                    'content' => array(),
                    'attributes' => $parser->iNodeAttributes // Almacena los atributos de la celda
                );
            }
        }

        if ($parser->iNodeName == 'Text' && isset($parser->iNodeValue)) {
            if (!empty($currentCell)) {
                // Agrega el contenido al array de la celda actual
                $currentCell['content'][] = $parser->iNodeValue;
            }
        }
    }
CYTSecureUtils::logObject($data);
    return $data;
}






function WriteHTML($html)
{
	$html=strip_tags($html, "<b><u><ul><li><i><a><img><p><strong><em><tr><blockquote><hr><td><tr><table><sup>"); //remove all unsupported tags
    $html=str_replace("\n", '', $html); //replace carriage returns by spaces
    $html=str_replace("\t", '', $html); //replace carriage returns by spaces
	//$html = $this->ReplaceHTML($html);
	$html =str_replace('background-color','bgcolor', $html);

    
	//Search for a table
	$start = strpos(strtolower($html),'<table');
	$end = strpos(strtolower($html),'</table');
	if($start!==false && $end!==false)
	{
		$this->WriteHTML2(substr($html,0,$start).'<BR>');

		$tableVar = substr($html, $start, $end-$start).'</table>';
       
        $tableVar =str_replace('px', '', $tableVar);
		$styleini = strpos($tableVar,'style=');
        while($styleini){
        	$primero = strpos($tableVar,'"',$styleini+1);
	    	$stylefin = strpos($tableVar,'"',$primero+1);
	    	$style = substr($tableVar,$styleini,$stylefin-$styleini+2);
	    	$attrs = str_replace('style="','', $style);
	    	$attrs = str_replace(';"','', $attrs);
	    	$attrs = str_replace(': ','=', $attrs);
	    	$attrs = trim($attrs);
	    	$attrs = str_replace(';',' ', $attrs);
	    	$attrs = str_replace('width','name', $attrs);
	    	$rgbpos = strpos($attrs,'rgb(');
		    if($rgbpos){
		    	$rgbfin = strpos($attrs,')');
		    	$rgb = substr($attrs,$rgbpos,$rgbfin-$rgbpos);
		    	$rgbarray = explode(',',$rgb);
		    	$rgbtrim = trim($rgbarray[0]).','.trim($rgbarray[1]).','.trim($rgbarray[2]);
		    	$attrs =str_replace($rgb,$rgbtrim, $attrs);
		    }
	    	$tableVar =str_replace($style, $attrs, $tableVar);
	    	//$html =str_replace($rgb,trim($rgb), $html);
	    	
	    	$styleini = strpos($tableVar,'style=');
	    }
        $tableVar =str_replace('"', '', $tableVar);
		//CYTSecureUtils::logObject($tableVar);
		$tableData = $this->ParseTable($tableVar);
		for($i=1;$i<=count($tableData[0]);$i++)
		{
			if($this->CurOrientation=='L')
				$w[] = abs(120/(count($tableData[0])-1))+24;
			else
				$w[] = abs(120/(count($tableData[0])-1))+8;
		}
		
		$this->WriteTable($tableData,190);

		/*$htmlR = substr($html, $end+8, strlen($html));
        $this->WriteHTML($htmlR);*/
	}
	else
	{
		$this->WriteHTML2($html);
	}
}
}
?>