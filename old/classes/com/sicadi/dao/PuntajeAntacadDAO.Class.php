<?php

/**
 * DAO para PuntajeAntacad
 *  
 * @author Marcos
 * @since 23-05-2014
 */
class PuntajeAntacadDAO extends EntityDAO {

	public function getTableName(){
		return CYT_TABLE_PUNTAJE_ANTACAD;
	}
	
	public function getEntityFactory(){
		return new PuntajeAntacadFactory();
	}
	
	public function getFieldsToAdd($entity){
		
		$fieldsValues = array();
		$fieldsValues["cd_evaluacion"] = $this->formatIfNull( $entity->getEvaluacion()->getOid(), 'null' );
		$fieldsValues["cd_modeloplanilla"] = $this->formatIfNull( $entity->getModeloplanilla()->getOid(), 'null' );
		$fieldsValues["cd_antacadmaximo"] = $this->formatIfNull( $entity->getAntacadmaximo()->getOid(), 'null' );	
		$fieldsValues["nu_puntaje"] = $this->formatString( $entity->getNu_puntaje() );
		return $fieldsValues;
	}
	
	
	
	public function getIdFieldName(){
		return "cd_puntajeantacad";
	}
	
	public function getFromToSelect(){
		$tPuntajeAntacad = $this->getTableName();
		$tAntacadMaximo = DAOFactory::getAntacadMaximoDAO()->getTableName();
		$tEvaluacion = CYTSecureDAOFactory::getEvaluacionDAO()->getTableName();
		$tModeloPlanilla = DAOFactory::getModeloPlanillaDAO()->getTableName();
		
		$sql  = parent::getFromToSelect();
		
		$sql .= " LEFT JOIN " . $tAntacadMaximo . " ON($tPuntajeAntacad.cd_antacadmaximo = $tAntacadMaximo.cd_antacadmaximo)";
		$sql .= " LEFT JOIN " . $tEvaluacion . " ON($tPuntajeAntacad.cd_evaluacion = $tEvaluacion.cd_evaluacion)";
		$sql .= " LEFT JOIN " . $tModeloPlanilla . " ON($tPuntajeAntacad.cd_modeloplanilla = $tModeloPlanilla.cd_modeloplanilla)";
		
		 return $sql;
	}
	
	public function getFieldsToSelect(){
		
		$fields = parent::getFieldsToSelect();
		
		$tAntacadMaximo = DAOFactory::getAntacadMaximoDAO()->getTableName();
        $fields[] = "$tAntacadMaximo.cd_antacadmaximo as " . $tAntacadMaximo . "_oid ";
        $fields[] = "$tAntacadMaximo.nu_max as " . $tAntacadMaximo . "_nu_max ";
        
        $tEvaluacion = CYTSecureDAOFactory::getEvaluacionDAO()->getTableName();
        $fields[] = "$tEvaluacion.cd_evaluacion as " . $tEvaluacion . "_oid ";
        
		$tModeloPlanilla = DAOFactory::getModeloPlanillaDAO()->getTableName();
        $fields[] = "$tModeloPlanilla.cd_modeloplanilla as " . $tModeloPlanilla . "_oid ";
        
         return $fields;
	}
	
	
	public function deletePuntajeAntacadPorEvaluacion($evaluacion_oid, $idConn=0) {
    	
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