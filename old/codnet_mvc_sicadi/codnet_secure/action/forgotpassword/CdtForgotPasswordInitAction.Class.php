<?php

/**
 * Acción para inicializar el contexto para
 * solicitar una nueva clave.
 *
 * @author bernardo
 * @since 12-09-2011
 *
 */
class CdtForgotPasswordInitAction extends CdtOutputAction{

	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getOutputContent();
	 */
	protected function getOutputContent(){
			
		$xtpl = $this->getXTemplate ();

		$xtpl->assign ( 'WEB_PATH', WEB_PATH );

		if( CdtUtils::hasRequestError() ){
			
			$error = CdtUtils::getRequestError();
			$msg  = $error['msg'];
			$code = $error['code'];
			
			$xtpl->assign('msg', urldecode( $msg ) ) ;
			$xtpl->parse ( 'main.msg_error' );
			
		}
		$_SESSION[APP_NAME]['csrf_token'] = bin2hex(random_bytes(32));
		$xtpl->assign( "csrf_token", (isset($_SESSION[APP_NAME]['csrf_token']))?$_SESSION [APP_NAME]["csrf_token"]:'' );
		$xtpl->assign ( 'titulo', CDT_SECURE_LOGIN_TITLE );
		$xtpl->assign ( 'subtitulo', CDT_SECURE_LOGIN_SUBTITLE );
				
		$xtpl->assign ( 'title', $this->getOutputTitle() );
		$xtpl->assign ( 'txt_forgot_password', CDT_SECURE_MSG_FORGOT_PASSWORD );
		$xtpl->assign ( 'txt_email', CDT_SECURE_LBL_FORGOT_PASSWORD_EMAIL);
		$xtpl->assign ( 'txt_fill_email', CDT_SECURE_MSG_FORGOT_PASSWORD_FILL_EMAIL);
		$xtpl->assign ( 'btn_reset_password', CDT_SECURE_BTN_FORGOT_PASSWORD_RESETEAR);
		$xtpl->assign ( 'txt_required_fields', CDT_SECURE_MSG_REQUIRED_FIELDS);

		$xtpl->assign('forgot_password_action', CDT_SECURE_FORGOT_PASSWORD_ACTION);
		
		$xtpl->parse ( 'main' );
		return $xtpl->text ( 'main' );
	}


	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getXTemplate();
	 */
	public function getXTemplate(){
		return new XTemplate ( CDT_SECURE_TEMPLATE_FORGOT_PASSWORD );
	}
	
	
	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getOutputTitle();
	 */
	protected function getOutputTitle(){
		return CDT_SECURE_MSG_FORGOT_PASSWORD_TITLE;
	}	

	protected function getLayout(){
		$oClass = new ReflectionClass( CDT_SECURE_FORGOT_PASSWORD_LAYOUT );
		$oLayout = $oClass->newInstance();		
		return $oLayout;
	}	
	
}