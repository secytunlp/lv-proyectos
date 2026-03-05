<?php

/**
 * Manager para PuntajeCargo
 *  
 * @author Marcos
 * @since 05-12-2013
 */
class PuntajeCargoManager extends EntityManager{

	public function getDAO(){
		return DAOFactory::getPuntajeCargoDAO();
	}

}
?>
