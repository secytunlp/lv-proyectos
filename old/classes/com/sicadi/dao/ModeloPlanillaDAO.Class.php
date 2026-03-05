<?php

/**
 * DAO para ModeloPlanilla
 *  
 * @author Marcos
 * @since 04-12-2013
 */
class ModeloPlanillaDAO extends EntityDAO {

	public function getTableName(){
		return CYT_TABLE_MODELO_PLANILLA;
	}
	
	public function getEntityFactory(){
		return new ModeloPlanillaFactory();
	}
	
	public function getFieldsToAdd($entity){
		
		
	}
	
	
	
	public function getIdFieldName(){
		return "cd_modeloplanilla";
	}
	
public function getFromToSelect(){
		
		$tModeloPlanilla = $this->getTableName();
		
		
		$tPeriodo = CYTSecureDAOFactory::getPeriodoDAO()->getTableName();
		
		
        $sql  = parent::getFromToSelect();
       
        
        $sql .= " LEFT JOIN " . $tPeriodo . " ON($tModeloPlanilla.cd_periodo = $tPeriodo.cd_periodo)";
       
        
        return $sql;
	}
	
	public function getFieldsToSelect(){
		
		
		
		$tPeriodo = CYTSecureDAOFactory::getPeriodoDAO()->getTableName();
		
		
		$fields = parent::getFieldsToSelect();
        
        
        
        $fields[] = "$tPeriodo.cd_periodo as " . $tPeriodo . "_oid ";
        $fields[] = "$tPeriodo.ds_periodo as " . $tPeriodo . "_ds_periodo ";
        
       
        
        return $fields;
	}
	
	
	

	
	
}
?>