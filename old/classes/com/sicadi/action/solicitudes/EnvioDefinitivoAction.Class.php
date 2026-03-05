<?php

/**
 * Acción para envío definitivo.
 *
 * @author Marcos
 * @since 21-03-2014
 *
 */
class EnvioDefinitivoAction extends CdtEditAsyncAction {

	
    protected function getEntity() {
        $entity = null;

    	$ids = explode(",", CdtUtils::getParam('id'));
        $manager = $this->getEntityManager();
        if (!empty($ids)) {
            $oCriteria = new CdtSearchCriteria();
            foreach ($ids as $solicitud_oid) {
                $oCriteria->addFilter('cd_solicitud', $solicitud_oid, '=', null, "OR");
            }
            
            $solicitudes = $manager->getEntities($oCriteria);
          
        } else {

            $solicitudes = new ItemCollection();
        }
        
		
		
    	/*$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('solicitud_oid', $entity->getOid(), '=');
		$oCriteria->addNull('fechaHasta');
		$managerSolicitudEstado =  CYTSecureManagerFactory::getSolicitudEstadoManager();
		$oSolicitudEstado = $managerSolicitudEstado->getEntity($oCriteria);
		if (($oSolicitudEstado->getEstado()->getOid()!=CYT_ESTADO_SOLICITUD_RECIBIDA)) {
			
			throw new GenericException( CYT_MSG_SOLICITUD_ADMITIR_PROHIBIDO);
		}*/
		
		return $solicitudes;
    }

    /**
     * (non-PHPdoc)
     * @see CdtEditAsyncAction::edit();
     */
    protected function edit($entities) {
    	foreach ($entities as $entity) {
				$oCriteria = new CdtSearchCriteria();
			$oCriteria->addFilter('solicitud_oid', $entity->getOid(), '=');
			$oCriteria->addNull('fechaHasta');
			$managerSolicitudEstado =  CYTSecureManagerFactory::getSolicitudEstadoManager();
			$oSolicitudEstado = $managerSolicitudEstado->getEntity($oCriteria);
			$confirmar = (($oSolicitudEstado->getEstado()->getOid()==CYT_ESTADO_SOLICITUD_RECIBIDA))?1:0;
			if ($confirmar) {
				$this->getEntityManager()->confirm($entity,11);
			}

    			
    	}
        
    }
    
	/**
	 * (non-PHPdoc)
	 * @see classes/com/gestion/action/entities/DeleteEntityAction::getEntityManager()
	 */
	protected function getEntityManager(){
		return ManagerFactory::getSolicitudManager();
	}


}