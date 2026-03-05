<?php

/**
 * Formulario para Evaluacion
 *
 * @author Marcos
 * @since 11-12-2013
 */
class CMPEvaluacionForm extends CMPForm{

	
	
	public function getRenderer(){
		return new CMPEvaluacionFormRenderer();
	}
	
	/**
	 * se construye el formulario para editar el encomienda
	 */
	public function __construct($action="", $id="edit_evaluacion") {

		parent::__construct($id);

		$fieldset = new FormFieldset( "" );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( CYT_LBL_EVALUACION_SOLICITANTE, "ds_investigador", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( CYT_LBL_EVALUACION_FACULTAD, "ds_facultad", ""  ) );
		
		$input = FieldBuilder::buildFieldTextArea ( CYT_LBL_EVALUACION_OBSERVACIONES, "ds_observacion","","",8,110);
		$fieldset->addField( $input );
		
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_max", ""  ) );
		
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_contenido", ""  ) );
		
		
		
		
		$this->addFieldset($fieldset);
	
		$this->addHidden( FieldBuilder::buildInputHidden ( "oid", "") );
		$this->addHidden( FieldBuilder::buildInputHidden ( "user.oid", "") );
		$this->addHidden( FieldBuilder::buildInputHidden ( "solicitud.oid", "") );
		$this->addHidden( FieldBuilder::buildInputHidden ( "modeloPlanilla_oid", "") );
		$this->addHidden( FieldBuilder::buildInputHidden ( "nu_puntaje", "") );
		
		

		//properties del form.
		$this->addProperty("method", "POST");
		$this->addProperty("enctype", "multipart/form-data");
		$this->setAction("doAction?action=$action");
		$this->setOnCancel("window.location.href = 'doAction?action=list_solicitudes';");
		$this->setUseAjaxSubmit( true );
		//$this->setOnSuccessCallback("successTest");
		//$this->setUseAjaxCallback( true );
		//$this->setIdAjaxCallback( "content-left" );
		
	}


}
?>
