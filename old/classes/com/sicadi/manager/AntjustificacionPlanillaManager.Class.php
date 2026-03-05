<?php

/**
 * Manager para AntjustificacionPlanilla
 *  
 * @author Marcos
 * @since 27-05-2014
 */
class AntjustificacionPlanillaManager extends EntityManager{

	public function getDAO(){
		return DAOFactory::getAntjustificacionPlanillaDAO();
	}

}
?>
