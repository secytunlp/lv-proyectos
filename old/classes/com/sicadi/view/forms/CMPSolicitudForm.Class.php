<?php

/**
 * Formulario para Solicitud
 *
 * @author Marcos
 * @since 11-12-2013
 */
class CMPSolicitudForm extends CMPForm{

	
	
	public function getRenderer(){
		return new CMPSolicitudFormRenderer();
	}
	
	/**
	 * se construye el formulario para editar el encomienda
	 */
	public function __construct($action="", $id="edit_solicitud") {

		parent::__construct($id);

		$fieldset = new FormFieldset( "" );
		$fieldset->addField( FieldBuilder::buildFieldDisabled ( CYT_LBL_SOLICITUD_SOLICITANTE, "ds_investigador", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldDisabled ( CYT_LBL_SOLICITUD_CUIL, "nu_cuil", ""  ) );
		
		
		$fieldset->addField( FieldBuilder::buildFieldText ( CYT_LBL_SOLICITUD_CALLE, "ds_calle", "","",23) );
		$fieldset->addField( FieldBuilder::buildFieldText ( CYT_LBL_SOLICITUD_CALLE_NRO, "nu_nro", "","",23) );
		$fieldset->addField( FieldBuilder::buildFieldText ( CYT_LBL_SOLICITUD_PISO, "nu_piso","","",23 ) );
		$fieldset->addField( FieldBuilder::buildFieldText ( CYT_LBL_SOLICITUD_DEPTO, "ds_depto","", "",23) );
		$fieldset->addField( FieldBuilder::buildFieldText ( CYT_LBL_SOLICITUD_CP, "nu_cp", "","",23) );
		$fieldset->addField( FieldBuilder::buildFieldEmail ( CYT_LBL_SOLICITUD_MAIL, "ds_mail", "","",23) );
		$fieldset->addField( FieldBuilder::buildFieldText ( CYT_LBL_SOLICITUD_TELEFONO, "nu_telefono","","",23) );
		$field = FieldBuilder::buildFieldCheckbox ( CYT_LBL_SOLICITUD_MAIL_ACEPTO, "bl_notificacion", "bl_notificacion") ;
		$field->getInput()->addProperty("style", "width:20px");
		$fieldset->addField( $field );
		$fieldset->addField( FieldBuilder::buildFieldEmail ( CYT_LBL_SOLICITUD_OTRO_MAIL, "ds_otromail", "","",23) );

		$fieldGenero = FieldBuilder::buildFieldSelect (CYT_LBL_SOLICITUD_GENERO, "ds_genero", Genero::getItems(), "", null, null, "--seleccionar--" );
		$fieldset->addField( $fieldGenero );

		//$fieldset->addField( FieldBuilder::buildFieldText ( CYT_LBL_SOLICITUD_ORCID, "ds_orcid","","",60) );
		$field = FieldBuilder::buildFieldText ( CYT_LBL_SOLICITUD_ORCID, "ds_orcid", "","",23);
		$field->getInput()->addProperty("placeholder", "XXXX-XXXX-XXXX-XXXX");
		$fieldset->addField( $field );
		//$fieldset->addField( FieldBuilder::buildFieldText ( CYT_LBL_SOLICITUD_SEDICI, "ds_sedici","","",60) );
		$field = FieldBuilder::buildFieldText ( CYT_LBL_SOLICITUD_SEDICI, "ds_sedici", "","",23);
		$field->getInput()->addProperty("placeholder", "http://sedici.unlp.edu.ar/browse?authority=...");
		$fieldset->addField( $field );
		//$fieldset->addField( FieldBuilder::buildFieldText ( CYT_LBL_SOLICITUD_SCHOLAR, "ds_scholar","","",60) );
		$field = FieldBuilder::buildFieldText ( CYT_LBL_SOLICITUD_SCHOLAR, "ds_scholar", "","",23);
		$field->getInput()->addProperty("placeholder", "https://scholar.google.com/citations?user=...=es");
		$fieldset->addField( $field );
		$field = FieldBuilder::buildFieldText ( CYT_LBL_SOLICITUD_SCOPUS, "ds_scopus", "","",23);
		//$field->getInput()->addProperty("placeholder", "https://scholar.google.com/citations?user=...=es");
		$fieldset->addField( $field );
		//$fieldset->addField( FieldBuilder::buildFieldText ( CYT_LBL_SOLICITUD_INSTAGRAM, "ds_instagram","","",60) );
		/*$field = FieldBuilder::buildFieldText ( CYT_LBL_SOLICITUD_INSTAGRAM, "ds_instagram", "","",60);
		$field->getInput()->addProperty("placeholder", "https://www.instagram.com/...");
		$fieldset->addField( $field );*/
		//$fieldset->addField( FieldBuilder::buildFieldText ( CYT_LBL_SOLICITUD_TWITTER, "ds_twitter","","",60) );
		/*$field = FieldBuilder::buildFieldText ( CYT_LBL_SOLICITUD_TWITTER, "ds_twitter", "","",60);
		$field->getInput()->addProperty("placeholder", "https://twitter.com/...");
		$fieldset->addField( $field );*/
		//$fieldset->addField( FieldBuilder::buildFieldText ( CYT_LBL_SOLICITUD_FACEBOOK, "ds_facebook","","",60) );
		/*$field = FieldBuilder::buildFieldText ( CYT_LBL_SOLICITUD_FACEBOOK, "ds_facebook", "","",60);
		$field->getInput()->addProperty("placeholder", "https://www.facebook.com/...");
		$fieldset->addField( $field );*/



		
		//$fieldTitulo = FieldBuilder::buildFieldEntityAutocomplete(CYT_LBL_SOLICITUD_TITULO, new CMPTituloAutocomplete(),"ds_titulogrado",CYT_MSG_SOLICITUD_TITULO_REQUIRED,"",60);
		
		$fieldTitulo = CYTSecureComponentsFactory::getFindTituloWithAdd(new Titulo(), CYT_LBL_SOLICITUD_TITULO, "", "solicitud_filter_titulo_oid", "titulo.oid","solicitud_filter_titulo_change");
		$fieldTitulo->getInput()->setInputSize(5,80);
		$fieldset->addField( $fieldTitulo );
		
		$fieldset->addField( FieldBuilder::buildFieldDate ( CYT_LBL_SOLICITUD_EGRESO_GRADO, "dt_egresogrado", "","d/m/Y","",23) );
		
		$fieldTitulo = CYTSecureComponentsFactory::getFindTituloPosgradoWithAdd(new Titulo(), CYT_LBL_SOLICITUD_TITULO_POSGRADO, "", "solicitud_filter_tituloposgrado_oid", "tituloposgrado.oid","solicitud_filter_tituloposgrado_change");
		$fieldTitulo->getInput()->setInputSize(5,80);
		$fieldset->addField( $fieldTitulo );
		
		$fieldset->addField( FieldBuilder::buildFieldDate ( CYT_LBL_SOLICITUD_EGRESO_POSGRADO, "dt_egresoposgrado", "","d/m/Y","",23) );

		
		/*$findLugarTrabajo = CYTSecureComponentsFactory::getFindLugarTrabajo(new LugarTrabajo(), CYT_LBL_SOLICITUD_LUGAR_TRABAJO_EXTENDIDO, "", "solicitud_filter_lugarTrabajo_oid", "lugarTrabajo.oid","solicitud_filter_lugarTrabajo_change");
		$findLugarTrabajo->getInput()->setInputSize(5,80);
		//$findLugarTrabajo->getInput()->setFunctionCallback("editSolicitud_lugarTrabajoCallback");
		$fieldset->addField( $findLugarTrabajo );*/

		$findLugarTrabajo = FieldBuilder::buildFieldSelect (CYT_LBL_SOLICITUD_LUGAR_TRABAJO_EXTENDIDO, "lugarTrabajo.oid", CYTUtils::getLugarTrabajoItems(), "", null, null, "--seleccionar--", "lugarTrabajo_oid" );
		$findLugarTrabajo->getInput()->addProperty( 'class', 'js-example-basic-single' );
		$fieldset->addField( $findLugarTrabajo );
		
		$fieldset->addField( FieldBuilder::buildFieldDate ( CYT_LBL_SOLICITUD_NACIMIENTO, "dt_nacimiento", "","d/m/Y","",23) );

		$fieldCargo = FieldBuilder::buildFieldSelect (CYT_LBL_SOLICITUD_CARGO, "cargo.oid", CYTSecureUtils::getCargosItems('1,2,3,4,5,7,8,9,10,11,12,13,14'), "", null, null, "--seleccionar--", "cargo_oid" );
		$fieldset->addField( $fieldCargo );
	
	  	/*$fieldDeddoc = FieldBuilder::buildFieldSelect (CYT_LBL_SOLICITUD_DEDICACION, "deddoc.oid", CYTSecureUtils::getDeddocsItems('1,2,3,4'), "", null, null, "--seleccionar--", "deddoc_oid" );
		$fieldset->addField( $fieldDeddoc );*/

		$fieldDeddoc = FieldBuilder::buildFieldSelect (CYT_LBL_SOLICITUD_DEDICACION, "deddoc.oid", Dedicacion::getItems(), "", null, null, "--seleccionar--" , "deddoc_oid");
		$fieldset->addField( $fieldDeddoc );
	
	  	$fieldFacultad = FieldBuilder::buildFieldSelect (CYT_LBL_SOLICITUD_FACULTAD, "facultad.oid", CYTSecureUtils::getFacultadesItems('165,167,168,169,170,171,172,173,174,175,176,177,179,180,181,187,1220'), "", null, null, "--seleccionar--", "facultad_oid" );
		$fieldset->addField( $fieldFacultad );

		/*$input = FieldBuilder::buildFieldTextArea ( CYT_LBL_SOLICITUD_EXPERTICIA_DOCENTE, "ds_experticiaD","","",8,110);
		$fieldset->addField( $input );

		$fieldset->addField( FieldBuilder::buildFieldText ( "", "ds_claveD1", "","",20) );
		$fieldset->addField( FieldBuilder::buildFieldText ( "", "ds_claveD2", "","",20) );
		$fieldset->addField( FieldBuilder::buildFieldText ( "", "ds_claveD3", "","",20) );
		$fieldset->addField( FieldBuilder::buildFieldText ( "", "ds_claveD4", "","",20) );
		$fieldset->addField( FieldBuilder::buildFieldText ( "", "ds_claveD5", "","",20) );
		$fieldset->addField( FieldBuilder::buildFieldText ( "", "ds_claveD6", "","",20) );*/

		$fieldCategoria = FieldBuilder::buildFieldSelect (CYT_LBL_SOLICITUD_CATEGORIA, "categoria.oid", CYTSecureUtils::getCategoriasItems(CYT_CATEGORIA_MOSTRADAS), "", null, null, "--seleccionar--", "categoria_oid" );
		$fieldCategoria->getInput()->setIsEditable(false);
		$fieldset->addField( $fieldCategoria );

		$fieldCategoriasicadi = FieldBuilder::buildFieldSelect (CYT_LBL_SOLICITUD_CATEGORIA_SOLICITADA, "categoriasicadi.oid", CYTUtils::getCategoriasItems(), "", null, null, "--seleccionar--", "categoriasicadi_oid" );
		//$fieldCategoria->getInput()->setIsEditable(false);

		$fieldset->addField( $fieldCategoriasicadi );

		$fieldEquivalencia = FieldBuilder::buildFieldSelect (CYT_LBL_SOLICITUD_EQUIVALENCIA, "equivalencia.oid",CYTUtils::getEquivalenciasItems(), "", null, null, "--seleccionar--", "equivalencia_oid" );
		//$fieldCategoria->getInput()->setIsEditable(false);
		
		$fieldEquivalencia->getInput()->addProperty( 'data-custom-event', 'change' );
		$fieldEquivalencia->getInput()->addProperty( 'data-custom-function', 'seleccionarEquivalencia' );
		$fieldset->addField( $fieldEquivalencia );
		
		//$fieldset->addField( FieldBuilder::buildFieldText ( CYT_LBL_SOLICITUD_ORGANISMO_BECA, "ds_orgbeca") );
		//$fieldset->addField( FieldBuilder::buildFieldText ( CYT_LBL_SOLICITUD_TIPO_BECA, "ds_tipobeca") );


		$fieldBeca = FieldBuilder::buildFieldSelect (CYT_LBL_SOLICITUD_ORGANISMO_BECA, "ds_orgbeca", Institucion::getItems('beca'), "", null, null, "--seleccionar--" );
		$fieldBeca->getInput()->addProperty( 'data-custom-event', 'change' );
		$fieldBeca->getInput()->addProperty( 'data-custom-function', 'seleccionarInstitucion' );
		$fieldset->addField( $fieldBeca );

		$fieldBeca = FieldBuilder::buildFieldSelect (CYT_LBL_SOLICITUD_TIPO_BECA, "ds_tipobeca", Tipobeca::getItems(), "", null, null);
		//print_r($fieldBeca);
		$fieldset->addField( $fieldBeca );

		$fieldset->addField( FieldBuilder::buildFieldDate ( CYT_LBL_SOLICITUD_BECA_DESDE, "dt_becadesde") );
		$fieldset->addField( FieldBuilder::buildFieldDate ( CYT_LBL_SOLICITUD_BECA_HASTA, "dt_becahasta") );
		
		/*$findLugarTrabajo = CYTSecureComponentsFactory::getFindLugarTrabajo(new LugarTrabajo(), CYT_LBL_SOLICITUD_LUGAR_TRABAJO_BECA, "", "solicitud_filter_lugarTrabajoBeca_oid", "lugarTrabajoBeca.oid","solicitud_filter_lugarTrabajoBeca_change");
		$findLugarTrabajo->getInput()->setInputSize(5,80);

		$fieldset->addField( $findLugarTrabajo );*/

		$findLugarTrabajo = FieldBuilder::buildFieldSelect (CYT_LBL_SOLICITUD_LUGAR_TRABAJO_BECA, "lugarTrabajoBeca.oid", CYTUtils::getLugarTrabajoItems(), "", null, null, "--seleccionar--", "lugarTrabajoBeca_oid" );
		$findLugarTrabajo->getInput()->addProperty( 'class', 'js-example-basic-single' );
		$fieldset->addField( $findLugarTrabajo );


		/*$input = FieldBuilder::buildFieldTextArea ( CYT_LBL_SOLICITUD_EXPERTICIA_INVESTIGACION, "ds_experticiaB","","",8,110);
		$fieldset->addField( $input );

		$fieldset->addField( FieldBuilder::buildFieldText ( "", "ds_claveB1", "","",20) );
		$fieldset->addField( FieldBuilder::buildFieldText ( "", "ds_claveB2", "","",20) );
		$fieldset->addField( FieldBuilder::buildFieldText ( "", "ds_claveB3", "","",20) );
		$fieldset->addField( FieldBuilder::buildFieldText ( "", "ds_claveB4", "","",20) );
		$fieldset->addField( FieldBuilder::buildFieldText ( "", "ds_claveB5", "","",20) );
		$fieldset->addField( FieldBuilder::buildFieldText ( "", "ds_claveB6", "","",20) );*/


		$fieldOrganismo = FieldBuilder::buildFieldSelect (CYT_LBL_SOLICITUD_INSTITUCION_CARRERAINV, "organismo.oid", CYTSecureUtils::getOrganismosItems(CYT_ORGANISMO_MOSTRADAS), "", null, null, "--seleccionar--", "organismo_oid" );
		$fieldOrganismo->getInput()->addProperty( 'data-custom-event', 'change' );
		$fieldOrganismo->getInput()->addProperty( 'data-custom-function', 'seleccionarOrganismo' );
		$fieldset->addField( $fieldOrganismo );

		$fieldCarreraInv = FieldBuilder::buildFieldSelect (CYT_LBL_SOLICITUD_CATEGORIA_CARRERAINV, "carrerainv.oid", Carrera::getItems('2'), "", null, null, "--seleccionar--", "carrerainv_oid" );


		//$fieldCarreraInv = FieldBuilder::buildFieldSelect (CYT_LBL_SOLICITUD_CATEGORIA_CARRERAINV, "carrerainv.oid", CYTSecureUtils::getCarreraInvsItems(CYT_CARRERAINV_MOSTRADAS), "", null, null, "--seleccionar--", "carrerainv_oid" );
		$fieldset->addField( $fieldCarreraInv );
		
		$fieldset->addField( FieldBuilder::buildFieldDate ( CYT_LBL_SOLICITUD_INGRESO_CARRERAINV, "dt_ingreso") );
		
		/*$findLugarTrabajo = CYTSecureComponentsFactory::getFindLugarTrabajo(new LugarTrabajo(), CYT_LBL_SOLICITUD_LUGAR_TRABAJO_CARRERAINV, "", "solicitud_filter_lugarTrabajoCarrerainv_oid", "lugarTrabajoCarrera.oid","solicitud_filter_lugarTrabajoCarrerainv_change");
		$findLugarTrabajo->getInput()->setInputSize(5,80);
		$fieldset->addField( $findLugarTrabajo );*/

		$findLugarTrabajo = FieldBuilder::buildFieldSelect (CYT_LBL_SOLICITUD_LUGAR_TRABAJO_CARRERAINV, "lugarTrabajoCarrera.oid", CYTUtils::getLugarTrabajoItems(), "", null, null, "--seleccionar--", "lugarTrabajoCarrera_oid" );
		$findLugarTrabajo->getInput()->addProperty( 'class', 'js-example-basic-single' );
		$fieldset->addField( $findLugarTrabajo );

		$fieldArea = FieldBuilder::buildFieldSelect (CYT_LBL_SOLICITUD_AREA, "areacarrera.oid",CYTUtils::getAreasItems(), "", null, null, "--seleccionar--", "areacarrera_oid" );
		//$fieldCategoria->getInput()->setIsEditable(false);
		
		$fieldArea->getInput()->addProperty( 'data-custom-event', 'change' );
		$fieldArea->getInput()->addProperty( 'data-custom-function', 'seleccionarAreaCarrera' );
		$fieldset->addField( $fieldArea );

		$fieldSubarea = FieldBuilder::buildFieldSelect (CYT_LBL_SOLICITUD_SUBAREA, "subareacarrera.oid",CYTUtils::getSubareasItems(null), "", null, null, "--seleccionar--", "subareacarrera_oid" );

		$fieldset->addField( $fieldSubarea );

		/*$input = FieldBuilder::buildFieldTextArea ( CYT_LBL_SOLICITUD_EXPERTICIA_INVESTIGACION, "ds_experticiaC","","",8,110);
		$fieldset->addField( $input );

		$fieldset->addField( FieldBuilder::buildFieldText ( "", "ds_claveC1", "","",20) );
		$fieldset->addField( FieldBuilder::buildFieldText ( "", "ds_claveC2", "","",20) );
		$fieldset->addField( FieldBuilder::buildFieldText ( "", "ds_claveC3", "","",20) );
		$fieldset->addField( FieldBuilder::buildFieldText ( "", "ds_claveC4", "","",20) );
		$fieldset->addField( FieldBuilder::buildFieldText ( "", "ds_claveC5", "","",20) );
		$fieldset->addField( FieldBuilder::buildFieldText ( "", "ds_claveC6", "","",20) );*/

		
		$fieldFacultadplanilla = FieldBuilder::buildFieldSelect (CYT_LBL_SOLICITUD_FACULTAD_PLANILLA, "facultadplanilla.oid", CYTSecureUtils::getFacultadesItems('165,167,168,169,170,171,172,173,174,175,176,177,179,180,181,187,1220'), "", null, null, "--seleccionar--", "facultadplanilla_oid" );
		$fieldset->addField( $fieldFacultadplanilla );

		$fieldYear = FieldBuilder::buildFieldSelect (CYT_LBL_SOLICITUD_YEAR1, "nu_year1",CYTUtils::getYearItems() , "", null, null, null, "nu_year1" );
		$fieldset->addField( $fieldYear );
		$fieldYear = FieldBuilder::buildFieldSelect (CYT_LBL_SOLICITUD_YEAR2, "nu_year2",CYTUtils::getYearItems() , "", null, null, null, "nu_year2" );
		$fieldset->addField( $fieldYear );
		$fieldYear = FieldBuilder::buildFieldSelect (CYT_LBL_SOLICITUD_YEAR3, "nu_year3",CYTUtils::getYearItems() , "", null, null, null, "nu_year3" );
		$fieldset->addField( $fieldYear );
		
		$fieldset->addField( FieldBuilder::buildFieldText ( CYT_LBL_SOLICITUD_DISCIPLINA, "ds_disciplina") );


		$fieldset->addField( FieldBuilder::buildFieldText ( CYT_LBL_SOLICITUD_PROYECTOS_CODIGO, "ds_codigoproyecto","","",10 ) );
		$fieldset->addField( FieldBuilder::buildFieldText ( CYT_LBL_SOLICITUD_PROYECTOS_TITULO, "ds_tituloproyecto","","",100 ) );
		$fieldset->addField( FieldBuilder::buildFieldText ( CYT_LBL_SOLICITUD_PROYECTOS_DIRECTOR, "ds_directorproyecto") );
		$fieldBeca = FieldBuilder::buildFieldSelect (CYT_LBL_SOLICITUD_ORGANISMO_BECA, "ds_organismoproyecto", Institucion::getItems('proyecto'), "", null, null, "--seleccionar--" );

		$fieldset->addField( $fieldBeca );
		//$fieldset->addField( FieldBuilder::buildFieldText ( CYT_LBL_SOLICITUD_ORGANISMO_BECA, "ds_organismoproyecto") );
		$fieldset->addField( FieldBuilder::buildFieldDate ( CYT_LBL_SOLICITUD_PROYECTOS_INICIO, "dt_proyectodesde") );
		$fieldset->addField( FieldBuilder::buildFieldDate ( CYT_LBL_SOLICITUD_PROYECTOS_FIN, "dt_proyectohasta") );
			
		
				
		/*$inputObjetivo = FieldBuilder::buildFieldTextArea ( CYT_LBL_SOLICITUD_OBJETIVO, "ds_objetivo","","",8,110);
		$fieldset->addField( $inputObjetivo );
		
		$input = FieldBuilder::buildFieldTextArea ( CYT_LBL_SOLICITUD_JUSTIFICACION_2017, "ds_justificacion","","",8,110);
		$fieldset->addField( $input );*/
		
		
		
		$this->addFieldset($fieldset);
	
		$this->addHidden( FieldBuilder::buildInputHidden ( "oid", "") );
		$this->addHidden( FieldBuilder::buildInputHidden ( "bl_unlp", "") );
		$this->addHidden( FieldBuilder::buildInputHidden ( "categoria.oid", "") );

		//$this->addHidden( FieldBuilder::buildInputHidden ( "ds_curriculum", "") );
		$this->addHidden( FieldBuilder::buildInputHidden ( "ds_resbeca", "") );
		$this->addHidden( FieldBuilder::buildInputHidden ( "ds_rescarrera", "") );
		$this->addHidden( FieldBuilder::buildInputHidden ( "ds_archivo", "") );
		$this->addHidden( FieldBuilder::buildInputHidden ( "ds_foto", "") );
		$this->addHidden( FieldBuilder::buildInputHidden ( "ds_informe1", "") );
		$this->addHidden( FieldBuilder::buildInputHidden ( "ds_informe2", "") );
		$this->addHidden( FieldBuilder::buildInputHidden ( "ds_informe3", "") );
		
		

		//properties del form.
		$this->addProperty("method", "POST");
		$this->addProperty("enctype", "multipart/form-data");
		$this->setAction("doAction?action=$action");
		$this->setOnCancel("window.location.href = 'doAction?action=list_solicitudes';");
		$this->setUseAjaxSubmit( true );
		//$this->setOnSuccessCallback("successTest");
		//$this->setUseAjaxCallback( true );
		//$this->setIdAjaxCallback( "content-left" );
		$this->setCustomHTML('<script nonce="tu_nonce_generado"> $(function() {$("#organismo_oid").change();$("#ds_orgbeca").change();$("#equivalencia_oid").change();$("#areabeca_oid").change();$("#areacarrera_oid").change();});</script>');
	}


}
?>
