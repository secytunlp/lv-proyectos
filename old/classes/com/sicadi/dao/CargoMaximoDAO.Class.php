<?php

/**
 * DAO para CargoMaximo
 *  
 * @author Marcos
 * @since 05-12-2013
 */
class CargoMaximoDAO extends EntityDAO {

	public function getTableName(){
		return CYT_TABLE_CARGO_MAXIMO;
	}
	
	public function getEntityFactory(){
		return new CargoMaximoFactory();
	}
	
	public function getFieldsToAdd($entity){
		
		
	}
	
	
	
	public function getIdFieldName(){
		return "cd_cargomaximo";
	}
	
	public function getFromToSelect(){
		$tCargoMaximo = $this->getTableName();
		$tCargoPlanilla = DAOFactory::getCargoPlanillaDAO()->getTableName();
		
		$sql  = parent::getFromToSelect();
		
		$sql .= " LEFT JOIN " . $tCargoPlanilla . " ON($tCargoMaximo.cd_cargoplanilla = $tCargoPlanilla.cd_cargoplanilla)";
		
		 return $sql;
	}
	
	public function getFieldsToSelect(){
		
		$fields = parent::getFieldsToSelect();
		
		$tCargoPlanilla = DAOFactory::getCargoPlanillaDAO()->getTableName();
        $fields[] = "$tCargoPlanilla.cd_cargoplanilla as " . $tCargoPlanilla . "_oid ";
        $fields[] = "$tCargoPlanilla.ds_cargoplanilla as " . $tCargoPlanilla . "_ds_cargoplanilla ";
        
         return $fields;
	}
	

	
	
}
?>