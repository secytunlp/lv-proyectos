<?php

/**
 * Manager para Equivalencia
 *  
 * @author Marcos
 * @since 10-04-2023
 */
class EquivalenciaManager extends EntityManager{

	public function getDAO(){
		return DAOFactory::getEquivalenciaDAO();
	}

}
?>
