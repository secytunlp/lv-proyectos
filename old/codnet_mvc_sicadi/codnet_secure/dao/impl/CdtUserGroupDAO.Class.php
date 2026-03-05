<?php 

/** 
 * DAO para CdtUserGroup 
 *  
 * @author codnet archetype builder
 * @since 09-11-2011
 */ 
class CdtUserGroupDAO implements ICdtUserGroupDAO { 

	/**
	 * se persiste la nueva entity
	 * @param CdtUserGroup $oCdtUserGroup entity a persistir.
	 */
	public function addCdtUserGroup(CdtUserGroup $oCdtUserGroup) { 
		$db = CdtDbManager::getConnection(); 

		$ds_usergroup = $oCdtUserGroup->getDs_usergroup();
		
		$tableName = CDT_SECURE_TABLE_CDTUSERGROUP;
		$field_ds_usergroup = CDT_SECURE_TABLE_CDTUSERGROUP_DS_USERGROUP;
				
		// Definimos la consulta SQL con un marcador de posición (?)
		$sql = "INSERT INTO $tableName ($field_ds_usergroup) VALUES(?)";

		// Preparamos la consulta
		$stmt = $db->sql_prepare($sql);

		if ($stmt) {
			// Vinculamos el valor como parámetro
			$stmt->bind_param("s", $ds_usergroup); // "s" indica que es una cadena

			// Ejecutamos la consulta
			if ($db->sql_execute($stmt)) {
				// Seteamos el nuevo ID
				$cd = $db->sql_nextid();
				$oCdtUserGroup->setCd_usergroup($cd);
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
	 * @param CdtUserGroup $oCdtUserGroup entity a modificar.
	 */
	public function updateCdtUserGroup(CdtUserGroup $oCdtUserGroup) { 
		
		$db = CdtDbManager::getConnection(); 

		$ds_usergroup = $oCdtUserGroup->getDs_usergroup();
		
		$cd_usergroup = CdtFormatUtils::ifEmpty( $oCdtUserGroup->getCd_usergroup(), 'null' );
		
		$tableName = CDT_SECURE_TABLE_CDTUSERGROUP;
		$field_cd_usergroup = CDT_SECURE_TABLE_CDTUSERGROUP_CD_USERGROUP;
		$field_ds_usergroup = CDT_SECURE_TABLE_CDTUSERGROUP_DS_USERGROUP;
		
		// Definimos la consulta SQL con marcadores de posición (?)
		$sql = "UPDATE $tableName SET $field_ds_usergroup = ? WHERE $field_cd_usergroup = ?";

		// Preparamos la consulta
		$stmt = $db->sql_prepare($sql);

		if ($stmt) {
			// Vinculamos los valores como parámetros
			$stmt->bind_param("si", $ds_usergroup, $cd_usergroup); // "si" indica cadena y entero

			// Ejecutamos la consulta
			if ($db->sql_execute($stmt)) {
				// Verificamos si se actualizó al menos una fila
				if ($stmt->affected_rows < 1) {
					throw new DBException("No se encontró el registro a actualizar.");
				}
			} else {
				throw new DBException($db->sql_error());
			}

			$stmt->close();
		} else {
			throw new DBException("Error de preparación de consulta.");
		}

	}

	/**
	 * se elimina la entity
	 * @param CdtUserGroup $oCdtUserGroup entity a eliminar.
	 */
	public function deleteCdtUserGroup(CdtUserGroup $oCdtUserGroup) { 
		
		$db = CdtDbManager::getConnection(); 

		$cd_usergroup = $oCdtUserGroup->getCd_usergroup();

		$tableName = CDT_SECURE_TABLE_CDTUSERGROUP;
		$field_cd_usergroup = CDT_SECURE_TABLE_CDTUSERGROUP_CD_USERGROUP;
		
		 // Definimos la consulta SQL con marcadores de posición (?)
		$sql = "DELETE FROM $tableName WHERE $field_cd_usergroup = ?";

		// Preparamos la consulta
		$stmt = $db->sql_prepare($sql);

		if ($stmt) {
			// Vinculamos el valor como parámetro
			$stmt->bind_param("i", $cd_usergroup); // "i" indica un entero

			// Ejecutamos la consulta
			if ($db->sql_execute($stmt)) {
				// Verificamos si se eliminó al menos una fila
				if ($stmt->affected_rows < 1) {
					throw new DBException("No se encontró el registro a eliminar.");
				}
			} else {
				throw new DBException($db->sql_error());
			}

			$stmt->close();
		} else {
			throw new DBException("Error de preparación de consulta.");
		}

	}

	/**
	 * se obtiene una colecciï¿½n de entities dado el filtro de bï¿½squeda
	 * @param CdtSearchCriteria $oCriteria filtro de bï¿½squeda.
	 * @return ItemCollection[CdtUserGroup]
	 */
	public function getCdtUserGroups(CdtSearchCriteria $oCriteria) { 
		
		$db = CdtDbManager::getConnection(); 

		$tableName = CDT_SECURE_TABLE_CDTUSERGROUP;

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
		

			
			$items = CdtResultFactory::toCollection($db, $stmt->get_result(), new CdtUserGroupFactory());
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
	public function getCdtUserGroupsCount(CdtSearchCriteria $oCriteria) { 
		$db = CdtDbManager::getConnection(); 

		$tableName = CDT_SECURE_TABLE_CDTUSERGROUP;
		
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
	 * @return CdtUserGroup
	 */
	public function getCdtUserGroup(CdtSearchCriteria $oCriteria) { 

		$db = CdtDbManager::getConnection(); 

		$tableName = CDT_SECURE_TABLE_CDTUSERGROUP;
		
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

			if (!$db->sql_execute($stmt)) {
				throw new DBException($db->sql_error());
			}

			$obj=null;
			
			$factory = new CdtUserGroupFactory();

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