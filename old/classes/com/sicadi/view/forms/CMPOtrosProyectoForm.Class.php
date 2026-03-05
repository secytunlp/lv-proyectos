<?php

/**
 * Formulario para proyecto de solicitud
 *  
 * @author Marcos
 * @since 04-04-2023
 */
class CMPOtrosProyectoForm extends CMPForm{


	public function getLegend(){
		return '<div style="color:#A43B3B; font-weight:bold">'.CYT_MSG_ASIGNAR_AVISO.'</div>';
	}
	
	/**
	 * se construye el formulario para editar un detalle de venta
	 */
	public function __construct($action="add_otrosProyecto_session",$id="edit_otrosProyecto") {

		parent::__construct($id, CYT_MSG_ASIGNAR);
		
		$this->setCancelLabel( null );
		
		//properties del form.
    	$this->addProperty("method", "POST");
		$this->setAction("doAction?action=$action");
		$this->addHidden( FieldBuilder::buildInputHidden ( "oid", "") );
		
		$this->setUseAjaxSubmit( true );
		
		$this->getRenderer()->setTemplateName( CDT_CMP_TEMPLATE_FORM_INLINE );
		
		$this->setOnSuccessCallback("add_otrosProyecto");
		$this->setBeforeSubmit("before_submit_otrosProyecto");
		

		$fieldset = new FormFieldset( $this->getLegend() );
		$fieldset->addField( FieldBuilder::buildFieldText ( CYT_LBL_SOLICITUD_PROYECTOS_CODIGO, "ds_codigo", CYT_MSG_PROYECTO_CODIGO_REQUIRED,"",8) );
		$fieldset->addField( FieldBuilder::buildFieldText ( CYT_LBL_SOLICITUD_PROYECTOS_TITULO, "ds_titulo", CYT_MSG_PROYECTO_TITULO_REQUIRED,"",40) );
		$fieldset->addField( FieldBuilder::buildFieldText ( CYT_LBL_SOLICITUD_PROYECTOS_DIRECTOR, "ds_director", CYT_MSG_PROYECTO_DIRECTOR_REQUIRED,"",25) );
		$fieldset->addField( FieldBuilder::buildFieldText ( CYT_LBL_SOLICITUD_ORGANISMO_BECA, "ds_organismo", CYT_MSG_PROYECTO_ORGANISMO_REQUIRED,"",25) );
		$fieldset->addField( FieldBuilder::buildFieldDate ( CYT_LBL_SOLICITUD_PROYECTOS_INICIO, "dt_desdeproyecto", CYT_MSG_PROYECTO_INICIO_REQUIRED) );
		$fieldset->addField( FieldBuilder::buildFieldDate ( CYT_LBL_SOLICITUD_PROYECTOS_FIN, "dt_hastaproyecto", CYT_MSG_PROYECTO_FIN_REQUIRED) );

		$this->addFieldset($fieldset);
		
    }
    
}
?>
