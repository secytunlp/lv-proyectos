<?php

/**
 * Implementación para renderizar un select 
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 26-02-2013
 *
 */
class SelectRenderer extends FormInputRenderer {

	private $emptyOptionLabel;
		
    protected function getXTemplate() {
    	return new XTemplate(CDT_CMP_TEMPLATE_FORM_INPUT_SELECT);
    }

    protected function renderCustom(CMPFormInput $input, XTemplate $xtpl){
    	
    	//renderizamos las opciones del select.
    	if(!$input->getIsEditable())
    		$xtpl->assign("custom", " disabled=\"disabled\" ");
    		
    	$emptyLabel = $this->getEmptyOptionLabel();
    	if( !empty( $emptyLabel ) ){
    		$xtpl->assign( "empty_label", $emptyLabel );
    		$xtpl->parse( "main.option_empty" );
    	}
    	
    	$selected = $input->getSelectedValue();
    	foreach ($input->getOptions() as $value => $label) {
    		
    		$xtpl->assign( "label", $label );
    		$xtpl->assign( "value", $value );
    		$xtpl->assign( "selected", CdtFormatUtils::selected( $value, $selected) );
    		$xtpl->parse( "main.option" );
    	}
    }
	   	

	public function getEmptyOptionLabel()
	{
	    return $this->emptyOptionLabel;
	}

	public function setEmptyOptionLabel($emptyOptionLabel)
	{
	    $this->emptyOptionLabel = $emptyOptionLabel;
	}
	
	protected function renderType(CMPFormInput $input, XTemplate $xtpl){
    	$xtpl->assign( "type", "" );
    }

	
}