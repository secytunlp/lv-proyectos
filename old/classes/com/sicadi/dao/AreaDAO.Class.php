<?php

/**
 * DAO para area
 *  
 * @author Marcos
 * @since 26-06-2023
 */
class AreaDAO extends EntityDAO {

	public function getTableName(){
		return CYT_TABLE_AREA;
	}
	
	public function getEntityFactory(){
		return new AreaFactory();
	}
	
	public function getFieldsToAdd($entity){
		
		
	}
	
	
	
	public function getIdFieldName(){
		return "cd_area";
	}
	
	
	
	
	

	
	
}
?>