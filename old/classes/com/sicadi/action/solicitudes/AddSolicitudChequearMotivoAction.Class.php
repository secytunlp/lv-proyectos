<?php

/**
 * Se chequea el motivo
 * 
 * @author Marcos
 * @since 26-12-2013
 *
 */
class AddSolicitudChequearMotivoAction extends CdtAction{


	/**
	 * (non-PHPdoc)
	 * @see CdtAction::execute();
	 */
	public function execute(){

		
		$result = "";
		
		try{
			
			$motivo_oid = CdtUtils::getParam("motivo_oid");
			
			//$valido = true; //TODO validar
			$show = array();
			$hide = array();
			
			
			switch ($motivo_oid) {
				case CYT_MOTIVO_A:
				$show[]='ds_objetivo';
				$show[]='bl_congreso';
				$show[]='ds_trabajo';
				$show[]='ds_aceptacion';
				$show[]='ds_titulotrabajo';
				$show[]='ds_autorestrabajo';
				$show[]='ds_congreso';
				$show[]='bl_nacional';
				$show[]='ds_lugartrabajo';
				$show[]='dt_fechatrabajo';
				$show[]='ds_resumentrabajo';
				$hide[]='ds_invitaciongrupo';
				$hide[]='ds_aval';
				$hide[]='ds_convenio';
				$hide[]='ds_generalB';
				$hide[]='ds_especificoB';
				$hide[]='ds_actividadesB';
				$hide[]='ds_cronogramaB';
				$hide[]='ds_aportesB';
				$hide[]='ds_cvprofesor';
				$hide[]='ds_profesor';
				$hide[]='ds_lugarprofesor';
				$hide[]='ds_objetivoC';
				$hide[]='ds_planC';
				$hide[]='ds_aportesC';
				$hide[]='ds_actividadesC';
				break;
				
				case CYT_MOTIVO_B:
				$hide[]='ds_objetivo';
				$hide[]='bl_congreso';
				$hide[]='ds_trabajo';
				$hide[]='ds_aceptacion';
				$hide[]='ds_titulotrabajo';
				$hide[]='ds_autorestrabajo';
				$hide[]='ds_congreso';
				$hide[]='bl_nacional';
				$hide[]='ds_lugartrabajo';
				$hide[]='dt_fechatrabajo';
				$hide[]='ds_resumentrabajo';
				$show[]='ds_invitaciongrupo';
				$show[]='ds_invitaciongrupo';
				$show[]='ds_aval';
				$show[]='ds_convenio';
				$show[]='ds_generalB';
				$show[]='ds_especificoB';
				$show[]='ds_actividadesB';
				$show[]='ds_cronogramaB';
				$show[]='ds_aportesB';
				$hide[]='ds_cvprofesor';
				$hide[]='ds_profesor';
				$hide[]='ds_lugarprofesor';
				$hide[]='ds_objetivoC';
				$hide[]='ds_planC';
				$hide[]='ds_aportesC';
				$hide[]='ds_actividadesC';
				break;
				
				case CYT_MOTIVO_C:
				$hide[]='ds_objetivo';
				$hide[]='bl_congreso';
				$hide[]='ds_trabajo';
				$hide[]='ds_aceptacion';
				$hide[]='ds_titulotrabajo';
				$hide[]='ds_autorestrabajo';
				$hide[]='ds_congreso';
				$hide[]='bl_nacional';
				$hide[]='ds_lugartrabajo';
				$hide[]='dt_fechatrabajo';
				$hide[]='ds_resumentrabajo';
				$hide[]='ds_invitaciongrupo';
				$hide[]='ds_invitaciongrupo';
				$hide[]='ds_aval';
				$hide[]='ds_generalB';
				$hide[]='ds_especificoB';
				$hide[]='ds_actividadesB';
				$hide[]='ds_cronogramaB';
				$hide[]='ds_aportesB';
				$show[]='ds_convenio';
				$show[]='ds_cvprofesor';
				$show[]='ds_profesor';
				$show[]='ds_lugarprofesor';
				$show[]='ds_objetivoC';
				$show[]='ds_planC';
				$show[]='ds_aportesC';
				$show[]='ds_actividadesC';
				break;
			}
			
	    	
			
			
			
			
			
			$result['hide'] = $hide;
			$result['show'] = $show;
			
		}catch(Exception $ex){
			
			$result['error'] = $ex->getMessage();
			
		}

		echo json_encode( $result ); 
		return null;
	}
	
	
	
}