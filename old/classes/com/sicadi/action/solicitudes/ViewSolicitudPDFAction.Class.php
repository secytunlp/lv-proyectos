<?php

/**
 * Acción para exportar a pdf una solicitud.
 *
 * @author Marcos
 * @since 19-11-203
 *
 */
class ViewSolicitudPDFAction extends CdtAction{

	
	
	/**
	 * (non-PHPdoc)
	 * @see CdtAction::execute()
	 */
	public function execute(){

		//armamos el pdf con la data necesaria.
		$pdf = new ViewSolicitudPDF();
		
		
		
		$oid = CdtUtils::getParam('id');
		
		$oSolicitudManager = ManagerFactory::getSolicitudManager();
		$oSolicitud = $oSolicitudManager->getObjectByCode($oid);
		if(empty($oSolicitud)){
			throw new GenericException( CDT_SECURE_MSG_PERMISSION_DENIED );
		}
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('solicitud_oid', $oSolicitud->getOid(), '=');
		$oCriteria->addNull('fechaHasta');
		$managerSolicitudEstado =  CYTSecureManagerFactory::getSolicitudEstadoManager();
		$oSolicitudEstado = $managerSolicitudEstado->getEntity($oCriteria);
		$pdf->setEstado_oid($oSolicitudEstado->getEstado()->getOid());
		$pdf->setPeriodo_oid($oSolicitud->getPeriodo()->getOid());
		
		
		$oPeriodoManager =  CYTSecureManagerFactory::getPeriodoManager();
		$oPeriodo = $oPeriodoManager->getObjectByCode($oSolicitud->getPeriodo()->getOid());
		$pdf->setYear($oPeriodo->getDs_periodo());
		
		$oPeriodoActual = $oPeriodoManager->getPeriodoActual(CYT_PERIODO_YEAR);
		
		$oUser = CdtSecureUtils::getUserLogged();
		if ((!CdtSecureUtils::hasPermission ( $oUser, CYT_FUNCTION_VER_ANTERIORES ))&&($oPeriodo->getOid()!=$oPeriodoActual->getOid())) {
			throw new GenericException( CYT_MSG_SOLICITUD_ANTERIORES_PROHIBIDO );
		}
		//CdtUtils::log($oSolicitud->getDocente()->getNu_precuil().'-'.str_pad($oSolicitud->getDocente()->getNu_documento(), 8, "0", STR_PAD_LEFT).'!='.$oUser->getDs_username());
		if (CdtSecureUtils::hasPermission ( $oUser, CYT_FUNCTION_ENVIAR_SOLICITUD )) {
            if (($oSolicitud->getDocente()->getNu_precuil().'-'.str_pad($oSolicitud->getDocente()->getNu_documento(), 8, "0", STR_PAD_LEFT).'-'.$oSolicitud->getDocente()->getNu_postcuil())!=$oUser->getDs_username()){
                throw new GenericException( CDT_SECURE_MSG_PERMISSION_DENIED );
            }
            
        }

		$pdf->setDt_fecha($oSolicitud->getDt_fecha());
		
		$pdf->setDs_investigador($oSolicitud->getDocente()->getDs_apellido().', '.$oSolicitud->getDocente()->getDs_nombre());
		$pdf->setNu_cuil($oSolicitud->getDocente()->getNu_precuil().'-'.str_pad($oSolicitud->getDocente()->getNu_documento(), 8, "0", STR_PAD_LEFT).'-'.$oSolicitud->getDocente()->getNu_postcuil());
		
		$pdf->setDs_calle($oSolicitud->getDs_calle());
		$pdf->setNu_nro($oSolicitud->getNu_nro());
		$pdf->setDs_depto($oSolicitud->getDs_depto());
		$pdf->setNu_piso($oSolicitud->getNu_piso());
		$pdf->setNu_cp($oSolicitud->getNu_cp());
		$pdf->setDs_mail($oSolicitud->getDs_mail());
		$pdf->setNu_telefono($oSolicitud->getNu_telefono());
		$pdf->setBl_notificacion($oSolicitud->getBl_notificacion());
		$pdf->setDs_titulogrado($oSolicitud->getDs_titulogrado());
		$pdf->setDt_egresogrado(CYTSecureUtils::formatDateToView($oSolicitud->getDt_egresogrado()));
		$pdf->setDs_tituloposgrado($oSolicitud->getDs_tituloposgrado());
		$pdf->setDt_egresoposgrado(CYTSecureUtils::formatDateToView($oSolicitud->getDt_egresoposgrado()));
		$ds_sigla = ($oSolicitud->getLugarTrabajo()->getDs_sigla())?" (".$oSolicitud->getLugarTrabajo()->getDs_sigla().")":"";
		$pdf->setDs_lugarTrabajo($oSolicitud->getLugarTrabajo()->getDs_unidad().$ds_sigla);
		
		if ($oSolicitud->getLugarTrabajo()->getOid()) {
			$oLugarTrabajoManager =  CYTSecureManagerFactory::getLugarTrabajoManager();
			$oLugarTrabajo = $oLugarTrabajoManager->getObjectByCode($oSolicitud->getLugarTrabajo()->getOid());
		}
		
		
		
		
		
		/*$pdf->setDs_cargo($oSolicitud->getCargo()->getDs_cargo());
		$pdf->setDs_deddoc($oSolicitud->getDeddoc()->getDs_deddoc());
		$pdf->setDs_facultad($oSolicitud->getFacultad()->getDs_facultad());*/
		//$pdf->setDs_disciplina($oSolicitud->getDs_disciplina());
		
		$pdf->setBl_becario($oSolicitud->getBl_becario());
		$pdf->setDs_orgbeca($oSolicitud->getDs_orgbeca());
		$pdf->setDs_tipobeca($oSolicitud->getDs_tipobeca());
		$pdf->setDs_periodobeca(CYTSecureUtils::formatDateToView($oSolicitud->getDt_becadesde()).' - '.CYTSecureUtils::formatDateToView($oSolicitud->getDt_becahasta()));
		$ds_sigla = ($oSolicitud->getLugarTrabajoBeca()->getDs_sigla())?" (".$oSolicitud->getLugarTrabajoBeca()->getDs_sigla().")":"";
		$pdf->setDs_lugarTrabajoBeca($oSolicitud->getLugarTrabajoBeca()->getDs_unidad().$ds_sigla);
		
		
		$pdf->setBl_carrera($oSolicitud->getBl_carrera());
		$pdf->setDs_organismo($oSolicitud->getOrganismo()->getDs_organismo());
		$pdf->setDs_carrerainv($oSolicitud->getCarrerainv()->getDs_carrerainv());
		$pdf->setDt_ingreso(CYTSecureUtils::formatDateToView($oSolicitud->getDt_ingreso()));
		$ds_sigla = ($oSolicitud->getLugarTrabajoCarrera()->getDs_sigla())?" (".$oSolicitud->getLugarTrabajoCarrera()->getDs_sigla().")":"";
		$pdf->setDs_lugarTrabajoCarrera($oSolicitud->getLugarTrabajoCarrera()->getDs_unidad().$ds_sigla);

        $pdf->setDs_categoria($oSolicitud->getCategoria()->getDs_categoria());
        $pdf->setDs_equivalencia($oSolicitud->getEquivalencia()->getDs_equivalencia());
        //CYTSecureUtils::logObject($oSolicitud);
        $pdf->setDs_categoriasicadi($oSolicitud->getCategoriasicadi()->getDs_categoriasicadi());


        //cargos
        $oCriteria = new CdtSearchCriteria();
        $oCriteria->addFilter('cd_solicitud', $oSolicitud->getOid(), '=');

        $oCargoManager =  ManagerFactory::getSolicitudCargoManager();
        $pdf->setCargos( $oCargoManager->getEntities($oCriteria) );

		//proyectos.
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_solicitud', $oSolicitud->getOid(), '=');
        $oCriteria->addFilter('bl_agregado', 0, '=');
		$proyectosManager = ManagerFactory::getOtrosProyectoManager();
		$pdf->setProyectos( $proyectosManager->getEntities($oCriteria) );
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_solicitud', $oSolicitud->getOid(), '=');
        $oCriteria->addFilter('bl_agregado', 1, '=');

		$proyectosManager = ManagerFactory::getOtrosProyectoManager();
		$pdf->setSolicitudProyectos( $proyectosManager->getEntities($oCriteria) );
		

		$pdf->setFacultadplanilla_oid($oSolicitud->getFacultadplanilla()->getOid());
    	($oSolicitud->getFacultadplanilla()->getOid() != CYT_FACULTAD_NO_DECLARADA)?$pdf->setDs_facultadplanilla($oSolicitud->getFacultadplanilla()->getDs_facultad()):$pdf->setDs_facultadplanilla(CYT_MSG_SOLICITUD_UNIVERSIDAD);;
		
		
    	
		$pdf->setTitle(CYT_MSG_SOLICITUD_PDF_TITLE);
		$pdf->SetFont('Arial','', 13);
		
		// establecemos los márgenes
		$pdf->SetMargins(10, 20 , 10);
		$pdf->setMaxWidth(210 - 10 - 10);
		//$pdf->SetAutoPageBreak(true,90);
		$pdf->AddPage();
		$pdf->AliasNbPages();
		
		//imprimimos la solicitud.
		$pdf->printSolicitud();
		
		$pdf->Output();

		//para que no haga el forward.
		$forward = null;

		return $forward;
	}


}