<?php 

/**
 * Acción para modificar un cliente.
 * 
 * @author bernardo
 * @since 18-03-2010
 * 
 */
class ModificarProvinciaAction extends EditarProvinciaAction{
	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/action/generic/EditarAction#editar($oEntidad)
	 */
	protected function editar($oEntidad){
		$manager = new ProvinciaManager();
		$manager->modificarProvincia($oEntidad);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/action/generic/EditarAction#getForwardSuccess()
	 */
	protected function getForwardSuccess(){
		return 'modificar_provincia_success';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/action/generic/EditarAction#getForwardError()
	 */
	protected function getForwardError(){
		return 'modificar_provincia_error';
	}
		
}