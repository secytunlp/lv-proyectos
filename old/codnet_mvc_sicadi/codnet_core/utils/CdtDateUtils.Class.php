<?php

/**
 * Utilidades para fechas.
 * Funciones �tiles para el tratamiento de fechas.
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 18/10/2011
 */
class CdtDateUtils {
	
	/**
	 * toma una fecha string mysql y retorna el time en php.
	 * el par�metro ser� un string con formato fecha YYYYmmdd (20113101)
	 * @param $value
	 */
	public static function mysqlDateToPHP( $value, $format="" ){
		
		if(empty($format)){
			$format = (strlen( $value ) > 8)?"YmdHis":"Ymd";
		}
		
		$oDateTime = CdtDateClass::createFromFormatOldVersion( $format, $value);
		return $oDateTime->getTimestamp();	
	}
	
	
	/**
	 * toma una fecha+hora string mysql y retorna el time en php.
	 * el par�metro ser� un string con formato fecha YYYYmmddhhiiss (20113101173010)
	 * @param $value
	 */
	public static function mysqlDateTimeToPHP( $value ){
		
		$d  = substr($value, - 2);
        $m  = substr($value, 4, 2);
        $y  = substr($value, 0, 4);
        $h  = substr($value, 8, 2);
        $i = substr($value, 10, 2);
        $s = substr($value, -2);
        
        $time =  mktime($h, $i, $s, $m  , $d, $y );
        return $time;
	}
	
	/**
	 * toma una fecha en php y retorna e fecha string tipo mysql (YYYYmmdd).
	 * el par�metro ser� un time php
	 * @param $format formato de la fecha php.
	 * @param $value string fecha php.
	 * @return fecha string mysql (YYYYmmdd).
	 */
	public static function datePHPToMysqlDate( $format, $value, $toFormat = "Ymd" ){

		return self::convertFormat( $value, $format, $toFormat );
		
	}
	
	public static function convertFormat( $value, $fromFormat, $toFormat ){
		return CdtDateClass::createFromFormatOldVersion($fromFormat, $value)->format($toFormat);
	}
	
	/**
	 * toma una fecha en php (time) y retorna e fecha string tipo mysql (YYYYmmdd).
	 * el par�metro ser� un time php
	 * @param $value string (YYYYmmdd)
	 */
	public static function timePHPToMysqlDate( $value ){
		
		return date( "Ymd" , $value );			
		
	}
	
	public static function phpToJQueryPattern( $phpPattern ){
		
		$jqueryPattern = str_replace("Y", "yy", $phpPattern);
		
		$jqueryPattern = str_replace("m", "mm", $jqueryPattern);
		$jqueryPattern = str_replace("d", "dd", $jqueryPattern);
		
		return $jqueryPattern;
	}
	
	/* 
	 * TODO
	 * habr�a que ir cambiando los nombres de todas las funciones siguientes
	 *  
	 * */
	
    /* * ******************************************************
     * 	Convierte una fecha con formato "20/06/2008
     *   al formato con el que se almacena en la BD 20080620
     * ******************************************************* */

    static function fechaPHPaMysql($fechaPHP) {
        $fechaPHP = str_replace("-", "/", $fechaPHP);
        $nuevaFecha = explode("/", $fechaPHP);
        //invierto los campos
        $fechaMySql [0] = $nuevaFecha [2];
        $fechaMySql [1] = $nuevaFecha [1];
        $fechaMySql [2] = $nuevaFecha [0];
        $fechaMySql = implode("", $fechaMySql);
        return ($fechaMySql);
    }

    //Entrada: $fechaPHP en formato mm-dd-yy
    //Salida: $fechaPHP en formato yyyymmdd
    static function fechaPHPmmddyyaMysql($fechaPHP) {
        $fechaPHP = str_replace("-", "/", $fechaPHP);
        $nuevaFecha = explode("/", $fechaPHP);
        //invierto los campos
        $fechaMySql [0] = $nuevaFecha [2]; //anio
        $fechaMySql [1] = $nuevaFecha [0]; // mes
        $fechaMySql [2] = $nuevaFecha [1]; // dia

        $fechaMySql = implode("", $fechaMySql);
        return ($fechaMySql);
    }
    
	//Entrada: $fechaPHP en formato dd-mm-yy
    //Salida: $fechaPHP en formato yyyymmdd
    static function fechaPHPddmmyyaMysql($fechaPHP) {
        $fechaPHP = str_replace("-", "/", $fechaPHP);
        $nuevaFecha = explode("/", $fechaPHP);
        //invierto los campos
        $fechaMySql [0] = $nuevaFecha [2]; //anio
        $fechaMySql [1] = $nuevaFecha [1]; // mes
        $fechaMySql [2] = $nuevaFecha [0]; // dia

        $fechaMySql = implode("", $fechaMySql);
        return ($fechaMySql);
    }

     //Entrada: $fechaPHP en formato yy-mm-dd
    //Salida: $fechaPHP en formato yyyymmdd
    static function fechaPHPyymmddaMysql($fechaPHP) {
        $fechaPHP = str_replace("/", "-", $fechaPHP);
        $nuevaFecha = explode("-", $fechaPHP);
        $fechaMySql = implode("", $nuevaFecha);
        return ($fechaMySql);
    }

    //Entrada: $fechaMysql en formato yyyymmdd
    //Salida: $fechaPHP en formato mm-dd-yy
    static function fechaMysqlammddyyPHP($fechaMysql) {
        //20080618 yyyymmdd
        $arrayFecha [0] = substr($fechaMysql, 4, 2);
        $arrayFecha [1] = substr($fechaMysql, - 2);
        $arrayFecha [2] = substr($fechaMysql, 0, 4);
        $fechaPHP = implode("-", $arrayFecha);
        return $fechaPHP;
    }

    //Entrada: $fechaMysql en formato yyyymmdd
    //Salida: $fechaPHP en formato yy-mm-dd
    static function fechaMysqlayymmddPHP($fechaMysql) {
        //20080618 yyyymmdd
        $arrayFecha [0] = substr($fechaMysql, 0, 4);
        $arrayFecha [1] = substr($fechaMysql, 4, 2);
        $arrayFecha [2] = substr($fechaMysql, - 2);
        
        $fechaPHP = implode("-", $arrayFecha);
        return $fechaPHP;
    }

    static function fechaMysqlaPHP($fechaMysql) {
        //20080618
        $arrayFecha [0] = substr($fechaMysql, - 2);
        $arrayFecha [1] = substr($fechaMysql, 4, 2);
        $arrayFecha [2] = substr($fechaMysql, 0, 4);
        $fechaPHP = implode("/", $arrayFecha);
        return $fechaPHP;
    }

    static function fechaStringToDateFormatYmd($fechaMysql) {
        //20080618
        $arrayFecha [2] = substr($fechaMysql, - 2);
        $arrayFecha [1] = substr($fechaMysql, 4, 2);
        $arrayFecha [0] = substr($fechaMysql, 0, 4);
        $fechaDateFormat = implode("-", $arrayFecha);
        return $fechaDateFormat;
    }

    static function fechaHoraMysqlaPHP($fechaMysql) {
        //20080618
        $arrayFecha [0] = substr($fechaMysql, 6, 2);
        $arrayFecha [1] = substr($fechaMysql, 4, 2);
        $arrayFecha [2] = substr($fechaMysql, 0, 4);
        $fechaPHP = implode("/", $arrayFecha);
        $arrayHora [0] = substr($fechaMysql, 8, 2);
        $arrayHora [1] = substr($fechaMysql, 10, 2);
        $arrayHora [2] = substr($fechaMysql, -2);
        $horaPHP = implode(":", $arrayHora);
        return $fechaPHP . ' ' . $horaPHP;
    }
    
	    
	//Entrada: $fechaPHP en formato dd-mm-yy HH:mm:ss
    //Salida: $fechaPHP en formato yyyymmddhhmmss
    static function fechaHoraPHPddmmyyaMysql($fechaPHP) {
        $fechaPHP = str_replace("-", "/", $fechaPHP);
        $nuevaFecha = explode("/", $fechaPHP);
        //invierto los campos
        $fechaMySql [0] = substr($nuevaFecha [2], 0, 4); //a�o
        $fechaMySql [1] = $nuevaFecha [1]; // mes
        $fechaMySql [2] = $nuevaFecha [0]; // dia
        $fechaMySql = implode("", $fechaMySql);
        $horaPHP = explode(":", substr($nuevaFecha [2],5,8));
        $horaMySql = implode("", $horaPHP);
        return ($fechaMySql.$horaMySql);
    }

    


    static function getMesDeNumero($numero) {
        $meses = array('01' => 'Enero',
            '02' => 'Febrero',
            '03' => 'Marzo',
            '04' => 'Abril',
            '05' => 'Mayo',
            '06' => 'Junio',
            '07' => 'Julio',
            '08' => 'Agosto',
            '09' => 'Septiembre',
            '1' => 'Enero',
            '2' => 'Febrero',
            '3' => 'Marzo',
            '4' => 'Abril',
            '5' => 'Mayo',
            '6' => 'Junio',
            '7' => 'Julio',
            '8' => 'Agosto',
            '9' => 'Septiembre',
            '10' => 'Octubre',
            '11' => 'Noviembre',
            '12' => 'Diciembre');
        return $meses[$numero];
    }


    public static function getFechaHoraActual() {

        $f = date('YmdHis');
        return $f;
    }

    public static function getFechaActual() {
        $f = date('Ymd');
        return $f;
    }

	//verifica si es valida una fecha xx-xx-xxxx o xx/xx/xxxx y la pasa a formato mssql
    static function validar_fechaPHPaMssql($fecha) {
    	list($dd, $mm, $yyyy) = split('[/.-]', $fecha);
    	If(!@checkdate($mm,$dd,$yyyy)){
			$fechaMSSql=false;
		}
		else{
			$fechaMSSql=$yyyy.'-'.$mm.'-'.$dd.' 00:00:00';
		}
        return ($fechaMSSql);
    }
}
