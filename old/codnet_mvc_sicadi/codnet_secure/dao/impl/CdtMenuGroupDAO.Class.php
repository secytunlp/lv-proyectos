<?php 

/** 
 * DAO para CdtMenuGroup 
 *  
 * @author codnet archetype builder
 * @since 09-11-2011
 */ 
class CdtMenuGroupDAO implements ICdtMenuGroupDAO{ 

	/**
	 * se persiste la nueva entity
	 * @param CdtMenuGroup $oCdtMenuGroup entity a persistir.
	 */
	public function addCdtMenuGroup(CdtMenuGroup $oCdtMenuGroup) { 
		$db = CdtDbManager::getConnection(); 

		
		$ds_name = $oCdtMenuGroup->getDs_name();
		
		$ds_action = $oCdtMenuGroup->getDs_action();
		
		$ds_cssclass = $oCdtMenuGroup->getDs_cssclass();
		
		$nu_order =  CdtFormatUtils::ifEmpty( $oCdtMenuGroup->getNu_order(), 'null' );
		
		$nu_width =  CdtFormatUtils::ifEmpty( $oCdtMenuGroup->getNu_width(), 'null' );
		
		
		$tableName = CDT_SECURE_TABLE_CDTMENUGROUP;
		$field_ds_name = CDT_SECURE_TABLE_CDTMENUGROUP_DS_NAME;
		$field_ds_action = CDT_SECURE_TABLE_CDTMENUGROUP_DS_ACTION;
		$field_ds_cssclass = CDT_SECURE_TABLE_CDTMENUGROUP_DS_CSSCLASS;
		$field_nu_order = CDT_SECURE_TABLE_CDTMENUGROUP_NU_ORDER;
		$field_nu_width = CDT_SECURE_TABLE_CDTMENUGROUP_NU_WIDTH;
		
		 $sql = "INSERT INTO $tableName ($field_nu_order, $field_nu_width, $field_ds_name, $field_ds_action, $field_ds_cssclass) VALUES(?, ?, ?, ?, ?)";

		$stmt = $db->sql_prepare($sql);

		if ($stmt) {
			// Vinculamos los valores como parámetros.
			$stmt->bind_param("iissi", $nu_order, $nu_width, $ds_name, $ds_action, $ds_cssclass);

			if ($db->sql_execute($stmt)) {
				// Seteamos el nuevo ID.
				$cd = $db->sql_nextid();
				$oCdtMenuGroup->setCd_menugroup($cd);
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
	 * @param CdtMenuGroup $oCdtMenuGroup entity a modificar.
	 */
	public function updateCdtMenuGroup(CdtMenuGroup $oCdtMenuGroup) { 
		$db = CdtDbManager::getConnection(); 

		
		$ds_name = $oCdtMenuGroup->getDs_name();
		
		$ds_action = $oCdtMenuGroup->getDs_action();
		
		$ds_cssclass = $oCdtMenuGroup->getDs_cssclass();
		
		
		$cd_menugroup = CdtFormatUtils::ifEmpty( $oCdtMenuGroup->getCd_menugroup(), 'null' );
		
		$nu_order = CdtFormatUtils::ifEmpty( $oCdtMenuGroup->getNu_order(), 'null' );
		
		$nu_width = CdtFormatUtils::ifEmpty( $oCdtMenuGroup->getNu_width(), 'null' );
		
		

		$tableName = CDT_SECURE_TABLE_CDTMENUGROUP;
		$field_cd_menugroup = CDT_SECURE_TABLE_CDTMENUGROUP_CD_MENUGROUP;
		$field_ds_name = CDT_SECURE_TABLE_CDTMENUGROUP_DS_NAME;
		$field_ds_action = CDT_SECURE_TABLE_CDTMENUGROUP_DS_ACTION;
		$field_ds_cssclass = CDT_SECURE_TABLE_CDTMENUGROUP_DS_CSSCLASS;
		$field_nu_order = CDT_SECURE_TABLE_CDTMENUGROUP_NU_ORDER;
		$field_nu_width = CDT_SECURE_TABLE_CDTMENUGROUP_NU_WIDTH;
				
		$sql = "UPDATE $tableName SET $field_nu_order = ?, $field_nu_width = ?, $field_ds_name = ?, $field_ds_action = ?, $field_ds_cssclass = ? WHERE $field_cd_menugroup = ?";

		$stmt = $db->sql_prepare($sql);

		if ($stmt) {
			// Vinculamos los valores como parámetros.
			$stmt->bind_param("iissii", $nu_order, $nu_width, $ds_name, $ds_action, $ds_cssclass, $cd_menugroup);

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
	 * @param CdtMenuGroup $oCdtMenuGroup entity a eliminar.
	 */
	public function deleteCdtMenuGroup(CdtMenuGroup $oCdtMenuGroup) { 
		$db = CdtDbManager::getConnection(); 

		$cd_menugroup = $oCdtMenuGroup->getCd_menugroup();

		$tableName = CDT_SECURE_TABLE_CDTMENUGROUP;
		$field_cd_menugroup = CDT_SECURE_TABLE_CDTMENUGROUP_CD_MENUGROUP;
		
		$sql = "DELETE FROM $tableName WHERE $field_cd_menugroup = ? "; 

				$stmt = $db->sql_prepare($sql);

		if ($stmt) {
			// Vinculamos el valor como parámetro.
			$stmt->bind_param("i", $cd_menugroup);

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
 * Se obtiene una colección de entidades dado el filtro de búsqueda.
 * @param CdtSearchCriteria $oCriteria Filtro de búsqueda.
 * @return ItemCollection[CdtMenuGroup]
 */
public function getCdtMenuGroups(CdtSearchCriteria $oCriteria) { 
    
    $db = CdtDbManager::getConnection(); 

    $tableName = CDT_SECURE_TABLE_CDTMENUGROUP;

    // Consulta SQL con marcadores de posición (?)
    $sql = "SELECT * FROM $tableName ";
    // TODO: Incluir joins si es necesario
    
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
	

		
        $items = CdtResultFactory::toCollection($db, $stmt->get_result(), new CdtMenuGroupFactory());
		mysqli_stmt_free_result($stmt);
        $stmt->close();
		
        return $items;	
    } else {
        throw new DBException("Error de preparación de consulta.");
    }
}


	
	/**
 * Se obtiene la cantidad de entidades dado el filtro de búsqueda.
 * @param CdtSearchCriteria $oCriteria Filtro de búsqueda.
 * @return int
 */
public function getCdtMenuGroupsCount(CdtSearchCriteria $oCriteria) { 
    $db = CdtDbManager::getConnection(); 

    $tableName = CDT_SECURE_TABLE_CDTMENUGROUP;

    // Consulta SQL con marcadores de posición (?)
    $sql = "SELECT COUNT(*) as count FROM $tableName ";
    // TODO: Incluir joins si es necesario
    
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
 * Se obtiene una entidad dado el filtro de búsqueda.
 * @param CdtSearchCriteria $oCriteria Filtro de búsqueda.
 * @return CdtMenuGroup|null
 */
public function getCdtMenuGroup(CdtSearchCriteria $oCriteria) { 

    $db = CdtDbManager::getConnection(); 

    $tableName = CDT_SECURE_TABLE_CDTMENUGROUP;
    
    // Consulta SQL con marcadores de posición (?)
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

        $result = $stmt->get_result();
        $obj = null;

        if ($result && $db->sql_numrows($result) > 0) {
            $temp = $db->sql_fetchassoc($result);
            $factory = new CdtMenuGroupFactory();
            $obj = $factory->build($temp);
        }

        $db->sql_freeresult($result);

        return $obj;
    } else {
        throw new DBException("Error de preparación de consulta.");
    }
}


} 
?>