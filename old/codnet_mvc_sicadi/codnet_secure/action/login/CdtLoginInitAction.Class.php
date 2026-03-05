<?php 

/**
 * Acción para iniciarlizar el login en el sistema.
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 16-03-2010
 * 
 */
class CdtLoginInitAction extends CdtOutputAction{

	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getOutputContent();
	 */
	protected function getOutputContent(){

			
		$xtpl = $this->getXTemplate ();

		if( CdtUtils::hasRequestError() ){
			
			$error = CdtUtils::getRequestError();
			$msg  = $error['msg'];
			$code = $error['code'];
			
			$xtpl->assign('msg', urldecode( $msg ) ) ;
			$xtpl->parse ( 'main.msg_error' );
			
		}
		$xtpl->assign('username',  CdtUtils::getParamPOST('username') ) ;
		
		$backTo = CdtUtils::getParam('backTo', CdtUtils::getParamPOST('backTo','') );
		
		if(!empty($backTo)){
			$xtpl->assign('backTo', $backTo );
		}		
		
		$xtpl->assign ( 'login_titulo', CDT_SECURE_LOGIN_TITLE );
		$xtpl->assign ( 'login_subtitulo', CDT_SECURE_LOGIN_SUBTITLE );
		
		
		$xtpl->assign('lbl_username', CDT_SECURE_LBL_CDTUSER_DS_USERNAME );
        $xtpl->assign('lbl_password', CDT_SECURE_LBL_CDTUSER_DS_PASSWORD);
		$xtpl->assign('txt_campos_obligatorios', CDT_SECURE_MSG_REQUIRED_FIELDS);
		$xtpl->assign('btn_ingresar', CDT_SECURE_MSG_BTN_LOGIN_WEB);
		$xtpl->assign('txt_ingrese_username', CDT_SECURE_MSG_INGRESE_USERNAME);
		$xtpl->assign('txt_ingrese_password', CDT_SECURE_MSG_INGRESE_PASSWORD);
		$xtpl->assign('txt_recurepar_password', CDT_SECURE_MSG_RECUPERAR_PASSWORD);
		
		$xtpl->assign('login_action', CDT_SECURE_LOGIN_ACTION);
		$xtpl->assign('solicitar_clave_init_action', CDT_SECURE_FORGOT_PASSWORD_INIT_ACTION);		

		if( CDT_SECURE_REGISTRATION_ENABLED ){
			$xtpl->assign('link_registrarse', CDT_SECURE_MSG_LINK_REGISTRARSE);
			$xtpl->parse ( 'main.registration' );
		}
		
		$xtpl->assign ( 'title', $this->getOutputTitle() );
		$xtpl->parse ( 'main' );
		return $xtpl->text ( 'main' );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getOutputTitle();
	 */
	protected function getOutputTitle(){
		return 'Login';
	}

	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getLayout();
	 */
	protected function getLayout(){
		
		$oLayout = CdtReflectionUtils::newInstance( DEFAULT_LOGIN_LAYOUT );
		return $oLayout;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getXTemplate();
	 */
	public function getXTemplate(){
		return new XTemplate ( CDT_SECURE_TEMPLATE_LOGIN );		
	}
}