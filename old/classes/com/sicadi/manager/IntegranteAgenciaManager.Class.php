<?php

/**
 * Manager para Integrante
 *  
 * @author Marcos
 * @since 14-08-2023
 */
class IntegranteAgenciaManager extends EntityManager{

	public function getDAO(){
		return DAOFactory::getIntegranteAgenciaDAO();
	}


	
	
	
}
?>
