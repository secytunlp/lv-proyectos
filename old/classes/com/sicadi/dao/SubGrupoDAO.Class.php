<?php

/**
 * DAO para SubGrupo
 *  
 * @author Marcos
 * @since 27-05-2014
 */
class SubGrupoDAO extends EntityDAO {

	public function getTableName(){
		return CYT_TABLE_SUBGRUPO;
	}
	
	public function getEntityFactory(){
		return new SubGrupoFactory();
	}
	
	public function getFieldsToAdd($entity){
		
		
	}
	
	
	
	public function getIdFieldName(){
		return "cd_subgrupo";
	}
	
	
	
	
	

	
	
}
?>