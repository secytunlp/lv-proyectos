<?php

/**
 * Implementación para renderizar un input text para números 
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 26-02-2013
 *
 */
class InputNumberRenderer extends InputTextRenderer {
	
    protected function getXTemplate() {
    	return new XTemplate(CDT_CMP_TEMPLATE_FORM_INPUT_NUMBER);
    }
    	
}