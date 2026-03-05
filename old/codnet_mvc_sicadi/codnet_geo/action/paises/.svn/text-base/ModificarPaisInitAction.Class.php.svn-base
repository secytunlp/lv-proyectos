<?php 

/**
 * Acción para inicializar el contexto para modificar
 * un pais.
 *  
 * @author bernardo
 * @since 18-03-2010
 * 
 */
class ModificarPaisInitAction extends EditarPaisInitAction{

	
	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/action/generic/SecureOutputAction#getTitulo()
	 */
	protected function getTitulo(){
		return "Modificar Pais";
	}

	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/action/generic/EditarInitAction#getAccionSubmit()
	 */
	protected function getAccionSubmit(){
		return "modificar_pais";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see clases/com/bulfon/bulfon/action/almacenes/EditarAlmacenInitAction#getEntidad()
	 */
	protected function getEntidad(){
		$oPais = null;
		if (isset ( $_GET ['id'] )) {
			//recuperamos la obra dado su identifidor.
			$cd_pais = FormatUtils::getParam('id');			
			
			$manager = new PaisManager();
			$oPais = $manager->getPaisPorId( $cd_pais );
		}
		
		return $oPais;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/action/generic/EditarInitAction#getMostrarCodigo()
	 */
	protected function getMostrarCodigo(){
		return true;
	}	
}