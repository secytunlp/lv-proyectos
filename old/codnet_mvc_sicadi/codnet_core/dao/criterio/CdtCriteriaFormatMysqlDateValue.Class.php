<?php
/**
 * Formatea un valor a usar en el criterio de b�squeda
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 06-05-10
 *
 */
class CdtCriteriaFormatMysqlDateValue extends CdtCriteriaFormatValue{
	
	private $dateFormat;
	private $dateToFormat;
	
	public function __construct( $dateFormat = "", $dateToFormat="" ){
		
		if( empty( $dateFormat ) ) 
			$dateFormat  = DB_DEFAULT_DATE_FORMAT;
			
		if( empty( $dateToFormat ) ){
			if (strstr($this->dateFormat,"H")) {
		    	$dateToFormat = DB_DEFAULT_DATETIME_FORMAT;
		    }
		    else $dateToFormat = DB_DEFAULT_DATE_FORMAT;	
		}
			
		$this->setDateToFormat( $dateToFormat );
	    			
		$this->setDateFormat( $dateFormat );
		
	}
	
	public function getDateFormat()
	{
	    return $this->dateFormat;
	}

	public function setDateFormat($dateFormat)
	{
	    $this->dateFormat = $dateFormat;
	}
	
	public function getDateToFormat()
	{
	    return $this->dateToFormat;
	}
	
	public function setDateToFormat($dateToFormat)
	{
	    $this->dateToFormat = $dateToFormat;
	}
	/**
	 * (non-PHPdoc)
	 * @see CdtCriteriaFormatValue::format();
	 */
	public function format($value){
		
		$value = str_replace('/', '-', $value);
		$time = strtotime( $value );
		
		$res =  date( $this->getDateToFormat(), $time) ;
		
		return  "'$res'" ;
		//return "'" . CdtDateUtils::datePHPToMysqlDate( $this->dateFormat, $value, $this->getDateToFormat()) . "'";
	}
}
?>