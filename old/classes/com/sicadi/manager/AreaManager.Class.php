<?php

/**
 * Manager para Area
 *  
 * @author Marcos
 * @since 26-06-2023
 */
class AreaManager extends EntityManager{

	public function getDAO(){
		return DAOFactory::getAreaDAO();
	}

}
?>
