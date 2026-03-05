<?php

/**
 * DAO para Solicitud Cargo
 *
 * @author Marcos
 * @since 12-06-2023
 */
class SolicitudCargoDAO extends EntityDAO {

	public function getTableName(){
		return CYT_TABLE_SOLICITUD_CARGO;
	}

	public function getEntityFactory(){
		return new SolicitudCargoFactory();
	}
	
	public function getFieldsToAdd($entity){
		$fieldsValues = array();
		
		$fieldsValues["cd_solicitud"] = $this->formatIfNull( $entity->getSolicitud()->getOid(), 'null' );
		$fieldsValues["cd_cargo"] = $this->formatIfNull( $entity->getCargo()->getOid(), 'null' );
        $fieldsValues["cd_deddoc"] = $this->formatIfNull( $entity->getdeddoc()->getOid(), 'null' );
        $fieldsValues["cd_facultad"] = $this->formatIfNull( $entity->getfacultad()->getOid(), 'null' );

		$fieldsValues["bl_agregado"] = $this->formatIfNull( $entity->getBl_agregado(), '0' );
		$fieldsValues["dt_fecha"] = $this->formatDate( $entity->getDt_fecha() );

        $fieldsValues["ds_archivo"] = $this->formatString( $entity->getDs_archivo() );
        $fieldsValues["bl_seleccionado"] = $this->formatIfNull( $entity->getBl_seleccionado(),0 );
        $fieldsValues["situacion"] = $this->formatString( $entity->getSituacion() );
		return $fieldsValues;
		
	}
	
	public function getFromToSelect(){
		
		$tSolicituCargo = $this->getTableName();
		
		$tSolicitud = DAOFactory::getSolicitudDAO()->getTableName();
		
		$tCargo = CYTSecureDAOFactory::getCargoDAO()->getTableName();
        $tDeddoc = CYTSecureDAOFactory::getDeddocDAO()->getTableName();
        $tFacultad = CYTSecureDAOFactory::getFacultadDAO()->getTableName();
		
		

		
        $sql  = parent::getFromToSelect();
        $sql .= " LEFT JOIN " . $tSolicitud . " ON($tSolicituCargo.cd_solicitud = $tSolicitud.cd_solicitud)";
       
        $sql .= " LEFT JOIN " . $tCargo . " ON($tSolicituCargo.cd_cargo = $tCargo.cd_cargo)";
        $sql .= " LEFT JOIN " . $tFacultad . " ON($tSolicituCargo.cd_facultad = $tFacultad.cd_facultad)";
        $sql .= " LEFT JOIN " . $tDeddoc . " ON($tSolicituCargo.cd_deddoc = $tDeddoc.cd_deddoc)";
       

      
       
        return $sql;
	}
	
	public function getFieldsToSelect(){
		
		
		
		$fields = parent::getFieldsToSelect();
		
        
        $tSolicitud = DAOFactory::getSolicitudDAO()->getTableName();
        $fields[] = "$tSolicitud.cd_solicitud as " . $tSolicitud . "_oid ";
        
        $tCargo = CYTSecureDAOFactory::getCargoDAO()->getTableName();
        $fields[] = "$tCargo.cd_cargo as " . $tCargo . "_oid ";
        $fields[] = "$tCargo.ds_cargo as " . $tCargo . "_ds_cargo ";

        $tDeddoc = CYTSecureDAOFactory::getDeddocDAO()->getTableName();
        $fields[] = "$tDeddoc.cd_deddoc as " . $tDeddoc . "_oid ";
        $fields[] = "$tDeddoc.ds_deddoc as " . $tDeddoc . "_ds_deddoc ";

        $tFacultad = CYTSecureDAOFactory::getFacultadDAO()->getTableName();
        $fields[] = "$tFacultad.cd_facultad as " . $tFacultad . "_oid ";
        $fields[] = "$tFacultad.ds_facultad as " . $tFacultad . "_ds_facultad ";
        
        

       
               
        return $fields;
	}	
	
	
	public function deleteSolicitudCargoPorSolicitud($solicitud_oid, $idConn=0) {
    	
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