<?php

/**
 * Manager para PuntajeGrupo
 *  
 * @author Marcos
 * @since 06-12-2013
 */
class PuntajeGrupoManager extends EntityManager{

	public function getDAO(){
		return DAOFactory::getPuntajeGrupoDAO();
	}

}
?>
