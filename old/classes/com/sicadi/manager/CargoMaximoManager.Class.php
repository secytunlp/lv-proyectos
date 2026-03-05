<?php

/**
 * Manager para CargoMaximo
 *  
 * @author Marcos
 * @since 05-12-2013
 */
class CargoMaximoManager extends EntityManager{

	public function getDAO(){
		return DAOFactory::getCargoMaximoDAO();
	}

}
?>
