<?php 

/**
 * Acción que visualiza los términos y condiciones para
 * la registración de usuarios.
  * 
 * @author bernardo
 * @since 12-09-2011
 * 
 */
class CdtTermsAndCondictonsAction extends CdtOutputAction{

	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::execute();
	 */
	protected function getOutputTitle(){
		return CDT_SECURE_MSG_TERMS_CONDITIONS_TITLE;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getOutputContent();
	 */
	protected function getOutputContent(){
			
		$xtpl = $this->getXTemplate();
		
		$xtpl->assign ( 'WEB_PATH', WEB_PATH );
		
		$xtpl->assign ( 'title', $this->getOutputTitle() );
		
		$xtpl->parse ( 'main' );
		
		return $xtpl->text ( 'main' );		
		
	}
	

	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getLayout();
	 */
	protected function getLayout(){
		$oClass = new ReflectionClass( CDT_SECURE_MSG_TERMS_CONDITIONS_LAYOUT );
		$oLayout = $oClass->newInstance();
		
		return $oLayout;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getXTemplate();
	 */
	public function getXTemplate(){
		return new XTemplate ( CDT_SECURE_TEMPLATE_MSG_TERMS_CONDITIONS );		
	}
	
}