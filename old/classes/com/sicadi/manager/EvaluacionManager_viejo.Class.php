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
     	
     	
    	$puntajePosgradoDAO =  DAOFactory::getPuntajePosgradoDAO();
        $puntajePosgradoDAO->deletePuntajePosgradoPorEvaluacion($entity->getOid());
        
     	$oPuntajePosgrado = new PuntajePosgrado();
     	$oPuntajePosgrado->setEvaluacion($entity);
     	
     	$oPeriodoManager = CYTSecureManagerFactory::getPeriodoManager();
		$oPerioActual = $oPeriodoManager->getPeriodoActual(CYT_PERIODO_YEAR);
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_periodo', $oPerioActual->getOid(), '=');
		$managerModeloPlanilla =  ManagerFactory::getModeloPlanillaManager();
		$oModeloPlanilla = $managerModeloPlanilla->getEntity($oCriteria);
		
     	$oPuntajePosgrado->setModeloPlanilla($oModeloPlanilla);
     	
     	$cd_posgradomaximo = explode('-',trim($entity->getCd_posgradomaximo()));
     	
     	$oPosgradoMaximo = new PosgradoMaximo();
     	$oPosgradoMaximo->setOid($cd_posgradomaximo[0]);
     	
     	$oPuntajePosgrado->setPosgradoMaximo($oPosgradoMaximo);
     	
     	//CYTSecureUtils::logObject($oPuntajePosgrado);
     	
     	$managerPuntajePosgrado = ManagerFactory::getPuntajePosgradoManager();
		$managerPuntajePosgrado->add($oPuntajePosgrado);	
		
		$puntajeAntacadDAO =  DAOFactory::getPuntajeAntacadDAO();
        $puntajeAntacadDAO->deletePuntajeAntacadPorEvaluacion($entity->getOid());
		
		$oPuntajeAntacad = new PuntajeAntacad();
     	$oPuntajeAntacad->setEvaluacion($entity);
     	$oPuntajeAntacad->setModeloPlanilla($oModeloPlanilla);
     	
     	$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_modeloplanilla', $oModeloPlanilla->getOid(), '=');
		$oCriteria->addOrder('cd_antacadmaximo');
		$managerAntacadMaximo =  ManagerFactory::getAntacadMaximoManager();
		$oAntacadsMaximos = $managerAntacadMaximo->getEntities($oCriteria);
		
		$oAntacadMaximo = new AntacadMaximo();
     	$oAntacadMaximo->setOid($oAntacadsMaximos->getObjectByIndex(0)->getOid());
		
		$oPuntajeAntacad->setAntacadMaximo($oAntacadMaximo);
     	
		if ($entity->getNu_puntajeantacadA2()) {
			$oPuntajeAntacad->setNu_puntaje($entity->getNu_puntajeantacadA2());
		}
     	else $oPuntajeAntacad->setNu_puntaje(0);
     	
     	$managerPuntajeAntacad = ManagerFactory::getPuntajeAntacadManager();
		$managerPuntajeAntacad->add($oPuntajeAntacad);	
     	
		$oAntacadMaximo = new AntacadMaximo();
     	$oAntacadMaximo->setOid($oAntacadsMaximos->getObjectByIndex(1)->getOid());
		
		$oPuntajeAntacad->setAntacadMaximo($oAntacadMaximo);
     	
     	if ($entity->getNu_puntajeantacadA3()) {
			$oPuntajeAntacad->setNu_puntaje($entity->getNu_puntajeantacadA3());
		}
     	else $oPuntajeAntacad->setNu_puntaje(0);
     	
     	$managerPuntajeAntacad = ManagerFactory::getPuntajeAntacadManager();
		$managerPuntajeAntacad->add($oPuntajeAntacad);	
		
		$oAntacadMaximo = new AntacadMaximo();
     	$oAntacadMaximo->setOid($oAntacadsMaximos->getObjectByIndex(2)->getOid());
		
		$oPuntajeAntacad->setAntacadMaximo($oAntacadMaximo);
		
		if ($entity->getBl_posgrado()) {
			$oPuntajeAntacad->setNu_puntaje(2);
		}
     	else {
     		$nu_puntaje = str_replace('F: ', '', $entity->getNu_puntajeantacadA4());
     		$oPuntajeAntacad->setNu_puntaje($nu_puntaje);
     	}
     	
     	$managerPuntajeAntacad = ManagerFactory::getPuntajeAntacadManager();
		$managerPuntajeAntacad->add($oPuntajeAntacad);	
		
		$oAntacadMaximo = new AntacadMaximo();
     	$oAntacadMaximo->setOid($oAntacadsMaximos->getObjectByIndex(3)->getOid());
		
		$oPuntajeAntacad->setAntacadMaximo($oAntacadMaximo);
     	
     	if ($entity->getNu_puntajeantacadA5()) {
			$oPuntajeAntacad->setNu_puntaje($oAntacadsMaximos->getObjectByIndex(3)->getNu_max());
		}
     	else $oPuntajeAntacad->setNu_puntaje(0);
     	
     	$managerPuntajeAntacad = ManagerFactory::getPuntajeAntacadManager();
		$managerPuntajeAntacad->add($oPuntajeAntacad);
		
		$oAntacadMaximo = new AntacadMaximo();
     	$oAntacadMaximo->setOid($oAntacadsMaximos->getObjectByIndex(4)->getOid());
		
		$oPuntajeAntacad->setAntacadMaximo($oAntacadMaximo);
     	
     	if ($entity->getNu_puntajeantacadA6()) {
			$oPuntajeAntacad->setNu_puntaje($entity->getNu_puntajeantacadA6());
		}
     	else $oPuntajeAntacad->setNu_puntaje(0);
     	
     	$managerPuntajeAntacad = ManagerFactory::getPuntajeAntacadManager();
		$managerPuntajeAntacad->add($oPuntajeAntacad);	
		
		$oPuntajeCargo = new PuntajeCargo();
     	$oPuntajeCargo->setEvaluacion($entity);
		$oPuntajeCargo->setModeloPlanilla($oModeloPlanilla);
     	
     	$cd_posgradomaximo = explode('-',trim($entity->getCd_cargomaximo()));
     	
     	if ($cd_posgradomaximo) {
     		$oCargoMaximo = new CargoMaximo();
	     	$oCargoMaximo->setOid($cd_posgradomaximo[0]);
	     	
	     	$oPuntajeCargo->setCargoMaximo($oCargoMaximo);
	     	
	     	$managerPuntajeCargo = ManagerFactory::getPuntajeCargoManager();
			$managerPuntajeCargo->add($oPuntajeCargo);
     	}
     	
     	$puntajeAntotrosDAO =  DAOFactory::getPuntajeAntotrosDAO();
        $puntajeAntotrosDAO->deletePuntajeAntotrosPorEvaluacion($entity->getOid());
		
		$oPuntajeAntotros = new PuntajeAntotros();
     	$oPuntajeAntotros->setEvaluacion($entity);
     	$oPuntajeAntotros->setModeloPlanilla($oModeloPlanilla);
     	
     	$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_modeloplanilla', $oModeloPlanilla->getOid(), '=');
		$oCriteria->addOrder('cd_antotrosmaximo');
		$managerAntotrosMaximo =  ManagerFactory::getAntotrosMaximoManager();
		$oAntotrossMaximos = $managerAntotrosMaximo->getEntities($oCriteria);
		
		$oAntotrosMaximo = new AntotrosMaximo();
     	$oAntotrosMaximo->setOid($oAntotrossMaximos->getObjectByIndex(0)->getOid());
		
		$oPuntajeAntotros->setAntotrosMaximo($oAntotrosMaximo);
     	
		if ($entity->getNu_puntajeantotrosC1()) {
			$oPuntajeAntotros->setNu_puntaje($entity->getNu_puntajeantotrosC1());
		}
     	else $oPuntajeAntotros->setNu_puntaje(0);
     	
     	$managerPuntajeAntotros = ManagerFactory::getPuntajeAntotrosManager();
		$managerPuntajeAntotros->add($oPuntajeAntotros);	
		
		$oAntotrosMaximo = new AntotrosMaximo();
     	$oAntotrosMaximo->setOid($oAntotrossMaximos->getObjectByIndex(1)->getOid());
		
		$oPuntajeAntotros->setAntotrosMaximo($oAntotrosMaximo);
     	
		if ($entity->getNu_puntajeantotrosC2_1()) {
			$oPuntajeAntotros->setNu_puntaje($entity->getNu_puntajeantotrosC2_1());
		}
     	else $oPuntajeAntotros->setNu_puntaje(0);
     	
     	$managerPuntajeAntotros = ManagerFactory::getPuntajeAntotrosManager();
		$managerPuntajeAntotros->add($oPuntajeAntotros);	
		
		$oAntotrosMaximo = new AntotrosMaximo();
     	$oAntotrosMaximo->setOid($oAntotrossMaximos->getObjectByIndex(2)->getOid());
		
		$oPuntajeAntotros->setAntotrosMaximo($oAntotrosMaximo);
     	
		if ($entity->getNu_puntajeantotrosC2_2()) {
			$oPuntajeAntotros->setNu_puntaje($entity->getNu_puntajeantotrosC2_2());
		}
     	else $oPuntajeAntotros->setNu_puntaje(0);
     	
     	$managerPuntajeAntotros = ManagerFactory::getPuntajeAntotrosManager();
		$managerPuntajeAntotros->add($oPuntajeAntotros);

		$oAntotrosMaximo = new AntotrosMaximo();
     	$oAntotrosMaximo->setOid($oAntotrossMaximos->getObjectByIndex(3)->getOid());
		
		$oPuntajeAntotros->setAntotrosMaximo($oAntotrosMaximo);
     	
		if ($entity->getNu_puntajeantotrosC2_3()) {
			$oPuntajeAntotros->setNu_puntaje($entity->getNu_puntajeantotrosC2_3());
		}
     	else $oPuntajeAntotros->setNu_puntaje(0);
     	
     	$managerPuntajeAntotros = ManagerFactory::getPuntajeAntotrosManager();
		$managerPuntajeAntotros->add($oPuntajeAntotros);
		
		/*$oAntotrosMaximo = new AntotrosMaximo();
     	$oAntotrosMaximo->setOid($oAntotrossMaximos->getObjectByIndex(4)->getOid());
		
		$oPuntajeAntotros->setAntotrosMaximo($oAntotrosMaximo);
     	
		if ($entity->getNu_puntajeantotrosC3()) {
			$oPuntajeAntotros->setNu_puntaje($entity->getNu_puntajeantotrosC3());
		}
     	else $oPuntajeAntotros->setNu_puntaje(0);
     	
     	$managerPuntajeAntotros = ManagerFactory::getPuntajeAntotrosManager();
		$managerPuntajeAntotros->add($oPuntajeAntotros);*/	
		
		$oAntotrosMaximo = new AntotrosMaximo();
     	$oAntotrosMaximo->setOid($oAntotrossMaximos->getObjectByIndex(4)->getOid());
		
		$oPuntajeAntotros->setAntotrosMaximo($oAntotrosMaximo);
     	
		if ($entity->getNu_puntajeantotrosC4()) {
			$oPuntajeAntotros->setNu_puntaje($entity->getNu_puntajeantotrosC4());
		}
     	else $oPuntajeAntotros->setNu_puntaje(0);
     	
     	$managerPuntajeAntotros = ManagerFactory::getPuntajeAntotrosManager();
		$managerPuntajeAntotros->add($oPuntajeAntotros);	
		
		$puntajeAntproduccionDAO =  DAOFactory::getPuntajeAntproduccionDAO();
        $puntajeAntproduccionDAO->deletePuntajeAntproduccionPorEvaluacion($entity->getOid());
		
		$oPuntajeAntproduccion = new PuntajeAntproduccion();
     	$oPuntajeAntproduccion->setEvaluacion($entity);
     	$oPuntajeAntproduccion->setModeloPlanilla($oModeloPlanilla);
     	
     	$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_modeloplanilla', $oModeloPlanilla->getOid(), '=');
		$oCriteria->addOrder('cd_antproduccionmaximo');
		$managerAntproduccionMaximo =  ManagerFactory::getAntproduccionMaximoManager();
		$oAntproduccionsMaximos = $managerAntproduccionMaximo->getEntities($oCriteria);
		
		/*$oAntproduccionMaximo = new AntproduccionMaximo();
     	$oAntproduccionMaximo->setOid($oAntproduccionsMaximos->getObjectByIndex(0)->getOid());
		
		$oPuntajeAntproduccion->setAntproduccionMaximo($oAntproduccionMaximo);
     	
		if ($entity->getNu_puntajeantproduccionD1_1()) {
			$oPuntajeAntproduccion->setNu_puntaje($entity->getNu_puntajeantproduccionD1_1());
		}
     	else $oPuntajeAntproduccion->setNu_puntaje(0);
     	
     	$managerPuntajeAntproduccion = ManagerFactory::getPuntajeAntproduccionManager();
		$managerPuntajeAntproduccion->add($oPuntajeAntproduccion);	
		
		$oAntproduccionMaximo = new AntproduccionMaximo();
     	$oAntproduccionMaximo->setOid($oAntproduccionsMaximos->getObjectByIndex(1)->getOid());
		
		$oPuntajeAntproduccion->setAntproduccionMaximo($oAntproduccionMaximo);
     	
		if ($entity->getNu_puntajeantproduccionD1_2()) {
			$oPuntajeAntproduccion->setNu_puntaje($entity->getNu_puntajeantproduccionD1_2());
		}
     	else $oPuntajeAntproduccion->setNu_puntaje(0);
     	
     	$managerPuntajeAntproduccion = ManagerFactory::getPuntajeAntproduccionManager();
		$managerPuntajeAntproduccion->add($oPuntajeAntproduccion);*/
		
		
		
		$oAntproduccionMaximo = new AntproduccionMaximo();
     	$oAntproduccionMaximo->setOid($oAntproduccionsMaximos->getObjectByIndex(0)->getOid());
		
		$oPuntajeAntproduccion->setAntproduccionMaximo($oAntproduccionMaximo);
     	
		if ($entity->getNu_puntajeantproduccionD6_1()) {
			$oPuntajeAntproduccion->setNu_puntaje($entity->getNu_puntajeantproduccionD6_1());
		}
     	else $oPuntajeAntproduccion->setNu_puntaje(0);
     	
     	$oPuntajeAntproduccion->setNu_cant(0);
     	
     	$managerPuntajeAntproduccion = ManagerFactory::getPuntajeAntproduccionManager();
		$managerPuntajeAntproduccion->add($oPuntajeAntproduccion);
		
		$oAntproduccionMaximo = new AntproduccionMaximo();
     	$oAntproduccionMaximo->setOid($oAntproduccionsMaximos->getObjectByIndex(1)->getOid());
		
		$oPuntajeAntproduccion->setAntproduccionMaximo($oAntproduccionMaximo);
     	
		if ($entity->getNu_puntajeantproduccionD6_2()) {
			$oPuntajeAntproduccion->setNu_puntaje($entity->getNu_puntajeantproduccionD6_2());
		}
     	else $oPuntajeAntproduccion->setNu_puntaje(0);
     	
     	$oPuntajeAntproduccion->setNu_cant(0);
     	
     	$managerPuntajeAntproduccion = ManagerFactory::getPuntajeAntproduccionManager();
		$managerPuntajeAntproduccion->add($oPuntajeAntproduccion);
		
		$oAntproduccionMaximo = new AntproduccionMaximo();
     	$oAntproduccionMaximo->setOid($oAntproduccionsMaximos->getObjectByIndex(2)->getOid());
		
		$oPuntajeAntproduccion->setAntproduccionMaximo($oAntproduccionMaximo);
     	
		if ($entity->getNu_puntajeantproduccionD6_3()) {
			$oPuntajeAntproduccion->setNu_puntaje($entity->getNu_puntajeantproduccionD6_3());
		}
     	else $oPuntajeAntproduccion->setNu_puntaje(0);
     	
     	$oPuntajeAntproduccion->setNu_cant(0);
     	
     	$managerPuntajeAntproduccion = ManagerFactory::getPuntajeAntproduccionManager();
		$managerPuntajeAntproduccion->add($oPuntajeAntproduccion);
		
		$oAntproduccionMaximo = new AntproduccionMaximo();
     	$oAntproduccionMaximo->setOid($oAntproduccionsMaximos->getObjectByIndex(3)->getOid());
		
		$oPuntajeAntproduccion->setAntproduccionMaximo($oAntproduccionMaximo);
     	
		if ($entity->getNu_puntajeantproduccionD6_4()) {
			$oPuntajeAntproduccion->setNu_puntaje($entity->getNu_puntajeantproduccionD6_4());
		}
     	else $oPuntajeAntproduccion->setNu_puntaje(0);
     	
     	$oPuntajeAntproduccion->setNu_cant(0);
     	
     	$managerPuntajeAntproduccion = ManagerFactory::getPuntajeAntproduccionManager();
		$managerPuntajeAntproduccion->add($oPuntajeAntproduccion);
		
		/*$oAntproduccionMaximo = new AntproduccionMaximo();
     	$oAntproduccionMaximo->setOid($oAntproduccionsMaximos->getObjectByIndex(4)->getOid());
		
		$oPuntajeAntproduccion->setAntproduccionMaximo($oAntproduccionMaximo);
     	
		if ($entity->getNu_puntajeantproduccionD2_1()) {
			$oPuntajeAntproduccion->setNu_puntaje($entity->getNu_puntajeantproduccionD2_1());
		}
     	else $oPuntajeAntproduccion->setNu_puntaje(0);
     	
     	$managerPuntajeAntproduccion = ManagerFactory::getPuntajeAntproduccionManager();
		$managerPuntajeAntproduccion->add($oPuntajeAntproduccion);*/
		
		$oAntproduccionMaximo = new AntproduccionMaximo();
     	$oAntproduccionMaximo->setOid($oAntproduccionsMaximos->getObjectByIndex(4)->getOid());
		
		$oPuntajeAntproduccion->setAntproduccionMaximo($oAntproduccionMaximo);
     	
		if ($entity->getNu_puntajeantproduccionD2_2()) {
			$oPuntajeAntproduccion->setNu_puntaje($entity->getNu_puntajeantproduccionD2_2());
		}
     	else $oPuntajeAntproduccion->setNu_puntaje(0);
     	
     	$managerPuntajeAntproduccion = ManagerFactory::getPuntajeAntproduccionManager();
		$managerPuntajeAntproduccion->add($oPuntajeAntproduccion);
		
		$oAntproduccionMaximo = new AntproduccionMaximo();
     	$oAntproduccionMaximo->setOid($oAntproduccionsMaximos->getObjectByIndex(5)->getOid());
		
		$oPuntajeAntproduccion->setAntproduccionMaximo($oAntproduccionMaximo);
     	
		if ($entity->getNu_puntajeantproduccionD2_3()) {
			$oPuntajeAntproduccion->setNu_puntaje($entity->getNu_puntajeantproduccionD2_3());
		}
     	else $oPuntajeAntproduccion->setNu_puntaje(0);
     	
     	$managerPuntajeAntproduccion = ManagerFactory::getPuntajeAntproduccionManager();
		$managerPuntajeAntproduccion->add($oPuntajeAntproduccion);
		
		$oAntproduccionMaximo = new AntproduccionMaximo();
     	$oAntproduccionMaximo->setOid($oAntproduccionsMaximos->getObjectByIndex(6)->getOid());
		
		$oPuntajeAntproduccion->setAntproduccionMaximo($oAntproduccionMaximo);
     	
		if ($entity->getNu_puntajeantproduccionD2_4()) {
			$oPuntajeAntproduccion->setNu_puntaje($entity->getNu_puntajeantproduccionD2_4());
		}
     	else $oPuntajeAntproduccion->setNu_puntaje(0);
     	
     	$managerPuntajeAntproduccion = ManagerFactory::getPuntajeAntproduccionManager();
		$managerPuntajeAntproduccion->add($oPuntajeAntproduccion);
		
		$oAntproduccionMaximo = new AntproduccionMaximo();
     	$oAntproduccionMaximo->setOid($oAntproduccionsMaximos->getObjectByIndex(7)->getOid());
		
		$oPuntajeAntproduccion->setAntproduccionMaximo($oAntproduccionMaximo);
     	
		if ($entity->getNu_puntajeantproduccionD2_5()) {
			$oPuntajeAntproduccion->setNu_puntaje($entity->getNu_puntajeantproduccionD2_5());
		}
     	else $oPuntajeAntproduccion->setNu_puntaje(0);
     	
     	$managerPuntajeAntproduccion = ManagerFactory::getPuntajeAntproduccionManager();
		$managerPuntajeAntproduccion->add($oPuntajeAntproduccion);
		
		$oAntproduccionMaximo = new AntproduccionMaximo();
     	$oAntproduccionMaximo->setOid($oAntproduccionsMaximos->getObjectByIndex(8)->getOid());
		
		$oPuntajeAntproduccion->setAntproduccionMaximo($oAntproduccionMaximo);
     	
		if ($entity->getNu_puntajeantproduccionD3_1()) {
			$oPuntajeAntproduccion->setNu_puntaje($entity->getNu_puntajeantproduccionD3_1());
		}
     	else $oPuntajeAntproduccion->setNu_puntaje(0);
     	
     	$managerPuntajeAntproduccion = ManagerFactory::getPuntajeAntproduccionManager();
		$managerPuntajeAntproduccion->add($oPuntajeAntproduccion);
		
		$oAntproduccionMaximo = new AntproduccionMaximo();
     	$oAntproduccionMaximo->setOid($oAntproduccionsMaximos->getObjectByIndex(9)->getOid());
		
		$oPuntajeAntproduccion->setAntproduccionMaximo($oAntproduccionMaximo);
     	
		if ($entity->getNu_puntajeantproduccionD3_2()) {
			$oPuntajeAntproduccion->setNu_puntaje($entity->getNu_puntajeantproduccionD3_2());
		}
     	else $oPuntajeAntproduccion->setNu_puntaje(0);
     	
     	$managerPuntajeAntproduccion = ManagerFactory::getPuntajeAntproduccionManager();
		$managerPuntajeAntproduccion->add($oPuntajeAntproduccion);
		
		$oAntproduccionMaximo = new AntproduccionMaximo();
     	$oAntproduccionMaximo->setOid($oAntproduccionsMaximos->getObjectByIndex(10)->getOid());
		
		$oPuntajeAntproduccion->setAntproduccionMaximo($oAntproduccionMaximo);
     	
		if ($entity->getNu_puntajeantproduccionD3_3()) {
			$oPuntajeAntproduccion->setNu_puntaje($entity->getNu_puntajeantproduccionD3_3());
		}
     	else $oPuntajeAntproduccion->setNu_puntaje(0);
     	
     	$managerPuntajeAntproduccion = ManagerFactory::getPuntajeAntproduccionManager();
		$managerPuntajeAntproduccion->add($oPuntajeAntproduccion);
		
		$oAntproduccionMaximo = new AntproduccionMaximo();
     	$oAntproduccionMaximo->setOid($oAntproduccionsMaximos->getObjectByIndex(11)->getOid());
		
		$oPuntajeAntproduccion->setAntproduccionMaximo($oAntproduccionMaximo);
     	
		if ($entity->getNu_puntajeantproduccionD3_4()) {
			$oPuntajeAntproduccion->setNu_puntaje($entity->getNu_puntajeantproduccionD3_4());
		}
     	else $oPuntajeAntproduccion->setNu_puntaje(0);
     	
     	$managerPuntajeAntproduccion = ManagerFactory::getPuntajeAntproduccionManager();
		$managerPuntajeAntproduccion->add($oPuntajeAntproduccion);
		
		/*$oAntproduccionMaximo = new AntproduccionMaximo();
     	$oAntproduccionMaximo->setOid($oAntproduccionsMaximos->getObjectByIndex(12)->getOid());
		
		$oPuntajeAntproduccion->setAntproduccionMaximo($oAntproduccionMaximo);
     	
		if ($entity->getNu_puntajeantproduccionD3_5()) {
			$oPuntajeAntproduccion->setNu_puntaje($entity->getNu_puntajeantproduccionD3_5());
		}
     	else $oPuntajeAntproduccion->setNu_puntaje(0);
     	
     	$managerPuntajeAntproduccion = ManagerFactory::getPuntajeAntproduccionManager();
		$managerPuntajeAntproduccion->add($oPuntajeAntproduccion);*/
		
		$oAntproduccionMaximo = new AntproduccionMaximo();
     	$oAntproduccionMaximo->setOid($oAntproduccionsMaximos->getObjectByIndex(12)->getOid());
		
		$oPuntajeAntproduccion->setAntproduccionMaximo($oAntproduccionMaximo);
     	
		if ($entity->getNu_puntajeantproduccionD4_1()) {
			$oPuntajeAntproduccion->setNu_puntaje($entity->getNu_puntajeantproduccionD4_1());
		}
     	else $oPuntajeAntproduccion->setNu_puntaje(0);
     	
     	$managerPuntajeAntproduccion = ManagerFactory::getPuntajeAntproduccionManager();
		$managerPuntajeAntproduccion->add($oPuntajeAntproduccion);
		
		$oAntproduccionMaximo = new AntproduccionMaximo();
     	$oAntproduccionMaximo->setOid($oAntproduccionsMaximos->getObjectByIndex(13)->getOid());
		
		$oPuntajeAntproduccion->setAntproduccionMaximo($oAntproduccionMaximo);
     	
		if ($entity->getNu_cantantproduccionD5_1()) {
			$oPuntajeAntproduccion->setNu_cant($entity->getNu_cantantproduccionD5_1());
		}
     	else $oPuntajeAntproduccion->setNu_cant(0);
     	
     	if ($entity->getNu_puntajeantproduccionD5_1()) {
			$oPuntajeAntproduccion->setNu_puntaje($entity->getNu_puntajeantproduccionD5_1());
		}
     	else $oPuntajeAntproduccion->setNu_puntaje(0);
     	
     	$managerPuntajeAntproduccion = ManagerFactory::getPuntajeAntproduccionManager();
		$managerPuntajeAntproduccion->add($oPuntajeAntproduccion);
		
		$oAntproduccionMaximo = new AntproduccionMaximo();
     	$oAntproduccionMaximo->setOid($oAntproduccionsMaximos->getObjectByIndex(14)->getOid());
		
		$oPuntajeAntproduccion->setAntproduccionMaximo($oAntproduccionMaximo);
     	
		if ($entity->getNu_cantantproduccionD5_2()) {
			$oPuntajeAntproduccion->setNu_cant($entity->getNu_cantantproduccionD5_2());
		}
     	else $oPuntajeAntproduccion->setNu_cant(0);
     	
     	if ($entity->getNu_puntajeantproduccionD5_2()) {
			$oPuntajeAntproduccion->setNu_puntaje($entity->getNu_puntajeantproduccionD5_2());
		}
     	else $oPuntajeAntproduccion->setNu_puntaje(0);
     	
     	$managerPuntajeAntproduccion = ManagerFactory::getPuntajeAntproduccionManager();
		$managerPuntajeAntproduccion->add($oPuntajeAntproduccion);
		
		$oAntproduccionMaximo = new AntproduccionMaximo();
     	$oAntproduccionMaximo->setOid($oAntproduccionsMaximos->getObjectByIndex(15)->getOid());
		
		$oPuntajeAntproduccion->setAntproduccionMaximo($oAntproduccionMaximo);
     	
		if ($entity->getNu_cantantproduccionD5_3()) {
			$oPuntajeAntproduccion->setNu_cant($entity->getNu_cantantproduccionD5_3());
		}
     	else $oPuntajeAntproduccion->setNu_cant(0);
     	
     	if ($entity->getNu_puntajeantproduccionD5_3()) {
			$oPuntajeAntproduccion->setNu_puntaje($entity->getNu_puntajeantproduccionD5_3());
		}
     	else $oPuntajeAntproduccion->setNu_puntaje(0);
     	
     	$managerPuntajeAntproduccion = ManagerFactory::getPuntajeAntproduccionManager();
		$managerPuntajeAntproduccion->add($oPuntajeAntproduccion);
		
		
		
		$oAntproduccionMaximo = new AntproduccionMaximo();
     	$oAntproduccionMaximo->setOid($oAntproduccionsMaximos->getObjectByIndex(16)->getOid());
		
		$oPuntajeAntproduccion->setAntproduccionMaximo($oAntproduccionMaximo);
     	
		if ($entity->getNu_puntajeantproduccionD7()) {
			$oPuntajeAntproduccion->setNu_puntaje($entity->getNu_puntajeantproduccionD7());
		}
     	else $oPuntajeAntproduccion->setNu_puntaje(0);
     	
     	$oPuntajeAntproduccion->setNu_cant(0);
     	
     	$managerPuntajeAntproduccion = ManagerFactory::getPuntajeAntproduccionManager();
		$managerPuntajeAntproduccion->add($oPuntajeAntproduccion);
		
		$oAntproduccionMaximo = new AntproduccionMaximo();
     	$oAntproduccionMaximo->setOid($oAntproduccionsMaximos->getObjectByIndex(17)->getOid());
		
		$oPuntajeAntproduccion->setAntproduccionMaximo($oAntproduccionMaximo);
     	
		if ($entity->getNu_puntajeantproduccionD8()) {
			$oPuntajeAntproduccion->setNu_puntaje($entity->getNu_puntajeantproduccionD8());
		}
     	else $oPuntajeAntproduccion->setNu_puntaje(0);
     	
     	$oPuntajeAntproduccion->setNu_cant(0);
     	
     	$managerPuntajeAntproduccion = ManagerFactory::getPuntajeAntproduccionManager();
		$managerPuntajeAntproduccion->add($oPuntajeAntproduccion);
		
		$puntajeAntjustificacionDAO =  DAOFactory::getPuntajeAntjustificacionDAO();
        $puntajeAntjustificacionDAO->deletePuntajeAntjustificacionPorEvaluacion($entity->getOid());
		
		$oPuntajeAntjustificacion = new PuntajeAntjustificacion();
     	$oPuntajeAntjustificacion->setEvaluacion($entity);
     	$oPuntajeAntjustificacion->setModeloPlanilla($oModeloPlanilla);
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_modeloplanilla', $oModeloPlanilla->getOid(), '=');
		$oCriteria->addOrder('cd_antjustificacionmaximo');
		$managerAntjustificacionMaximo =  ManagerFactory::getAntjustificacionMaximoManager();
		$oAntjustificacionsMaximos = $managerAntjustificacionMaximo->getEntities($oCriteria);
		
		$oAntjustificacionMaximo = new AntjustificacionMaximo();
     	$oAntjustificacionMaximo->setOid($oAntjustificacionsMaximos->getObjectByIndex(0)->getOid());
		
		$oPuntajeAntjustificacion->setAntjustificacionMaximo($oAntjustificacionMaximo);
     	
		if ($entity->getNu_puntajeantjustificacionE1()) {
			$oPuntajeAntjustificacion->setNu_puntaje($entity->getNu_puntajeantjustificacionE1());
		}
     	else $oPuntajeAntjustificacion->setNu_puntaje(0);
     	
     	$managerPuntajeAntjustificacion = ManagerFactory::getPuntajeAntjustificacionManager();
		$managerPuntajeAntjustificacion->add($oPuntajeAntjustificacion);	
     	
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
		
		$oCriteria = new CdtSearchCriteria();
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
		$oSolicitud->setJovenesProyectos( $proyectosAnterioresManager->getEntities($oCriteria) );
		
		$oEstado = new Estado();
		$oEstado->setOid(CYT_ESTADO_SOLICITUD_EN_EVALUACION);
		$this->cambiarEstado($entity, $oEstado, '');
		$oSolicitudManager->cambiarEstado($oSolicitud, $oEstado, '');
	
		$this->cambiarEstado($entity, $oEstado, '');
		
		$pdf->setEstado_oid(CYT_ESTADO_SOLICITUD_RECIBIDA);
		$pdf->setPeriodo_oid($oSolicitud->getPeriodo()->getOid());
		
		
		$oPeriodoManager =  CYTSecureManagerFactory::getPeriodoManager();
		$oPeriodo = $oPeriodoManager->getObjectByCode($oSolicitud->getPeriodo()->getOid());
		$pdf->setYear($oPeriodo->getDs_periodo());
				
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
		}
        
		
		$year = $oPeriodo->getDs_periodo();
			
		
		$subjectMail = htmlspecialchars(CYT_LBL_EVALUACION_MAIL_SUBJECT.' '.$year, ENT_QUOTES, "UTF-8");
			
		$xtpl = new XTemplate( CYT_TEMPLATE_SOLICITUD_MAIL_ENVIAR );
		$xtpl->assign ( 'img_logo', WEB_PATH.'css/images/image002.gif' );
		$xtpl->assign('solicitud_titulo', $subjectMail);
		$xtpl->assign('year_label', CYT_LBL_SOLICITUD_MAIL_YEAR);
		$xtpl->assign('year', $oPeriodo->getDs_periodo());
		$xtpl->assign('investigador_label', CYT_LBL_SOLICITUD_MAIL_INVESTIGADOR);
		$xtpl->assign('investigador', htmlspecialchars($oSolicitud->getDocente()->getDs_apellido().', '.$oSolicitud->getDocente()->getDs_nombre(), ENT_QUOTES, "UTF-8"));
		$xtpl->assign('comment', CYT_LBL_EVALUACION_MAIL_COMMENT);
		$xtpl->parse('main');		
		$bodyMail = $xtpl->text('main');
		
		
		/*$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('oid', $entity->getUser()->getOid(), '=');
				
		$managerUser = CYTSecureManagerFactory::getUserManager();
		$oUsuario = $managerUser->getEntity($oCriteria);*/
		$managerUser = CYTSecureManagerFactory::getUserManager();
		$oUsuario = $managerUser->getObjectByCode($entity->getUser()->getOid());
		 
       if ($oUsuario->getDs_email() != "") {
				$ds_name = ($oUsuario->getDs_name())?$oUsuario->getDs_name():$oUsuario->getDs_username();
         		CYTSecureUtils::sendMail($ds_name, $oUsuario->getDs_email(), $subjectMail, $bodyMail, $attachs);
         		
        }
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
		$oEvaluacionEstadoDAO =  CYTSecureDAOFactory::getEvaluacionEstadoDAO();
		$oEvaluacionEstadoDAO->deleteEvaluacionEstadoPorEvaluacion($evaluacion_oid);
		$this->delete($evaluacion_oid);
	}
	
	
}
?>
