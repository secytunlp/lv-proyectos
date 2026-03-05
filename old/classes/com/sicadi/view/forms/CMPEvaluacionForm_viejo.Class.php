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
		
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_max", ""  ) );
		
		
		$oPeriodoManager = CYTSecureManagerFactory::getPeriodoManager();
		$oPerioActual = $oPeriodoManager->getPeriodoActual(CYT_PERIODO_YEAR);
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_periodo', $oPerioActual->getOid(), '=');
		$managerModeloPlanilla =  ManagerFactory::getModeloPlanillaManager();
		$oModeloPlanilla = $managerModeloPlanilla->getEntity($oCriteria);
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_modeloplanilla', $oModeloPlanilla->getOid(), '=');
		$oCriteria->addOrder('nu_max','DESC');
		$managerPosgradoMaximo =  ManagerFactory::getPosgradoMaximoManager();
		$oPosgradosMaximos = $managerPosgradoMaximo->getEntities($oCriteria);
		
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_posgradomaximo", ""  ) );
		$radios = array();
		
		foreach ($oPosgradosMaximos as $oPosgradosMaximo) {				
			$radio = FieldBuilder::buildFieldRadio( $oPosgradosMaximo->getPosgradoPlanilla()->getDs_posgradoplanilla(). ' ('.$oPosgradosMaximo->getNu_max().CYT_LBL_EVALUACION_PT.')', "cd_posgradomaximo", "cd_posgradomaximo", false, $oPosgradosMaximo->getOid().'-'.$oPosgradosMaximo->getNu_max());	
			$radio->getInput()->addProperty( 'onClick', 'sumar_total(this)' );
			$radios[] = $radio;
		}
		$fieldset->addField( FieldBuilder::buildFieldRadios(  "", "cd_posgradomaximo", $radios) );
		
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_antacadmaximo", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_topeA2", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_descripcionA2", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_puntajeA2", ""  ) );
		
		
		
		$field = FieldBuilder::buildFieldNumber( "", "nu_puntajeantacadA2","","",1 );
		$field->getInput()->addProperty( 'onChange', 'sumar_total(this)' );
		$fieldset->addField( $field);
		
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_topeA3", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_descripcionA3", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_puntajeA3", ""  ) );
		$field = FieldBuilder::buildFieldNumber( "", "nu_puntajeantacadA3","","",1 );
		$field->getInput()->addProperty( 'onChange', 'sumar_total(this)' );
		$fieldset->addField( $field);
		
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_descripcionA4", ""  ) );
		$field = FieldBuilder::buildFieldCheckbox ( CYT_LBL_EVALUACION_POSGRADO, "bl_posgrado", "bl_posgrado");
		$field->getInput()->addProperty( 'onClick', 'sumar_total(this)' );
		$fieldset->addField( $field);
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_puntajeantacadA4", ""  ) );
		
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_descripcionA5", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_puntajeA5", ""  ) );
		$field = FieldBuilder::buildFieldCheckbox( "", "nu_puntajeantacadA5", "nu_puntajeantacadA5" );
		$field->getInput()->addProperty( 'onClick', 'sumar_total(this)' );
		$fieldset->addField( $field);
		
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_topeA6", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_descripcionA6", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_puntajeA6", ""  ) );
		$field = FieldBuilder::buildFieldNumber( "", "nu_puntajeantacadA6","","",1 );
		$field->getInput()->addProperty( 'onChange', 'sumar_total(this)' );
		$fieldset->addField( $field);
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_modeloplanilla', $oModeloPlanilla->getOid(), '=');
		//$oCriteria->addOrder('nu_max','DESC');
		$managerCargoMaximo =  ManagerFactory::getCargoMaximoManager();
		$oCargosMaximos = $managerCargoMaximo->getEntities($oCriteria);
		
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_cargomaximo", ""  ) );
		$radios = array();
		
		foreach ($oCargosMaximos as $oCargosMaximo) {				
			$radio = FieldBuilder::buildFieldRadio( '<br>'.$oCargosMaximo->getCargoPlanilla()->getDs_cargoplanilla(). ' ('.$oCargosMaximo->getNu_max().CYT_LBL_EVALUACION_PT.')', "cd_cargomaximo", "cd_cargomaximo", false, $oCargosMaximo->getOid().'-'.$oCargosMaximo->getNu_max());	
			$radio->getInput()->addProperty( 'onClick', 'sumar_total(this)' );
			$radios[] = $radio;
		}
		$field = FieldBuilder::buildFieldRadios(  "", "cd_cargomaximo", $radios);
		$field->getInput()->setIsEditable(false);
		$fieldset->addField( $field );
		
		
		
		$input = FieldBuilder::buildFieldTextArea ( CYT_LBL_EVALUACION_OBSERVACIONES, "ds_observacion","","",8,135);
		$fieldset->addField( $input );
		
		
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_antotrosmaximo", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_topeC1", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_descripcionC1", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_puntajeC1", ""  ) );
		$field = FieldBuilder::buildFieldNumber( "", "nu_puntajeantotrosC1","","",1 );
		$field->getInput()->addProperty( 'onChange', 'sumar_total(this)' );
		$fieldset->addField( $field);
		
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_grupoC2", ""  ) );
		
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_descripcionC2_1", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_puntajeC2_1", ""  ) );
		$field = FieldBuilder::buildFieldNumber( "", "nu_puntajeantotrosC2_1","","",1 );
		$field->getInput()->addProperty( 'onChange', 'sumar_total(this)' );
		$fieldset->addField( $field);
		
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_descripcionC2_2", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_puntajeC2_2", ""  ) );
		$field = FieldBuilder::buildFieldNumber( "", "nu_puntajeantotrosC2_2","","",1 );
		$field->getInput()->addProperty( 'onChange', 'sumar_total(this)' );
		$fieldset->addField( $field);
		
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_descripcionC2_3", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_puntajeC2_3", ""  ) );
		$field = FieldBuilder::buildFieldNumber( "", "nu_puntajeantotrosC2_3","","",1 );
		$field->getInput()->addProperty( 'onChange', 'sumar_total(this)' );
		$fieldset->addField( $field);
		
		/*$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_topeC3", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_descripcionC3", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_puntajeC3", ""  ) );
		$field = FieldBuilder::buildFieldNumber( "", "nu_puntajeantotrosC3","","",1 );
		$field->getInput()->addProperty( 'onChange', 'sumar_total(this)' );
		$fieldset->addField( $field);*/
		
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_descripcionC4", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_puntajeC4", ""  ) );
		$field = FieldBuilder::buildFieldCheckbox( "", "nu_puntajeantotrosC4", "nu_puntajeantotrosC4" );
		$field->getInput()->addProperty( 'onClick', 'sumar_total(this)' );
		$field->getInput()->setIsEditable(false);
		$fieldset->addField( $field);
		
		
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_antproduccionmaximo", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_grupoD1", ""  ) );
		/*$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_topeD1_1", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_descripcionD1_1", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_puntajeD1_1", ""  ) );
		$field = FieldBuilder::buildFieldNumber( "", "nu_puntajeantproduccionD1_1","","",1 );
		$field->getInput()->addProperty( 'onChange', 'sumar_total(this)' );
		$fieldset->addField( $field);
		
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_topeD1_2", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_descripcionD1_2", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_puntajeD1_2", ""  ) );
		$field = FieldBuilder::buildFieldNumber( "", "nu_puntajeantproduccionD1_2","","",1 );
		$field->getInput()->addProperty( 'onChange', 'sumar_total(this)' );
		$fieldset->addField( $field);*/
		
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_grupoD2", ""  ) );
		/*$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_topeD2_1", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_descripcionD2_1", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_puntajeD2_1", ""  ) );
		$field = FieldBuilder::buildFieldNumber( "", "nu_puntajeantproduccionD2_1","","",1 );
		$field->getInput()->addProperty( 'onChange', 'sumar_total(this)' );
		$fieldset->addField( $field);*/
		
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_topeD2_2", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_descripcionD2_2", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_puntajeD2_2", ""  ) );
		$field = FieldBuilder::buildFieldNumber( "", "nu_puntajeantproduccionD2_2","","",1 );
		$field->getInput()->addProperty( 'onChange', 'sumar_total(this)' );
		$fieldset->addField( $field);
		
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_topeD2_3", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_descripcionD2_3", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_puntajeD2_3", ""  ) );
		$field = FieldBuilder::buildFieldNumber( "", "nu_puntajeantproduccionD2_3","","",1 );
		$field->getInput()->addProperty( 'onChange', 'sumar_total(this)' );
		$fieldset->addField( $field);
		
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_topeD2_4", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_descripcionD2_4", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_puntajeD2_4", ""  ) );
		$field = FieldBuilder::buildFieldNumber( "", "nu_puntajeantproduccionD2_4","","",1 );
		$field->getInput()->addProperty( 'onChange', 'sumar_total(this)' );
		$fieldset->addField( $field);
		
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_topeD2_5", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_descripcionD2_5", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_puntajeD2_5", ""  ) );
		$field = FieldBuilder::buildFieldNumber( "", "nu_puntajeantproduccionD2_5","","",1 );
		$field->getInput()->addProperty( 'onChange', 'sumar_total(this)' );
		$fieldset->addField( $field);
		
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_grupoD3", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_descripcionD3_1", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_puntajeD3_1", ""  ) );
		$field = FieldBuilder::buildFieldNumber( "", "nu_puntajeantproduccionD3_1","","",1 );
		$field->getInput()->addProperty( 'onChange', 'sumar_total(this)' );
		$fieldset->addField( $field);
		
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_descripcionD3_2", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_puntajeD3_2", ""  ) );
		$field = FieldBuilder::buildFieldNumber( "", "nu_puntajeantproduccionD3_2","","",1 );
		$field->getInput()->addProperty( 'onChange', 'sumar_total(this)' );
		$fieldset->addField( $field);
		
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_descripcionD3_3", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_puntajeD3_3", ""  ) );
		$field = FieldBuilder::buildFieldNumber( "", "nu_puntajeantproduccionD3_3","","",1 );
		$field->getInput()->addProperty( 'onChange', 'sumar_total(this)' );
		$fieldset->addField( $field);
		
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_descripcionD3_4", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_puntajeD3_4", ""  ) );
		$field = FieldBuilder::buildFieldNumber( "", "nu_puntajeantproduccionD3_4","","",1 );
		$field->getInput()->addProperty( 'onChange', 'sumar_total(this)' );
		$fieldset->addField( $field);
		
		/*$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_descripcionD3_5", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_puntajeD3_5", ""  ) );
		$field = FieldBuilder::buildFieldNumber( "", "nu_puntajeantproduccionD3_5","","",1 );
		$field->getInput()->addProperty( 'onChange', 'sumar_total(this)' );
		$fieldset->addField( $field);*/
		
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_grupoD4", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_descripcionD4_1", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_puntajeD4_1", ""  ) );
		$field = FieldBuilder::buildFieldNumber( "", "nu_puntajeantproduccionD4_1","","",1 );
		$field->getInput()->addProperty( 'onChange', 'sumar_total(this)' );
		$fieldset->addField( $field);
		
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_grupoD5", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_descripcionD5_1", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_puntajeD5_1", ""  ) );
		$field = FieldBuilder::buildFieldNumber( "", "nu_cantantproduccionD5_1","","",1 );
		$field->getInput()->addProperty( 'onChange', 'sumar_total(this)' );
		$fieldset->addField( $field);
		$field = FieldBuilder::buildFieldNumber( "", "nu_puntajeantproduccionD5_1","","",1 );
		$field->getInput()->addProperty( 'onChange', 'sumar_total(this)' );
		$fieldset->addField( $field);
		
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_descripcionD5_2", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_puntajeD5_2", ""  ) );
		$field = FieldBuilder::buildFieldNumber( "", "nu_cantantproduccionD5_2","","",1 );
		$field->getInput()->addProperty( 'onChange', 'sumar_total(this)' );
		$fieldset->addField( $field);
		$field = FieldBuilder::buildFieldNumber( "", "nu_puntajeantproduccionD5_2","","",1 );
		$field->getInput()->addProperty( 'onChange', 'sumar_total(this)' );
		$fieldset->addField( $field);
		
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_descripcionD5_3", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_puntajeD5_3", ""  ) );
		$field = FieldBuilder::buildFieldNumber( "", "nu_cantantproduccionD5_3","","",1 );
		$field->getInput()->addProperty( 'onChange', 'sumar_total(this)' );
		$fieldset->addField( $field);
		$field = FieldBuilder::buildFieldNumber( "", "nu_puntajeantproduccionD5_3","","",1 );
		$field->getInput()->addProperty( 'onChange', 'sumar_total(this)' );
		$fieldset->addField( $field);
		
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_grupoD6", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_descripcionD6_1", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_puntajeD6_1", ""  ) );
		$field = FieldBuilder::buildFieldNumber( "", "nu_puntajeantproduccionD6_1","","",1 );
		$field->getInput()->addProperty( 'onChange', 'sumar_total(this)' );
		$fieldset->addField( $field);
		
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_descripcionD6_2", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_puntajeD6_2", ""  ) );
		$field = FieldBuilder::buildFieldNumber( "", "nu_puntajeantproduccionD6_2","","",1 );
		$field->getInput()->addProperty( 'onChange', 'sumar_total(this)' );
		$fieldset->addField( $field);
		
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_descripcionD6_3", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_puntajeD6_3", ""  ) );
		$field = FieldBuilder::buildFieldNumber( "", "nu_puntajeantproduccionD6_3","","",1 );
		$field->getInput()->addProperty( 'onChange', 'sumar_total(this)' );
		$fieldset->addField( $field);
		
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_descripcionD6_4", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_puntajeD6_4", ""  ) );
		$field = FieldBuilder::buildFieldNumber( "", "nu_puntajeantproduccionD6_4","","",1 );
		$field->getInput()->addProperty( 'onChange', 'sumar_total(this)' );
		$fieldset->addField( $field);
		
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_grupoD7", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_topeD7", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_descripcionD7", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_puntajeD7", ""  ) );
		$field = FieldBuilder::buildFieldNumber( "", "nu_puntajeantproduccionD7","","",1 );
		$field->getInput()->addProperty( 'onChange', 'sumar_total(this)' );
		$fieldset->addField( $field);
		
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_grupoD8", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_descripcionD8", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_puntajeD8", ""  ) );
		$field = FieldBuilder::buildFieldCheckbox( "", "nu_puntajeantproduccionD8", "nu_puntajeantproduccionD8" );
		$field->getInput()->addProperty( 'onClick', 'sumar_total(this)' );
		$field->getInput()->setIsEditable(false);
		$fieldset->addField( $field);
		
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_antjustificacionmaximo", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_topeE1", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "ds_descripcionE1", ""  ) );
		$fieldset->addField( FieldBuilder::buildFieldReadOnly ( "", "nu_puntajeE1", ""  ) );
		$field = FieldBuilder::buildFieldNumber( "", "nu_puntajeantjustificacionE1","","",1 );
		$field->getInput()->addProperty( 'onChange', 'sumar_total(this)' );
		$fieldset->addField( $field);
		
		$this->addFieldset($fieldset);
	
		$this->addHidden( FieldBuilder::buildInputHidden ( "oid", "") );
		$this->addHidden( FieldBuilder::buildInputHidden ( "user.oid", "") );
		$this->addHidden( FieldBuilder::buildInputHidden ( "solicitud.oid", "") );
		$this->addHidden( FieldBuilder::buildInputHidden ( "nu_puntaje", "") );
		$this->addHidden( FieldBuilder::buildInputHidden ( "nu_puntajeantacadA4", "") );
		$this->addHidden( FieldBuilder::buildInputHidden ( "nu_puntajeantotrosC4", "") );
		$this->addHidden( FieldBuilder::buildInputHidden ( "nu_puntajeantproduccionD8", "") );
		$this->addHidden( FieldBuilder::buildInputHidden ( "cd_cargomaximo", "") );
		

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
