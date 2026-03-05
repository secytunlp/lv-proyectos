<?php

/**
 * DAO para PuntajeGrupo
 *  
 * @author Marcos
 * @since 06-12-2013
 */
class PuntajeGrupoDAO extends EntityDAO {

	public function getTableName(){
		return CYT_TABLE_PUNTAJE_GRUPO;
	}
	
	public function getEntityFactory(){
		return new PuntajeGrupoFactory();
	}
	
	public function getFieldsToAdd($entity){
		
		
	}
	
	
	
	public function getIdFieldName(){
		return "cd_puntajegrupo";
	}
	
	
	
	
	

	
	
}
?>