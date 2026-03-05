<?php

/**
 * Acción para eliminar una entity.
 * 
 * @author Bernardo
 * @since 05-03-2013
 * 
 */
abstract class DeleteEntityAction extends CdtEditAsyncAction {

	protected abstract function getEntityManager();

	/**
     * (non-PHPdoc)
     * @see CdtEditAsyncAction::getEntity();
     */
    protected function getEntity() {

        //se obtiene el id de la entidad a eliminar.
        return CdtUtils::getParam('id');
    }

    /**
     * (non-PHPdoc)
     * @see CdtEditAsyncAction::edit();
     */
    protected function edit($entity) {
        $id = CdtUtils::getParam('id');
        
        $this->getEntityManager()->delete($id);
    }

}
