<?php

/**
 * Formulario para RectificarSolicitud
 *
 * @author Marcos
 * @since 07-07-2023
 */
class CMPRectifySolicitudForm extends CMPForm{

	/**
	 * se construye el formulario para editar el registro
	 */
	public function __construct($action="", $id="edit_rechazarSolicitud") {

		parent::__construct($id);

		$fieldset = new FormFieldset( "" );
		//$fieldset->addField( FieldBuilder::buildFieldReadOnly ( CDT_ENTITIES_LBL_ENTITY_OID, "oid", ""  ) );
		
			
		$findSolicitud = CYTSecureComponentsFactory::getFindSolicitud(new Solicitud(), CYT_LBL_SOLICITUD, CYT_MSG_SOLICITUD_ESTADO_SOLICITUD_REQUIRED, "solicitud_form_estado_oid", "solicitud.oid", "estado_filter_solicitud_change");
		//$findSolicitud->getInput()->setInputSize(5,80);
		$fieldset->addField( $findSolicitud );
		
		$fieldset->addField( FieldBuilder::buildFieldTextArea ( CYT_MSG_SOLICITUD_RECHAZAR_MOTIVOS, "observaciones", CYT_MSG_SOLICITUD_RECHAZAR_MOTIVOS_REQUIRED  ) );
		
		
		$this->addFieldset($fieldset);
		$this->addHidden( FieldBuilder::buildInputHidden ( "oid", "") );

		//properties del form.
		$this->addProperty("method", "POST");
		$this->setAction("doAction?action=$action");
		
		$cancel = 'doAction?action=list_solicitudes';	
		
		$this->setOnCancel("window.location.href = '$cancel';");
		$this->setUseAjaxSubmit( true );
		//$this->setOnSuccessCallback("successTest");
		//$this->setUseAjaxCallback( true );
		//$this->setIdAjaxCallback( "content-left" );
	}

}
?>
