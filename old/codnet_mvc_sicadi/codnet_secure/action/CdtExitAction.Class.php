<?php 

/**
 * Acción para desloguearse del sistema.
 * 
 * @author bernardo
 * @since 16-03-2010
 * 
 */
class CdtExitAction extends CdtAction{

	/**
	 * (non-PHPdoc)
	 * @see CdtAction::execute();
	 */
	public function execute(){
		
		$oUser = new CdtUser ( );
		
		CdtSecureUtils::logout();

		$forward='exit_success';
		return $forward;
	}
	
}