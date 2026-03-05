<?php

/**
 * DAO para Otros Proyecto
 *
 * @author Marcos
 * @since 04-04-2023
 */
class OtrosProyectoDAO extends EntityDAO {

	public function getTableName(){
		return CYT_TABLE_SOLICITUD_PROYECTO;
	}

	public function getEntityFactory(){
		return new OtrosProyectoFactory();
	}
	
	public function getFieldsToAdd($entity){
		$fieldsValues = array();
		
		$fieldsValues["cd_solicitud"] = $this->formatIfNull( $entity->getSolicitud()->getOid(), 'null' );
		$fieldsValues["cd_proyecto"] = $this->formatIfNull( $entity->getProyecto()->getOid(), 'null' );
		$fieldsValues["ds_titulo"] = $this->formatString( $entity->getDs_titulo() );
		$fieldsValues["ds_director"] = $this->formatString( $entity->getDs_director() );
        $fieldsValues["ds_organismo"] = $this->formatString( $entity->getDs_organismo() );
		$fieldsValues["ds_codigo"] = $this->formatString( $entity->getDs_codigo() );
		$fieldsValues["bl_agregado"] = $this->formatIfNull( $entity->getBl_agregado(), '0' );
		$fieldsValues["dt_desde"] = $this->formatDate( $entity->getDt_desdeproyecto() );
		$fieldsValues["dt_hasta"] = $this->formatDate( $entity->getDt_hastaproyecto() );
        $fieldsValues["ds_archivo"] = $this->formatString( $entity->getDs_archivo() );
		
		return $fieldsValues;
		
	}
	
	public function getFromToSelect(){
		
		$tOtrosProyecto = $this->getTableName();
		
		$tSolicitud = DAOFactory::getSolicitudDAO()->getTableName();
		
		$tProyecto = CYTSecureDAOFactory::getProyectoDAO()->getTableName();
		
		
		$tTipoEstadoProyecto = CYTSecureDAOFactory::getTipoEstadoProyectoDAO()->getTableName();
		
		$tDisciplina = CYTSecureDAOFactory::getDisciplinaDAO()->getTableName();
		$tEspecialidad = CYTSecureDAOFactory::getEspecialidadDAO()->getTableName();
		
        $sql  = parent::getFromToSelect();
        $sql .= " LEFT JOIN " . $tSolicitud . " ON($tOtrosProyecto.cd_solicitud = $tSolicitud.cd_solicitud)";
       
        $sql .= " LEFT JOIN " . $tProyecto . " ON($tOtrosProyecto.cd_proyecto = $tProyecto.cd_proyecto)";
       
        $sql .= " LEFT JOIN " . $tTipoEstadoProyecto . " ON($tProyecto.cd_estado = $tTipoEstadoProyecto.cd_estado)";
        
        $sql .= " LEFT JOIN " . $tDisciplina . " ON($tProyecto.cd_disciplina = $tDisciplina.cd_disciplina)";
        $sql .= " LEFT JOIN " . $tEspecialidad . " ON($tProyecto.cd_especialidad = $tEspecialidad.cd_especialidad)";
        
      
       
        return $sql;
	}
	
	public function getFieldsToSelect(){
		
		
		
		$fields = parent::getFieldsToSelect();
		
        
        $tSolicitud = DAOFactory::getSolicitudDAO()->getTableName();
        $fields[] = "$tSolicitud.cd_solicitud as " . $tSolicitud . "_oid ";
        
        $tProyecto = CYTSecureDAOFactory::getProyectoDAO()->getTableName();
        $fields[] = "$tProyecto.cd_proyecto as " . $tProyecto . "_oid ";
        $fields[] = "$tProyecto.ds_abstract1 as " . $tProyecto . "_ds_abstract1 ";
        
        
        $tTipoEstadoProyecto = CYTSecureDAOFactory::getTipoEstadoProyectoDAO()->getTableName();
        $fields[] = "$tTipoEstadoProyecto.cd_estado as " . $tTipoEstadoProyecto . "_oid ";
        $fields[] = "$tTipoEstadoProyecto.ds_estado as " . $tTipoEstadoProyecto . "_ds_estado ";
        
        $tDisciplina = CYTSecureDAOFactory::getDisciplinaDAO()->getTableName();
        $fields[] = "$tDisciplina.cd_disciplina as " . $tDisciplina . "_oid ";
        $fields[] = "$tDisciplina.ds_disciplina as " . $tDisciplina . "_ds_disciplina ";
        
        $tEspecialidad = CYTSecureDAOFactory::getEspecialidadDAO()->getTableName();
        $fields[] = "$tEspecialidad.cd_especialidad as " . $tEspecialidad . "_oid ";
        $fields[] = "$tEspecialidad.ds_especialidad as " . $tEspecialidad . "_ds_especialidad ";
       
               
        return $fields;
	}	
	
	
	public function deleteOtrosProyectoPorSolicitud($solicitud_oid, $idConn=0) {
    	
        $db = CdtDbManager::getConnection( $idConn );

        
        
        $tableName = $this->getTableName();

        $sql = "DELETE FROM $tableName WHERE cd_solicitud = ? ";

        CdtUtils::log($sql, __CLASS__,LoggerLevel::getLevelDebug());
        
        $stmt = $db->sql_prepare($sql);

		// Verifica si la preparación fue exitosa.
		if ($stmt) {
			// Bind del parámetro de ID.
			mysqli_stmt_bind_param($stmt, 'i', $solicitud_oid);

			if (!$db->sql_execute($stmt)) {
				throw new DBException($db->sql_error());
			}

			// Cierra la sentencia preparada.
			mysqli_stmt_free_result($stmt);
			$stmt->close();

		} else {
			throw new DBException("Error de preparación de consulta.");
		}
    }
}
?>