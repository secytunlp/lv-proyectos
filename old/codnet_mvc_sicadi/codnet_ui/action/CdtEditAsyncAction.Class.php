<?php

/**
 * Acción genérica para para editar una entidad
 * por ajax y obtener el resultado en Json.
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 21-04-2010
 *
 */
abstract class CdtEditAsyncAction extends CdtAction{


	/**
	 * (non-PHPdoc)
	 * @see CdtAction::execute();
	 */
	public function execute(){

		
		//se inicia una transacción.
		CdtDbManager::begin_tran();
		
		$result = array();
		
		try{
			$oEntity = $this->getEntity();
			$result = $this->edit( $oEntity );
			
			$result['info'] = $this->getInfoMessage( $oEntity, $result );
			
			//commit de la transacci�n.
			CdtDbManager::save();
			
		}catch(Exception $ex){
			
			//rollback de la transacci�n.
			CdtDbManager::undo();
			$result['error'] = $this->getErrorMessage( $ex );
			
			//CdtUtils::setRequestError( $ex );
			//$forward = $this->getForwardError();
		}

		echo json_encode( $result ); 
		return null;
	}
	
	/**
	 * entidad a editar.
	 * @return object
	 */
	protected abstract function getEntity();

	/**
	 * se edita la entidad.
	 * @param object $oEntity entidad a editar
	 */
	protected abstract function edit($oEntity);

	/**
	 * construimos el array con el error.
	 * @return string
	 */
	protected function getErrorMessage( Exception $ex ){
		$message = '';
    	switch($ex->getCode())
		{
			case '1451':
				$message = CDT_SECURE_MSG_EXCEPTION_CANNOT_DELETE_ITEMS_RELATED;
				break;
			case '1062':
				$message = CDT_SECURE_MSG_EXCEPTION_DUPLICATE_DATA;
				break;
			default:
				$message=$ex->getMessage();
		}
        return $message;
	}

	protected function getInfoMessage( $entity, $result ){
		//CdtUtils::setRequestInfo(1, CDT_UI_SUBMIT_SUCCESS);
		return CDT_UI_SUBMIT_SUCCESS;	
	}
	
}