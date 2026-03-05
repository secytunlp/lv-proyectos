<?php

/**
 * DAO para PuntajePosgrado
 *  
 * @author Marcos
 * @since 22-05-2014
 */
class PuntajePosgradoDAO extends EntityDAO {

	public function getTableName(){
		return CYT_TABLE_PUNTAJE_POSGRADO;
	}
	
	public function getEntityFactory(){
		return new PuntajePosgradoFactory();
	}
	
	public function getFieldsToAdd($entity){
		
		$fieldsValues = array();
		$fieldsValues["cd_evaluacion"] = $this->formatIfNull( $entity->getEvaluacion()->getOid(), 'null' );
		$fieldsValues["cd_modeloplanilla"] = $this->formatIfNull( $entity->getModeloplanilla()->getOid(), 'null' );
		$fieldsValues["cd_posgradomaximo"] = $this->formatIfNull( $entity->getPosgradomaximo()->getOid(), 'null' );	
		
		return $fieldsValues;
	}
	
	
	
	public function getIdFieldName(){
		return "cd_puntajeposgrado";
	}
	
	public function getFromToSelect(){
		$tPuntajePosgrado = $this->getTableName();
		$tPosgradoMaximo = DAOFactory::getPosgradoMaximoDAO()->getTableName();
		$tEvaluacion = CYTSecureDAOFactory::getEvaluacionDAO()->getTableName();
		$tModeloPlanilla = DAOFactory::getModeloPlanillaDAO()->getTableName();
		
		$sql  = parent::getFromToSelect();
		
		$sql .= " LEFT JOIN " . $tPosgradoMaximo . " ON($tPuntajePosgrado.cd_posgradomaximo = $tPosgradoMaximo.cd_posgradomaximo)";
		$sql .= " LEFT JOIN " . $tEvaluacion . " ON($tPuntajePosgrado.cd_evaluacion = $tEvaluacion.cd_evaluacion)";
		$sql .= " LEFT JOIN " . $tModeloPlanilla . " ON($tPuntajePosgrado.cd_modeloplanilla = $tModeloPlanilla.cd_modeloplanilla)";
		
		 return $sql;
	}
	
	public function getFieldsToSelect(){
		
		$fields = parent::getFieldsToSelect();
		
		$tPosgradoMaximo = DAOFactory::getPosgradoMaximoDAO()->getTableName();
        $fields[] = "$tPosgradoMaximo.cd_posgradomaximo as " . $tPosgradoMaximo . "_oid ";
        $fields[] = "$tPosgradoMaximo.nu_max as " . $tPosgradoMaximo . "_nu_max ";
        
        $tEvaluacion = CYTSecureDAOFactory::getEvaluacionDAO()->getTableName();
        $fields[] = "$tEvaluacion.cd_evaluacion as " . $tEvaluacion . "_oid ";
        
		$tModeloPlanilla = DAOFactory::getModeloPlanillaDAO()->getTableName();
        $fields[] = "$tModeloPlanilla.cd_modeloplanilla as " . $tModeloPlanilla . "_oid ";
        
         return $fields;
	}
	
	
	public function deletePuntajePosgradoPorEvaluacion($evaluacion_oid, $idConn=0) {
    	
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