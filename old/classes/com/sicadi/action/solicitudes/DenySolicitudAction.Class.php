<?php

/**
 * Acción para rechazar la solicitud
 *
 * @author Marcos
 * @since 21-03-2014
 *
 */
class DenySolicitudAction extends AddEntityAction{

	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::edit();
	 */
	protected function edit( $entity ){
		
		$this->getEntityManager()->confirm($entity->getSolicitud(),CYT_ESTADO_SOLICITUD_NO_ADMITIDA,$entity->getObservaciones() );
		$result["oid"] = $entity->getOid();		
		return $result;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see classes/com/gestion/action/entities/EditEntityAction::getNewFormInstance()
	 */
	public function getNewFormInstance(){
		return new CMPDenySolicitudForm();
	}

	/**
	 * (non-PHPdoc)
	 * @see classes/com/gestion/action/entities/EditEntityAction::getNewEntityInstance()
	 */
	public function getNewEntityInstance(){
		
		return new DenySolicitud();
	}

	protected function getEntityManager(){
		return ManagerFactory::getSolicitudManager();
	}



	


}
