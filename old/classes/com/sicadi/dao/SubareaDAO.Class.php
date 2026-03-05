<?php

/**
 * DAO para subarea
 *  
 * @author Marcos
 * @since 26-06-2023
 */
class subareaDAO extends EntityDAO {

	public function getTableName(){
		return CYT_TABLE_SUBAREA;
	}
	
	public function getEntityFactory(){
		return new SubareaFactory();
	}
	
	public function getFieldsToAdd($entity){
		
		
	}
	
	
	
	public function getIdFieldName(){
		return "cd_subarea";
	}
	
	
	
	
	

	
	
}
?>