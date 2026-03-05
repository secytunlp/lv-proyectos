<?php

/**
 * Manager para PuntajePosgrado
 *  
 * @author Marcos
 * @since 22-05-2014
 */
class PuntajePosgradoManager extends EntityManager{

	public function getDAO(){
		return DAOFactory::getPuntajePosgradoDAO();
	}
	
	public function add(Entity $entity) {
		
		parent::add($entity);
	}

}
?>
