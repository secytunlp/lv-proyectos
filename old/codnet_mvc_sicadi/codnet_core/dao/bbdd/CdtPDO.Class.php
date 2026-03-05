<?php
/**
 * Interfaz que define los métodos para interactuar
 * con un manejador de base de datos.
 * 
 * @author bernardo
 * @since 12-03-2010
 *
 */
class CdtPDO implements ICdtDatabase {
	
	/**
	 * conectar con la bbdd.
	 * @return link identifier / FALSE: si se produce error.
	 */
	public function connect($sqlserver, $sqluser, $sqlpassword, $database){
		
		
		
	}

	/**
	 * cerrar la conección actual.
	 * @return TRUE: éxito / FALSE: error.
	 */
	public function sql_close();

	/**
	 * iniciar una transacción.
	 * @return TRUE: éxito / FALSE: error. 
	 */
	function begin_tran();

	/**
	 * comitear una transacción.
	 * @return TRUE: éxito / FALSE: error.
	 */
	function commit_tran();

	/**
	 * rollback de una transacción.
	 * @return TRUE: éxito / FALSE: error.
	 */
	function rollback_tran();

	/**
	 * ejecutar una consulta.
	 * @param  $query sql string.
	 * @return resource: sentencias que retornen resultset (SELECT, DESCRIBE, EXPLAIN, ...)
	 *         TRUE: cuando ejecutamos operaciones que alteran los datos (INSERT, UPDATE, DELETE, DROP, ...)
	 *         FALSE: cuando se produce un error.
	 */
	function sql_query($query);

	/**
	 * número de filas de una consulta dada.
	 * @param $result
	 * @return int
	 */
	function sql_numrows($result = 0);
	
	/**
	 * retorna el número de registros afectados en el 
	 * último INSERT, UPDATE, REPLACE o DELETE ejecutado.
	 * @return int
	 */
	function sql_affectedrows();
	
	/**
	 * retorna la cantidad de fields de una consulta.
	 * @param $result resultado de una consulta.
	 * @return int
	 */
	function sql_numfields($result = 0);
	
	/**
	 * retorna el nombre del field asociado al índice
	 * @param $offset
	 * @param $result resultado de una consulta.
	 * @return string
	 */
	function sql_fieldname($offset, $result = 0);
	
	/**
	 * retorna el type del field asociado al índice
	 * @param $offset índice
	 * @param $result resultado de una consulta.
	 * @return field type
	 */
	function sql_fieldtype($offset, $result = 0);
	
	/**
	 * retorna la fila actual del resultado como un arreglo.
	 * @param $result resultado de una consulta.
	 * @return arreglo con la fila actual / FALSE: si no hay más filas. 
	 */
	function sql_fetchrow($result = 0);
	
	/**
	 * TODO
	 * @param $result
	 * @return unknown_type
	 */
	function sql_fetchrowset($result = 0);
	
	/**
	 * retorna información asociado a un field. 
	 * @param $field field
	 * @param $rownum número de fila
	 * @param $result resultado de una consulta.
	 * @return unknown_type
	 */
	function sql_fetchfield($field, $rownum = -1, $query_id = 0);
	
	/**
	 * se mueve a la fila especificada dentro del resultado.
	 * @param $rownum fila a moverse.
	 * @param $result resultado de consulta.
	 * @return TRUE: éxito / FALSE: error.
	 */
	function sql_rowseek($rownum, $result = 0);
	
	/**
	 * retorna el id generado en el último query.
	 * @return int / 0 si no generó un autoincremento / FALSE si no hay conexión.
	 */
	function sql_nextid();
	
	/**
	 * libera memoria del resultado.
	 * @param $result resultado de consulta.
	 * @return TRUE: éxito / FALSE: error.
	 */
	function sql_freeresult($result = 0);
	
	/**
	 * retorna información del error generado por la última operación.
	 * @param $result
	 * @return array["message"] = mensaje de error / array["code"] = código del error.
	 */
	function sql_error($result = 0);
	
	
	/**
	 * Retorna el contenido de una celda del resultado.
	 * @param $result resultado de consulta.
	 * @param $indice íncide
	 * @return unknown_type
	 * TODO:siempre retorna el primer field ya que no está definido el field.
	 * VER SI SE USA.
	 */
	function sql_result($result, $indice);
	
	/**
	 * retorna la fila actual como un arreglo asociativo.
	 * @param $result resultado de consulta.
	 * @return array
	 */
	function sql_fetchassoc($result);
	
	/**
	 * retorna el link de conexión actual.
	 * @return unknown_type
	 */
	function db_connect_id();	
}

?>