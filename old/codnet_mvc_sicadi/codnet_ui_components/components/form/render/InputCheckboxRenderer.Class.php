<?php

/**
 * Implementación para renderizar un input checkbox 
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 26-02-2013
 *
 */
class InputCheckboxRenderer extends InputTextRenderer {

	protected function renderCustom(CMPFormInput $input, XTemplate $xtpl){

		$custom = "";
		
		if( $input->getIsChecked() ){
			
			$custom .= " checked ";
		}
		
		if( !$input->getIsEditable() ){
				
			$custom .= " disabled ";
		}
		
		$xtpl->assign( "custom", $custom );
		
    }
}