<?php 

/**
 * TODO
 * Acción para loguearse en la web pública.
 * 
 * @author bernardo
 * @since 12-09-2011
 * 
 */
class LoginWebAction extends Action{

	/**
	 * se loguea el usuario.
	 * @return forward.
	 */
	public function execute(){
		
		$nomusuario = FormatUtils::getParamPOST('ds_username');
		$password = FormatUtils::getParamPOST('ds_password');
		
		//tomamos del get o del post.
		$backTo = FormatUtils::getParam('backTo', FormatUtils::getParamPOST('backTo','') );
		
		
		try{
			$manager = new UsuarioManager();
			$manager->login($nomusuario,$password);
			
			if(!empty($backTo)){
				$forward = null;
				DbManager::close();
				header("Location:". $backTo);
				exit();
			}			
			
			$forward = 'login_web_success';
			
		}catch(GenericException $ex){
			DbManager::undo();
			$_GET['backTo'] = $backTo;
			$forward = $this->doForwardException( $ex, 'login_web_error' );			
		}
		
		
		return $forward;
	}
	
}