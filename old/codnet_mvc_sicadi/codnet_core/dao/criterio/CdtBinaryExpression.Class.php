<?php
/**
 * Para representar una expresión binaria.
 * 
 * Ej: X AND Y
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 25-08-10
 *
 */
class CdtBinaryExpression extends CdtExpression{

	private $operator;
	private $oLeftExpression;
	private $oRightExpression;
	
	public function CdtBinaryExpression( $operator="AND", CdtExpression $left, CdtExpression $right){
		$this->operator = $operator;
		$this->oLeftExpression = $left;
		$this->oRightExpression = $right;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtExpression::build();
	 */
	public function build(){
		return " ( " . $this->oLeftExpression->build() . " $this->operator " . $this->oRightExpression->build() . " ) ";  	
	}
	
}
	
?>