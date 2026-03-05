<?php

/**
 * Acción para actualizar una entity
 * 
 * @author Bernardo
 * @since 07-03-2013
 * 
 */
abstract class UpdateEntityAction extends EditEntityAction {

	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::edit();
	 */
	protected function edit( $entity ){
		$this->getEntityManager()->update( $entity );
	}
	
}
