<?php
/**
 * 
 * @author bernardo
 * @since 03-05-2010
 * 
 * Table model para paises.
 * 
 */

class PaisTableModel extends ListarTableModel{

	private $columnNames = array('Código', 'País');

	private $columnWidths = array(110, 165);
	
	public function PaisTableModel(ItemCollection $items){
		$this->items = $items;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/view/tableModel/TableModel#getTitle()
	 */
	function getTitle(){
		return "Paises";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/view/tableModel/TableModel#getColumnCount()
	 */
	function getColumnCount(){
		return 2;
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
		$oPais = $this->items->getObjectByIndex($rowIndex);
		return $this->getValue($oPais, $columnIndex);
	}

	public function getValue($anObject, $columnIndex){
		$oPais = $anObject;
		$value=0;
		switch ($columnIndex) {
			case 0: $value= $oPais->getCd_pais(); break;
			case 1: $value= $oPais->getDs_pais(); break;
			default: $value='';	break;
		}
		return $value;
	}
	
	public function getEncabezados(){
	 	$encabezados[]= $this->buildTh($this->getColumnName(0), 'cd_pais', 'c&oacute;digo de pais');
		$encabezados[]= $this->buildTh($this->getColumnName(1), 'ds_pais', 'pais');
	 	return $encabezados;	
	}
	
}
?>