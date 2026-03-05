<?php

/**
 * Manager para PuntajeAntproduccion
 *  
 * @author Marcos
 * @since 28-05-2014
 */
class PuntajeAntproduccionManager extends EntityManager{

	public function getDAO(){
		return DAOFactory::getPuntajeAntproduccionDAO();
	}
	
	public function add(Entity $entity) {
		
		parent::add($entity);
	}

}
?>
