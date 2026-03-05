<?php

/**
 * Manager para AntacadMaximo
 *  
 * @author Marcos
 * @since 23-05-2014
 */
class AntacadMaximoManager extends EntityManager{

	public function getDAO(){
		return DAOFactory::getAntacadMaximoDAO();
	}

}
?>
