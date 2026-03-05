<?php 

/** 
 * DAO para CdtFunction 
 *  
 * @author codnet archetype builder
 * @since 09-11-2011
 */ 
class CdtFunctionDAO implements ICdtFunctionDAO{ 

	/**
	 * se persiste la nueva entity
	 * @param CdtFunction $oCdtFunction entity a persistir.
	 */
	public function addCdtFunction(CdtFunction $oCdtFunction) { 
		
		$db = CdtDbManager::getConnection(); 

		$ds_function = $oCdtFunction->getDs_function();
		
		$tableName = CDT_SECURE_TABLE_CDTFUNCTION;
		$field_ds_function = CDT_SECURE_TABLE_CDTFUNCTION_DS_FUNCTION;
		
		 // Definimos la consulta SQL con marcadores de posición (?)
		$sql = "INSERT INTO $tableName ($field_ds_function) VALUES(?)"; 

		// Preparamos la consulta SQL
		$stmt = $db->sql_prepare($sql);

		if ($stmt) {
			// Vinculamos el valor como parámetro
			$stmt->bind_param("s", $ds_function);

			// Ejecutamos la consulta
			if ($db->sql_execute($stmt)) {
				// Seteamos el nuevo id
				$cd = $db->sql_nextid();
				$oCdtFunction->setCd_function($cd);

				// Cerramos el statement
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
	 * @param CdtFunction $oCdtFunction entity a modificar.
	 */
	public function updateCdtFunction(CdtFunction $oCdtFunction) { 
		$db = CdtDbManager::getConnection(); 

		
		$ds_function = $oCdtFunction->getDs_function();
		
		
		$cd_function = CdtFormatUtils::ifEmpty( $oCdtFunction->getCd_function(), 'null' );
		
		$tableName = CDT_SECURE_TABLE_CDTFUNCTION;
		$field_cd_function = CDT_SECURE_TABLE_CDTFUNCTION_CD_FUNCTION;
		$field_ds_function = CDT_SECURE_TABLE_CDTFUNCTION_DS_FUNCTION;
		
		// Definimos la consulta SQL con marcadores de posición (?)
		$sql = "UPDATE $tableName SET $field_ds_function = ? WHERE $field_cd_function = ?"; 

		// Preparamos la consulta SQL
		$stmt = $db->sql_prepare($sql);

		if ($stmt) {
			// Vinculamos los valores como parámetros
			$stmt->bind_param("si", $ds_function, $cd_function);

			// Ejecutamos la consulta
			if ($db->sql_execute($stmt)) {
				// Cerramos el statement
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
	 * @param CdtFunction $oCdtFunction entity a eliminar.
	 */
	public function deleteCdtFunction(CdtFunction $oCdtFunction) { 
		$db = CdtDbManager::getConnection(); 

		$cd_function = $oCdtFunction->getCd_function();

		$tableName = CDT_SECURE_TABLE_CDTFUNCTION;
		$field_cd_function = CDT_SECURE_TABLE_CDTFUNCTION_CD_FUNCTION;
		
		// Definimos la consulta SQL con marcadores de posición (?)
		$sql = "DELETE FROM $tableName WHERE $field_cd_function = ?"; 

		// Preparamos la consulta SQL
		$stmt = $db->sql_prepare($sql);

		if ($stmt) {
			// Vinculamos el valor como parámetro
			$stmt->bind_param("i", $cd_function);

			// Ejecutamos la consulta
			if ($db->sql_execute($stmt)) {
				// Cerramos el statement
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
	 * @return ItemCollection[CdtFunction]
	 */
	public function getCdtFunctions(CdtSearchCriteria $oCriteria) { 
		
		$db = CdtDbManager::getConnection(); 

		$tableName = CDT_SECURE_TABLE_CDTFUNCTION;

		$sql = "SELECT * FROM $tableName ";
		//TODO left joins
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
		

			
			$items = CdtResultFactory::toCollection($db, $stmt->get_result(), new CdtFunctionFactory());
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
	public function getCdtFunctionsCount(CdtSearchCriteria $oCriteria) { 
		$db = CdtDbManager::getConnection(); 

		$tableName = CDT_SECURE_TABLE_CDTFUNCTION;
		
		$sql = "SELECT count(*) as count FROM $tableName "; 
		//TODO left joins
		
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
	 * @return CdtFunction
	 */
	public function getCdtFunction(CdtSearchCriteria $oCriteria) { 

		$db = CdtDbManager::getConnection(); 

		$tableName = CDT_SECURE_TABLE_CDTFUNCTION;
		
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
			
			$factory = new CdtFunctionFactory();

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
	 * funciones asociadas al grupo del usuario.
	 * @param CdtUser $oUser
	 * @return ItemCollecion[CdtFunction]
	 */
	public function getCdtUserFunctions(CdtUser $oUser) {
		
		$db = CdtDbManager::getConnection();
		
		$cd_user = $oUser->getCd_user();
		
		$userGroup_field_cd_usergroup = CDT_SECURE_TABLE_CDTUSERGROUPFUNCTION_CD_USERGROUP;
		$userGroup_field_cd_function = CDT_SECURE_TABLE_CDTUSERGROUPFUNCTION_CD_FUNCTION;
		$user_field_cd_usergroup = CDT_SECURE_TABLE_CDTUSER_CD_USERGROUP;
		$function_field_cd_function = CDT_SECURE_TABLE_CDTFUNCTION_CD_FUNCTION;
		
		$sql = "SELECT F.* FROM ".  CDT_SECURE_TABLE_CDTUSER . " U"; 
		$sql .= " LEFT JOIN ".  CDT_SECURE_TABLE_CDTUSERGROUPFUNCTION . " PF ON PF.$userGroup_field_cd_usergroup=U.$user_field_cd_usergroup"; 
		$sql .= " LEFT JOIN ".  CDT_SECURE_TABLE_CDTFUNCTION . " F ON F.$function_field_cd_function=PF.$userGroup_field_cd_function ";
		$sql .= " WHERE U.cd_user = $cd_user";
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
		

			
			$items = CdtResultFactory::toCollection($db, $stmt->get_result(), new CdtFunctionFactory());
			mysqli_stmt_free_result($stmt);
			$stmt->close();
			
			return $items;	
		} else {
			throw new DBException("Error de preparación de consulta.");
		}
		
	}

	/**
	 * funciones asociadas al grupo de usuario.
	 * @param CdtUser $oUser
	 * @return ItemCollecion[CdtFunction]
	 */
	public function getCdtUserGroupFunctions(CdtUserGroup $oUserGroup) {
		
		$db = CdtDbManager::getConnection();
		
		$cd_usergroup = $oUserGroup->getCd_usergroup();
		
		$userGroup_field_cd_usergroup = CDT_SECURE_TABLE_CDTUSERGROUPFUNCTION_CD_USERGROUP;
		$userGroup_field_cd_function = CDT_SECURE_TABLE_CDTUSERGROUPFUNCTION_CD_FUNCTION;
		$function_field_cd_function = CDT_SECURE_TABLE_CDTFUNCTION_CD_FUNCTION;
		
		$sql = "SELECT F.* FROM ".  CDT_SECURE_TABLE_CDTUSERGROUPFUNCTION . " UGF, "; 
		$sql .= CDT_SECURE_TABLE_CDTFUNCTION . " F  ";
		$sql .= " WHERE UGF.$userGroup_field_cd_usergroup = $cd_usergroup AND F.$function_field_cd_function=UGF.$userGroup_field_cd_function ";
		
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
		

			
			$items = CdtResultFactory::toCollection($db, $stmt->get_result(), new CdtFunctionFactory());
			mysqli_stmt_free_result($stmt);
			$stmt->close();
			
			return $items;	
		} else {
			throw new DBException("Error de preparación de consulta.");
		}
	}		
} 
?>
