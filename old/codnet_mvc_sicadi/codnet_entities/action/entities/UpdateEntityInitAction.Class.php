<?php

/**
 * Acción para inicializar el contexto 
 * para editar una entity.
 * 
 * @author Bernardo
 * @since 05-03-2013
 * 
 */
//use CdtGestion\utils\GESComponentsFactory;
//use CdtGestion\model\impl\Cliente;

abstract class UpdateEntityInitAction extends EditEntityInitAction {

	
	protected abstract function getEntityManager();
	
    /**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::getEntity();
	 */
	protected function getEntity(){

		$entity = null;

		//recuperamos dado su identifidor.
		$oid = CdtUtils::getParam('id');
			
		if (!empty( $oid )) {
						
			$manager = $this->getEntityManager();
			$entity = $manager->getEntityById($oid);
		}else{
		
			$entity = parent::getEntity();
		
		}
		return $entity;
	}
	
}
