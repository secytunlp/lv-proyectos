<?php

/**
 * Manager para SubanteriorMaximo
 *  
 * @author Marcos
 * @since 31-08-2017
 */
class SubanteriorMaximoManager extends EntityManager{

	public function getDAO(){
		return DAOFactory::getSubanteriorMaximoDAO();
	}

}
?>
