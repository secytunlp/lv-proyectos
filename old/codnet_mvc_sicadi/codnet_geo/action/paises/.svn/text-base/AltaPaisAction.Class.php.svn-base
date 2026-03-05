<?php 

/**
 * Acción para dar de alta un pais.
 * 
 * @author bernardo
 * @since 18-03-2010
 * 
 */
class AltaPaisAction extends EditarPaisAction{

	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/action/generic/EditarAction#editar($oEntidad)
	 */
	protected function editar($oEntidad){
		$manager = new PaisManager();
		$manager->agregarPais($oEntidad);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/action/generic/EditarAction#getForwardSuccess()
	 */
	protected function getForwardSuccess(){
		return 'alta_pais_success';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/action/generic/EditarAction#getForwardError()
	 */
	protected function getForwardError(){
		return 'alta_pais_error';
	}

}