<?php 

/**
 * Acción para mostrar que la nueva clave solicitada fue enviada exitosamente.
 * 
 * @author bernardo
 * @since 12-09-2011
 * 
 */
class CdtForgotPasswordSuccessAction extends CdtOutputAction{

	
	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getOutputContent();
	 */
	protected function getOutputContent(){
		
		$xtpl = new XTemplate ( CDT_SECURE_TEMPLATE_FORGOT_PASSWORD_SUCCESS );
		
		$xtpl->assign ( 'WEB_PATH', WEB_PATH );
		$xtpl->assign ( 'title', $this->getOutputTitle() );
		
		$xtpl->assign ( 'message', CDT_SECURE_MSG_FORGOT_PASSWORD_NEW_PASSWORD_SENT );
		$xtpl->assign ( 'titulo', CDT_SECURE_LOGIN_TITLE );
		$xtpl->assign ( 'subtitulo', CDT_SECURE_LOGIN_SUBTITLE );
		$xtpl->assign('btn_ingresar', CDT_SECURE_MSG_BTN_LOGIN_WEB);
		
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
	 * @see CdtOutputAction::getOutputContent();
	 */
	protected function getOutputTitle(){
		return CDT_SECURE_MSG_FORGOT_PASSWORD_TITLE;
	}	

	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getLayout();
	 */
	protected function getLayout(){
		$oClass = new ReflectionClass( CDT_SECURE_FORGOT_PASSWORD_LAYOUT );
		$oLayout = $oClass->newInstance();		
		return $oLayout;
	}	
}