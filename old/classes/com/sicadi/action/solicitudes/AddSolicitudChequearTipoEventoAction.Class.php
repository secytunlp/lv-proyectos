<?php

/**
 * Se chequea el tipo de evento
 * 
 * @author Marcos
 * @since 27-12-2013
 *
 */
class AddSolicitudChequearTipoEventoAction extends CdtAction{


	/**
	 * (non-PHPdoc)
	 * @see CdtAction::execute();
	 */
	public function execute(){

		
		$result = "";
		
		try{
			
			$bl_congreso = CdtUtils::getParam("bl_congreso");
			
			//$valido = true; //TODO validar
			$labels = array();
			$widths = array();
			
			
			if ($bl_congreso==CYT_CD_CONGRESO) {
				$labels['label_ds_trabajo']=CYT_LBL_SOLICITUD_A_COPIA_TRABAJO.':';
	    		$labels['label_ds_aceptacion']=CYT_LBL_SOLICITUD_A_ACEPTACION.':';
	    		$labels['label_ds_titulotrabajo']=CYT_LBL_SOLICITUD_A_TITULO_CONGRESO.'*:';
	    		$labels['label_ds_autorestrabajo']=CYT_LBL_SOLICITUD_A_AUTOR_CONGRESO.'*:';
	    		$labels['label_ds_congreso']=CYT_LBL_SOLICITUD_A_CONGRESO_CONGRESO.'*:';
	    		$labels['label_ds_resumentrabajo']=CYT_LBL_SOLICITUD_A_RESUMEN_CONGRESO.'*:';
	    		
	    		
	    	}
	    	if ($bl_congreso==CYT_CD_CONFERENCIA) {
	    		$labels['label_ds_trabajo']=CYT_LBL_SOLICITUD_A_COPIA_CONFERENCIA.':';
	    		$widths['label_ds_trabajo']=210;
	    		$labels['label_ds_aceptacion']=CYT_LBL_SOLICITUD_A_INVITACION.':';
	    		$labels['label_ds_titulotrabajo']=CYT_LBL_SOLICITUD_A_TITULO_CONFERENCIA.'*:';
	    		$labels['label_ds_autorestrabajo']=CYT_LBL_SOLICITUD_A_AUTOR_CONFERENCIA.'*:';
	    		$labels['label_ds_congreso']=CYT_LBL_SOLICITUD_A_CONGRESO_CONFERENCIA.'*:';
	    		$widths['label_ds_congreso']=300;
	    		$labels['label_ds_resumentrabajo']=CYT_LBL_SOLICITUD_A_RESUMEN_CONFERENCIA.'*:';
	    	}
			
	    	
			
			
			
			
			
			$result['labels'] = $labels;
			$result['widths'] = $widths;
			
			
		}catch(Exception $ex){
			
			$result['error'] = $ex->getMessage();
			
		}

		echo json_encode( $result ); 
		return null;
	}
	
	
	
}