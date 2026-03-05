<?php

/**
 * AcciÃ³n para editar una entity
 * 
 * @author Bernardo
 * @since 05-03-2013
 * 
 */
abstract class EditEntityAction extends CdtEditAsyncAction {

	private $form;
	
	public function __construct(){
		$this->form = $this->getNewFormInstance();
	}
	
	public abstract function getNewFormInstance();
	
	public abstract function getNewEntityInstance();
	
	protected abstract function getEntityManager();
	
	
    /**
     * (non-PHPdoc)
     * @see CdtEditAction::getEntity();
     */
    protected function getEntity() {
        // Agrega la validación del token CSRF aquí
        if (CdtSecureUtils::isCSRFTokenValid()) {
            $entity = $this->getNewEntityInstance();
            $this->form->fillEntityValues($entity);
            return $entity;
        } else {
			throw new GenericException(CDT_SECURE_MSG_INVALID_CSRF);
        }
    }

    

}
