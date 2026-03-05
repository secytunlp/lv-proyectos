<?php

/**
 * Manager para PuntajeAntotros
 *  
 * @author Marcos
 * @since 27-05-2014
 */
class PuntajeAntotrosManager extends EntityManager{

	public function getDAO(){
		return DAOFactory::getPuntajeAntotrosDAO();
	}
	
	public function add(Entity $entity) {
		
		parent::add($entity);
	}

}
?>
