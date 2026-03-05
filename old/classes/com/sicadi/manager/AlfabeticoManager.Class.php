<?php

/**
 * Manager para Alfabetico
 *  
 * @author Marcos
 * @since 30-10-2013
 */
class AlfabeticoManager extends EntityManager{

	public function getDAO(){
		return DAOFactory::getAlfabeticoDAO();
	}
	


}
?>
