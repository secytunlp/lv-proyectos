<?php

/**
 * PDF de EvaluaciÃƒÂ³n
 * 
 * @author Marcos
 * @since 29/05/2014
 */
class ViewEvaluacionPDF extends CdtPDFPrint{
	
	private $maxWidth = "";	

	private $evaluacion_oid = "";
	private $solicitud_oid = "";
	
	private $periodo_oid = "";
	
	private $estado_oid = "";
	private $facultadplanilla_oid = "";
	private $year = "";
	private $ds_investigador = "";
	
	
	private $ds_facultadplanilla = "";	
		
	private $observacion = "";
		
	private $ds_evaluador = "";	
	
	private $modeloPlanilla;	
		
	  
	public function __construct(){
		
		parent::__construct();
		
	}
	
/**
	 * (non-PHPdoc)
	 * @see CdtPDFPrint#Header()
	 */
	function Header(){
		
		$this->SetTextColor(100, 100, 100);
		/*$this->SetDrawColor(1,1,1);
		$this->SetLineWidth(.1);*/
		$this->SetFont('Arial','B',36);
		if ($this->getEstado_oid()!=CYT_ESTADO_SOLICITUD_EVALUADA) {
			$this->RotatedText($this->lMargin, $this->h - 10, $this->encodeCharacters('      '.CYT_MSG_EVALUACION_PDF_PRELIMINAR_TEXT.'       '.CYT_MSG_EVALUACION_PDF_PRELIMINAR_TEXT), 60);
		}
			
		
		$this->SetY(3);
		
		$this->SetTextColor(0, 0, 0);
		$this->Image(APP_PATH . 'css/images/unlp.png',12,12,80,15);
		//$this->Image('../img/unlp.png',12,12,80,15);
	
		$this->SetFont ( 'Arial', '', 11 );
		
		$this->ln(7);
		$this->Cell ( $this->getMaxWidth()-4, 10, $this->encodeCharacters(CYT_MSG_SOLICITUD_PDF_HEADER_TITLE)." ".$this->getYear(), '',0,'R');
		$this->ln(4);
		
		$this->Cell ( $this->getMaxWidth()-4, 10, $this->encodeCharacters(CYT_MSG_SOLICITUD_PDF_HEADER_TITLE_2), '',0,'R');
		$this->ln(4);
		
		$this->SetFont ( 'Arial', 'B', 11 );
		$this->Cell ( $this->getMaxWidth()-4, 10, $this->encodeCharacters(CYT_MSG_EVALUACION_PDF_HEADER_TITLE)." ".$this->getYear(), '',0,'R');
		$this->SetFont ( 'Arial', '', 15 );
		
		$this->SetFont ( 'Arial', '', 11 );
		
		
		$this->ln(13);
		$this->NyAp();
		$this->ln(10);
		
		//Line break
		//$this->Ln(15);
	}
	
	
	function NyAp() {
		$this->SetFillColor(255,255,255);
		$this->SetFont ( 'Arial', '', 10 );
		$this->Cell ( $this->getMaxWidth()-95, 8, CYT_LBL_DOCENTE_APELLIDO_NOMBRE.": ".$this->encodeCharacters($this->getDs_investigador()), 'LTBR',0,'L',1);
		
		$this->Cell ( $this->getMaxWidth()-99, 8, CYT_LBL_DOCENTE_FACULTAD.": ".$this->encodeCharacters($this->getDs_facultadplanilla()), 'LTBR',0,'L',1);
		
	}
	
	function printEvaluacion(  ){
		$total = 0;
		$this->separadorNegro(CYT_MSG_EVALUACION_SEPARADOR_NEGRO_1_1,CYT_MSG_EVALUACION_SEPARADOR_NEGRO_1_2,CYT_MSG_EVALUACION_SEPARADOR_NEGRO_1_3);
				
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_modeloplanilla', $this->getModeloPlanilla()->getOid(), '=');
		$oCriteria->addOrder('nu_max','DESC');
		$managerPosgradoMaximo =  ManagerFactory::getPosgradoMaximoManager();
		$oPosgradosMaximos = $managerPosgradoMaximo->getEntities($oCriteria);
		$totalA = 0;
		
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $this->getEvaluacion_oid(), '=');
		$managerPuntajePosgrado =  ManagerFactory::getPuntajePosgradoManager();
		$oPuntajePosgrado = $managerPuntajePosgrado->getEntity($oCriteria);
		
		
		$nu_puntaje='0,00';
		$nu_puntaje1=0;
		$ds_posgrado = array();
		
		if ($oPuntajePosgrado) {
			foreach ($oPosgradosMaximos as $oPosgradoMaximo) {
				IF ($oPuntajePosgrado->getPosgradoMaximo()->getOid()==$oPosgradoMaximo->getOid()){
					$nu_puntaje=number_format($oPosgradoMaximo->getNu_max(), CYT_DECIMALES , CYT_SEPARADOR_DECIMAL, CYT_SEPARADOR_MILES);
					$nu_puntaje1=$oPosgradoMaximo->getNu_max();
					//$seleccionado=1;
				}
			
				
				//$ds_posgrado[] =array('descripcion'=>' '.$oPosgradoMaximo->getPosgradoPlanilla()->getDs_posgradoplanilla().' ('.$oPosgradoMaximo->getNu_max().CYT_MSG_EVALUACION_PT.')','seleccionado'=>$seleccionado);
			
			}	
		}
		
		$this->SetFillColor(225,225,225);
		$this->SetFont ( 'Arial', '', 10 );
		$this->cell ( $this->getMaxWidth()-150, 6, 'A', 'LTR',0,'C',1);
		$this->cell ( $this->getMaxWidth()-64, 6, $this->encodeCharacters(CYT_MSG_EVALUACION_ANTEDENTES_ACADEMICOS), 'LT',0,'C',1);
		$this->Cell ( $this->getMaxWidth()-170, 6, '', 'TR',0,'C',1);
		$this->ln(5);
		$this->cell ( $this->getMaxWidth()-150, 6, '', 'BLR',0,'C',1);
		$this->SetFont ( 'Arial', '', 8 );
		$descripcion = $this->encodeCharacters(CYT_LBL_SOLICITUD_TITULO_POSGRADO).': '.$this->encodeCharacters(CYT_LBL_EVALUACION_HASTA.' '.$oPosgradosMaximos->getObjectByIndex(0)->getNu_max().' '.CYT_LBL_EVALUACION_PUNTOS).' '.$this->encodeCharacters(CYT_LBL_EVALUACION_POSGRADO_COMMENT);
		
		//$this->WriteHTML($descripcion);
		$this->cell ( $this->getMaxWidth()-44, 6, $descripcion, 'LBR',0,'L',1);
		$this->ln(6);
		for($i = 0; $i < $oPosgradosMaximos->size(); $i ++) {
			for($j = 0; $j < 2; $j ++) {	
				if ($oPuntajePosgrado) {
					IF ($oPuntajePosgrado->getPosgradoMaximo()->getOid()==$oPosgradosMaximos->getObjectByIndex($i+$j)->getOid()){
						//$nu_puntaje=FuncionesComunes::Format_toDecimal($cargos[$i+$j]['nu_max']);
						$seleccionado=1;
					}
					else $seleccionado=0;
				}
				$ds_posgrado[] =array('descripcion'=>$oPosgradosMaximos->getObjectByIndex($i+$j)->getPosgradoPlanilla()->getDs_posgradoplanilla().' ('.$oPosgradosMaximos->getObjectByIndex($i+$j)->getNu_max().CYT_MSG_EVALUACION_PT.')','seleccionado'=>$seleccionado);
			}
			$i++;
			switch ( $i) {
				case '1' :
					$posgrado = "A1";
					$negrita='';
					$bordes1='LTR';
					
					$punt=$nu_puntaje;
				break;
				case '3' :
					$posgrado = CYT_MSG_EVALUACION_MAX.' '.$oPosgradosMaximos->getObjectByIndex(0)->getNu_max().CYT_MSG_EVALUACION_PT.' ';
					$negrita='B';
					$bordes1='LBR';
					
					$punt='';
				break;
			}
			$this->posgrado($posgrado,$ds_posgrado , $punt, $negrita, $bordes1);	
			$ds_posgrado = array();
			
		}
		
		$totalA+=$nu_puntaje1;
		
		
		$this->ln(3);
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_modeloplanilla', $this->getModeloPlanilla()->getOid(), '=');
		$oCriteria->addOrder('cd_antacadmaximo');
		$managerAntacadMaximo =  ManagerFactory::getAntacadMaximoManager();
		$oAntacadsMaximos = $managerAntacadMaximo->getEntities($oCriteria);
		
		$topeA = $oAntacadsMaximos->getObjectByIndex(0)->getPuntajeGrupo()->getNu_max();
		
		$ds_descripcionA2 = $oAntacadsMaximos->getObjectByIndex(0)->getAntacadPlanilla()->getDs_antacadplanilla();
		$ds_descripcionA2 = str_replace('#puntaje#', $oAntacadsMaximos->getObjectByIndex(0)->getNu_max().' '.CYT_LBL_EVALUACION_PUNTOS,$ds_descripcionA2);
		
		$nu_max = ($oAntacadsMaximos->getObjectByIndex(0)->getNu_max()==$oAntacadsMaximos->getObjectByIndex(0)->getNu_min())?$oAntacadsMaximos->getObjectByIndex(0)->getNu_max().' '.CYT_LBL_EVALUACION_C_U:CYT_LBL_EVALUACION_HASTA.' '.$oAntacadsMaximos->getObjectByIndex(0)->getNu_max();
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $this->getEvaluacion_oid(), '=');
		$oCriteria->addFilter('cd_antacadmaximo', $oAntacadsMaximos->getObjectByIndex(0)->getOid(), '=');
		$managerPuntajeAntacad =  ManagerFactory::getPuntajeAntacadManager();
		$oPuntajeAntacadA2 = $managerPuntajeAntacad->getEntity($oCriteria);

		$nu_puntaje = ($oPuntajeAntacadA2)?$oPuntajeAntacadA2->getNu_puntaje():0;
		$totalA2 = ($oPuntajeAntacadA2)?$nu_puntaje*$oAntacadsMaximos->getObjectByIndex(0)->getNu_max():0;
		if($totalA2>$oAntacadsMaximos->getObjectByIndex(0)->getNu_tope()){
			$totalA2 = $oAntacadsMaximos->getObjectByIndex(0)->getNu_tope();
		}
		
		
		$this->produccion('A2', $ds_descripcionA2, $nu_max, $nu_puntaje, number_format($totalA2, CYT_DECIMALES , CYT_SEPARADOR_DECIMAL, CYT_SEPARADOR_MILES), CYT_MSG_EVALUACION_MAX.' '.$oAntacadsMaximos->getObjectByIndex(0)->getNu_tope().' '.CYT_LBL_EVALUACION_PT,'LTR','LBR');
		
		$totalA+=$totalA2;
		
		$ds_descripcionA3 = $oAntacadsMaximos->getObjectByIndex(1)->getAntacadPlanilla()->getDs_antacadplanilla();
		$ds_descripcionA3 = str_replace('#puntaje#', $oAntacadsMaximos->getObjectByIndex(1)->getNu_max().' '.CYT_LBL_EVALUACION_PUNTOS,$ds_descripcionA3);
		
		$nu_max = ($oAntacadsMaximos->getObjectByIndex(1)->getNu_max()==$oAntacadsMaximos->getObjectByIndex(1)->getNu_min())?$oAntacadsMaximos->getObjectByIndex(1)->getNu_max().' '.CYT_LBL_EVALUACION_C_U:CYT_LBL_EVALUACION_HASTA.' '.$oAntacadsMaximos->getObjectByIndex(1)->getNu_max();
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $this->getEvaluacion_oid(), '=');
		$oCriteria->addFilter('cd_antacadmaximo', $oAntacadsMaximos->getObjectByIndex(1)->getOid(), '=');
		$managerPuntajeAntacad =  ManagerFactory::getPuntajeAntacadManager();
		$oPuntajeAntacadA3 = $managerPuntajeAntacad->getEntity($oCriteria);

		$nu_puntaje = ($oPuntajeAntacadA3)?$oPuntajeAntacadA3->getNu_puntaje():0;
		$totalA3 = $nu_puntaje*$oAntacadsMaximos->getObjectByIndex(1)->getNu_max();
		if($totalA3>$oAntacadsMaximos->getObjectByIndex(1)->getNu_tope()){
			$totalA3 = $oAntacadsMaximos->getObjectByIndex(1)->getNu_tope();
		}
		
		
		$this->produccion('A3', $ds_descripcionA3, $nu_max, $nu_puntaje, number_format($totalA3, CYT_DECIMALES , CYT_SEPARADOR_DECIMAL, CYT_SEPARADOR_MILES), CYT_MSG_EVALUACION_MAX.' '.$oAntacadsMaximos->getObjectByIndex(1)->getNu_tope().' '.CYT_LBL_EVALUACION_PT,'LTR','LBR');
		
		$totalA+=$totalA3;
		
		$xpri=$this->GetX();
       	$ypri=$this->GetY();
		$this->SetFillColor(225,225,225);
		$this->SetFont ( 'Arial', '', 10 );
		$this->cell ( $this->getMaxWidth()-150, 6, '', 'LTR',0,'C',1);
		$x=$this->GetX();
       	$y=$this->GetY();
		$this->MultiCell ($this->getMaxWidth()-44, 4, $this->encodeCharacters(CYT_MSG_EVALUACION_FACTOR_DESCRIPCION_1), 'LTR','L',1);
		$y1=$this->GetY();
		$this->SetXY($xpri,$ypri);
		$this->cell ( $this->getMaxWidth()-150, $y1-$y, '', 'LTR',0,'C',1);
		$this->ln(12);
		
		$this->cell ( $this->getMaxWidth()-150, 4, '', 'LR',0,'C',1);
		$this->cell ( $this->getMaxWidth()-44, 4, $this->encodeCharacters(CYT_MSG_EVALUACION_FACTOR_DESCRIPCION_2), 'LR',0,'L',1);
		//$this->MultiCell ($this->getMaxWidth()-44, 4, $this->encodeCharacters(CYT_LBL_EVALUACION_FACTOR_DESCRIPCION_2), 'LR','L',1);
		
		$this->ln(4);
		$xpri=$this->GetX();
       	$ypri=$this->GetY();
		$this->SetFillColor(225,225,225);
		$this->SetFont ( 'Arial', '', 10 );
		$this->cell ( $this->getMaxWidth()-150, 6, 'A4', 'LR',0,'C',1);
		$x=$this->GetX();
       	$y=$this->GetY();
		$this->MultiCell ($this->getMaxWidth()-44, 4, $this->encodeCharacters(CYT_MSG_EVALUACION_FACTOR_DESCRIPCION_3), 'LBR','L',1);
		$y1=$this->GetY();
		$this->SetXY($xpri,$ypri);
		$this->cell ( $this->getMaxWidth()-150, $y1-$y, 'A4', 'LR',0,'C',1);
		$this->ln(12);
		
		$ds_descripcionA4 = $oAntacadsMaximos->getObjectByIndex(2)->getAntacadPlanilla()->getDs_antacadplanilla();
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $this->getEvaluacion_oid(), '=');
		$oCriteria->addFilter('cd_antacadmaximo', $oAntacadsMaximos->getObjectByIndex(2)->getOid(), '=');
		$managerPuntajeAntacad =  ManagerFactory::getPuntajeAntacadManager();
		$oPuntajeAntacadA4 = $managerPuntajeAntacad->getEntity($oCriteria);
				
		if (($oPuntajeAntacadA4)&&($oPuntajeAntacadA4->getNu_puntaje()==2)) {
			
			$posgrado_seleccionado = 'Si';
			$nu_factor = 1;
		}
		else{
			$posgrado_seleccionado = 'No';
			$nu_puntaje = ($oPuntajeAntacadA4)? $oPuntajeAntacadA4->getNu_puntaje():0;
			$nu_factor = $nu_puntaje;
	 		/*$hoy = date(CYT_FECHA_CIERRE);
	 		$oSolicitudManager = ManagerFactory::getSolicitudManager();
			$oSolicitud = $oSolicitudManager->getObjectByCode($this->getSolicitud_oid());
 			$yeargrado =(CYTSecureUtils::dias($hoy, $oSolicitud->getDt_egresogrado())/360);
 			if($yeargrado <6 ){
				$nu_factor = 1;
			}
			else if(($yeargrado >= 6)&&($yeargrado < 7)){
				$nu_factor = 0.9;
			}
			else if(($yeargrado >= 7)&&($yeargrado < 8)){
				$nu_factor = 0.8;
			}
			else if(($yeargrado >= 8)&&($yeargrado < 9)){
				$nu_factor = 0.7;
			}
			else if(($yeargrado >= 9)&&($yeargrado < 10)){
				$nu_factor = 0.6;
			}
			else if($yeargrado >= 10){
				$nu_factor = 0.5;
			}*/
			
		}
	 		
		
		$totalA=$totalA*$nu_factor;
		$ds_descripcionA4 = str_replace('<br>', ' ', $ds_descripcionA4);
		$this->produccion('', $ds_descripcionA4, CYT_MSG_EVALUACION_POSGRADO_PDF.': '.$posgrado_seleccionado, 'F: '.$nu_factor, number_format($totalA, CYT_DECIMALES , CYT_SEPARADOR_DECIMAL, CYT_SEPARADOR_MILES), '','LR','LBR');
		
		
		$ds_descripcionA5 = $oAntacadsMaximos->getObjectByIndex(3)->getAntacadPlanilla()->getDs_antacadplanilla();
		
		
		$nu_max = (($oAntacadsMaximos->getObjectByIndex(3)->getNu_max()!=0)&&($oAntacadsMaximos->getObjectByIndex(3)->getNu_min()==$oAntacadsMaximos->getObjectByIndex(3)->getNu_max()))?((($oAntacadsMaximos->getObjectByIndex(3)->getNu_min()==$oAntacadsMaximos->getObjectByIndex(3)->getNu_tope())&&($oAntacadsMaximos->getObjectByIndex(3)->getNu_min()==$oAntacadsMaximos->getObjectByIndex(3)->getNu_max()))?$oAntacadsMaximos->getObjectByIndex(3)->getNu_max().' '.CYT_LBL_EVALUACION_PT:$oAntacadsMaximos->getObjectByIndex(3)->getNu_max(). ' '.CYT_LBL_EVALUACION_C_U):CYT_LBL_EVALUACION_HASTA.' '.$oAntacadsMaximos->getObjectByIndex(3)->getNu_tope();
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $this->getEvaluacion_oid(), '=');
		$oCriteria->addFilter('cd_antacadmaximo', $oAntacadsMaximos->getObjectByIndex(3)->getOid(), '=');
		$managerPuntajeAntacad =  ManagerFactory::getPuntajeAntacadManager();
		$oPuntajeAntacadA5 = $managerPuntajeAntacad->getEntity($oCriteria);
				
		$totalA5 = ($oPuntajeAntacadA5)?$oPuntajeAntacadA5->getNu_puntaje():0;
		
		
		
		$this->produccion('A5', $ds_descripcionA5, $nu_max, $totalA5, number_format($totalA5, CYT_DECIMALES , CYT_SEPARADOR_DECIMAL, CYT_SEPARADOR_MILES), '','LTR','LBR');
		
		$totalA += $totalA5;
		
		$ds_descripcionA6 = $oAntacadsMaximos->getObjectByIndex(4)->getAntacadPlanilla()->getDs_antacadplanilla();
		$ds_descripcionA6 = str_replace('#puntaje#', $oAntacadsMaximos->getObjectByIndex(4)->getNu_max().' '.CYT_LBL_EVALUACION_PUNTOS,$ds_descripcionA6);
		
		$nu_max = (($oAntacadsMaximos->getObjectByIndex(4)->getNu_max()!=0)&&($oAntacadsMaximos->getObjectByIndex(4)->getNu_min()==$oAntacadsMaximos->getObjectByIndex(4)->getNu_max()))?((($oAntacadsMaximos->getObjectByIndex(4)->getNu_min()==$oAntacadsMaximos->getObjectByIndex(4)->getNu_tope())&&($oAntacadsMaximos->getObjectByIndex(4)->getNu_min()==$oAntacadsMaximos->getObjectByIndex(4)->getNu_max()))?$oAntacadsMaximos->getObjectByIndex(4)->getNu_max().' '.CYT_LBL_EVALUACION_PT:$oAntacadsMaximos->getObjectByIndex(4)->getNu_max(). ' '.CYT_LBL_EVALUACION_C_U):CYT_LBL_EVALUACION_HASTA.' '.$oAntacadsMaximos->getObjectByIndex(4)->getNu_tope();
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $this->getEvaluacion_oid(), '=');
		$oCriteria->addFilter('cd_antacadmaximo', $oAntacadsMaximos->getObjectByIndex(4)->getOid(), '=');
		$managerPuntajeAntacad =  ManagerFactory::getPuntajeAntacadManager();
		$oPuntajeAntacadA6 = $managerPuntajeAntacad->getEntity($oCriteria);

		$nu_puntaje = ($oPuntajeAntacadA6)?$oPuntajeAntacadA6->getNu_puntaje():0;
		$totalA6 = $nu_puntaje;
		if($totalA6>$oAntacadsMaximos->getObjectByIndex(4)->getNu_tope()){
			$totalA6 = $oAntacadsMaximos->getObjectByIndex(4)->getNu_tope();
		}
		
		
		$this->produccion('A6', $ds_descripcionA6, $nu_max, $nu_puntaje, number_format($totalA6, CYT_DECIMALES , CYT_SEPARADOR_DECIMAL, CYT_SEPARADOR_MILES), CYT_MSG_EVALUACION_MAX.' '.$oAntacadsMaximos->getObjectByIndex(4)->getNu_tope().' '.CYT_LBL_EVALUACION_PT,'LTR','LBR');
		
		$totalA+=$totalA6;
		
		if($totalA>$topeA){
			$totalA = $topeA;
		}
		
		$this->subtotal('',CYT_MSG_EVALUACION_SUBTOTAL.' ('.CYT_MSG_EVALUACION_MAX.' '.$topeA.')', number_format($totalA, CYT_DECIMALES , CYT_SEPARADOR_DECIMAL, CYT_SEPARADOR_MILES), '','LBT');
		
		$total +=$totalA;

		//CDTUtils::log('total: '.$total);
		
		$this->ln(3);
		
		$this->SetFillColor(225,225,225);
		$this->SetFont ( 'Arial', '', 10 );
		$this->cell ( $this->getMaxWidth()-150, 6, 'B', 'LTR',0,'C',1);
		$this->cell ( $this->getMaxWidth()-64, 6, $this->encodeCharacters(CYT_MSG_EVALUACION_ANTEDENTES_DOCENTES), 'LT',0,'C',1);
		$this->Cell ( $this->getMaxWidth()-170, 6, '', 'TR',0,'C',1);
		$this->ln(6);
		/*$this->cell ( $this->getMaxWidth()-150, 6, '', 'BLR',0,'C',1);
		$this->SetFont ( 'Arial', '', 8 );
		$this->cell ( $this->getMaxWidth()-44, 6, $this->encodeCharacters(CYT_MSG_EVALUACION_ANTEDENTES_DOCENTES_DESCRIPCION), 'LBR',0,'L',1);
		$this->ln(6);*/
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_modeloplanilla', $this->getModeloPlanilla()->getOid(), '=');
		$oCargoMaximoManager =  ManagerFactory::getCargoMaximoManager();
		$cargos = $oCargoMaximoManager->getEntities($oCriteria);
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $this->getEvaluacion_oid(), '=');
		$oPuntajeCargoManager =  ManagerFactory::getPuntajeCargoManager();
		$oPuntajecargo = $oPuntajeCargoManager->getEntity($oCriteria);
		
		
		 $ds_cargo = array();
		 $nu_puntaje='0,00';
		 $nu_puntaje1=0;
		 if ($oPuntajecargo) {
			 foreach ($cargos as $oCargoMaximo) {
			 	IF ($oPuntajecargo->getCargomaximo()->getOid()==$oCargoMaximo->getOid()){
						$nu_puntaje=number_format($oCargoMaximo->getNu_max(), CYT_DECIMALES , CYT_SEPARADOR_DECIMAL, CYT_SEPARADOR_MILES);
						$nu_puntaje1=$oCargoMaximo->getNu_max();
			 	}
			 }
		 }
		 
		for($i = 0; $i < $cargos->size(); $i ++) {
			
			for($j = 0; $j < 2; $j ++) {	
				if ($oPuntajecargo) {
					IF ($oPuntajecargo->getCargomaximo()->getOid()==$cargos->getObjectByIndex($i+$j)->getOid()){
						//$nu_puntaje=FuncionesComunes::Format_toDecimal($cargos[$i+$j]['nu_max']);
						$seleccionado=1;
					}
					else $seleccionado=0;
				}
				$ds_cargo[] =array('descripcion'=>$cargos->getObjectByIndex($i+$j)->getCargoPlanilla()->getDs_cargoplanilla().' ('.$cargos->getObjectByIndex($i+$j)->getNu_max().CYT_MSG_EVALUACION_PT.')','seleccionado'=>$seleccionado);
			}
			$i++;
			switch ( $i) {
				case '1' :
					$cargo = "";
					$negrita='';
					$bordes1='LTR';
					
					$punt='';
				break;
				case '3' :
					$cargo = 'B1';
					$negrita='';
					$bordes1='LR';
					
					$punt='';
				break;
				case '5' :
					$cargo = CYT_MSG_EVALUACION_MAX.' '.$cargos->getObjectByIndex(0)->getNu_max().CYT_MSG_EVALUACION_PT;
					$negrita='B';
					$bordes='LR';
					$punt=$nu_puntaje;
				break;
				case '7' :
					$cargo = '';
					$negrita='';
					$bordes='LR';
					$punt='';
				break;
				case '9' :
					$cargo = "";
					
					$negrita='';
					$bordes1='LBR';
					
					$punt='';
				break;
				default :
					$cargo = "";
					$negrita='';
					$bordes1='LR';
					
					$punt='';
				break;
			}
			$this->cargo($cargo,$ds_cargo , $punt, $negrita, $bordes1);	
			$ds_cargo = array();
			
		}	
		$total+=$nu_puntaje1;
		//CDTUtils::log('total: '.$total);
		
		$this->Ln(3);
		$this->SetFillColor(225,225,225);
		$this->SetFont ( 'Arial', '', 10 );
		$this->cell ( $this->getMaxWidth()-150, 6, 'C', 'LBTR',0,'C',1);
		$this->cell ( $this->getMaxWidth()-64, 6, $this->encodeCharacters(CYT_MSG_EVALUACION_OTROS_ANTEDENTES), 'LBT',0,'C',1);
		$this->Cell ( $this->getMaxWidth()-170, 6, '', 'TBR',0,'C',1);
		
		$this->ln(6);
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_modeloplanilla', $this->getModeloPlanilla()->getOid(), '=');
		$oCriteria->addOrder('cd_antotrosmaximo');
		$managerAntotrosMaximo =  ManagerFactory::getAntotrosMaximoManager();
		$oAntotrossMaximos = $managerAntotrosMaximo->getEntities($oCriteria);
		
		
		$submax=0;
		$max=0;
		$antotrosSub=0;
		$j=0;
		$subpuntaje=0;
		$nu_punt=0;
		$ds_subgrupo='';
		$itemSub=0;
		$totalC=0;
		for($i = 0; $i < $oAntotrossMaximos->size(); $i ++) {	
			
			
			 if (($antotrosSub!=$oAntotrossMaximos->getObjectByIndex($i)->getAntotrosPlanilla()->getSubGrupo()->getOid() )&&($oAntotrossMaximos->getObjectByIndex($i)->getAntotrosPlanilla()->getSubGrupo()->getOid()) ){
			 	$j++;
				$nu_punt=0;
				$ds_subgrupo=$oAntotrossMaximos->getObjectByIndex($i)->getAntotrosPlanilla()->getSubGrupo()->getDs_pdf();
				$antotrosSub=$oAntotrossMaximos->getObjectByIndex($i)->getAntotrosPlanilla()->getSubGrupo()->getOid();
				$imprimir=0;
				
				if ($i!=0){
					$subpuntaje += ($oPuntajeAntotros)?$nu_puntaje:0;
					if($oAntotrossMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max()!=0){
						$itemSub++;
						
						
						
						$subpuntaje = ($subpuntaje>$oAntotrossMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max())?$oAntotrossMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max():$subpuntaje;
						//$total+=$subpuntaje;
						$this->subtotal($ds_texto1,CYT_MSG_EVALUACION_SUBTOTAL.' ('.CYT_MSG_EVALUACION_MAX.' '.$oAntotrossMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max().')', number_format($subpuntaje, CYT_DECIMALES , CYT_SEPARADOR_DECIMAL, CYT_SEPARADOR_MILES), '','LR');	
						
					}
					$totalC +=$subpuntaje;
					CDTUtils::log('totalC: '.$totalC);
					CDTUtils::log('subpuntaje: '.$subpuntaje);
					$subpuntaje=0;	
				}
				$letra = 'C'.($j);
			}
			//elseif (!$oAntotrossMaximos->getObjectByIndex($i)->getAntotrosPlanilla()->getSubGrupo()->getOid()){
			else{
				if($i==0){
					$j++;
					$letra = 'C'.($j);
				}
				else{
					$letra = '';
				}
				$nu_puntant=$nu_punt;
				//$j++;
				$ds_antotrosplanilla=$oAntotrossMaximos->getObjectByIndex($i)->getAntotrosPlanilla()->getDs_antotrosplanilla();
				$imprimir=1;
				
				
			}
		
			$oCriteria = new CdtSearchCriteria();
			$oCriteria->addFilter('cd_evaluacion', $this->getEvaluacion_oid(), '=');
			$oCriteria->addFilter('cd_antotrosmaximo', $oAntotrossMaximos->getObjectByIndex($i)->getOid(), '=');
			$managerPuntajeAntotros =  ManagerFactory::getPuntajeAntotrosManager();
			$oPuntajeAntotros = $managerPuntajeAntotros->getEntity($oCriteria);
			
		 	
			if($oPuntajeAntotros->getNu_puntaje()){
							
				$nu_puntaje = ($oAntotrossMaximos->getObjectByIndex($i)->getNu_tope())?(((($oAntotrossMaximos->getObjectByIndex($i)->getNu_max())&&($oPuntajeAntotros->getNu_puntaje()*$oAntotrossMaximos->getObjectByIndex($i)->getNu_max()>$oAntotrossMaximos->getObjectByIndex($i)->getNu_tope()))||((!$oAntotrossMaximos->getObjectByIndex($i)->getNu_max())&&($oPuntajeAntotros->getNu_puntaje()>$oAntotrossMaximos->getObjectByIndex($i)->getNu_tope())))?$oAntotrossMaximos->getObjectByIndex($i)->getNu_tope():(($oAntotrossMaximos->getObjectByIndex($i)->getNu_max())?$oPuntajeAntotros->getNu_puntaje()*$oAntotrossMaximos->getObjectByIndex($i)->getNu_max():$oPuntajeAntotros->getNu_puntaje())):(($oAntotrossMaximos->getObjectByIndex($i)->getNu_max())?$oAntotrossMaximos->getObjectByIndex($i)->getNu_max()*$oPuntajeAntotros->getNu_puntaje():$oPuntajeAntotros->getNu_puntaje());
				$nu_punt += $nu_puntaje;
				
				//$nu_puntaje = FuncionesComunes::Format_toDecimal($nu_puntaje);
			}
			else $nu_puntaje = 0;
			
			
			
			
			//if ($imprimir){
				
				if ($ds_subgrupo!='') {
					$this->SetFillColor(225,225,225);
					$this->SetFont ( 'Arial', '', 10 );
					$this->cell ( $this->getMaxWidth()-150, 6, $letra, 'LBTR',0,'C',1);
					$this->cell ( $this->getMaxWidth()-64, 6, $this->encodeCharacters($ds_subgrupo), 'LBT',0,'L',1);
					$this->Cell ( $this->getMaxWidth()-170, 6, '', 'TBR',0,'C',1);
					
					$this->ln(6);
					$ds_subgrupo='';
					
					$letra='';
				}
				
				$ds_tope=($oAntotrossMaximos->getObjectByIndex($i)->getNu_tope())?CYT_MSG_EVALUACION_MAX.' '.$oAntotrossMaximos->getObjectByIndex($i)->getNu_tope().' '.CYT_LBL_EVALUACION_PT:'';
				$nu_max = (($oAntotrossMaximos->getObjectByIndex($i)->getNu_max()!=0)&&($oAntotrossMaximos->getObjectByIndex($i)->getNu_min()==$oAntotrossMaximos->getObjectByIndex($i)->getNu_max()))?((($oAntotrossMaximos->getObjectByIndex($i)->getNu_min()==$oAntotrossMaximos->getObjectByIndex($i)->getNu_tope())&&($oAntotrossMaximos->getObjectByIndex($i)->getNu_min()==$oAntotrossMaximos->getObjectByIndex($i)->getNu_max()))?$oAntotrossMaximos->getObjectByIndex($i)->getNu_max().' '.CYT_LBL_EVALUACION_PT:$oAntotrossMaximos->getObjectByIndex($i)->getNu_max(). ' '.CYT_LBL_EVALUACION_C_U):CYT_LBL_EVALUACION_HASTA.' '.$oAntotrossMaximos->getObjectByIndex($i)->getNu_tope();
				
				
				$this->produccion($letra, $oAntotrossMaximos->getObjectByIndex($i)->getAntotrosPlanilla()->getDs_antotrosplanilla(), $nu_max, $oPuntajeAntotros->getNu_puntaje(), number_format($nu_puntaje, CYT_DECIMALES , CYT_SEPARADOR_DECIMAL, CYT_SEPARADOR_MILES), $ds_tope,'LTR','LBR');
				$subpuntaje += $nu_puntaje;
				//$oPDF_Evaluacion->item('C'.($j).'.- '.$ds_antotrosplanilla, FuncionesComunes::Format_toDecimal($nu_puntaje));
			//}
			
			$subpuntaje = ($subpuntaje>$oAntotrossMaximos->getObjectByIndex($i)->getPuntajeGrupo()->getNu_max())?$oAntotrossMaximos->getObjectByIndex($i)->getPuntajeGrupo()->getNu_max():$subpuntaje;
			//CDTUtils::log('subpuntaje: '.$subpuntaje);
			//$totalC += $subpuntaje;
			
			
			
		}	
	    
	    //CDTUtils::log('totalC: '.$totalC);
	    
	    
	   $subpuntaje = ($subpuntaje>$oAntotrossMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max())?$oAntotrossMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max():$subpuntaje;
					
		$this->subtotal($ds_texto1,CYT_MSG_EVALUACION_SUBTOTAL.' ('.CYT_MSG_EVALUACION_MAX.' '.$oAntotrossMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max().')', number_format($subpuntaje, CYT_DECIMALES , CYT_SEPARADOR_DECIMAL, CYT_SEPARADOR_MILES), '','LR');	

		$totalC +=$subpuntaje;
		
		$this->subtotal('',CYT_MSG_EVALUACION_SUBTOTAL.' C ('.CYT_MSG_EVALUACION_MAX.' 5)', number_format($totalC, CYT_DECIMALES , CYT_SEPARADOR_DECIMAL, CYT_SEPARADOR_MILES), '','LBT');
		/*CDTUtils::log('totalC: '.$totalC);
		CDTUtils::log('subpuntaje: '.$subpuntaje);*/
	    $subpuntaje=0;
		$total +=$totalC;
		
		
				
		$this->AddPage();
		
		$this->SetFillColor(225,225,225);
		$this->SetFont ( 'Arial', '', 10 );

		if($this->getYear()<2019){
			$this->cell ( $this->getMaxWidth()-150, 6, 'D', 'LBTR',0,'C',1);
			$this->cell ( $this->getMaxWidth()-54, 6, $this->encodeCharacters(CYT_MSG_EVALUACION_PRODUCCION_CIENTIFICA), 'LBT',0,'L',1);
			$this->Cell ( $this->getMaxWidth()-180, 6, '', 'TBR',0,'C',1);
		
			$this->ln(6);
		}
		else{
			
			$xpri=$this->GetX();
			$ypri=$this->GetY();
			$this->cell ( $this->getMaxWidth()-150, 6, 'D', 'LBTR',0,'C',1);
			$y=$this->GetY();
			$this->MultiCell ( $this->getMaxWidth()-44, 4, $this->encodeCharacters(CYT_MSG_EVALUACION_PRODUCCION_CIENTIFICA). ' ('.(intval($this->getYear())-4).', '.(intval($this->getYear())-3).', '.(intval($this->getYear())-2).', '.(intval($this->getYear())-1).', '.(intval($this->getYear())).')', 'LTBR','L',1);
			$y1=$this->GetY();
			
			$this->SetXY($xpri,$ypri);
			$this->cell ( $this->getMaxWidth()-150, $y1-$y, 'D', 'LBTR',0,'C',1);
			$this->ln($y1-$y);

		
		}
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_modeloplanilla', $this->getModeloPlanilla()->getOid(), '=');
		$oCriteria->addOrder('cd_antproduccionmaximo');
		$managerAntproduccionMaximo =  ManagerFactory::getAntproduccionMaximoManager();
		$oAntproduccionsMaximos = $managerAntproduccionMaximo->getEntities($oCriteria);
		$totalD = 0;
		//CDTUtils::log('totalD: '.$totalD);
		$j=1;
		$i=0;
		while ($oAntproduccionsMaximos->getObjectByIndex($i)){

			if ($i!=0){
				if($oAntproduccionsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max()!=0){
					$itemSub++;
					
					//$subpuntaje += ($oPuntajeAntproduccion)?$oPuntajeAntproduccion->getNu_puntaje():0;
					
					$subpuntaje = ($subpuntaje>$oAntproduccionsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max())?$oAntproduccionsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max():$subpuntaje;
					//$totalD+=$subpuntaje;
					$this->subtotal($ds_texto1,CYT_MSG_EVALUACION_SUBTOTAL.' ('.CYT_MSG_EVALUACION_MAX.' '.$oAntproduccionsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max().')', number_format($subpuntaje, CYT_DECIMALES , CYT_SEPARADOR_DECIMAL, CYT_SEPARADOR_MILES), '','LR');	
					
				}
				$totalD+=$subpuntaje;
				$subpuntaje=0;	
			}

			$tieneLetra = '';
			if ($oAntproduccionsMaximos->getObjectByIndex($i)->getAntproduccionPlanilla()->getSubGrupo()->getOid()!=0) {
				$this->SetFillColor(225,225,225);
				$this->SetFont ( 'Arial', '', 10 );
				$xpri=$this->GetX();
		       	$ypri=$this->GetY();
				$this->cell ( $this->getMaxWidth()-150, 6, 'D'.$j, 'LBTR',0,'C',1);
		       	$y=$this->GetY();
				$this->MultiCell ( $this->getMaxWidth()-44, 4, $this->encodeCharacters($oAntproduccionsMaximos->getObjectByIndex($i)->getAntproduccionPlanilla()->getSubGrupo()->getDs_subgrupo()), 'LTBR','L',1);
				$y1=$this->GetY();
				
				$this->SetXY($xpri,$ypri);
				$this->cell ( $this->getMaxWidth()-150, $y1-$y, 'D'.$j, 'LBTR',0,'C',1);
				$this->ln($y1-$y);
			}
			else 
				$tieneLetra = 'D'.$j;
			
			
			
			$topeD = $oAntproduccionsMaximos->getObjectByIndex($i)->getPuntajeGrupo()->getNu_max();
			$cd_grupo = $oAntproduccionsMaximos->getObjectByIndex($i)->getAntproduccionPlanilla()->getSubGrupo()->getOid();
			while (($oAntproduccionsMaximos->getObjectByIndex($i))&&($cd_grupo == $oAntproduccionsMaximos->getObjectByIndex($i)->getAntproduccionPlanilla()->getSubGrupo()->getOid())) {
				$ds_descripcion = $oAntproduccionsMaximos->getObjectByIndex($i)->getAntproduccionPlanilla()->getDs_antproduccionplanilla();
				$ds_descripcion = str_replace('#puntaje#', $oAntproduccionsMaximos->getObjectByIndex($i)->getNu_max().' '.CYT_LBL_EVALUACION_PUNTOS,$ds_descripcion);
				$hs = (($i==0)&&($this->getPeriodo_oid()<=CYT_SOLICITUD_PERIODO_2013))?CYT_LBL_EVALUACION_C_10:CYT_LBL_EVALUACION_C_U;
				$nu_max = (($oAntproduccionsMaximos->getObjectByIndex($i)->getNu_max()!=0)&&($oAntproduccionsMaximos->getObjectByIndex($i)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex($i)->getNu_max()))?((($oAntproduccionsMaximos->getObjectByIndex($i)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex($i)->getNu_tope())&&($oAntproduccionsMaximos->getObjectByIndex($i)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex($i)->getNu_max()))?$oAntproduccionsMaximos->getObjectByIndex($i)->getNu_max().' '.CYT_LBL_EVALUACION_PT:$oAntproduccionsMaximos->getObjectByIndex($i)->getNu_max(). ' '.$hs):(($oAntproduccionsMaximos->getObjectByIndex($i)->getNu_min()!=$oAntproduccionsMaximos->getObjectByIndex($i)->getNu_max())?CYT_LBL_EVALUACION_HASTA.' '.$oAntproduccionsMaximos->getObjectByIndex($i)->getNu_max(). ' '.CYT_LBL_EVALUACION_C_U:CYT_LBL_EVALUACION_HASTA.' '.$oAntproduccionsMaximos->getObjectByIndex($i)->getNu_tope());
				$oCriteria = new CdtSearchCriteria();
				$oCriteria->addFilter('cd_evaluacion', $this->getEvaluacion_oid(), '=');
				$oCriteria->addFilter('cd_antproduccionmaximo', $oAntproduccionsMaximos->getObjectByIndex($i)->getOid(), '=');
				$managerPuntajeAntproduccion =  ManagerFactory::getPuntajeAntproduccionManager();
				$oPuntajeAntproduccion = $managerPuntajeAntproduccion->getEntity($oCriteria);
		
				$nu_puntaje =($oPuntajeAntproduccion)?$oPuntajeAntproduccion->getNu_puntaje():0;
				
				$tot = ($oAntproduccionsMaximos->getObjectByIndex($i)->getNu_max())?$nu_puntaje*$oAntproduccionsMaximos->getObjectByIndex($i)->getNu_max():$nu_puntaje;
				if(($oAntproduccionsMaximos->getObjectByIndex($i)->getNu_tope())&&($tot>$oAntproduccionsMaximos->getObjectByIndex($i)->getNu_tope())){
					$tot = $oAntproduccionsMaximos->getObjectByIndex($i)->getNu_tope();
				}
				
				$ds_tope = ($oAntproduccionsMaximos->getObjectByIndex($i)->getNu_tope()&&($oAntproduccionsMaximos->getObjectByIndex($i)->getNu_tope()!=$oAntproduccionsMaximos->getObjectByIndex($i)->getNu_max()))?CYT_MSG_EVALUACION_MAX.' '.$oAntproduccionsMaximos->getObjectByIndex($i)->getNu_tope().' '.CYT_LBL_EVALUACION_PT:'';
				
				if (($oPuntajeAntproduccion)&&($oPuntajeAntproduccion->getNu_cant())) {
					$tot = $nu_puntaje;
					$nu_puntaje= $oPuntajeAntproduccion->getNu_cant();
					 
				}
				//CDTUtils::log($ds_descripcion);
				$this->produccion($tieneLetra, $ds_descripcion, $nu_max, $nu_puntaje, number_format($tot, CYT_DECIMALES , CYT_SEPARADOR_DECIMAL, CYT_SEPARADOR_MILES), $ds_tope,'LTR','LBR');
				
				if ($cd_grupo==0) {
					$cd_grupo=1;//para que salga del while
				}
				$subpuntaje+=$tot;
				
				/*CDTUtils::log('subpuntaje: '.$subpuntaje);
				CDTUtils::log('totalD: '.$totalD);*/
				$i++;
			}
			$j++;
		}
		
		if ($i!=0 && $oAntproduccionsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max()!=0){
			$itemSub++;
			
			//$subpuntaje += ($oPuntajeAntproduccion)?$oPuntajeAntproduccion->getNu_puntaje():0;
			
			$subpuntaje = ($totalD>$oAntproduccionsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max())?$oAntproduccionsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max():$subpuntaje;
			//$totalD+=$subpuntaje;
			$this->subtotal($ds_texto1,CYT_MSG_EVALUACION_SUBTOTAL.' ('.CYT_MSG_EVALUACION_MAX.' '.$oAntproduccionsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max().')', number_format($subpuntaje, CYT_DECIMALES , CYT_SEPARADOR_DECIMAL, CYT_SEPARADOR_MILES), '','LR');	
			$subpuntaje=0;	
		}
		$totalD+=$subpuntaje;
		/*CDTUtils::log('subpuntaje: '.$subpuntaje);
		CDTUtils::log('totalD: '.$totalD);*/
		if($oAntproduccionsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getCd_grupopadre()){
			$oPuntajeGrupoManager =  ManagerFactory::getPuntajeGrupoManager();
			$oPuntajeGrupo = $oPuntajeGrupoManager->getObjectByCode($oAntproduccionsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getCd_grupopadre());

			if($totalD>$oPuntajeGrupo->getNu_max()){
				$totalD = $oPuntajeGrupo->getNu_max();
			}
			
			$this->subtotal('',CYT_MSG_EVALUACION_SUBTOTAL.' ('.CYT_MSG_EVALUACION_MAX.' '.$oPuntajeGrupo->getNu_max().')', number_format($totalD, CYT_DECIMALES , CYT_SEPARADOR_DECIMAL, CYT_SEPARADOR_MILES), '','LBT');
				
		}

		
	
		$subpuntaje=0;
		
		
		$total +=$totalD;
				
		$this->AddPage();

		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_modeloplanilla', $this->getModeloPlanilla()->getOid(), '=');
		$oCriteria->addOrder('cd_subanteriormaximo');
		$managerSubanteriorMaximo =  ManagerFactory::getSubanteriorMaximoManager();
		$oSubanteriorsMaximos = $managerSubanteriorMaximo->getEntities($oCriteria);
		$totalE = 0;
		
		
		$topeE = $oSubanteriorsMaximos->getObjectByIndex(0)->getPuntajeGrupo()->getNu_max();
		
		$ds_descripcionE1_1 = $oSubanteriorsMaximos->getObjectByIndex(0)->getSubanteriorPlanilla()->getDs_subanteriorplanilla();
		$ds_descripcionE1_1 = str_replace('#puntaje#', $oSubanteriorsMaximos->getObjectByIndex(0)->getNu_max().' '.CYT_LBL_EVALUACION_PUNTOS,$ds_descripcionE1_1);
		
		$nu_max = (($oSubanteriorsMaximos->getObjectByIndex(0)->getNu_max()!=0)&&($oSubanteriorsMaximos->getObjectByIndex(0)->getNu_min()==$oSubanteriorsMaximos->getObjectByIndex(0)->getNu_max()))?((($oSubanteriorsMaximos->getObjectByIndex(0)->getNu_min()==$oSubanteriorsMaximos->getObjectByIndex(0)->getNu_tope())&&($oSubanteriorsMaximos->getObjectByIndex(0)->getNu_min()==$oSubanteriorsMaximos->getObjectByIndex(0)->getNu_max()))?$oSubanteriorsMaximos->getObjectByIndex(0)->getNu_max().' '.CYT_LBL_EVALUACION_PT:$oSubanteriorsMaximos->getObjectByIndex(0)->getNu_max(). ' '.CYT_LBL_EVALUACION_C_10):CYT_LBL_EVALUACION_HASTA.' '.$oSubanteriorsMaximos->getObjectByIndex(0)->getNu_tope();
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $this->getEvaluacion_oid(), '=');
		$oCriteria->addFilter('cd_subanteriormaximo', $oSubanteriorsMaximos->getObjectByIndex(0)->getOid(), '=');
		$managerPuntajeSubanterior =  ManagerFactory::getPuntajeSubanteriorManager();
		$oPuntajeSubanteriorE1_1 = $managerPuntajeSubanterior->getEntity($oCriteria);
				
		$totalE1_1 = ($oPuntajeSubanteriorE1_1)?$oPuntajeSubanteriorE1_1->getNu_puntaje():0;
		if($totalE1_1>$oSubanteriorsMaximos->getObjectByIndex(0)->getNu_tope()){
			$totalE1_1 = $oSubanteriorsMaximos->getObjectByIndex(0)->getNu_tope();
		}
		
		
		$this->produccion('E1', $ds_descripcionE1_1, $nu_max, $totalE1_1, number_format($totalE1_1, CYT_DECIMALES , CYT_SEPARADOR_DECIMAL, CYT_SEPARADOR_MILES), CYT_MSG_EVALUACION_MAX.' '.$oSubanteriorsMaximos->getObjectByIndex(0)->getNu_tope().' '.CYT_LBL_EVALUACION_PT,'LTR','LBR');
		
		$totalE+=$totalE1_1;
		
		/*$this->subtotal('',CYT_MSG_EVALUACION_SUBTOTAL.' ('.CYT_MSG_EVALUACION_MAX.' '.$topeE.')', number_format($totalE, CYT_DECIMALES , CYT_SEPARADOR_DECIMAL, CYT_SEPARADOR_MILES), '','LBT');*/
		
		$total +=$totalE;
		
		$this->SetFillColor(225,225,225);
		$this->SetFont ( 'Arial', '', 10 );
		$this->cell ( $this->getMaxWidth()-150, 6, 'F', 'LBTR',0,'C',1);
		$this->cell ( $this->getMaxWidth()-64, 6, $this->encodeCharacters(CYT_MSG_EVALUACION_JUSTIFICACION), 'LBT',0,'C',1);
		$this->Cell ( $this->getMaxWidth()-170, 6, '', 'TBR',0,'C',1);
		
		$this->ln(6);
		
		
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_modeloplanilla', $this->getModeloPlanilla()->getOid(), '=');
		$oCriteria->addOrder('cd_antjustificacionmaximo');
		$managerAntjustificacionMaximo =  ManagerFactory::getAntjustificacionMaximoManager();
		$oAntjustificacionsMaximos = $managerAntjustificacionMaximo->getEntities($oCriteria);
		$totalE = 0;
		
		
		$topeE = $oAntjustificacionsMaximos->getObjectByIndex(0)->getPuntajeGrupo()->getNu_max();
		
		$ds_descripcionE1_1 = $oAntjustificacionsMaximos->getObjectByIndex(0)->getAntjustificacionPlanilla()->getDs_antjustificacionplanilla();
		$ds_descripcionE1_1 = str_replace('#puntaje#', $oAntjustificacionsMaximos->getObjectByIndex(0)->getNu_max().' '.CYT_LBL_EVALUACION_PUNTOS,$ds_descripcionE1_1);
		
		$nu_max = (($oAntjustificacionsMaximos->getObjectByIndex(0)->getNu_max()!=0)&&($oAntjustificacionsMaximos->getObjectByIndex(0)->getNu_min()==$oAntjustificacionsMaximos->getObjectByIndex(0)->getNu_max()))?((($oAntjustificacionsMaximos->getObjectByIndex(0)->getNu_min()==$oAntjustificacionsMaximos->getObjectByIndex(0)->getNu_tope())&&($oAntjustificacionsMaximos->getObjectByIndex(0)->getNu_min()==$oAntjustificacionsMaximos->getObjectByIndex(0)->getNu_max()))?$oAntjustificacionsMaximos->getObjectByIndex(0)->getNu_max().' '.CYT_LBL_EVALUACION_PT:$oAntjustificacionsMaximos->getObjectByIndex(0)->getNu_max(). ' '.CYT_LBL_EVALUACION_C_10):CYT_LBL_EVALUACION_HASTA.' '.$oAntjustificacionsMaximos->getObjectByIndex(0)->getNu_tope();
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $this->getEvaluacion_oid(), '=');
		$oCriteria->addFilter('cd_antjustificacionmaximo', $oAntjustificacionsMaximos->getObjectByIndex(0)->getOid(), '=');
		$managerPuntajeAntjustificacion =  ManagerFactory::getPuntajeAntjustificacionManager();
		$oPuntajeAntjustificacionE1_1 = $managerPuntajeAntjustificacion->getEntity($oCriteria);
				
		$totalE1_1 = ($oPuntajeAntjustificacionE1_1)?$oPuntajeAntjustificacionE1_1->getNu_puntaje():0;
		if($totalE1_1>$oAntjustificacionsMaximos->getObjectByIndex(0)->getNu_tope()){
			$totalE1_1 = $oAntjustificacionsMaximos->getObjectByIndex(0)->getNu_tope();
		}
		
		
		$this->produccion('F1', $ds_descripcionE1_1, $nu_max, $totalE1_1, number_format($totalE1_1, CYT_DECIMALES , CYT_SEPARADOR_DECIMAL, CYT_SEPARADOR_MILES), CYT_MSG_EVALUACION_MAX.' '.$oAntjustificacionsMaximos->getObjectByIndex(0)->getNu_tope().' '.CYT_LBL_EVALUACION_PT,'LTR','LBR');
		
		$totalE+=$totalE1_1;
		
		/*$this->subtotal('',CYT_MSG_EVALUACION_SUBTOTAL.' ('.CYT_MSG_EVALUACION_MAX.' '.$topeE.')', number_format($totalE, CYT_DECIMALES , CYT_SEPARADOR_DECIMAL, CYT_SEPARADOR_MILES), '','LBT');*/
		
		$total +=$totalE;
		
		$this->ln(3);
		
		$this->total(CYT_MSG_EVALUACION_TOTAL.' ('.CYT_MSG_EVALUACION_MAX.' '.$this->getModeloplanilla()->getNu_max().')', number_format($total, CYT_DECIMALES , CYT_SEPARADOR_DECIMAL, CYT_SEPARADOR_MILES), '','LBR');	
		
		$this->Ln(3);
		$this->separadorNegro(CYT_MSG_EVALUACION_OBSERVACIONES,'','');
		$this->SetFont ( 'Arial', '', 8 );
		$this->MultiCell($this->getMaxWidth()-4,4,$this->encodeCharacters($this->getObservacion()),'LTBR');
		$this->firma2();	
	}
	
	function separadorNegro($ds_texto1, $ds_texto2, $ds_texto3) {
		
		$this->SetTextColor(255,255,255);
		$this->SetFillColor(0,0,0);
		$this->SetFont ( 'Arial', '', 10 );
		$this->Cell ( $this->getMaxWidth()-150, 6, $ds_texto1,0,0,'C',1);
		$this->Cell ( $this->getMaxWidth()-84, 6, $ds_texto2,0,0,'C',1);
		$this->Cell ( $this->getMaxWidth()-150, 6, $ds_texto3,0,0,'C',1);
		$this->ln(6);
		$this->SetTextColor(0,0,0);
	}
	
	function separarcant() {
		$this->SetFillColor(255,255,255);
		
		
		$this->cell ( $this->getMaxWidth()-34, 2, "", 'TBR',0,'L',1);
		
		$this->SetFont ( 'Arial', '', 8 );
		$this->cell ( $this->getMaxWidth()-180, 2, CYT_MSG_EVALUACION_CANT, 'LTBR',0,'R',1);
		
		
		$this->Cell ( $this->getMaxWidth()-170, 2, '', 'LTBR',0,'C',1);
		$this->ln();
	}
	
	function subtotal($ds_texto1, $ds_texto2, $nu_puntaje, $negrita, $bordes1) {
		$this->SetFillColor(225,225,225);
		
		$this->SetFont ( 'Arial', $negrita, 10 );
		$this->cell ( $this->getMaxWidth()-150, 6, $ds_texto1, $bordes1,0,'C',1);
		
		$this->SetFont ( 'Arial', '', 10 );
		$this->cell ( $this->getMaxWidth()-64, 6, $ds_texto2, 'TBR',0,'R',1);
		
		$this->SetFont ( 'Arial', 'B', 10 );
		$this->Cell ( $this->getMaxWidth()-170, 6, $nu_puntaje, 'LTBR',0,'C',1);
		$this->ln(5);
	}
	
	function total($ds_texto1, $nu_puntaje) {
		$this->SetFillColor(255,255,255);
		
		$this->SetFont ( 'Arial', 'B',  12 );
		$this->cell ( $this->getMaxWidth()-24, 6, $ds_texto1, 'LTBR',0,'R',1);
		
		
		
		$this->SetFillColor(225,225,225);
		$this->Cell ( $this->getMaxWidth()-170, 6, $nu_puntaje, 'LTBR',0,'C',1);
		$this->ln(5);
	}
		
	
	
	
	function posgrado($ds_texto1, $posgrados, $nu_puntaje, $negrita, $bordes1) {
		$this->SetFillColor(225,225,225);
		
		$this->SetFont ( 'Arial', $negrita, 10 );
		$this->cell ( $this->getMaxWidth()-150, 6, $ds_texto1, $bordes1,0,'C',1);
		$this->SetFillColor(255,255,255);
		$this->SetFont ( 'Arial', '', 10 );
		foreach ($posgrados as $posgrado){
			$hor=intval($this->GetX()+1);
 			$ver=intval($this->GetY()+1);
 			$nombre_imagen=($posgrado['seleccionado']==1)?APP_PATH . 'css/images/si.jpg':APP_PATH . 'css/images/no.jpg';
			$this->Image($nombre_imagen,$hor,$ver,5);
			$bordes2 = str_replace('R','',$bordes1);
			$this->Cell ( $this->getMaxWidth()-183, 6, "", $bordes2,0,'L');
			$bordes2 = str_replace('L','',$bordes1);
			$this->Cell ( $this->getMaxWidth()-134, 6, $this->encodeCharacters($posgrado['descripcion']), $bordes2,0,'L');
		}
		$this->SetFillColor(225,225,225);
		$this->SetFont ( 'Arial', 'B', 10 );
		$this->Cell ( $this->getMaxWidth()-170, 6, $nu_puntaje, $bordes1,0,'C',1);
		$this->ln(5);
	}
	
	function cargo($ds_texto1, $cargos, $nu_puntaje, $negrita, $bordes1) {
		$this->SetFillColor(225,225,225);
		
		$this->SetFont ( 'Arial', $negrita, 10 );
		$this->cell ( $this->getMaxWidth()-150, 6, $ds_texto1, $bordes1,0,'C',1);
		$this->SetFillColor(255,255,255);
		$this->SetFont ( 'Arial', '', 10 );
		foreach ($cargos as $cargo){
			$hor=intval($this->GetX()+1);
 			$ver=intval($this->GetY()+1);
 			$nombre_imagen=($cargo['seleccionado']==1)?APP_PATH . 'css/images/si.jpg':APP_PATH . 'css/images/no.jpg';
			$this->Image($nombre_imagen,$hor,$ver,5);
			$bordes2 = str_replace('R','',$bordes1);
			$this->Cell ( $this->getMaxWidth()-183, 6, "", $bordes2,0,'L');
			$bordes2 = str_replace('L','',$bordes1);
			$this->Cell ( $this->getMaxWidth()-134, 6, $cargo['descripcion'], $bordes2,0,'L');
		}
		$this->SetFillColor(225,225,225);
		$this->SetFont ( 'Arial', 'B', 10 );
		$this->Cell ( $this->getMaxWidth()-170, 6, $nu_puntaje, $bordes1,0,'C',1);
		$this->ln(5);
	}
	
	function produccion($ds_texto1, $ds_texto2, $hasta, $nu_cant, $nu_puntaje, $ds_tope, $bordes1, $bordes2) {
		//CDTUtils::log('ds_texto1: '.$ds_texto1.' - ds_texto2: '.$ds_texto2.' - hasta:'. $hasta.' nu_cant: '.$nu_cant.' - nu_puntaje: '.$nu_puntaje.' -  ds_tope: '.$ds_tope.' -  bordes1: '.$bordes1.' -  bordes2: '.$bordes2);
		$xpri=$this->GetX();
       	$ypri=$this->GetY();
		$this->SetFillColor(225,225,225);
		
		$this->SetFont ( 'Arial', '', 10 );
		$this->cell ( $this->getMaxWidth()-150, 6, $ds_texto1, $bordes1,0,'C',1);
		$this->SetFillColor(255,255,255);
		$this->SetFont ( 'Arial', '', 9 );
		$x=$this->GetX();
       	$y=$this->GetY();
		
		while (strlen($ds_texto2)<CYT_LONGITUD_EVALUACION_LINEA) {
			$ds_texto2 .=' ';
		}
		$this->MultiCell ( $this->getMaxWidth()-92, 4, $this->encodeCharacters($ds_texto2), 'LTBR','L',1);
		$y1=$this->GetY();
		
		
		$this->SetXY($xpri,$ypri+6);
		$this->SetFillColor(225,225,225);
		
		$this->SetFont ( 'Arial', 'B', 10 );
		$this->cell ( $this->getMaxWidth()-150, $y1-$y-6, $ds_tope, $bordes2,0,'C',1);
		$this->SetFillColor(255,255,255);
		$this->SetFont ( 'Arial', '', 9 );
		$this->SetXY($x+98,$y);
		
		$this->SetFont ( 'Arial', '', 8 );
		$this->cell ( $this->getMaxWidth()-172, $y1-$y, $hasta, 'LTBR',0,'R',1);
		$this->SetFont ( 'Arial', '', 10 );
		$this->cell ( $this->getMaxWidth()-180, $y1-$y, $nu_cant, 'LTBR',0,'R',1);
		
		$this->SetFont ( 'Arial', 'B', 10 );
		$this->Cell ( $this->getMaxWidth()-170, $y1-$y, $nu_puntaje, 'LTBR',0,'C',1);
		$this->Ln(($y1-$y));
	}
	
	
	
	function firma2() {
		$this->ln(15);
		
		$this->SetFillColor(255,255,255);
		
		$this->SetFont ( 'Arial', 'B', 10 );
		$this->Cell ( $this->getMaxWidth()-180, 8);
		$this->Cell ( $this->getMaxWidth()-130, 8, '', 'B');
		$this->Cell ( $this->getMaxWidth()-160, 8);
		$this->Cell ( $this->getMaxWidth()-130, 8, $this->encodeCharacters($this->getDs_evaluador()), 'B', 0, 'C');
		$this->ln(8);
		$this->Cell ( $this->getMaxWidth()-180, 8);
		$this->Cell ( $this->getMaxWidth()-130, 8, CYT_MSG_EVALUACION_FIRMA, '', 0, 'C');
		$this->Cell ( $this->getMaxWidth()-160, 8);
		$this->Cell ( $this->getMaxWidth()-130, 8, $this->encodeCharacters(CYT_MSG_EVALUACION_ACLARACION), '', 0, 'C');
		
		
	}

	/**
	 * (non-PHPdoc)
	 * @see CdtPDFPrint#Footer()
	 */
	function Footer(){
		
		$this->SetY(-15);
		
		
		$this->SetFont('Arial','I',8);

		$this->Cell(0,10,$this->encodeCharacters(CYT_MSG_SOLICITUD_PDF_PAGINA).' '.$this->PageNo().' '.CYT_MSG_SOLICITUD_PDF_PAGINA_DE.' {nb}',0,0,'C');
		
	}

	
	
	

	public function getMaxWidth()
	{
	    return $this->maxWidth;
	}

	public function setMaxWidth($maxWidth)
	{
	    $this->maxWidth = $maxWidth;
	}

	public function getEvaluacion_oid()
	{
	    return $this->evaluacion_oid;
	}

	public function setEvaluacion_oid($evaluacion_oid)
	{
	    $this->evaluacion_oid = $evaluacion_oid;
	}

	public function getPeriodo_oid()
	{
	    return $this->periodo_oid;
	}

	public function setPeriodo_oid($periodo_oid)
	{
	    $this->periodo_oid = $periodo_oid;
	}

	public function getEstado_oid()
	{
	    return $this->estado_oid;
	}

	public function setEstado_oid($estado_oid)
	{
	    $this->estado_oid = $estado_oid;
	}

	public function getFacultadplanilla_oid()
	{
	    return $this->facultadplanilla_oid;
	}

	public function setFacultadplanilla_oid($facultadplanilla_oid)
	{
	    $this->facultadplanilla_oid = $facultadplanilla_oid;
	}

	public function getYear()
	{
	    return $this->year;
	}

	public function setYear($year)
	{
	    $this->year = $year;
	}

	public function getDs_investigador()
	{
	    return $this->ds_investigador;
	}

	public function setDs_investigador($ds_investigador)
	{
	    $this->ds_investigador = $ds_investigador;
	}

	public function getDs_facultadplanilla()
	{
	    return $this->ds_facultadplanilla;
	}

	public function setDs_facultadplanilla($ds_facultadplanilla)
	{
	    $this->ds_facultadplanilla = $ds_facultadplanilla;
	}

	public function getObservacion()
	{
	    return $this->observacion;
	}

	public function setObservacion($observacion)
	{
	    $this->observacion = $observacion;
	}

	public function getDs_evaluador()
	{
	    return $this->ds_evaluador;
	}

	public function setDs_evaluador($ds_evaluador)
	{
	    $this->ds_evaluador = $ds_evaluador;
	}

	public function getModeloPlanilla()
	{
	    return $this->modeloPlanilla;
	}

	public function setModeloPlanilla($modeloPlanilla)
	{
	    $this->modeloPlanilla = $modeloPlanilla;
	}

	public function getSolicitud_oid()
	{
	    return $this->solicitud_oid;
	}

	public function setSolicitud_oid($solicitud_oid)
	{
	    $this->solicitud_oid = $solicitud_oid;
	}
}