<?php

/**
 * Acción para enviar una evaluacion.
 *
 * @author Marcos
 * @since 05-06-2014
 *
 */
class SendEvaluacionAction extends CdtEditAsyncAction {

	
    protected function getEntity() {
        $entity = null;

		//recuperamos dado su identifidor.
		$oid = CdtUtils::getParam('id');
			
		if (!empty( $oid )) {
			$oUser = CdtSecureUtils::getUserLogged();
			$oCriteria = new CdtSearchCriteria();
			$oCriteria->addFilter('cd_solicitud', $oid, '=');
			$oCriteria->addFilter('cd_usuario', $oUser->getCd_user(), '=');
			$oCriteria->addNull('fechaHasta');
			$manager = $this->getEntityManager();
			$entity = $manager->getEntity($oCriteria);	
		}else{
		
			$entity = parent::getEntity();
		
		}
		
    	$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('evaluacion_oid', $entity->getOid(), '=');
		$oCriteria->addNull('fechaHasta');
		$managerEvaluacionEstado =  CYTSecureManagerFactory::getEvaluacionEstadoManager();
		$oEvaluacionEstado = $managerEvaluacionEstado->getEntity($oCriteria);
		if (($oEvaluacionEstado->getEstado()->getOid()!=CYT_ESTADO_SOLICITUD_EN_EVALUACION)) {
			
			throw new GenericException( CYT_MSG_EVALUACION_MODIFICAR_PROHIBIDO);
		}
		
		
		return $entity;
    }

    /**
     * (non-PHPdoc)
     * @see CdtEditAsyncAction::edit();
     */
    protected function edit($entity) {
        $this->getEntityManager()->send($entity);
    }
    
	/**
	 * (non-PHPdoc)
	 * @see classes/com/gestion/action/entities/DeleteEntityAction::getEntityManager()
	 */
	protected function getEntityManager(){
		return ManagerFactory::getEvaluacionManager();
	}


}