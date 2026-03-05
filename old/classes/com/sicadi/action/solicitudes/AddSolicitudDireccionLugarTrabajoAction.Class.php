<?php

/**
 * Se deveulve la direccion y el teléfono del lugar de trabajo
 * 
 * @author Marcos
 * @since 31-03-2014
 *
 */
class AddSolicitudDireccionLugarTrabajoAction extends CdtAction{


	/**
	 * (non-PHPdoc)
	 * @see CdtAction::execute();
	 */
	public function execute(){

		
		$result = "";
		
		try{
			
			$lugarTrabajo_oid = CdtUtils::getParam("lugarTrabajo_oid");
			
			$managerLugarTrabajo = CYTSecureManagerFactory::getLugarTrabajoManager();
			$oLugarTrabajo = $managerLugarTrabajo->getObjectByCode($lugarTrabajo_oid);
					
			$result['direccion'] = $oLugarTrabajo->getDs_direccion();
			$result['telefono'] = $oLugarTrabajo->getDs_telefono();
			
			
		}catch(Exception $ex){
			
			$result['error'] = $ex->getMessage();
			
		}

		echo json_encode( $result ); 
		return null;
	}
	
	
	
}