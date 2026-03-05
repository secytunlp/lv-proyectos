<?php

/**
 * Manager para SubanteriorPlanilla
 *  
 * @author Marcos
 * @since 31-08-2017
 */
class SubanteriorPlanillaManager extends EntityManager{

	public function getDAO(){
		return DAOFactory::getSubanteriorPlanillaDAO();
	}

}
?>
