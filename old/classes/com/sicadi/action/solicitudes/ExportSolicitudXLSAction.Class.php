<?php

/**
 * Acción para exportar solicitudes a xls
 *
 * @author Marcos
 * @since 10-06-2014
 *
 */
class ExportSolicitudXLSAction extends CdtAction{


     public function execute(){
          //CdtDbManager::begin_tran();

         $layout =  new CdtLayoutXls();
         $nombre = date(CYT_DATETIME_FORMAT_STRING).'_'.CYT_LBL_SOLICITUD_XLS_NOMBRE;
         $layout->setFilename($nombre);

         try{
			$html = "<table><tr><th rowspan='5'><img src='".WEB_PATH."css/smile/images/sicadi_little.png' alt='logo' style='width: 10px; height: 10px;'></th></tr></table><table border=1'><tr><th>".CYT_LBL_SOLICITUD_PERIODO."</th><th>".CYT_LBL_SOLICITUD_SOLICITANTE."</th><th>".CYT_LBL_SOLICITUD_CUIL."</th><th>".CYT_LBL_SOLICITUD_MAIL."</th><th>".CYT_LBL_SOLICITUD_ESTADO."</th><th>".CYT_LBL_SOLICITUD_FACULTAD."</th><th>".CYT_LBL_SOLICITUD_CATEGORIA."</th><th>".CYT_LBL_SOLICITUD_EQUIVALENCIA."</th><th>".CYT_LBL_SOLICITUD_CATEGORIA_SOLICITADA."</th><th>".CYT_MSG_SOLICITUD_PDF_HEADER_TITLE."</th></tr>";
			$filtro = new CMPSolicitudFilter();
			$filtro->fillSavedProperties();			
		
			$oCriteria = new CdtSearchCriteria();
			$oCriteria->addOrder('cd_solicitud','DESC');
			$oCriteria->setPage(1);
			$oCriteria->setRowPerPage(3000);
			
			$solicitante = $filtro->getSolicitante();

			if(!empty($solicitante)){
				$tDocente = CYTSecureDAOFactory::getDocenteDAO()->getTableName();
				/*$filter = new CdtSimpleExpression( "(($tDocente.ds_nombre like '$solicitante%') OR ($tDocente.ds_apellido like '$solicitante%'))");
	
				$oCriteria->setExpresion($filter);*/
				$filterExpression = "(($tDocente.ds_nombre like ? OR ($tDocente.ds_apellido like ?))";

			// Agregar el filtro al criterio con los valores de marcador de posici�n correspondientes
			$criteria->addFilterWithPlaceholders($filterExpression, ["$solicitante%", "$solicitante%"]);
			}
			
			
			$facultad = $filtro->getFacultad();
			if($facultad!=null && $facultad->getOid()!=null){
				$oCriteria->addFilter("cd_facultadplanilla", $facultad->getOid(), "=" );
			}
			
			$estado = $filtro->getEstado();
			if($estado!=null && $estado->getOid()!=null){
				$tSolicitudEstado = CYTSecureDAOFactory::getSolicitudEstadoDAO()->getTableName();
				$oCriteria->addFilter("$tSolicitudEstado.estado_oid", $estado->getOid(), "=" );
			}

			$periodo = $filtro->getPeriodo();
			if($periodo!=null && $periodo->getOid()!=null){
				$oCriteria->addFilter("cd_periodo", $filtro->getPeriodo()->getOid(), "=" );
				
			}
			
			
			
			
	
			$oUser = CdtSecureUtils::getUserLogged();
			
			if ($oUser->getCd_usergroup()==CYT_CD_GROUP_SOLICITANTE) {
	            $separarCUIL = explode('-',trim($oUser->getDs_username()));
	            $oCriteria = new CdtSearchCriteria();
				$oCriteria->addFilter('nu_documento', $separarCUIL[1], '=');
				
				$oDocenteManager =  CYTSecureManagerFactory::getDocenteManager();
				$oDocente = $oDocenteManager->getEntity($oCriteria);
	            $oCriteria->addFilter("cd_docente", $oDocente->getOid(), "=");
	        }
	        if ($oUser->getCd_usergroup()==CYT_CD_GROUP_EVALUADOR) {
	        	$oCriteria = new CdtSearchCriteria();
				$oCriteria->addFilter('cd_usuario',$oUser->getCd_user(), '=');
				$oCriteria->addNull('fechaHasta');
				$tEvaluacionEstado = CYTSecureDAOFactory::getEvaluacionEstadoDAO()->getTableName();
				$filter = new CdtSimpleExpression( "(".$tEvaluacionEstado.".estado_oid =".CYT_ESTADO_SOLICITUD_RECIBIDA." OR ".$tEvaluacionEstado.".estado_oid =".CYT_ESTADO_SOLICITUD_EN_EVALUACION." OR ".$tEvaluacionEstado.".estado_oid =".CYT_ESTADO_SOLICITUD_EVALUADA.")");

				$oCriteria->setExpresion($filter);
				
				$oEvaluacionManager =  ManagerFactory::getEvaluacionManager();
				$oEvaluaciones = $oEvaluacionManager->getEntities($oCriteria);
				$evaluaciones = '';
				foreach ($oEvaluaciones as $oEvaluacion) {
					$evaluaciones .= $oEvaluacion->getSolicitud()->getOid().',';
				}
				
				if (($evaluaciones!='')) {
					
					$evaluaciones = substr( $evaluaciones, 0, strlen($evaluaciones)-1); //se le quita la última , (coma)
				}
				else{
					$evaluaciones = 0;
				}
				
				$tSolicitud = DAOFactory::getSolicitudDAO()->getTableName();
				$filter = new CdtSimpleExpression( "$tSolicitud.cd_solicitud IN (".$evaluaciones.")");
	
				$oCriteria->setExpresion($filter);
	        	
	        }
             if ($oUser->getCd_usergroup()==19) {
                 $userManager = CYTSecureManagerFactory::getUserManager();
                 $oUsuario = $userManager->getObjectByCode($oUser->getCd_user());
                 //print_r($oUsuario);

                 $oCriteria->addFilter("cd_facultadPlanilla", $oUsuario->getFacultad()->getOid(), "=");


             }
			
			
			$oCriteria->addNull('fechaHasta');
			
			$managerSolicitud =  ManagerFactory::getSolicitudManager();
			$solicitudes = $managerSolicitud->getEntities($oCriteria);
			$cant=0;
			
			foreach ($solicitudes as $oSolicitud) {
				$fecha = CYTSecureUtils::formatDateTimeToView($oSolicitud->getDt_fecha());
				//$edad = CYTSecureUtils::edad(CYT_PERIODO_YEAR.CYT_DIA_MES_EDAD,CYTSecureUtils::formatDateToPersist($oSolicitud->getDt_nacimiento()));
				
				
				/*$oCriteria = new CdtSearchCriteria();
				$oCriteria->addFilter('cd_solicitud', $oSolicitud->getOid(), '=');
				$proyectosManager = ManagerFactory::getSolicitudProyectoManager();
				$oProyectos= $proyectosManager->getEntities($oCriteria);
				$proyectos = '';
				foreach ($oProyectos as $oProyecto) {
					if ($oProyecto->getProyecto()->getOid()) {
						$oCriteriaIntegrante = new CdtSearchCriteria();
						$oCriteriaIntegrante->addFilter("cd_tipoinvestigador", CYT_INTEGRANTE_CODIRECTOR, '=');
						$oCriteriaIntegrante->addFilter("cd_proyecto", $oProyecto->getProyecto()->getOid(), '=');
						$integrantesManager = CYTSecureManagerFactory::getIntegranteManager();
						$oCoDirector = $integrantesManager->getEntity($oCriteriaIntegrante);
					}
					
					
					$cordir = ($oCoDirector)?' CODIR: '.$oCoDirector->getDocente()->getDs_apellido().', '.$oCoDirector->getDocente()->getDs_nombre():'';
					
					$proyectos .= $oProyecto->getProyecto()->getDs_codigo().' DIR: '.$oProyecto->getDs_director().$cordir.' ('.CYTSecureUtils::formatDateToView($oProyecto->getDt_desdeproyecto()).'-'.CYTSecureUtils::formatDateToView($oProyecto->getDt_hastaproyecto()).') '.CYT_LBL_SOLICITUD_PROYECTOS_ESPECIALIDAD.': '.$oProyecto->getProyecto()->getDisciplina()->getDs_disciplina().'/'.$oProyecto->getProyecto()->getEspecialidad()->getDs_especialidad().'---';
				}
				
				
				

				$managerEvaluacion = ManagerFactory::getEvaluacionManager();

				
				$oCriteria = new CdtSearchCriteria();
				$oCriteria->addFilter('cd_solicitud', $oSolicitud->getOid(), '=');
				$oCriteria->addNull('fechaHasta');
				

				$oEvaluaciones = $managerEvaluacion->getEntities($oCriteria);
				$count=1;
				$evals = '';
				foreach ($oEvaluaciones as $oEval) {

					$strInterno = ($oEval->getBl_interno())?'Interno':'Externo';
					$evals .= $oEval->getUser()->getDs_username().' / '.$strInterno.' / '.$oEval->getEstado()->getDs_estado().' / P. '.number_format ( $oEval->getNu_puntaje() , CYT_DECIMALES , CYT_SEPARADOR_DECIMAL, CYT_SEPARADOR_MILES ).'---';
				}*/
				

				
				$html .= "<tr><td>".$oSolicitud->getPeriodo()->getDs_periodo()."</td><td>".$oSolicitud->getDocente()->getDs_apellido().', '.$oSolicitud->getDocente()->getDs_nombre()."</td><td>".$oSolicitud->getDocente()->getNu_precuil().'-'.$oSolicitud->getDocente()->getNu_documento().'-'.$oSolicitud->getDocente()->getNu_postcuil()."</td><td>".$oSolicitud->getDs_mail()."</td><td>".$oSolicitud->getEstado()->getDs_estado()."</td><td>".$oSolicitud->getFacultadplanilla()->getDs_facultad()."</td><td>".$oSolicitud->getCategoria()->getDs_categoria()."</td><td>".$oSolicitud->getEquivalencia()->getDs_equivalencia()."</td><td>".$oSolicitud->getCategoriasicadi()->getDs_categoriasicadi()."</td><td>'".$oSolicitud->getDt_fecha()."</td></tr>";
				$cant++;

				
			}
           // $html .= "<tr><td colspan='5'></td></tr><tr><td colspan='5'>".CYT_LBL_PRESUPUESTO_TOTAL.": ".$cant."</td></tr></table>";
             

             //armamos el layout.

             $layout->setContent(CdtUIUtils::encodeCharactersXls($html));
             $layout->setTitle(CYT_LBL_SOLICITUD_XLS_NOMBRE);

             CdtDbManager::save();

         }catch(GenericException $ex){
             //capturamos la excepción y la parseamos en el layout.
             $layout->setException( $ex );
             CdtDbManager::undo();
         }

         //mostramos la salida formada por el layout.
         echo $layout->show();

         //no hay forward.
         $forward = null;

         return $forward;
     }

}
