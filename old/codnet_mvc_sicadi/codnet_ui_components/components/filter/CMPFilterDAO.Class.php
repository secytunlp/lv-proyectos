<?php

/**
 * DAO para CMPFilter
 *
 * @author Bernardo
 * @since 22-05-2013
 */
class CMPFilterDAO extends GenericDAO {

	public function getFieldsToUpdate($entity){
		
		return $this->getFieldsToAdd($entity);
	}
	
	public function getId($entity){
		return $entity->getId();
	}
	
	public function getIdFieldName(){
		return "filter_id";
	}
	
	public function setId($entity, $id){
		
		$entity->setId($id);
	}	
	
	public function getTableName(){
		return CMP_TABLE_CMPFILTER;
	}

	public function getEntityFactory(){
		return new CPMFilterFactory();
	}
	
	public function getFieldsToAdd($entity){

		$fieldsValues = array();

		$fieldsValues["cd_user"] = $this->formatIfNull( $entity->getCd_user(), "null" );
		$fieldsValues["filter_name"] = $this->formatString( $entity->getName() );
		$fieldsValues["filter_values"] = $this->formatString( $entity->getFieldsValuesToPersist() );
		$fieldsValues["filter_id"] = $this->formatString( $entity->getId() );
		
		

		return $fieldsValues;
	}
	
}
?>