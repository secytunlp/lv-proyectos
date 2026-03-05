<?php 

/**
 * Acción para mostrar que la registración fur realizada exitosamente.
 * 
 * @author bernardo
 * @since 13-07-2011
 * 
 */
class CdtSignupSuccessAction extends CdtOutputAction{

	
	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getOutputContent();
	 */
	protected function getOutputContent(){
		
		$xtpl = new XTemplate ( CDT_SECURE_TEMPLATE_REGISTRATION_SIGNUP_SUCCESS );
		
		$xtpl->assign ( 'WEB_PATH', WEB_PATH );
		$xtpl->assign ( 'title', $this->getOutputTitle() );
		$xtpl->assign ( 'message', CDT_SECURE_MSG_REGISTRATION_SIGNUP_SUCCESS );
		$xtpl->assign('btn_ingresar', CDT_SECURE_MSG_BTN_LOGIN_WEB);		
		$xtpl->parse ( 'main' );
		
		return $xtpl->text ( 'main' );	
	}
	
		
	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getOutputTitle();
	 */
	protected function getOutputTitle(){
		return CDT_SECURE_MSG_REGISTRATION_SIGNUP_TITLE;
	}
	

	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getLayout();
	 */
	protected function getLayout(){
		$oClass = new ReflectionClass( CDT_SECURE_REGISTRATION_LAYOUT );
		$oLayout = $oClass->newInstance();
		
		return $oLayout;
	}

}