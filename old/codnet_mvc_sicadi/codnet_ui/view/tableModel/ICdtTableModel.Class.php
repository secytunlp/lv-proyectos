<?php
/**
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 20-04-2010
 *
 * Interfaz que define métodos para obtener información
 * (y meta-información) de una colección o conjunto de elementos.
 *
 */

interface ICdtTableModel{

	/**
	 * retorna el título.
	 * @return
	 */
	function getTitle();

	/**
	 * retorna la cantidad de columnas a visualizar.
	 * @param unknown_type $items
	 * @return cantidad de columnas.
	 */
	function getColumnCount();

	/**
	 * retorna el nombre de la columna para el índice dado.
	 * @param unknown_type $columnIndex
	 * @return descripción
	 */
	function getColumnName($columnIndex);

	/**
	 * retorna el label de la columna para el índice dado.
	 * @param unknown_type $columnIndex
	 * @return descripción
	 */
	function getColumnLabel($columnIndex);

	/**
	 * retorna el ancho de una columna.
	 * @param unknown_type $columnIndex
	 * @return int
	 */
	function getColumnWidth($columnIndex);

	/**
	 * retorna la cantidad de filas en el modelo.
	 * @return unknown_type
	 */
	function getRowCount();

	/**
	 * retorna el valor de una celda.
	 * @param unknown_type $rowIndex
	 * @param unknown_type $columnIndex
	 * @return valor
	 */
	function getValueAt($rowIndex, $columnIndex);

	/**
	 * retorna el valor de la entitdad dado el índice de columna
	 * @param oEntity $oEntity entitdad
	 * @param int $columnIndex índice de la columna
	 * @return anObject.
	 */
	public function getValue($oEntity, $columnIndex);

}
?>