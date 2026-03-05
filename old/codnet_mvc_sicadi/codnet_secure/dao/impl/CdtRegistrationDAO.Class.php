<?php 

/** 
 * DAO para CdtRegistration 
 *  
 * @author codnet archetype builder
 * @since 09-11-2011
 */ 
class CdtRegistrationDAO implements ICdtRegistrationDAO{ 

	/**
	 * se persiste la nueva entity 
	 * @param CdtRegistration $oCdtRegistration entity a persistir.
	 */
	public function addCdtRegistration(CdtRegistration $oCdtRegistration) { 

		$db = CdtDbManager::getConnection(); 
		
		$ds_activationcode = $oCdtRegistration->getDs_activationcode();
		
		$dt_date = $oCdtRegistration->getDt_date();
		
		$ds_username = $oCdtRegistration->getDs_username();
		
		$ds_name = $oCdtRegistration->getDs_name();
		
		$ds_email = $oCdtRegistration->getDs_email();
		
		$ds_password = $oCdtRegistration->getDs_password();
		
		$ds_phone = $oCdtRegistration->getDs_phone();
		
		$ds_address = $oCdtRegistration->getDs_address();
		
		$tableName = CDT_SECURE_TABLE_CDTREGISTRATION;
		$field_ds_activationdate = CDT_SECURE_TABLE_CDTREGISTRATION_DS_ACTIVATIONCODE;
		$field_dt_date = CDT_SECURE_TABLE_CDTREGISTRATION_DT_DATE;
		$field_ds_username = CDT_SECURE_TABLE_CDTREGISTRATION_DS_USERNAME;
		$field_ds_name = CDT_SECURE_TABLE_CDTREGISTRATION_DS_NAME;
		$field_ds_email = CDT_SECURE_TABLE_CDTREGISTRATION_DS_EMAIL;
		$field_ds_password = CDT_SECURE_TABLE_CDTREGISTRATION_DS_PASSWORD;
		$field_ds_phone = CDT_SECURE_TABLE_CDTREGISTRATION_DS_PHONE;
		$field_ds_address = CDT_SECURE_TABLE_CDTREGISTRATION_DS_ADDRESS;
		
		// Definimos la consulta SQL con marcadores de posición (?)
		$sql = "INSERT INTO $tableName ($field_ds_activationcode, 
										$field_dt_date, 
										$field_ds_username, 
										$field_ds_name, 
										$field_ds_email, 
										$field_ds_password, 
										$field_ds_phone, 
										$field_ds_address) 
				VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
		
		// Preparamos la consulta SQL
		$stmt = $db->sql_prepare($sql);
		
		if ($stmt) {
			// Vinculamos los valores como parámetros
			$stmt->bind_param("ssssssss", $ds_activationcode, $dt_date, $ds_username, $ds_name, $ds_email, $ds_password, $ds_phone, $ds_address);
		
			// Ejecutamos la consulta
			if ($db->sql_execute($stmt)) {
				$stmt->close();
			} else {
				throw new DBException($db->sql_error());
			}
		} else {
			throw new DBException("Error de preparación de consulta.");
		}

		// Seteamos el nuevo ID
		$cd = $db->sql_nextid();
		$oCdtRegistration->setCd_registration($cd);
        
	}


	/**
	 * se modifica la entity
	 * @param CdtRegistration $oCdtRegistration entity a modificar.
	 */
	public function updateCdtRegistration(CdtRegistration $oCdtRegistration) { 
		$db = CdtDbManager::getConnection(); 

		
		$ds_activationcode = $oCdtRegistration->getDs_activationcode();
		
		$dt_date = $oCdtRegistration->getDt_date();
		
		$ds_username = $oCdtRegistration->getDs_username();
		
		$ds_name = $oCdtRegistration->getDs_name();
		
		$ds_email = $oCdtRegistration->getDs_email();
		
		$ds_password = $oCdtRegistration->getDs_password();
		
		$ds_phone = $oCdtRegistration->getDs_phone();
		
		$ds_address = $oCdtRegistration->getDs_address();
		
		$cd_registration = CdtFormatUtils::ifEmpty( $oCdtRegistration->getCd_registration(), 'null' );
		
		
		$tableName = CDT_SECURE_TABLE_CDTREGISTRATION;
		$field_cd_registration = CDT_SECURE_TABLE_CDTREGISTRATION_CD_REGISTRATION;
		$field_ds_activationdate = CDT_SECURE_TABLE_CDTREGISTRATION_DS_ACTIVATIONCODE;
		$field_dt_date = CDT_SECURE_TABLE_CDTREGISTRATION_DT_DATE;
		$field_ds_username = CDT_SECURE_TABLE_CDTREGISTRATION_DS_USERNAME;
		$field_ds_name = CDT_SECURE_TABLE_CDTREGISTRATION_DS_NAME;
		$field_ds_email = CDT_SECURE_TABLE_CDTREGISTRATION_DS_EMAIL;
		$field_ds_password = CDT_SECURE_TABLE_CDTREGISTRATION_DS_PASSWORD;
		$field_ds_phone = CDT_SECURE_TABLE_CDTREGISTRATION_DS_PHONE;
		$field_ds_address = CDT_SECURE_TABLE_CDTREGISTRATION_DS_ADDRESS;
		
		// Definimos la consulta SQL con marcadores de posición (?)
		$sql = "UPDATE $tableName SET $field_ds_activationdate = ?, 
										$field_dt_date = ?, 
										$field_ds_username = ?, 
										$field_ds_name = ?, 
										$field_ds_email = ?, 
										$field_ds_password = ?, 
										$field_ds_phone = ?, 
										$field_ds_address = ? 
				WHERE $field_cd_registration = ?";

		// Preparamos la consulta SQL
		$stmt = $db->sql_prepare($sql);

		if ($stmt) {
			// Vinculamos los valores como parámetros
			$stmt->bind_param("ssssssssi", $ds_activationcode, $dt_date, $ds_username, $ds_name, $ds_email, $ds_password, $ds_phone, $ds_address, $cd_registration);

			// Ejecutamos la consulta
			if ($db->sql_execute($stmt)) {
				$stmt->close();
			} else {
				throw new DBException($db->sql_error());
			}
		} else {
			throw new DBException("Error de preparación de consulta.");
		}

	}

	/**
	 * se elimina la entity
	 * @param CdtRegistration $oCdtRegistration entity a eliminar.
	 */
	public function deleteCdtRegistration(CdtRegistration $oCdtRegistration) { 
		$db = CdtDbManager::getConnection(); 

		$cd_registration = $oCdtRegistration->getCd_registration();

		$tableName = CDT_SECURE_TABLE_CDTREGISTRATION;
		$field_cd_registration = CDT_SECURE_TABLE_CDTREGISTRATION_CD_REGISTRATION;
		
		// Definimos la consulta SQL con marcadores de posición (?)
		$sql = "DELETE FROM $tableName WHERE $field_cd_registration = ?";

		// Preparamos la consulta SQL
		$stmt = $db->sql_prepare($sql);

		if ($stmt) {
			// Vinculamos el valor como parámetro
			$stmt->bind_param("i", $cd_registration);

			// Ejecutamos la consulta
			if ($db->sql_execute($stmt)) {
				$stmt->close();
			} else {
				throw new DBException($db->sql_error());
			}
		} else {
			throw new DBException("Error de preparación de consulta.");
		}

	}

	/**
	 * se obtiene una colecciï¿½n de entities dado el filtro de bï¿½squeda
	 * @param CdtSearchCriteria $oCriteria filtro de bï¿½squeda.
	 * @return ItemCollection[CdtRegistration]
	 */
	public function getCdtRegistrations(CdtSearchCriteria $oCriteria) { 
		
		$db = CdtDbManager::getConnection(); 

		$tableName = CDT_SECURE_TABLE_CDTREGISTRATION;

		$sql = "SELECT * FROM $tableName ";
		  // Obtén la cláusula WHERE y los parámetros vinculados
		$criteriaParams = $oCriteria->buildCriteria();
		$sql .= $criteriaParams['where'];
		$params = $criteriaParams['params'];

		$stmt = $db->sql_prepare($sql);

		if ($stmt) {
			// Vincula los parámetros a la consulta preparada
			if (!empty($params)) {
				$paramTypes = CdtFormatUtils::getParamTypes($params);
				$paramValues = array_values($params);
				$stmt = CdtFormatUtils::bindParamsToStatement($stmt, $paramTypes, ...$paramValues);
			}

			if (!$db->sql_execute($stmt)) {
				throw new DBException($db->sql_error());
			}
		

			
			$items = CdtResultFactory::toCollection($db, $stmt->get_result(), new CdtRegistrationFactory);
			mysqli_stmt_free_result($stmt);
			$stmt->close();
			
			return $items;	
		} else {
			throw new DBException("Error de preparación de consulta.");
		}
		
	}

	
	/**
	 * se obtiene la cantidad de entities dado el filtro de bï¿½squeda
	 * @param CdtSearchCriteria $oCriteria filtro de bï¿½squeda.
	 * @return int
	 */
	public function getCdtRegistrationsCount(CdtSearchCriteria $oCriteria) { 
		$db = CdtDbManager::getConnection(); 

		$tableName = CDT_SECURE_TABLE_CDTREGISTRATION;
		
		$sql = "SELECT count(*) as count FROM $tableName "; 
		
		// Obtén la cláusula WHERE y los parámetros vinculados
		$criteriaParams = $oCriteria->buildCriteriaWithoutPaging();
		$sql .= $criteriaParams['where'];
		$params = $criteriaParams['params'];

		$stmt = $db->sql_prepare($sql);

		if ($stmt) {
			// Vincula los parámetros a la consulta preparada
			if (!empty($params)) {
				$paramTypes = CdtFormatUtils::getParamTypes($params);
				$paramValues = array_values($params);
				$stmt = CdtFormatUtils::bindParamsToStatement($stmt, $paramTypes, ...$paramValues);
			}

			if (!$db->sql_execute($stmt)) {
				throw new DBException($db->sql_error());
			}

			$result = $stmt->get_result();
			$next = $db->sql_fetchassoc($result);
			$cant = $next['count'];
			$db->sql_freeresult($result);

			return ((int) $cant);
		} else {
			throw new DBException("Error de preparación de consulta.");
		}
	}


	/**
	 * se obtiene un entity dado el filtro de bï¿½squeda
	 * @param CdtSearchCriteria $oCriteria filtro de bï¿½squeda.
	 * @return CdtRegistration
	 */
	public function getCdtRegistration(CdtSearchCriteria $oCriteria) { 

		$db = CdtDbManager::getConnection(); 

		$tableName = CDT_SECURE_TABLE_CDTREGISTRATION;
		
		$sql = "SELECT * FROM $tableName ";
		 
		 // Agregamos el filtro construido por el criterio de búsqueda.
		$criteriaParams = $oCriteria->buildCriteria();
		//CdtUtils::LogObject($criteriaParams);	
		$sql .= $criteriaParams['where'];
		$params = $criteriaParams['params'];
		//CdtUtils::Log($sql);	
		$stmt = $db->sql_prepare($sql);
		
		// Verificamos si la preparación fue exitosa.
		if ($stmt) {
			// Verificamos si hay parámetros para vincular.
			if (!empty($params)) {
				
				// Preparamos los tipos de parámetros para la función mysqli_stmt_bind_param.
				$paramTypes = CdtFormatUtils::getParamTypes($params);
				

				// Preparamos los valores para la vinculación.
				$paramValues = array_values($params);

				// Insertamos los parámetros en la consulta preparada.
				$stmt = CdtFormatUtils::bindParamsToStatement($stmt, $paramTypes, ...$paramValues);
			}

			

			if (!$db->sql_execute($stmt)) {
				throw new DBException($db->sql_error());
			}

			$obj=null;
			
			$factory = new CdtRegistrationFactory();

			if ($stmt_result = mysqli_stmt_get_result($stmt)) {
				$row = $db->sql_fetchassoc($stmt_result);
				
				if ($row) {
					// Si hay un resultado válido, crea el objeto
					$obj = $factory->build($row);
				}
			}
				mysqli_stmt_free_result($stmt);
				// Cerramos la sentencia preparada.
				$stmt->close();
				//CdtUtils::LogObject($obj);
				return $obj;
			} else {
				throw new DBException("Error de preparación de consulta.");
			}
		
	}

} 
?>
