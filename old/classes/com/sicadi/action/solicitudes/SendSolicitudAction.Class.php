<?php

/**
 * Acción para enviar una solicitud.
 *
 * @author Marcos
 * @since 24-02-2014
 *
 */
class SendSolicitudAction extends CdtEditAsyncAction {

	
    protected function getEntity() {
    	if (date('Y-m-d')>CYT_FECHA_CIERRE) {
			throw new GenericException( CYT_MSG_FIN_PERIODO );
		}
        $entity = null;

		//recuperamos dado su identifidor.
		$oid = CdtUtils::getParam('id');
			
		if (!empty( $oid )) {
						
			$manager = $this->getEntityManager();
			$entity = $manager->getEntityById($oid);
		}else{
		
			$entity = parent::getEntity();
		
		}
		
    	$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('solicitud_oid', $entity->getOid(), '=');
		$oCriteria->addNull('fechaHasta');
		$managerSolicitudEstado =  CYTSecureManagerFactory::getSolicitudEstadoManager();
		$oSolicitudEstado = $managerSolicitudEstado->getEntity($oCriteria);
		if (($oSolicitudEstado->getEstado()->getOid()!=CYT_ESTADO_SOLICITUD_CREADA)) {
			
			throw new GenericException( CYT_MSG_SOLICITUD_ENVIAR_PROHIBIDO);
		}
		
    	$oPeriodoManager = CYTSecureManagerFactory::getPeriodoManager();
		$oPerioActual = $oPeriodoManager->getPeriodoActual(CYT_PERIODO_YEAR);
		
		if (($entity->getPeriodo()->getOid()!=$oPerioActual->getOid())) {
			
			throw new GenericException( CYT_MSG_SOLICITUD_ENVIAR_PROHIBIDO_1);
		}
		
		//cargos
        $oCriteria = new CdtSearchCriteria();
        $oCriteria->addFilter('cd_solicitud', $entity->getOid(), '=');

        $oCargoManager =  ManagerFactory::getSolicitudCargoManager();
        $oCargos = $oCargoManager->getEntities($oCriteria);
        $cargosArray = new ItemCollection();
        foreach ($oCargos as $oCargo) {


            $cargosArray->addItem($oCargo);
        }
        $entity->setCargos( $cargosArray );
				
		//proyectos.
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_solicitud', $entity->getOid(), '=');
        $oCriteria->addFilter('bl_agregado', 0, '=');

		$proyectosActualesManager = ManagerFactory::getOtrosProyectoManager();
		$entity->setProyectos( $proyectosActualesManager->getEntities($oCriteria) );
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_solicitud', $entity->getOid(), '=');
        $oCriteria->addFilter('bl_agregado', 1, '=');
		$otrosProyectosManager = ManagerFactory::getOtrosProyectoManager();
		$entity->setOtrosProyectos( $otrosProyectosManager->getEntities($oCriteria) );
		
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
		return ManagerFactory::getSolicitudManager();
	}


}