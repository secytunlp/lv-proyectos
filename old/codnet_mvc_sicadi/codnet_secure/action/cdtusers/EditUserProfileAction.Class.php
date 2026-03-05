<?php 

/**
 * Acción para modificar la cuenta del usuario..
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 06-01-2012
 *  
 */
class EditUserProfileAction extends EditCdtUserAction{


	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getEntity();
	 */
	protected function getEntity(){
		
		//se construye el CdtUser a modificar.
		$oCdtUser = parent::getEntity();
		
		 $ds_new_password = CdtUtils::getParamPOST('ds_new_password') ;	
				
					
		return array( "user" => $oCdtUser, "password" => $ds_new_password );
	}
		
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::edit();
	 */
	protected function edit($oEntity){
		$manager = new CdtUserManager();
		$manager->updateCdtUserProfile( $oEntity["user"], $oEntity["password"] );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getForwardSuccess();
	 */
	protected function getForwardSuccess(){
		return 'edit_cdtuserprofile_success';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getForwardError();
	 */
	protected function getForwardError(){
		return 'edit_cdtuserprofile_error';
	}

	
}
