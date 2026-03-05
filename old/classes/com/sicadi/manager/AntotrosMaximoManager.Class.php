<?php

/**
 * Manager para AntotrosMaximo
 *  
 * @author Marcos
 * @since 27-05-2014
 */
class AntotrosMaximoManager extends EntityManager{

	public function getDAO(){
		return DAOFactory::getAntotrosMaximoDAO();
	}

}
?>
