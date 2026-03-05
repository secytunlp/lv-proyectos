<?php

/**
 * AcciÃ³n para inicializar el contexto
 * para editar una solicitud.
 *
 * @author Marcos
 * @since 11-12-2013
 *
 */

class AddSolicitudInitAction extends EditEntityInitAction {

	/**
	 * (non-PHPdoc)
	 * @see classes/com/gestion/action/entities/EditEntityInitAction::getNewFormInstance()
	 */
	public function getNewFormInstance($action){
		$form = new CMPSolicitudForm($action);
		// Genera un nuevo token CSRF y guárdalo en una variable de sesión
		
		$_SESSION[APP_NAME]['csrf_token'] = bin2hex(random_bytes(32)); 
		$bl_notificacion = $form->getInput("bl_notificacion");
		$bl_notificacion->setIsChecked(true);
		return $form;
		
	}
	
 	

	/**
	 * (non-PHPdoc)
	 * @see classes/com/gestion/action/entities/EditEntityInitAction::getNewEntityInstance()
	 */
	public function getNewEntityInstance(){
		if (date('Y-m-d')>CYT_FECHA_CIERRE) {
			throw new GenericException( CYT_MSG_FIN_PERIODO );
		}
		
		$oSolicitud = new Solicitud();
		$oUser = CdtSecureUtils::getUserLogged();
		
		if ($oUser->getCd_usergroup()==CYT_CD_GROUP_SOLICITANTE) {
            $separarCUIL = explode('-',trim($oUser->getDs_username()));
            $oCriteria = new CdtSearchCriteria();
			$oCriteria->addFilter('nu_documento', $separarCUIL[1], '=');
			
			$oDocenteManager =  CYTSecureManagerFactory::getDocenteManager();
			$oDocente = $oDocenteManager->getEntity($oCriteria);
           
			$oCriteria = new CdtSearchCriteria();
			$tDocente = CYTSecureDAOFactory::getDocenteDAO()->getTableName();
			$oCriteria->addFilter("$tDocente.cd_docente", $oDocente->getOid(), '=');
			
			$oPeriodoManager = CYTSecureManagerFactory::getPeriodoManager();
			$oPerioActual = $oPeriodoManager->getPeriodoActual(CYT_PERIODO_YEAR);
			$tPeriodo = CYTSecureDAOFactory::getPeriodoDAO()->getTableName();
			$oCriteria->addFilter("$tPeriodo.cd_periodo", $oPerioActual->getOid(), '=');
			
			$oSolicitudManager =  ManagerFactory::getSolicitudManager();
			$oSolicitudAux = $oSolicitudManager->getEntity($oCriteria);
			
			if(!empty($oSolicitudAux)){
				throw new GenericException( CYT_MSG_SOLICITUD_CREADA );
			}
			
			$error = '';
			
			$oCriteria = new CdtSearchCriteria();
			$tDocente = CYTSecureDAOFactory::getDocenteDAO()->getTableName();
			$oCriteria->addFilter("$tDocente.nu_documento", $oDocente->getNu_documento(), '=');
			
			/*$oNoRendidasManager =  CYTSecureManagerFactory::getNoRendidasManager();
			$oNoRendidas = $oNoRendidasManager->getEntities($oCriteria);
			foreach ($oNoRendidas as $oNoRendida) {
				$error .= CYT_MSG_SOLICITUD_NO_RENDIDAS.$oNoRendida->getNu_year()."<br>";
			}
			
			$twoYear = intval(CYT_PERIODO_YEAR)-2;
			
			for ($i = $twoYear; $i > CYT_PERIODO_INICIAL; $i--) {
				
				$oCriteria = new CdtSearchCriteria();
				$oCriteria->addFilter("ds_periodo", $i, "=", new CdtCriteriaFormatStringValue());
				$oPeriodoManager = CYTSecureManagerFactory::getPeriodoManager();
				$oPeriodoAnterior = $oPeriodoManager->getEntity($oCriteria);
				
				if ($oPeriodoAnterior) {
					$oCriteria = new CdtSearchCriteria();
					$tDocente = CYTSecureDAOFactory::getDocenteDAO()->getTableName();
					$oCriteria->addFilter("$tDocente.cd_docente", $oDocente->getOid(), '=');
					
					
					$tPeriodo = CYTSecureDAOFactory::getPeriodoDAO()->getTableName();
					$oCriteria->addFilter("$tPeriodo.cd_periodo", $oPeriodoAnterior->getOid(), '=');
					
					$oSolicitudManager =  ManagerFactory::getSolicitudManager();
					$oSolicitudAnterior = $oSolicitudManager->getEntity($oCriteria);
					
					if ($oSolicitudAnterior) {
						$oCriteria = new CdtSearchCriteria();
						$oCriteria->addFilter('solicitud_oid', $oSolicitudAnterior->getOid(), '=');
						$oCriteria->addNull('fechaHasta');
						$managerSolicitudEstado =  CYTSecureManagerFactory::getSolicitudEstadoManager();
						$oSolicitudEstado = $managerSolicitudEstado->getEntity($oCriteria);
						if (($oSolicitudEstado->getEstado()->getOid()==CYT_ESTADO_SOLICITUD_OTORGADA)) {
							$error .= CYT_MSG_SOLICITUD_NO_RENDIDAS.$i."<br>";
							
						}
					}	
				}
				
				
				
			}*/
			
			if($error){
				throw new GenericException( $error );
			}
			
            $oSolicitud->setDs_investigador($oDocente->getDs_apellido().', '.$oDocente->getDs_nombre());
            $oSolicitud->setNu_cuil($oUser->getDs_username());
            $oSolicitud->setDs_calle($oDocente->getDs_calle());
            $oSolicitud->setNu_nro($oDocente->getNu_nro());
            $oSolicitud->setNu_piso($oDocente->getNu_piso());
            $oSolicitud->setDs_depto($oDocente->getDs_depto());
            $oSolicitud->setNu_cp($oDocente->getNu_cp());
            $oSolicitud->setDs_mail($oDocente->getDs_mail());
            $oSolicitud->setNu_telefono($oDocente->getNu_telefono());
            
            /*$oCriteria = new CdtSearchCriteria();
			$oCriteria->addFilter('cd_titulo', $oDocente->getTitulo()->getOid(), '=');
            $oTituloManager =  CYTSecureManagerFactory::getTituloManager();
			$oTitulo = $oTituloManager->getEntity($oCriteria);
			$oSolicitud->setDs_titulogrado($oTitulo->getDs_titulo());*/
			$oSolicitud->setTitulo($oDocente->getTitulo());
			$oSolicitud->setTituloposgrado($oDocente->getTitulopost());
			
			if ($oDocente->getLugarTrabajo()->getOid()) {
				$oCriteria = new CdtSearchCriteria();
				$oCriteria->addFilter('cd_unidad', $oDocente->getLugarTrabajo()->getOid(), '=');
	            $oLugarTrabajoManager =  CYTSecureManagerFactory::getLugarTrabajoManager();
				$oLugarTrabajo = $oLugarTrabajoManager->getEntity($oCriteria);
				$oSolicitud->setLugarTrabajo($oDocente->getLugarTrabajo());
			}
			
			
			$oSolicitud->setCargo($oDocente->getCargo());
			$oSolicitud->setDeddoc($oDocente->getDeddoc());
			$oSolicitud->setFacultad($oDocente->getFacultad());
			$oSolicitud->setCategoria($oDocente->getCategoria());
			
			$oCriteria = new CdtSearchCriteria();
			$oCriteria->addFilter('cd_docente', $oDocente->getOid(), '=');
			$oCriteria->addFilter('dt_hasta', CYT_PERIODO_YEAR.CYT_DIA_MES_BECA, '>', new CdtCriteriaFormatStringValue());
			$oBecaManager =  CYTSecureManagerFactory::getBecaManager();
			$oBeca = $oBecaManager->getEntity($oCriteria);
			if (!empty($oBeca)) {
				$oSolicitud->setBl_unlp(($oBeca->getBl_unlp()?1:0));
				$oSolicitud->setDs_orgbeca(($oBeca->getBl_unlp()?'UNLP':''));
                $ds_tipobeca = $oBeca->getDs_tipobeca();
                switch ($oBeca->getDs_tipobeca()) {
                    case 'Doctorado':
                        $ds_tipobeca = 'Beca doctoral';
                        break;
                    case 'TIPO B (DOCTORADO)':
                        $ds_tipobeca = 'TIPO B-DOCTORADO';
                        break;
                    case 'TIPO B (MAESTRÃ­A)':
                        $ds_tipobeca = 'TIPO B-MAESTRIA';
                        break;
                    case 'Cofinanciadas CIC-UNLP':
                        $ds_tipobeca = 'Beca Cofinanciada (UNLP-CIC)';
                        break;
                    case 'MaestrÃ­a':
                        $ds_tipobeca = 'Beca maestria';
                        break;
                    case 'Postdoctorado':
                        $ds_tipobeca = 'Beca posdoctoral';
                        break;
                    case 'RETENCION DE POSTGRADUADO':
                        $ds_tipobeca = 'Programa de retencion de Doctores';
                        break;
                }
				$oSolicitud->setDs_tipobeca($ds_tipobeca);
				$oSolicitud->setDt_becadesde($oBeca->getDt_desde());
				$oSolicitud->setDt_becahasta($oBeca->getDt_hasta());
			}
			$oSolicitud->setOrganismo($oDocente->getOrganismo());
			$oSolicitud->setCarrerainv($oDocente->getCarrerainv());

            $oCriteria = new CdtSearchCriteria();
            $oCriteria->addFilter('dni', $oDocente->getNu_documento(), '=');
            $oCriteria->addFilter('escalafon', 'Docente', '=', new CdtCriteriaFormatStringValue());
            /*$filter = new CdtSimpleExpression("(clase in ('05X','05S','05E','06X','06S','06E','07X','07S','07E','08X','08S','08E','09X','09S','09E') AND (situacion IN ('Trabajando','Licencia por maternidad')))");
            $oCriteria->setExpresion($filter);*/
			$filter = "(clase IN (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) AND (situacion IN (?, ?)))";
			$values = ['05X','05S','05E','06X','06S','06E','07X','07S','07E','08X','08S','08E','09X','09S','09E', 'Trabajando', 'Licencia por maternidad'];

			$oCriteria->addFilterWithPlaceholders($filter, $values);
            $oCargoManager =  ManagerFactory::getAlfabeticoManager();
            $oCargos = $oCargoManager->getEntities($oCriteria);
            $cargosArray = new ItemCollection();
            foreach ($oCargos as $oCargo) {


                $cargosArray->addItem($oCargo);
            }
            $oSolicitud->setCargos( $cargosArray );

            $manager = new SolicitudCargoSessionManager();
            $manager->setEntities( $oSolicitud->getCargos() );


			$oCriteria = new CdtSearchCriteria();
			$tDocente = CYTSecureDAOFactory::getDocenteDAO()->getTableName();
			$tIntegrante = CYTSecureDAOFactory::getIntegranteDAO()->getTableName();
			$tProyecto = CYTSecureDAOFactory::getProyectoDAO()->getTableName();
			$oCriteria->addFilter("$tDocente.cd_docente", $oDocente->getOid(), '=');
			$oCriteria->addFilter('DIR.cd_tipoinvestigador', CYT_INTEGRANTE_DIRECTOR, '=');
			$oCriteria->addFilter("$tIntegrante.cd_tipoinvestigador", CYT_INTEGRANTE_COLABORADOR, '<>');
			//$oCriteria->addFilter("$tIntegrante.cd_estado", CYT_ESTADO_INTEGRANTE_ADMITIDO, '=');
			
			//$filter = new CdtSimpleExpression("(".$tProyecto.".cd_estado =".CYT_ESTADO_PROYECTO_ADMITIDO." OR ".$tProyecto.".cd_estado=".CYT_ESTADO_PROYECTO_ACREDITADO." OR ".$tProyecto.".cd_estado=".CYT_ESTADO_PROYECTO_EN_EVALUACION." OR ".$tProyecto.".cd_estado=".CYT_ESTADO_PROYECTO_EVALUADO.") AND (".$tIntegrante.".dt_baja > '".date('Y-m-d')."' OR ".$tIntegrante.".dt_baja IS NULL OR ".$tIntegrante.".dt_baja = '0000-00-00')");
			//$filter = new CdtSimpleExpression("(".$tProyecto.".cd_estado=".CYT_ESTADO_PROYECTO_ACREDITADO.") AND (".$tIntegrante.".cd_estado = ".CYT_ESTADO_INTEGRANTE_ADMITIDO." OR ".$tIntegrante.".cd_estado = ".CYT_ESTADO_INTEGRANTE_BAJA_CREADA." OR ".$tIntegrante.".cd_estado = ".CYT_ESTADO_INTEGRANTE_BAJA_RECIBIDA." OR ".$tIntegrante.".cd_estado = ".CYT_ESTADO_INTEGRANTE_CAMBIO_HS_CREADO." OR ".$tIntegrante.".cd_estado = ".CYT_ESTADO_INTEGRANTE_CAMBIO_HS_RECIBIDO.") AND ((".$tIntegrante.".cd_estado != ".CYT_ESTADO_INTEGRANTE_BAJA_CREADA." AND ".$tIntegrante.".cd_estado != ".CYT_ESTADO_INTEGRANTE_BAJA_RECIBIDA." AND ".$tIntegrante.".dt_baja > '".date('Y-m-d')."') OR ".$tIntegrante.".cd_estado = ".CYT_ESTADO_INTEGRANTE_BAJA_CREADA." OR ".$tIntegrante.".cd_estado = ".CYT_ESTADO_INTEGRANTE_BAJA_RECIBIDA." OR ".$tIntegrante.".dt_baja IS NULL OR ".$tIntegrante.".dt_baja = '0000-00-00')");
			//quitar esta linea y poner la de arriba (con esta se muestran lo sproyectos en evaluacion)
			/*$filter = new CdtSimpleExpression("(".$tProyecto.".cd_estado=".CYT_ESTADO_PROYECTO_ACREDITADO." OR ".$tProyecto.".cd_estado=".CYT_ESTADO_PROYECTO_EN_EVALUACION.") AND (".$tIntegrante.".cd_estado = ".CYT_ESTADO_INTEGRANTE_ADMITIDO." OR ".$tIntegrante.".cd_estado = ".CYT_ESTADO_INTEGRANTE_BAJA_CREADA." OR ".$tIntegrante.".cd_estado = ".CYT_ESTADO_INTEGRANTE_BAJA_RECIBIDA." OR ".$tIntegrante.".cd_estado = ".CYT_ESTADO_INTEGRANTE_CAMBIO_HS_CREADO." OR ".$tIntegrante.".cd_estado = ".CYT_ESTADO_INTEGRANTE_CAMBIO_HS_RECIBIDO.") AND ((".$tIntegrante.".cd_estado != ".CYT_ESTADO_INTEGRANTE_BAJA_CREADA." AND ".$tIntegrante.".cd_estado != ".CYT_ESTADO_INTEGRANTE_BAJA_RECIBIDA." AND ".$tIntegrante.".dt_baja > '".date('Y-m-d')."') OR ".$tIntegrante.".cd_estado = ".CYT_ESTADO_INTEGRANTE_BAJA_CREADA." OR ".$tIntegrante.".cd_estado = ".CYT_ESTADO_INTEGRANTE_BAJA_RECIBIDA." OR ".$tIntegrante.".dt_baja IS NULL OR ".$tIntegrante.".dt_baja = '0000-00-00')");
			$oCriteria->setExpresion($filter);*/

			$filter = "(
				($tProyecto.cd_estado = ? OR $tProyecto.cd_estado = ?) 
				AND (
					$tIntegrante.cd_estado = ? 
					OR $tIntegrante.cd_estado = ? 
					OR $tIntegrante.cd_estado = ? 
					OR $tIntegrante.cd_estado = ? 
					OR $tIntegrante.cd_estado = ?
				) 
				AND (
					(
						$tIntegrante.cd_estado != ? 
						AND $tIntegrante.cd_estado != ? 
						AND $tIntegrante.dt_baja > ?
					) 
					OR $tIntegrante.cd_estado = ? 
					OR $tIntegrante.cd_estado = ? 
					OR $tIntegrante.dt_baja IS NULL 
					OR $tIntegrante.dt_baja = '0000-00-00'
				)
			)";

			$values = [
				CYT_ESTADO_PROYECTO_ACREDITADO,
				CYT_ESTADO_PROYECTO_EN_EVALUACION,
				CYT_ESTADO_INTEGRANTE_ADMITIDO,
				CYT_ESTADO_INTEGRANTE_BAJA_CREADA,
				CYT_ESTADO_INTEGRANTE_BAJA_RECIBIDA,
				CYT_ESTADO_INTEGRANTE_CAMBIO_HS_CREADO,
				CYT_ESTADO_INTEGRANTE_CAMBIO_HS_RECIBIDO,
				CYT_ESTADO_INTEGRANTE_BAJA_CREADA,
				CYT_ESTADO_INTEGRANTE_BAJA_RECIBIDA,
				date('Y-m-d'),
				CYT_ESTADO_INTEGRANTE_BAJA_CREADA,
				CYT_ESTADO_INTEGRANTE_BAJA_RECIBIDA,
			];

			$oCriteria->addFilterWithPlaceholders($filter, $values);


			$oneYearAgo = intval(CYT_PERIODO_YEAR)-1;
			$oCriteria->addFilter('dt_fin', $oneYearAgo.CYT_DIA_MES_PROYECTO_FIN, '>', new CdtCriteriaFormatStringValue());
			
			//proyectos.
			$proyectosManager = CYTSecureManagerFactory::getProyectoManager();
			$proyectos = $proyectosManager->getEntities($oCriteria);
			$proyectosArray = new ItemCollection();
			foreach ($proyectos as $oProyecto) {
				$oCriteriaIntegrante = new CdtSearchCriteria();
				$oCriteriaIntegrante->addFilter("$tDocente.cd_docente", $oDocente->getOid(), '=');
				$oCriteriaIntegrante->addFilter("cd_proyecto", $oProyecto->getOid(), '=');
				$integrantesManager = CYTSecureManagerFactory::getIntegranteManager();
				$oIntegrante = $integrantesManager->getEntity($oCriteriaIntegrante);
				$oProyecto->setDt_ini($oIntegrante->getDt_alta());
				$dt_hasta = (($oIntegrante->getDt_baja()=='0000-00-00')||($oIntegrante->getDt_baja()=='')||(!$oIntegrante->getDt_baja())||($oIntegrante->getCd_estado()==CYT_ESTADO_INTEGRANTE_BAJA_CREADA)||($oIntegrante->getCd_estado()==CYT_ESTADO_INTEGRANTE_BAJA_RECIBIDA))?$oProyecto->getDt_fin():$oIntegrante->getDt_baja();
				$oProyecto->setDt_fin($dt_hasta);
                $oProyecto->setDt_fin($dt_hasta);
				$proyectosArray->addItem($oProyecto);
			}
			$oSolicitud->setProyectos( $proyectosArray );

            $oCriteria = new CdtSearchCriteria();
            $tIntegrante = DAOFactory::getIntegranteAgenciaDAO()->getTableName();
            $oCriteria->addFilter("$tIntegrante.nu_documento", $oDocente->getNu_documento(), '=');
            $oCriteria->addFilter('DIR.cd_tipoinvestigador', CYT_INTEGRANTE_DIRECTOR, '=');

            $oCriteria->addFilter('dt_fin', $oneYearAgo.CYT_DIA_MES_PROYECTO_FIN, '>', new CdtCriteriaFormatStringValue());

            //proyectos.
            $proyectosManager = ManagerFactory::getProyectoAgenciaManager();
            $proyectos = $proyectosManager->getEntities($oCriteria);

            //$proyectosArray = new ItemCollection();
            foreach ($proyectos as $oProyecto) {
                CDTUtils::log('Proyecto:');
                CYTSecureUtils::logObject($oProyecto);
                $oCriteriaIntegrante = new CdtSearchCriteria();
                $oCriteriaIntegrante->addFilter("$tIntegrante.nu_documento", $oDocente->getNu_documento(), '=');
                $oCriteriaIntegrante->addFilter("cd_proyecto", $oProyecto->getOid(), '=');
                $integrantesManager = ManagerFactory::getIntegranteAgenciaManager();
                $oIntegrante = $integrantesManager->getEntity($oCriteriaIntegrante);
                $oProyecto->setDt_ini($oIntegrante->getDt_alta());
                $dt_hasta = (($oIntegrante->getDt_baja()=='0000-00-00')||($oIntegrante->getDt_baja()=='')||(!$oIntegrante->getDt_baja()))?$oProyecto->getDt_fin():$oIntegrante->getDt_baja();
                $oProyecto->setDt_fin($dt_hasta);
                //CYTSecureUtils::logObject($oProyecto);
                $proyectosArray->addItem($oProyecto);
            }
            $oSolicitud->setProyectos( $proyectosArray );


            /*$proyectoActual = 0;
            if($oSolicitud->getProyectos()->size()){
                    $proyectoActual=1;
            }


            if(!$oSolicitud->getBl_unlp() && !$proyectoActual){
                throw new GenericException( CYT_MSG_SOLICITUD_SIN_PROYECTO_ACTUAL );
            }*/
			
			$manager = new SolicitudProyectoSessionManager();
			$manager->setEntities( $oSolicitud->getProyectos() );
            //$manager->setEntities( $oSolicitud->getProyectosAgencia() );
			
			//fue director.
			/*$oCriteria = new CdtSearchCriteria();
			$tDocente = CYTSecureDAOFactory::getDocenteDAO()->getTableName();
			$tIntegrante = CYTSecureDAOFactory::getIntegranteDAO()->getTableName();
			$tProyecto = CYTSecureDAOFactory::getProyectoDAO()->getTableName();
			$oCriteria->addFilter("$tDocente.cd_docente", $oDocente->getOid(), '=');
			$oCriteria->addFilter("$tIntegrante.cd_estado", CYT_ESTADO_INTEGRANTE_ADMITIDO, '=');
			$oCriteria->addFilter("$tProyecto.cd_estado", CYT_ESTADO_PROYECTO_ACREDITADO, '=');
			$oCriteria->addFilter("$tProyecto.cd_tipoacreditacion", CYT_TIPO_ACREDITACION_ID, '=');
			
			$filter = new CdtSimpleExpression("(".$tIntegrante.".cd_tipoinvestigador = '".CYT_INTEGRANTE_DIRECTOR."' OR ".$tIntegrante.".cd_tipoinvestigador = '".CYT_INTEGRANTE_CODIRECTOR."')");
			$oCriteria->setExpresion($filter);
			
			
			
			$proyectosManager = CYTSecureManagerFactory::getProyectoManager();
			$proyectosDirigidos = $proyectosManager->getEntities($oCriteria);
			$oSolicitud->setBl_director($proyectosDirigidos->size()?1:0);
			
			if($oSolicitud->getBl_director()){
				throw new GenericException( CYT_MSG_SOLICITUD_FUE_DIRCODIR );
			}*/
			
			//becas.
			/*$oCriteria = new CdtSearchCriteria();
			$tDocente = CYTSecureDAOFactory::getDocenteDAO()->getTableName();
			
			$oCriteria->addFilter("$tDocente.cd_docente", $oDocente->getOid(), '=');
			$oCriteria->addFilter('dt_hasta', CYT_FECHA_CIERRE, '<=', new CdtCriteriaFormatStringValue());
			
			
			$becasManager = CYTSecureManagerFactory::getBecaManager();
			$becas = $becasManager->getEntities($oCriteria);
			$jovenesBecas = new ItemCollection();
			foreach ($becas as $oBeca) {
				$oJovenesBeca = new JovenesBeca();
				$oJovenesBeca->setDs_tipobeca($oBeca->getDs_tipobeca());
				$oJovenesBeca->setBl_unlp($oBeca->getBl_unlp());
				$oJovenesBeca->setDt_desde($oBeca->getDt_desde());
				$oJovenesBeca->setDt_hasta($oBeca->getDt_hasta());
				$oJovenesBeca->setBl_agregado(0);
				$jovenesBecas->addItem($oJovenesBeca);
			}
			
			$oSolicitud->setBecas( $jovenesBecas );
			
			$manager = new JovenesBecaSessionManager();
			$manager->setEntities( $oSolicitud->getBecas() );*/
			
			//proyectos anteriores.
			/*$oCriteria = new CdtSearchCriteria();
			$tDocente = CYTSecureDAOFactory::getDocenteDAO()->getTableName();
			$tIntegrante = CYTSecureDAOFactory::getIntegranteDAO()->getTableName();
			$tProyecto = CYTSecureDAOFactory::getProyectoDAO()->getTableName();
			$oCriteria->addFilter("$tDocente.cd_docente", $oDocente->getOid(), '=');
			$oCriteria->addFilter('DIR.cd_tipoinvestigador', CYT_INTEGRANTE_DIRECTOR, '=');
			$oCriteria->addFilter("$tIntegrante.cd_tipoinvestigador", CYT_INTEGRANTE_COLABORADOR, '<>');
			//$oCriteria->addFilter("$tIntegrante.cd_estado", CYT_ESTADO_INTEGRANTE_ADMITIDO, '=');
			$filter = new CdtSimpleExpression("(".$tIntegrante.".cd_estado = ".CYT_ESTADO_INTEGRANTE_ADMITIDO." OR ".$tIntegrante.".cd_estado = ".CYT_ESTADO_INTEGRANTE_BAJA_CREADA." OR ".$tIntegrante.".cd_estado = ".CYT_ESTADO_INTEGRANTE_BAJA_RECIBIDA." OR ".$tIntegrante.".cd_estado = ".CYT_ESTADO_INTEGRANTE_CAMBIO_HS_CREADO." OR ".$tIntegrante.".cd_estado = ".CYT_ESTADO_INTEGRANTE_CAMBIO_HS_RECIBIDO.") AND (dt_fin <= '".$oneYearAgo.CYT_DIA_MES_PROYECTO_FIN."' OR (dt_fin > '".$oneYearAgo.CYT_DIA_MES_PROYECTO_FIN."' AND  (".$tIntegrante.".dt_baja <= '".$oneYearAgo.CYT_DIA_MES_PROYECTO_FIN."' AND ".$tIntegrante.".dt_baja IS NOT NULL AND ".$tIntegrante.".dt_baja <> '0000-00-00') ))");
			$oCriteria->setExpresion($filter);
			
			
			
			/*$proyectosManager = CYTSecureManagerFactory::getProyectoManager();
			$proyectos = $proyectosManager->getEntities($oCriteria);*/
			$solicitudProyectos = new ItemCollection();
			/*foreach ($proyectos as $oProyecto) {
				$oJovenesProyecto = new JovenesProyecto();
				$oJovenesProyecto->setProyecto($oProyecto);
				$oJovenesProyecto->setDs_codigo($oProyecto->getDs_codigo());
				$oJovenesProyecto->setDs_titulo($oProyecto->getDs_titulo());
				$oJovenesProyecto->setDs_director($oProyecto->getDirector()->getDs_apellido().', '.$oProyecto->getDirector()->getDs_nombre());
				$oCriteriaIntegrante = new CdtSearchCriteria();
				$oCriteriaIntegrante->addFilter("$tDocente.cd_docente", $oDocente->getOid(), '=');
				$oCriteriaIntegrante->addFilter("cd_proyecto", $oProyecto->getOid(), '=');
				$integrantesManager = CYTSecureManagerFactory::getIntegranteManager();
				$oIntegrante = $integrantesManager->getEntity($oCriteriaIntegrante);
				$oJovenesProyecto->setDt_desdeproyecto($oIntegrante->getDt_alta());
				$dt_hasta = (($oIntegrante->getDt_baja()=='0000-00-00')||($oIntegrante->getDt_baja()=='')||(!$oIntegrante->getDt_baja()))?$oProyecto->getDt_fin():$oIntegrante->getDt_baja();
				$oJovenesProyecto->setDt_hastaproyecto($dt_hasta);
				$oJovenesProyecto->setBl_agregado(0);
				$jovenesProyectos->addItem($oJovenesProyecto);
			}*/
			
			$oSolicitud->setOtrosProyectos( $solicitudProyectos );
			
			$manager = new OtrosProyectoSessionManager();
			$manager->setEntities( $oSolicitud->getOtrosProyectos() );
			
			/*$managerTipo1 = new PresupuestoTipo1SessionManager();
			$managerTipo2 = new PresupuestoTipo2SessionManager();
			$managerTipo3 = new PresupuestoTipo3SessionManager();
			$managerTipo4 = new PresupuestoTipo4SessionManager();
			$managerTipo5 = new PresupuestoTipo5SessionManager();
						
			$managerTipo1->setEntities( new ItemCollection() );
			$managerTipo2->setEntities(new ItemCollection()  );
			$managerTipo3->setEntities(new ItemCollection()  );
			$managerTipo4->setEntities( new ItemCollection() );
			$managerTipo5->setEntities( new ItemCollection() );*/
			
        }

		return $oSolicitud;
	}

	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getOutputTitle();
	 */
	protected function getOutputTitle(){
		return CYT_MSG_SOLICITUD_TITLE_ADD;
	}

	/**
	 * (non-PHPdoc)
	 * @see classes/com/gestion/action/entities/EditEntityInitAction::getSubmitAction()
	 */
	protected function getSubmitAction(){
		return "add_solicitud";
	}


}
