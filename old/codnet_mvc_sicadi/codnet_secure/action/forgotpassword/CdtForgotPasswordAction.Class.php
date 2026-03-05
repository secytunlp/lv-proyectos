<?php 

/**
 * Acción para solicitar una nueva clave.
 * 
 * @author bernardo
 * @since 12-09-2011
 * 
 */
class CdtForgotPasswordAction extends CdtAction{

	/**
	 * (non-PHPdoc)
	 * @see CdtAction::execute();
	 */
	public function execute(){
		
		$ds_email = CdtUtils::getParamPOST('ds_email');
		
		try{
			
			$manager = new CdtUserManager();
			$manager->sendNewPassword( $ds_email );
			
			$forward = 'forgot_password_success';
			
		}catch(GenericException $ex){
			
			CdtDbManager::undo();
			$forward = $this->doForwardException( $ex, 'forgot_password_error' );			
		}
		
		
		return $forward;
	}
	
}