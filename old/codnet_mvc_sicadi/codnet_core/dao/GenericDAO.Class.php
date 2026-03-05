<?php

/**
 * DAO para GenÃ©rico
 *  
 * @author Bernardo
 * @since 05-03-2013
 */
abstract class GenericDAO {

	
	public abstract function getFieldsToAdd($entity);
	
	public abstract function getFieldsToUpdate($entity);
	
	public abstract function getId($entity);
	
	public abstract function getIdFieldName();
	
	public abstract function setId($entity, $id);
	
	public abstract function getTableName();
	
	public abstract function getEntityFactory();

	public function getEntitiesSQL(CdtSearchCriteria $oCriteria){
	
		$sql = "SELECT " . implode(", ", $this->getFieldsToSelect() );
        $sql .= " FROM " . $this->getFromToSelect() ;
        return $sql;	
		
	}
	
	public function getEntitiesCountSQL(CdtSearchCriteria $oCriteria){
		
		$sql = "SELECT count(*) as count";
        $sql .= " FROM " . $this->getFromToSelect() ;
        
        return $sql;
	}
	
	public function getFromToSelect(){
	
		return $this->getTableName();
	}
	
	public function getFieldsToSelect(){
		$fields = array();
		$fields[] = $this->getTableName() . ".*";
		return $fields;
	}
	
	
    /**
     * se persiste la nueva entity
     * @param $entity entity a persistir.
     */
    public function addEntity($entity, $idConn = 0)
	{
		$db = CdtDbManager::getConnection($idConn);

		$fields = $this->getFieldsToAdd($entity);

		$strFields = array();
		$strParams = array();
		$paramValues = array();
		$paramTypes = "";

		foreach ($fields as $name => $value) {

			if ($value === 'null') {
				//CdtUtils::log('nulo');
			
			} else {
				//CdtUtils::log('no nulo');
				$strFields[] = $name;
				$strParams[] = "?";
				// Asumiendo que todos los valores son cadenas, ajusta el tipo de parámetro según sea necesario.
				$paramTypes .= "s";
				$paramValues[] = preg_replace("/^'(.*)'$/", "$1", $value);
			}


			
		}

		$strFields = implode(",", $strFields);
		$strParams = implode(",", $strParams);
		$tableName = $this->getTableName();

		$sql = "INSERT INTO $tableName ( $strFields ) VALUES( $strParams )";

		$stmt = $db->sql_prepare($sql);

		// Verifica si la preparación fue exitosa.
		if ($stmt) {
			$paramRefs = array();
			foreach ($paramValues as $key => $value) {
				$paramRefs[$key] = &$paramValues[$key]; // Obtener referencias a los valores
			}

			array_unshift($paramRefs, $paramTypes);

			// Llamar a mysqli_stmt_bind_param con el array de referencias de valores.
			call_user_func_array(array($stmt, 'bind_param'), $paramRefs);

			if ($db->sql_execute($stmt)) {
				//seteamos el nuevo id.
				$id = $db->sql_nextid();
				$this->setId($entity, $id);
			} else {
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
 * Actualiza la entidad en la base de datos.
 *
 * @param $entity Entity a modificar.
 * @param int $idConn ID de conexión (opcional).
 */
public function updateEntity($entity, $idConn = 0)
{
    $db = CdtDbManager::getConnection($idConn);

    $fields = $this->getFieldsToUpdate($entity);

    $strFields = array();
    $paramTypes = "";
    $paramValues = array();

    foreach ($fields as $name => $value) {

        //CdtUtils::log($name.' => '.$value);
		if ($value === 'null') {
			//CdtUtils::log('nulo');
            // Si el valor es nulo, asigna NULL en la consulta SQL sin comillas.
            $strFields[] = "$name = NULL";
        } else {
			//CdtUtils::log('no nulo');
			$strFields[] = "$name = ?";
			// Asumiendo que todos los valores son cadenas, ajusta el tipo de parámetro según sea necesario.
			$paramTypes .= "s";
			//$paramValues[] = $value;
			// Elimina las comillas simples del principio y del final
			$paramValues[] = preg_replace("/^'(.*)'$/", "$1", $value);
		}
    }
	//CdtUtils::logObject($paramValues);
    $strFields = implode(",", $strFields);
    $tableName = $this->getTableName();

    $id = CdtFormatUtils::ifEmpty($this->getId($entity), 'null');
    $idName = $this->getIdFieldName();

    $sql = "UPDATE $tableName SET $strFields WHERE $idName = ?";

    //CdtUtils::log($sql, __CLASS__, LoggerLevel::getLevelDebug());

    // Añade el valor del ID al final del array de parámetros.
    $paramTypes .= "i";
    $paramValues[] = $id;
	//CdtUtils::logObject($paramValues);
    $stmt = $db->sql_prepare($sql);

    // Verifica si la preparación fue exitosa.
    if ($stmt) {
        // Usar call_user_func_array para pasar un array de parámetros a mysqli_stmt_bind_param.
        $paramRefs = array();
        foreach ($paramValues as $key => $value) {
            $paramRefs[$key] = &$paramValues[$key]; // Obtener referencias a los valores
        }

        array_unshift($paramRefs, $paramTypes);

        // Llamar a mysqli_stmt_bind_param con el array de referencias de valores.
        call_user_func_array(array($stmt, 'bind_param'), $paramRefs);

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
 * Elimina la entidad de la base de datos.
 *
 * @param int $oid Identificador de la entidad a eliminar.
 * @param int $idConn ID de conexión (opcional).
 */
public function deleteEntity($oid, $idConn = 0)
{
    $db = CdtDbManager::getConnection($idConn);

    $idName = $this->getIdFieldName();
    $tableName = $this->getTableName();

    $sql = "DELETE FROM $tableName WHERE $idName = ?";

    //CdtUtils::log($sql, __CLASS__, LoggerLevel::getLevelDebug());

    $stmt = $db->sql_prepare($sql);

    // Verifica si la preparación fue exitosa.
    if ($stmt) {
        // Bind del parámetro de ID.
        mysqli_stmt_bind_param($stmt, 'i', $oid);

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
 * Obtiene una colección de entidades dado el filtro de búsqueda.
 *
 * @param CdtSearchCriteria $oCriteria Filtro de búsqueda.
 * @param int $idConn ID de conexión (opcional).
 * @return ItemCollection
 */
public function getEntities(CdtSearchCriteria $oCriteria, $idConn = 0)
{
   
	$db = CdtDbManager::getConnection($idConn);

    // Obtenemos la consulta SQL sin aplicar el filtro.
    $sql = $this->getEntitiesSQL($oCriteria);
	//CdtUtils::log($sql, __CLASS__, LoggerLevel::getLevelDebug());
    // Agregamos el filtro construido por el criterio de búsqueda.
    
	$criteriaParams = $oCriteria->buildCriteria();
    $sql .= $criteriaParams['where'];
    $params = $criteriaParams['params'];

    CdtUtils::logObject($params);

    $stmt = $db->sql_prepare($sql);

    
    CdtUtils::log($sql, __CLASS__, LoggerLevel::getLevelDebug());

    

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
        $items = CdtResultFactory::toCollection($db, $stmt->get_result(), $this->getEntityFactory());

        // Cerramos la sentencia preparada.
        mysqli_stmt_free_result($stmt);
		$stmt->close();

        return $items;
    } else {
        throw new DBException("Error de preparación de consulta.");
    }
}




   /**
 * Obtiene la cantidad de entidades que cumplen con el filtro de búsqueda.
 *
 * @param CdtSearchCriteria $oCriteria Filtro de búsqueda.
 * @param int $idConn ID de conexión (opcional).
 * @return int Cantidad de entidades que cumplen con el filtro.
 */
public function getEntitiesCount(CdtSearchCriteria $oCriteria, $idConn = 0)
{
    $db = CdtDbManager::getConnection($idConn);

    // Obtenemos la consulta SQL sin aplicar el filtro.
    $sql = $this->getEntitiesCountSQL($oCriteria);

    // Agregamos el filtro construido por el criterio de búsqueda.
    $criteriaParams= $oCriteria->buildCriteriaWithoutPaging();
	$sql .= $criteriaParams['where'];
    $params = $criteriaParams['params'];
   
	//CdtUtils::log($sql, __CLASS__, LoggerLevel::getLevelDebug());
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
		$stmt->close();

        return (int) $cant;
    } else {
        throw new DBException("Error de preparación de consulta.");
    }
}


    /**
 * Obtiene una entidad que cumple con el filtro de búsqueda.
 *
 * @param CdtSearchCriteria $oCriteria Filtro de búsqueda.
 * @param int $idConn ID de conexión (opcional).
 * @return Entity|null Entidad que cumple con el filtro o null si no se encuentra.
 */
public function getEntity(CdtSearchCriteria $oCriteria, $idConn = 0)
{
    $db = CdtDbManager::getConnection($idConn);

    $idName = $this->getIdFieldName();

    // Para evitar ambigüedades de identificador.
    $oCriteria->renameFilter("$idName", $this->getTableName() . ".$idName");

    // Obtenemos la consulta SQL sin aplicar el filtro.
    $sql = $this->getEntitiesSQL($oCriteria);

    // Agregamos el filtro construido por el criterio de búsqueda.
    $criteriaParams = $oCriteria->buildCriteria();
    $sql .= $criteriaParams['where'];
    $params = $criteriaParams['params'];
	
	CdtUtils::log($sql, __CLASS__, LoggerLevel::getLevelDebug());
	CdtUtils::logObject($params);

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
		
		$factory = $this->getEntityFactory();

		if ($stmt_result = mysqli_stmt_get_result($stmt)) {
			$row = $db->sql_fetchassoc($stmt_result);
			
			if ($row) {
				// Si hay un resultado válido, crea el objeto
				$obj = $factory->build($row);
			}
		}
	




	


    // Liberar los resultados y cerrar la sentencia preparada.
    mysqli_stmt_free_result($stmt);
    $stmt->close();

    return $obj;
} else {
    throw new DBException("Error de preparación de consulta.");
}
}



	public function formatString( $value ){
		$res = ($value !== null)?addslashes($value):'';
		return "'$res'";
	}
	
	public function formatIfNull( $value, $nullValue = "null"){
		return CdtFormatUtils::ifEmpty($value, $nullValue);
	}
	
	public function getEntityById($id) {
        $oCriteria = new CdtSearchCriteria();
        $idName = $this->getIdFieldName();
        $oCriteria->addFilter( $this->getTableName() . ".$idName", $id, "=");
        return self::getEntity($oCriteria);
    }

	public function formatDate( $value, $format = "Y-m-d H:i:s"){
		
		if(empty($value))
			return "null";
		
		$value = str_replace('/', '-', $value);
		$time = strtotime( $value );
		$res = $this->formatString( date( $format, $time) );
		
		//CdtUtils::log("formatDate value $value, format $format, result $res", __CLASS__, LoggerLevel::getLevelDebug());
		
		return $res;
	}
    
}
?>
