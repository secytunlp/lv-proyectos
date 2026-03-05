<?php

/**
 * Implementación para renderizar un input para solo lectura. 
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 04-03-2013
 *
 */
class SelectReadOnlyRenderer extends InputReadOnlyRenderer {

    protected function renderCustom(CMPFormInput $input, XTemplate $xtpl){
    	
    	$selected = $input->getSelectedValue();
    	if(!empty($selected)){
    		$selectedLabel = "";
	    	foreach ($input->getOptions() as $value => $label) {
	    		if( $selected == $value )
	    			$selectedLabel = $label; 	
	    	}
	    	
			$xtpl->assign("value", $selectedLabel);
    	}else	
    		$xtpl->assign("value", "&nbsp;");
    	
    }
    	
}