<?php

/**
 * Formato para renderizar un número en la grilla
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 21-06-2012
 *
 */
class GridNumberValueFormat extends GridValueFormat{

	private $dec_point;
	private $thousands_sep;
	private $decimals;
	private $symbol;
	private $symbol_side;
	private $nullValue;
	
	public function __construct( $decimals = 0 , $dec_point = '.' , $thousands_sep = ',', $symbol = "", $symbol_side = 1, $nullValue = "" ){
		
		parent::__construct("number");
		
		$this->setDecimals($decimals);
		$this->setDec_point($dec_point);
		$this->setThousands_sep($thousands_sep);
		$this->setSymbol($symbol);
		$this->setNullValue($nullValue);
		$this->setSymbol_side($symbol_side);
	}
	
	public function format( $value, $item=null ){
		
		if( $value == null )
			$value = $this->nullValue;
		
		
		if ( isset($value) && $value!="") {
            //$time = CdtDateUtils::mysqlDateToPHP($value);
            //$response = date($this->dateFormat, $time);
            $response = number_format ( $value , $this->decimals , $this->dec_point, $this->thousands_sep );
        } else {
            $response = "";
        }
		
        if( !empty( $this->symbol ) ){
        	
        	if( $this->symbol_side == 1 )
        		$response = "$this->symbol $response";
        	else	
        		$response = "$response $this->symbol";
        }
        
        
        
        //return $value;
        return $response;
	}		
	

	public function getPattern(){
		return "";
	}

	public function getDec_point()
	{
	    return $this->dec_point;
	}

	public function setDec_point($dec_point)
	{
	    $this->dec_point = $dec_point;
	}

	public function getThousands_sep()
	{
	    return $this->thousands_sep;
	}

	public function setThousands_sep($thousands_sep)
	{
	    $this->thousands_sep = $thousands_sep;
	}

	public function getDecimals()
	{
	    return $this->decimals;
	}

	public function setDecimals($decimals)
	{
	    $this->decimals = $decimals;
	}

	public function getSymbol()
	{
	    return $this->symbol;
	}

	public function setSymbol($symbol)
	{
	    $this->symbol = $symbol;
	}

	public function getSymbol_side()
	{
	    return $this->symbol_side;
	}

	public function setSymbol_side($symbol_side)
	{
	    $this->symbol_side = $symbol_side;
	}

	public function getNullValue()
	{
	    return $this->nullValue;
	}

	public function setNullValue($nullValue)
	{
	    $this->nullValue = $nullValue;
	}
}