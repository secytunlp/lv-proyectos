<?php

/**
 * GridModel para Solicitud
 *
 * @author Marcos
 * @since 12-11-2013
 */
class SolicitudGridModel extends GridModel {

	public function __construct() {

		parent::__construct();
		$this->initModel();

	}

	protected function initModel() {

		
		
		$column = GridModelBuilder::buildColumn( "oid", CYT_LBL_ENTITY_OID, 20, CDT_CMP_GRID_TEXTALIGN_RIGHT );
		$this->addColumn( $column );
				
		$tPeriodo = CYTSecureDAOFactory::getPeriodoDAO()->getTableName();
        $column = GridModelBuilder::buildColumn( "periodo.ds_periodo", CYT_LBL_PERIODO, 20, CDT_CMP_GRID_TEXTALIGN_LEFT, "$tPeriodo.ds_periodo" );
		$this->addColumn( $column );
		
		
		$tDocente = CYTSecureDAOFactory::getDocenteDAO()->getTableName();
		$column = GridModelBuilder::buildColumn( "docente.ds_apellido,docente.ds_nombre", CYT_LBL_SOLICITUD_SOLICITANTE, 50, CDT_CMP_GRID_TEXTALIGN_LEFT, "$tDocente.ds_apellido,$tDocente.ds_nombre" ) ;
		$this->addColumn( $column );
		
		$column = GridModelBuilder::buildColumn( "dt_fecha", CYT_LBL_SOLICITUD_FECHA, 30, CDT_CMP_GRID_TEXTALIGN_CENTER, null, new GridDateValueFormat(CYT_DATETIME_FORMAT) ); 
		$this->addColumn( $column );
		
		$tEstado = CYTSecureDAOFactory::getEstadoDAO()->getTableName();
        $column = GridModelBuilder::buildColumn( "estado.ds_estado", CYT_LBL_ESTADO, 40, CDT_CMP_GRID_TEXTALIGN_LEFT, "$tEstado.ds_estado" );
		$this->addColumn( $column );
		
		/*$tCat = CYTSecureDAOFactory::getCatDAO()->getTableName();
        $column = GridModelBuilder::buildColumn( "cat.ds_cat", CYT_LBL_CAT, 40, CDT_CMP_GRID_TEXTALIGN_LEFT, "$tCat.ds_cat" );
		$this->addColumn( $column );*/
		
		//$tFacultad = CYTSecureDAOFactory::getFacultadDAO()->getTableName();
        $column = GridModelBuilder::buildColumn( "FacultadPlanilla.ds_facultad", CYT_LBL_FACULTAD, 40, CDT_CMP_GRID_TEXTALIGN_LEFT, "FacultadPlanilla.ds_facultad" );
		$this->addColumn( $column );

        $column = GridModelBuilder::buildColumn( "Categoria.ds_categoria", CYT_LBL_SOLICITUD_CATEGORIA, 40, CDT_CMP_GRID_TEXTALIGN_CENTER, "Categoria.ds_categoria" );
        $this->addColumn( $column );

        $column = GridModelBuilder::buildColumn( "Equivalencia.ds_equivalencia", CYT_LBL_SOLICITUD_EQUIVALENCIA, 40, CDT_CMP_GRID_TEXTALIGN_LEFT, "Equivalencia.ds_equivalencia" );
        $this->addColumn( $column );

        $tCategoriasicadi = DAOFactory::getCategoriasicadiDAO()->getTableName();
        $column = GridModelBuilder::buildColumn( "Categoriasicadi.ds_categoriasicadi", CYT_LBL_SOLICITUD_CATEGORIA_SOLICITADA, 40, CDT_CMP_GRID_TEXTALIGN_CENTER, "$tCategoriasicadi.ds_categoriasicadi" );
        $this->addColumn( $column );
		
		
		
		$column = GridModelBuilder::buildColumn( "oid", CYT_LBL_SOLICITUD_OBSEVACIONES, 80, CDT_CMP_GRID_TEXTALIGN_LEFT,"",new GridObservacionesValueFormat() ) ;
		$this->addColumn( $column );
		$oUser = CdtSecureUtils::getUserLogged();
		
		if (CdtSecureUtils::hasPermission ( $oUser, CYT_FUNCTION_VER_PUNTAJE )) {
			/*$column = GridModelBuilder::buildColumn( "oid", CYT_LBL_SOLICITUD_EVALUADORES, 40, CDT_CMP_GRID_TEXTALIGN_LEFT,"",new GridEvaluacionValueFormat(1) ) ;
			$this->addColumn( $column );*/
			/*$column = GridModelBuilder::buildColumn( "oid", CYT_LBL_SOLICITUD_PRIMER_EVALUADOR, 40, CDT_CMP_GRID_TEXTALIGN_LEFT,"",new GridEvaluacionValueFormat(1) ) ;
			$this->addColumn( $column );
			$column = GridModelBuilder::buildColumn( "oid", CYT_LBL_SOLICITUD_SEGUNDO_EVALUADOR, 40, CDT_CMP_GRID_TEXTALIGN_LEFT,"",new GridEvaluacionValueFormat(2) ) ;
			$this->addColumn( $column );
			$column = GridModelBuilder::buildColumn( "oid", CYT_LBL_SOLICITUD_TERCER_EVALUADOR, 40, CDT_CMP_GRID_TEXTALIGN_LEFT,"",new GridEvaluacionValueFormat(3) ) ;
			$this->addColumn( $column );*/
			/*$column = GridModelBuilder::buildColumn( "nu_diferencia", CYT_LBL_SOLICITUD_DIFERENCIA, 20, CDT_CMP_GRID_TEXTALIGN_RIGHT,"",new GridDiferenciaValueFormat() ) ;
			$this->addColumn( $column );
			$column = GridModelBuilder::buildColumn( "nu_puntaje", CYT_LBL_SOLICITUD_PUNTAJE, 20, CDT_CMP_GRID_TEXTALIGN_RIGHT,"",new GridNumeroValueFormat() ) ;
			$this->addColumn( $column );*/
			$this->buildAction( "export_solicitud_xls", "xls", CDT_UI_LBL_EXPORT_XLS, "image", "excel", false, "delete_items('export_solicitud_xls')" );
			//$this->buildExportXlsAction(CMPSolicitudGrid);
		}
		$column = GridModelBuilder::buildColumn( "oid", CYT_LBL_SOLICITUD_ARCHIVOS, 60, CDT_CMP_GRID_TEXTALIGN_RIGHT,"",new GridFilesValueFormat() ) ;
		$this->addColumn( $column );



		if (CdtSecureUtils::hasPermission ( $oUser, CYT_FUNCTION_AGREGAR_SOLICITUD )) {
			//acciones sobre la lista
			$this->buildAction("add_solicitud_init", "add_solicitud_init", CYT_MSG_SOLICITUD_TITLE_ADD, "image", "add");
		}
        else{
            $this->buildAction( "export_solicitud_xls", "xls", CDT_UI_LBL_EXPORT_XLS, "image", "excel", false, "delete_items('export_solicitud_xls')" );
        }
	}

	/**
	 * (non-PHPdoc)
	 * @see GridModel::getTitle();
	 */
	function getTitle() {
		return CYT_MSG_SOLICITUD_TITLE_LIST;
	}

	/**
	 * (non-PHPdoc)
	 * @see GridModel::getEntityManager();
	 */
	public function getEntityManager() {
		return ManagerFactory::getSolicitudManager();
	}

	/**
	 * (non-PHPdoc)
	 * @see GridModel::getRowActionsModel( $item );
	 */
	public function getRowActionsModel($item) {
		//return $this->getDefaultRowActions($item, "unidad", CYT_LBL_UNIDAD, true, true, true, false, 500, 750);
		$actions = new ItemCollection();
	
		/*$oUser = CdtSecureUtils::getUserLogged();
		if ($oUser->getCd_usergroup()!=CDT_SECURE_USERGROUP_DEFAULT_ID) {
			$action = $this->buildViewAction( $item, "unidad", CYT_LBL_UNIDAD) ;
			//$actions->addItem( $this->buildViewAction( "view_unidad", "view_unidad", CDT_CMP_GRID_MSG_VIEW . " ".CPIQ_LBL_UNIDAD) );
			$actions->addItem( $action );
		}*/
		$oUser = CdtSecureUtils::getUserLogged();

        if (CdtSecureUtils::hasPermission ( $oUser, CYT_FUNCTION_AGREGAR_SOLICITUD )) {
			$action = $this->buildRowAction( "update_solicitud_init", "update_solicitud_init", CDT_CMP_GRID_MSG_EDIT . " ".CYT_LBL_SOLICITUD, CDT_UI_IMG_EDIT, "edit") ;
			$actions->addItem( $action );
		}

        if (CdtSecureUtils::hasPermission ( $oUser, CYT_FUNCTION_AGREGAR_SOLICITUD )) {
			$action =  $this->buildDeleteAction( $item, "solicitud", CYT_LBL_SOLICITUD, $this->getMsgConfirmDelete( $item ), false ) ;
			$actions->addItem( $action );
		}
		
		/*$action = $this->buildRowAction("list_integrantes", "list_integrantes", CYT_MSG_INTEGRANTE_TITLE_LIST, CDT_CMP_GRID_MSG_VIEWCDT_UI_IMG_SEARCH, "attach" ) ;
		$actions->addItem( $action );*/
		
		$action = $this->buildRowAction( "view_solicitud_pdf", "view_solicitud_pdf", CDT_UI_LBL_EXPORT_PDF, CDT_UI_IMG_SEARCH, "view") ;
		$action->setBl_targetblank(true);
		$actions->addItem( $action );
		
		
		
		if (CdtSecureUtils::hasPermission ( $oUser, CYT_FUNCTION_ENVIAR_SOLICITUD )) {
			$action =  $this->buildRowAction( "send_solicitud", "send_solicitud", CYT_LBL_ENVIAR, CDT_UI_IMG_SEARCH, "view", "delete_items('send_solicitud')", false, $this->getMsgConfirmSend(CYT_MSG_SOLICITUD_ENVIAR_PREGUNTA)) ;
			$actions->addItem( $action );
		}

        if (CdtSecureUtils::hasPermission ( $oUser, CYT_FUNCTION_ENVIO_DEFINITIVO)) {
            $action =  $this->buildRowAction( "envio_definitivo", "envio_definitivo", CYT_LBL_ENVIO_DEFINITIVO, CDT_UI_IMG_SEARCH, "view", "delete_items('envio_definitivo')", true, $this->getMsgConfirmSendFinal()) ;
            $actions->addItem( $action );
        }

        if (CdtSecureUtils::hasPermission ( $oUser, CYT_FUNCTION_ENVIO_DEFINITIVO )) {

            $action =  $this->buildRowAction( "rectify_solicitud_init", "rectify_solicitud_init", CYT_LBL_RECTIFICAR, CDT_UI_IMG_SEARCH, "view") ;
            $actions->addItem( $action );
        }


		if (CdtSecureUtils::hasPermission ( $oUser, CYT_FUNCTION_ADMITIR_SOLICITUD )) {
			$action =  $this->buildRowAction( "admit_solicitud", "admit_solicitud", CYT_LBL_ADMITIR, CDT_UI_IMG_SEARCH, "view", "delete_items('admit_solicitud')", true, $this->getMsgConfirmAdmit()) ;
			$actions->addItem( $action );
		}
		
		if (CdtSecureUtils::hasPermission ( $oUser, CYT_FUNCTION_RECHAZAR_SOLICITUD )) {
			$action =  $this->buildRowAction( "deny_solicitud_init", "deny_solicitud_init", CYT_LBL_RECHAZAR, CDT_UI_IMG_SEARCH, "view") ;
			$actions->addItem( $action );
		}
		
		if (CdtSecureUtils::hasPermission ( $oUser, CYT_FUNCTION_LISTAR_ESTADO )) {
			
			$action = $this->buildRowAction("list_solicitudesEstado", "list_solicitudesEstado", CYT_MSG_SOLICITUD_ESTADO_TITLE_LIST, CDT_UI_IMG_SEARCH, "attach" ) ;
			$actions->addItem( $action );
			
		}
		if ((CdtSecureUtils::hasPermission ( $oUser, CYT_FUNCTION_VER_EVALUACION )) && (!CdtSecureUtils::hasPermission ( $oUser, CYT_FUNCTION_EVALUAR_SOLICITUD ))) {
			/*$action = $this->buildRowAction("list_evaluaciones", "list_evaluaciones", CYT_MSG_EVALUACION_TITLE_LIST, CDT_CMP_GRID_MSG_VIEWCDT_UI_IMG_SEARCH, "attach" ) ;
			$actions->addItem( $action );*/
			/*$action = $this->buildRowAction( "view_evaluacion_pdf", "view_evaluacion_pdf", CYT_LBL_EVALUACION_VIEW, CDT_UI_IMG_SEARCH, "view") ;
			$action->setBl_targetblank(true);
			$actions->addItem( $action );*/
		}
		
		/*if (CdtSecureUtils::hasPermission ( $oUser, CYT_FUNCTION_EVALUAR_SOLICITUD )) {
			$action = $this->buildRowAction( "admit_evaluacion", "admit_evaluacion", CYT_LBL_ACEPTAR, CDT_UI_IMG_SEARCH, "view", "delete_items('admit_evaluacion')", true, $this->getMsgConfirmAdmit()) ;
			$actions->addItem( $action );
			$action = $this->buildRowAction( "deny_evaluacion_init", "deny_evaluacion_init", CYT_LBL_RECHAZAR, CDT_UI_IMG_SEARCH, "view") ;
			$actions->addItem( $action );
			$action = $this->buildRowAction( "evaluar_solicitud_init", "evaluar_solicitud_init", CYT_LBL_EVALUAR . " ".CYT_LBL_SOLICITUD, CDT_UI_IMG_EDIT, "edit") ;
			$actions->addItem( $action );
			$action = $this->buildRowAction( "view_evaluacion_pdf", "view_evaluacion_pdf", CYT_LBL_EVALUACION_VIEW, CDT_UI_IMG_SEARCH, "view") ;
			$action->setBl_targetblank(true);
			$actions->addItem( $action );
			$action =  $this->buildRowAction( "send_evaluacion", "send_evaluacion", CYT_LBL_EVALUACION_SEND, CDT_UI_IMG_SEARCH, "view", "delete_items('send_evaluacion')", false, $this->getMsgConfirmSend(CYT_MSG_EVALUACION_ENVIAR_PREGUNTA)) ;
			$actions->addItem( $action );
		}*/
		
		return $actions;
	}


	protected function getMsgConfirmSend( $msg ){
		
		return CdtFormatUtils::quitarEnters($msg);
	}
	
	protected function getMsgConfirmAdmit(  ){
		
		$msg = CYT_MSG_SOLICITUD_ADMITIR_PREGUNTA;
		return CdtFormatUtils::quitarEnters($msg);
	}

    protected function getMsgConfirmSendFinal(  ){

        $msg = CYT_MSG_SOLICITUD_SEND_FINAL_PREGUNTA;
        return CdtFormatUtils::quitarEnters($msg);
    }

    protected function getMsgConfirmRectify(  ){

        $msg = CYT_MSG_SOLICITUD_RECTIFY_PREGUNTA;
        return CdtFormatUtils::quitarEnters($msg);
    }
}
?>