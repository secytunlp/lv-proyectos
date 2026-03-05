<?php

/**
 * Manager para Proyecto
 *  
 * @author Marcos
 * @since 14-08-2023
 */
class ProyectoAgenciaManager extends EntityManager{

	public function getDAO(){
		return DAOFactory::getProyectoAgenciaDAO();
	}


	
	
	
}
?>
