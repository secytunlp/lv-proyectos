<?php 

/**
 * Acción para redireccionar a la página de
 * acceso denegado.
 * 
 * @author bernardo
 * @since 16-03-2010
 * 
 */
class CdtAccessDeniedAction extends CdtOutputAction{

	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getOutputContent();
	 */
	protected function getOutputContent(){
		
		$xtpl = $this->getXTemplate ();
		$xtpl->assign ( 'WEB_PATH', WEB_PATH );
		$xtpl->parse ( 'main' );
		return $xtpl->text ( 'main' );
	}
		
	public function getOutputTitle(){
		return '';
	}

	public function getXTemplate(){
		return new XTemplate ( CDT_SECURE_PATH . 'view/templates/access_denied.html' );		
	}
	
}