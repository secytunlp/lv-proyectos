<?php

/**
 * Implementación para renderizar un input findentity
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 30/05/2013
 */
class InputFindEntityRenderer implements IFormInputRenderer {
	
	public function render( CMPFormInput $input ){
		
		$xtpl = $this->getXTemplate();
		
		$xtpl->assign("inputId", $input->getId() );   

		$xtpl->assign("parent", $input->getParent() );  
		
		//renderizar el input del code
        $this->renderInputCode( $input, $xtpl );

        //renderizar el input del autocomplete
        $this->renderInputAutocomplete( $input, $xtpl );
        
        //para bucar por popup.
        if( $input->getHasPopup() && $input->getIsEditable() ){
        	//$xtpl->assign("gridModel", $input->getGridModelClazz() );
        	$xtpl->assign("gridClazz", $input->getGridClazz() );
        	$xtpl->parse('main.popup');
        }
        $xtpl->assign("widthPopup", $input->getWidthPopup() );
        $xtpl->assign("heightPopup", $input->getHeightPopup() );

        $xtpl->assign("minWidth", $input->getMinWidth() );
        
        //para agregar una nueva entity.
        if( $input->getHasAddEntity()  && $input->getIsEditable()){
        	$xtpl->assign("addentity_action", $input->getAddEntityAction() );
        	$xtpl->parse('main.addentity');	
        }

        $xtpl->assign("widthAddEntityPopup", $input->getWidthAddEntityPopup() );
        $xtpl->assign("heightAddEntityPopup", $input->getHeightAddEntityPopup() );
        	
        $this->renderCustom( $input, $xtpl );
        
        
		$xtpl->parse('main');
        
		$content = $xtpl->text('main');
        
        return $content;
	}    
	
    protected function getXTemplate() {
    	return new XTemplate(CDT_CMP_TEMPLATE_FORM_FINDENTITY);
    }
    

    protected function renderInputCode(CMPFormInput $input, XTemplate $xtpl ){

    	//clase para buscar la entity.
    	$xtpl->assign("finder", $input->getFinderClazz());    	
    	
    	//function callback
    	$xtpl->assign("functionCallback", $input->getFunctionCallback());
    	
    	//atributos a retornar por callback    	
    	$xtpl->assign("attributes", $input->getItemAttributesCallback());
    	
    	$xtpl->assign("inputCodeId", $input->getInputCode()->getId() );
    	$xtpl->assign("input_code", $input->getInputCode()->show() );
    }
    
	protected function renderInputAutocomplete(CMPFormInput $input, XTemplate $xtpl ){
    	
		$xtpl->assign("itemCode", $input->getInputAutocomplete()->getAutocomplete()->getItemCode() );
		
		$xtpl->assign("inputLabelId", $input->getInputAutocomplete()->getId() );
		$xtpl->assign("input_autocomplete", $input->getInputAutocomplete()->show() );
		
		
		
    }
    
    protected function renderCustom( $input, $xtpl ){
    }
    
}