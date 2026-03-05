<?php

/**
 * Modelo para la grilla
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 14-12-2011
 *
 */
class GridTableModelWrapper implements ICdtTableModel {

	private $oGridModel;

	public function GridTableModelWrapper( IGridModel $oGridModel ){

		$this->setGridModel( $oGridModel );
	}

	/**
	 * retorna el título.
	 * @return
	 */
	function getTitle(){
		return $this->getGridModel()->getTitle();
	}

	/**
	 * retorna la cantidad de columnas a visualizar.
	 * @param unknown_type $items
	 * @return cantidad de columnas.
	 */
	function getColumnCount(){
		return $this->getGridModel()->getColumnCount();
	}

	/**
	 * retorna el nombre de la columna para el índice dado.
	 * @param unknown_type $columnIndex
	 * @return descripción
	 */
	function getColumnName($columnIndex){
		return $this->getGridModel()->getColumnModel( $columnIndex)->getDs_name();
	}

	/**
	 * retorna el label de la columna para el índice dado.
	 * @param unknown_type $columnIndex
	 * @return descripción
	 */
	function getColumnLabel($columnIndex){
		return $this->getGridModel()->getColumnModel( $columnIndex)->getDs_label();
	}

	/**
	 * retorna el ancho de una columna.
	 * @param unknown_type $columnIndex
	 * @return int
	 */
	function getColumnWidth($columnIndex){
		return $this->getGridModel()->getColumnModel( $columnIndex)->getNu_width();
	}

	/**
	 * retorna la cantidad de filas en el modelo.
	 * @return unknown_type
	 */
	function getRowCount(){
		return $this->getGridModel()->getRowsCount();
	}

	/**
	 * retorna el valor de una celda.
	 * @param unknown_type $rowIndex
	 * @param unknown_type $columnIndex
	 * @return valor
	 */
	function getValueAt($rowIndex, $columnIndex){
		return $this->getGridModel()->getValueAt($rowIndex, $columnIndex);
	}

	/**
	 * retorna el valor de la entitdad dado el índice de columna
	 * @param oEntity $oEntity entitdad
	 * @param int $columnIndex índice de la columna
	 * @return anObject.
	 */
	public function getValue($oEntity, $columnIndex){
		 $oColumnModel = $this->getGridModel()->getColumnModel($columnIndex);

            $value = $this->getGridModel()->getValue($oEntity, $columnIndex);

            return $oColumnModel->getFormat()->format($value);
		//return $this->getGridModel()->getValue($oEntity, $columnIndex);
	}


	public function getGridModel()
	{
		return $this->oGridModel;
	}

	public function setGridModel($oGridModel)
	{
		$this->oGridModel = $oGridModel;
	}
	
	/**
	 * retorna el título para el archivo exportado.
	 * @return
	 */
	function getExportTitle(){
		return $this->getGridModel()->getExportTitle();
	}
	
}