<?php 

/**
 * Acción para eliminar un pais.
 * 
 * @author bernardo
 * @since 18-03-2010
 * 
 */
class EliminarPaisAction extends Action{

	/**
	 * se elimina un cliente.
	 * @return boolean (true=exito).
	 */
	public function execute(){
		
		$cd_pais = FormatUtils::getParam('id');
	
		$manager = new PaisManager();

		//se inicia una transacción.
		DbManager::begin_tran();
		
		try{
			
			$manager->eliminarPais( $cd_pais );
			$forward = 'eliminar_pais_success';
			//commit de la transacción.
			DbManager::save();
			
		}catch(GenericException $ex){
			$forward = 'eliminar_pais_error';
			$this->setDs_forward_params( 'er=1'.'&msg=' .$ex->getMessage() . '&code=' . $ex->getCode());
			//rollback de la transacción.
			DbManager::undo();
		}
		
		return $forward;
	}

	
}