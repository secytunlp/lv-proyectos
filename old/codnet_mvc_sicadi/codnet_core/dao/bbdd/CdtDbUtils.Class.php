<?php
/**
 * Administra las conexiones a la bbdd.
 *
 * @author Marcos Piñero
 * @since 26-09-2023
 *
 */
class CdtDbUtils {

	/**
 * Obtiene los tipos de parámetros para la función mysqli_stmt_bind_param.
 *
 * @param array $params Parámetros del criterio.
 * @return string Tipos de parámetros.
 */
private function getParamTypes(array $params)
{
    $paramTypes = '';
    foreach ($params as $param) {
        if (is_int($param)) {
            $paramTypes .= 'i'; // Entero.
        } elseif (is_double($param)) {
            $paramTypes .= 'd'; // Doble/decimal.
        } else {
            $paramTypes .= 's'; // Cadena (por defecto).
        }
    }
    return $paramTypes;
}

/**
 * Vincula los parámetros a la consulta preparada.
 *
 * @param mysqli_stmt $stmt Sentencia preparada.
 * @param string $paramTypes Tipos de parámetros.
 * @param mixed ...$params Valores de los parámetros.
 * @return mysqli_stmt Sentencia preparada vinculada con los parámetros.
 */
private function bindParamsToStatement($stmt, $paramTypes, ...$params)
{
    
	if (!empty($paramTypes) && !empty($params)) {
        $bindParams = array_merge([$paramTypes], $params);
        call_user_func_array([$stmt, 'bind_param'], $this->makeValuesReferenced($bindParams));
    }
    return $stmt;
}

/**
 * Convierte los valores en referencias para la función mysqli_stmt_bind_param.
 *
 * @param array $arr Valores a convertir en referencias.
 * @return array Valores convertidos en referencias.
 */
private function makeValuesReferenced(array $arr)
{
    $refs = array();
    foreach ($arr as $key => $value) {
        $refs[$key] = &$arr[$key];
    }
    return $refs;
}
	
}

?>