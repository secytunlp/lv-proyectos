<?php 

/**
 * Acci�n para editar CdtUser.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
abstract class EditCdtUserAction extends CdtEditAction{

	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getEntity();
	 */
	protected function getEntity(){
		
		//se construye el CdtUser a modificar.
		$oCdtUser = new CdtUser( );
		
		$oCdtUser->setCd_user ( CdtUtils::getParamPOST('cd_user') );	
				
		$oCdtUser->setDs_username ( CdtUtils::getParamPOST('ds_username') );	
				
		$oCdtUser->setDs_name ( CdtUtils::getParamPOST('ds_name') );	
				
		$oCdtUser->setDs_email ( CdtUtils::getParamPOST('ds_email') );	
				
		$oCdtUser->setDs_password ( CdtUtils::getParamPOST('ds_password') );	
				
		$oCdtUser->setCd_usergroup ( CdtUtils::getParamPOST('cd_usergroup') );	
				
		$oCdtUser->setDs_phone ( CdtUtils::getParamPOST('ds_phone') );	
				
		$oCdtUser->setDs_address ( CdtUtils::getParamPOST('ds_address') );	
		
		$ips = unserialize($_SESSION["cdtuser_ips"]);
        
        $oCdtUser->setDs_ips( implode(",", $ips));
        					
		return $oCdtUser;
	}
	
		
}
