<?php


/**
 * Concepto 
 *  
 * @author Marcos
 * @since 02-01-2014
 */

class Concepto {
    
      
    private static $items = array(  
    								   CYT_CD_VIATICO=> CYT_DS_VIATICO,
    								   CYT_DS_PASAJE=> CYT_DS_PASAJE,
    								   CYT_CD_INSCRIPCION=> CYT_DS_INSCRIPCION,
    								   CYT_CD_OTROS=> CYT_CD_OTROS,
    								   );
    
	public static function getItems(){
		return self::$items;
	}
	
	public static function getLabel($value){
		return self::$items[$value];
	}
					   
}
?>
