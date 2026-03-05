<?php

/**
 * Manager para PuntajeAntacad
 *  
 * @author Marcos
 * @since 23-05-2014
 */
class PuntajeAntacadManager extends EntityManager{

	public function getDAO(){
		return DAOFactory::getPuntajeAntacadDAO();
	}
	
	public function add(Entity $entity) {
		
		parent::add($entity);
	}

}
?>
