<?php 

/**
 * Acci�n para loguearse en el sistema.
 * 
 * @author bernardo
 * @since 16-03-2010
 * 
 */
class CdtLoginAction extends CdtAction{

	/**
	 * (non-PHPdoc)
	 * @see CdtAction::execute();
	 */
	public function execute(){
		
		
		$username =  CdtUtils::getParamPOST('username') ;
		
		$password = CdtUtils::getParamPOST('password') ; 
		
		$ip = $_SERVER[ 'REMOTE_ADDR'] ; 
		
		
		try{

			$manager = new CdtUserManager();
			$oUser = $manager->getUserByUsernamePassword($username,$password, $ip);
			
			//lo dejamos en sesi�n.
			CdtSecureUtils::login( $oUser );
			
			$forward = $this->login( $oUser );
			
			//tomamos del get o del post.
			$backTo = CdtUtils::getParam('backTo', CdtUtils::getParamPOST('backTo','') );
		
			if(!empty($backTo)){
				$forward = null;
				CdtDbManager::close();
				header("Location:". $backTo);
				exit();
			}

			
			
		}catch(GenericException $ex){
			
			CdtDbManager::undo();
			$forward = $this->doForwardException( $ex, 'login_error');
		}
		
		return $forward;
	}
	
	protected function login($oUser){
	
		//lo dejamos en sesi�n.
		CdtSecureUtils::login( $oUser );
			
		return 'login_success';
	}
}