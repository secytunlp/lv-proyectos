<?php


/**
 * Carrera
 *
 * @author Marcos
 * @since 16-06-2023
 */

class Carrera {

	const Investigador_Asistente_CONICET = '6';
	const Investigador_Adjunto_CONICET = '12';
	const Investigador_Independiente_CONICET = '3';
	const Investigador_Principal_CONICET = '2';
	const Investigador_Superior_CONICET = '1';
	const Profesional_Asistente_CONICET = '13';
	const Profesional_Adjunto_CONICET = '9';
	const Profesional_Principal_CONICET = '8';
	const Investigador_Asistente_CIC = '6';
	const Investigador_Adjunto_CIC = '12';
	const Investigador_Independiente_CIC = '3';
	const Investigador_Principal_CIC = '2';
	const Investigador_Superior_CIC = '1';
	const Profesional_Asistente_CIC = '13';
	const Profesional_Adjunto_CIC = '9';
	const Profesional_Principal_CIC = '8';







	/*private static $itemsCONICET = array(
		CYT_LBL_SOLICITUD_CARRERA_INVESTIGADOR_ASISTENTE_CONICET=> Carrera::Investigador_Asistente_CONICET,
		CYT_LBL_SOLICITUD_CARRERA_INVESTIGADOR_ADJUNTO_CONICET=> Carrera::Investigador_Adjunto_CONICET,
		CYT_LBL_SOLICITUD_CARRERA_INVESTIGADOR_INDEPENDIENTE_CONICET=> Carrera::Investigador_Independiente_CONICET,
		CYT_LBL_SOLICITUD_CARRERA_INVESTIGADOR_PRINCIPAL_CONICET=> Carrera::Investigador_Principal_CONICET,
		CYT_LBL_SOLICITUD_CARRERA_INVESTIGADOR_SUPERIOR_CONICET=> Carrera::Investigador_Superior_CONICET,
		CYT_LBL_SOLICITUD_CARRERA_PROFESIONAL_ASISTENTE_CONICET=> Carrera::Profesional_Asistente_CONICET,
		CYT_LBL_SOLICITUD_CARRERA_PROFESIONAL_ADJUNTO_CONICET=> Carrera::Profesional_Adjunto_CONICET,
		CYT_LBL_SOLICITUD_CARRERA_PROFESIONAL_PRINCIPAL_CONICET=> Carrera::Profesional_Principal_CONICET,

	);
	private static $itemsCIC = array(
		CYT_LBL_SOLICITUD_CARRERA_INVESTIGADOR_ASISTENTE_CIC => Carrera::Investigador_Asistente_CIC,
		CYT_LBL_SOLICITUD_CARRERA_INVESTIGADOR_ADJUNTO_CIC => Carrera::Investigador_Adjunto_CIC,
		CYT_LBL_SOLICITUD_CARRERA_INVESTIGADOR_INDEPENDIENTE_CIC => Carrera::Investigador_Independiente_CIC,
		CYT_LBL_SOLICITUD_CARRERA_INVESTIGADOR_PRINCIPAL_CIC => Carrera::Investigador_Principal_CIC,
		CYT_LBL_SOLICITUD_CARRERA_INVESTIGADOR_SUPERIOR_CIC => Carrera::Investigador_Superior_CIC,
		CYT_LBL_SOLICITUD_CARRERA_PROFESIONAL_ASISTENTE_CIC => Carrera::Profesional_Asistente_CIC,
		CYT_LBL_SOLICITUD_CARRERA_PROFESIONAL_ADJUNTO_CIC => Carrera::Profesional_Adjunto_CIC,
		CYT_LBL_SOLICITUD_CARRERA_PROFESIONAL_PRINCIPAL_CIC => Carrera::Profesional_Principal_CIC,
	);*/

	private static $itemsCONICET = array(
		Carrera::Investigador_Asistente_CONICET=> CYT_LBL_SOLICITUD_CARRERA_INVESTIGADOR_ASISTENTE_CONICET,
		Carrera::Investigador_Adjunto_CONICET=>CYT_LBL_SOLICITUD_CARRERA_INVESTIGADOR_ADJUNTO_CONICET ,
		Carrera::Investigador_Independiente_CONICET=> CYT_LBL_SOLICITUD_CARRERA_INVESTIGADOR_INDEPENDIENTE_CONICET,
		Carrera::Investigador_Principal_CONICET=>CYT_LBL_SOLICITUD_CARRERA_INVESTIGADOR_PRINCIPAL_CONICET ,
		Carrera::Investigador_Superior_CONICET=> CYT_LBL_SOLICITUD_CARRERA_INVESTIGADOR_SUPERIOR_CONICET,
		Carrera::Profesional_Asistente_CONICET=> CYT_LBL_SOLICITUD_CARRERA_PROFESIONAL_ASISTENTE_CONICET,
		Carrera::Profesional_Adjunto_CONICET=> CYT_LBL_SOLICITUD_CARRERA_PROFESIONAL_ADJUNTO_CONICET,
		Carrera::Profesional_Principal_CONICET=> CYT_LBL_SOLICITUD_CARRERA_PROFESIONAL_PRINCIPAL_CONICET,

	);
	private static $itemsCIC = array(
		Carrera::Investigador_Asistente_CIC => CYT_LBL_SOLICITUD_CARRERA_INVESTIGADOR_ASISTENTE_CIC,
		Carrera::Investigador_Adjunto_CIC => CYT_LBL_SOLICITUD_CARRERA_INVESTIGADOR_ADJUNTO_CIC,
		Carrera::Investigador_Independiente_CIC => CYT_LBL_SOLICITUD_CARRERA_INVESTIGADOR_INDEPENDIENTE_CIC,
		Carrera::Investigador_Principal_CIC => CYT_LBL_SOLICITUD_CARRERA_INVESTIGADOR_PRINCIPAL_CIC,
		Carrera::Investigador_Superior_CIC => CYT_LBL_SOLICITUD_CARRERA_INVESTIGADOR_SUPERIOR_CIC,
		Carrera::Profesional_Asistente_CIC => CYT_LBL_SOLICITUD_CARRERA_PROFESIONAL_ASISTENTE_CIC,
		Carrera::Profesional_Adjunto_CIC => CYT_LBL_SOLICITUD_CARRERA_PROFESIONAL_ADJUNTO_CIC,
		Carrera::Profesional_Principal_CIC => CYT_LBL_SOLICITUD_CARRERA_PROFESIONAL_PRINCIPAL_CIC,
	);



	public static function getItems($organismo){
		switch ($organismo) {
			case '2':
				return self::$itemsCONICET;
				break;
			case '1':

				return self::$itemsCIC;
				break;

		}

	}


	public static function getLabel($value){
		return self::$items[$value];
	}

}
?>
