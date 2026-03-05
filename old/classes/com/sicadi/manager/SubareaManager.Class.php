<?php

/**
 * Manager para Subarea
 *  
 * @author Marcos
 * @since 26-06-2023
 */
class SubareaManager extends EntityManager{

	public function getDAO(){
		return DAOFactory::getSubareaDAO();
	}

}
?>
