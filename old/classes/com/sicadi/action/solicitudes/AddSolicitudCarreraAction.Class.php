<?php

/**
 * Se trae lal carreras
 * 
 * @author Marcos
 * @since 16-06-2023
 *
 */
class AddSolicitudCarreraAction extends CdtAction{


	/**
	 * (non-PHPdoc)
	 * @see CdtAction::execute();
	 */
	public function execute(){

		
		$result = "";
		
		try{
			
			$cd_organismo = CdtUtils::getParam("cd_organismo");
			
			
			$result = Carrera::getItems($cd_organismo);
			
			
		}catch(Exception $ex){
			
			$result['error'] = $ex->getMessage();
			
		}

		echo json_encode( $result ); 
		return null;
	}
	
	
	
}