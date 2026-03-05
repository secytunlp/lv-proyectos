<?php

/**
 * Acción para para editar una entidad.
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 21-04-2010
 *
 */
abstract class CdtEditAction extends CdtAction{


	/**
	 * (non-PHPdoc)
	 * @see CdtAction::execute();
	 */
	public function execute(){

		//se inicia una transacción.
		CdtDbManager::begin_tran();
		
		try{
			$oEntity = $this->getEntity();
			$this->edit( $oEntity );
			$forward = $this->getForwardSuccess();
			//commit de la transacción.
			CdtDbManager::save();
			
		}catch(GenericException $ex){
			
			//rollback de la transacción.
			CdtDbManager::undo();
			CdtUtils::setRequestError( $ex );
			$forward = $this->doForwardException( $ex, $this->getForwardError() );
			//$forward = $this->getForwardError();
		}

		return $forward;
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
	 * forward para el success de la edición.
	 * @return string
	 */
	protected abstract function getForwardSuccess();

	/**
	 * forward para cuando hay error.
	 * @return string
	 */
	protected abstract function getForwardError();


}