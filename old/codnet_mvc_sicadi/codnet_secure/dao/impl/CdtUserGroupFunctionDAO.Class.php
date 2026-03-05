<?php 

/** 
 * DAO para CdtUserGroupFunction 
 *  
 * @author codnet archetype builder
 * @since 09-11-2011
 */ 
class CdtUserGroupFunctionDAO implements ICdtUserGroupFunctionDAO{ 

	/**
	 * (non-PHPdoc)
	 * @see dao/ICdtUserGroupFunctionDAO::addCdtUserGroupFunction()
	 */
	public function addCdtUserGroupFunction(CdtUserGroupFunction $oCdtUserGroupFunction) { 
		
		$db = CdtDbManager::getConnection(); 
		
		$cd_usergroup =  CdtFormatUtils::ifEmpty( $oCdtUserGroupFunction->getCd_usergroup(), 'null' );
		
		$cd_function =  CdtFormatUtils::ifEmpty( $oCdtUserGroupFunction->getCd_function(), 'null' );
		
		$tableName = CDT_SECURE_TABLE_CDTUSERGROUPFUNCTION;
		$field_cd_usergroup = CDT_SECURE_TABLE_CDTUSERGROUPFUNCTION_CD_USERGROUP;
		$field_cd_function = CDT_SECURE_TABLE_CDTUSERGROUPFUNCTION_CD_FUNCTION;
				
		$sql = "INSERT INTO $tableName ($field_cd_usergroup, $field_cd_function) VALUES (?, ?)";
    
		// Preparamos la consulta
		$stmt = $db->sql_prepare($sql);
		
		if ($stmt) {
			// Vinculamos los valores como parámetros
			$stmt->bind_param("ii", $cd_usergroup, $cd_function); // "ii" indica que son enteros
			
			// Ejecutamos la consulta
			if ($db->sql_execute($stmt)) {
				$stmt->close();
			} else {
				throw new DBException($db->sql_error());
			}
		} else {
			throw new DBException("Error de preparación de consulta.");
		}
		
		// Establecemos el nuevo ID generado por la base de datos
		$cd = $db->sql_nextid();
		$oCdtUserGroupFunction->setCd_usergroup_function($cd);
	}


	/**
	 * (non-PHPdoc)
	 * @see dao/ICdtUserGroupFunctionDAO::updateCdtUserGroupFunction()
	 */
	public function updateCdtUserGroupFunction(CdtUserGroupFunction $oCdtUserGroupFunction) { 

		$db = CdtDbManager::getConnection(); 

		
		$cd_usergroup_function = CdtFormatUtils::ifEmpty( $oCdtUserGroupFunction->getCd_usergroup_function(), 'null' );
		
		$cd_usergroup = CdtFormatUtils::ifEmpty( $oCdtUserGroupFunction->getCd_usergroup(), 'null' );
		
		$cd_function = CdtFormatUtils::ifEmpty( $oCdtUserGroupFunction->getCd_function(), 'null' );
		

		$tableName = CDT_SECURE_TABLE_CDTUSERGROUPFUNCTION;
		$field_cd_usergroupfunction = CDT_SECURE_TABLE_CDTUSERGROUPFUNCTION_CD_USERGROUPFUNCTION;
		$field_cd_usergroup = CDT_SECURE_TABLE_CDTUSERGROUPFUNCTION_CD_USERGROUP;
		$field_cd_function = CDT_SECURE_TABLE_CDTUSERGROUPFUNCTION_CD_FUNCTION;
				
		// Definimos la consulta SQL con marcadores de posición (?)
		$sql = "UPDATE $tableName SET $field_cd_usergroup = ?, $field_cd_function = ? WHERE $field_cd_usergroupfunction = ?";

		// Preparamos la consulta
		$stmt = $db->sql_prepare($sql);

		if ($stmt) {
			// Vinculamos los valores como parámetros
			$stmt->bind_param("iii", $cd_usergroup, $cd_function, $cd_usergroup_function); // "iii" indica que son enteros

			// Ejecutamos la consulta
			if (!$db->sql_execute($stmt)) {
				throw new DBException($db->sql_error());
			}

			$stmt->close();
		} else {
			throw new DBException("Error de preparación de consulta.");
		}

	}

	/**
	 * (non-PHPdoc)
	 * @see dao/ICdtUserGroupFunctionDAO::deleteCdtUserGroupFunction()
	 */
	public function deleteCdtUserGroupFunction(CdtUserGroupFunction $oCdtUserGroupFunction) { 
		
		$db = CdtDbManager::getConnection(); 

		$cd_usergroup_function = $oCdtUserGroupFunction->getCd_usergroup_function();

		$tableName = CDT_SECURE_TABLE_CDTUSERGROUPFUNCTION;
		$field_cd_usergroupfunction = CDT_SECURE_TABLE_CDTUSERGROUPFUNCTION_CD_USERGROUPFUNCTION;
		
		// Definimos la consulta SQL con un marcador de posición (?)
		$sql = "DELETE FROM $tableName WHERE $field_cd_usergroupfunction = ?";

		// Preparamos la consulta
		$stmt = $db->sql_prepare($sql);

		if ($stmt) {
			// Vinculamos el valor como parámetro
			$stmt->bind_param("i", $cd_usergroup_function); // "i" indica que es un entero

			// Ejecutamos la consulta
			if (!$db->sql_execute($stmt)) {
				throw new DBException($db->sql_error());
			}

			$stmt->close();
		} else {
			throw new DBException("Error de preparación de consulta.");
		}

	}

	/**
	 * (non-PHPdoc)
	 * @see dao/ICdtUserGroupFunctionDAO::getCdtUserGroupFunctions()
	 */
	public function getCdtUserGroupFunctions(CdtSearchCriteria $oCriteria) { 
		
		$db = CdtDbManager::getConnection(); 

		$tableName = CDT_SECURE_TABLE_CDTUSERGROUPFUNCTION;
		$tableNameFunction = CDT_SECURE_TABLE_CDTFUNCTION;
		$tableNameUserGroup = CDT_SECURE_TABLE_CDTUSERGROUP;
		
		$field_cd_usergroup = CDT_SECURE_TABLE_CDTUSERGROUPFUNCTION_CD_USERGROUP;
		$field_cd_function = CDT_SECURE_TABLE_CDTUSERGROUPFUNCTION_CD_FUNCTION;
		
		$usergroup_field_cd_usergroup = CDT_SECURE_TABLE_CDTUSERGROUP_CD_USERGROUP;
		$usergroup_field_ds_usergroup = CDT_SECURE_TABLE_CDTUSERGROUP_DS_USERGROUP;
		
		$function_field_cd_function = CDT_SECURE_TABLE_CDTFUNCTION_CD_FUNCTION;
		$function_field_ds_function = CDT_SECURE_TABLE_CDTFUNCTION_DS_FUNCTION;
		
		$sql = "SELECT UGF.* ";
		$sql .= ", UG.$usergroup_field_cd_usergroup as UG_cd_usergroup, UG.$usergroup_field_ds_usergroup as UG_ds_usergroup ";
		$sql .= ", F.$function_field_cd_function as F_cd_function, F.$function_field_ds_function as F_ds_function ";
		$sql .= " FROM $tableName UGF ";
		$sql .= " LEFT JOIN $tableNameUserGroup UG ON(UG.$usergroup_field_cd_usergroup=UGF.$field_cd_usergroup) ";
		$sql .= " LEFT JOIN $tableNameFunction F ON(F.$function_field_cd_function=UGF.$field_cd_function) ";
		
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
		

			
			$items = CdtResultFactory::toCollection($db, $stmt->get_result(), new CdtUserGroupFunctionFactory("UG", "F"));
			mysqli_stmt_free_result($stmt);
			$stmt->close();
			
			return $items;	
		} else {
			throw new DBException("Error de preparación de consulta.");
		}
		 
		
	}

	
	/**
	 * (non-PHPdoc)
	 * @see dao/ICdtUserGroupFunctionDAO::getCdtUserGroupFunctionsCount()
	 */
	public function getCdtUserGroupFunctionsCount(CdtSearchCriteria $oCriteria) { 
		
		$db = CdtDbManager::getConnection(); 

		$tableName = CDT_SECURE_TABLE_CDTUSERGROUPFUNCTION;
		
		 
		$tableName = CDT_SECURE_TABLE_CDTUSERGROUPFUNCTION;
		$tableNameFunction = CDT_SECURE_TABLE_CDTFUNCTION;
		$tableNameUserGroup = CDT_SECURE_TABLE_CDTUSERGROUP;
		
		$field_cd_usergroup = CDT_SECURE_TABLE_CDTUSERGROUPFUNCTION_CD_USERGROUP;
		$field_cd_function = CDT_SECURE_TABLE_CDTUSERGROUPFUNCTION_CD_FUNCTION;
		
		$usergroup_field_cd_usergroup = CDT_SECURE_TABLE_CDTUSERGROUP_CD_USERGROUP;
		$function_field_cd_function = CDT_SECURE_TABLE_CDTFUNCTION_CD_FUNCTION;
		
		
		$sql = "SELECT count(*) as count FROM $tableName UGF ";
		$sql .= " LEFT JOIN $tableNameUserGroup UG ON(UG.$usergroup_field_cd_usergroup=UGF.$field_cd_usergroup) ";
		$sql .= " LEFT JOIN $tableNameFunction F ON(F.$function_field_cd_function=UGF.$field_cd_function) ";
		
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
	 * (non-PHPdoc)
	 * @see dao/ICdtUserGroupFunctionDAO::getCdtUserGroupFunction()
	 */
	public function getCdtUserGroupFunction(CdtSearchCriteria $oCriteria) { 

		$db = CdtDbManager::getConnection(); 

		$tableName = CDT_SECURE_TABLE_CDTUSERGROUPFUNCTION;
		$tableNameFunction = CDT_SECURE_TABLE_CDTFUNCTION;
		$tableNameUserGroup = CDT_SECURE_TABLE_CDTUSERGROUP;
		
		$field_cd_usergroup = CDT_SECURE_TABLE_CDTUSERGROUPFUNCTION_CD_USERGROUP;
		$field_cd_function = CDT_SECURE_TABLE_CDTUSERGROUPFUNCTION_CD_FUNCTION;
		
		$usergroup_field_cd_usergroup = CDT_SECURE_TABLE_CDTUSERGROUP_CD_USERGROUP;
		$usergroup_field_ds_usergroup = CDT_SECURE_TABLE_CDTUSERGROUP_DS_USERGROUP;
		
		$function_field_cd_function = CDT_SECURE_TABLE_CDTFUNCTION_CD_FUNCTION;
		$function_field_ds_function = CDT_SECURE_TABLE_CDTFUNCTION_DS_FUNCTION;
		
		$sql = "SELECT UGF.* ";
		$sql .= ", UG.$usergroup_field_cd_usergroup as UG_cd_usergroup, UG.$usergroup_field_ds_usergroupas UG_ds_usergroup ";
		$sql .= ", F.$function_field_cd_function as F_cd_function, F.$function_field_ds_function as F_ds_function ";
		$sql .= " FROM $tableName UGF ";
		$sql .= " LEFT JOIN $tableNameUserGroup UG ON(UG.$usergroup_field_cd_usergroup=UGF.$field_cd_usergroup) ";
		$sql .= " LEFT JOIN $tableNameFunction F ON(F.$function_field_cd_function=UGF.$field_cd_function) ";
		
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

			if (!$db->sql_execute($stmt)) {
			throw new DBException($db->sql_error());
		}

		$obj=null;
		
		$factory = new CdtUserGroupFunctionFactory("UG","F");

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
	 * (non-PHPdoc)
	 * @see dao/ICdtUserGroupFunctionDAO::deleteCdtUserGroupFunctions()
	 */
	public function deleteCdtUserGroupFunctions(CdtUserGroup $oCdtUserGroup) { 
		$db = CdtDbManager::getConnection(); 

		$cd_usergroup = $oCdtUserGroup->getCd_usergroup();

		$tableName = CDT_SECURE_TABLE_CDTUSERGROUPFUNCTION;
		$field_cd_usergroup = CDT_SECURE_TABLE_CDTUSERGROUPFUNCTION_CD_USERGROUP;
		
		$sql = "DELETE FROM $tableName WHERE $field_cd_usergroup = ? "; 

		$stmt = $db->sql_prepare($sql);

		// Verifica si la preparación fue exitosa.
		if ($stmt) {
			// Bind del parámetro de ID.
			mysqli_stmt_bind_param($stmt, 'i', $cd_usergroup);

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
