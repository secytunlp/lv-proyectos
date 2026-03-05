<?php 

/**
 * Acción para inicializar el contexto para modificar
 * una provincia.
 *  
 * @author bernardo
 * @since 18-03-2010
 * 
 */
class ModificarProvinciaInitAction extends EditarProvinciaInitAction{

	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/action/generic/SecureOutputAction#getTitulo()
	 */
	protected function getTitulo(){
		return "Modificar Provincia";
	}

	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/action/generic/EditarInitAction#getAccionSubmit()
	 */
	protected function getAccionSubmit(){
		return "modificar_provincia";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see clases/com/bulfon/bulfon/action/almacenes/EditarAlmacenInitAction#getEntidad()
	 */
	protected function getEntidad(){
		$oProvincia = null;
		if (isset ( $_GET ['id'] )) {
			//recuperamos la obra dado su identifidor.
			$cd_provincia = FormatUtils::getParam('id');			
			
			$manager = new ProvinciaManager();
			$oProvincia = $manager->getProvinciaPorId( $cd_provincia );
		}
		
		return $oProvincia;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/action/generic/EditarInitAction#getMostrarCodigo()
	 */
	protected function getMostrarCodigo(){
		return true;
	}	
}