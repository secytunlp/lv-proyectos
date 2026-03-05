<?php

/**
 * Manager para Categoria
 *  
 * @author Marcos
 * @since 07-06-2023
 */
class CategoriasicadiManager extends EntityManager{

	public function getDAO(){
		return DAOFactory::getCategoriasicadiDAO();
	}

}
?>
