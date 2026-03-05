<?php

/**
 * Manager para AntotrosPlanilla
 *  
 * @author Marcos
 * @since 27-05-2014
 */
class AntotrosPlanillaManager extends EntityManager{

	public function getDAO(){
		return DAOFactory::getAntotrosPlanillaDAO();
	}

}
?>
