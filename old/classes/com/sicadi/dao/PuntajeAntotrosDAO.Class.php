<?php

/**
 * DAO para PuntajeAntotros
 *  
 * @author Marcos
 * @since 27-05-2014
 */
class PuntajeAntotrosDAO extends EntityDAO {

	public function getTableName(){
		return CYT_TABLE_PUNTAJE_ANTOTROS;
	}
	
	public function getEntityFactory(){
		return new PuntajeAntotrosFactory();
	}
	
	public function getFieldsToAdd($entity){
		
		$fieldsValues = array();
		$fieldsValues["cd_evaluacion"] = $this->formatIfNull( $entity->getEvaluacion()->getOid(), 'null' );
		$fieldsValues["cd_modeloplanilla"] = $this->formatIfNull( $entity->getModeloplanilla()->getOid(), 'null' );
		$fieldsValues["cd_antotrosmaximo"] = $this->formatIfNull( $entity->getAntotrosmaximo()->getOid(), 'null' );	
		$fieldsValues["nu_puntaje"] = $this->formatString( $entity->getNu_puntaje() );
		return $fieldsValues;
	}
	
	
	
	public function getIdFieldName(){
		return "cd_puntajeantotros";
	}
	
	public function getFromToSelect(){
		$tPuntajeAntotros = $this->getTableName();
		$tAntotrosMaximo = DAOFactory::getAntotrosMaximoDAO()->getTableName();
		$tEvaluacion = CYTSecureDAOFactory::getEvaluacionDAO()->getTableName();
		$tModeloPlanilla = DAOFactory::getModeloPlanillaDAO()->getTableName();
		
		$sql  = parent::getFromToSelect();
		
		$sql .= " LEFT JOIN " . $tAntotrosMaximo . " ON($tPuntajeAntotros.cd_antotrosmaximo = $tAntotrosMaximo.cd_antotrosmaximo)";
		$sql .= " LEFT JOIN " . $tEvaluacion . " ON($tPuntajeAntotros.cd_evaluacion = $tEvaluacion.cd_evaluacion)";
		$sql .= " LEFT JOIN " . $tModeloPlanilla . " ON($tPuntajeAntotros.cd_modeloplanilla = $tModeloPlanilla.cd_modeloplanilla)";
		
		 return $sql;
	}
	
	public function getFieldsToSelect(){
		
		$fields = parent::getFieldsToSelect();
		
		$tAntotrosMaximo = DAOFactory::getAntotrosMaximoDAO()->getTableName();
        $fields[] = "$tAntotrosMaximo.cd_antotrosmaximo as " . $tAntotrosMaximo . "_oid ";
        $fields[] = "$tAntotrosMaximo.nu_max as " . $tAntotrosMaximo . "_nu_max ";
        
        $tEvaluacion = CYTSecureDAOFactory::getEvaluacionDAO()->getTableName();
        $fields[] = "$tEvaluacion.cd_evaluacion as " . $tEvaluacion . "_oid ";
        
		$tModeloPlanilla = DAOFactory::getModeloPlanillaDAO()->getTableName();
        $fields[] = "$tModeloPlanilla.cd_modeloplanilla as " . $tModeloPlanilla . "_oid ";
        
         return $fields;
	}
	
	
	public function deletePuntajeAntotrosPorEvaluacion($evaluacion_oid, $idConn=0) {
    	
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