<?php

/**
 * Implementación para renderizar un input radio 
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 26-02-2013
 *
 */
class InputRadioRenderer extends InputCheckboxRenderer {

	protected function renderType(CMPFormInput $input, XTemplate $xtpl){
    	$xtpl->assign( "type", "radio" );
    }
}