<?php
/**
 * Para representar una expresión unaria.
 * 
 * Ej: NOT Y
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 25-08-10
 *
 */
class CdtUnaryExpression extends CdtExpression{

	private $operator;
	private $operatorBefore;
	private $oExpression;
	
	public function CdtUnaryExpression( $operator="NOT", CdtExpression $exp, $operatorBefore=true){
		$this->operator = $operator;
		$this->operatorBefore = $operatorBefore;
		$this->oExpression = $exp;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtExpression::build();
	 */
	public function build(){
		if( $this->operatorBefore )
			return  " ( $this->operator " . $this->oExpression->build() . " ) ";
		else  
			return  " ( " . $this->oExpression->build() . "  $this->operator ) ";	
	}
	
}
	
?>