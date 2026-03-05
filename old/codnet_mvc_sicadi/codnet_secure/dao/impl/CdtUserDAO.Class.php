<?php 

/** 
 * DAO para CdtUser 
 *  
 * @author codnet archetype builder
 * @since 09-11-2011
 */ 
class CdtUserDAO implements ICdtUserDAO{ 

	/**
	 * se persiste la nueva entity
	 * @param CdtUser $oCdtUser entity a persistir.
	 */
	public function addCdtUser(CdtUser $oCdtUser) { 
		$db = CdtDbManager::getConnection(); 

		$ds_username = $oCdtUser->getDs_username();
		
		$ds_name = $oCdtUser->getDs_name();
		
		$ds_email = $oCdtUser->getDs_email();
		
		$ds_password = $oCdtUser->getDs_password();
		
		$ds_phone = $oCdtUser->getDs_phone();
		
		$ds_address = $oCdtUser->getDs_address();
		
		$cd_usergroup =  CdtFormatUtils::ifEmpty( $oCdtUser->getCd_usergroup(), 'null' );
		
		$ds_ips = $oCdtUser->getDs_ips();
		
		$tableName = CDT_SECURE_TABLE_CDTUSER;
		$field_ds_username = CDT_SECURE_TABLE_CDTUSER_DS_USERNAME;
		$field_ds_name = CDT_SECURE_TABLE_CDTUSER_DS_NAME;
		$field_ds_email = CDT_SECURE_TABLE_CDTUSER_DS_EMAIL;
		$field_ds_password = CDT_SECURE_TABLE_CDTUSER_DS_PASSWORD;
		$field_ds_phone = CDT_SECURE_TABLE_CDTUSER_DS_PHONE;
		$field_ds_address = CDT_SECURE_TABLE_CDTUSER_DS_ADDRESS;
		$field_cd_usergroup = CDT_SECURE_TABLE_CDTUSER_CD_USERGROUP;
		$field_ds_ips = CDT_SECURE_TABLE_CDTUSER_DS_IPS;
		
		// Definimos la consulta SQL con marcadores de posición (?)
		$sql = "INSERT INTO $tableName ($field_ds_username, 
										$field_ds_name, 
										$field_ds_email, 
										$field_ds_password, 
										$field_cd_usergroup, 
										$field_ds_phone, 
										$field_ds_address, 
										$field_ds_ips) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

		// Preparamos la consulta
		$stmt = $db->sql_prepare($sql);

		if ($stmt) {
			// Vinculamos los valores como parámetros
			$stmt->bind_param("ssssisss", $ds_username, $ds_name, $ds_email, $ds_password, $cd_usergroup, $ds_phone, $ds_address, $ds_ips);

			// Ejecutamos la consulta
			if ($db->sql_execute($stmt)) {
				//seteamos el nuevo id.
				$cd = $db->sql_nextid();
				$oCdtUser->setCd_user($cd);
			} else {
				throw new DBException($db->sql_error());
			}

			$stmt->close();
		} else {
			throw new DBException("Error de preparación de consulta.");
		}
	}


	
	/**
	 * se modifica la entity
	 * @param CdtUser $oCdtUser entity a modificar.
	 */
	public function updateCdtUser(CdtUser $oCdtUser) { 
		$db = CdtDbManager::getConnection(); 

		
		$ds_username = $oCdtUser->getDs_username();
		
		$ds_name = $oCdtUser->getDs_name();
		
		$ds_email = $oCdtUser->getDs_email();
		
		$ds_phone = $oCdtUser->getDs_phone();
		
		$ds_address = $oCdtUser->getDs_address();
		
		$cd_user = CdtFormatUtils::ifEmpty( $oCdtUser->getCd_user(), 'null' );
		
		$cd_usergroup = CdtFormatUtils::ifEmpty( $oCdtUser->getCd_usergroup(), 'null' );
		
		$ds_ips = $oCdtUser->getDs_ips();
		
		$nu_attemps = $oCdtUser->getNu_attemps();
		
		$tableName = CDT_SECURE_TABLE_CDTUSER;
		$field_cd_user = CDT_SECURE_TABLE_CDTUSER_CD_USER;
		$field_ds_username = CDT_SECURE_TABLE_CDTUSER_DS_USERNAME;
		$field_ds_name = CDT_SECURE_TABLE_CDTUSER_DS_NAME;
		$field_ds_email = CDT_SECURE_TABLE_CDTUSER_DS_EMAIL;
		$field_ds_phone = CDT_SECURE_TABLE_CDTUSER_DS_PHONE;
		$field_ds_address = CDT_SECURE_TABLE_CDTUSER_DS_ADDRESS;
		$field_cd_usergroup = CDT_SECURE_TABLE_CDTUSER_CD_USERGROUP;
		$field_ds_ips = CDT_SECURE_TABLE_CDTUSER_DS_IPS;
		$field_nu_attemps = CDT_SECURE_TABLE_CDTUSER_NU_ATTEMPS;
		
		// Definimos la consulta SQL con marcadores de posición (?)
		$sql = "UPDATE $tableName SET $field_nu_attemps = ?, 
										$field_ds_username = ?, 
										$field_ds_name = ?, 
										$field_ds_email = ?, 
										$field_cd_usergroup = ?, 
										$field_ds_phone = ?, 
										$field_ds_address = ?, 
										$field_ds_ips = ? WHERE $field_cd_user = ?";

		// Preparamos la consulta SQL
		$stmt = $db->sql_prepare($sql);

		if ($stmt) {
			// Vinculamos los valores como parámetros
			$stmt->bind_param("isssisssi", $nu_attemps, $ds_username, $ds_name, $ds_email, $cd_usergroup, $ds_phone, $ds_address, $ds_ips, $cd_user);

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
	 * se modifica la entity
	 * @param CdtUser $oCdtUser entity a modificar.
	 */
	public function updateAttemps(CdtUser $oCdtUser) { 

		$db = CdtDbManager::getConnection(); 
		
		$cd_user = CdtFormatUtils::ifEmpty( $oCdtUser->getCd_user(), 'null' );
		
		$nu_attemps = $oCdtUser->getNu_attemps();
		
		$tableName = CDT_SECURE_TABLE_CDTUSER;
		$field_cd_user = CDT_SECURE_TABLE_CDTUSER_CD_USER;
		$field_nu_attemps = CDT_SECURE_TABLE_CDTUSER_NU_ATTEMPS;
		
		// Definimos la consulta SQL con marcadores de posición (?)
		$sql = "UPDATE $tableName SET $field_nu_attemps = ? WHERE $field_cd_user = ?";

		// Preparamos la consulta SQL
		$stmt = $db->sql_prepare($sql);

		if ($stmt) {
			// Vinculamos los valores como parámetros
			$stmt->bind_param("ii", $nu_attemps, $cd_user);

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
	 * se modifica la password del usuario entity
	 * @param CdtUser $oCdtUser entity a modificar.
	 */
	public function updatePassword(CdtUser $oCdtUser) { 
		
		$db = CdtDbManager::getConnection(); 

		$ds_password = $oCdtUser->getDs_password();
		$cd_user = CdtFormatUtils::ifEmpty( $oCdtUser->getCd_user(), 'null' );
		
		
		$tableName = CDT_SECURE_TABLE_CDTUSER;
		$field_cd_user = CDT_SECURE_TABLE_CDTUSER_CD_USER;
		$field_ds_password = CDT_SECURE_TABLE_CDTUSER_DS_PASSWORD;
		
		
		// Definimos la consulta SQL con marcadores de posición (?)
		$sql = "UPDATE $tableName SET $field_ds_password = ? WHERE $field_cd_user = ?";
		
		// Preparamos la consulta SQL
		$stmt = $db->sql_prepare($sql);
		
		if ($stmt) {
			// Vinculamos los valores como parámetros
			$stmt->bind_param("si", $ds_password, $cd_user);
		
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
	 * @param CdtUser $oCdtUser entity a eliminar.
	 */
	public function deleteCdtUser(CdtUser $oCdtUser) { 
		$db = CdtDbManager::getConnection(); 

		$cd_user = $oCdtUser->getCd_user();

		$tableName = CDT_SECURE_TABLE_CDTUSER;
		$field_cd_user = CDT_SECURE_TABLE_CDTUSER_CD_USER;
		
		 // Definimos la consulta SQL con marcadores de posición (?)
		$sql = "DELETE FROM $tableName WHERE $field_cd_user = ?";
		
		// Preparamos la consulta SQL
		$stmt = $db->sql_prepare($sql);
		
		if ($stmt) {
			// Vinculamos el valor como parámetro
			$stmt->bind_param("i", $cd_user);
		
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
	 * @return ItemCollection[CdtUser]
	 */
	public function getCdtUsers(CdtSearchCriteria $oCriteria) { 
		
		$db = CdtDbManager::getConnection(); 

		$tableNameUser = CDT_SECURE_TABLE_CDTUSER;
		$tableNameUserGroup = CDT_SECURE_TABLE_CDTUSERGROUP;

		$user_field_cd_usergroup = CDT_SECURE_TABLE_CDTUSER_CD_USERGROUP;
		
		$usergroup_field_cd_usergroup = CDT_SECURE_TABLE_CDTUSERGROUP_CD_USERGROUP;
		$usergroup_field_ds_usergroup = CDT_SECURE_TABLE_CDTUSERGROUP_DS_USERGROUP;
		
		$sql = "SELECT U.*, UG.$usergroup_field_cd_usergroup as UG_cd_usergroup, UG.$usergroup_field_ds_usergroup as UG_ds_usergroup FROM $tableNameUser U ";
		$sql .= " LEFT JOIN $tableNameUserGroup UG ON(UG.$usergroup_field_cd_usergroup=U.$user_field_cd_usergroup) ";
		 

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
		

			
			$items = CdtResultFactory::toCollection($db, $stmt->get_result(), new CdtUserFactory("", "UG"));
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
	public function getCdtUsersCount(CdtSearchCriteria $oCriteria) { 
		$db = CdtDbManager::getConnection(); 

		$tableNameUser = CDT_SECURE_TABLE_CDTUSER;
		$tableNameUserGroup = CDT_SECURE_TABLE_CDTUSERGROUP;
		
		$user_field_cd_usergroup = CDT_SECURE_TABLE_CDTUSER_CD_USERGROUP;
		
		$usergroup_field_cd_usergroup = CDT_SECURE_TABLE_CDTUSERGROUP_CD_USERGROUP;
		
		$sql = "SELECT count(*) as count FROM $tableNameUser U ";
		$sql .= " LEFT JOIN $tableNameUserGroup UG ON(UG.$usergroup_field_cd_usergroup=U.$user_field_cd_usergroup) ";
		
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
	 * @return CdtUser
	 */
	public function getCdtUser(CdtSearchCriteria $oCriteria) { 

		$db = CdtDbManager::getConnection(); 

		$tableName = CDT_SECURE_TABLE_CDTUSER;
		
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
			
			$factory = new CdtUserFactory();

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

	/**
	 * se obtiene un user con su grupo dado el filtro de bï¿½squeda
	 * @param CdtSearchCriteria $oCriteria filtro de bï¿½squeda.
	 * @return CdtUser
	 */
	public function getCdtUserWithUserGroup( CdtSearchCriteria $oCriteria ) { 

		$db = CdtDbManager::getConnection(); 

		$tableNameUser = CDT_SECURE_TABLE_CDTUSER;
		$tableNameUserGroup = CDT_SECURE_TABLE_CDTUSERGROUP;
		
		$user_field_cd_usergroup = CDT_SECURE_TABLE_CDTUSER_CD_USERGROUP;
		
		$usergroup_field_cd_usergroup = CDT_SECURE_TABLE_CDTUSERGROUP_CD_USERGROUP;
		$usergroup_field_ds_usergroup = CDT_SECURE_TABLE_CDTUSERGROUP_DS_USERGROUP;
		
		$sql = "SELECT U.*, UG.$usergroup_field_cd_usergroup as UG_cd_usergroup, UG.$usergroup_field_ds_usergroup as UG_ds_usergroup FROM $tableNameUser U ";
		$sql .= " LEFT JOIN $tableNameUserGroup UG ON(UG.$usergroup_field_cd_usergroup=U.$user_field_cd_usergroup) ";
		 
		
		$sql .= $oCriteria->buildCriteria();
		$result = $db->sql_query($sql);
		
		if (!$result)//hubo un error en la bbdd.
			throw new DBException($db->sql_error());

		if ($db->sql_numrows() > 0) {
			
			$temp = $db->sql_fetchassoc($result);
			$factory = new CdtUserFactory("", "UG");
			$oUser = $factory->build($temp);
			
		}else {$oUser = null;}
		
		$db->sql_freeresult($result);
		return $oUser;
	}

	public function existUsername( CdtUser $oUser ) {
		$db = CdtDbManager::getConnection(); 

		$ds_username = $oUser->getDs_username();
		
		$tableNameUser = CDT_SECURE_TABLE_CDTUSER;
		$field_cd_user = CDT_SECURE_TABLE_CDTUSER_CD_USER;
		$field_ds_username = CDT_SECURE_TABLE_CDTUSER_DS_USERNAME;
		
		$sql = "select count(*) as count FROM $tableNameUser WHERE $field_ds_username ='$ds_username'";
		
		$cd_user = $oUser->getCd_user();
		if(!empty($cd_user))
			$sql .= " AND $field_cd_user <> $cd_user";
		
		
		$result = $db->sql_query ( $sql );
		if(!$result)//hubo un error en la bbdd.
			throw new DBException($db->sql_error());
		
		$next = $db->sql_fetchassoc ( $result );		 
		$count = $next['count'];
		$db->sql_freeresult($result);
		return ($count > 0);
	}
	
	public function existEmail( CdtUser $oUser ) {
		$db = CdtDbManager::getConnection(); 

		$tableNameUser = CDT_SECURE_TABLE_CDTUSER;
		$field_cd_user = CDT_SECURE_TABLE_CDTUSER_CD_USER;
		$field_ds_email = CDT_SECURE_TABLE_CDTUSER_DS_EMAIL;
		
		$ds_email = $oUser->getDs_email();
		$sql = "select count(*) as count FROM $tableNameUser WHERE $field_ds_email ='$ds_email'";
		
		$cd_user = $oUser->getCd_user();
		if(!empty($cd_user))
			$sql .= " AND $field_cd_user <> $cd_user";
		
		
		$result = $db->sql_query ( $sql );
		if(!$result)//hubo un error en la bbdd.
			throw new DBException($db->sql_error());
		
		$next = $db->sql_fetchassoc ( $result );		 
		$count = $next['count'];
		$db->sql_freeresult($result);
		return ($count > 0);
	}
} 
?>
