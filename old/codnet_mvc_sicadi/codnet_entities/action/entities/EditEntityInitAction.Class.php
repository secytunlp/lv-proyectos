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
//use CdtGestion\model\impl\Entity;

abstract class EditEntityInitAction extends CdtEditInitAction {

	private $form;
	
	
	public function __construct(){
		$this->form = $this->getNewFormInstance($this->getSubmitAction());
	}
	
	public abstract function getNewFormInstance($action);
	
	public abstract function getNewEntityInstance();
	
	
	/**
     * retorna el action para el submit.
     * @return string
     */
    protected abstract function getSubmitAction();
	
	
    /**
     * (non-PHPdoc)
     * @see CdtEditInitAction::getXTemplate();
     */
    protected function getXTemplate() {
        return new XTemplate(CDT_ENTITIES_TEMPLATE_ENTITY_EDIT);
    }

	/**
     * (non-PHPdoc)
     * @see CdtEditInitAction::getEntity();
     */
    protected function getEntity() {

        //se construye la entity a modificar.
        $entity = $this->getNewEntityInstance();
        
        //$this->getForm()->fillEntityValues($entity);
        
        return $entity;
    }

    /**
     * (non-PHPdoc)
     * @see CdtEditInitAction::parseEntity();
     */
    protected function parseEntity($entity, XTemplate $xtpl) {

        $entity = CdtFormatUtils::ifEmpty($entity, $this->getNewEntityInstance());

        $this->getForm()->fillInputValues($entity);
        
        $xtpl->assign('formulario', $this->getForm()->show() );
        
    }

	public function getForm()
	{
	    return $this->form;
	}

	public function setForm($form)
	{
	    $this->form = $form;
	}
}
