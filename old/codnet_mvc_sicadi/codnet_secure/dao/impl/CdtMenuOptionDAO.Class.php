<?php 

/** 
 * DAO para CdtMenuOption 
 *  
 * @author codnet archetype builder
 * @since 09-11-2011
 */ 
class CdtMenuOptionDAO implements ICdtMenuOptionDAO{ 

	/**
	 * se persiste la nueva entity
	 * @param CdtMenuOption $oCdtMenuOption entity a persistir.
	 */
	public function addCdtMenuOption(CdtMenuOption $oCdtMenuOption) { 
		$db = CdtDbManager::getConnection(); 

		
		$ds_name = $oCdtMenuOption->getDs_name();
		
		$ds_href = $oCdtMenuOption->getDs_href();
		
		$ds_cssclass = $oCdtMenuOption->getDs_cssclass();
		
		$ds_description = $oCdtMenuOption->getDs_description();
		
		
		$cd_function =  CdtFormatUtils::ifEmpty( $oCdtMenuOption->getCd_function(), 'null' );
		
		$nu_order =  CdtFormatUtils::ifEmpty( $oCdtMenuOption->getNu_order(), 'null' );
		
		$cd_menugroup =  CdtFormatUtils::ifEmpty( $oCdtMenuOption->getCd_menugroup(), 'null' );
		
		
		$tableName = CDT_SECURE_TABLE_CDTMENUOPTION;
		$field_ds_name = CDT_SECURE_TABLE_CDTMENUOPTION_DS_NAME;
		$field_ds_href = CDT_SECURE_TABLE_CDTMENUOPTION_DS_HREF;
		$field_cd_function = CDT_SECURE_TABLE_CDTMENUOPTION_CD_FUNCTION;
		$field_nu_order = CDT_SECURE_TABLE_CDTMENUOPTION_NU_ORDER;
		$field_cd_menugroup = CDT_SECURE_TABLE_CDTMENUOPTION_CD_MENUGROUP;
		$field_ds_cssclass = CDT_SECURE_TABLE_CDTMENUOPTION_DS_CSSCLASS;
		$field_ds_description = CDT_SECURE_TABLE_CDTMENUOPTION_DS_DESCRIPTION;
		
		$sql = "INSERT INTO $tableName ($field_ds_name, $field_ds_href, $field_cd_function, $field_nu_order, $field_cd_menugroup, $field_ds_cssclass, $field_ds_description) VALUES (?, ?, ?, ?, ?, ?, ?)";

		$stmt = $db->sql_prepare($sql);

		if ($stmt) {
			// Vinculamos los valores como parámetros.
			$stmt->bind_param("ssiiiss", $ds_name, $ds_href, $cd_function, $nu_order, $cd_menugroup, $ds_cssclass, $ds_description);

			if ($db->sql_execute($stmt)) {
				// Seteamos el nuevo ID.
				$cd = $db->sql_nextid();
				$oCdtMenuOption->setCd_menuoption($cd);

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
	 * @param CdtMenuOption $oCdtMenuOption entity a modificar.
	 */
	public function updateCdtMenuOption(CdtMenuOption $oCdtMenuOption) { 

		$db = CdtDbManager::getConnection(); 
		
		$ds_name = $oCdtMenuOption->getDs_name();
		
		$ds_href = $oCdtMenuOption->getDs_href();
		
		$ds_cssclass = $oCdtMenuOption->getDs_cssclass();
		
		$ds_description = $oCdtMenuOption->getDs_description();
		
		$cd_menuoption = CdtFormatUtils::ifEmpty( $oCdtMenuOption->getCd_menuoption(), 'null' );
		
		$cd_function = CdtFormatUtils::ifEmpty( $oCdtMenuOption->getCd_function(), 'null' );
		
		$nu_order = CdtFormatUtils::ifEmpty( $oCdtMenuOption->getNu_order(), 'null' );
		
		$cd_menugroup = CdtFormatUtils::ifEmpty( $oCdtMenuOption->getCd_menugroup(), 'null' );
		
		$tableName = CDT_SECURE_TABLE_CDTMENUOPTION;
		$field_cd_menuoption = CDT_SECURE_TABLE_CDTMENUOPTION_CD_MENUOPTION;
		$field_ds_name = CDT_SECURE_TABLE_CDTMENUOPTION_DS_NAME;
		$field_ds_href = CDT_SECURE_TABLE_CDTMENUOPTION_DS_HREF;
		$field_cd_function = CDT_SECURE_TABLE_CDTMENUOPTION_CD_FUNCTION;
		$field_nu_order = CDT_SECURE_TABLE_CDTMENUOPTION_NU_ORDER;
		$field_cd_menugroup = CDT_SECURE_TABLE_CDTMENUOPTION_CD_MENUGROUP;
		$field_ds_cssclass = CDT_SECURE_TABLE_CDTMENUOPTION_DS_CSSCLASS;
		$field_ds_description = CDT_SECURE_TABLE_CDTMENUOPTION_DS_DESCRIPTION;
		
		$sql = "UPDATE $tableName SET $field_ds_name = ?, $field_ds_href = ?, $field_cd_function = ?, $field_nu_order = ?, $field_cd_menugroup = ?, $field_ds_cssclass = ?, $field_ds_description = ? WHERE $field_cd_menuoption = ?";

		$stmt = $db->sql_prepare($sql);

		if ($stmt) {
			// Vinculamos los valores como parámetros.
			$stmt->bind_param("ssiiissi", $ds_name, $ds_href, $cd_function, $nu_order, $cd_menugroup, $ds_cssclass, $ds_description, $cd_menuoption);

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
	 * @param CdtMenuOption $oCdtMenuOption entity a eliminar.
	 */
	public function deleteCdtMenuOption(CdtMenuOption $oCdtMenuOption) { 
		$db = CdtDbManager::getConnection(); 

		$cd_menuoption = $oCdtMenuOption->getCd_menuoption();

		$tableName = CDT_SECURE_TABLE_CDTMENUOPTION;
		$field_cd_menuoption = CDT_SECURE_TABLE_CDTMENUOPTION_CD_MENUOPTION;
		
		$sql = "DELETE FROM $tableName WHERE $field_cd_menuoption = ?";

		$stmt = $db->sql_prepare($sql);

		if ($stmt) {
			// Vinculamos el valor como parámetro.
			$stmt->bind_param("i", $cd_menuoption);

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
 * @return ItemCollection[CdtMenuOption]
 */
public function getCdtMenuOptions(CdtSearchCriteria $oCriteria) { 

    $db = CdtDbManager::getConnection(); 

    $tableName = CDT_SECURE_TABLE_CDTMENUOPTION;
    $tableNameMenuGroup = CDT_SECURE_TABLE_CDTMENUGROUP;
    $tableNameFunction = CDT_SECURE_TABLE_CDTFUNCTION;
    
    $menuoption_field_cd_menugroup = CDT_SECURE_TABLE_CDTMENUOPTION_CD_MENUGROUP;
    $menuoption_field_cd_function = CDT_SECURE_TABLE_CDTMENUOPTION_CD_FUNCTION;
    
    $menugroup_field_cd_menugroup = CDT_SECURE_TABLE_CDTMENUGROUP_CD_MENUGROUP;
    $menugroup_field_ds_name = CDT_SECURE_TABLE_CDTMENUGROUP_DS_NAME;
    
    $function_field_cd_function = CDT_SECURE_TABLE_CDTFUNCTION_CD_FUNCTION;
    $function_field_ds_function = CDT_SECURE_TABLE_CDTFUNCTION_DS_FUNCTION;

    // Consulta SQL con marcadores de posición (?)
    $sql  = "SELECT MO.*, MG.$menugroup_field_cd_menugroup as MG_cd_menugroup, MG.$menugroup_field_ds_name as MG_ds_name, ";
    $sql .= "F.$function_field_cd_function as F_cd_function, F.$function_field_ds_function as F_ds_function ";
    $sql .= "FROM $tableName MO ";
    $sql .= "LEFT JOIN $tableNameMenuGroup MG ON(MG.$menugroup_field_cd_menugroup=MO.$menuoption_field_cd_menugroup) ";
    $sql .= "LEFT JOIN $tableNameFunction F ON(F.$function_field_cd_function=MO.$menuoption_field_cd_function) ";
    
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
        $items = CdtResultFactory::toCollection($db, $result, new CdtMenuOptionFactory("", "MG", "F"));
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
public function getCdtMenuOptionsCount(CdtSearchCriteria $oCriteria) { 
    $db = CdtDbManager::getConnection(); 

    $tableName = CDT_SECURE_TABLE_CDTMENUOPTION;
    $tableNameMenuGroup = CDT_SECURE_TABLE_CDTMENUGROUP;
    $tableNameFunction = CDT_SECURE_TABLE_CDTFUNCTION;

    $menuoption_field_cd_menugroup = CDT_SECURE_TABLE_CDTMENUOPTION_CD_MENUGROUP;
    $menuoption_field_cd_function = CDT_SECURE_TABLE_CDTMENUOPTION_CD_FUNCTION;
    
    $menugroup_field_cd_menugroup = CDT_SECURE_TABLE_CDTMENUGROUP_CD_MENUGROUP;
    
    $function_field_cd_function = CDT_SECURE_TABLE_CDTFUNCTION_CD_FUNCTION;

    // Consulta SQL con marcadores de posición (?)
    $sql = "SELECT COUNT(*) as count FROM $tableName MO ";
    $sql .= "LEFT JOIN $tableNameMenuGroup MG ON(MG.$menugroup_field_cd_menugroup=MO.$menuoption_field_cd_menugroup) ";
    $sql .= "LEFT JOIN $tableNameFunction F ON(F.$function_field_cd_function=MO.$menuoption_field_cd_function) ";
    
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
        $next = $result->fetch_assoc();
        $cant = $next['count'];
        mysqli_stmt_free_result($stmt);
        $stmt->close();

        return ((int) $cant);
    } else {
        throw new DBException("Error de preparación de consulta.");
    }
}



	/**
 * Se obtiene una entidad dado el filtro de búsqueda.
 * @param CdtSearchCriteria $oCriteria Filtro de búsqueda.
 * @return CdtMenuOption|null
 */
public function getCdtMenuOption(CdtSearchCriteria $oCriteria) { 

    $db = CdtDbManager::getConnection(); 

    $tableName = CDT_SECURE_TABLE_CDTMENUOPTION;
    $tableNameMenuGroup = CDT_SECURE_TABLE_CDTMENUGROUP;
    $tableNameFunction = CDT_SECURE_TABLE_CDTFUNCTION;

    $menuoption_field_cd_menugroup = CDT_SECURE_TABLE_CDTMENUOPTION_CD_MENUGROUP;
    $menuoption_field_cd_function = CDT_SECURE_TABLE_CDTMENUOPTION_CD_FUNCTION;

    $menugroup_field_cd_menugroup = CDT_SECURE_TABLE_CDTMENUGROUP_CD_MENUGROUP;
    $menugroup_field_ds_name = CDT_SECURE_TABLE_CDTMENUGROUP_DS_NAME;

    $function_field_cd_function = CDT_SECURE_TABLE_CDTFUNCTION_CD_FUNCTION;
    $function_field_ds_function = CDT_SECURE_TABLE_CDTFUNCTION_DS_FUNCTION;

    // Consulta SQL con marcadores de posición (?)
    $sql  = "SELECT MO.* ";
    $sql .= ", MG.$menugroup_field_cd_menugroup as MG_cd_menugroup, MG.$menugroup_field_ds_name as MG_ds_name ";
    $sql .= ", F.$function_field_cd_function as F_cd_function, F.$function_field_ds_function as F_ds_function ";
    $sql .= " FROM $tableName MO ";
    $sql .= " LEFT JOIN $tableNameMenuGroup MG ON(MG.$menugroup_field_cd_menugroup=MO.$menuoption_field_cd_menugroup) ";
    $sql .= " LEFT JOIN $tableNameFunction F ON(F.$function_field_cd_function=MO.$menuoption_field_cd_function) ";

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

        if ($db->sql_numrows() > 0) {
            $temp = $db->sql_fetchassoc($stmt->get_result());
            $factory = new CdtMenuOptionFactory("", "MG", "F");
            $obj = $factory->build($temp);
        } else {
            $obj = null;
        }

        mysqli_stmt_free_result($stmt);
        $stmt->close();
        return $obj;
    } else {
        throw new DBException("Error de preparación de consulta.");
    }
}


} 
?>
