<?php

/**
 * DAO para Categoria
 *  
 * @author Marcos
 * @since 07-06-2023
 */
class CategoriasicadiDAO extends EntityDAO {

	public function getTableName(){
		return CYT_TABLE_CATEGORIA_SICADI;
	}
	
	public function getEntityFactory(){
		return new CategoriasicadiFactory();
	}
	
	public function getFieldsToAdd($entity){
		
		
	}
	
	
	
	public function getIdFieldName(){
		return "cd_categoriasicadi";
	}
	
	
	
	
	

	
	
}
?>