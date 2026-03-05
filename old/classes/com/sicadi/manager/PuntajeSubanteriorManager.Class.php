<?php

/**
 * Manager para PuntajeSubanterior
 *  
 * @author Marcos
 * @since 31-08-2017
 */
class PuntajeSubanteriorManager extends EntityManager{

	public function getDAO(){
		return DAOFactory::getPuntajeSubanteriorDAO();
	}
	
	public function add(Entity $entity) {
		
		parent::add($entity);
	}

}
?>
