<?php

/**
 * Se traen las subareas
 * 
 * @author Marcos
 * @since 27-06-2023
 *
 */
class AddSolicitudAreaSubareaAction extends CdtAction{


	/**
	 * (non-PHPdoc)
	 * @see CdtAction::execute();
	 */
	public function execute(){

		
		$result = "";
		
		try{
			
			$cd_area = CdtUtils::getParam("cd_area");



			
			$result = CYTUtils::getSubareasItems($cd_area);
			
			
		}catch(Exception $ex){
			
			$result['error'] = $ex->getMessage();
			
		}
		echo json_encode( $result ); 
		return null;
	}
	
	
	
}