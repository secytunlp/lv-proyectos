<?php

/**
 * Manager para PosgradoPlanilla
 *  
 * @author Marcos
 * @since 22-05-2014
 */
class PosgradoPlanillaManager extends EntityManager{

	public function getDAO(){
		return DAOFactory::getPosgradoPlanillaDAO();
	}

}
?>
