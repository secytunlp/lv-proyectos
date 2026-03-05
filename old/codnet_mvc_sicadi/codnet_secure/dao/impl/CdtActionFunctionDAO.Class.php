<?php 

/** 
 * DAO para CdtActionFunction 
 *  
 * @author codnet archetype builder
 * @since 09-11-2011
 */ 
class CdtActionFunctionDAO implements ICdtActionFunctionDAO{ 

	/**
	 * (non-PHPdoc)
	 * @see dao/ICdtActionFunctionDAO::addCdtActionFunction()
	 */
	public function addCdtActionFunction(CdtActionFunction $oCdtActionFunction) { 

		$db = CdtDbManager::getConnection(); 

		$ds_action = $oCdtActionFunction->getDs_action();
		
		$cd_function =  CdtFormatUtils::ifEmpty( $oCdtActionFunction->getCd_function(), 'null' );
		
		$tableName = CDT_SECURE_TABLE_CDTACTIONFUNCTION;
		$field_cd_function = CDT_SECURE_TABLE_CDTACTIONFUNCTION_CD_FUNCTION;
		$field_ds_action = CDT_SECURE_TABLE_CDTACTIONFUNCTION_DS_ACTION;
		
		$sql = "INSERT INTO $tableName ($field_cd_function, $field_ds_action) VALUES (?, ?)";

		$stmt = $db->sql_prepare($sql);

		if ($stmt) {
			// Vinculamos los valores como parámetros.
			$stmt->bind_param("is", $cd_function, $ds_action);

			if ($db->sql_execute($stmt)) {
				// Seteamos el nuevo ID.
				$cd = $db->sql_nextid();
				$oCdtActionFunction->setCd_actionfunction($cd);

				$stmt->close();
			} else {
				throw new DBException($db->sql_error());
			}
		} else {
			throw new DBException("Error de preparación de consulta.");
		}
	}

	
	
	/**
	 * (non-PHPdoc)
	 * @see dao/ICdtActionFunctionDAO::updateCdtActionFunction()
	 */
	public function updateCdtActionFunction(CdtActionFunction $oCdtActionFunction) { 
		
		$db = CdtDbManager::getConnection(); 

		
		$ds_action = $oCdtActionFunction->getDs_action();
		
		$cd_actionfunction = CdtFormatUtils::ifEmpty( $oCdtActionFunction->getCd_actionfunction(), 'null' );
		
		$cd_function = CdtFormatUtils::ifEmpty( $oCdtActionFunction->getCd_function(), 'null' );
		
		$tableName = CDT_SECURE_TABLE_CDTACTIONFUNCTION;
		$field_cd_actionfunction = CDT_SECURE_TABLE_CDTACTIONFUNCTION_CD_ACTIONFUNCTION;
		$field_cd_function = CDT_SECURE_TABLE_CDTACTIONFUNCTION_CD_FUNCTION;
		$field_ds_action = CDT_SECURE_TABLE_CDTACTIONFUNCTION_DS_ACTION;
		
		 $sql = "UPDATE $tableName SET $field_cd_function = ?, $field_ds_action = ? WHERE $field_cd_actionfunction = ?";

		$stmt = $db->sql_prepare($sql);


		if ($stmt) {
			// Vinculamos los valores como parámetros.
			$stmt->bind_param("iss", $cd_function, $ds_action, $cd_actionfunction);

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
	 * @see dao/ICdtActionFunctionDAO::deleteCdtActionFunction()
	 */
	public function deleteCdtActionFunction(CdtActionFunction $oCdtActionFunction) { 

		$db = CdtDbManager::getConnection(); 

		$cd_actionfunction = $oCdtActionFunction->getCd_actionfunction();

		$tableName = CDT_SECURE_TABLE_CDTACTIONFUNCTION;
		$field_cd_actionfunction = CDT_SECURE_TABLE_CDTACTIONFUNCTION_CD_ACTIONFUNCTION;
		
		$sql = "DELETE FROM $tableName WHERE $field_cd_actionfunction = ? "; 

		$stmt = $db->sql_prepare($sql);

		// Verifica si la preparación fue exitosa.
		if ($stmt) {
			// Bind del parámetro de ID.
			mysqli_stmt_bind_param($stmt, 'i', $cd_actionfunction);

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

	/**
	 * (non-PHPdoc)
	 * @see dao/ICdtActionFunctionDAO::getCdtActionFunctions()
	 */
	public function getCdtActionFunctions(CdtSearchCriteria $oCriteria) { 
		
		$db = CdtDbManager::getConnection(); 

		$tableName = CDT_SECURE_TABLE_CDTACTIONFUNCTION;
		$tableNameFunction = CDT_SECURE_TABLE_CDTFUNCTION;

		$actionfunction_field_cd_function = CDT_SECURE_TABLE_CDTACTIONFUNCTION_CD_FUNCTION;
		$function_field_cd_function = CDT_SECURE_TABLE_CDTFUNCTION_CD_FUNCTION;
		$function_field_ds_function = CDT_SECURE_TABLE_CDTFUNCTION_DS_FUNCTION;
		
		$sql = "SELECT AF.* ";
		$sql .= ", F.$function_field_cd_function as F_cd_function, F.$function_field_ds_function as F_ds_function ";
		$sql .= " FROM $tableName AF ";
		$sql .= " LEFT JOIN $tableNameFunction F ON(F.$function_field_cd_function=AF.$actionfunction_field_cd_function) ";
		
		$criteriaParams = $oCriteria->buildCriteria();
		$sql .= $criteriaParams['where'];
		$params = $criteriaParams['params'];
		
		
		$stmt = $db->sql_prepare($sql);
		
		
		

		
		

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

			// Obtenemos los resultados.
			$items = CdtResultFactory::toCollection($db, $stmt->get_result(), new CdtActionFunctionFactory("F"));

			// Cerramos la sentencia preparada.
			mysqli_stmt_free_result($stmt);

			return $items;
		} else {
			throw new DBException("Error de preparación de consulta.");
		}
	}

	
	/**
	 * (non-PHPdoc)
	 * @see dao/ICdtActionFunctionDAO::getCdtActionFunctionsCount()
	 */
	public function getCdtActionFunctionsCount(CdtSearchCriteria $oCriteria) { 
		
		$db = CdtDbManager::getConnection(); 

		$tableName = CDT_SECURE_TABLE_CDTACTIONFUNCTION;
		$tableNameFunction = CDT_SECURE_TABLE_CDTFUNCTION;

		$actionfunction_field_cd_function = CDT_SECURE_TABLE_CDTACTIONFUNCTION_CD_FUNCTION;
		$function_field_cd_function = CDT_SECURE_TABLE_CDTFUNCTION_CD_FUNCTION;
		
		$sql = "SELECT count(*) as count "; 
		$sql .= " FROM $tableName AF ";
		$sql .= " LEFT JOIN $tableNameFunction F ON(F.$function_field_cd_function=AF.$actionfunction_field_cd_function) ";
		
		$criteriaParams = $oCriteria->buildCriteriaWithoutPaging();
		$sql .= $criteriaParams['where'];
		$params = $criteriaParams['params'];
		$stmt = $db->sql_prepare($sql);

		// Verificamos si la preparación fue exitosa.
		if ($stmt) {
			

			// Verificamos si hay parámetros para vincular.
			if (!empty($params)) {
				// Obtenemos los tipos de parámetros para la función mysqli_stmt_bind_param.
				$paramTypes = CdtFormatUtils::getParamTypes($params);

				// Preparamos los valores para la vinculación.
				$paramValues = array_values($params);

				// Insertamos los parámetros en la consulta preparada.
				$stmt = CdtFormatUtils::bindParamsToStatement($stmt, $paramTypes, ...$paramValues);
			}

			if (!$db->sql_execute($stmt)) {
				throw new DBException($db->sql_error());
			}

			// Obtenemos el resultado.
			$next = $db->sql_fetchassoc($stmt->get_result());
			$cant = $next['count'];

			// Cerramos la sentencia preparada.
			mysqli_stmt_free_result($stmt);

			return (int) $cant;
		} else {
			throw new DBException("Error de preparación de consulta.");
		}
	}


	/**
	 * (non-PHPdoc)
	 * @see dao/ICdtActionFunctionDAO::getCdtActionFunction()
	 */
	public function getCdtActionFunction(CdtSearchCriteria $oCriteria)
	{
		$obj = '';
		$db = CdtDbManager::getConnection();

		$tableName = CDT_SECURE_TABLE_CDTACTIONFUNCTION;
		$tableNameFunction = CDT_SECURE_TABLE_CDTFUNCTION;

		$actionfunction_field_cd_function = CDT_SECURE_TABLE_CDTACTIONFUNCTION_CD_FUNCTION;
		$function_field_cd_function = CDT_SECURE_TABLE_CDTFUNCTION_CD_FUNCTION;
		$function_field_ds_function = CDT_SECURE_TABLE_CDTFUNCTION_DS_FUNCTION;

		$sql = "SELECT AF.* ";
		$sql .= ", F.$function_field_cd_function as F_cd_function, F.$function_field_ds_function as F_ds_function ";
		$sql .= " FROM $tableName AF ";
		$sql .= " LEFT JOIN $tableNameFunction F ON(F.$function_field_cd_function=AF.$actionfunction_field_cd_function) ";

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
			
			$factory = new CdtActionFunctionFactory("F");;

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