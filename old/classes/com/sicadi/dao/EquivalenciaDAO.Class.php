<?php

/**
 * DAO para Equivalencia
 *  
 * @author Marcos
 * @since 10-04-2023
 */
class EquivalenciaDAO extends EntityDAO {

	public function getTableName(){
		return CYT_TABLE_EQUIVALENCIA;
	}
	
	public function getEntityFactory(){
		return new EquivalenciaFactory();
	}
	
	public function getFieldsToAdd($entity){
		
		
	}
	
	
	
	public function getIdFieldName(){
		return "cd_equivalencia";
	}
	
	
	
	
	

	
	
}
?>