<?php

/**
 * Acción para rectificar la solicitud
 *
 * @author Marcos
 * @since 07-07-2023
 *
 */
class RectifySolicitudAction extends AddEntityAction{

	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::edit();
	 */
	protected function edit( $entity ){
		
		$this->getEntityManager()->confirm($entity->getSolicitud(),1,$entity->getObservaciones() );
		$result["oid"] = $entity->getOid();		
		return $result;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see classes/com/gestion/action/entities/EditEntityAction::getNewFormInstance()
	 */
	public function getNewFormInstance(){
		return new CMPRectifySolicitudForm();
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
