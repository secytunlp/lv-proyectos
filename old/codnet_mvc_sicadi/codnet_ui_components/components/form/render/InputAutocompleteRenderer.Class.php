<?php

/**
 * Implementación para renderizar un input text para el autocomplete
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 24-05-2013
 *
 */
class InputAutocompleteRenderer implements IFormInputRenderer {
	
	public function render( CMPFormInput $oFormInput ){
		
		if(!$oFormInput->getIsEditable())
			
			$oFormInput->getAutocomplete()->setDisabled(true);
		
		return $oFormInput->getAutocomplete()->show();
	}    
    
    	
}