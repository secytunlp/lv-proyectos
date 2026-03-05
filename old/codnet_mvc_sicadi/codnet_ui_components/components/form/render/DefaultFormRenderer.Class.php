<?php

/**
 * Implementación default para renderizar un formulario 
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 26-02-2013
 *
 */
class DefaultFormRenderer implements IFormRenderer {

	/**
	 * formulario a renderizar.
	 * @var CMPForm
	 */
	private $oForm;
	
	private $templateName;
	
	public function render(CMPForm $oForm) {

        $xtpl = $this->getXTemplate();

        $xtpl->assign( "form_id", $oForm->getId() );
		$xtpl->assign( "csrf_token", (isset($_SESSION[APP_NAME]['csrf_token']))?$_SESSION [APP_NAME]["csrf_token"]:'' );
        
        $this->renderProperties( $oForm, $xtpl );
        
        $this->renderFieldset( $oForm, $xtpl );
        
        $this->renderHiddens( $oForm, $xtpl );
        
        $this->renderCustom( $oForm, $xtpl );
        
        $this->renderButtons( $oForm, $xtpl );
        
        $xtpl->parse('main');
        $content = $xtpl->text('main');
        return $content;
    }

    protected function getXTemplate() {
    	
    	$name = $this->getTemplateName();
    	if( empty($name) )
    		$name = CDT_CMP_TEMPLATE_FORM;
    	
    	return new XTemplate( $name );
    }

	protected function renderProperties(CMPForm $form, XTemplate $xtpl){
	
		foreach ($form->getProperties() as $name => $value) {
			
			$xtpl->assign("name", $name);
			$xtpl->assign("value", $value);			
			$xtpl->parse("main.property");
		} 
	}
	    	
	protected function renderHiddens(CMPForm $form, XTemplate $xtpl){

		foreach ($form->getHiddens() as $input) {
			
			$xtpl->assign("input_hidden", $input->show() );
			$xtpl->parse("main.field_hidden");
		} 
		
	}
	
	protected function renderFieldset(CMPForm $form, XTemplate $xtpl){

		foreach ($form->getFieldsets() as $fieldset) {
			
			//legend
			$legend = $fieldset->getLegend();
			if(!empty($legend)){
				$xtpl->assign("value", $legend);
				$xtpl->parse("main.fieldset.legend");
			}
			
			//fields
			/*
			foreach ($fieldset->getFields() as $formField) {
				$input = $formField->getInput();
				$label = $formField->getLabel();
				
				if( $input->getIsVisible() ){
					$this->renderLabel( $label, $input, $xtpl );
					$this->renderInput( $input, $xtpl );
				
					$xtpl->parse("main.fieldset.column.field");
				}
				
			}
			$xtpl->parse("main.fieldset.column");
			 */
			
			foreach ($fieldset->getFieldsColumns() as $column => $fields) {
				
				foreach ($fields as $formField) {
					
					$input = $formField->getInput();
					$label = $formField->getLabel();
					
					$this->renderLabel( $label, $input, $xtpl );
					$this->renderInput( $input, $xtpl );
					$xtpl->assign("minWidth", $formField->getMinWidth());
					
					if( $input->getIsVisible() ){
						$xtpl->assign("display", 'block');
						
					}
					else $xtpl->assign("display", 'none');
					
					$xtpl->parse("main.fieldset.column.field");
				}
				$xtpl->parse("main.fieldset.column");
			}
			
			
			$xtpl->parse("main.fieldset");
		} 
	}

	protected function renderLabel( $label, CMPFormInput $input, XTemplate $xtpl ){
		
		$xtpl->assign("value", $label );
		
		if( $input->getIsRequired() && $input->getIsEditable() ){
			$xtpl->assign("required", $input->getRequiredLabel() );
		}else{
			$xtpl->assign("required", "" );
		}
		
		$xtpl->assign("input_name", $input->getId() );
		$xtpl->parse("main.fieldset.column.field.label");
	}
	
	protected function renderInput( CMPFormInput $input, XTemplate $xtpl ){
		

        $xtpl->assign("input", $input->show() );
		
		$xtpl->parse("main.fieldset.column.field.input");
		
	}
	
	protected function renderCustom(CMPForm $form, XTemplate $xtpl){
		$xtpl->assign("customHTML", $form->getCustomHTML() );
		$xtpl->assign("intoFormCustomHTML", $form->getIntoFormCustomHTML() );
		
	}
		
	protected function renderButtons(CMPForm $form, XTemplate $xtpl){
	
		$cancel = $form->getCancelLabel();
		if($form->getIsEditable() && !empty($cancel)){
			$xtpl->assign("lbl_cancel", $cancel);
			$xtpl->assign("function", $form->getOnCancel() );
			$xtpl->parse("main.buttons.cancel");
		}
		
		if( $form->getUseAjaxCallback() ){
			$xtpl->assign("useAjaxCallback", 1 );
			$xtpl->assign("idAjaxCallback", $form->getIdAjaxCallback() );
				
		}else 
			$xtpl->assign("useAjaxCallback", 0 );
		
		$bs = ($form->getBeforeSubmit())?$form->getBeforeSubmit():"null";
		$xtpl->assign("beforeSubmit", $bs );
		$xtpl->assign("onCancel", $form->getOnCancel() );
		$xtpl->assign("onSuccessCallback", $form->getOnSuccessCallback() );
		$xtpl->assign("onSubmit", $form->getOnSubmit() );
		$xtpl->assign("action", $form->getAction() );
		$xtpl->assign("msg_required_fields", $form->getRequiredFieldsMessage() );
		
		$submit = $form->getSubmitLabel();
		if($form->getIsEditable() && !empty($submit)){
			if( $form->getUseAjaxSubmit() ){
				$xtpl->assign("lbl_button", $submit );
				$xtpl->parse("main.buttons.submit_ajax");
			}else{
				$xtpl->assign("lbl_submit", $submit );
				$xtpl->parse("main.buttons.submit");
			}
		}
	
		if( $form->getUseAjaxSubmit() ){
			$xtpl->assign("useAjaxSubmit", 1 );
		}else{
			$xtpl->assign("useAjaxSubmit", 0 );
		}
		
		$index = 0;
		foreach ($form->getButtons() as $label => $onclick) {
			
			$xtpl->assign("i", $index);
			$xtpl->assign("lbl_button", $label );
			$xtpl->assign("function", $onclick );
			$xtpl->parse("main.buttons.button");
			$index++;
		}
		
		if($form->getIsEditable())
			$xtpl->parse("main.buttons");
		
	}    	
	
	public function getForm()
	{
	    return $this->oForm;
	}

	public function setForm($oForm)
	{
	    $this->oForm = $oForm;
	}

	public function getTemplateName()
	{
	    return $this->templateName;
	}

	public function setTemplateName($templateName)
	{
	    $this->templateName = $templateName;
	}
}