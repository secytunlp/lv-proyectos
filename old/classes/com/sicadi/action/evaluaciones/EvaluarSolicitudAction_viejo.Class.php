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
