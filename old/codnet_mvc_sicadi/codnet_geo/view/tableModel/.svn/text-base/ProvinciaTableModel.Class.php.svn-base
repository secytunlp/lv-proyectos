<?php
/**
 * 
 * @author bernardo
 * @since 03-05-2010
 * 
 * Table model para provincias.
 * 
 */

class ProvinciaTableModel extends ListarTableModel{

	private $columnNames = array('Código', 'Provincia', 'País');

	private $columnWidths = array(35, 120, 120);
	
	public function ProvinciaTableModel(ItemCollection $items){
		$this->items = $items;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/view/tableModel/TableModel#getTitle()
	 */
	function getTitle(){
		return "Provincias";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/view/tableModel/TableModel#getColumnCount()
	 */
	function getColumnCount(){
		return 3;
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
		$oProvincia = $this->items->getObjectByIndex($rowIndex);
		return $this->getValue($oProvincia, $columnIndex);
	}

	public function getValue($anObject, $columnIndex){
		$oProvincia = $anObject;
		$value=0;
		switch ($columnIndex) {
			case 0: $value= $oProvincia->getCd_provincia(); break;
			case 1: $value= $oProvincia->getDs_provincia(); break;
			case 2: $value= $oProvincia->getDs_pais(); break;
			default: $value='';	break;
		}
		return $value;
	}
	
	public function getEncabezados(){
	 	$encabezados[]= $this->buildTh($this->getColumnName(0), 'cd_provincia', 'c&oacute;digo de provincia');
		$encabezados[]= $this->buildTh($this->getColumnName(1), 'ds_provincia', 'provincia');
		$encabezados[]= $this->buildTh($this->getColumnName(2), 'ds_pais', 'pa&iacute;s');
	 	return $encabezados;	
	}
	
}
?>