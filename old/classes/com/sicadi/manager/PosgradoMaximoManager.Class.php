<?php

/**
 * Manager para PosgradoMaximo
 *  
 * @author Marcos
 * @since 22-05-2014
 */
class PosgradoMaximoManager extends EntityManager{

	public function getDAO(){
		return DAOFactory::getPosgradoMaximoDAO();
	}

}
?>
