<?php

/**
 * Manager para PuntajeAntjustificacion
 *  
 * @author Marcos
 * @since 28-05-2014
 */
class PuntajeAntjustificacionManager extends EntityManager{

	public function getDAO(){
		return DAOFactory::getPuntajeAntjustificacionDAO();
	}
	
	public function add(Entity $entity) {
		
		parent::add($entity);
	}

}
?>
