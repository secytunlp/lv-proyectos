<?php

//define("SQL_LAYER","mysql");

class MySQL implements ICdtDatabase
{
    private $db_connect_id;
    private $stmt;
    private $stmt_result;
    private $row;
    private $rowset;
    private $num_queries = 0;
    private $query_result;
    private $user;
    private $password;
    private $server;
    private $dbname;

    function __construct()
    {
        $this->row = array();
        $this->rowset = array();
    }

    function connect($sqlserver, $sqluser, $sqlpassword, $database)
	{
		$this->user = $sqluser;
		$this->password = $sqlpassword;
		$this->server = $sqlserver;
		$this->dbname = $database;

		$this->db_connect_id = mysqli_connect($this->server, $this->user, $this->password);

		if ($this->db_connect_id) {
			if ($database != "") {
				$this->dbname = $database;
				$dbselect = @mysqli_select_db($this->db_connect_id, $this->dbname);
				if (!$dbselect) {
					@mysqli_close($this->db_connect_id);
					$this->db_connect_id = $dbselect;
				}
			}
			
			// Configurar la codificación a utf8mb4
			mysqli_set_charset($this->db_connect_id, "utf8mb4");
			
			return $this->db_connect_id;
		} else {
			return false;
		}
	}


    function sql_close()
    {
        if ($this->db_connect_id) {
            $result = mysqli_close($this->db_connect_id);
            return $result;
        } else {
            return false;
        }
    }

    function sql_query($query)
{
    $query = $this->decode($query);

    // Elimina la liberación de resultados anteriores
    // unset($this->stmt_result);

    $this->num_queries++;

    $has_multiquery = count(explode(";", $query)) > 1;

    if ($has_multiquery) {
        mysqli_multi_query($this->db_connect_id, $query);
        $this->stmt_result = mysqli_store_result($this->db_connect_id);
    } else {
        $this->stmt = mysqli_prepare($this->db_connect_id, $query);
        if ($this->stmt) {
            mysqli_stmt_execute($this->stmt);
            $this->stmt_result = mysqli_stmt_get_result($this->stmt);
        }
    }

    // No es necesario liberar los resultados aquí

    return $this->stmt_result;
}


    function sql_bind_params($types, ...$params)
    {
        if ($this->stmt) {
            mysqli_stmt_bind_param($this->stmt, $types, ...$params);
        }
    }

    // Resto de las funciones existentes...

    function begin_tran()
    {
        return mysqli_autocommit($this->db_connect_id, false);
    }

    function commit_tran()
    {
        return mysqli_commit($this->db_connect_id);
    }

    function rollback_tran()
    {
        return mysqli_rollback($this->db_connect_id);
    }

    function db_connect_id()
    {
        return $this->db_connect_id;
    }

    function encode($value)
    {
        // Implementa tu lógica de codificación aquí si es necesario.
        return $value;
    }

    function decode($value)
    {
        // Implementa tu lógica de decodificación aquí si es necesario.
        return $value;
    }

    function sql_execute($stmt)
    {
        if ($stmt) {
            return mysqli_stmt_execute($stmt);
        } else {
            return false;
        }
    }

    function sql_prepare($query)
    {
        $query = $this->decode($query);

        $this->stmt = mysqli_prepare($this->db_connect_id, $query);

        return $this->stmt;
    }

    //
    // Other query methods
    //
    function sql_numrows($query_id = 0)
    {
        if (!$query_id) {
            $query_id = $this->stmt_result;
        }
		
        if ($query_id) {
            $result = mysqli_num_rows($query_id);
            return $result;
        } else {
            return false;
        }
    }

    function sql_affectedrows()
    {
        if ($this->db_connect_id) {
            $result = mysqli_affected_rows($this->db_connect_id);
            return $result;
        } else {
            return false;
        }
    }

    function sql_numfields($query_id = 0)
    {
        if (!$query_id) {
            $query_id = $this->stmt_result;
        }
        if ($query_id) {
            $result = mysqli_num_fields($query_id);
            return $result;
        } else {
            return false;
        }
    }

    function sql_fieldname($offset, $query_id = 0)
    {
        if (!$query_id) {
            $query_id = $this->stmt_result;
        }
        if ($query_id) {
            $result = mysqli_field_name($query_id, $offset);
            return $result;
        } else {
            return false;
        }
    }

    function sql_fetchrow($query_id = 0)
    {
        if (!$query_id) {
            $query_id = $this->stmt_result;
        }
        if ($query_id) {
            $this->row[$query_id] = mysqli_fetch_array($query_id);
            return $this->row[$query_id];
        } else {
            return false;
        }
    }

    function sql_fetchrowset($query_id = 0)
    {
        if (!$query_id) {
            $query_id = $this->stmt_result;
        }
        if ($query_id) {
            unset($this->rowset[$query_id]);
            unset($this->row[$query_id]);
            while ($this->rowset[$query_id] = mysqli_fetch_array($query_id)) {
                $result[] = $this->rowset[$query_id];
            }
            return $result;
        } else {
            return false;
        }
    }

    function sql_fetchfield($field, $rownum = -1, $query_id = 0)
    {
        if (!$query_id) {
            $query_id = $this->stmt_result;
        }
        if ($query_id) {
            if ($rownum > -1) {
                $result = mysqli_result($query_id, $rownum, $field);
            } else {
                if (empty($this->row[$query_id]) && empty($this->rowset[$query_id])) {
                    if ($this->sql_fetchrow()) {
                        $result = $this->row[$query_id][$field];
                    }
                } else {
                    if ($this->rowset[$query_id]) {
                        $result = $this->rowset[$query_id][$field];
                    } else if ($this->row[$query_id]) {
                        $result = $this->row[$query_id][$field];
                    }
                }
            }
            return $this->encode($result);
        } else {
            return false;
        }
    }

    function sql_rowseek($rownum, $query_id = 0)
    {
        if (!$query_id) {
            $query_id = $this->stmt_result;
        }
        if ($query_id) {
            $result = mysqli_data_seek($query_id, $rownum);
            return $result;
        } else {
            return false;
        }
    }

    function sql_nextid()
    {
        if ($this->db_connect_id) {
            $result = mysqli_insert_id($this->db_connect_id);
            return $result;
        } else {
            return false;
        }
    }

    function sql_freeresult($query_id = 0)
    {
        if (!$query_id) {
            $query_id = $this->stmt_result;
        }
        mysqli_free_result($query_id);
    }

    function sql_error($query_id = 0)
    {
        $result["message"] = mysqli_error($this->db_connect_id);
        $result["code"] = mysqli_errno($this->db_connect_id);

        return $result;
    }

    //agregadas, solo estan para mysql
    function sql_result($result, $indice)
    {
        $row = mysqli_fetch_array($result, MYSQLI_NUM);
        return $this->encode($row[$indice]);
    }

    //agregadas, solo estan para mysql
    function sql_fetchassoc($result)
    {
        //return  mysqli_fetch_assoc($result);
        /*
        $res =  mysqli_fetch_assoc($result);
        for ($i=0; $i<count($res); $i++){
            $res[$i] = utf8_encode($res[$i]);
        }
        return  $res;*/
        //CdtUtils::logObject($result);
        $res =  mysqli_fetch_assoc($result);
        //CdtUtils::logObject($res);
        if ($res != null) {
            $keys = array_keys($res);
            foreach ($keys as $key) {
                $res[$key] = $this->encode(($res[$key]));
            }
        }

        return $res;
    }


	function sql_fieldtype($offset, $query_id = 0)
{
    if (!$query_id) {
        $query_id = $this->stmt_result;
    }
    if ($query_id) {
        $result = mysqli_fetch_field_direct($query_id, $offset);
        return $result->type;
    } else {
        return false;
    }
}

}





?>