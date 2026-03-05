<?php 

/**
 * Acción para inicializar el contexto para modificar
 * una localidad.
 *  
 * @author bernardo
 * @since 18-03-2010
 * 
 */
class ModificarLocalidadInitAction extends EditarLocalidadInitAction{
	
	
	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/action/generic/SecureOutputAction#getTitulo()
	 */
	protected function getTitulo(){
		return "Modificar Localidad";
	}

	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/action/generic/EditarInitAction#getAccionSubmit()
	 */
	protected function getAccionSubmit(){
		return "modificar_localidad";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see clases/com/bulfon/bulfon/action/almacenes/EditarAlmacenInitAction#getEntidad()
	 */
	protected function getEntidad(){
		$oLocalidad = null;
		if (isset ( $_GET ['id'] )) {
			//recuperamos la obra dado su identifidor.
			$cd_localidad = FormatUtils::getParam('id');			
			
			$manager = new LocalidadManager();
			$oLocalidad = $manager->getLocalidadPorId( $cd_localidad );
		}
		
		return $oLocalidad;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/action/generic/EditarInitAction#getMostrarCodigo()
	 */
	protected function getMostrarCodigo(){
		return true;
	}	
}