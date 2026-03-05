<?php

/**
 * Manager para AntjustificacionMaximo
 *  
 * @author Marcos
 * @since 27-05-2014
 */
class AntjustificacionMaximoManager extends EntityManager{

	public function getDAO(){
		return DAOFactory::getAntjustificacionMaximoDAO();
	}

}
?>
