<?php
/**
 * Para representar una expresi�n simple, o sea, s�lo el campo, sin operadores.
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 06-05-11
 *
 */
class CdtSimpleExpression extends CdtExpression{

	private $field;
		
	public function __construct($field){
		
		$this->field = $field;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtExpression::build();
	 */
	public function build(){
		return $this->field;
	}
	
}
	
?>