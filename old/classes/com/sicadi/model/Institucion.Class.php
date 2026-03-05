<?php


/**
 * Institucion 
 *  
 * @author Marcos
 * @since 10-11-2016
 */

class Institucion {
    
    const ANPCyT = 'ANPCyT';
    const CIC = 'CIC';
	const CONICET = 'CONICET';
	const UNLP = 'UNLP';
	const OTRA = 'OTRA';
    
   
										
    
    private static $itemsBeca = array(
									   Institucion::UNLP=> CYT_LBL_SOLICITUD_INSTITUCION_BECA_UNLP,
    								   Institucion::ANPCyT=> CYT_LBL_SOLICITUD_INSTITUCION_BECA_ANPCyT,
    								   Institucion::CIC=> CYT_LBL_SOLICITUD_INSTITUCION_BECA_CIC,
    								   Institucion::CONICET=> CYT_LBL_SOLICITUD_INSTITUCION_BECA_CONICET,
									   Institucion::OTRA=> CYT_LBL_SOLICITUD_INSTITUCION_PROYECTO_OTRA,
    								   );
	private static $itemsProyecto = array(
		Institucion::ANPCyT=> CYT_LBL_SOLICITUD_INSTITUCION_BECA_ANPCyT,
		Institucion::CIC=> CYT_LBL_SOLICITUD_INSTITUCION_BECA_CIC,
		Institucion::CONICET=> CYT_LBL_SOLICITUD_INSTITUCION_BECA_CONICET,
		Institucion::OTRA=> CYT_LBL_SOLICITUD_INSTITUCION_PROYECTO_OTRA,
	);



	public static function getItems($tipo){
		switch ($tipo) {
			case 'beca':
				return self::$itemsBeca;
				break;
			case 'proyecto':
				return self::$itemsProyecto;
				break;

		}

	}

	
	public static function getLabel($value){
		return self::$items[$value];
	}
					   
}
?>
