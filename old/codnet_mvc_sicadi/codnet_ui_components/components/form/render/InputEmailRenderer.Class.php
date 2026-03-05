<?php

/**
 * Implementación para renderizar un input text para emails
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 26-02-2013
 *
 */
class InputEmailRenderer extends InputTextRenderer {

    protected function getXTemplate() {
    	return new XTemplate(CDT_CMP_TEMPLATE_FORM_INPUT_EMAIL);
    }
	
	protected function enhanceInput(CMPFormInput $input){
		$input->addProperty("placeholder", "example@mail.com");
	}
}