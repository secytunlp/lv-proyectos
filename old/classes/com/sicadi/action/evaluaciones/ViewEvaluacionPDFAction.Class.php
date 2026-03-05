<?php

/**
 * Acción para exportar a pdf una evaluación.
 *
 * @author Marcos
 * @since 29-05-2014
 *
 */
class ViewEvaluacionPDFAction extends CdtAction{

	
	/**
	 * (non-PHPdoc)
	 * @see CdtAction::execute()
	 */
	public function execute(){

		
		
		//armamos el pdf con la data necesaria.
		$pdf = new ViewEvaluacionPDF();
		
		$oUser = CdtSecureUtils::getUserLogged();
		$oCriteria = new CdtSearchCriteria();
		if ((CdtSecureUtils::hasPermission ( $oUser, CYT_FUNCTION_VER_EVALUACION )) && (!CdtSecureUtils::hasPermission ( $oUser, CYT_FUNCTION_EVALUAR_SOLICITUD ))) {
			$oCriteria->addFilter('cd_evaluacion', CdtUtils::getParam('id'), '=');
		}
		else{
			$solicitud_oid = CdtUtils::getParam('id');
		
			$oCriteria->addFilter('cd_solicitud', $solicitud_oid, '=');
			$oCriteria->addFilter('cd_usuario', $oUser->getCd_user(), '=');
		}
		
		$oCriteria->addNull('fechaHasta');
		$managerEvaluacion =  ManagerFactory::getEvaluacionManager();
		$oEvaluacion = $managerEvaluacion->getEntity($oCriteria);
		$pdf->setSolicitud_oid($oEvaluacion->getSolicitud()->getOid());
		$pdf->setEvaluacion_oid($oEvaluacion->getOid());
		
		/*$oEvaluacionManager = ManagerFactory::getEvaluacionManager();
		$oEvaluacion = $oEvaluacionManager->getObjectByCode($oEvaluacion->getOid());*/
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('evaluacion_oid', $oEvaluacion->getOid(), '=');
		$oCriteria->addNull('fechaHasta');
		$managerEvaluacionEstado =  CYTSecureManagerFactory::getEvaluacionEstadoManager();
		$oEvaluacionEstado = $managerEvaluacionEstado->getEntity($oCriteria);
		$pdf->setEstado_oid($oEvaluacionEstado->getEstado()->getOid());
		
		$oSolicitudManager = ManagerFactory::getSolicitudManager();
		$oSolicitud = $oSolicitudManager->getObjectByCode($oEvaluacion->getSolicitud()->getOid());
		
		$pdf->setPeriodo_oid($oSolicitud->getPeriodo()->getOid());
		$oPeriodoManager =  CYTSecureManagerFactory::getPeriodoManager();
		$oPeriodo = $oPeriodoManager->getObjectByCode($oSolicitud->getPeriodo()->getOid());
		$pdf->setYear($oPeriodo->getDs_periodo());
		
		$oPeriodoActual = $oPeriodoManager->getPeriodoActual(CYT_PERIODO_YEAR);
		
		//$oUser = CdtSecureUtils::getUserLogged();
		if ((!CdtSecureUtils::hasPermission ( $oUser, CYT_FUNCTION_VER_ANTERIORES ))&&($oPeriodo->getOid()!=$oPeriodoActual->getOid())) {
			throw new GenericException( CYT_MSG_EVALUACION_ANTERIORES_PROHIBIDO );
		}
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_periodo', $oPeriodo->getOid(), '=');
		$managerModeloPlanilla =  ManagerFactory::getModeloPlanillaManager();
		$oModeloPlanilla = $managerModeloPlanilla->getEntity($oCriteria);
		$pdf->setModeloPlanilla($oModeloPlanilla);
		
		$pdf->setDs_investigador($oSolicitud->getDocente()->getDs_apellido().', '.$oSolicitud->getDocente()->getDs_nombre());
		
		$pdf->setFacultadplanilla_oid($oSolicitud->getFacultadplanilla()->getOid());
	
    	($oSolicitud->getFacultadplanilla()->getOid() != CYT_FACULTAD_NO_DECLARADA)?$pdf->setDs_facultadplanilla($oSolicitud->getFacultadplanilla()->getDs_facultad()):$pdf->setDs_facultadplanilla(CYT_MSG_SOLICITUD_UNIVERSIDAD);;
		
    	$pdf->setObservacion($oEvaluacion->getDs_observacion());
    	
    	$pdf->setDs_evaluador($oEvaluacion->getUser()->getDs_username());
    	
    	
		$pdf->title = CYT_MSG_EVALUACION_PDF_TITLE;
		$pdf->SetFont('Arial','', 13);
		
		// establecemos los márgenes
		$pdf->SetMargins(10, 20 , 10);
		$pdf->setMaxWidth($pdf->w - $pdf->lMargin - $pdf->rMargin);
		//$pdf->SetAutoPageBreak(true,90);
		$pdf->AddPage();
		$pdf->AliasNbPages();
		
		//imprimimos la evaluación.
		$pdf->printEvaluacion();
		
		$pdf->Output();

		//para que no haga el forward.
		$forward = null;

		return $forward;
	}


}