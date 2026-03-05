<?php 

/**
 * Acción para eliminar una localidad.
 * 
 * @author bernardo
 * @since 18-03-2010
 * 
 */
class EliminarLocalidadAction extends Action{

	/**
	 * se elimina un cliente.
	 * @return boolean (true=exito).
	 */
	public function execute(){
		
		$cd_localidad = FormatUtils::getParam('id');
	
		//se elimina el cliente.
		$manager = new LocalidadManager();

		//se inicia una transacción.
		DbManager::begin_tran();
		
		try{
			
			$manager->eliminarLocalidad( $cd_localidad );
			$forward = 'eliminar_localidad_success';
			//commit de la transacción.
			DbManager::save();
			
		}catch(GenericException $ex){
			$forward = 'eliminar_localidad_error';
			$this->setDs_forward_params( 'er=1'.'&msg=' .$ex->getMessage() . '&code=' . $ex->getCode());
			//rollback de la transacción.
			DbManager::undo();
		}
		
		return $forward;
	}

	
}