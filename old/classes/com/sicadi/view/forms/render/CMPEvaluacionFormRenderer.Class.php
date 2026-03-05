<?php

/**
 * Implementación para renderizar un formulario de evaluación 
 *
 * @author Marcos
 * @since 21-05-2014
 *
 */
class CMPEvaluacionFormRenderer extends DefaultFormRenderer {

	 protected function getXTemplate() {
    	return new XTemplate( CYT_TEMPLATE_EVALUACION_FORM );
    }
	
	
	protected function renderFieldset(CMPForm $form, XTemplate $xtpl){
		$xtpl->assign("p_max_item", CYT_MSG_EVALUACION_SEPARADOR_NEGRO_1_1);
		$xtpl->assign("detalle_puntaje", CYT_MSG_EVALUACION_SEPARADOR_NEGRO_1_2);
		$xtpl->assign("puntaje_otorgado", CYT_MSG_EVALUACION_SEPARADOR_NEGRO_1_3);
		$xtpl->assign("hasta", CYT_LBL_EVALUACION_HASTA);
		$xtpl->assign("puntos", CYT_LBL_EVALUACION_PUNTOS);
		$xtpl->assign("total", CYT_LBL_PRESUPUESTO_TOTAL);
		$xtpl->assign("subtotal", CYT_LBL_PRESUPUESTO_SUBTOTAL);
		$xtpl->assign("max_excedido", CYT_LBL_EVALUACION_MAX_EXCEDIDO);
		
		$xtpl->assign("c_u", CYT_LBL_EVALUACION_C_U);
		$xtpl->assign("c_10", CYT_LBL_EVALUACION_C_10);
		$xtpl->assign("pt", CYT_LBL_EVALUACION_PT);
		$xtpl->assign("decimales", CYT_DECIMALES);
		$xtpl->assign("falta_puntaje", CYT_LBL_EVALUACION_FALTA_PUNTAJE);
		$xtpl->assign("puntaje_excedido", CYT_LBL_EVALUACION_PUNTAJE_EXCEDIDO);
		
		
		foreach ($form->getFieldsets() as $fieldset) {
			
			//legend
			$legend = $fieldset->getLegend();
			if(!empty($legend)){
				$xtpl->assign("value", $legend);
				$xtpl->parse("main.fieldset.legend");
			}
			
			
			$fields = $fieldset->getFields();
			
			
			
			$field = $fields['ds_investigador'];		
			$input = $field->getInput();
			$label = $field->getLabel();	
			$this->renderLabel( $label, $input, $xtpl );
			$this->renderInput( $input, $xtpl );
			$xtpl->assign("minWidth", $field->getMinWidth());
			
			if( $input->getIsVisible() ){
				$xtpl->assign("display", 'block');
				
			}
			else $xtpl->assign("display", 'none');
			
			$xtpl->parse("main.fieldset.ds_investigador");
			
			$field = $fields['ds_facultad'];		
			$input = $field->getInput();
			$label = $field->getLabel();	
			$this->renderLabel( $label, $input, $xtpl );
			$this->renderInput( $input, $xtpl );
			$xtpl->assign("minWidth", $field->getMinWidth());
			
			if( $input->getIsVisible() ){
				$xtpl->assign("display", 'block');
				
			}
			else $xtpl->assign("display", 'none');
			
			$xtpl->parse("main.fieldset.ds_facultad");
			
			
			
			$field = $fields['nu_max'];		
			$input = $field->getInput();
			$label = $field->getLabel();	
			$this->renderLabel( $label, $input, $xtpl );
			$this->renderInput( $input, $xtpl );
			$xtpl->assign("minWidth", $field->getMinWidth());
			
			if( $input->getIsVisible() ){
				$xtpl->assign("display", 'block');
				
			}
			else $xtpl->assign("display", 'none');
			
			$xtpl->parse("main.fieldset.nu_max");
			
			$field = $fields['ds_contenido'];		
			
			CYTSecureUtils::logObject($field);
			
			$xtpl->assign("ds_contenido", $field->getInput()->getProperty("value") );
			
			$xtpl->parse("main.fieldset.ds_contenido");
			
			
			$field = $fields['ds_observacion'];		
			$input = $field->getInput();
			$label = $field->getLabel();	
			$this->renderLabel( $label, $input, $xtpl );
			$this->renderInput( $input, $xtpl );
			$xtpl->assign("minWidth", $field->getMinWidth());
			
			if( $input->getIsVisible() ){
				$xtpl->assign("display", 'block');
				
			}
			else $xtpl->assign("display", 'none');
			
			$xtpl->parse("main.fieldset.ds_observacion");
			
			
			$xtpl->parse("main.fieldset");
			
			//$xtpl->parse("main");
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
		$xtpl->parse("main.fieldset.".$input->getId().".label");
	}
	
	protected function renderInput( CMPFormInput $input, XTemplate $xtpl ){
		
		$xtpl->assign("input", $input->show() );
		
		$xtpl->parse("main.fieldset.".$input->getId().".input");
		
	}
	
	protected function renderLabelTab( $label, CMPFormInput $input, XTemplate $xtpl ){
		
		$xtpl->assign("value", $label );
		
		if( $input->getIsRequired() && $input->getIsEditable() ){
			$xtpl->assign("required", $input->getRequiredLabel() );
		}else{
			$xtpl->assign("required", "" );
		}
		
		$xtpl->assign("input_name", $input->getId() );
		$xtpl->parse("main.".$input->getId().".label");
	}
	
	protected function renderInputTab( CMPFormInput $input, XTemplate $xtpl ){
		
		$xtpl->assign("input", $input->show() );
		
		$xtpl->parse("main.".$input->getId().".input");
		
	}
	
	
	
	
	
	
	
}