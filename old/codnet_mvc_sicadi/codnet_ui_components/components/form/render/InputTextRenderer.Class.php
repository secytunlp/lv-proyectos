<?php

/**
 * Implementación para renderizar un input text 
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 26-02-2013
 *
 */
class InputTextRenderer extends FormInputRenderer {

	
    protected function getXTemplate() {
    	return new XTemplate(CDT_CMP_TEMPLATE_FORM_INPUT_TEXT);
    }

    protected function renderCustom(CMPFormInput $input, XTemplate $xtpl){
    	
    	if(!$input->getIsEditable())
    		$xtpl->assign("custom", " readonly=\"readonly\" ");
    		//$xtpl->assign("custom", " disabled ");
    	
    }

     	
}