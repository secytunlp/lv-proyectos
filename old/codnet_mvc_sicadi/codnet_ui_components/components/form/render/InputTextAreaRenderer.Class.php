<?php

/**
 * Implementación para renderizar un textarea
 * 
 * @author bernardo
 * @since 04/06/2013
 */
class InputTextAreaRenderer extends InputTextRenderer {

	
    protected function getXTemplate() {
    	return new XTemplate(CDT_CMP_TEMPLATE_FORM_INPUT_TEXTAREA);
    }

    protected function renderCustom(CMPFormInput $input, XTemplate $xtpl){
    	
    	if(!$input->getIsEditable())
    		$xtpl->assign("custom", " readonly=\"readonly\" ");
    		
    	$xtpl->assign("inputValue", $input->getInputValue());	
    	$xtpl->assign("rows", $input->getRows());
    	$xtpl->assign("cols", $input->getCols());
    }

     	
}