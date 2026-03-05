<?php

/**
 * Manager para ModeloPlanilla
 *  
 * @author Marcos
 * @since 04-12-2013
 */
class ModeloPlanillaManager extends EntityManager{

	public function getDAO(){
		return DAOFactory::getModeloPlanillaDAO();
	}

}
?>
