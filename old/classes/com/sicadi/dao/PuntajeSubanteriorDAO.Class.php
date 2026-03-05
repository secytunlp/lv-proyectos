<?php

/**
 * DAO para PuntajeSubanterior
 *  
 * @author Marcos
 * @since 31-08-2017
 */
class PuntajeSubanteriorDAO extends EntityDAO {

	public function getTableName(){
		return CYT_TABLE_PUNTAJE_SUBANTERIOR;
	}
	
	public function getEntityFactory(){
		return new PuntajeSubanteriorFactory();
	}
	
	public function getFieldsToAdd($entity){
		
		$fieldsValues = array();
		$fieldsValues["cd_evaluacion"] = $this->formatIfNull( $entity->getEvaluacion()->getOid(), 'null' );
		$fieldsValues["cd_modeloplanilla"] = $this->formatIfNull( $entity->getModeloplanilla()->getOid(), 'null' );
		$fieldsValues["cd_subanteriormaximo"] = $this->formatIfNull( $entity->getSubanteriormaximo()->getOid(), 'null' );	
		$fieldsValues["nu_puntaje"] = $this->formatString( $entity->getNu_puntaje() );
		return $fieldsValues;
	}
	
	
	
	public function getIdFieldName(){
		return "cd_puntajesubanterior";
	}
	
	public function getFromToSelect(){
		$tPuntajeSubanterior = $this->getTableName();
		$tSubanteriorMaximo = DAOFactory::getSubanteriorMaximoDAO()->getTableName();
		$tEvaluacion = CYTSecureDAOFactory::getEvaluacionDAO()->getTableName();
		$tModeloPlanilla = DAOFactory::getModeloPlanillaDAO()->getTableName();
		
		$sql  = parent::getFromToSelect();
		
		$sql .= " LEFT JOIN " . $tSubanteriorMaximo . " ON($tPuntajeSubanterior.cd_subanteriormaximo = $tSubanteriorMaximo.cd_subanteriormaximo)";
		$sql .= " LEFT JOIN " . $tEvaluacion . " ON($tPuntajeSubanterior.cd_evaluacion = $tEvaluacion.cd_evaluacion)";
		$sql .= " LEFT JOIN " . $tModeloPlanilla . " ON($tPuntajeSubanterior.cd_modeloplanilla = $tModeloPlanilla.cd_modeloplanilla)";
		
		 return $sql;
	}
	
	public function getFieldsToSelect(){
		
		$fields = parent::getFieldsToSelect();
		
		$tSubanteriorMaximo = DAOFactory::getSubanteriorMaximoDAO()->getTableName();
        $fields[] = "$tSubanteriorMaximo.cd_subanteriormaximo as " . $tSubanteriorMaximo . "_oid ";
        $fields[] = "$tSubanteriorMaximo.nu_max as " . $tSubanteriorMaximo . "_nu_max ";
        
        $tEvaluacion = CYTSecureDAOFactory::getEvaluacionDAO()->getTableName();
        $fields[] = "$tEvaluacion.cd_evaluacion as " . $tEvaluacion . "_oid ";
        
		$tModeloPlanilla = DAOFactory::getModeloPlanillaDAO()->getTableName();
        $fields[] = "$tModeloPlanilla.cd_modeloplanilla as " . $tModeloPlanilla . "_oid ";
        
         return $fields;
	}
	
	
	public function deletePuntajeSubanteriorPorEvaluacion($evaluacion_oid, $idConn=0) {
    	
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