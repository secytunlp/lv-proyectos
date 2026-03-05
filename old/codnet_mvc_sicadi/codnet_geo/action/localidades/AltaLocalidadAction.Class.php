<?php 

/**
 * Acción para dar de alta una localidad.
 * 
 * @author bernardo
 * @since 18-03-2010
 * 
 */
class AltaLocalidadAction extends EditarLocalidadAction{

	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/action/generic/EditarAction#editar($oEntidad)
	 */
	protected function editar($oEntidad){
		$manager = new LocalidadManager();
		$manager->agregarLocalidad($oEntidad);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/action/generic/EditarAction#getForwardSuccess()
	 */
	protected function getForwardSuccess(){
		return 'alta_localidad_success';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/action/generic/EditarAction#getForwardError()
	 */
	protected function getForwardError(){
		return 'alta_localidad_error';
	}


}