<?php

/**
 * Formato para renderizar un valor de la grilla
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 14-12-2011
 *
 */
class GridDateValueFormat extends GridValueFormat{

	private $dateFormat;
	
	public function __construct( $dateFormat = "" ){
		
		parent::__construct("date");
		
		if( empty( $dateFormat ) ) 
			$dateFormat  = "Ymd";
			
		$this->setDateFormat( $dateFormat );
		
	}
	
	public function format( $value, $item=null ){
		
		if ($value != "") {
            //$time = CdtDateUtils::mysqlDateToPHP($value);
            $time = strtotime($value);
            $response = date($this->dateFormat, $time);
        } else {
            $response = "";
        }

        //return $value;
        return $response;
	}		
	

	public function getDateFormat()
	{
	    return $this->dateFormat;
	}

	public function setDateFormat($dateFormat)
	{
	    $this->dateFormat = $dateFormat;
	}

	public function getPattern(){
		return $this->getDateFormat();
	}
}