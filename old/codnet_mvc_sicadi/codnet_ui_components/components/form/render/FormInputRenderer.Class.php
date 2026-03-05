<?php

/**
 * Implementación para renderizar un input text 
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 26-02-2013
 *
 */
abstract class FormInputRenderer implements IFormInputRenderer {

	
	public function render(CMPFormInput $input) {

		$xtpl = $this->getXTemplate();
		
		$this->enhanceInput( $input, $xtpl );
		
        $this->renderProperties( $input, $xtpl );

        $this->renderRequired( $input, $xtpl );
        
        $this->renderNoRequired( $input, $xtpl );
        
        $this->renderCustom( $input, $xtpl );
        
        $this->renderType( $input, $xtpl );
        
        $this->renderFormat($input, $xtpl);
        
        $xtpl->assign("input_id", $input->getId() );        
    	
        $xtpl->parse('main');
        $content = $xtpl->text('main');
        return $content;
    }

    protected function renderFormat(CMPFormInput $input, XTemplate $xtpl){
    	$xtpl->assign("format", $input->getFormat() );
    }
    
    protected abstract function getXTemplate() ;
    
	protected function renderType(CMPFormInput $input, XTemplate $xtpl){
    	$xtpl->assign( "type", $input->getType() );
    }

	protected function enhanceInput(CMPFormInput $input){}

	protected function renderCustom(CMPFormInput $input, XTemplate $xtpl){}
    
    protected function renderRequired(CMPFormInput $input, XTemplate $xtpl){
    	
    	if( $input->getIsRequired() ){
        
        	$xtpl->assign("msg_required", $input->getRequiredMessage() );
        	$xtpl->assign("msg_invalid_format", $input->getInvalidFormatMessage() );
        	$xtpl->parse('main.required');
        	
        }
        
    }
    
	protected function renderNoRequired(CMPFormInput $input, XTemplate $xtpl){
    	
    	if( !$input->getIsRequired() ){
        	
        	$xtpl->assign("msg_invalid_format", $input->getInvalidFormatMessage() );
        	$xtpl->parse('main.no_required');
        }
    }
    
	protected function renderProperties(CMPFormInput $input, XTemplate $xtpl){
	
		foreach ($input->getProperties() as $name => $value) {
			
			//si la property es el value, hay que formatearlo.
			if( $name == "value"){
				$value = $this->formatValue( $input, $value );
			}
			
			$xtpl->assign("name", $name);
			$xtpl->assign("value", $value);			
			$xtpl->parse("main.property");
		} 
	}

	protected function formatValue( CMPFormInput $input, $value ){
		return $value;
	}
}