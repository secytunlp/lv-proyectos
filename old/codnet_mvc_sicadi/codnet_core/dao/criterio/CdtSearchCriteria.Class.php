<?php
/**
 * Para representar criterios de bï¿½squeda.
 *
 * La idea es no tener que modificar la especificaciï¿½n de un mï¿½todo
 * cada vez que queremos agregar criterios de bï¿½squeda a un query.
 *
 * Ej:
 *    -  buscar($id, $nombre);
 *    despuï¿½s queremos buscar tambiï¿½n por domicilio y telï¿½fono
 *    -  buscar($id, $nombre, $domicilio, $telefono);
 *    y ademï¿½s luego queremos filtrar tambiï¿½n por celular
 *    -  buscar($id, $nombre, $domicilio, $telefono, $celular);
 *
 *    y asï¿½ va creciendo la especificaciï¿½n del mï¿½todo.
 *
 *    Usando un criterio de bï¿½squeda serï¿½a:
 *
 *    - buscar ($criterio );
 *
 *    y vamos modificando el criterio de bï¿½squeda a medida que sea necesario.
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 27-04-10
 *
 */
class CdtSearchCriteria
{
    private $notNullFields;
    private $nullFields;
    private $orderFields;
    private $groupByFields;
    private $page;
    private $rowPerPage;
    private $filters;
    private $filtersHaving;
    private $oExpresion;
    private $tableName;

    public function __construct()
    {
        $this->filters = array();
        $this->filtersHaving = array();
        $this->notNullFields = array();
        $this->nullFields = array();
        $this->orderFields = array();
        $this->groupByFields = array();
        $this->oExpresion = null;
    }

    public function addFilter($field, $value, $operator, $format = null, $operatorWhere = "AND")
    {
        if (empty($format)) {
            $format = new CdtCriteriaFormatValue();
        }

        $this->filters[] = array('field' => $field, 'operator' => $operator, 'value' => $value, 'format' => $format, 'operatorWhere' => $operatorWhere);
    }

    public function addFilterHaving($field, $value, $operator, $format = null)
    {
        if (empty($format)) {
            $format = new FormatValue();
        }

        $this->filtersHaving[] = array('field' => $field, 'operator' => $operator, 'value' => $value, 'format' => $format);
    }

    public function addNotNull($field)
    {
        $this->notNullFields[] = $field;
    }

    public function addNull($field)
    {
        $this->nullFields[] = $field;
    }

    public function addOrder($field, $value = 'ASC')
    {
        $this->orderFields[] = " $field $value ";
    }

    public function addGroupBy($field)
    {
        $this->groupByFields[] = $field;
    }

    protected function getFieldName($name)
    {
        $field = $name;

        $names = explode(".", $name);

        if (count($names) == 1) {
            $tableName = $this->getTableName();
            if (!empty($tableName)) {
                $field = $tableName . "." . $name;
				//CdtUtils::log('tabla: '.$tableName . '.' . $name):
					//CdtUtils::log('tabla '.$tableName);
            }
        }

        return $field;
    }

    public function buildWHERE()
	{
		$where = '';
		$params = array();

		foreach ($this->filters as $key => $filterData) {
			// Verifica si se trata de un filtro con marcadores de posición
			if (isset($filterData['expression']) && isset($filterData['values'])) {
				$expression = $filterData['expression'];
				$values = $filterData['values'];
				$operatorWhere = $filterData['operatorWhere'];

				// Agrega el filtro con marcadores de posición a la cláusula WHERE
				$where .= " $operatorWhere ($expression)";

				// Agrega los valores al array de parámetros
				$params = array_merge($params, $values);
			} else {
				$field = $this->getFieldName($filterData['field']);
				$operator = $filterData['operator'];
				$value = $filterData['value'];
				$format = $filterData['format'];
				$operatorWhere = $filterData['operatorWhere'];

				if ($operator == "LIKE") {
					$value = $filterData['value'];
					$words = explode(" ", $value);

					if (count($words) > 1) {
						$whereLIKE = array();
						foreach ($words as $word) {
							//$word = $format->format($word);
							$paramName = "?";
							$whereLIKE[] = "$field $operator $paramName";
							$params[] = '%' . $word . '%';
						}
						$whereLIKE = implode(" AND ", $whereLIKE);
					} else {
						//$value = $format->format($value);
						$paramName = "?";
						$whereLIKE = "$field $operator $paramName";
						$params[] = '%' . $value . '%';
					}

					$where .= " $operatorWhere ($whereLIKE)";
				} else {
					$paramName = "?";
					$where .= " $operatorWhere $field $operator $paramName";
					$params[] = $value;
				}
			}
		}

		foreach ($this->getNotNullFields() as $field) {
			$where .= " AND not ($field is null OR $field='')";
		}

		foreach ($this->getNullFields() as $field) {
			$where .= " AND ($field is null)";
		}

		if (!empty($this->oExpresion)) {
			$where .= " AND  " . $this->oExpresion->build();
		}

		if (!empty($where)) {
			$where = substr($where, 4);
			$where = " WHERE " . $where;
		}

		return array('where' => $where, 'params' => $params);
	}


    public function buildHAVING()
    {
        $having = '';
        $params = array();

        foreach ($this->filtersHaving as $key => $field_op_value) {
            $field = $this->getFieldName($field_op_value['field']);
            $op = $field_op_value['operator'];
            $value = $field_op_value['value'];
            $format = $field_op_value['format'];
            $value = $format->format($value);
            $paramName = "?";
            $having .= " AND $field $op $paramName";
            $params[] = $value;
        }

        if (!empty($having)) {
            $having = substr($having, 4);
            $having = " HAVING " . $having;
        }

        return array('having' => $having, 'params' => $params);
    }


	// Función para verificar si el nombre de la columna es válido para la tabla dada
	private function isValidColumnName($tableName, $columnName) {
		// Implementa la lógica para verificar si $columnName es válido para la tabla $tableName
		// Puedes consultar la estructura de la tabla en la base de datos para obtener
		// las columnas válidas para esa tabla y comparar con $columnName.
		
		// Ejemplo de verificación si el nombre de columna es válido para la tabla:
		// Puedes consultar la base de datos para obtener las columnas válidas de la tabla
		$validColumns = $this->getValidColumnsForTable($tableName);
		$found = false;

		foreach ($validColumns as $validColumn) {
			//CdtUtils::log('valida: '.$validColumn.' - '.$columnName);
			/*echo "Valid Column: [" . $validColumn . "]\n";
			echo "Column Name: [" . $columnName . "]\n";*/
			if (strlen(trim($validColumn)) === strlen(trim($columnName)) && strcasecmp(trim($validColumn), trim($columnName)) === 0) {
				//CdtUtils::log('encuentra');
				$found = true;
				break;
			}
		}

		return $found;
		 
	}

	// Función para obtener las columnas válidas para una tabla específica desde la base de datos (ejemplo para MySQL)
	private function getValidColumnsForTable($tableName) {
		
		
		return array('facultad. cd_facultad ASC','facultad. cd_facultad DESC','estadocategorizacion. cd_estado ASC','estadocategorizacion. cd_estado DESC','periodo. cd_periodo ASC' ,'periodo. cd_periodo DESC','solicitudcategorizacion. cd_solicitud ASC','solicitudcategorizacion. cd_solicitud DESC','nu_order ASC','nu_order DESC','MO.nu_order ASC','MO.nu_order DESC','estadocategorizacion.ds_estado ASC','estadocategorizacion.ds_estado DESC','estadocategorizacion. cd_estado ASC','estadocategorizacion. cd_estado DESC','periodo.ds_periodo ASC','periodo.ds_periodo DESC','docente.ds_apellido DESC','docente.ds_apellido ASC','docente.ds_nombre DESC','docente.ds_nombre ASC','solicitudcategorizacion. dt_fecha ASC','solicitudcategorizacion. dt_fecha DESC','FacultadPlanilla.ds_facultad ASC','FacultadPlanilla.ds_facultad DESC','Categoria.ds_categoria DESC','Categoria.ds_categoria ASC','Equivalencia.ds_equivalencia ASC','Equivalencia.ds_equivalencia DESC','categoriasicadi.ds_categoriasicadi DESC','categoriasicadi.ds_categoriasicadi ASC','cyt_solicitudcategorizacion_estado. oid DESC','cyt_solicitudcategorizacion_estado. oid ASC','unidad. ds_unidad ASC','unidad. ds_unidad DESC','cargo. nu_orden ASC','cargo. nu_orden DESC','facultad. cd_facultad ASC','facultad. cd_facultad DESC','categoria. cd_categoria ASC','categoria. cd_categoria DESC','categoriasicadi. cd_categoriasicadi ASC','categoriasicadi. cd_categoriasicadi DESC','equivalencia. ds_equivalencia ASC','equivalencia. ds_equivalencia DESC','organismo. cd_organismo ASC','organismo. cd_organismo DESC','unidad. cd_unidad ASC','unidad. cd_unidad DESC','area. ds_area ASC','area. ds_area DESC','subarea. cd_subarea ASC','subarea. cd_subarea DESC','cargo. nu_orden ASC','cargo. nu_orden DESC','solicitudcategorizacion. oid DESC','solicitudcategorizacion. oid ASC','universidad.nu_orden DESC','universidad.nu_orden ASC');
	}

    public function buildORDERBY()
	{
		$order = '';

		$tableName = $this->getTableName(); // Obtener el nombre de la tabla

		for ($index = 0; $index < count($this->orderFields); $index++) {
			$fieldName = $this->getFieldName($this->orderFields[$index]);
			//CdtUtils::log('Campo orden: '.$fieldName);
			// Verifica si $fieldName es un nombre de columna válido para la tabla
			if ($this->isValidColumnName($tableName, $fieldName)) {
				switch (trim($fieldName)) {
					case 'solicitudcategorizacion. oid DESC':
						$fieldName='solicitudcategorizacion.cd_solicitud DESC';
						break;
					case 'solicitudcategorizacion. oid ASC':
						$fieldName='solicitudcategorizacion.cd_solicitud ASC';
						break;
					case 'Categoria.ds_categoria ASC':
						$fieldName='categoria_ds_categoria ASC';
						break;
					case 'Categoria.ds_categoria DESC':
						$fieldName='categoria_ds_categoria DESC';
						break;
					case 'Equivalencia.ds_equivalencia DESC':
						$fieldName='equivalencia_ds_equivalencia DESC';
						break;
					case 'Equivalencia.ds_equivalencia ASC':
						$fieldName='equivalencia_ds_equivalencia ASC';
						break;
					
				}
				$order .= ", " . $fieldName;
				//CdtUtils::log('orden establecido: '.$order);
			}
			else{
				CdtUtils::log('Falta campo: '.$fieldName);
			}
		}

		if (!empty($order)) {
			$order = substr($order, 2);
			$order = " ORDER BY " . $order;
		}

		return $order;
	}

	public function addFilterWithPlaceholders($expression, $values, $operatorWhere = "AND")
	{
		// Verifica si la expresión y los valores son válidos
		if (!empty($expression) && is_array($values) && !empty($values)) {
			// Agrega el filtro con marcadores de posición
			$this->filters[] = array('expression' => $expression, 'values' => $values, 'operatorWhere' => $operatorWhere);
		}
	}

    public function buildGROUPBY()
    {
        $group = '';

        foreach ($this->groupByFields as $field) {
            $field = $this->getFieldName($field);
            $group .= ", $field ";
        }

        if (!empty($group)) {
            $group = substr($group, 2);
            $group = " GROUP BY " . $group;
        }

        return $group;
    }

    public function buildLIMIT()
    {
        $limit = '';

        if (!empty($this->page)) {
            $limitInf = (($this->page - 1) * $this->rowPerPage);
            $limit .= " LIMIT ?, ?";
        }

        return $limit;
    }

    public function buildCriteria()
    {
        $whereParams = $this->buildWHERE();
        $havingParams = $this->buildHAVING();

        $criteria = $whereParams['where'];
        $params = $whereParams['params'];

        $criteria .= $this->buildGROUPBY();
        $criteria .= $havingParams['having'];
        $params = array_merge($params, $havingParams['params']);
        $criteria .= $this->buildORDERBY();
        //$criteria .= $this->buildLIMIT();
		if (!empty($this->page) && !empty($this->rowPerPage)) {
			// Asegúrate de que $this->page y $this->rowPerPage sean enteros válidos
			$page = intval($this->page);
			$rowPerPage = intval($this->rowPerPage);

			// Usa una sentencia preparada para el paginado
			$criteria .= " LIMIT ?, ?";
			$params[] = $page > 0 ? ($page - 1) * $rowPerPage : 0; // Calcula el offset
			$params[] = $rowPerPage;
		}

		return array('where' => $criteria, 'params' => $params);
    }

    public function buildCriteriaWithoutPaging()
    {
        $whereAndParams = $this->buildWHERE();
        $where = $whereAndParams['where'];
        $params = $whereAndParams['params'];

        return array('where' => $where . $this->buildGROUPBY(), 'params' => $params);
    }

    public function removeFilter($fieldName)
    {
        $filter = null;
        $ok = false;
        foreach ($this->filters as $key => $field_op_value) {
            $field = $field_op_value['field'];
            if ($fieldName == $field) {
                $filter = $key;
                $ok = true;
            }
        }
        if ($ok) {
            unset($this->filters[$filter]);
        }
    }

    public function getFilterValue($fieldName)
    {
        $value = null;
        foreach ($this->filters as $key => $field_op_value) {
            $field = $field_op_value['field'];
            if ($fieldName == $field) {
                $value = $field_op_value['value'];
            }
        }
        return $value;
    }

    public function renameFilter($fieldName, $newName)
    {
        $filter = null;
        $ok = false;
        foreach ($this->filters as $key => $field_op_value) {
            $field = $field_op_value['field'];
            if ($fieldName == $field) {
                $filter = $key;
                $ok = true;
            }
        }
        if ($ok) {
            $newFilter = $this->filters[$filter];
            unset($this->filters[$filter]);
            $newFilter["field"] = $newName;
            $this->filters[$newName] = $newFilter;
        }
    }

    public function setExpresion(CdtExpression $exp)
    {
        $this->oExpresion = $exp;
    }

    public function setPage($page)
    {
        $this->page = $page;
    }

    public function setRowPerPage($rowPerPage)
    {
        $this->rowPerPage = $rowPerPage;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function getRowPerPage()
    {
        return $this->rowPerPage;
    }

    public function getNotNullFields()
    {
        return $this->notNullFields;
    }

    public function getNullFields()
    {
        return $this->nullFields;
    }

    public function getFilters()
    {
        return $this->filters;
    }

    public function getTableName()
    {
        return $this->tableName;
    }

    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    public function getExpresion()
    {
        return $this->oExpresion;
    }
}



?>