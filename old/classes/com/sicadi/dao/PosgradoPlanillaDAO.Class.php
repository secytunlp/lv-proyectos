<?php

/**
 * DAO para PosgradoPlanilla
 *  
 * @author Marcos
 * @since 22-05-2014
 */
class PosgradoPlanillaDAO extends EntityDAO {

	public function getTableName(){
		return CYT_TABLE_POSGRADOPLANILLA;
	}
	
	public function getEntityFactory(){
		return new PosgradoPlanillaFactory();
	}
	
	public function getFieldsToAdd($entity){
		
		
	}
	
	
	
	public function getIdFieldName(){
		return "cd_posgradoplanilla";
	}
	
	
	
	
	

	
	
}
?>