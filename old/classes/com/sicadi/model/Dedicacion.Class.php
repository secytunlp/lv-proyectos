<?php


/**
 * Dedicacion
 *  
 * @author Marcos
 * @since 08-05-2023
 */

class Dedicacion {





	private static $items = array(
		'1'=> 'Exclusiva',
		'2'=> 'Semi Exclusiva',
		'3'=> 'Simple',
	);

	public static function getItems(){
		return self::$items;
	}

	public static function getLabel($value){
		return self::$items[$value];
	}

}
?>
