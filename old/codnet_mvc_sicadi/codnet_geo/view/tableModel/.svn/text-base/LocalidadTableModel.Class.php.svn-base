<?php
/**
 * 
 * @author bernardo
 * @since 03-05-2010
 * 
 * Table model para localidades.
 * 
 */

class LocalidadTableModel extends ListarTableModel{

	private $columnNames = array('Código', 'Localidad',	'Provincia', 'País');

	private $columnWidths = array(35, 80, 80, 80);
	
	public function LocalidadTableModel(ItemCollection $items){
		$this->items = $items;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/view/tableModel/TableModel#getTitle()
	 */
	function getTitle(){
		return "Localidades";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/view/tableModel/TableModel#getColumnCount()
	 */
	function getColumnCount(){
		return 4;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/view/tableModel/TableModel#getColumnName($columnIndex)
	 */
	function getColumnName($columnIndex){
		return $this->columnNames[$columnIndex];
	}
	
	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/view/tableModel/TableModel#getColumnWidth($columnIndex)
	 */
	function getColumnWidth($columnIndex){
		return $this->columnWidths[$columnIndex];
	}
	
	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/view/tableModel/TableModel#getRowCount()
	 */
	function getRowCount(){
		$this->items->size();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/view/tableModel/TableModel#getValueAt($rowIndex, $columnIndex)
	 */
	function getValueAt($rowIndex, $columnIndex){
		$oLocalidad = $this->items->getObjectByIndex($rowIndex);
		return $this->getValue($oLocalidad, $columnIndex);
	}

	public function getValue($anObject, $columnIndex){
		$oLocalidad = $anObject;
		$value=0;
		switch ($columnIndex) {
			case 0: $value= $oLocalidad->getCd_localidad(); break;
			case 1: $value= $oLocalidad->getDs_localidad(); break;
			case 2: $value= $oLocalidad->getDs_provincia(); break;
			case 3: $value= $oLocalidad->getDs_pais(); break;
			default: $value='';	break;
		}
		return $value;
	}
	
	public function getEncabezados(){
	 	$encabezados[]= $this->buildTh($this->getColumnName(0), 'cd_localidad', 'c&oacute;digo de localidad');
	 	$encabezados[]= $this->buildTh($this->getColumnName(1), 'ds_localidad', 'nombre de localidad');
		$encabezados[]= $this->buildTh($this->getColumnName(2), 'ds_provincia', 'provincia');
		$encabezados[]= $this->buildTh($this->getColumnName(3), 'ds_pais', 'pa&iacute;s');
	 	return $encabezados;	
	}
	
}
?>