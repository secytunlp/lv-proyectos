<?php

/**
 * DAO para PuntajeAntjustificacion
 *  
 * @author Marcos
 * @since 27-05-2014
 */
class PuntajeAntjustificacionDAO extends EntityDAO {

	public function getTableName(){
		return CYT_TABLE_PUNTAJE_ANTJUSTIFICACION;
	}
	
	public function getEntityFactory(){
		return new PuntajeAntjustificacionFactory();
	}
	
	public function getFieldsToAdd($entity){
		
		$fieldsValues = array();
		$fieldsValues["cd_evaluacion"] = $this->formatIfNull( $entity->getEvaluacion()->getOid(), 'null' );
		$fieldsValues["cd_modeloplanilla"] = $this->formatIfNull( $entity->getModeloplanilla()->getOid(), 'null' );
		$fieldsValues["cd_antjustificacionmaximo"] = $this->formatIfNull( $entity->getAntjustificacionmaximo()->getOid(), 'null' );	
		$fieldsValues["nu_puntaje"] = $this->formatString( $entity->getNu_puntaje() );
		return $fieldsValues;
	}
	
	
	
	public function getIdFieldName(){
		return "cd_puntajeantjustificacion";
	}
	
	public function getFromToSelect(){
		$tPuntajeAntjustificacion = $this->getTableName();
		$tAntjustificacionMaximo = DAOFactory::getAntjustificacionMaximoDAO()->getTableName();
		$tEvaluacion = CYTSecureDAOFactory::getEvaluacionDAO()->getTableName();
		$tModeloPlanilla = DAOFactory::getModeloPlanillaDAO()->getTableName();
		
		$sql  = parent::getFromToSelect();
		
		$sql .= " LEFT JOIN " . $tAntjustificacionMaximo . " ON($tPuntajeAntjustificacion.cd_antjustificacionmaximo = $tAntjustificacionMaximo.cd_antjustificacionmaximo)";
		$sql .= " LEFT JOIN " . $tEvaluacion . " ON($tPuntajeAntjustificacion.cd_evaluacion = $tEvaluacion.cd_evaluacion)";
		$sql .= " LEFT JOIN " . $tModeloPlanilla . " ON($tPuntajeAntjustificacion.cd_modeloplanilla = $tModeloPlanilla.cd_modeloplanilla)";
		
		 return $sql;
	}
	
	public function getFieldsToSelect(){
		
		$fields = parent::getFieldsToSelect();
		
		$tAntjustificacionMaximo = DAOFactory::getAntjustificacionMaximoDAO()->getTableName();
        $fields[] = "$tAntjustificacionMaximo.cd_antjustificacionmaximo as " . $tAntjustificacionMaximo . "_oid ";
        $fields[] = "$tAntjustificacionMaximo.nu_max as " . $tAntjustificacionMaximo . "_nu_max ";
        
        $tEvaluacion = CYTSecureDAOFactory::getEvaluacionDAO()->getTableName();
        $fields[] = "$tEvaluacion.cd_evaluacion as " . $tEvaluacion . "_oid ";
        
		$tModeloPlanilla = DAOFactory::getModeloPlanillaDAO()->getTableName();
        $fields[] = "$tModeloPlanilla.cd_modeloplanilla as " . $tModeloPlanilla . "_oid ";
        
         return $fields;
	}
	
	
	public function deletePuntajeAntjustificacionPorEvaluacion($evaluacion_oid, $idConn=0) {
    	
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