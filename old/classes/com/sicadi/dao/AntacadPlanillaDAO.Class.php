<?php

/**
 * DAO para AntacadPlanilla
 *  
 * @author Marcos
 * @since 23-05-2014
 */
class AntacadPlanillaDAO extends EntityDAO {

	public function getTableName(){
		return CYT_TABLE_ANTACAD_PLANILLA;
	}
	
	public function getEntityFactory(){
		return new AntacadPlanillaFactory();
	}
	
	public function getFieldsToAdd($entity){
		
		
	}
	
	
	
	public function getIdFieldName(){
		return "cd_antacadplanilla";
	}
	
	
	
	
	

	
	
}
?>