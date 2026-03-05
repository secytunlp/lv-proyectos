<?php

/**
 * ImplementaciÃ³n para renderizar un formulario de solicitud
 *
 * @author Marcos
 * @since 11-12-2013
 *
 */
class CMPSolicitudFormRenderer extends DefaultFormRenderer {

    protected function getXTemplate() {
        return new XTemplate( CYT_TEMPLATE_SOLICITUD_FORM );
    }


    protected function renderFieldset(CMPForm $form, XTemplate $xtpl){
        $xtpl->assign("titulo_domicilio", CYT_MSG_SOLICITUD_DOMICILIO_TITULO);
        $xtpl->assign("titulo_becario", CYT_MSG_SOLICITUD_BECARIO_TITULO);
        $xtpl->assign("titulo_carrera", CYT_MSG_SOLICITUD_CARRERA_TITULO);
        $xtpl->assign("titulo_proyectos", CYT_MSG_SOLICITUD_PROYECTOS_TITULO);
        $xtpl->assign("label_equivalencia_help", CYT_MSG_SOLICITUD_EQUIVALENCIA_HELP);
        //$xtpl->assign("titulo_tipo_investigador", CYT_MSG_SOLICITUD_TIPO_INVESTIGADOR_TITULO);
        foreach ($form->getFieldsets() as $fieldset) {

            //legend
            $legend = $fieldset->getLegend();
            if(!empty($legend)){
                $xtpl->assign("value", $legend);
                $xtpl->parse("main.fieldset.legend");
            }


            $fields = $fieldset->getFields();
            $fieldInvestigador = $fields['ds_investigador'];
            $input = $fieldInvestigador->getInput();
            $label = $fieldInvestigador->getLabel();
            $this->renderLabel( $label, $input, $xtpl );
            $this->renderInput( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldInvestigador->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.fieldset.column.ds_investigador");

            $fieldCUIL = $fields['nu_cuil'];
            $input = $fieldCUIL->getInput();
            $label = $fieldCUIL->getLabel();
            $this->renderLabel( $label, $input, $xtpl );
            $this->renderInput( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldCUIL->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.fieldset.column.nu_cuil");

            $xtpl->assign("value", CYT_LBL_SOLICITUD_FOTO );
            $xtpl->parse("main.fieldset.column.ds_foto.label");
            $xtpl->assign("actionFile", "doAction?action=add_file_session" );
            $xtpl->parse("main.fieldset.column.ds_foto.input");
            $xtpl->assign("display", 'block');
            $xtpl->assign("label_foto", CYT_LBL_SOLICITUD_FOTO_SPEECH);

            $hiddens = $form->getHiddens();
            $hiddenDs_foto = $hiddens['ds_foto'];

            if ($hiddenDs_foto->getInputValue()) {
                $dir = CYT_PATH_PDFS.'/';
                if (!file_exists($dir)) mkdir($dir, 0777);
                $dir .= CYT_PERIODO_YEAR.'/';
                if (!file_exists($dir)) mkdir($dir, 0777);
                //$oUser = CdtSecureUtils::getUserLogged();
                $separarCUIL = explode('-',trim($fieldCUIL->getInput()->getProperties()['value']));
                $dir .= $separarCUIL[1].'/';
                if (!file_exists($dir)) mkdir($dir, 0777);


                $xtpl->assign("ds_foto_cargado", '<img style="width:100px;border-radius:50%;" src="'.$dir.$hiddenDs_foto->getInputValue().'">');
            }
            else{
                $xtpl->assign("ds_foto_cargado", '<img style="width:100px;border-radius:50%;" src="css/images/user-icon-jpg-11.jpg">');
            }

            $xtpl->parse("main.fieldset.column.ds_foto");

            $xtpl->parse("main.fieldset.column");


            $xtpl->parse("main.fieldset");
            $xtpl->assign("domicilio_tab", CYT_MSG_SOLICITUD_TAB_DOMICILIO);
            $fieldCalle = $fields['ds_calle'];
            $input = $fieldCalle->getInput();
            $label = $fieldCalle->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldCalle->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.ds_calle");

            $fieldNro = $fields['nu_nro'];
            $input = $fieldNro->getInput();
            $label = $fieldNro->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldNro->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.nu_nro");

            $fieldPiso = $fields['nu_piso'];
            $input = $fieldPiso->getInput();
            $label = $fieldPiso->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldPiso->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.nu_piso");

            $fieldDepto = $fields['ds_depto'];
            $input = $fieldDepto->getInput();
            $label = $fieldDepto->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldDepto->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.ds_depto");

            $fieldCP = $fields['nu_cp'];
            $input = $fieldCP->getInput();
            $label = $fieldCP->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldCP->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.nu_cp");
            $fieldMail = $fields['ds_mail'];
            $input = $fieldMail->getInput();
            $label = $fieldMail->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldMail->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.ds_mail");
            $fieldNotificacion = $fields['bl_notificacion'];
            $input = $fieldNotificacion->getInput();
            $label = $fieldNotificacion->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldNotificacion->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.bl_notificacion");
            $fieldOtromail = $fields['ds_otromail'];
            $input = $fieldOtromail->getInput();
            $label = $fieldOtromail->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldOtromail->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.ds_otromail");
            $fieldGenero = $fields['ds_genero'];
            $input = $fieldGenero->getInput();
            $label = $fieldGenero->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldGenero->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.ds_genero");
            $fieldTelefono = $fields['nu_telefono'];
            $input = $fieldTelefono->getInput();
            $label = $fieldTelefono->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldTelefono->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.nu_telefono");

            $fieldNacimiento = $fields['dt_nacimiento'];
            $input = $fieldNacimiento->getInput();
            $label = $fieldNacimiento->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldNacimiento->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.dt_nacimiento");

            $fieldOrcid = $fields['ds_orcid'];
            $input = $fieldOrcid->getInput();
            $label = $fieldOrcid->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldOrcid->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.ds_orcid");
            $fieldSedici = $fields['ds_sedici'];
            $input = $fieldSedici->getInput();
            $label = $fieldSedici->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldSedici->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.ds_sedici");
            $fieldScholar = $fields['ds_scholar'];
            $input = $fieldScholar->getInput();
            $label = $fieldScholar->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldScholar->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.ds_scholar");

            $fieldScopus = $fields['ds_scopus'];
            $input = $fieldScopus->getInput();
            $label = $fieldScopus->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldScopus->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.ds_scopus");

            /*$fieldInstagram = $fields['ds_instagram'];
            $input = $fieldInstagram->getInput();
            $label = $fieldInstagram->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldInstagram->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.ds_instagram");
            $fieldTwitter = $fields['ds_twitter'];
            $input = $fieldTwitter->getInput();
            $label = $fieldTwitter->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldTwitter->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.ds_twitter");
            $fieldFacebook = $fields['ds_facebook'];
            $input = $fieldFacebook->getInput();
            $label = $fieldFacebook->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldFacebook->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.ds_facebook");*/

            $xtpl->assign("label_resguardo", CYT_LBL_SOLICITUD_RESGUARDO_SPEECH);

            $xtpl->assign("universidad_tab", CYT_MSG_SOLICITUD_TAB_UNIVERSIDAD);


            $field = $fields['solicitud_filter_titulo_oid'];
            $input = $field->getInput();
            $label = $field->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $field->getMinWidth());
            $xtpl->assign("input_help", CYT_MSG_SOLICITUD_TITULO_HELP);
            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.solicitud_filter_titulo_oid");
            $field = $fields['dt_egresogrado'];
            $input = $field->getInput();
            $label = $field->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $field->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.dt_egresogrado");

            $field = $fields['solicitud_filter_tituloposgrado_oid'];
            $input = $field->getInput();
            $label = $field->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $field->getMinWidth());
            $xtpl->assign("input_help", CYT_MSG_SOLICITUD_TITULO_POST_HELP);
            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.solicitud_filter_tituloposgrado_oid");
            $field = $fields['dt_egresoposgrado'];
            $input = $field->getInput();
            $label = $field->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $field->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.dt_egresoposgrado");


            $fieldLugarTrabajo = $fields['lugarTrabajo_oid'];
            $input = $fieldLugarTrabajo->getInput();
            $label = $fieldLugarTrabajo->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldLugarTrabajo->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.lugarTrabajo_oid");




            /*$fieldCargo = $fields['cargo_oid'];
            $input = $fieldCargo->getInput();
            $label = $fieldCargo->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldCargo->getMinWidth());
            $xtpl->assign("input_help", CYT_MSG_SOLICITUD_CARGO_HELP);
            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.cargo_oid");

            $fieldDedDoc = $fields['deddoc_oid'];
            $input = $fieldDedDoc->getInput();
            $label = $fieldDedDoc->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldDedDoc->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.deddoc_oid");

            $fieldFacultad = $fields['facultad_oid'];
            $input = $fieldFacultad->getInput();
            $label = $fieldFacultad->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldFacultad->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.facultad_oid");*/

            $HTMLCargos = $this->getHTMLCargos($form, $xtpl);
            $xtpl->assign("HTMLCargos", $HTMLCargos);

            //recuperamos las cargos de la unidad desde la sesiÃ³n.
            $manager = new SolicitudCargoSessionManager();
            $cargos = $manager->getEntities( new CdtSearchCriteria() );



            $fieldDisciplina = $fields['ds_disciplina'];
            $input = $fieldDisciplina->getInput();
            $label = $fieldDisciplina->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldDisciplina->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.ds_disciplina");



            $xtpl->assign("becario_tab", CYT_MSG_SOLICITUD_TAB_BECARIO);


            $fieldOrgBeca = $fields['ds_orgbeca'];
            $input = $fieldOrgBeca->getInput();
            $label = $fieldOrgBeca->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldOrgBeca->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.ds_orgbeca");

            $fieldTipoBeca = $fields['ds_tipobeca'];
            $input = $fieldTipoBeca->getInput();
            $label = $fieldTipoBeca->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldTipoBeca->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.ds_tipobeca");

            $field = $fields['dt_becadesde'];
            $input = $field->getInput();
            $label = $field->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $field->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.dt_becadesde");

            $field = $fields['dt_becahasta'];
            $input = $field->getInput();
            $label = $field->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $field->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.dt_becahasta");

            /*$fieldLugarTrabajoBeca = $fields['solicitud_filter_lugarTrabajoBeca_oid'];
            //CYTSecureUtils::logObject($fieldLugarTrabajoBeca);
            $input = $fieldLugarTrabajoBeca->getInput();
            $label = $fieldLugarTrabajoBeca->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldLugarTrabajoBeca->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.solicitud_filter_lugarTrabajoBeca_oid");*/

            $fieldLugartrabajobeca = $fields['lugarTrabajoBeca_oid'];
            $input = $fieldLugartrabajobeca->getInput();
            $label = $fieldLugartrabajobeca->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldLugartrabajobeca->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.lugarTrabajoBeca_oid");

            $xtpl->assign("value", CYT_LBL_SOLICITUD_BECA_RESOLUCION );

            $xtpl->parse("main.ds_resbeca.label");
            $xtpl->assign("actionFile", "doAction?action=add_file_session" );
            $xtpl->parse("main.ds_resbeca.input");
            $xtpl->assign("display", 'block');
            $xtpl->assign("label_resbeca", CYT_LBL_SOLICITUD_BECA_RESOLUCION_SPEECH);

            $hiddens = $form->getHiddens();
            $hiddenDs_resbeca = $hiddens['ds_resbeca'];

            if ($hiddenDs_resbeca->getInputValue()) {
                $xtpl->assign("ds_resbeca_cargado", '<span style="color:#009900; font-weight:bold">'.CYT_MSG_FILE_UPLOAD_EXITO.'</span>');
            }

            $xtpl->parse("main.ds_resbeca");




            $xtpl->assign("carrerainv_tab", CYT_MSG_SOLICITUD_TAB_CARRERAINV);

            $fieldInstitucion = $fields['organismo_oid'];
            $input = $fieldInstitucion->getInput();
            $label = $fieldInstitucion->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldInstitucion->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.organismo_oid");

            $fieldCarreraInv = $fields['carrerainv_oid'];
            $input = $fieldCarreraInv->getInput();
            $label = $fieldCarreraInv->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldCarreraInv->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.carrerainv_oid");

            $fieldIngreso = $fields['dt_ingreso'];
            $input = $fieldIngreso->getInput();
            $label = $fieldIngreso->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldIngreso->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.dt_ingreso");

            $fieldLugarTrabajoCarrerainv = $fields['lugarTrabajoCarrera_oid'];
            $input = $fieldLugarTrabajoCarrerainv->getInput();
            $label = $fieldLugarTrabajoCarrerainv->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldLugarTrabajoCarrerainv->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.lugarTrabajoCarrera_oid");

            $xtpl->assign("value", CYT_LBL_SOLICITUD_CARRERA_RESOLUCION );

            $xtpl->parse("main.ds_rescarrera.label");
            $xtpl->assign("actionFile", "doAction?action=add_file_session" );
            $xtpl->parse("main.ds_rescarrera.input");
            $xtpl->assign("display", 'block');
            $xtpl->assign("label_rescarrera", CYT_LBL_SOLICITUD_CARRERA_RESOLUCION_SPEECH);

            $hiddens = $form->getHiddens();
            $hiddenDs_rescarrera = $hiddens['ds_rescarrera'];

            if ($hiddenDs_rescarrera->getInputValue()) {
                $xtpl->assign("ds_rescarrera_cargado", '<span style="color:#009900; font-weight:bold">'.CYT_MSG_FILE_UPLOAD_EXITO.'</span>');
            }

            $xtpl->parse("main.ds_rescarrera");



            $xtpl->assign("proyectos_tab", CYT_MSG_SOLICITUD_TAB_PROYECTOS);

            $HTMLProyectos = $this->getHTMLProyectos($form, $xtpl);
            $xtpl->assign("HTMLProyectos", $HTMLProyectos);

            //recuperamos las proyectos de la unidad desde la sesiÃ³n.
            $manager = new SolicitudProyectoSessionManager();
            $proyectos = $manager->getEntities( new CdtSearchCriteria() );

            if ($proyectos->size()==0){
                $xtpl->assign('fieldsetopen', '<fieldset>' );
                $xtpl->assign('fieldsetclose', '</fieldset>' );
                $xtpl->assign('otrosProyectos_legend', '<legend>'.CYT_MSG_SOLICITUD_PROYECTOS_ANTERIORES_TITULO.'</legend>');

                $fieldOrganismoproyecto = $fields['ds_organismoproyecto'];
                $input = $fieldOrganismoproyecto->getInput();
                $label = $fieldOrganismoproyecto->getLabel();
                $this->renderLabelTab( $label, $input, $xtpl );
                $this->renderInputTab( $input, $xtpl );
                $xtpl->assign("minWidth", $fieldOrganismoproyecto->getMinWidth());

                if( $input->getIsVisible() ){
                    $xtpl->assign("display", 'block');

                }
                else $xtpl->assign("display", 'none');

                $xtpl->parse("main.ds_organismoproyecto");

                $fieldCodigoproyecto = $fields['ds_codigoproyecto'];
                $input = $fieldCodigoproyecto->getInput();
                $label = $fieldCodigoproyecto->getLabel();
                $this->renderLabelTab( $label, $input, $xtpl );
                $this->renderInputTab( $input, $xtpl );
                $xtpl->assign("minWidth", $fieldCodigoproyecto->getMinWidth());

                if( $input->getIsVisible() ){
                    $xtpl->assign("display", 'block');

                }
                else $xtpl->assign("display", 'none');

                $xtpl->parse("main.ds_codigoproyecto");

                $fieldDirectorproyecto = $fields['ds_directorproyecto'];
                $input = $fieldDirectorproyecto->getInput();
                $label = $fieldDirectorproyecto->getLabel();
                $this->renderLabelTab( $label, $input, $xtpl );
                $this->renderInputTab( $input, $xtpl );
                $xtpl->assign("minWidth", $fieldDirectorproyecto->getMinWidth());

                if( $input->getIsVisible() ){
                    $xtpl->assign("display", 'block');

                }
                else $xtpl->assign("display", 'none');

                $xtpl->parse("main.ds_directorproyecto");

                $fieldTituloproyecto = $fields['ds_tituloproyecto'];
                $input = $fieldTituloproyecto->getInput();
                $label = $fieldTituloproyecto->getLabel();
                $this->renderLabelTab( $label, $input, $xtpl );
                $this->renderInputTab( $input, $xtpl );
                $xtpl->assign("minWidth", $fieldTituloproyecto->getMinWidth());

                if( $input->getIsVisible() ){
                    $xtpl->assign("display", 'block');

                }
                else $xtpl->assign("display", 'none');

                $xtpl->parse("main.ds_tituloproyecto");



                $field = $fields['dt_proyectodesde'];
                $input = $field->getInput();
                $label = $field->getLabel();
                $this->renderLabelTab( $label, $input, $xtpl );
                $this->renderInputTab( $input, $xtpl );
                $xtpl->assign("minWidth", $field->getMinWidth());

                if( $input->getIsVisible() ){
                    $xtpl->assign("display", 'block');

                }
                else $xtpl->assign("display", 'none');

                $xtpl->parse("main.dt_proyectodesde");

                $field = $fields['dt_proyectohasta'];
                $input = $field->getInput();
                $label = $field->getLabel();
                $this->renderLabelTab( $label, $input, $xtpl );
                $this->renderInputTab( $input, $xtpl );
                $xtpl->assign("minWidth", $field->getMinWidth());

                if( $input->getIsVisible() ){
                    $xtpl->assign("display", 'block');

                }
                else $xtpl->assign("display", 'none');

                $xtpl->parse("main.dt_proyectohasta");

                $xtpl->assign("value", CYT_LBL_SOLICITUD_PROYECTOS_ARCHIVO );

                $xtpl->parse("main.ds_archivo.label");
                $xtpl->assign("actionFile", "doAction?action=add_file_session" );
                $xtpl->parse("main.ds_archivo.input");
                $xtpl->assign("display", 'block');
                $xtpl->assign("label_archivo", CYT_LBL_SOLICITUD_PROYECTOS_ARCHIVO_SPEECH);

                $hiddens = $form->getHiddens();
                $hiddenDs_archivo = $hiddens['ds_archivo'];

                if ($hiddenDs_archivo->getInputValue()) {
                    $xtpl->assign("ds_archivo_cargado", '<span style="color:#009900; font-weight:bold">'.CYT_MSG_FILE_UPLOAD_EXITO.'</span>');
                }

                $xtpl->parse("main.ds_archivo");
            }


            $xtpl->assign("descripcion_tab", CYT_MSG_SOLICITUD_TAB_DESCRIPCION);
            $fieldFacultadplanilla = $fields['facultadplanilla_oid'];
            $input = $fieldFacultadplanilla->getInput();
            $label = $fieldFacultadplanilla->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldFacultadplanilla->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.facultadplanilla_oid");
            $fieldCategoria = $fields['categoria_oid'];
            //CYTSecureUtils::logObject($fieldCategoria);
            $input = $fieldCategoria->getInput();
            $label = $fieldCategoria->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldCategoria->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.categoria_oid");

            $fieldEquivalencia = $fields['equivalencia_oid'];
            $input = $fieldEquivalencia->getInput();
            $label = $fieldEquivalencia->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldEquivalencia->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.equivalencia_oid");

            $fieldCategoriasicadi = $fields['categoriasicadi_oid'];
            //CYTSecureUtils::logObject($fieldCategoriasicadi);
            $input = $fieldCategoriasicadi->getInput();
            $label = $fieldCategoriasicadi->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldCategoriasicadi->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.categoriasicadi_oid");





            /*$xtpl->assign("value", CYT_LBL_SOLICITUD_A_CURRICULUM );
            $xtpl->assign("required", "*" );
            $xtpl->parse("main.ds_curriculum.label");
            $xtpl->assign("actionFile", "doAction?action=add_file_session" );
            $xtpl->parse("main.ds_curriculum.input");
            $xtpl->assign("display", 'block');
            $xtpl->assign("label_curriculum", CYT_LBL_SOLICITUD_A_CURRICULUM_SIGEVA);
            $hiddens = $form->getHiddens();
            $hiddenDs_curriculum = $hiddens['ds_curriculum'];

            if ($hiddenDs_curriculum->getInputValue()) {
                $xtpl->assign("ds_curriculum_cargado", '<span style="color:#009900; font-weight:bold">'.CYT_MSG_FILE_UPLOAD_EXITO.$hiddenDs_curriculum->getInputValue().'</span>');
            }
            $xtpl->parse("main.ds_curriculum");*/

            $fieldYear1 = $fields['nu_year1'];
            $input = $fieldYear1->getInput();
            $label = $fieldYear1->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldYear1->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.nu_year1");

            $xtpl->assign("value", CYT_LBL_SOLICITUD_A_INFORME1 );
            $xtpl->parse("main.ds_informe1.label");
            $xtpl->assign("actionFile", "doAction?action=add_file_session" );
            $xtpl->parse("main.ds_informe1.input");
            $xtpl->assign("display", 'block');
            //$xtpl->assign("label_informe1", CYT_LBL_SOLICITUD_A_INFORME1_SIGEVA);
            $hiddens = $form->getHiddens();
            $hiddenDs_informe1 = $hiddens['ds_informe1'];

            if ($hiddenDs_informe1->getInputValue()) {
                $xtpl->assign("ds_informe1_cargado", '<span style="color:#009900; font-weight:bold">'.CYT_MSG_FILE_UPLOAD_EXITO.'</span>');
            }
            $xtpl->parse("main.ds_informe1");

            $fieldYear2 = $fields['nu_year2'];
            $input = $fieldYear2->getInput();
            $label = $fieldYear2->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldYear2->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.nu_year2");

            $xtpl->assign("value", CYT_LBL_SOLICITUD_A_INFORME2 );
            $xtpl->parse("main.ds_informe2.label");
            $xtpl->assign("actionFile", "doAction?action=add_file_session" );
            $xtpl->parse("main.ds_informe2.input");
            $xtpl->assign("display", 'block');
            //$xtpl->assign("label_informe2", CYT_LBL_SOLICITUD_A_INFORME2_SIGEVA);
            $hiddens = $form->getHiddens();
            $hiddenDs_informe2 = $hiddens['ds_informe2'];

            if ($hiddenDs_informe2->getInputValue()) {
                $xtpl->assign("ds_informe2_cargado", '<span style="color:#009900; font-weight:bold">'.CYT_MSG_FILE_UPLOAD_EXITO.'</span>');
            }
            $xtpl->parse("main.ds_informe2");

            $fieldYear3 = $fields['nu_year3'];
            $input = $fieldYear3->getInput();
            $label = $fieldYear3->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldYear3->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.nu_year3");

            $xtpl->assign("value", CYT_LBL_SOLICITUD_A_INFORME3 );
            $xtpl->parse("main.ds_informe3.label");
            $xtpl->assign("actionFile", "doAction?action=add_file_session" );
            $xtpl->parse("main.ds_informe3.input");
            $xtpl->assign("display", 'block');
            //$xtpl->assign("label_informe3", CYT_LBL_SOLICITUD_A_INFORME3_SIGEVA);
            $hiddens = $form->getHiddens();
            $hiddenDs_informe3 = $hiddens['ds_informe3'];

            if ($hiddenDs_informe3->getInputValue()) {
                $xtpl->assign("ds_informe3_cargado", '<span style="color:#009900; font-weight:bold">'.CYT_MSG_FILE_UPLOAD_EXITO.'</span>');
            }
            $xtpl->parse("main.ds_informe3");

            $fieldAreacarrera = $fields['areacarrera_oid'];
            $input = $fieldAreacarrera->getInput();
            $label = $fieldAreacarrera->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldAreacarrera->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.areacarrera_oid");

            $fieldSubareacarrera = $fields['subareacarrera_oid'];
            $input = $fieldSubareacarrera->getInput();
            $label = $fieldSubareacarrera->getLabel();
            $this->renderLabelTab( $label, $input, $xtpl );
            $this->renderInputTab( $input, $xtpl );
            $xtpl->assign("minWidth", $fieldSubareacarrera->getMinWidth());

            if( $input->getIsVisible() ){
                $xtpl->assign("display", 'block');

            }
            else $xtpl->assign("display", 'none');

            $xtpl->parse("main.subareacarrera_oid");
        }
        $xtpl->assign( "customHTML",$form->getCustomHTML());
    }




    /**
     * renderizamos en el formulario de solicitud los proyectos que tiene en ejecucion.

     *
     * @param CMPForm $form
     * @param XTemplate $xtpl
     */
    protected function getHTMLProyectos(CMPForm $form, XTemplate $xtpl){

        $xtpl_proyectos = new XTemplate( CYT_TEMPLATE_SOLICITUD_EDIT_PROYECTOS );

        //mostrar las proyectos actuales.
        //$xtpl_proyectos->assign('proyectos_title', CYT_MSG_UNIDAD_FACULTAD );


        //recuperamos las proyectos de la unidad desde la sesiÃ³n.
        $manager = new SolicitudProyectoSessionManager();
        $proyectos = $manager->getEntities( new CdtSearchCriteria() );

        if ($proyectos->size()>0){
            //TODO parsear labels.
            $this->parseProyectosLabels($xtpl_proyectos);



            //parseamos los proyectos.
            $this->parseProyectos($proyectos, $xtpl_proyectos);
        }



        $xtpl_proyectos->parse("main");

        return $xtpl_proyectos->text("main");

    }

    /**
     * renderizamos en el formulario de solicitud los cargos que tiene en ejecucion.

     *
     * @param CMPForm $form
     * @param XTemplate $xtpl
     */
    protected function getHTMLCargos(CMPForm $form, XTemplate $xtpl){

        $xtpl_cargos = new XTemplate( CYT_TEMPLATE_SOLICITUD_EDIT_CARGOS );



        //TODO parsear labels.
        $this->parseCargosLabels($xtpl_cargos);

        //recuperamos las cargos de la unidad desde la sesiÃ³n.
        $manager = new SolicitudCargoSessionManager();
        $cargos = $manager->getEntities( new CdtSearchCriteria() );

        //parseamos los cargos.
        $this->parseCargos($cargos, $xtpl_cargos);


        $xtpl_cargos->parse("main");

        return $xtpl_cargos->text("main");

    }



    /**
     * renderizamos en el formulario de solicitud los otros proyectos.

     *
     * @param CMPForm $form
     * @param XTemplate $xtpl
     */
    protected function getHTMLOtrosProyectos(CMPForm $form){

        $xtpl = new XTemplate( CYT_TEMPLATE_SOLICITUD_EDIT_OTROSPROYECTOS );

        //mostrar las becas actuales.
        $xtpl->assign('otrosProyectos_title', CYT_MSG_SOLICITUD_PROYECTOS_ANTERIORES_TITULO );

        //TODO parsear labels.
        $this->parseOtrosProyectosLabels($xtpl);

        //recuperamos los proyectos de la solicitud desde la sesiÃ³n.
        $manager = new OtrosProyectoSessionManager();
        $proyectos = $manager->getEntities( new CdtSearchCriteria() );

        //parseamos los proyectos.
        $this->parseOtrosProyectos($proyectos, $form, $xtpl);

        //formulario para agregar un nuevo proyecto a la solicitud.
        if( $form->getIsEditable() ){
            $form = new CMPOtrosProyectoForm();
            $xtpl->assign('formulario', $form->show() );
        }
        $xtpl->parse("main");

        return $xtpl->text("main") ;

    }





    protected function renderLabel( $label, CMPFormInput $input, XTemplate $xtpl ){

        $xtpl->assign("value", $label );

        if( $input->getIsRequired() && $input->getIsEditable() ){
            $xtpl->assign("required", $input->getRequiredLabel() );
        }else{
            $xtpl->assign("required", "" );
        }

        $xtpl->assign("input_name", $input->getId() );
        $xtpl->parse("main.fieldset.column.".$input->getId().".label");
    }

    protected function renderInput( CMPFormInput $input, XTemplate $xtpl ){

        $xtpl->assign("input", $input->show() );

        $xtpl->parse("main.fieldset.column.".$input->getId().".input");

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

    /*protected function renderCustom(CMPForm $form, XTemplate $xtpl){

        //renderizamos las relaciones con sus formularios de alta

        $xtpl_relaciones = new XTemplate( CYT_TEMPLATE_SOLICITUD_EDIT_SOLICITUD_RELACIONES );

//proyectos anteriores
        $otrosProyectosHTML = $this->getHTMLotrosProyectos($form);
        $xtpl_relaciones->assign( "proyectos_tab", CYT_MSG_SOLICITUD_TAB_PROYECTOS_ANTERIORES );
        $xtpl_relaciones->assign( "proyectos", $otrosProyectosHTML );

        $xtpl_relaciones->parse("main");



        $xtpl->assign( "customHTML", $xtpl_relaciones->text("main").$form->getCustomHTML());
    }*/



    /**
     * armamos un array con los datos del proyecto.
     * @param Proyecto $solicitudProyecto
     */
    public function buildArrayProyecto($solicitudProyecto){
        //CYTSecureUtils::logObject($solicitudProyecto);
        $nombreDeClase = get_class($solicitudProyecto);

        $array_proyecto = array();

        $array_proyecto["item_oid"] = $solicitudProyecto->getOid();

        /*$oCriteria = new CdtSearchCriteria();
        $oCriteria->addFilter('cd_proyecto', $solicitudProyecto->getOid(), '=');
        $oCriteria->addFilter('DIR.cd_tipoinvestigador', CYT_INTEGRANTE_DIRECTOR, '=');
        $managerProyecto =  CYTSecureManagerFactory::getProyectoManager();
        $oProyecto = $managerProyecto->getEntity($oCriteria);*/
        $array_proyecto["ds_organismo"] = ($nombreDeClase=='ProyectoAgencia')?$solicitudProyecto->getDs_organismo():'UNLP';
        $array_proyecto["ds_codigo"] = $solicitudProyecto->getDs_codigo();
        $array_proyecto["ds_director"] = $solicitudProyecto->getDirector()->getDs_apellido().', '.$solicitudProyecto->getDirector()->getDs_nombre();
        $array_proyecto["ds_titulo"] = $solicitudProyecto->getDs_titulo();
        $array_proyecto["dt_inicio"] = CYTSecureUtils::formatDateToView($solicitudProyecto->getDt_ini());
        $array_proyecto["dt_fin"] = CYTSecureUtils::formatDateToView($solicitudProyecto->getDt_fin());
        //$array_proyecto["ds_estado"] = $solicitudProyecto->getTipoEstadoProyecto()->getDs_estado();
        //CYTSecureUtils::logObject($array_proyecto);
        return $array_proyecto;

    }
    /**
     * columnas para el listado de proyectos
     * @return multitype:string
     */
    public function getProyectoColumns(){
        return array( "ds_organismo","ds_codigo","ds_titulo","ds_director","dt_inicio","dt_fin");
    }

    /**
     * labels para el listado de proyectos
     * @return multitype:string
     */
    public function getProyectoColumnsLabels(){
        return array( CYT_LBL_SOLICITUD_PROYECTOS_ENTIDAD,CYT_LBL_SOLICITUD_PROYECTOS_CODIGO,CYT_LBL_SOLICITUD_PROYECTOS_TITULO,CYT_LBL_SOLICITUD_PROYECTOS_DIRECTOR,CYT_LBL_SOLICITUD_PROYECTOS_INICIO,CYT_LBL_SOLICITUD_PROYECTOS_FIN);
    }

    /**
     * aligns para las columnas del listado de facultades.
     * @return multitype:string
     */
    public function getProyectoColumnsAlign(){
        return array( "left","center","left","left","center","center");
    }

    /**
     * parseamos los labels para el listado de proyectos.
     * @param XTemplate $xtpl_facultades
     */
    protected function parseProyectosLabels(XTemplate $xtpl_proyectos){

        $aligns = $this->getProyectoColumnsAlign();

        $index=0;
        foreach ( $this->getProyectoColumnsLabels() as $label) {

            $xtpl_proyectos->assign('proyecto_label', $label );
            $xtpl_proyectos->assign('align', $aligns[$index]);
            $xtpl_proyectos->parse('main.proyecto_th');

            $index++;
        }
    }


    /**
     * parseamos el listado de proyectos.
     * @param ItemCollection $proyectos
     * @param CMPForm $form
     * @param XTemplate $xtpl_proyectos
     */
    protected function parseProyectos(ItemCollection $proyectos=null, XTemplate $xtpl_proyectos){

        if( $proyectos!= null ){
            foreach ($proyectos as $proyecto) {

                $this->parseProyecto($proyecto, $xtpl_proyectos);

                /*if( $form->getIsEditable() ){
                    $xtpl_proyectos->assign('item_oid', $proyecto->getFacultad()->getOid() );
                    $xtpl_proyectos->parse("main.proyecto.editar_proyecto");
                }*/

                $xtpl_proyectos->parse("main.proyecto");
            }
        }
    }

    /**
     * parseamos un proyecto.
     * @param UnidadFacultad $proyecto
     * @param XTemplate $xtpl_proyectos
     */
    protected function parseProyecto($solicitudProyecto, XTemplate $xtpl_proyectos){

        $columns = $this->getProyectoColumns();
        $aligns = $this->getProyectoColumnsAlign();
        $array_proyecto = $this->buildArrayProyecto($solicitudProyecto);

        $index=0;
        foreach ($columns as $column) {

            $xtpl_proyectos->assign('data', $array_proyecto[$column] );
            $xtpl_proyectos->assign('align', $aligns[$index]);
            $xtpl_proyectos->parse('main.proyecto.proyecto_data');

            $index++;
        }

    }

    /**
     * armamos un array con los datos de la beca.
     * @param JovenesBeca $beca
     */
    public function buildArrayBeca(JovenesBeca $beca){

        $array_beca = array();

        $array_beca["item_oid"] = $beca->getDs_tipobeca();
        $array_beca["ds_tipobeca"] = $beca->getDs_tipobeca();
        $array_beca["bl_unlp"] = ($beca->getBl_unlp())?CDT_UI_LBL_YES:CDT_UI_LBL_NO;
        $array_beca["dt_desde"] = CYTSecureUtils::formatDateToView($beca->getDt_desde());
        $array_beca["dt_hasta"] = CYTSecureUtils::formatDateToView($beca->getDt_hasta());
        $array_beca["bl_agregado"] = $beca->getBl_agregado();

        return $array_beca;

    }


    /**
     * columnas para el listado de becas
     * @return multitype:string
     */
    public function getBecaColumns(){
        return array( "ds_tipobeca","bl_unlp","dt_desde","dt_hasta");
    }

    /**
     * labels para el listado de becas
     * @return multitype:string
     */
    public function getBecaColumnsLabels(){
        return array( CYT_LBL_SOLICITUD_BECA_NIVEL, CYT_LBL_SOLICITUD_BECARIO_UNLP,CYT_LBL_SOLICITUD_BECA_DESDE,CYT_LBL_SOLICITUD_BECA_HASTA);
    }

    /**
     * aligns para las columnas del listado de becas.
     * @return multitype:string
     */
    public function getBecaColumnsAlign(){
        return array( "left", "center","left","left");
    }

    /**
     * parseamos los labels para el listado de becas.
     * @param XTemplate $xtpl_becas
     */
    protected function parseBecasLabels(XTemplate $xtpl_becas){

        $aligns = $this->getBecaColumnsAlign();

        $index=0;
        foreach ( $this->getBecaColumnsLabels() as $label) {

            $xtpl_becas->assign('beca_label', $label );
            $xtpl_becas->assign('align', $aligns[$index]);
            $xtpl_becas->parse('main.beca_th');

            $index++;
        }
    }

    /**
     * armamos un array con los datos del proyecto.
     * @param OtrosProyecto $proyecto
     */
    public function buildArrayOtrosProyecto(OtrosProyecto $proyecto){

        //CYTSecureUtils::logObject($proyecto);
        $array_proyecto = array();

        $array_proyecto["item_oid"] = $proyecto->getDs_codigo();
        $array_proyecto["cd_proyecto"] = $proyecto->getProyecto()->getOid();
        $array_proyecto["ds_codigo"] = $proyecto->getDs_codigo();
        $array_proyecto["ds_titulo"] = $proyecto->getDs_titulo();
        $array_proyecto["ds_director"] = $proyecto->getDs_director();
        $array_proyecto["ds_organismo"] = $proyecto->getDs_organismo();
        $array_proyecto["dt_desdeproyecto"] = CYTSecureUtils::formatDateToView($proyecto->getDt_desdeproyecto());
        $array_proyecto["dt_hastaproyecto"] = CYTSecureUtils::formatDateToView($proyecto->getDt_hastaproyecto());
        $array_proyecto["bl_agregado"] = $proyecto->getBl_agregado();

        return $array_proyecto;

    }
    /**
     * columnas para el listado de proyectos
     * @return multitype:string
     */
    public function getOtrosProyectoColumns(){
        return array( "ds_codigo","ds_titulo","ds_director","ds_organismo","dt_desdeproyecto","dt_hastaproyecto");
    }

    /**
     * labels para el listado de proyectos anteriores
     * @return multitype:string
     */
    public function getOtrosProyectoColumnsLabels(){
        return array( CYT_LBL_SOLICITUD_PROYECTOS_CODIGO, CYT_LBL_SOLICITUD_PROYECTOS_TITULO,CYT_LBL_SOLICITUD_PROYECTOS_DIRECTOR,CYT_LBL_SOLICITUD_ORGANISMO_BECA,CYT_LBL_SOLICITUD_PROYECTOS_INICIO,CYT_LBL_SOLICITUD_PROYECTOS_FIN);
    }

    /**
     * aligns para las columnas del listado de proyectos anteriores.
     * @return multitype:string
     */
    public function getOtrosProyectoColumnsAlign(){
        return array( "left", "Left","left","left","left","left");
    }

    /**
     * parseamos los labels para el listado de los otros proyectos.
     * @param XTemplate $xtpl_becas
     */
    protected function parseOtrosProyectosLabels(XTemplate $xtpl_otrosProyectos){

        $aligns = $this->getOtrosProyectoColumnsAlign();

        $index=0;
        foreach ( $this->getOtrosProyectoColumnsLabels() as $label) {

            $xtpl_otrosProyectos->assign('otrosProyecto_label', $label );
            $xtpl_otrosProyectos->assign('align', $aligns[$index]);
            $xtpl_otrosProyectos->parse('main.otrosProyecto_th');

            $index++;
        }
    }



    /**
     * parseamos el listado de otrosProyectos.
     * @param ItemCollection $otrosProyectos
     * @param CMPForm $form
     * @param XTemplate $xtpl_otrosProyectos
     */
    protected function parseOtrosProyectos(ItemCollection $otrosProyectos=null, CMPForm $form, XTemplate $xtpl_otrosProyectos){

        if( $otrosProyectos!= null ){
            foreach ($otrosProyectos as $otrosProyecto) {

                $this->parseOtrosProyecto($otrosProyecto, $xtpl_otrosProyectos);

                if( $form->getIsEditable()&& ($otrosProyecto->getBl_agregado()) ){
                    $xtpl_otrosProyectos->assign('item_oid',$otrosProyecto->getDs_codigo());
                    $xtpl_otrosProyectos->parse("main.otrosProyecto.editar_otrosProyecto");
                }

                $xtpl_otrosProyectos->parse("main.otrosProyecto");
            }
        }
    }

    /**
     * parseamos un proyecto.
     * @param OtrosProyecto $otrosProyecto
     * @param XTemplate $xtpl_otrosProyectos
     */
    protected function parseOtrosProyecto(OtrosProyecto $otrosProyecto, XTemplate $xtpl_otrosProyectos){

        $columns = $this->getOtrosProyectoColumns();
        $aligns = $this->getOtrosProyectoColumnsAlign();
        $array_otrosProyecto = $this->buildArrayOtrosProyecto($otrosProyecto);

        $index=0;
        foreach ($columns as $column) {

            $xtpl_otrosProyectos->assign('data', $array_otrosProyecto[$column] );
            $xtpl_otrosProyectos->assign('align', $aligns[$index]);
            $xtpl_otrosProyectos->parse('main.otrosProyecto.otrosProyecto_data');

            $index++;
        }

    }

    /**
     * armamos un array con los datos del cargo.
     * @param Cargo $solicitudCargo
     */
    public function buildArrayCargo($solicitudCargo){

        $array_cargo = array();

        $array_cargo["item_oid"] = $solicitudCargo->getOid();



        $array_cargo["ds_cargo"] = str_replace(" Ordinario","",$solicitudCargo->getCargo()->getDs_cargo());
        $array_cargo["ds_deddoc"] = $solicitudCargo->getDeddoc()->getDs_deddoc();
        $array_cargo["ds_facultad"] = $solicitudCargo->getFacultad()->getDs_facultad();

        $array_cargo["dt_fecha"] = CYTSecureUtils::formatDateToView($solicitudCargo->getDt_fecha());
        $array_cargo["situacion"] = $solicitudCargo->getSituacion();

        return $array_cargo;

    }
    /**
     * columnas para el listado de cargos
     * @return multitype:string
     */
    public function getCargoColumns(){
        return array( "ds_cargo","ds_deddoc","ds_facultad","dt_fecha","situacion");
    }

    /**
     * labels para el listado de cargos
     * @return multitype:string
     */
    public function getCargoColumnsLabels(){
        return array( CYT_LBL_SOLICITUD_CARGO,CYT_LBL_SOLICITUD_DEDICACION,CYT_LBL_SOLICITUD_FACULTAD,CYT_LBL_SOLICITUD_FECHA,CYT_LBL_SOLICITUD_CARGO_SITUACION);
    }

    /**
     * aligns para las columnas del listado de facultades.
     * @return multitype:string
     */
    public function getCargoColumnsAlign(){
        return array( "left","left","left","center","left");
    }

    /**
     * parseamos los labels para el listado de cargos.
     * @param XTemplate $xtpl_facultades
     */
    protected function parseCargosLabels(XTemplate $xtpl_cargos){

        $aligns = $this->getCargoColumnsAlign();

        $index=0;
        foreach ( $this->getCargoColumnsLabels() as $label) {

            $xtpl_cargos->assign('cargo_label', $label );
            $xtpl_cargos->assign('align', $aligns[$index]);
            $xtpl_cargos->parse('main.cargo_th');

            $index++;
        }
    }


    /**
     * parseamos el listado de cargos.
     * @param ItemCollection $cargos
     * @param CMPForm $form
     * @param XTemplate $xtpl_cargos
     */
    protected function parseCargos(ItemCollection $cargos=null, XTemplate $xtpl_cargos){

        if( $cargos!= null ){
            foreach ($cargos as $cargo) {

                //CYTSecureUtils::logObject($cargo);
                $this->parseCargo($cargo, $xtpl_cargos);

                /*if( $form->getIsEditable() ){
                    $xtpl_cargos->assign('item_oid', $cargo->getFacultad()->getOid() );
                    $xtpl_cargos->parse("main.cargo.editar_cargo");
                }*/

                $xtpl_cargos->parse("main.cargo");
            }
        }
    }

    /**
     * parseamos un cargo.
     * @param UnidadFacultad $cargo
     * @param XTemplate $xtpl_cargos
     */
    protected function parseCargo($solicitudCargo, XTemplate $xtpl_cargos){

        $columns = $this->getCargoColumns();
        $aligns = $this->getCargoColumnsAlign();
        $array_cargo = $this->buildArrayCargo($solicitudCargo);

        $index=0;
        foreach ($columns as $column) {

            $xtpl_cargos->assign('data', $array_cargo[$column] );
            $xtpl_cargos->assign('align', $aligns[$index]);
            $xtpl_cargos->parse('main.cargo.cargo_data');

            $index++;
        }

    }


}