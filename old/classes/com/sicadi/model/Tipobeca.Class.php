<?php


/**
 * Tipobeca 
 *  
 * @author Marcos
 * @since 10-11-2016
 */

class Tipobeca {
    
 
	const AGENCIA1 = 'Beca Inicial';
	const AGENCIA2 = 'Beca Superior';

	const CIC2 = 'Beca Doctoral';
	const CIC3 = 'Beca Posdoctoral';
	const CONICET1 = 'Beca Doctoral';
	const CONICET2 = 'Beca Finalizacion del Doctorado';
	const CONICET3 = 'Beca Posdoctoral';

	const UNLP6 = "Beca Doctoral";
	const UNLP7 = 'Beca Maestria';
	const UNLP8 = 'Beca Posdoctoral';


    
   
										
    
    private static $itemsAgencia = array(  
    								   Tipobeca::AGENCIA1=> CYT_LBL_SOLICITUD_TIPO_BECA_AGENCIA1,
    								   Tipobeca::AGENCIA2=> CYT_LBL_SOLICITUD_TIPO_BECA_AGENCIA2,
    								  
    								   );
    								   
    private static $itemsCIC = array(  
    								   Tipobeca::CIC2=> CYT_LBL_SOLICITUD_TIPO_BECA_CIC2,
    								   Tipobeca::CIC3=> CYT_LBL_SOLICITUD_TIPO_BECA_CIC3,
    								  
    								   );
    private static $itemsCONICET = array(  
    								   Tipobeca::CONICET1=> CYT_LBL_SOLICITUD_TIPO_BECA_CONICET1,
    								   Tipobeca::CONICET2=> CYT_LBL_SOLICITUD_TIPO_BECA_CONICET2,
    								   Tipobeca::CONICET3=> CYT_LBL_SOLICITUD_TIPO_BECA_CONICET3,
    								  
    								   );								   
    
    private static $itemsUNLP = array(  

    								   Tipobeca::UNLP6=> CYT_LBL_SOLICITUD_TIPO_BECA_UNLP6,
    								   Tipobeca::UNLP7=> CYT_LBL_SOLICITUD_TIPO_BECA_UNLP7,
    								   Tipobeca::UNLP8=> CYT_LBL_SOLICITUD_TIPO_BECA_UNLP8,

    								   );
	private static $itemsOtra = array(
		Tipobeca::CIC2=> CYT_LBL_SOLICITUD_TIPO_BECA_CIC2,
		Tipobeca::CIC3=> CYT_LBL_SOLICITUD_TIPO_BECA_CIC3,

	);
   	private static $items = array(  
    								   
    								''=> "--seleccionar--",
   									Tipobeca::AGENCIA1=> CYT_LBL_SOLICITUD_TIPO_BECA_AGENCIA1,
    								   Tipobeca::AGENCIA2=> CYT_LBL_SOLICITUD_TIPO_BECA_AGENCIA2,
    								  
    								  

    								   Tipobeca::CIC2=> CYT_LBL_SOLICITUD_TIPO_BECA_CIC2,
    								  Tipobeca::CIC3=> CYT_LBL_SOLICITUD_TIPO_BECA_CIC3,
    								  
    								   Tipobeca::CONICET1=> CYT_LBL_SOLICITUD_TIPO_BECA_CONICET1,
    								   Tipobeca::CONICET2=> CYT_LBL_SOLICITUD_TIPO_BECA_CONICET2,
    								  Tipobeca::CONICET3=> CYT_LBL_SOLICITUD_TIPO_BECA_CONICET3,
    								

    								   Tipobeca::UNLP6=> CYT_LBL_SOLICITUD_TIPO_BECA_UNLP6,
    								   Tipobeca::UNLP7=> CYT_LBL_SOLICITUD_TIPO_BECA_UNLP7,
    								   Tipobeca::UNLP8=> CYT_LBL_SOLICITUD_TIPO_BECA_UNLP8,

    								   );									   
	public static function getItems($institucion=''){
		switch ($institucion) {
			case 'ANPCyT':
				return self::$itemsAgencia;
			break;
			case 'CIC':
				return self::$itemsCIC;
			break;
			case 'CONICET':
				return self::$itemsCONICET;
			break;
			case 'UNLP':
				return self::$itemsUNLP;
			break;
			case 'OTRA':
				return self::$itemsOtra;
				break;
			default:
				return self::$items;
			break;
		}
		
	}
	
	public static function getLabel($value){
		return self::$items[$value];
	}
					   
}
?>
