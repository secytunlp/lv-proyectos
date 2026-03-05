<?php

/**
 * Acción para inicializar el contexto
 * para editar una evaluación.
 *
 * @author Marcos
 * @since 21-05-2014
 *
 */

class EvaluarSolicitudInitAction extends UpdateEntityInitAction {

	
	protected function getEntity(){

		//$entity = parent::getEntity();
		$oUser = CdtSecureUtils::getUserLogged();
		
		$solicitud_oid = CdtUtils::getParam('id');
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_solicitud', $solicitud_oid, '=');
		$oCriteria->addFilter('cd_usuario', $oUser->getCd_user(), '=');
		$oCriteria->addNull('fechaHasta');
		$managerEvaluacion =  ManagerFactory::getEvaluacionManager();
		$entity = $managerEvaluacion->getEntity($oCriteria);

		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('evaluacion_oid', $entity->getOid(), '=');
		$oCriteria->addNull('fechaHasta');
		$managerEvaluacionEstado =  CYTSecureManagerFactory::getEvaluacionEstadoManager();
		$oEvaluacionEstado = $managerEvaluacionEstado->getEntity($oCriteria);
		if (($oEvaluacionEstado->getEstado()->getOid()==CYT_ESTADO_SOLICITUD_EVALUADA)) {
			
			throw new GenericException( CYT_MSG_EVALUACION_MODIFICAR_PROHIBIDO);
		}
			
		$oSolicitudManager =  ManagerFactory::getSolicitudManager();
		$oSolicitud = $oSolicitudManager->getObjectByCode($solicitud_oid);
				
		$oDocenteManager =  CYTSecureManagerFactory::getDocenteManager();
		$oDocente = $oDocenteManager->getObjectByCode($oSolicitud->getDocente()->getOid());
		
		$entity->setDs_investigador($oDocente->getDs_apellido().', '.$oDocente->getDs_nombre());
		
		$oFacultadManager =  CYTSecureManagerFactory::getFacultadManager();
		$oFacultad = $oFacultadManager->getObjectByCode($oSolicitud->getFacultadplanilla()->getOid());
        
		$entity->setDs_facultad($oFacultad->getDs_facultad());
		
		$oPeriodoManager = CYTSecureManagerFactory::getPeriodoManager();
		$oPerioActual = $oPeriodoManager->getPeriodoActual(CYT_PERIODO_YEAR);
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_periodo', $oPerioActual->getOid(), '=');
		$managerModeloPlanilla =  ManagerFactory::getModeloPlanillaManager();
		$oModeloPlanilla = $managerModeloPlanilla->getEntity($oCriteria);
		
		$entity->setNu_max($oModeloPlanilla->getNu_max());
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_modeloplanilla', $oModeloPlanilla->getOid(), '=');
		$oCriteria->addOrder('nu_max','DESC');
		$managerPosgradoMaximo =  ManagerFactory::getPosgradoMaximoManager();
		$oPosgradosMaximos = $managerPosgradoMaximo->getEntities($oCriteria);
		
		$entity->setNu_posgradomaximo($oPosgradosMaximos->getObjectByIndex(0)->getNu_max());
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $entity->getOid(), '=');
		$managerPuntajePosgrado =  ManagerFactory::getPuntajePosgradoManager();
		$oPuntajePosgrado = $managerPuntajePosgrado->getEntity($oCriteria);
		
		if ($oPuntajePosgrado) {
			$entity->setCd_posgradomaximo($oPuntajePosgrado->getPosgradoMaximo()->getOid().'-'.$oPuntajePosgrado->getPosgradoMaximo()->getNu_max());
		}
		
		
		
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_modeloplanilla', $oModeloPlanilla->getOid(), '=');
		$oCriteria->addOrder('cd_antacadmaximo');
		$managerAntacadMaximo =  ManagerFactory::getAntacadMaximoManager();
		$oAntacadsMaximos = $managerAntacadMaximo->getEntities($oCriteria);
		
		$entity->setNu_antacadmaximo($oAntacadsMaximos->getObjectByIndex(0)->getPuntajeGrupo()->getNu_max());
		$entity->setNu_topeA2('<strong>Max. '.$oAntacadsMaximos->getObjectByIndex(0)->getNu_tope().' '.CYT_LBL_EVALUACION_PT.'</strong>');
		
		$ds_descripcionA2 = $oAntacadsMaximos->getObjectByIndex(0)->getAntacadPlanilla()->getDs_antacadplanilla();
		$ds_descripcionA2 = str_replace('#puntaje#', '<strong>'.$oAntacadsMaximos->getObjectByIndex(0)->getNu_max().' '.CYT_LBL_EVALUACION_PUNTOS.'</strong>',$ds_descripcionA2);
		$entity->setDs_descripcionA2($ds_descripcionA2);
		
		$nu_max = ($oAntacadsMaximos->getObjectByIndex(0)->getNu_max()==$oAntacadsMaximos->getObjectByIndex(0)->getNu_min())?$oAntacadsMaximos->getObjectByIndex(0)->getNu_max().' '.CYT_LBL_EVALUACION_C_U:CYT_LBL_EVALUACION_HASTA.' '.$oAntacadsMaximos->getObjectByIndex(0)->getNu_max();
		$entity->setNu_puntajeA2($nu_max);
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $entity->getOid(), '=');
		$oCriteria->addFilter('cd_antacadmaximo', $oAntacadsMaximos->getObjectByIndex(0)->getOid(), '=');
		$managerPuntajeAntacad =  ManagerFactory::getPuntajeAntacadManager();
		$oPuntajeAntacadA2 = $managerPuntajeAntacad->getEntity($oCriteria);
		
		if ($oPuntajeAntacadA2) {
			$entity->setNu_puntajeantacadA2($oPuntajeAntacadA2->getNu_puntaje());
		}
		
		$entity->setNu_topeA3('<strong>Max. '.$oAntacadsMaximos->getObjectByIndex(1)->getNu_tope().' '.CYT_LBL_EVALUACION_PT.'</strong>');
		
		$ds_descripcionA3 = $oAntacadsMaximos->getObjectByIndex(1)->getAntacadPlanilla()->getDs_antacadplanilla();
		$ds_descripcionA3 = str_replace('#puntaje#', '<strong>'.$oAntacadsMaximos->getObjectByIndex(1)->getNu_max().' '.CYT_LBL_EVALUACION_PUNTOS.'</strong>',$ds_descripcionA3);
		$entity->setDs_descripcionA3($ds_descripcionA3);
		
		$nu_max = ($oAntacadsMaximos->getObjectByIndex(1)->getNu_max()==$oAntacadsMaximos->getObjectByIndex(1)->getNu_min())?$oAntacadsMaximos->getObjectByIndex(1)->getNu_max().' '.CYT_LBL_EVALUACION_C_U:CYT_LBL_EVALUACION_HASTA.' '.$oAntacadsMaximos->getObjectByIndex(1)->getNu_max();
		$entity->setNu_puntajeA3($nu_max);
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $entity->getOid(), '=');
		$oCriteria->addFilter('cd_antacadmaximo', $oAntacadsMaximos->getObjectByIndex(1)->getOid(), '=');
		$managerPuntajeAntacad =  ManagerFactory::getPuntajeAntacadManager();
		$oPuntajeAntacadA3 = $managerPuntajeAntacad->getEntity($oCriteria);
		
		if ($oPuntajeAntacadA3) {
			$entity->setNu_puntajeantacadA3($oPuntajeAntacadA3->getNu_puntaje());
		}
		
		$ds_descripcionA4 = $oAntacadsMaximos->getObjectByIndex(2)->getAntacadPlanilla()->getDs_antacadplanilla();
		$entity->setDs_descripcionA4($ds_descripcionA4);
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $entity->getOid(), '=');
		$oCriteria->addFilter('cd_antacadmaximo', $oAntacadsMaximos->getObjectByIndex(2)->getOid(), '=');
		$managerPuntajeAntacad =  ManagerFactory::getPuntajeAntacadManager();
		$oPuntajeAntacadA4 = $managerPuntajeAntacad->getEntity($oCriteria);
				
		if (($oPuntajeAntacadA4)&&($oPuntajeAntacadA4->getNu_puntaje()==2)) {
			$form = $this->getForm();
			$bl_posgrado = $form->getInput("bl_posgrado");
			$bl_posgrado->setIsChecked(true);
		
			//$nu_factor = 1;
		}
		//else{
	 		$hoy = date(CYT_FECHA_CIERRE);
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
			}
			
		//}
	 		
		$entity->setNu_puntajeantacadA4('F: '.$nu_factor);
		
		$entity->setDs_descripcionA5($oAntacadsMaximos->getObjectByIndex(3)->getAntacadPlanilla()->getDs_antacadplanilla());
		
			
		$nu_max = (($oAntacadsMaximos->getObjectByIndex(3)->getNu_max()!=0)&&($oAntacadsMaximos->getObjectByIndex(3)->getNu_min()==$oAntacadsMaximos->getObjectByIndex(3)->getNu_max()))?((($oAntacadsMaximos->getObjectByIndex(3)->getNu_min()==$oAntacadsMaximos->getObjectByIndex(3)->getNu_tope())&&($oAntacadsMaximos->getObjectByIndex(3)->getNu_min()==$oAntacadsMaximos->getObjectByIndex(3)->getNu_max()))?$oAntacadsMaximos->getObjectByIndex(3)->getNu_max().' '.CYT_LBL_EVALUACION_PT:$oAntacadsMaximos->getObjectByIndex(3)->getNu_max(). ' '.CYT_LBL_EVALUACION_C_U):CYT_LBL_EVALUACION_HASTA.' '.$oAntacadsMaximos->getObjectByIndex(3)->getNu_tope();
		
		$entity->setNu_puntajeA5($nu_max);
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $entity->getOid(), '=');
		$oCriteria->addFilter('cd_antacadmaximo', $oAntacadsMaximos->getObjectByIndex(3)->getOid(), '=');
		$managerPuntajeAntacad =  ManagerFactory::getPuntajeAntacadManager();
		$oPuntajeAntacadA5 = $managerPuntajeAntacad->getEntity($oCriteria);
		
		if ($oPuntajeAntacadA5) {
			$form = $this->getForm();
			$bl_posgrado = $form->getInput("nu_puntajeantacadA5");
			if ($oPuntajeAntacadA5->getNu_puntaje()) {
				$bl_posgrado->setIsChecked(true);
			}
			$entity->setNu_puntajeantacadA5($oPuntajeAntacadA5->getNu_puntaje());
		}
		
		$entity->setNu_topeA6('<strong>Max. '.$oAntacadsMaximos->getObjectByIndex(4)->getNu_tope().' '.CYT_LBL_EVALUACION_PT.'</strong>');
		
		$ds_descripcionA6 = $oAntacadsMaximos->getObjectByIndex(4)->getAntacadPlanilla()->getDs_antacadplanilla();
		
		$entity->setDs_descripcionA6($ds_descripcionA6);
		
		$nu_max = (($oAntacadsMaximos->getObjectByIndex(4)->getNu_max()!=0)&&($oAntacadsMaximos->getObjectByIndex(4)->getNu_min()==$oAntacadsMaximos->getObjectByIndex(4)->getNu_max()))?((($oAntacadsMaximos->getObjectByIndex(4)->getNu_min()==$oAntacadsMaximos->getObjectByIndex(4)->getNu_tope())&&($oAntacadsMaximos->getObjectByIndex(4)->getNu_min()==$oAntacadsMaximos->getObjectByIndex(4)->getNu_max()))?$oAntacadsMaximos->getObjectByIndex(4)->getNu_max().' '.CYT_LBL_EVALUACION_PT:$oAntacadsMaximos->getObjectByIndex(4)->getNu_max(). ' '.CYT_LBL_EVALUACION_C_U):CYT_LBL_EVALUACION_HASTA.' '.$oAntacadsMaximos->getObjectByIndex(4)->getNu_tope();
		$entity->setNu_puntajeA6($nu_max);
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $entity->getOid(), '=');
		$oCriteria->addFilter('cd_antacadmaximo', $oAntacadsMaximos->getObjectByIndex(4)->getOid(), '=');
		$managerPuntajeAntacad =  ManagerFactory::getPuntajeAntacadManager();
		$oPuntajeAntacadA6 = $managerPuntajeAntacad->getEntity($oCriteria);
		
		if ($oPuntajeAntacadA6) {
			$entity->setNu_puntajeantacadA6($oPuntajeAntacadA6->getNu_puntaje());
		}
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_modeloplanilla', $oModeloPlanilla->getOid(), '=');
		//$oCriteria->addOrder('nu_max','DESC');
		$managerCargoMaximo =  ManagerFactory::getCargoMaximoManager();
		$oCargosMaximos = $managerCargoMaximo->getEntities($oCriteria);
		
		$entity->setNu_cargomaximo($oCargosMaximos->getObjectByIndex(0)->getNu_max());
		
		
		
		$cd_cargo = 0;
			
		switch ($oSolicitud->getCargo()->getOid()) {
			case '1':
				$cd_cargo = 1;
			break;
			case '2':
				$cd_cargo = 5;
			break;
			case '3':
				$cd_cargo = 3;
			break;
			case '4':
				$cd_cargo = 7;
			break;
			case '5':
				$cd_cargo = 9;
			break;
			case '7':
				$cd_cargo = 2;
			break;
			case '8':
				$cd_cargo = 6;
			break;
			case '9':
				$cd_cargo = 4;
			break;
			case '10':
				$cd_cargo = 8;
			break;
			case '11':
				$cd_cargo = 10;
			break;
		}
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_cargoplanilla', $cd_cargo, '=');
		$oCriteria->addFilter('cd_modeloplanilla', $oModeloPlanilla->getOid(), '=');
		$managerCargoMaximo =  ManagerFactory::getCargoMaximoManager();
		$oCargoMaximo = $managerCargoMaximo->getEntity($oCriteria);
		
		if ($oCargoMaximo) {
			$entity->setCd_cargomaximo($oCargoMaximo->getOid().'-'.$oCargoMaximo->getNu_max());
		}
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_modeloplanilla', $oModeloPlanilla->getOid(), '=');
		$oCriteria->addOrder('cd_antotrosmaximo');
		$managerAntotrosMaximo =  ManagerFactory::getAntotrosMaximoManager();
		$oAntotrossMaximos = $managerAntotrosMaximo->getEntities($oCriteria);
		
		$entity->setNu_antotrosmaximo($oAntotrossMaximos->getObjectByIndex(0)->getPuntajeGrupo()->getNu_max());
		$entity->setNu_topeC1('<strong>Max. '.$oAntotrossMaximos->getObjectByIndex(0)->getNu_tope().' '.CYT_LBL_EVALUACION_PT.'</strong>');
		
		$ds_descripcionC1 = $oAntotrossMaximos->getObjectByIndex(0)->getAntotrosPlanilla()->getDs_antotrosplanilla();
		
		$entity->setDs_descripcionC1($ds_descripcionC1);
		
		$nu_max = (($oAntotrossMaximos->getObjectByIndex(0)->getNu_max()!=0)&&($oAntotrossMaximos->getObjectByIndex(0)->getNu_min()==$oAntotrossMaximos->getObjectByIndex(0)->getNu_max()))?((($oAntotrossMaximos->getObjectByIndex(0)->getNu_min()==$oAntotrossMaximos->getObjectByIndex(0)->getNu_tope())&&($oAntotrossMaximos->getObjectByIndex(0)->getNu_min()==$oAntotrossMaximos->getObjectByIndex(0)->getNu_max()))?$oAntotrossMaximos->getObjectByIndex(0)->getNu_max().' '.CYT_LBL_EVALUACION_PT:$oAntotrossMaximos->getObjectByIndex(0)->getNu_max(). ' '.CYT_LBL_EVALUACION_C_U):CYT_LBL_EVALUACION_HASTA.' '.$oAntotrossMaximos->getObjectByIndex(0)->getNu_tope();
		$entity->setNu_puntajeC1($nu_max);
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $entity->getOid(), '=');
		$oCriteria->addFilter('cd_antotrosmaximo', $oAntotrossMaximos->getObjectByIndex(0)->getOid(), '=');
		$managerPuntajeAntotros =  ManagerFactory::getPuntajeAntotrosManager();
		$oPuntajeAntotrosC1 = $managerPuntajeAntotros->getEntity($oCriteria);
		
		if ($oPuntajeAntotrosC1) {
			$entity->setNu_puntajeantotrosC1($oPuntajeAntotrosC1->getNu_puntaje());
		}
		
		$entity->setDs_grupoC2($oAntotrossMaximos->getObjectByIndex(1)->getAntotrosPlanilla()->getSubGrupo()->getDs_subgrupo());
		
		$ds_descripcionC2_1 = $oAntotrossMaximos->getObjectByIndex(1)->getAntotrosPlanilla()->getDs_antotrosplanilla();
		
		$entity->setDs_descripcionC2_1($ds_descripcionC2_1);
		
		$nu_max = (($oAntotrossMaximos->getObjectByIndex(1)->getNu_max()!=0)&&($oAntotrossMaximos->getObjectByIndex(1)->getNu_min()==$oAntotrossMaximos->getObjectByIndex(1)->getNu_max()))?((($oAntotrossMaximos->getObjectByIndex(1)->getNu_min()==$oAntotrossMaximos->getObjectByIndex(1)->getNu_tope())&&($oAntotrossMaximos->getObjectByIndex(1)->getNu_min()==$oAntotrossMaximos->getObjectByIndex(1)->getNu_max()))?$oAntotrossMaximos->getObjectByIndex(1)->getNu_max().' '.CYT_LBL_EVALUACION_PT:$oAntotrossMaximos->getObjectByIndex(1)->getNu_max(). ' '.CYT_LBL_EVALUACION_C_U):CYT_LBL_EVALUACION_HASTA.' '.$oAntotrossMaximos->getObjectByIndex(1)->getNu_tope();
		$entity->setNu_puntajeC2_1($nu_max);
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $entity->getOid(), '=');
		$oCriteria->addFilter('cd_antotrosmaximo', $oAntotrossMaximos->getObjectByIndex(1)->getOid(), '=');
		$managerPuntajeAntotros =  ManagerFactory::getPuntajeAntotrosManager();
		$oPuntajeAntotrosC2_1 = $managerPuntajeAntotros->getEntity($oCriteria);
		
		if ($oPuntajeAntotrosC2_1) {
			$entity->setNu_puntajeantotrosC2_1($oPuntajeAntotrosC2_1->getNu_puntaje());
		}
		
		$ds_descripcionC2_2 = $oAntotrossMaximos->getObjectByIndex(2)->getAntotrosPlanilla()->getDs_antotrosplanilla();
		
		$entity->setDs_descripcionC2_2($ds_descripcionC2_2);
		
		$nu_max = (($oAntotrossMaximos->getObjectByIndex(2)->getNu_max()!=0)&&($oAntotrossMaximos->getObjectByIndex(2)->getNu_min()==$oAntotrossMaximos->getObjectByIndex(2)->getNu_max()))?((($oAntotrossMaximos->getObjectByIndex(2)->getNu_min()==$oAntotrossMaximos->getObjectByIndex(2)->getNu_tope())&&($oAntotrossMaximos->getObjectByIndex(2)->getNu_min()==$oAntotrossMaximos->getObjectByIndex(2)->getNu_max()))?$oAntotrossMaximos->getObjectByIndex(2)->getNu_max().' '.CYT_LBL_EVALUACION_PT:$oAntotrossMaximos->getObjectByIndex(2)->getNu_max(). ' '.CYT_LBL_EVALUACION_C_U):CYT_LBL_EVALUACION_HASTA.' '.$oAntotrossMaximos->getObjectByIndex(2)->getNu_tope();
		$entity->setNu_puntajeC2_2($nu_max);
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $entity->getOid(), '=');
		$oCriteria->addFilter('cd_antotrosmaximo', $oAntotrossMaximos->getObjectByIndex(2)->getOid(), '=');
		$managerPuntajeAntotros =  ManagerFactory::getPuntajeAntotrosManager();
		$oPuntajeAntotrosC2_2 = $managerPuntajeAntotros->getEntity($oCriteria);
		
		if ($oPuntajeAntotrosC2_2) {
			$entity->setNu_puntajeantotrosC2_2($oPuntajeAntotrosC2_2->getNu_puntaje());
		}
		
		$ds_descripcionC2_3 = $oAntotrossMaximos->getObjectByIndex(3)->getAntotrosPlanilla()->getDs_antotrosplanilla();
		
		$entity->setDs_descripcionC2_3($ds_descripcionC2_3);
		
		$nu_max = (($oAntotrossMaximos->getObjectByIndex(3)->getNu_max()!=0)&&($oAntotrossMaximos->getObjectByIndex(3)->getNu_min()==$oAntotrossMaximos->getObjectByIndex(3)->getNu_max()))?((($oAntotrossMaximos->getObjectByIndex(3)->getNu_min()==$oAntotrossMaximos->getObjectByIndex(3)->getNu_tope())&&($oAntotrossMaximos->getObjectByIndex(3)->getNu_min()==$oAntotrossMaximos->getObjectByIndex(3)->getNu_max()))?$oAntotrossMaximos->getObjectByIndex(3)->getNu_max().' '.CYT_LBL_EVALUACION_PT:$oAntotrossMaximos->getObjectByIndex(3)->getNu_max(). ' '.CYT_LBL_EVALUACION_C_U):CYT_LBL_EVALUACION_HASTA.' '.$oAntotrossMaximos->getObjectByIndex(3)->getNu_tope();
		$entity->setNu_puntajeC2_3($nu_max);
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $entity->getOid(), '=');
		$oCriteria->addFilter('cd_antotrosmaximo', $oAntotrossMaximos->getObjectByIndex(3)->getOid(), '=');
		$managerPuntajeAntotros =  ManagerFactory::getPuntajeAntotrosManager();
		$oPuntajeAntotrosC2_3 = $managerPuntajeAntotros->getEntity($oCriteria);
		
		if ($oPuntajeAntotrosC2_3) {
			$entity->setNu_puntajeantotrosC2_3($oPuntajeAntotrosC2_3->getNu_puntaje());
		}
		
		/*$entity->setNu_topeC3('<strong>Max. '.$oAntotrossMaximos->getObjectByIndex(4)->getNu_tope().' '.CYT_LBL_EVALUACION_PT.'</strong>');
		
		$ds_descripcionC3 = $oAntotrossMaximos->getObjectByIndex(4)->getAntotrosPlanilla()->getDs_antotrosplanilla();
		
		$entity->setDs_descripcionC3($ds_descripcionC3);
		
		$nu_max = (($oAntotrossMaximos->getObjectByIndex(4)->getNu_max()!=0)&&($oAntotrossMaximos->getObjectByIndex(4)->getNu_min()==$oAntotrossMaximos->getObjectByIndex(4)->getNu_max()))?((($oAntotrossMaximos->getObjectByIndex(4)->getNu_min()==$oAntotrossMaximos->getObjectByIndex(4)->getNu_tope())&&($oAntotrossMaximos->getObjectByIndex(4)->getNu_min()==$oAntotrossMaximos->getObjectByIndex(4)->getNu_max()))?$oAntotrossMaximos->getObjectByIndex(4)->getNu_max().' '.CYT_LBL_EVALUACION_PT:$oAntotrossMaximos->getObjectByIndex(4)->getNu_max(). ' '.CYT_LBL_EVALUACION_C_U):CYT_LBL_EVALUACION_HASTA.' '.$oAntotrossMaximos->getObjectByIndex(4)->getNu_tope();
		$entity->setNu_puntajeC3($nu_max);
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $entity->getOid(), '=');
		$oCriteria->addFilter('cd_antotrosmaximo', $oAntotrossMaximos->getObjectByIndex(4)->getOid(), '=');
		$managerPuntajeAntotros =  ManagerFactory::getPuntajeAntotrosManager();
		$oPuntajeAntotrosC3 = $managerPuntajeAntotros->getEntity($oCriteria);
		
		if ($oPuntajeAntotrosC3) {
			$entity->setNu_puntajeantotrosC3($oPuntajeAntotrosC3->getNu_puntaje());
		}*/
		
		$entity->setDs_descripcionC4($oAntotrossMaximos->getObjectByIndex(4)->getAntotrosPlanilla()->getDs_antotrosplanilla());
		
			
		$nu_max = (($oAntotrossMaximos->getObjectByIndex(4)->getNu_max()!=0)&&($oAntotrossMaximos->getObjectByIndex(4)->getNu_min()==$oAntotrossMaximos->getObjectByIndex(4)->getNu_max()))?((($oAntotrossMaximos->getObjectByIndex(4)->getNu_min()==$oAntotrossMaximos->getObjectByIndex(4)->getNu_tope())&&($oAntotrossMaximos->getObjectByIndex(4)->getNu_min()==$oAntotrossMaximos->getObjectByIndex(4)->getNu_max()))?$oAntotrossMaximos->getObjectByIndex(4)->getNu_max().' '.CYT_LBL_EVALUACION_PT:$oAntotrossMaximos->getObjectByIndex(4)->getNu_max(). ' '.CYT_LBL_EVALUACION_C_U):CYT_LBL_EVALUACION_HASTA.' '.$oAntotrossMaximos->getObjectByIndex(4)->getNu_tope();
		
		$entity->setNu_puntajeC4($nu_max);
		
		$oCriteria = new CdtSearchCriteria();
		$tDocente = CYTSecureDAOFactory::getDocenteDAO()->getTableName();
		$oCriteria->addFilter("$tDocente.cd_docente", $oSolicitud->getDocente()->getOid(), '=');
		
		$oPeriodoManager = CYTSecureManagerFactory::getPeriodoManager();
		$oPerioActual = $oPeriodoManager->getPeriodoActual(CYT_PERIODO_YEAR);
		$tSolicitudEstado = CYTSecureDAOFactory::getSolicitudEstadoDAO()->getTableName();
		//$oCriteria->addFilter("$tSolicitudEstado.estado_oid", CYT_ESTADO_SOLICITUD_OTORGADA, '=');
		$filter = new CdtSimpleExpression( "(".$tSolicitudEstado.".estado_oid =".CYT_ESTADO_SOLICITUD_OTORGADA." OR ".$tSolicitudEstado.".estado_oid =".CYT_ESTADO_SOLICITUD_RENDIDA." OR ".$tSolicitudEstado.".estado_oid =".CYT_ESTADO_SOLICITUD_RENUNCIADA.")");
		$oCriteria->setExpresion($filter);
		$oCriteria->addNull('fechaHasta');
		$tSolicitud = DAOFactory::getSolicitudDAO()->getTableName();
		$periodoAnt = $oPerioActual->getOid()-1;
		$oCriteria->addFilter("$tSolicitud.cd_periodo", $periodoAnt, '=');
		
		$oSolicitudManager =  ManagerFactory::getSolicitudManager();
		$oSolicitudAnterior = $oSolicitudManager->getEntity($oCriteria);
		
		if (!$oSolicitudAnterior) {
			$entity->setNu_puntajeantotrosC4($oAntotrossMaximos->getObjectByIndex(4)->getNu_max());
			$form = $this->getForm();
			$bl_posgrado = $form->getInput("nu_puntajeantotrosC4");
			$bl_posgrado->setIsChecked(true);
			
		}
		
		
		
		
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_modeloplanilla', $oModeloPlanilla->getOid(), '=');
		$oCriteria->addOrder('cd_antproduccionmaximo');
		$managerAntproduccionMaximo =  ManagerFactory::getAntproduccionMaximoManager();
		$oAntproduccionsMaximos = $managerAntproduccionMaximo->getEntities($oCriteria);
		
		$entity->setNu_antproduccionmaximo($oAntproduccionsMaximos->getObjectByIndex(0)->getPuntajeGrupo()->getNu_max());
		
		/*$entity->setDs_grupoD1($oAntproduccionsMaximos->getObjectByIndex(0)->getAntproduccionPlanilla()->getSubGrupo()->getDs_subgrupo());
		
		$entity->setNu_topeD1_1('<strong>Max. '.$oAntproduccionsMaximos->getObjectByIndex(0)->getNu_tope().' '.CYT_LBL_EVALUACION_PT.'</strong>');
		$ds_descripcionD1_1 = str_replace('#puntaje#', '<strong>'.$oAntproduccionsMaximos->getObjectByIndex(0)->getNu_max().' '.CYT_LBL_EVALUACION_PUNTOS.'</strong>',$oAntproduccionsMaximos->getObjectByIndex(0)->getAntproduccionPlanilla()->getDs_antproduccionplanilla());
		
		
		$entity->setDs_descripcionD1_1($ds_descripcionD1_1);
		
		$nu_max = (($oAntproduccionsMaximos->getObjectByIndex(0)->getNu_max()!=0)&&($oAntproduccionsMaximos->getObjectByIndex(0)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(0)->getNu_max()))?((($oAntproduccionsMaximos->getObjectByIndex(0)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(0)->getNu_tope())&&($oAntproduccionsMaximos->getObjectByIndex(0)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(0)->getNu_max()))?$oAntproduccionsMaximos->getObjectByIndex(0)->getNu_max().' '.CYT_LBL_EVALUACION_PT:$oAntproduccionsMaximos->getObjectByIndex(0)->getNu_max(). ' '.CYT_LBL_EVALUACION_C_10):CYT_LBL_EVALUACION_HASTA.' '.$oAntproduccionsMaximos->getObjectByIndex(0)->getNu_tope();
		$entity->setNu_puntajeD1_1($nu_max);

		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $entity->getOid(), '=');
		$oCriteria->addFilter('cd_antproduccionmaximo', $oAntproduccionsMaximos->getObjectByIndex(0)->getOid(), '=');
		$managerPuntajeAntproduccion =  ManagerFactory::getPuntajeAntproduccionManager();
		$oPuntajeAntproduccionD1_1 = $managerPuntajeAntproduccion->getEntity($oCriteria);
		
		if ($oPuntajeAntproduccionD1_1) {
			$entity->setNu_puntajeantproduccionD1_1($oPuntajeAntproduccionD1_1->getNu_puntaje());
		}
		
		$entity->setNu_topeD1_2('<strong>Max. '.$oAntproduccionsMaximos->getObjectByIndex(1)->getNu_tope().' '.CYT_LBL_EVALUACION_PT.'</strong>');
		$ds_descripcionD1_2 = str_replace('#puntaje#', '<strong>'.$oAntproduccionsMaximos->getObjectByIndex(1)->getNu_max().' '.CYT_LBL_EVALUACION_PUNTOS.'</strong>',$oAntproduccionsMaximos->getObjectByIndex(1)->getAntproduccionPlanilla()->getDs_antproduccionplanilla());
		
		
		$entity->setDs_descripcionD1_2($ds_descripcionD1_2);
		
		$nu_max = (($oAntproduccionsMaximos->getObjectByIndex(1)->getNu_max()!=0)&&($oAntproduccionsMaximos->getObjectByIndex(1)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(1)->getNu_max()))?((($oAntproduccionsMaximos->getObjectByIndex(1)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(1)->getNu_tope())&&($oAntproduccionsMaximos->getObjectByIndex(1)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(1)->getNu_max()))?$oAntproduccionsMaximos->getObjectByIndex(1)->getNu_max().' '.CYT_LBL_EVALUACION_PT:$oAntproduccionsMaximos->getObjectByIndex(1)->getNu_max(). ' '.CYT_LBL_EVALUACION_C_U):CYT_LBL_EVALUACION_HASTA.' '.$oAntproduccionsMaximos->getObjectByIndex(1)->getNu_tope();
		$entity->setNu_puntajeD1_2($nu_max);

		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $entity->getOid(), '=');
		$oCriteria->addFilter('cd_antproduccionmaximo', $oAntproduccionsMaximos->getObjectByIndex(1)->getOid(), '=');
		$managerPuntajeAntproduccion =  ManagerFactory::getPuntajeAntproduccionManager();
		$oPuntajeAntproduccionD1_2 = $managerPuntajeAntproduccion->getEntity($oCriteria);
		
		if ($oPuntajeAntproduccionD1_2) {
			$entity->setNu_puntajeantproduccionD1_2($oPuntajeAntproduccionD1_2->getNu_puntaje());
		}*/
		
		$entity->setDs_grupoD6($oAntproduccionsMaximos->getObjectByIndex(0)->getAntproduccionPlanilla()->getSubGrupo()->getDs_subgrupo());
		
		$ds_descripcionD6_1 = str_replace('#puntaje#', '<strong>'.$oAntproduccionsMaximos->getObjectByIndex(0)->getNu_max().' '.CYT_LBL_EVALUACION_PUNTOS.'</strong>',$oAntproduccionsMaximos->getObjectByIndex(0)->getAntproduccionPlanilla()->getDs_antproduccionplanilla());
		
		
		$entity->setDs_descripcionD6_1($ds_descripcionD6_1);
		
		$nu_max = (($oAntproduccionsMaximos->getObjectByIndex(0)->getNu_max()!=0)&&($oAntproduccionsMaximos->getObjectByIndex(0)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(0)->getNu_max()))?((($oAntproduccionsMaximos->getObjectByIndex(0)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(0)->getNu_tope())&&($oAntproduccionsMaximos->getObjectByIndex(0)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(0)->getNu_max()))?$oAntproduccionsMaximos->getObjectByIndex(0)->getNu_max().' '.CYT_LBL_EVALUACION_PT:$oAntproduccionsMaximos->getObjectByIndex(0)->getNu_max(). ' '.CYT_LBL_EVALUACION_C_U):CYT_LBL_EVALUACION_HASTA.' '.$oAntproduccionsMaximos->getObjectByIndex(0)->getNu_tope();
		$entity->setNu_puntajeD6_1($nu_max);

		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $entity->getOid(), '=');
		$oCriteria->addFilter('cd_antproduccionmaximo', $oAntproduccionsMaximos->getObjectByIndex(0)->getOid(), '=');
		$managerPuntajeAntproduccion =  ManagerFactory::getPuntajeAntproduccionManager();
		$oPuntajeAntproduccionD6_1 = $managerPuntajeAntproduccion->getEntity($oCriteria);
		
		if ($oPuntajeAntproduccionD6_1) {
			$entity->setNu_puntajeantproduccionD6_1($oPuntajeAntproduccionD6_1->getNu_puntaje());
		}
		
		$ds_descripcionD6_2 = str_replace('#puntaje#', '<strong>'.$oAntproduccionsMaximos->getObjectByIndex(1)->getNu_max().' '.CYT_LBL_EVALUACION_PUNTOS.'</strong>',$oAntproduccionsMaximos->getObjectByIndex(1)->getAntproduccionPlanilla()->getDs_antproduccionplanilla());
		
		
		$entity->setDs_descripcionD6_2($ds_descripcionD6_2);
		
		$nu_max = (($oAntproduccionsMaximos->getObjectByIndex(1)->getNu_max()!=0)&&($oAntproduccionsMaximos->getObjectByIndex(1)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(1)->getNu_max()))?((($oAntproduccionsMaximos->getObjectByIndex(1)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(1)->getNu_tope())&&($oAntproduccionsMaximos->getObjectByIndex(1)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(1)->getNu_max()))?$oAntproduccionsMaximos->getObjectByIndex(1)->getNu_max().' '.CYT_LBL_EVALUACION_PT:$oAntproduccionsMaximos->getObjectByIndex(1)->getNu_max(). ' '.CYT_LBL_EVALUACION_C_U):CYT_LBL_EVALUACION_HASTA.' '.$oAntproduccionsMaximos->getObjectByIndex(1)->getNu_tope();
		$entity->setNu_puntajeD6_2($nu_max);

		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $entity->getOid(), '=');
		$oCriteria->addFilter('cd_antproduccionmaximo', $oAntproduccionsMaximos->getObjectByIndex(1)->getOid(), '=');
		$managerPuntajeAntproduccion =  ManagerFactory::getPuntajeAntproduccionManager();
		$oPuntajeAntproduccionD6_2 = $managerPuntajeAntproduccion->getEntity($oCriteria);
		
		if ($oPuntajeAntproduccionD6_2) {
			$entity->setNu_puntajeantproduccionD6_2($oPuntajeAntproduccionD6_2->getNu_puntaje());
		}
		
		$ds_descripcionD6_3 = str_replace('#puntaje#', '<strong>'.$oAntproduccionsMaximos->getObjectByIndex(2)->getNu_max().' '.CYT_LBL_EVALUACION_PUNTOS.'</strong>',$oAntproduccionsMaximos->getObjectByIndex(2)->getAntproduccionPlanilla()->getDs_antproduccionplanilla());
		
		
		$entity->setDs_descripcionD6_3($ds_descripcionD6_3);
		
		$nu_max = (($oAntproduccionsMaximos->getObjectByIndex(2)->getNu_max()!=0)&&($oAntproduccionsMaximos->getObjectByIndex(2)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(2)->getNu_max()))?((($oAntproduccionsMaximos->getObjectByIndex(2)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(2)->getNu_tope())&&($oAntproduccionsMaximos->getObjectByIndex(2)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(2)->getNu_max()))?$oAntproduccionsMaximos->getObjectByIndex(2)->getNu_max().' '.CYT_LBL_EVALUACION_PT:$oAntproduccionsMaximos->getObjectByIndex(2)->getNu_max(). ' '.CYT_LBL_EVALUACION_C_U):CYT_LBL_EVALUACION_HASTA.' '.$oAntproduccionsMaximos->getObjectByIndex(2)->getNu_tope();
		$entity->setNu_puntajeD6_3($nu_max);

		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $entity->getOid(), '=');
		$oCriteria->addFilter('cd_antproduccionmaximo', $oAntproduccionsMaximos->getObjectByIndex(2)->getOid(), '=');
		$managerPuntajeAntproduccion =  ManagerFactory::getPuntajeAntproduccionManager();
		$oPuntajeAntproduccionD6_3 = $managerPuntajeAntproduccion->getEntity($oCriteria);
		
		if ($oPuntajeAntproduccionD6_3) {
			$entity->setNu_puntajeantproduccionD6_3($oPuntajeAntproduccionD6_3->getNu_puntaje());
		}
		
		$ds_descripcionD6_4 = str_replace('#puntaje#', '<strong>'.$oAntproduccionsMaximos->getObjectByIndex(3)->getNu_max().' '.CYT_LBL_EVALUACION_PUNTOS.'</strong>',$oAntproduccionsMaximos->getObjectByIndex(3)->getAntproduccionPlanilla()->getDs_antproduccionplanilla());
		
		
		$entity->setDs_descripcionD6_4($ds_descripcionD6_4);
		
		$nu_max = (($oAntproduccionsMaximos->getObjectByIndex(3)->getNu_max()!=0)&&($oAntproduccionsMaximos->getObjectByIndex(3)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(3)->getNu_max()))?((($oAntproduccionsMaximos->getObjectByIndex(3)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(3)->getNu_tope())&&($oAntproduccionsMaximos->getObjectByIndex(3)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(3)->getNu_max()))?$oAntproduccionsMaximos->getObjectByIndex(3)->getNu_max().' '.CYT_LBL_EVALUACION_PT:$oAntproduccionsMaximos->getObjectByIndex(3)->getNu_max(). ' '.CYT_LBL_EVALUACION_C_U):CYT_LBL_EVALUACION_HASTA.' '.$oAntproduccionsMaximos->getObjectByIndex(3)->getNu_tope();
		$entity->setNu_puntajeD6_4($nu_max);

		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $entity->getOid(), '=');
		$oCriteria->addFilter('cd_antproduccionmaximo', $oAntproduccionsMaximos->getObjectByIndex(3)->getOid(), '=');
		$managerPuntajeAntproduccion =  ManagerFactory::getPuntajeAntproduccionManager();
		$oPuntajeAntproduccionD6_4 = $managerPuntajeAntproduccion->getEntity($oCriteria);
		
		if ($oPuntajeAntproduccionD6_4) {
			$entity->setNu_puntajeantproduccionD6_4($oPuntajeAntproduccionD6_4->getNu_puntaje());
		}
		
		
		$entity->setDs_grupoD2($oAntproduccionsMaximos->getObjectByIndex(4)->getAntproduccionPlanilla()->getSubGrupo()->getDs_subgrupo());
		
		/*$entity->setNu_topeD2_1('<strong>Max. '.$oAntproduccionsMaximos->getObjectByIndex(4)->getNu_tope().' '.CYT_LBL_EVALUACION_PT.'</strong>');
		$ds_descripcionD2_1 = str_replace('#puntaje#', '<strong>'.$oAntproduccionsMaximos->getObjectByIndex(4)->getNu_max().' '.CYT_LBL_EVALUACION_PUNTOS.'</strong>',$oAntproduccionsMaximos->getObjectByIndex(4)->getAntproduccionPlanilla()->getDs_antproduccionplanilla());
		
		
		$entity->setDs_descripcionD2_1($ds_descripcionD2_1);
		
		$nu_max = (($oAntproduccionsMaximos->getObjectByIndex(4)->getNu_max()!=0)&&($oAntproduccionsMaximos->getObjectByIndex(4)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(4)->getNu_max()))?((($oAntproduccionsMaximos->getObjectByIndex(4)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(4)->getNu_tope())&&($oAntproduccionsMaximos->getObjectByIndex(4)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(4)->getNu_max()))?$oAntproduccionsMaximos->getObjectByIndex(4)->getNu_max().' '.CYT_LBL_EVALUACION_PT:$oAntproduccionsMaximos->getObjectByIndex(4)->getNu_max(). ' '.CYT_LBL_EVALUACION_C_U):CYT_LBL_EVALUACION_HASTA.' '.$oAntproduccionsMaximos->getObjectByIndex(4)->getNu_tope();
		$entity->setNu_puntajeD2_1($nu_max);

		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $entity->getOid(), '=');
		$oCriteria->addFilter('cd_antproduccionmaximo', $oAntproduccionsMaximos->getObjectByIndex(4)->getOid(), '=');
		$managerPuntajeAntproduccion =  ManagerFactory::getPuntajeAntproduccionManager();
		$oPuntajeAntproduccionD2_1 = $managerPuntajeAntproduccion->getEntity($oCriteria);
		
		if ($oPuntajeAntproduccionD2_1) {
			$entity->setNu_puntajeantproduccionD2_1($oPuntajeAntproduccionD2_1->getNu_puntaje());
		}*/
		
		$entity->setNu_topeD2_2('<strong>Max. '.$oAntproduccionsMaximos->getObjectByIndex(4)->getNu_tope().' '.CYT_LBL_EVALUACION_PT.'</strong>');
		$ds_descripcionD2_2 = str_replace('#puntaje#', '<strong>'.$oAntproduccionsMaximos->getObjectByIndex(4)->getNu_max().' '.CYT_LBL_EVALUACION_PUNTOS.'</strong>',$oAntproduccionsMaximos->getObjectByIndex(4)->getAntproduccionPlanilla()->getDs_antproduccionplanilla());
		
		
		$entity->setDs_descripcionD2_2($ds_descripcionD2_2);
		
		$nu_max = (($oAntproduccionsMaximos->getObjectByIndex(4)->getNu_max()!=0)&&($oAntproduccionsMaximos->getObjectByIndex(4)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(4)->getNu_max()))?((($oAntproduccionsMaximos->getObjectByIndex(4)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(4)->getNu_tope())&&($oAntproduccionsMaximos->getObjectByIndex(4)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(4)->getNu_max()))?$oAntproduccionsMaximos->getObjectByIndex(4)->getNu_max().' '.CYT_LBL_EVALUACION_PT:$oAntproduccionsMaximos->getObjectByIndex(4)->getNu_max(). ' '.CYT_LBL_EVALUACION_C_U):CYT_LBL_EVALUACION_HASTA.' '.$oAntproduccionsMaximos->getObjectByIndex(4)->getNu_tope();
		$entity->setNu_puntajeD2_2($nu_max);

		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $entity->getOid(), '=');
		$oCriteria->addFilter('cd_antproduccionmaximo', $oAntproduccionsMaximos->getObjectByIndex(4)->getOid(), '=');
		$managerPuntajeAntproduccion =  ManagerFactory::getPuntajeAntproduccionManager();
		$oPuntajeAntproduccionD2_2 = $managerPuntajeAntproduccion->getEntity($oCriteria);
		
		if ($oPuntajeAntproduccionD2_2) {
			$entity->setNu_puntajeantproduccionD2_2($oPuntajeAntproduccionD2_2->getNu_puntaje());
		}
		
		$entity->setNu_topeD2_3('<strong>Max. '.$oAntproduccionsMaximos->getObjectByIndex(5)->getNu_tope().' '.CYT_LBL_EVALUACION_PT.'</strong>');
		$ds_descripcionD2_3 = str_replace('#puntaje#', '<strong>'.$oAntproduccionsMaximos->getObjectByIndex(5)->getNu_max().' '.CYT_LBL_EVALUACION_PUNTOS.'</strong>',$oAntproduccionsMaximos->getObjectByIndex(5)->getAntproduccionPlanilla()->getDs_antproduccionplanilla());
		
		
		$entity->setDs_descripcionD2_3($ds_descripcionD2_3);
		
		$nu_max = (($oAntproduccionsMaximos->getObjectByIndex(5)->getNu_max()!=0)&&($oAntproduccionsMaximos->getObjectByIndex(5)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(5)->getNu_max()))?((($oAntproduccionsMaximos->getObjectByIndex(5)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(5)->getNu_tope())&&($oAntproduccionsMaximos->getObjectByIndex(5)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(5)->getNu_max()))?$oAntproduccionsMaximos->getObjectByIndex(5)->getNu_max().' '.CYT_LBL_EVALUACION_PT:$oAntproduccionsMaximos->getObjectByIndex(5)->getNu_max(). ' '.CYT_LBL_EVALUACION_C_U):CYT_LBL_EVALUACION_HASTA.' '.$oAntproduccionsMaximos->getObjectByIndex(5)->getNu_tope();
		$entity->setNu_puntajeD2_3($nu_max);

		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $entity->getOid(), '=');
		$oCriteria->addFilter('cd_antproduccionmaximo', $oAntproduccionsMaximos->getObjectByIndex(5)->getOid(), '=');
		$managerPuntajeAntproduccion =  ManagerFactory::getPuntajeAntproduccionManager();
		$oPuntajeAntproduccionD2_3 = $managerPuntajeAntproduccion->getEntity($oCriteria);
		
		if ($oPuntajeAntproduccionD2_3) {
			$entity->setNu_puntajeantproduccionD2_3($oPuntajeAntproduccionD2_3->getNu_puntaje());
		}
		
		$entity->setNu_topeD2_4('<strong>Max. '.$oAntproduccionsMaximos->getObjectByIndex(6)->getNu_tope().' '.CYT_LBL_EVALUACION_PT.'</strong>');
		$ds_descripcionD2_4 = str_replace('#puntaje#', '<strong>'.$oAntproduccionsMaximos->getObjectByIndex(6)->getNu_max().' '.CYT_LBL_EVALUACION_PUNTOS.'</strong>',$oAntproduccionsMaximos->getObjectByIndex(6)->getAntproduccionPlanilla()->getDs_antproduccionplanilla());
		
		
		$entity->setDs_descripcionD2_4($ds_descripcionD2_4);
		
		$nu_max = (($oAntproduccionsMaximos->getObjectByIndex(6)->getNu_max()!=0)&&($oAntproduccionsMaximos->getObjectByIndex(6)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(6)->getNu_max()))?((($oAntproduccionsMaximos->getObjectByIndex(6)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(6)->getNu_tope())&&($oAntproduccionsMaximos->getObjectByIndex(6)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(6)->getNu_max()))?$oAntproduccionsMaximos->getObjectByIndex(6)->getNu_max().' '.CYT_LBL_EVALUACION_PT:$oAntproduccionsMaximos->getObjectByIndex(6)->getNu_max(). ' '.CYT_LBL_EVALUACION_C_U):CYT_LBL_EVALUACION_HASTA.' '.$oAntproduccionsMaximos->getObjectByIndex(6)->getNu_tope();
		$entity->setNu_puntajeD2_4($nu_max);

		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $entity->getOid(), '=');
		$oCriteria->addFilter('cd_antproduccionmaximo', $oAntproduccionsMaximos->getObjectByIndex(6)->getOid(), '=');
		$managerPuntajeAntproduccion =  ManagerFactory::getPuntajeAntproduccionManager();
		$oPuntajeAntproduccionD2_4 = $managerPuntajeAntproduccion->getEntity($oCriteria);
		
		if ($oPuntajeAntproduccionD2_4) {
			$entity->setNu_puntajeantproduccionD2_4($oPuntajeAntproduccionD2_4->getNu_puntaje());
		}
		
		$entity->setNu_topeD2_5('<strong>Max. '.$oAntproduccionsMaximos->getObjectByIndex(7)->getNu_tope().' '.CYT_LBL_EVALUACION_PT.'</strong>');
		$ds_descripcionD2_5 = str_replace('#puntaje#', '<strong>'.$oAntproduccionsMaximos->getObjectByIndex(7)->getNu_max().' '.CYT_LBL_EVALUACION_PUNTOS.'</strong>',$oAntproduccionsMaximos->getObjectByIndex(7)->getAntproduccionPlanilla()->getDs_antproduccionplanilla());
		
		
		$entity->setDs_descripcionD2_5($ds_descripcionD2_5);
		
		$nu_max = (($oAntproduccionsMaximos->getObjectByIndex(7)->getNu_max()!=0)&&($oAntproduccionsMaximos->getObjectByIndex(7)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(7)->getNu_max()))?((($oAntproduccionsMaximos->getObjectByIndex(7)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(7)->getNu_tope())&&($oAntproduccionsMaximos->getObjectByIndex(7)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(7)->getNu_max()))?$oAntproduccionsMaximos->getObjectByIndex(7)->getNu_max().' '.CYT_LBL_EVALUACION_PT:$oAntproduccionsMaximos->getObjectByIndex(7)->getNu_max(). ' '.CYT_LBL_EVALUACION_C_U):CYT_LBL_EVALUACION_HASTA.' '.$oAntproduccionsMaximos->getObjectByIndex(7)->getNu_tope();
		$entity->setNu_puntajeD2_5($nu_max);

		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $entity->getOid(), '=');
		$oCriteria->addFilter('cd_antproduccionmaximo', $oAntproduccionsMaximos->getObjectByIndex(7)->getOid(), '=');
		$managerPuntajeAntproduccion =  ManagerFactory::getPuntajeAntproduccionManager();
		$oPuntajeAntproduccionD2_5 = $managerPuntajeAntproduccion->getEntity($oCriteria);
		
		if ($oPuntajeAntproduccionD2_5) {
			$entity->setNu_puntajeantproduccionD2_5($oPuntajeAntproduccionD2_5->getNu_puntaje());
		}
		
		$entity->setDs_grupoD3($oAntproduccionsMaximos->getObjectByIndex(8)->getAntproduccionPlanilla()->getSubGrupo()->getDs_subgrupo());
		
		$ds_descripcionD3_1 = str_replace('#puntaje#', '<strong>'.$oAntproduccionsMaximos->getObjectByIndex(8)->getNu_max().' '.CYT_LBL_EVALUACION_PUNTOS.'</strong>',$oAntproduccionsMaximos->getObjectByIndex(8)->getAntproduccionPlanilla()->getDs_antproduccionplanilla());
		
		
		$entity->setDs_descripcionD3_1($ds_descripcionD3_1);
		
		$nu_max = (($oAntproduccionsMaximos->getObjectByIndex(8)->getNu_max()!=0)&&($oAntproduccionsMaximos->getObjectByIndex(8)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(8)->getNu_max()))?((($oAntproduccionsMaximos->getObjectByIndex(8)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(8)->getNu_tope())&&($oAntproduccionsMaximos->getObjectByIndex(8)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(8)->getNu_max()))?$oAntproduccionsMaximos->getObjectByIndex(8)->getNu_max().' '.CYT_LBL_EVALUACION_PT:$oAntproduccionsMaximos->getObjectByIndex(8)->getNu_max(). ' '.CYT_LBL_EVALUACION_C_U):CYT_LBL_EVALUACION_HASTA.' '.$oAntproduccionsMaximos->getObjectByIndex(8)->getNu_tope();
		$entity->setNu_puntajeD3_1($nu_max);

		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $entity->getOid(), '=');
		$oCriteria->addFilter('cd_antproduccionmaximo', $oAntproduccionsMaximos->getObjectByIndex(8)->getOid(), '=');
		$managerPuntajeAntproduccion =  ManagerFactory::getPuntajeAntproduccionManager();
		$oPuntajeAntproduccionD3_1 = $managerPuntajeAntproduccion->getEntity($oCriteria);
		
		if ($oPuntajeAntproduccionD3_1) {
			$entity->setNu_puntajeantproduccionD3_1($oPuntajeAntproduccionD3_1->getNu_puntaje());
		}
		
		$ds_descripcionD3_2 = str_replace('#puntaje#', '<strong>'.$oAntproduccionsMaximos->getObjectByIndex(9)->getNu_max().' '.CYT_LBL_EVALUACION_PUNTOS.'</strong>',$oAntproduccionsMaximos->getObjectByIndex(9)->getAntproduccionPlanilla()->getDs_antproduccionplanilla());
		
		
		$entity->setDs_descripcionD3_2($ds_descripcionD3_2);
		
		$nu_max = (($oAntproduccionsMaximos->getObjectByIndex(9)->getNu_max()!=0)&&($oAntproduccionsMaximos->getObjectByIndex(9)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(9)->getNu_max()))?((($oAntproduccionsMaximos->getObjectByIndex(9)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(9)->getNu_tope())&&($oAntproduccionsMaximos->getObjectByIndex(9)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(9)->getNu_max()))?$oAntproduccionsMaximos->getObjectByIndex(9)->getNu_max().' '.CYT_LBL_EVALUACION_PT:$oAntproduccionsMaximos->getObjectByIndex(9)->getNu_max(). ' '.CYT_LBL_EVALUACION_C_U):CYT_LBL_EVALUACION_HASTA.' '.$oAntproduccionsMaximos->getObjectByIndex(9)->getNu_tope();
		$entity->setNu_puntajeD3_2($nu_max);

		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $entity->getOid(), '=');
		$oCriteria->addFilter('cd_antproduccionmaximo', $oAntproduccionsMaximos->getObjectByIndex(9)->getOid(), '=');
		$managerPuntajeAntproduccion =  ManagerFactory::getPuntajeAntproduccionManager();
		$oPuntajeAntproduccionD3_2 = $managerPuntajeAntproduccion->getEntity($oCriteria);
		
		if ($oPuntajeAntproduccionD3_2) {
			$entity->setNu_puntajeantproduccionD3_2($oPuntajeAntproduccionD3_2->getNu_puntaje());
		}
		
		$ds_descripcionD3_3 = str_replace('#puntaje#', '<strong>'.$oAntproduccionsMaximos->getObjectByIndex(10)->getNu_max().' '.CYT_LBL_EVALUACION_PUNTOS.'</strong>',$oAntproduccionsMaximos->getObjectByIndex(10)->getAntproduccionPlanilla()->getDs_antproduccionplanilla());
		
		
		$entity->setDs_descripcionD3_3($ds_descripcionD3_3);
		
		$nu_max = (($oAntproduccionsMaximos->getObjectByIndex(10)->getNu_max()!=0)&&($oAntproduccionsMaximos->getObjectByIndex(10)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(10)->getNu_max()))?((($oAntproduccionsMaximos->getObjectByIndex(10)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(10)->getNu_tope())&&($oAntproduccionsMaximos->getObjectByIndex(10)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(10)->getNu_max()))?$oAntproduccionsMaximos->getObjectByIndex(10)->getNu_max().' '.CYT_LBL_EVALUACION_PT:$oAntproduccionsMaximos->getObjectByIndex(10)->getNu_max(). ' '.CYT_LBL_EVALUACION_C_U):CYT_LBL_EVALUACION_HASTA.' '.$oAntproduccionsMaximos->getObjectByIndex(10)->getNu_tope();
		$entity->setNu_puntajeD3_3($nu_max);

		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $entity->getOid(), '=');
		$oCriteria->addFilter('cd_antproduccionmaximo', $oAntproduccionsMaximos->getObjectByIndex(10)->getOid(), '=');
		$managerPuntajeAntproduccion =  ManagerFactory::getPuntajeAntproduccionManager();
		$oPuntajeAntproduccionD3_3 = $managerPuntajeAntproduccion->getEntity($oCriteria);
		
		if ($oPuntajeAntproduccionD3_3) {
			$entity->setNu_puntajeantproduccionD3_3($oPuntajeAntproduccionD3_3->getNu_puntaje());
		}
		
		$ds_descripcionD3_4 = str_replace('#puntaje#', '<strong>'.$oAntproduccionsMaximos->getObjectByIndex(11)->getNu_max().' '.CYT_LBL_EVALUACION_PUNTOS.'</strong>',$oAntproduccionsMaximos->getObjectByIndex(11)->getAntproduccionPlanilla()->getDs_antproduccionplanilla());
		
		
		$entity->setDs_descripcionD3_4($ds_descripcionD3_4);
		
		$nu_max = (($oAntproduccionsMaximos->getObjectByIndex(11)->getNu_max()!=0)&&($oAntproduccionsMaximos->getObjectByIndex(11)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(11)->getNu_max()))?((($oAntproduccionsMaximos->getObjectByIndex(11)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(11)->getNu_tope())&&($oAntproduccionsMaximos->getObjectByIndex(11)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(11)->getNu_max()))?$oAntproduccionsMaximos->getObjectByIndex(11)->getNu_max().' '.CYT_LBL_EVALUACION_PT:$oAntproduccionsMaximos->getObjectByIndex(11)->getNu_max(). ' '.CYT_LBL_EVALUACION_C_U):CYT_LBL_EVALUACION_HASTA.' '.$oAntproduccionsMaximos->getObjectByIndex(11)->getNu_tope();
		$entity->setNu_puntajeD3_4($nu_max);

		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $entity->getOid(), '=');
		$oCriteria->addFilter('cd_antproduccionmaximo', $oAntproduccionsMaximos->getObjectByIndex(11)->getOid(), '=');
		$managerPuntajeAntproduccion =  ManagerFactory::getPuntajeAntproduccionManager();
		$oPuntajeAntproduccionD3_4 = $managerPuntajeAntproduccion->getEntity($oCriteria);
		
		if ($oPuntajeAntproduccionD3_4) {
			$entity->setNu_puntajeantproduccionD3_4($oPuntajeAntproduccionD3_4->getNu_puntaje());
		}
		
		/*$ds_descripcionD3_5 = str_replace('#puntaje#', '<strong>'.$oAntproduccionsMaximos->getObjectByIndex(12)->getNu_max().' '.CYT_LBL_EVALUACION_PUNTOS.'</strong>',$oAntproduccionsMaximos->getObjectByIndex(12)->getAntproduccionPlanilla()->getDs_antproduccionplanilla());
		
		
		$entity->setDs_descripcionD3_5($ds_descripcionD3_5);
		
		$nu_max = (($oAntproduccionsMaximos->getObjectByIndex(12)->getNu_max()!=0)&&($oAntproduccionsMaximos->getObjectByIndex(12)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(12)->getNu_max()))?((($oAntproduccionsMaximos->getObjectByIndex(12)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(12)->getNu_tope())&&($oAntproduccionsMaximos->getObjectByIndex(12)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(12)->getNu_max()))?$oAntproduccionsMaximos->getObjectByIndex(12)->getNu_max().' '.CYT_LBL_EVALUACION_PT:$oAntproduccionsMaximos->getObjectByIndex(12)->getNu_max(). ' '.CYT_LBL_EVALUACION_C_U):CYT_LBL_EVALUACION_HASTA.' '.$oAntproduccionsMaximos->getObjectByIndex(12)->getNu_tope();
		$entity->setNu_puntajeD3_5($nu_max);

		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $entity->getOid(), '=');
		$oCriteria->addFilter('cd_antproduccionmaximo', $oAntproduccionsMaximos->getObjectByIndex(12)->getOid(), '=');
		$managerPuntajeAntproduccion =  ManagerFactory::getPuntajeAntproduccionManager();
		$oPuntajeAntproduccionD3_5 = $managerPuntajeAntproduccion->getEntity($oCriteria);
		
		if ($oPuntajeAntproduccionD3_5) {
			$entity->setNu_puntajeantproduccionD3_5($oPuntajeAntproduccionD3_5->getNu_puntaje());
		}*/
		
		$entity->setDs_grupoD4($oAntproduccionsMaximos->getObjectByIndex(12)->getAntproduccionPlanilla()->getSubGrupo()->getDs_subgrupo());
		
		$ds_descripcionD4_1 = str_replace('#puntaje#', '<strong>'.$oAntproduccionsMaximos->getObjectByIndex(12)->getNu_max().' '.CYT_LBL_EVALUACION_PUNTOS.'</strong>',$oAntproduccionsMaximos->getObjectByIndex(12)->getAntproduccionPlanilla()->getDs_antproduccionplanilla());
		
		
		$entity->setDs_descripcionD4_1($ds_descripcionD4_1);
		
		$nu_max = (($oAntproduccionsMaximos->getObjectByIndex(12)->getNu_max()!=0)&&($oAntproduccionsMaximos->getObjectByIndex(12)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(12)->getNu_max()))?((($oAntproduccionsMaximos->getObjectByIndex(12)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(12)->getNu_tope())&&($oAntproduccionsMaximos->getObjectByIndex(12)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(12)->getNu_max()))?$oAntproduccionsMaximos->getObjectByIndex(12)->getNu_max().' '.CYT_LBL_EVALUACION_PT:$oAntproduccionsMaximos->getObjectByIndex(12)->getNu_max(). ' '.CYT_LBL_EVALUACION_C_U):CYT_LBL_EVALUACION_HASTA.' '.$oAntproduccionsMaximos->getObjectByIndex(12)->getNu_tope();
		$entity->setNu_puntajeD4_1($nu_max);

		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $entity->getOid(), '=');
		$oCriteria->addFilter('cd_antproduccionmaximo', $oAntproduccionsMaximos->getObjectByIndex(12)->getOid(), '=');
		$managerPuntajeAntproduccion =  ManagerFactory::getPuntajeAntproduccionManager();
		$oPuntajeAntproduccionD4_1 = $managerPuntajeAntproduccion->getEntity($oCriteria);
		
		if ($oPuntajeAntproduccionD4_1) {
			$entity->setNu_puntajeantproduccionD4_1($oPuntajeAntproduccionD4_1->getNu_puntaje());
		}
		
		$entity->setDs_grupoD5($oAntproduccionsMaximos->getObjectByIndex(13)->getAntproduccionPlanilla()->getSubGrupo()->getDs_subgrupo());
		
		$ds_descripcionD5_1 = str_replace('#puntaje#', '<strong>'.$oAntproduccionsMaximos->getObjectByIndex(13)->getNu_max().' '.CYT_LBL_EVALUACION_PUNTOS.'</strong>',$oAntproduccionsMaximos->getObjectByIndex(13)->getAntproduccionPlanilla()->getDs_antproduccionplanilla());
		
		
		$entity->setDs_descripcionD5_1($ds_descripcionD5_1);
		
		
		$nu_max = (($oAntproduccionsMaximos->getObjectByIndex(13)->getNu_max()!=0)&&($oAntproduccionsMaximos->getObjectByIndex(13)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(13)->getNu_max()))?((($oAntproduccionsMaximos->getObjectByIndex(13)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(13)->getNu_tope())&&($oAntproduccionsMaximos->getObjectByIndex(13)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(13)->getNu_max()))?$oAntproduccionsMaximos->getObjectByIndex(13)->getNu_max().' '.CYT_LBL_EVALUACION_PT:$oAntproduccionsMaximos->getObjectByIndex(13)->getNu_max(). ' '.CYT_LBL_EVALUACION_C_U):(($oAntproduccionsMaximos->getObjectByIndex(13)->getNu_min()!=$oAntproduccionsMaximos->getObjectByIndex(13)->getNu_max())?CYT_LBL_EVALUACION_HASTA.' '.$oAntproduccionsMaximos->getObjectByIndex(13)->getNu_max(). ' '.CYT_LBL_EVALUACION_C_U:CYT_LBL_EVALUACION_HASTA.' '.$oAntproduccionsMaximos->getObjectByIndex(13)->getNu_tope());
		$entity->setNu_puntajeD5_1($nu_max);

		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $entity->getOid(), '=');
		$oCriteria->addFilter('cd_antproduccionmaximo', $oAntproduccionsMaximos->getObjectByIndex(13)->getOid(), '=');
		$managerPuntajeAntproduccion =  ManagerFactory::getPuntajeAntproduccionManager();
		$oPuntajeAntproduccionD5_1 = $managerPuntajeAntproduccion->getEntity($oCriteria);
		
		if ($oPuntajeAntproduccionD5_1) {
			$entity->setNu_cantantproduccionD5_1($oPuntajeAntproduccionD5_1->getNu_cant());
			$entity->setNu_puntajeantproduccionD5_1($oPuntajeAntproduccionD5_1->getNu_puntaje());
		}
		
		$ds_descripcionD5_2 = str_replace('#puntaje#', '<strong>'.$oAntproduccionsMaximos->getObjectByIndex(14)->getNu_max().' '.CYT_LBL_EVALUACION_PUNTOS.'</strong>',$oAntproduccionsMaximos->getObjectByIndex(14)->getAntproduccionPlanilla()->getDs_antproduccionplanilla());
		
		
		$entity->setDs_descripcionD5_2($ds_descripcionD5_2);
		
		
		$nu_max = (($oAntproduccionsMaximos->getObjectByIndex(14)->getNu_max()!=0)&&($oAntproduccionsMaximos->getObjectByIndex(14)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(14)->getNu_max()))?((($oAntproduccionsMaximos->getObjectByIndex(14)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(14)->getNu_tope())&&($oAntproduccionsMaximos->getObjectByIndex(14)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(14)->getNu_max()))?$oAntproduccionsMaximos->getObjectByIndex(14)->getNu_max().' '.CYT_LBL_EVALUACION_PT:$oAntproduccionsMaximos->getObjectByIndex(14)->getNu_max(). ' '.CYT_LBL_EVALUACION_C_U):(($oAntproduccionsMaximos->getObjectByIndex(14)->getNu_min()!=$oAntproduccionsMaximos->getObjectByIndex(14)->getNu_max())?CYT_LBL_EVALUACION_HASTA.' '.$oAntproduccionsMaximos->getObjectByIndex(14)->getNu_max(). ' '.CYT_LBL_EVALUACION_C_U:CYT_LBL_EVALUACION_HASTA.' '.$oAntproduccionsMaximos->getObjectByIndex(14)->getNu_tope());
		$entity->setNu_puntajeD5_2($nu_max);

		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $entity->getOid(), '=');
		$oCriteria->addFilter('cd_antproduccionmaximo', $oAntproduccionsMaximos->getObjectByIndex(14)->getOid(), '=');
		$managerPuntajeAntproduccion =  ManagerFactory::getPuntajeAntproduccionManager();
		$oPuntajeAntproduccionD5_2 = $managerPuntajeAntproduccion->getEntity($oCriteria);
		
		if ($oPuntajeAntproduccionD5_2) {
			$entity->setNu_cantantproduccionD5_2($oPuntajeAntproduccionD5_2->getNu_cant());
			$entity->setNu_puntajeantproduccionD5_2($oPuntajeAntproduccionD5_2->getNu_puntaje());
		}
		
		$ds_descripcionD5_3 = str_replace('#puntaje#', '<strong>'.$oAntproduccionsMaximos->getObjectByIndex(15)->getNu_max().' '.CYT_LBL_EVALUACION_PUNTOS.'</strong>',$oAntproduccionsMaximos->getObjectByIndex(15)->getAntproduccionPlanilla()->getDs_antproduccionplanilla());
		
		
		$entity->setDs_descripcionD5_3($ds_descripcionD5_3);
		
		
		$nu_max = (($oAntproduccionsMaximos->getObjectByIndex(15)->getNu_max()!=0)&&($oAntproduccionsMaximos->getObjectByIndex(15)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(15)->getNu_max()))?((($oAntproduccionsMaximos->getObjectByIndex(15)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(15)->getNu_tope())&&($oAntproduccionsMaximos->getObjectByIndex(15)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(15)->getNu_max()))?$oAntproduccionsMaximos->getObjectByIndex(15)->getNu_max().' '.CYT_LBL_EVALUACION_PT:$oAntproduccionsMaximos->getObjectByIndex(15)->getNu_max(). ' '.CYT_LBL_EVALUACION_C_U):(($oAntproduccionsMaximos->getObjectByIndex(15)->getNu_min()!=$oAntproduccionsMaximos->getObjectByIndex(15)->getNu_max())?CYT_LBL_EVALUACION_HASTA.' '.$oAntproduccionsMaximos->getObjectByIndex(15)->getNu_max(). ' '.CYT_LBL_EVALUACION_C_U:CYT_LBL_EVALUACION_HASTA.' '.$oAntproduccionsMaximos->getObjectByIndex(15)->getNu_tope());
		$entity->setNu_puntajeD5_3($nu_max);

		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $entity->getOid(), '=');
		$oCriteria->addFilter('cd_antproduccionmaximo', $oAntproduccionsMaximos->getObjectByIndex(15)->getOid(), '=');
		$managerPuntajeAntproduccion =  ManagerFactory::getPuntajeAntproduccionManager();
		$oPuntajeAntproduccionD5_3 = $managerPuntajeAntproduccion->getEntity($oCriteria);
		
		if ($oPuntajeAntproduccionD5_3) {
			$entity->setNu_cantantproduccionD5_3($oPuntajeAntproduccionD5_3->getNu_cant());
			$entity->setNu_puntajeantproduccionD5_3($oPuntajeAntproduccionD5_3->getNu_puntaje());
		}
		
		
		$entity->setDs_grupoD7($oAntproduccionsMaximos->getObjectByIndex(16)->getAntproduccionPlanilla()->getSubGrupo()->getDs_subgrupo());
		$entity->setNu_topeD7('<strong>Max. '.$oAntproduccionsMaximos->getObjectByIndex(16)->getNu_tope().' '.CYT_LBL_EVALUACION_PT.'</strong>');
		$ds_descripcionD7 = str_replace('#puntaje#', '<strong>'.$oAntproduccionsMaximos->getObjectByIndex(16)->getNu_max().' '.CYT_LBL_EVALUACION_PUNTOS.'</strong>',$oAntproduccionsMaximos->getObjectByIndex(16)->getAntproduccionPlanilla()->getDs_antproduccionplanilla());
		
		
		$entity->setDs_descripcionD7($ds_descripcionD7);
		
		$nu_max = (($oAntproduccionsMaximos->getObjectByIndex(16)->getNu_max()!=0)&&($oAntproduccionsMaximos->getObjectByIndex(16)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(16)->getNu_max()))?((($oAntproduccionsMaximos->getObjectByIndex(16)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(16)->getNu_tope())&&($oAntproduccionsMaximos->getObjectByIndex(16)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(16)->getNu_max()))?$oAntproduccionsMaximos->getObjectByIndex(16)->getNu_max().' '.CYT_LBL_EVALUACION_PT:$oAntproduccionsMaximos->getObjectByIndex(16)->getNu_max(). ' '.CYT_LBL_EVALUACION_C_U):CYT_LBL_EVALUACION_HASTA.' '.$oAntproduccionsMaximos->getObjectByIndex(16)->getNu_tope();
		$entity->setNu_puntajeD7($nu_max);

		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $entity->getOid(), '=');
		$oCriteria->addFilter('cd_antproduccionmaximo', $oAntproduccionsMaximos->getObjectByIndex(16)->getOid(), '=');
		$managerPuntajeAntproduccion =  ManagerFactory::getPuntajeAntproduccionManager();
		$oPuntajeAntproduccionD7 = $managerPuntajeAntproduccion->getEntity($oCriteria);
		
		if ($oPuntajeAntproduccionD7) {
			$entity->setNu_puntajeantproduccionD7($oPuntajeAntproduccionD7->getNu_puntaje());
		}
		
		$entity->setDs_grupoD8($oAntproduccionsMaximos->getObjectByIndex(17)->getAntproduccionPlanilla()->getSubGrupo()->getDs_subgrupo());
		$entity->setDs_descripcionD8($oAntproduccionsMaximos->getObjectByIndex(17)->getAntproduccionPlanilla()->getDs_antproduccionplanilla());
		$nu_max = (($oAntproduccionsMaximos->getObjectByIndex(17)->getNu_max()!=0)&&($oAntproduccionsMaximos->getObjectByIndex(17)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(17)->getNu_max()))?((($oAntproduccionsMaximos->getObjectByIndex(17)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(17)->getNu_tope())&&($oAntproduccionsMaximos->getObjectByIndex(17)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex(17)->getNu_max()))?$oAntproduccionsMaximos->getObjectByIndex(17)->getNu_max().' '.CYT_LBL_EVALUACION_PT:$oAntproduccionsMaximos->getObjectByIndex(17)->getNu_max(). ' '.CYT_LBL_EVALUACION_C_U):CYT_LBL_EVALUACION_HASTA.' '.$oAntproduccionsMaximos->getObjectByIndex(17)->getNu_tope();
		
		$entity->setNu_puntajeD8($nu_max);
		
		$oCriteria = new CdtSearchCriteria();
	
		$tUnidadAprobada = CYTSecureDAOFactory::getUnidadAprobadaDAO()->getTableName();
		$oCriteria->addFilter("$tUnidadAprobada.cd_periodo", $oPerioActual->getOid(), '=');
		$oCriteria->addFilter("$tUnidadAprobada.cd_unidad", $oSolicitud->getLugarTrabajo()->getOid(), '=');
		
		$oUnidadAprobadaManager =  CYTSecureManagerFactory::getUnidadAprobadaManager();
		$oUnidadAprobada = $oUnidadAprobadaManager->getEntity($oCriteria);
		
		if ($oUnidadAprobada) {
			$entity->setNu_puntajeantproduccionD8($oAntproduccionsMaximos->getObjectByIndex(17)->getNu_max());
			$form = $this->getForm();
			$bl_posgrado = $form->getInput("nu_puntajeantproduccionD8");
			$bl_posgrado->setIsChecked(true);
			
		}
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_modeloplanilla', $oModeloPlanilla->getOid(), '=');
		$oCriteria->addOrder('cd_antjustificacionmaximo');
		$managerAntjustificacionMaximo =  ManagerFactory::getAntjustificacionMaximoManager();
		$oAntjustificacionsMaximos = $managerAntjustificacionMaximo->getEntities($oCriteria);
		
		$entity->setNu_antjustificacionmaximo($oAntjustificacionsMaximos->getObjectByIndex(0)->getPuntajeGrupo()->getNu_max());
		$entity->setNu_topeE1('<strong>Max. '.$oAntjustificacionsMaximos->getObjectByIndex(0)->getNu_tope().' '.CYT_LBL_EVALUACION_PT.'</strong>');
		
		$ds_descripcionE1 = $oAntjustificacionsMaximos->getObjectByIndex(0)->getAntjustificacionPlanilla()->getDs_antjustificacionplanilla();
		
		$entity->setDs_descripcionE1($ds_descripcionE1);
		
		$nu_max = (($oAntjustificacionsMaximos->getObjectByIndex(0)->getNu_max()!=0)&&($oAntjustificacionsMaximos->getObjectByIndex(0)->getNu_min()==$oAntjustificacionsMaximos->getObjectByIndex(0)->getNu_max()))?((($oAntjustificacionsMaximos->getObjectByIndex(0)->getNu_min()==$oAntjustificacionsMaximos->getObjectByIndex(0)->getNu_tope())&&($oAntjustificacionsMaximos->getObjectByIndex(0)->getNu_min()==$oAntjustificacionsMaximos->getObjectByIndex(0)->getNu_max()))?$oAntjustificacionsMaximos->getObjectByIndex(0)->getNu_max().' '.CYT_LBL_EVALUACION_PT:$oAntjustificacionsMaximos->getObjectByIndex(0)->getNu_max(). ' '.CYT_LBL_EVALUACION_C_U):CYT_LBL_EVALUACION_HASTA.' '.$oAntjustificacionsMaximos->getObjectByIndex(0)->getNu_tope();
		$entity->setNu_puntajeE1($nu_max);
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $entity->getOid(), '=');
		$oCriteria->addFilter('cd_antjustificacionmaximo', $oAntjustificacionsMaximos->getObjectByIndex(0)->getOid(), '=');
		$managerPuntajeAntjustificacion =  ManagerFactory::getPuntajeAntjustificacionManager();
		$oPuntajeAntjustificacionE1 = $managerPuntajeAntjustificacion->getEntity($oCriteria);
		
		if ($oPuntajeAntjustificacionE1) {
			$entity->setNu_puntajeantjustificacionE1($oPuntajeAntjustificacionE1->getNu_puntaje());
		}
		
		//CYTSecureUtils::logObject($entity);
		return $entity;
	}
	
	protected function parseEntity($entity, XTemplate $xtpl) {

			
		
		parent::parseEntity($entity, $xtpl);
		
	}
	
	protected function getEntityManager(){
		//return ManagerFactory::getEvaluacionManager();
	}

	/**
	 * (non-PHPdoc)
	 * @see classes/com/gestion/action/entities/EditEntityInitAction::getNewFormInstance()
	 */
	public function getNewFormInstance($action){
		$form = new CMPEvaluacionForm($action);
		return $form;
	}

	/**
	 * (non-PHPdoc)
	 * @see classes/com/gestion/action/entities/EditEntityInitAction::getNewEntityInstance()
	 */
	public function getNewEntityInstance(){
		$oEvaluacion = new Evaluacion();
		return $oEvaluacion;
	}


	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getOutputTitle();
	 */
	protected function getOutputTitle(){
		return CYT_MSG_SOLICITUD_TITLE_EVALUAR;
	}

	/**
	 * retorna el action para el submit.
	 * @return string
	 */
	protected function getSubmitAction(){
		return "evaluar_solicitud";
	}


}