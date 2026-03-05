<?php

/**
 * DAO para CargoPlanilla
 *  
 * @author Marcos
 * @since 05-12-2013
 */
class CargoPlanillaDAO extends EntityDAO {

	public function getTableName(){
		return CYT_TABLE_CARGO_PLANILLA;
	}
	
	public function getEntityFactory(){
		return new CargoPlanillaFactory();
	}
	
	public function getFieldsToAdd($entity){
		
		
	}
	
	
	
	public function getIdFieldName(){
		return "cd_cargoplanilla";
	}
	
	
	
	
	

	
	
}
?>