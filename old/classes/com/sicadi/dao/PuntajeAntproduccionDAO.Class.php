<?php

/**
 * DAO para PuntajeAntproduccion
 *  
 * @author Marcos
 * @since 28-05-2014
 */
class PuntajeAntproduccionDAO extends EntityDAO {

	public function getTableName(){
		return CYT_TABLE_PUNTAJE_ANTPRODUCCION;
	}
	
	public function getEntityFactory(){
		return new PuntajeAntproduccionFactory();
	}
	
	public function getFieldsToAdd($entity){
		
		$fieldsValues = array();
		$fieldsValues["cd_evaluacion"] = $this->formatIfNull( $entity->getEvaluacion()->getOid(), 'null' );
		$fieldsValues["cd_modeloplanilla"] = $this->formatIfNull( $entity->getModeloplanilla()->getOid(), 'null' );
		$fieldsValues["cd_antproduccionmaximo"] = $this->formatIfNull( $entity->getAntproduccionmaximo()->getOid(), 'null' );	
		$fieldsValues["nu_cant"] =$this->formatIfNull( $entity->getNu_cant(), '0' );
		$fieldsValues["nu_puntaje"] = $this->formatString( $entity->getNu_puntaje() );
		return $fieldsValues;
	}
	
	
	
	public function getIdFieldName(){
		return "cd_puntajeantproduccion";
	}
	
	public function getFromToSelect(){
		$tPuntajeAntproduccion = $this->getTableName();
		$tAntproduccionMaximo = DAOFactory::getAntproduccionMaximoDAO()->getTableName();
		$tEvaluacion = CYTSecureDAOFactory::getEvaluacionDAO()->getTableName();
		$tModeloPlanilla = DAOFactory::getModeloPlanillaDAO()->getTableName();
		
		$sql  = parent::getFromToSelect();
		
		$sql .= " LEFT JOIN " . $tAntproduccionMaximo . " ON($tPuntajeAntproduccion.cd_antproduccionmaximo = $tAntproduccionMaximo.cd_antproduccionmaximo)";
		$sql .= " LEFT JOIN " . $tEvaluacion . " ON($tPuntajeAntproduccion.cd_evaluacion = $tEvaluacion.cd_evaluacion)";
		$sql .= " LEFT JOIN " . $tModeloPlanilla . " ON($tPuntajeAntproduccion.cd_modeloplanilla = $tModeloPlanilla.cd_modeloplanilla)";
		
		 return $sql;
	}
	
	public function getFieldsToSelect(){
		
		$fields = parent::getFieldsToSelect();
		
		$tAntproduccionMaximo = DAOFactory::getAntproduccionMaximoDAO()->getTableName();
        $fields[] = "$tAntproduccionMaximo.cd_antproduccionmaximo as " . $tAntproduccionMaximo . "_oid ";
        $fields[] = "$tAntproduccionMaximo.nu_max as " . $tAntproduccionMaximo . "_nu_max ";
        
        $tEvaluacion = CYTSecureDAOFactory::getEvaluacionDAO()->getTableName();
        $fields[] = "$tEvaluacion.cd_evaluacion as " . $tEvaluacion . "_oid ";
        
		$tModeloPlanilla = DAOFactory::getModeloPlanillaDAO()->getTableName();
        $fields[] = "$tModeloPlanilla.cd_modeloplanilla as " . $tModeloPlanilla . "_oid ";
        
         return $fields;
	}
	
	
	public function deletePuntajeAntproduccionPorEvaluacion($evaluacion_oid, $idConn=0) {
    	
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