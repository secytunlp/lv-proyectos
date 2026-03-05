<?php


/**
 * Pasaje 
 *  
 * @author Marcos
 * @since 02-01-2014
 */

class Pasaje {
    
      
    private static $items = array(  
    								   CYT_CD_AEREO=> CYT_DS_AEREO,
    								   CYT_DS_OMNIBUS=> CYT_DS_OMNIBUS,
    								   CYT_DS_AUTOMOVIL=> CYT_DS_AUTOMOVIL,
    								   );
    
	public static function getItems(){
		return self::$items;
	}
	
	public static function getLabel($value){
		return self::$items[$value];
	}
					   
}
?>
