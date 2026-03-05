<?php

/**
 * Manager para AntproduccionMaximo
 *  
 * @author Marcos
 * @since 28-05-2014
 */
class AntproduccionMaximoManager extends EntityManager{

	public function getDAO(){
		return DAOFactory::getAntproduccionMaximoDAO();
	}

}
?>
