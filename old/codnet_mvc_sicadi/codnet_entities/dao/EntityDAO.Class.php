<?php

/**
 * DAO para las entities
 *  
 * @author Bernardo
 * @since 05-03-2013
 */
abstract class EntityDAO extends GenericDAO {

	
	public function getFieldsToUpdate($entity){
		
		return $this->getFieldsToAdd($entity);
	}
	
	public function getId($entity){
		return $entity->getOid();
	}
	
	public function getIdFieldName(){
		return "oid";
	}
	
	public function setId($entity, $id){
		
		$entity->setOid($id);
	}
	
}
?>