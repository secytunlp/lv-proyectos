<?php

/**
 * Acción para actualizar una evaluacion
 *
 * @author Marcos
 * @since 22-05-2014
 *
 */
class EvaluarSolicitudAction extends UpdateEntityAction{

	protected function getEntity() {
	
		
		
		$entity =  parent::getEntity();
		$entity->setDt_fecha(date('YmdHis'));
		
		//CYTSecureUtils::logObject($_POST);
		
		if ( $_POST ['cd_posgradomaximo'] ){
			$entity->setPosgrado_oid( $_POST ['cd_posgradomaximo'] );
		}
		
		if ( $_POST ['cd_cargomaximo'] ){
			$entity->setCargo_oid( $_POST ['cd_cargomaximo'] );
		}
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_modeloplanilla', $entity->getModeloplanilla_oid(), '=');
		$managerAntacadMaximo =  ManagerFactory::getAntacadMaximoManager();
		$oAntacadsMaximos = $managerAntacadMaximo->getEntities($oCriteria);
		foreach ($oAntacadsMaximos as $antacad) {
			$antacadmaximo = $antacad->getOid();
			if ((($antacad->getAntacadPlanilla()->getOid()==3)||($antacad->getAntacadPlanilla()->getOid()==7))&&($_POST ['bl_posgrado'])) {
				$antacadmaximo .='-2';
			}
			elseif ( isset($_POST ['nu_puntajeantacadP'.$antacad->getOid()]) ){
				$antacadmaximo .='-'.$_POST ['nu_puntajeantacadP'.$antacad->getOid()];
				if ( isset($_POST ['nu_cantantacadP'.$antacad->getOid()]) ){
					$antacadmaximo .='-'.$_POST ['nu_cantantacadP'.$antacad->getOid()];
				}
				else{
					$antacadmaximo .='-0';
				}
			}
			else{
				$antacadmaximo .='-0';
			}	
			
			$entity->getAntacads()->addItem($antacadmaximo);	
		}
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_modeloplanilla', $entity->getModeloplanilla_oid(), '=');
		$managerAntotrosMaximo =  ManagerFactory::getAntotrosMaximoManager();
		$oAntotrossMaximos = $managerAntotrosMaximo->getEntities($oCriteria);
		foreach ($oAntotrossMaximos as $antotros) {
			$antotrosmaximo = $antotros->getOid();
			if ( isset($_POST ['nu_puntajeantotrosP'.$antotros->getOid()]) ){
				$antotrosmaximo .='-'.$_POST ['nu_puntajeantotrosP'.$antotros->getOid()];
				if ( isset($_POST ['nu_cantantotrosP'.$antotros->getOid()]) ){
					$antotrosmaximo .='-'.$_POST ['nu_cantantotrosP'.$antotros->getOid()];
				}
				else{
					$antotrosmaximo .='-0';
				}
			}
			else{
				$antotrosmaximo .='-0';
			}	
			$entity->getAntotross()->addItem($antotrosmaximo);	
		}
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_modeloplanilla', $entity->getModeloplanilla_oid(), '=');
		$managerAntproduccionMaximo =  ManagerFactory::getAntproduccionMaximoManager();
		$oAntproduccionsMaximos = $managerAntproduccionMaximo->getEntities($oCriteria);
		foreach ($oAntproduccionsMaximos as $antproduccion) {
			$antproduccionmaximo = $antproduccion->getOid();
			if ( isset($_POST ['nu_puntajeantproduccionP'.$antproduccion->getOid()]) ){
				$antproduccionmaximo .='-'.$_POST ['nu_puntajeantproduccionP'.$antproduccion->getOid()];
				if ( isset($_POST ['nu_cantantproduccionP'.$antproduccion->getOid()]) ){
					$antproduccionmaximo .='-'.$_POST ['nu_cantantproduccionP'.$antproduccion->getOid()];
				}
				else{
					$antproduccionmaximo .='-0';
				}
			}
			else{
				$antproduccionmaximo .='-0';
			}	
			$entity->getAntproduccions()->addItem($antproduccionmaximo);	
		}
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_modeloplanilla', $entity->getModeloplanilla_oid(), '=');
		$managerAntjustificacionMaximo =  ManagerFactory::getAntjustificacionMaximoManager();
		$oAntjustificacionsMaximos = $managerAntjustificacionMaximo->getEntities($oCriteria);
		foreach ($oAntjustificacionsMaximos as $antjustificacion) {
			$antjustificacionmaximo = $antjustificacion->getOid();
			if ( isset($_POST ['nu_puntajeantjustificacionP'.$antjustificacion->getOid()]) ){
				$antjustificacionmaximo .='-'.$_POST ['nu_puntajeantjustificacionP'.$antjustificacion->getOid()];
				if ( isset($_POST ['nu_cantantjustificacionP'.$antjustificacion->getOid()]) ){
					$antjustificacionmaximo .='-'.$_POST ['nu_cantantjustificacionP'.$antjustificacion->getOid()];
				}
				else{
					$antjustificacionmaximo .='-0';
				}
			}
			else{
				$antjustificacionmaximo .='-0';
			}	
			$entity->getAntjustificacions()->addItem($antjustificacionmaximo);	
		}
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_modeloplanilla', $entity->getModeloplanilla_oid(), '=');
		$managerSubanteriorMaximo =  ManagerFactory::getSubanteriorMaximoManager();
		$oSubanteriorsMaximos = $managerSubanteriorMaximo->getEntities($oCriteria);
		foreach ($oSubanteriorsMaximos as $subanterior) {
			$subanteriormaximo = $subanterior->getOid();
			if ( isset($_POST ['nu_puntajesubanteriorP'.$subanterior->getOid()]) ){
				$subanteriormaximo .='-'.$_POST ['nu_puntajesubanteriorP'.$subanterior->getOid()];
				if ( isset($_POST ['nu_cantsubanteriorP'.$subanterior->getOid()]) ){
					$subanteriormaximo .='-'.$_POST ['nu_cantsubanteriorP'.$subanterior->getOid()];
				}
				else{
					$subanteriormaximo .='-0';
				}
			}
			else{
				$subanteriormaximo .='-0';
			}	
			$entity->getSubanteriors()->addItem($subanteriormaximo);	
		}
		
		return $entity;
		
	}
	
	public function getNewFormInstance(){
		return new CMPEvaluacionForm();
	}

	public function getNewEntityInstance(){
		$oEvaluacion = new Evaluacion();
		
		return $oEvaluacion;
	}

	protected function getEntityManager(){
		return ManagerFactory::getEvaluacionManager();
	}



	



}
