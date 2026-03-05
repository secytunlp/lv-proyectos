<?php
/**
 * Para representar un filtro de búsqueda.
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 25-08-10
 *
 */
class CdtFilterExpression extends CdtExpression{

	private $field;
	private $value;
	private $operator;
	private $format;
		
	public function CdtFilterExpression($field, $value, $operator, $format=null){
		
		if(empty($format))
			$format = new CdtCriteriaFormatValue();
			
		$this->field = $field;
		$this->operator=$operator;
		$this->value = $value;
		$this->format = $format;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtExpression::build();
	 */
	public function build(){
		$filter = '' ;
		$value = $this->format->format( $this->value );
		$filter .= " ( $this->field $this->operator $value ) ";
		return $filter;		
	}
	
	/* Getters & Setters */
	
	public function getValue(){
		return $this->value;
	}	
	
}
	
?>