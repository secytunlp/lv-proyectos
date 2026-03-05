<?php
/**
 * Formatea un valor a usar en el criterio de búsqueda
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 06-05-10
 *
 */
class CdtCriteriaFormatINValue extends CdtCriteriaFormatValue{
	
	/**
	 * (non-PHPdoc)
	 * @see CdtCriteriaFormatValue::format();
	 */
	public function format($value){
		return "(" . $value . ")";
	}
}
?>