<?php

/**
 * Acción para visualizar una entity.
 *  
 * @author Bernardo
 * @since 05-03-2013
 * 
 */
abstract class ViewEntityAction extends UpdateEntityInitAction {

	
	/**
     * (non-PHPdoc)
     * @see CdtEditInitAction::getXTemplate();
     */
    protected function getXTemplate() {
        return new XTemplate(CDT_ENTITIES_TEMPLATE_ENTITY_VIEW);
    }
	
    
	protected function parseEntity($entity, XTemplate $xtpl) {

		$this->getForm()->setIsEditable( false );
		
        parent::parseEntity($entity, $xtpl);
        
        
    }
	
    /**
     * (non-PHPdoc)
     * @see CdtOutputAction::getLayout();
     */
    protected function getLayout() {
        $oLayout = new CdtLayoutBasicAjax();
        return $oLayout;
    }

}
