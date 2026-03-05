<?php 

/**
 * Acción para dar de alta una provincia.
 * 
 * @author bernardo
 * @since 18-03-2010
 * 
 */
class AltaProvinciaAction extends EditarProvinciaAction{

	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/action/generic/EditarAction#editar($oEntidad)
	 */
	protected function editar($oEntidad){
		$manager = new ProvinciaManager();
		$manager->agregarProvincia($oEntidad);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/action/generic/EditarAction#getForwardSuccess()
	 */
	protected function getForwardSuccess(){
		return 'alta_provincia_success';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/action/generic/EditarAction#getForwardError()
	 */
	protected function getForwardError(){
		return 'alta_provincia_error';
	}


}