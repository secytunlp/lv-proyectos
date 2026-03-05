<?php

/**
 * DAO para PlanMaximo
 *  
 * @author Marcos
 * @since 05-12-2013
 */
class PlanMaximoDAO extends EntityDAO {

	public function getTableName(){
		return CYT_TABLE_PLAN_MAXIMO;
	}
	
	public function getEntityFactory(){
		return new PlanMaximoFactory();
	}
	
	public function getFieldsToAdd($entity){
		
		
	}
	
	
	
	public function getIdFieldName(){
		return "cd_planmaximo";
	}
	
	
	
	
	

	
	
}
?>