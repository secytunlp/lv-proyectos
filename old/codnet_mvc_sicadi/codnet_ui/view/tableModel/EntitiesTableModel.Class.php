<?php
/**
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 03-05-2010
 * 
 * Table model para las acciones listar.
 * 
 */
abstract class EntitiesTableModel implements ICdtTableModel{

	protected $items; //modelo (ItemCollection).
	
	protected $columnNames = array();

	protected $columnWidths = array();
	
	protected $columnLabels = array();
	
	public function setItems(ItemCollection $items){
		$this->items = $items;
	}
	
	public function getItems(){
		return $this->items;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see ICdtTableModel::getColumnCount();
	 */
	function getColumnCount(){
		return count($this->columnNames);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see ICdtTableModel::getColumnName();
	 */
	function getColumnName($columnIndex){
		return $this->columnNames[$columnIndex];
	}
	
	/**
	 * (non-PHPdoc)
	 * @see ICdtTableModel::getColumnName();
	 */
	function getColumnLabel($columnIndex){
		return $this->columnLabels[$columnIndex];
	}
	
	/**
	 * (non-PHPdoc)
	 * @see ICdtTableModel::getColumnWidth();
	 */
	function getColumnWidth($columnIndex){
		return $this->columnWidths[$columnIndex];
	}
	
	
	/**
	 * se retorna una lista con los encabezados de las columnas.
	 * cada elemento de la lista deberá ser un array de la forma:
	 *    - th['encabezado']='titulo'
	 *    - th['campoOrden']='campo_orden'
	 *    - th['descripcionOrden']='descripción de la ordenación'
	 * se puede usar el método buildTh(nombre, orden, descripcion) para formar dicho arreglo.   
	 * @return unknown_type
	 */
	/**
	 * (non-PHPdoc)
	 * @see ICdtTableModel::getTHs();
	 */
	public function getTHs(){
	 	
		for( $index=0; $index < count($this->columnNames); $index++){

			$ths[]= $this->buildTh($this->getColumnLabel($index), $this->getColumnName($index), $this->getColumnLabel($index));
			
		}
	 	
	 	return $ths;	
	}
	
	
	/**
	 * construye un encabezado.
	 * @param string $title
	 * @param string $orderField
	 * @param string $orderLabel
	 * @return array(string, string, string)
	 */
	protected function buildTh($title, $orderField, $orderLabel){
		$th['label']= $title;
	 	$th['orderField']= $orderField;
	 	$th['orderLabel']= $orderLabel;
		return $th;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see ICdtTableModel#getRowCount()
	 */
	function getRowCount(){
		return $this->getItems()->size();
	}
	
	/**
	 * por default se asume que el item id es la primer columna. 
	 * @param $anObject
	 */
	public function getItemId( $anObject ){
	 	return $this->getValue($anObject, 0);	
	}
	

	/**
	 * campo de ordenamiento por default.
	 * @return string
	 */
	public function getDefaultOrderField(){
		return $this->getColumnName($anObject, 0);
	}

	/**
	 * campo de filtro por default.
	 * @return string
	 */
	protected function getDefaultFilterField(){
		return $this->getDefaultOrderField();
	}
	
	
	/**
	 * (non-PHPdoc)
	 * @see ICdtTableModel::getValueAt();
	 */
	function getValueAt($rowIndex, $columnIndex){
		$oObject = $this->items->getObjectByIndex($rowIndex);
		return $this->getValue($oObject, $columnIndex);
	}
	
	protected function addColumn( $name, $label, $width){
		
		$this->columnLabels[] = $label;
		$this->columnWidths[] = $width;
		$this->columnNames[] = $name;
		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see ICdtTableModel::getValue();
	 */
	public function getValue($anObject, $columnIndex){

		$columnName = $this->getColumnName( $columnIndex );
		
		return CdtReflectionUtils::doGetter( $anObject, $columnName );
	}
	
}
?>