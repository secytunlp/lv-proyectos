<?php 

/**
 * Acción para redireccionar a la página de incio
 * del usuario logueado.
 * 
 * @author bernardo
 * @since 16-03-2010
 * 
 */
class CdtInitAction extends CdtOutputAction{

	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getOutputContent();
	 */
	protected function getOutputContent(){
		return "";
	}

	
	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getOutputTitle();
	 */
	public function getOutputTitle(){
		return NOMBRE_APLICACION;
	}
}