<?php

/**
 * Manager para AntacadPlanilla
 *  
 * @author Marcos
 * @since 23-05-2014
 */
class AntacadPlanillaManager extends EntityManager{

	public function getDAO(){
		return DAOFactory::getAntacadPlanillaDAO();
	}

}
?>
