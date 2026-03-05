<?php 

/**
 * Acción para modificar un cliente.
 * 
 * @author bernardo
 * @since 18-03-2010
 * 
 */
class ModificarLocalidadAction extends EditarLocalidadAction{
	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/action/generic/EditarAction#editar($oEntidad)
	 */
	protected function editar($oEntidad){
		$manager = new LocalidadManager();
		$manager->modificarLocalidad($oEntidad);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/action/generic/EditarAction#getForwardSuccess()
	 */
	protected function getForwardSuccess(){
		return 'modificar_localidad_success';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/action/generic/EditarAction#getForwardError()
	 */
	protected function getForwardError(){
		return 'modificar_localidad_error';
	}
		
}