<?php

/**
 * Implementación para renderizar un input para solo lectura. 
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 04-03-2013
 *
 */
class InputReadOnlyRenderer extends FormInputRenderer {

	
    protected function getXTemplate() {
    	return new XTemplate(CDT_CMP_TEMPLATE_FORM_INPUT_READ_ONLY);
    }

    protected function renderCustom(CMPFormInput $input, XTemplate $xtpl){
    	
    	$value = $input->getProperty("value");
    	if(!empty($value))
    		$xtpl->assign("value", $value);
    	else	
    		$xtpl->assign("value", "&nbsp;");
    		
    }
    
    	
}