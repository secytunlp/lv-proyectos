<?php

/**
 * Acción para eliminar solicitudes.
 *
 * @author Marcos
 * @since 04-02-2014
 *
 */
class DeleteSolicitudAction extends DeleteEntityAction {

	/**
	 * (non-PHPdoc)
	 * @see classes/com/gestion/action/entities/DeleteEntityAction::getEntityManager()
	 */
	protected function getEntityManager(){
		return ManagerFactory::getSolicitudManager();
	}

	

}
