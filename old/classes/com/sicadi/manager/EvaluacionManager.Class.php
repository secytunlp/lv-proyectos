<?php

/**
 * Manager para Evaluacion
 *  
 * @author Marcos
 * @since 17-11-2013
 */
class EvaluacionManager extends EntityManager{

	public function getDAO(){
		return CYTSecureDAOFactory::getEvaluacionDAO();
	}

	public function add(Entity $entity) {
    	
		parent::add($entity);
		
    }	
    
     public function update(Entity $entity) {
     	
     	parent::update($entity);
     	
     	
     	$oModeloPlanilla = new ModeloPlanilla();
		$oModeloPlanilla->setOid($entity->getModeloplanilla_oid());
     	
    	$puntajePosgradoDAO =  DAOFactory::getPuntajePosgradoDAO();
        $puntajePosgradoDAO->deletePuntajePosgradoPorEvaluacion($entity->getOid());
     	
     	$oPuntajePosgrado = new PuntajePosgrado();
     	$oPuntajePosgrado->setEvaluacion($entity);
		$oPuntajePosgrado->setModeloPlanilla($oModeloPlanilla);
     	
     	$cd_cargomaximo = explode('-',trim($entity->getPosgrado_oid()));
     	
     	if ($cd_cargomaximo) {
     		$oPosgradoMaximo = new PosgradoMaximo();
	     	$oPosgradoMaximo->setOid($cd_cargomaximo[0]);
	     	
	     	$oPuntajePosgrado->setPosgradoMaximo($oPosgradoMaximo);
	     	
	     	$managerPuntajePosgrado = ManagerFactory::getPuntajePosgradoManager();
			$managerPuntajePosgrado->add($oPuntajePosgrado);
     	}
		
		
		
     	$puntajeCargoDAO =  DAOFactory::getPuntajeCargoDAO();
        $puntajeCargoDAO->deletePuntajeCargoPorEvaluacion($entity->getOid());
     	
     	$oPuntajeCargo = new PuntajeCargo();
     	$oPuntajeCargo->setEvaluacion($entity);
		$oPuntajeCargo->setModeloPlanilla($oModeloPlanilla);
     	
     	$cd_cargomaximo = explode('-',trim($entity->getCargo_oid()));
     	
     	if ($cd_cargomaximo) {
     		$oCargoMaximo = new CargoMaximo();
	     	$oCargoMaximo->setOid($cd_cargomaximo[0]);
	     	
	     	$oPuntajeCargo->setCargoMaximo($oCargoMaximo);
	     	
	     	$managerPuntajeCargo = ManagerFactory::getPuntajeCargoManager();
			$managerPuntajeCargo->add($oPuntajeCargo);
     	}
     	
     	$puntajeAntacadDAO =  DAOFactory::getPuntajeAntacadDAO();
        $puntajeAntacadDAO->deletePuntajeAntacadPorEvaluacion($entity->getOid());
     	
    	foreach ($entity->getAntacads() as $item) {
    		$arrayAntacad = explode('-',trim($item));
    		$oPuntajeAntacad = new PuntajeAntacad();
	     	$oPuntajeAntacad->setEvaluacion($entity);
			$oPuntajeAntacad->setModeloPlanilla($oModeloPlanilla);
    		$oAntacadMaximo = new AntacadMaximo();
	     	$oAntacadMaximo->setOid($arrayAntacad[0]);
	     	
	     	$oPuntajeAntacad->setAntacadMaximo($oAntacadMaximo);
	     	$oPuntajeAntacad->setNu_puntaje($arrayAntacad[1]);
	     	
	     	if ($arrayAntacad[2]) {
	     		$oPuntajeAntacad->setNu_cant($arrayAntacad[2]);
	     	}
	     	
	     	$managerPuntajeAntacad = ManagerFactory::getPuntajeAntacadManager();
			$managerPuntajeAntacad->add($oPuntajeAntacad);
    	}
    	
     	$puntajeAntotrosDAO =  DAOFactory::getPuntajeAntotrosDAO();
        $puntajeAntotrosDAO->deletePuntajeAntotrosPorEvaluacion($entity->getOid());
     	
    	foreach ($entity->getAntotross() as $item) {
    		$arrayAntotros = explode('-',trim($item));
    		$oPuntajeAntotros = new PuntajeAntotros();
	     	$oPuntajeAntotros->setEvaluacion($entity);
			$oPuntajeAntotros->setModeloPlanilla($oModeloPlanilla);
    		$oAntotrosMaximo = new AntotrosMaximo();
	     	$oAntotrosMaximo->setOid($arrayAntotros[0]);
	     	
	     	$oPuntajeAntotros->setAntotrosMaximo($oAntotrosMaximo);
	     	$oPuntajeAntotros->setNu_puntaje($arrayAntotros[1]);
	     	
	     	if ($arrayAntotros[2]) {
	     		$oPuntajeAntotros->setNu_cant($arrayAntotros[2]);
	     	}
	     	
	     	$managerPuntajeAntotros = ManagerFactory::getPuntajeAntotrosManager();
			$managerPuntajeAntotros->add($oPuntajeAntotros);
    	}
    	
     	$puntajeAntproduccionDAO =  DAOFactory::getPuntajeAntproduccionDAO();
        $puntajeAntproduccionDAO->deletePuntajeAntproduccionPorEvaluacion($entity->getOid());
     	
    	foreach ($entity->getAntproduccions() as $item) {
    		$arrayAntproduccion = explode('-',trim($item));
    		$oPuntajeAntproduccion = new PuntajeAntproduccion();
	     	$oPuntajeAntproduccion->setEvaluacion($entity);
			$oPuntajeAntproduccion->setModeloPlanilla($oModeloPlanilla);
    		$oAntproduccionMaximo = new AntproduccionMaximo();
	     	$oAntproduccionMaximo->setOid($arrayAntproduccion[0]);
	     	
	     	$oPuntajeAntproduccion->setAntproduccionMaximo($oAntproduccionMaximo);
	     	$oPuntajeAntproduccion->setNu_puntaje($arrayAntproduccion[1]);
	     	
	     	if ($arrayAntproduccion[2]) {
	     		$oPuntajeAntproduccion->setNu_cant($arrayAntproduccion[2]);
	     	}
	     	
	     	$managerPuntajeAntproduccion = ManagerFactory::getPuntajeAntproduccionManager();
			$managerPuntajeAntproduccion->add($oPuntajeAntproduccion);
    	}
    	
     	$puntajeAntjustificacionDAO =  DAOFactory::getPuntajeAntjustificacionDAO();
        $puntajeAntjustificacionDAO->deletePuntajeAntjustificacionPorEvaluacion($entity->getOid());
     	
    	foreach ($entity->getAntjustificacions() as $item) {
    		$arrayAntjustificacion = explode('-',trim($item));
    		$oPuntajeAntjustificacion = new PuntajeAntjustificacion();
	     	$oPuntajeAntjustificacion->setEvaluacion($entity);
			$oPuntajeAntjustificacion->setModeloPlanilla($oModeloPlanilla);
    		$oAntjustificacionMaximo = new AntjustificacionMaximo();
	     	$oAntjustificacionMaximo->setOid($arrayAntjustificacion[0]);
	     	
	     	$oPuntajeAntjustificacion->setAntjustificacionMaximo($oAntjustificacionMaximo);
	     	$oPuntajeAntjustificacion->setNu_puntaje($arrayAntjustificacion[1]);
	     	
	     	if ($arrayAntjustificacion[2]) {
	     		$oPuntajeAntjustificacion->setNu_cant($arrayAntjustificacion[2]);
	     	}
	     	
	     	$managerPuntajeAntjustificacion = ManagerFactory::getPuntajeAntjustificacionManager();
			$managerPuntajeAntjustificacion->add($oPuntajeAntjustificacion);
    	}
    	
     	$puntajeSubanteriorDAO =  DAOFactory::getPuntajeSubanteriorDAO();
        $puntajeSubanteriorDAO->deletePuntajeSubanteriorPorEvaluacion($entity->getOid());
     	
    	foreach ($entity->getSubanteriors() as $item) {
    		$arraySubanterior = explode('-',trim($item));
    		$oPuntajeSubanterior = new PuntajeSubanterior();
	     	$oPuntajeSubanterior->setEvaluacion($entity);
			$oPuntajeSubanterior->setModeloPlanilla($oModeloPlanilla);
    		$oSubanteriorMaximo = new SubanteriorMaximo();
	     	$oSubanteriorMaximo->setOid($arraySubanterior[0]);
	     	
	     	$oPuntajeSubanterior->setSubanteriorMaximo($oSubanteriorMaximo);
	     	$oPuntajeSubanterior->setNu_puntaje($arraySubanterior[1]);
	     	
	     	if ($arraySubanterior[2]) {
	     		$oPuntajeSubanterior->setNu_cant($arraySubanterior[2]);
	     	}
	     	
	     	$managerPuntajeSubanterior = ManagerFactory::getPuntajeSubanteriorManager();
			$managerPuntajeSubanterior->add($oPuntajeSubanterior);
    	}
    	
     	
     	
     }

    
    
    
	/**
     * se elimina la entity
     * @param int identificador de la entity a eliminar.
     */
    public function delete($id) {
        
		parent::delete( $id );
		
    	
    }
    
    
	public function sendSolicitud(Entity $entity) {
	//	$this->validateOnSend($entity);
		//armamos el pdf con la data necesaria.
		$pdf = new ViewSolicitudPDF();
		
		
		
		$oid = $entity->getSolicitud()->getOid();
		
		
		$oSolicitudManager = ManagerFactory::getSolicitudManager();
		$oSolicitud = $oSolicitudManager->getObjectByCode($oid);
		
		/*$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_solicitud', $oSolicitud->getOid(), '=');
		
		//becas.
		$becasManager = ManagerFactory::getJovenesBecaManager();
		$oSolicitud->setBecas( $becasManager->getEntities($oCriteria) );
		
		//presupuestos.
		$presupuestosManager = new PresupuestoManager();
		$oSolicitud->setPresupuestos( $presupuestosManager->getEntities($oCriteria) );
				
		//proyectos.
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_solicitud', $oSolicitud->getOid(), '=');
		$filter = new CdtSimpleExpression("(dt_hasta > '".date('Y-m-d')."' OR dt_hasta IS NULL OR dt_hasta = '0000-00-00')");
		$oCriteria->setExpresion($filter);
		$proyectosActualesManager = ManagerFactory::getJovenesProyectoManager();
		$oSolicitud->setProyectos( $proyectosActualesManager->getEntities($oCriteria) );
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_solicitud', $oSolicitud->getOid(), '=');
		$filter = new CdtSimpleExpression("(dt_hasta <= '".date('Y-m-d')."')");
		$oCriteria->setExpresion($filter);
		$proyectosAnterioresManager = ManagerFactory::getJovenesProyectoManager();
		$oSolicitud->setJovenesProyectos( $proyectosAnterioresManager->getEntities($oCriteria) );*/
		
		$oEstado = new Estado();
		$oEstado->setOid(CYT_ESTADO_SOLICITUD_EN_EVALUACION);
		$this->cambiarEstado($entity, $oEstado, '');
		$oSolicitudManager->cambiarEstado($oSolicitud, $oEstado, '');
		
		$oEstado = new Estado();
		$oEstado->setOid(CYT_ESTADO_SOLICITUD_RECIBIDA);//Se pasa a evaluacion recibida para que el evaluador la acepte o no
	
		$this->cambiarEstado($entity, $oEstado, '');
		
		/*$pdf->setEstado_oid(CYT_ESTADO_SOLICITUD_RECIBIDA);
		$pdf->setPeriodo_oid($oSolicitud->getPeriodo()->getOid());*/
		
		
		$oPeriodoManager =  CYTSecureManagerFactory::getPeriodoManager();
		$oPeriodo = $oPeriodoManager->getObjectByCode($oSolicitud->getPeriodo()->getOid());
		/*$pdf->setYear($oPeriodo->getDs_periodo());
				
		$pdf->setDs_investigador($oSolicitud->getDocente()->getDs_apellido().', '.$oSolicitud->getDocente()->getDs_nombre());
		$pdf->setNu_cuil($oSolicitud->getDocente()->getNu_precuil().'-'.$oSolicitud->getDocente()->getNu_documento().'-'.$oSolicitud->getDocente()->getNu_postcuil());
		
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
		
		$oLugarTrabajoManager =  CYTSecureManagerFactory::getLugarTrabajoManager();
		$oLugarTrabajo = $oLugarTrabajoManager->getObjectByCode($oSolicitud->getLugarTrabajo()->getOid());
		
		
		
		$pdf->setDs_cargo($oSolicitud->getCargo()->getDs_cargo());
		$pdf->setDs_deddoc($oSolicitud->getDeddoc()->getDs_deddoc());
		$pdf->setDs_facultad($oSolicitud->getFacultad()->getDs_facultad());
		
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
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_solicitud', $oSolicitud->getOid(), '=');
		$jovenesBecasManager = new JovenesBecaManager();
		$pdf->setJovenesBecas( $jovenesBecasManager->getEntities($oCriteria) );
		
		$pdf->setDs_director(($oSolicitud->getBl_director())?CDT_UI_LBL_YES:CDT_UI_LBL_NO);
		
		//proyectos.
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_solicitud', $oSolicitud->getOid(), '=');
		$oneYearAgo = intval(CYT_PERIODO_YEAR)-1;
		$oCriteria->addFilter('dt_hasta', $oneYearAgo.CYT_DIA_MES_PROYECTO_FIN, '>', new CdtCriteriaFormatStringValue());
		$proyectosManager = ManagerFactory::getJovenesProyectoManager();
		$pdf->setProyectos( $proyectosManager->getEntities($oCriteria) );
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_solicitud', $oSolicitud->getOid(), '=');
		$oneYearAgo = intval(CYT_PERIODO_YEAR)-1;
		$oCriteria->addFilter('dt_hasta', $oneYearAgo.CYT_DIA_MES_PROYECTO_FIN, '<=', new CdtCriteriaFormatStringValue());
		$proyectosManager = ManagerFactory::getJovenesProyectoManager();
		$pdf->setJovenesProyectos( $proyectosManager->getEntities($oCriteria) );
		
		$pdf->setDs_objetivo($oSolicitud->getDs_objetivo());
		$pdf->setDs_justificacion($oSolicitud->getDs_justificacion());
		$pdf->setFacultadplanilla_oid($oSolicitud->getFacultadplanilla()->getOid());
    	($oSolicitud->getFacultadplanilla()->getOid() != CYT_FACULTAD_NO_DECLARADA)?$pdf->setDs_facultadplanilla($oSolicitud->getFacultadplanilla()->getDs_facultad()):$pdf->setDs_facultadplanilla(CYT_MSG_SOLICITUD_UNIVERSIDAD);;
		
    	$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_solicitud', $oSolicitud->getOid(), '=');
		$oCriteria->addFilter('cd_tipopresupuesto', CYT_CD_PRESUPUESTO_TIPO_1, '=');
    	$presupuestosManager = new PresupuestoManager();
		$pdf->setPresupuestosTipo1( $presupuestosManager->getEntities($oCriteria) );
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_solicitud', $oSolicitud->getOid(), '=');
		$oCriteria->addFilter('cd_tipopresupuesto', CYT_CD_PRESUPUESTO_TIPO_2, '=');
    	$presupuestosManager = new PresupuestoManager();
		$pdf->setPresupuestosTipo2( $presupuestosManager->getEntities($oCriteria) );
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_solicitud', $oSolicitud->getOid(), '=');
		$oCriteria->addFilter('cd_tipopresupuesto', CYT_CD_PRESUPUESTO_TIPO_3, '=');
    	$presupuestosManager = new PresupuestoManager();
		$pdf->setPresupuestosTipo3( $presupuestosManager->getEntities($oCriteria) );
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_solicitud', $oSolicitud->getOid(), '=');
		$oCriteria->addFilter('cd_tipopresupuesto', CYT_CD_PRESUPUESTO_TIPO_4, '=');
    	$presupuestosManager = new PresupuestoManager();
		$pdf->setPresupuestosTipo4( $presupuestosManager->getEntities($oCriteria) );
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_solicitud', $oSolicitud->getOid(), '=');
		$oCriteria->addFilter('cd_tipopresupuesto', CYT_CD_PRESUPUESTO_TIPO_5, '=');
    	$presupuestosManager = new PresupuestoManager();
		$pdf->setPresupuestosTipo5( $presupuestosManager->getEntities($oCriteria) );
    	
		$pdf->title = CYT_MSG_SOLICITUD_PDF_TITLE;
		$pdf->SetFont('Arial','', 13);
		
		// establecemos los márgenes
		$pdf->SetMargins(10, 20 , 10);
		$pdf->setMaxWidth($pdf->w - $pdf->lMargin - $pdf->rMargin);
		//$pdf->SetAutoPageBreak(true,90);
		$pdf->AddPage();
		$pdf->AliasNbPages();
		
		//imprimimos la solicitud.
		$pdf->printSolicitud();
		
		$dir = CYT_PATH_PDFS.'/';
		if (!file_exists($dir)) mkdir($dir, 0777); 
		$dir .= CYT_PERIODO_YEAR.'/';
		if (!file_exists($dir)) mkdir($dir, 0777); 
		$dir .= $oSolicitud->getDocente()->getNu_documento().'/';
		if (!file_exists($dir)) mkdir($dir, 0777);
		
		
		
		$fileName = $dir.CYT_MSG_SOLICITUD_ARCHIVO_NOMBRE.CYTSecureUtils::stripAccents($oSolicitud->getDocente()->getDs_apellido()).'.pdf';;
		$pdf->Output($fileName,'F');
        //$pdf->Output(); 	
	        
		$attachs = array();
		$handle=opendir($dir);
		while ($archivo = readdir($handle))
		{
	        if (is_file($dir.$archivo))
	        if ((is_file($dir.$archivo))&&(!strchr($archivo,CYT_MSG_EVALUACION_ARCHIVO_NOMBRE)))
	         {
	         	$attachs[]=$dir.$archivo;
	         }
		}*/
        
		
		$year = $oPeriodo->getDs_periodo();
			
		
		$subjectMail = htmlspecialchars(CYT_LBL_EVALUACION_MAIL_SUBJECT.' '.$year, ENT_QUOTES, "UTF-8");
		
		$managerUser = CYTSecureManagerFactory::getUserManager();
		$oUsuario = $managerUser->getObjectByCode($entity->getUser()->getOid());
		 
       if ($oUsuario->getDs_email() != "") {
				$ds_name = ($oUsuario->getDs_name())?$oUsuario->getDs_name():$oUsuario->getDs_username();
         		
         		
        }
		
			
		$xtpl = new XTemplate( CYT_TEMPLATE_EVALUACION_MAIL_ENVIAR );
		$xtpl->assign ( 'img_logo', WEB_PATH.'css/images/image002.gif' );
		$xtpl->assign('solicitud_titulo', $subjectMail);
		$xtpl->assign('solicitud_descripcion', htmlspecialchars(CYT_LBL_SOLICITUD_MAIL_SUBJECT.' '.$year, ENT_QUOTES, "UTF-8"));
		$xtpl->assign('dt_fecha', date('d/m/Y'));
		$xtpl->assign('ds_evaluador', $ds_name);
		$xtpl->assign('urlWeb', WEB_PATH);
		$xtpl->assign('ds_postulante', htmlspecialchars($oSolicitud->getDocente()->getDs_apellido().', '.$oSolicitud->getDocente()->getDs_nombre(), ENT_QUOTES, "UTF-8"));
		$xtpl->assign('urlInstructivo', 'http://secyt.presi.unlp.edu.ar/Wordpress/wp-content/uploads/2017/09/manual_jovenes_investigadores_evaluador2017.pdf');
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('userGroup_oid', CYT_CD_GROUP_COORDINADOR, '=');
				
		$managerUserGroup = CYTSecureManagerFactory::getUserUserGroupManager();
		$oCoordinadores = $managerUserGroup->getEntities($oCriteria);
		$ds_coordinadores='';
		foreach ($oCoordinadores as $oCoordinador) {
			$oUsuarioCoordinador = $managerUser->getObjectByCode($oCoordinador->getUser()->getOid());
		
			$ds_name1 = ($oUsuarioCoordinador->getDs_name())?$oUsuarioCoordinador->getDs_name():$oUsuarioCoordinador->getDs_username();
	         		
			if ($oUsuarioCoordinador->getFacultad()->getOid() != "") {
	        	$managerFacultad = CYTSecureManagerFactory::getFacultadManager();
				$oFacultad = $managerFacultad->getObjectByCode($oUsuarioCoordinador->getFacultad()->getOid());
				if ($oFacultad->getCat()->getOid()==$oSolicitud->getCat()->getOid()) {
					$ds_coordinadores .=$ds_name1. ' ('.$oFacultad->getDs_facultad().') '.$oUsuarioCoordinador->getDs_email().'<br><br>';
				}
				
	        } 		
	        
	        
		}
		
		$xtpl->assign('ds_coordinadores', $ds_coordinadores);
		
		$xtpl->parse('main');		
		$bodyMail = $xtpl->text('main');
		
		
		if ($oUsuario->getDs_email() != "") {
				//$ds_name = ($oUsuario->getDs_name())?$oUsuario->getDs_name():$oUsuario->getDs_username();
         		CYTSecureUtils::sendMail($ds_name, $oUsuario->getDs_email(), $subjectMail, $bodyMail, $attachs);
         		
        }
		
		/*$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('oid', $entity->getUser()->getOid(), '=');
				
		$managerUser = CYTSecureManagerFactory::getUserManager();
		$oUsuario = $managerUser->getEntity($oCriteria);*/
		
       // CYTSecureUtils::sendMail(CDT_POP_MAIL_FROM_NAME, CDT_POP_MAIL_FROM, $subjectMail, $bodyMail, $attachs);
	}
	
	public function send(Entity $entity) {
		CdtUtils::log('entra');
		$this->validateOnSend($entity);
		//armamos el pdf con la data necesaria.
		$pdf = new ViewEvaluacionPDF();
		
		$oUser = CdtSecureUtils::getUserLogged();
		
		$pdf->setSolicitud_oid($entity->getSolicitud()->getOid());
		
		$pdf->setEvaluacion_oid($entity->getOid());
		
		$oEstado = new Estado();
		$oEstado->setOid(CYT_ESTADO_SOLICITUD_EVALUADA);
		$this->cambiarEstado($entity, $oEstado, '');
		
		$pdf->setEstado_oid(CYT_ESTADO_SOLICITUD_EVALUADA);
		
		$oSolicitudManager = ManagerFactory::getSolicitudManager();
		$oSolicitud = $oSolicitudManager->getObjectByCode($entity->getSolicitud()->getOid());
		
		$pdf->setPeriodo_oid($oSolicitud->getPeriodo()->getOid());
		$oPeriodoManager =  CYTSecureManagerFactory::getPeriodoManager();
		$oPeriodo = $oPeriodoManager->getObjectByCode($oSolicitud->getPeriodo()->getOid());
		$pdf->setYear($oPeriodo->getDs_periodo());
		
		$oPeriodoActual = $oPeriodoManager->getPeriodoActual(CYT_PERIODO_YEAR);
		
		$oUser = CdtSecureUtils::getUserLogged();
		if ((!CdtSecureUtils::hasPermission ( $oUser, CYT_FUNCTION_VER_ANTERIORES ))&&($oPeriodo->getOid()!=$oPeriodoActual->getOid())) {
			throw new GenericException( CYT_MSG_EVALUACION_ANTERIORES_PROHIBIDO );
		}
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_periodo', $oPeriodoActual->getOid(), '=');
		$managerModeloPlanilla =  ManagerFactory::getModeloPlanillaManager();
		$oModeloPlanilla = $managerModeloPlanilla->getEntity($oCriteria);
		$pdf->setModeloPlanilla($oModeloPlanilla);
		
		$pdf->setDs_investigador($oSolicitud->getDocente()->getDs_apellido().', '.$oSolicitud->getDocente()->getDs_nombre());
		
		$pdf->setFacultadplanilla_oid($oSolicitud->getFacultadplanilla()->getOid());
	
    	($oSolicitud->getFacultadplanilla()->getOid() != CYT_FACULTAD_NO_DECLARADA)?$pdf->setDs_facultadplanilla($oSolicitud->getFacultadplanilla()->getDs_facultad()):$pdf->setDs_facultadplanilla(CYT_MSG_SOLICITUD_UNIVERSIDAD);;
		
    	$pdf->setObservacion($entity->getDs_observacion());
    	
    	$pdf->setDs_evaluador($entity->getUser()->getDs_username());
    	
    	
		$pdf->title = CYT_MSG_EVALUACION_PDF_TITLE;
		$pdf->SetFont('Arial','', 13);
		
		// establecemos los márgenes
		$pdf->SetMargins(10, 20 , 10);
		$pdf->setMaxWidth($pdf->w - $pdf->lMargin - $pdf->rMargin);
		//$pdf->SetAutoPageBreak(true,90);
		$pdf->AddPage();
		$pdf->AliasNbPages();
		
		//imprimimos la solicitud.
		$pdf->printEvaluacion();
		
		$dir = CYT_PATH_PDFS.'/';
		if (!file_exists($dir)) mkdir($dir, 0777); 
		$dir .= CYT_PERIODO_YEAR.'/';
		if (!file_exists($dir)) mkdir($dir, 0777); 
		$dir .= $oSolicitud->getDocente()->getNu_documento().'/';
		if (!file_exists($dir)) mkdir($dir, 0777);
		
		$ds_apellido = stripslashes(str_replace("'","_",$oSolicitud->getDocente()->getDs_apellido()));
		$ds_apellido = stripslashes(str_replace(" ","_",$ds_apellido));
		$ds_evaluador = stripslashes(str_replace("'","_",str_replace(',','',$oUser->getDs_name())));
		$ds_evaluador = stripslashes(str_replace(" ","_",str_replace(',','',$ds_evaluador)));

		
		$fileName = $dir.CYT_MSG_EVALUACION_ARCHIVO_NOMBRE.CYTSecureUtils::stripAccents($ds_evaluador).'_'.CYTSecureUtils::stripAccents($ds_apellido).'.pdf';;
		$pdf->Output($fileName,'F');
        //$pdf->Output(); 	
	        
		
        
		
		$year = $oPeriodo->getDs_periodo();
			
		
		$subjectMail = htmlspecialchars(CYT_LBL_EVALUACION_MAIL_SUBJECT.' '.$year, ENT_QUOTES, "UTF-8");
			
		$xtpl = new XTemplate( CYT_TEMPLATE_SOLICITUD_MAIL_ENVIAR );
		$xtpl->assign ( 'img_logo', WEB_PATH.'css/images/image002.gif' );
		$xtpl->assign('solicitud_titulo', $subjectMail);
		$xtpl->assign('year_label', CYT_LBL_SOLICITUD_MAIL_YEAR);
		$xtpl->assign('year', $oPeriodo->getDs_periodo());
		$xtpl->assign('investigador_label', CYT_LBL_SOLICITUD_MAIL_INVESTIGADOR);
		$xtpl->assign('investigador', htmlspecialchars($oSolicitud->getDocente()->getDs_apellido().', '.$oSolicitud->getDocente()->getDs_nombre(), ENT_QUOTES, "UTF-8"));
		//$xtpl->assign('comment', CYT_LBL_EVALUACION_MAIL_COMMENT);
		$xtpl->parse('main');		
		$bodyMail = $xtpl->text('main');
		
		$attachs = array();
		$attachs[]=$fileName;

		$managerUser = CYTSecureManagerFactory::getUserManager();
		$oUsuario = $managerUser->getObjectByCode($oUser->getCd_user());
		$ds_name = ($oUsuario->getDs_name())?$oUsuario->getDs_name():$oUsuario->getDs_username();
		CYTSecureUtils::sendMail(CDT_POP_MAIL_FROM_NAME, CDT_POP_MAIL_FROM, $subjectMail, $bodyMail, $attachs,$ds_name,$oUsuario->getDs_email());

		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_solicitud', $oSolicitud->getOid(), '=');
		$tEvaluacionEstado = CYTSecureDAOFactory::getEvaluacionEstadoDAO()->getTableName();
		$oCriteria->addFilter($tEvaluacionEstado.'.estado_oid', CYT_ESTADO_SOLICITUD_EVALUADA, '!=');
		$oCriteria->addNull('fechaHasta');
		$managerEvaluacion =  ManagerFactory::getEvaluacionManager();
		$noEvaluadas = $managerEvaluacion->getEntities($oCriteria);
		if ($noEvaluadas->size()==0) {
			$oCriteria = new CdtSearchCriteria();
			$oCriteria->addFilter('cd_solicitud', $oSolicitud->getOid(), '=');
			$oCriteria->addNull('fechaHasta');
			$managerEvaluacion =  ManagerFactory::getEvaluacionManager();
			$oEvaluaciones = $managerEvaluacion->getEntities($oCriteria);
			if ($oEvaluaciones->size()==1) {
				$oSolicitud->setNu_diferencia(0);
				$oSolicitud->setNu_puntaje($oEvaluaciones->getObjectByIndex(0)->getNu_puntaje());
			}
			else{
				$oSolicitud->setNu_diferencia(abs($oEvaluaciones->getObjectByIndex(0)->getNu_puntaje()-$oEvaluaciones->getObjectByIndex(1)->getNu_puntaje()));
				$oSolicitud->setNu_puntaje(($oEvaluaciones->getObjectByIndex(0)->getNu_puntaje()+$oEvaluaciones->getObjectByIndex(1)->getNu_puntaje())/2);
			}
			/*if ($oSolicitud->getNu_diferencia()<CYT_DIFERENCIA){
				$oEstado = new Estado();
				$oEstado->setOid(CYT_ESTADO_SOLICITUD_EVALUADA);
				$oSolicitudManager = ManagerFactory::getSolicitudManager();
				$oSolicitudManager->cambiarEstado($oSolicitud, $oEstado, '');
			}*/
			if ($oEvaluaciones->size()>2) {
				if ($oEvaluaciones->getObjectByIndex(2)->getNu_puntaje()){
					$puntajes = array(abs($oEvaluaciones->getObjectByIndex(0)->getNu_puntaje()-$oEvaluaciones->getObjectByIndex(1)->getNu_puntaje()),abs($oEvaluaciones->getObjectByIndex(1)->getNu_puntaje()-$oEvaluaciones->getObjectByIndex(2)->getNu_puntaje()),abs($oEvaluaciones->getObjectByIndex(0)->getNu_puntaje()-$oEvaluaciones->getObjectByIndex(2)->getNu_puntaje()));
					CYTSecureUtils::logObject($puntajes);
					$minimo = 100;
					$item=0;
					for ($i=0; $i<3; $i++){
						if($puntajes[$i]<=$minimo){
							$minimo = $puntajes[$i];
							$item=$i;	
						}
						
					}
					switch ( $item) {
						case '0' :
							$oSolicitud->setNu_puntaje(($oEvaluaciones->getObjectByIndex(0)->getNu_puntaje()+$oEvaluaciones->getObjectByIndex(1)->getNu_puntaje())/2);
							$oSolicitud->setNu_diferencia(abs($oEvaluaciones->getObjectByIndex(0)->getNu_puntaje()-$oEvaluaciones->getObjectByIndex(1)->getNu_puntaje()));
							break;
						case '1' :
							$oSolicitud->setNu_puntaje(($oEvaluaciones->getObjectByIndex(1)->getNu_puntaje()+$oEvaluaciones->getObjectByIndex(2)->getNu_puntaje())/2);
							$oSolicitud->setNu_diferencia(abs($oEvaluaciones->getObjectByIndex(1)->getNu_puntaje()-$oEvaluaciones->getObjectByIndex(2)->getNu_puntaje()));
							break;
						case '2' :
							$oSolicitud->setNu_puntaje(($oEvaluaciones->getObjectByIndex(0)->getNu_puntaje()+$oEvaluaciones->getObjectByIndex(2)->getNu_puntaje())/2);
							$oSolicitud->setNu_diferencia(abs($oEvaluaciones->getObjectByIndex(0)->getNu_puntaje()-$oEvaluaciones->getObjectByIndex(2)->getNu_puntaje()));
							break;
					}
					$oEstado = new Estado();
					$oEstado->setOid(CYT_ESTADO_SOLICITUD_EVALUADA);
					$oSolicitudManager = ManagerFactory::getSolicitudManager();
					$oSolicitudManager->cambiarEstado($oSolicitud, $oEstado, '');
					
				}
			}
			else{
				$oEstado = new Estado();
				$oEstado->setOid(CYT_ESTADO_SOLICITUD_EVALUADA);
				$oSolicitudManager = ManagerFactory::getSolicitudManager();
				$oSolicitudManager->cambiarEstado($oSolicitud, $oEstado, '');
			}
			$oSolicitudManager = ManagerFactory::getSolicitudManager();
			$oSolicitudManager->updateWithoutRelations($oSolicitud);
		}
	}
    
	public function cambiarEstado(Evaluacion $oEvaluacion, Estado $oEstado, $motivo){
	 	$oEvaluacionEstado = new EvaluacionEstado();
		$oEvaluacionEstado->setEvaluacion($oEvaluacion);
		$oEvaluacionEstado->setFechaDesde(date(DB_DEFAULT_DATETIME_FORMAT));
		$oEvaluacionEstado->setEstado($oEstado);
		$oEvaluacionEstado->setMotivo($motivo);
		$oUser = CdtSecureUtils::getUserLogged();
		$oEvaluacionEstado->setUser($oUser);
		$oEvaluacionEstado->setFechaUltModificacion(date(DB_DEFAULT_DATETIME_FORMAT));
	 	$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('evaluacion_oid', $oEvaluacion->getOid(), '=');
		$oCriteria->addNull('fechaHasta');
		$managerEvaluacionEstado =  CYTSecureManagerFactory::getEvaluacionEstadoManager();
		$evaluacionEstado = $managerEvaluacionEstado->getEntity($oCriteria);
		if (!empty($evaluacionEstado)) {
			if ($evaluacionEstado->getEstado()->getOid()!=$oEstado->getOid()) {// si el estado anterior es el mismo que el nuevo no hago nada
				$evaluacionEstado->setFechaHasta(date(DB_DEFAULT_DATETIME_FORMAT));
				$evaluacionEstado->setUser($oUser);
				$evaluacionEstado->setFechaUltModificacion(date(DB_DEFAULT_DATETIME_FORMAT));
				$evaluacionEstado->setEvaluacion($oEvaluacion);
				$managerEvaluacionEstado->update($evaluacionEstado);
				$managerEvaluacionEstado->add($oEvaluacionEstado);
			}
		}
		else
			$managerEvaluacionEstado->add($oEvaluacionEstado);
	 }
	
	protected function validateOnSend(Entity $entity){
	
		$error='';
		
		if ((!$entity->getNu_puntaje())||($entity->getNu_puntaje()==0)) {
			$error .= CYT_MSG_EVALUACION_PUNTAJE_CERO.'<br>';
		}
		
    	
    	
		if ($error) {
    		throw new GenericException( $error );
    	}
	}
	
	public function eliminarEvaluacion($evaluacion_oid){
		$oPuntajePosgradoDAO = DAOFactory::getPuntajePosgradoDAO();
		$oPuntajePosgradoDAO->deletePuntajePosgradoPorEvaluacion($evaluacion_oid);
		$oPuntajeAntacadDAO = DAOFactory::getPuntajeAntacadDAO();
		$oPuntajeAntacadDAO->deletePuntajeAntacadPorEvaluacion($evaluacion_oid);
		$oPuntajeCargoDAO = DAOFactory::getPuntajeCargoDAO();
		$oPuntajeCargoDAO->deletePuntajeCargoPorEvaluacion($evaluacion_oid);
		$oPuntajeAntotrosDAO = DAOFactory::getPuntajeAntotrosDAO();
		$oPuntajeAntotrosDAO->deletePuntajeAntotrosPorEvaluacion($evaluacion_oid);
		$oPuntajeAntproduccionDAO = DAOFactory::getPuntajeAntproduccionDAO();
		$oPuntajeAntproduccionDAO->deletePuntajeAntproduccionPorEvaluacion($evaluacion_oid);
		$oPuntajeAntjustificacionDAO = DAOFactory::getPuntajeAntjustificacionDAO();
		$oPuntajeAntjustificacionDAO->deletePuntajeAntjustificacionPorEvaluacion($evaluacion_oid);
		$oPuntajeSubanteriorDAO = DAOFactory::getPuntajeSubanteriorDAO();
		$oPuntajeSubanteriorDAO->deletePuntajeSubanteriorPorEvaluacion($evaluacion_oid);
		$oEvaluacionEstadoDAO =  CYTSecureDAOFactory::getEvaluacionEstadoDAO();
		$oEvaluacionEstadoDAO->deleteEvaluacionEstadoPorEvaluacion($evaluacion_oid);
		$this->delete($evaluacion_oid);
	}
	
	public function confirm(Entity $entity, $estado_oid, $motivo='') {
		
		$oid = $entity->getOid();
		
		
		$oEvaluacionManager = ManagerFactory::getEvaluacionManager();
		$oEvaluacion = $oEvaluacionManager->getObjectByCode($oid);
		$oEstado = new Estado();
		$oEstado->setOid($estado_oid);
		$this->cambiarEstado($oEvaluacion, $oEstado, $motivo);
		
		$oSolicitudManager = ManagerFactory::getSolicitudManager();
		$oSolicitud = $oSolicitudManager->getObjectByCode($entity->getSolicitud()->getOid());
		
		switch ($estado_oid) {
			case CYT_ESTADO_SOLICITUD_EN_EVALUACION:
				$ds_subjet = CYT_LBL_EVALUACION_ADMISION;
				$ds_comment = CYT_LBL_SOLICITUD_ADMISION_COMMENT;
			break;
			
			case CYT_ESTADO_SOLICITUD_NO_ADMITIDA:
				$ds_subjet = CYT_LBL_EVALUACION_NO_ADMISION;
				$ds_comment = '<strong>'.htmlspecialchars(CYT_LBL_EVALUACION_NO_ADMISION_COMMENT).'</strong>: '.htmlspecialchars($motivo);
			break;
			
		}
		
        
		$msg = $ds_subjet.CYT_LBL_EVALUACION_MAIL_SUBJECT;
		
		$oPeriodoManager =  CYTSecureManagerFactory::getPeriodoManager();
		$oPeriodo = $oPeriodoManager->getObjectByCode($oSolicitud->getPeriodo()->getOid());
		
		$year = $oPeriodo->getDs_periodo();
		$yearP = $year+1;
    	$params = array ($year,$yearP );		
		
		$subjectMail = htmlspecialchars(CdtFormatUtils::formatMessage( $msg, $params ), ENT_QUOTES, "UTF-8");
		
		
		$xtpl = new XTemplate( CYT_TEMPLATE_SOLICITUD_MAIL_ENVIAR );
		$xtpl->assign ( 'img_logo', WEB_PATH.'css/images/image002.gif' );
		$xtpl->assign('solicitud_titulo', $subjectMail);
		$xtpl->assign('year_label', CYT_LBL_SOLICITUD_MAIL_YEAR);
		$xtpl->assign('year', $oPeriodo->getDs_periodo());
		$xtpl->assign('investigador_label', CYT_LBL_SOLICITUD_MAIL_INVESTIGADOR);
		$xtpl->assign('investigador', htmlspecialchars($oSolicitud->getDocente()->getDs_apellido().', '.$oSolicitud->getDocente()->getDs_nombre(), ENT_QUOTES, "UTF-8"));
		$xtpl->assign('comment', $ds_comment);
		$xtpl->parse('main');		
		$bodyMail = $xtpl->text('main');
		
		
		$oUser = CdtSecureUtils::getUserLogged();
		
		$managerUser = CYTSecureManagerFactory::getUserManager();
		$oUsuario = $managerUser->getObjectByCode($oUser->getCd_user());
		$ds_name = ($oUsuario->getDs_name())?$oUsuario->getDs_name():$oUsuario->getDs_username();
		CYTSecureUtils::sendMail(CDT_POP_MAIL_FROM_NAME, CDT_POP_MAIL_FROM, $subjectMail, $bodyMail, $attachs,$ds_name,$oUsuario->getDs_email());
		
		
        
				
       
        
        
	}
	
	public function actualizarPuntaje(Entity $entity) {
		$oSolicitudManager = ManagerFactory::getSolicitudManager();
		$oSolicitud = $oSolicitudManager->getObjectByCode($entity->getSolicitud()->getOid());
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_solicitud', $oSolicitud->getOid(), '=');
		$tEvaluacionEstado = CYTSecureDAOFactory::getEvaluacionEstadoDAO()->getTableName();
		$oCriteria->addFilter($tEvaluacionEstado.'.estado_oid', CYT_ESTADO_SOLICITUD_EVALUADA, '=');
		$oCriteria->addNull('fechaHasta');
		$managerEvaluacion =  ManagerFactory::getEvaluacionManager();
		$oEvaluaciones = $managerEvaluacion->getEntities($oCriteria);
		if ($oEvaluaciones->size()==1) {
			$oSolicitud->setNu_diferencia(0);
			$oSolicitud->setNu_puntaje($oEvaluaciones->getObjectByIndex(0)->getNu_puntaje());
		}
		else{
			$oSolicitud->setNu_diferencia(abs($oEvaluaciones->getObjectByIndex(0)->getNu_puntaje()-$oEvaluaciones->getObjectByIndex(1)->getNu_puntaje()));
			$oSolicitud->setNu_puntaje(($oEvaluaciones->getObjectByIndex(0)->getNu_puntaje()+$oEvaluaciones->getObjectByIndex(1)->getNu_puntaje())/2);
		}
		if ($oEvaluaciones->size()>2) {
			if ($oEvaluaciones->getObjectByIndex(2)->getNu_puntaje()){
				$puntajes = array(abs($oEvaluaciones->getObjectByIndex(0)->getNu_puntaje()-$oEvaluaciones->getObjectByIndex(1)->getNu_puntaje()),abs($oEvaluaciones->getObjectByIndex(1)->getNu_puntaje()-$oEvaluaciones->getObjectByIndex(2)->getNu_puntaje()),abs($oEvaluaciones->getObjectByIndex(0)->getNu_puntaje()-$oEvaluaciones->getObjectByIndex(2)->getNu_puntaje()));
				CYTSecureUtils::logObject($puntajes);
				$minimo = 100;
				$item=0;
				for ($i=0; $i<3; $i++){
					if($puntajes[$i]<=$minimo){
						$minimo = $puntajes[$i];
						$item=$i;	
					}
					
				}
				switch ( $item) {
					case '0' :
						$oSolicitud->setNu_puntaje(($oEvaluaciones->getObjectByIndex(0)->getNu_puntaje()+$oEvaluaciones->getObjectByIndex(1)->getNu_puntaje())/2);
						$oSolicitud->setNu_diferencia(abs($oEvaluaciones->getObjectByIndex(0)->getNu_puntaje()-$oEvaluaciones->getObjectByIndex(1)->getNu_puntaje()));
						break;
					case '1' :
						$oSolicitud->setNu_puntaje(($oEvaluaciones->getObjectByIndex(1)->getNu_puntaje()+$oEvaluaciones->getObjectByIndex(2)->getNu_puntaje())/2);
						$oSolicitud->setNu_diferencia(abs($oEvaluaciones->getObjectByIndex(1)->getNu_puntaje()-$oEvaluaciones->getObjectByIndex(2)->getNu_puntaje()));
						break;
					case '2' :
						$oSolicitud->setNu_puntaje(($oEvaluaciones->getObjectByIndex(0)->getNu_puntaje()+$oEvaluaciones->getObjectByIndex(2)->getNu_puntaje())/2);
						$oSolicitud->setNu_diferencia(abs($oEvaluaciones->getObjectByIndex(0)->getNu_puntaje()-$oEvaluaciones->getObjectByIndex(2)->getNu_puntaje()));
						break;
				}
				$oEstado = new Estado();
				$oEstado->setOid(CYT_ESTADO_SOLICITUD_EVALUADA);
				$oSolicitudManager = ManagerFactory::getSolicitudManager();
				$oSolicitudManager->cambiarEstado($oSolicitud, $oEstado, '');
				
			}
		}
		else{
			$oEstado = new Estado();
			$oEstado->setOid(CYT_ESTADO_SOLICITUD_EVALUADA);
			$oSolicitudManager = ManagerFactory::getSolicitudManager();
			$oSolicitudManager->cambiarEstado($oSolicitud, $oEstado, '');
		}
		$oSolicitudManager = ManagerFactory::getSolicitudManager();
		$oSolicitudManager->updateWithoutRelations($oSolicitud);
	}
	
	
}
?>
