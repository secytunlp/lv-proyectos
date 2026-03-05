<?php

/**
 * Manager para AntproduccionPlanilla
 *  
 * @author Marcos
 * @since 28-05-2014
 */
class AntproduccionPlanillaManager extends EntityManager{

	public function getDAO(){
		return DAOFactory::getAntproduccionPlanillaDAO();
	}

}
?>
