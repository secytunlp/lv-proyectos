<?php

/**
 * DAO para PuntajeCargo
 *  
 * @author Marcos
 * @since 05-12-2013
 */
class PuntajeCargoDAO extends EntityDAO {

	public function getTableName(){
		return CYT_TABLE_PUNTAJE_CARGO;
	}
	
	public function getEntityFactory(){
		return new PuntajeCargoFactory();
	}
	
	public function getFieldsToAdd($entity){
		$fieldsValues = array();
		$fieldsValues["cd_evaluacion"] = $this->formatIfNull( $entity->getEvaluacion()->getOid(), 'null' );
		$fieldsValues["cd_modeloplanilla"] = $this->formatIfNull( $entity->getModeloplanilla()->getOid(), 'null' );
		$fieldsValues["cd_cargomaximo"] = $this->formatIfNull( $entity->getCargomaximo()->getOid(), 'null' );	
		
		return $fieldsValues;
		
	}
	
	
	
	public function getIdFieldName(){
		return "cd_puntajecargo";
	}
	
	public function getFromToSelect(){
		$tPuntajeCargo = $this->getTableName();
		$tCargoMaximo = DAOFactory::getCargoMaximoDAO()->getTableName();
		
		$sql  = parent::getFromToSelect();
		
		$sql .= " LEFT JOIN " . $tCargoMaximo . " ON($tPuntajeCargo.cd_cargomaximo = $tCargoMaximo.cd_cargomaximo)";
		
		 return $sql;
	}
	
	public function getFieldsToSelect(){
		
		$fields = parent::getFieldsToSelect();
		
		$tCargoMaximo = DAOFactory::getCargoMaximoDAO()->getTableName();
        $fields[] = "$tCargoMaximo.cd_cargomaximo as " . $tCargoMaximo . "_oid ";
        
        
         return $fields;
	}
	
	public function deletePuntajeCargoPorEvaluacion($evaluacion_oid, $idConn=0) {
    	
        $db = CdtDbManager::getConnection( $idConn );

        
        
        $tableName = $this->getTableName();

        $sql = "DELETE FROM $tableName WHERE cd_evaluacion = $evaluacion_oid ";

        CdtUtils::log($sql, __CLASS__,LoggerLevel::getLevelDebug());
        
        $result = $db->sql_query($sql);
        if (!$result)//hubo un error en la bbdd.
            throw new DBException($db->sql_error());
    }
	
	
	

	
	
}
?>