<?php 

/**
 * Acción para eliminar una provincia.
 * 
 * @author bernardo
 * @since 18-03-2010
 * 
 */
class EliminarProvinciaAction extends Action{

	/**
	 * se elimina un cliente.
	 * @return boolean (true=exito).
	 */
	public function execute(){
		
		$cd_provincia = FormatUtils::getParam('id');
	
		//se elimina el cliente.
		$manager = new ProvinciaManager();

		//se inicia una transacción.
		DbManager::begin_tran();
		
		try{
			
			$manager->eliminarProvincia( $cd_provincia );
			$forward = 'eliminar_provincia_success';
			//commit de la transacción.
			DbManager::save();
			
		}catch(GenericException $ex){
			$forward = 'eliminar_provincia_error';
			$this->setDs_forward_params( 'er=1'.'&msg=' .$ex->getMessage() . '&code=' . $ex->getCode());
			//rollback de la transacción.
			DbManager::undo();
		}
		
		return $forward;
	}
	
}