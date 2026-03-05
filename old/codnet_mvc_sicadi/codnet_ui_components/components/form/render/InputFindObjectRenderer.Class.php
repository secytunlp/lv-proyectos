<?php

/**
 * Implementación para renderizar un input text para el findobject
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 12-03-2013
 *
 */
class InputFindObjectRenderer implements IFormInputRenderer {
	
	public function render( CMPFormInput $oFormInput ){
		
		if(!$oFormInput->getIsEditable())
			
			$oFormInput->getFindObject()->setDisabled(true);
		
		return $oFormInput->getFindObject()->show();
	}    
    
    	
}