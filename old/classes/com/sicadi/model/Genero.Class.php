<?php


/**
 * Genero
 *  
 * @author Marcos
 * @since 25-04-2023
 */

class Genero {





	private static $items = array(
		'F'=> 'Mujer',
		'MT'=> 'Mujer-Trans',
		'T'=> 'Travesti',
		'M'=> 'Varón',
		'VT'=> 'Varón-Trans',
		'NB'=> 'No Binarie',
		'O'=> 'Otro',
		'PN'=> 'Prefiero no responder',
	);







	public static function getItems(){
		return self::$items;
	}

	public static function getLabel($value){
		return self::$items[$value];
	}

}
?>
