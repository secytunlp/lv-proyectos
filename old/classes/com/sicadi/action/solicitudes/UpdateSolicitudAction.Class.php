<?php

/**
 * Acción para actualizar una solicitud
 *
 * @author Marcos
 * @since 04-02-2014
 *
 */
class UpdateSolicitudAction extends UpdateEntityAction{

	protected function getEntity() {
		if (date('Y-m-d')>CYT_FECHA_CIERRE) {
			throw new GenericException( CYT_MSG_FIN_PERIODO );
		}
		$entity =  parent::getEntity();

		//CdtUtils::logObject($entity);

		//CdtUtils::logObject($entity);
		if ($entity->getTitulo()->getOid()) {
			$managerTitulo = CYTSecureManagerFactory::getTituloManager();
			$oTitulo = $managerTitulo->getObjectByCode($entity->getTitulo()->getOid());
			$entity->setDs_titulogrado($oTitulo->getDs_titulo());
		}	
		if ($entity->getTituloposgrado()->getOid()) {
			$managerTitulo = CYTSecureManagerFactory::getTituloManager();
			$oTitulo = $managerTitulo->getObjectByCode($entity->getTituloposgrado()->getOid());
			$entity->setDs_tituloposgrado($oTitulo->getDs_titulo());
		}
	
		$error = '';
		$dir = CYT_PATH_PDFS.'/';
		if (!file_exists($dir)) mkdir($dir, 0777); 
		$dir .= CYT_PERIODO_YEAR.'/';
		if (!file_exists($dir)) mkdir($dir, 0777); 
		$oUser = CdtSecureUtils::getUserLogged();
        $separarCUIL = explode('-',trim($oUser->getDs_username()));
		$dir .= $separarCUIL[1].'/';
		if (!file_exists($dir)) mkdir($dir, 0777);
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('nu_documento', $separarCUIL[1], '=');
		
		$oDocenteManager =  CYTSecureManagerFactory::getDocenteManager();
		$oDocente = $oDocenteManager->getEntity($oCriteria);
		

		$entity->setDocente($oDocente);

		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_docente', $oDocente->getOid(), '=');
		$oSolicitudManager =  ManagerFactory::getSolicitudManager();
		$oSolicitudAux = $oSolicitudManager->getEntity($oCriteria);
		
		if((empty($oSolicitudAux))||($entity->getOid()!=$oSolicitudAux->getOid())){
			throw new GenericException( CDT_SECURE_MSG_PERMISSION_DENIED );
		}

		/*$oPeriodoManager = CYTSecureManagerFactory::getPeriodoManager();
		$oPerioActual = $oPeriodoManager->getPeriodoActual(CYT_PERIODO_YEAR);*/
		
		$entity->setBl_becario(($entity->getDs_orgbeca()||($entity->getLugarTrabajoBeca()->getOid())||($entity->getDs_tipobeca())||($entity->getDt_becadesde())||($entity->getDt_becahasta()))?1:0);
		$entity->setBl_carrera((($entity->getDt_ingreso())||($entity->getLugarTrabajoCarrera()->getOid())||($entity->getOrganismo()->getOid())||($entity->getCarrerainv()->getOid()))?1:0);
		
		$entity->setDt_fecha(date(DB_DEFAULT_DATETIME_FORMAT));
		if(isset($_SESSION['archivos'])){
			$archivos = unserialize( $_SESSION['archivos'] );
			
			foreach ($archivos as $key => $archivo) {
				//CdtUtils::log("FILE: "   . $key);
                $extenciones_permitidas = CYT_EXTENSIONES_PERMITIDAS;

                switch ($key) {
                    case 'ds_curriculum':
                        $nombre = CYT_LBL_SOLICITUD_A_CURRICULUM;
                        $sigla = CYT_LBL_SOLICITUD_A_CURRICULUM_SIGLA;

                        break;

                    case 'ds_resbeca':
                        $nombre = CYT_LBL_SOLICITUD_BECA_RESOLUCION;
                        $sigla = CYT_LBL_SOLICITUD_BECA_RESOLUCION_SIGLA;
                        break;
                    case 'ds_rescarrera':
                        $nombre = CYT_LBL_SOLICITUD_CARRERA_RESOLUCION;
                        $sigla = CYT_LBL_SOLICITUD_CARRERA_RESOLUCION_SIGLA;
                        break;
                    case 'ds_archivo':
                        $nombre = CYT_LBL_SOLICITUD_PROYECTOS_ARCHIVO;
                        $sigla = CYT_LBL_SOLICITUD_PROYECTOS_ARCHIVO_SIGLA;
                        break;
                    case 'ds_foto':
                        $nombre = CYT_LBL_SOLICITUD_FOTO;
                        $sigla = CYT_LBL_SOLICITUD_FOTO_SIGLA;
                        $extenciones_permitidas = CYT_EXTENSIONES_PERMITIDAS_IMG;

                        break;
                    case 'ds_informe1':
                        $nombre = CYT_LBL_SOLICITUD_A_INFORME1;
                        $sigla = CYT_LBL_SOLICITUD_A_INFORME1_SIGLA;
                        break;
                    case 'ds_informe2':
                        $nombre = CYT_LBL_SOLICITUD_A_INFORME2;
                        $sigla = CYT_LBL_SOLICITUD_A_INFORME2_SIGLA;
                        break;
                    case 'ds_informe3':
                        $nombre = CYT_LBL_SOLICITUD_A_INFORME3;
                        $sigla = CYT_LBL_SOLICITUD_A_INFORME3_SIGLA;
                        break;

                }
            		
            		
				$explode_name = explode('.', $archivo['name']);
	            //Se valida así y no con el mime type porque este no funciona par algunos programas
	            $pos_ext = count($explode_name) - 1;
	            if (in_array(strtolower($explode_name[$pos_ext]), explode(",",$extenciones_permitidas))) {
	            	
	            	
	            	if (is_file($dir.$archivo['nuevo'])){
	            		rename ($dir.$archivo['nuevo'],$dir.uniqid() . "_" .str_replace('TMP_'.$sigla, $sigla, $archivo['nuevo'])); 
	            	}
	            	CdtReflectionUtils::doSetter( $entity, $key, str_replace('TMP_'.$sigla, $sigla, $archivo['nuevo']) );
	            	
	            }
	            else {
	            	
	            	$error .=CYT_MSG_FORMATO_INVALIDO.$nombre.'<br />';
	            }
			}
		}
		unset($_SESSION['archivos']);
		$handle=opendir($dir);
		while ($archivo = readdir($handle)){
	        if ((is_file($dir.$archivo))&&(strchr($archivo,'TMP_'))){
	         	unlink($dir.$archivo);
			}
		}
		closedir($handle);
		if ($error) {
			throw new GenericException( $error );
		}

        $oCriteria = new CdtSearchCriteria();
        $oCriteria->addFilter('cd_solicitud', $entity->getOid(), '=');

        $oCargoManager =  ManagerFactory::getSolicitudCargoManager();
        $oCargos = $oCargoManager->getEntities($oCriteria);
        $cargosArray = new ItemCollection();
        foreach ($oCargos as $oCargo) {


            $cargosArray->addItem($oCargo);
        }
        $entity->setCargos( $cargosArray );


        //proyectos.
        $oCriteria = new CdtSearchCriteria();
        $oCriteria->addFilter('cd_solicitud', $entity->getOid(), '=');
        $oCriteria->addFilter('bl_agregado', 0, '=');
        /*$filter = new CdtSimpleExpression("(dt_hasta > '".date('Y-m-d')."' OR dt_hasta IS NULL OR dt_hasta = '0000-00-00')");
        $oCriteria->setExpresion($filter);*/
        $proyectosActualesManager = ManagerFactory::getOtrosProyectoManager();
        $proyectosActuales = $proyectosActualesManager->getEntities($oCriteria);
        $proyectos = new ItemCollection();
        foreach ($proyectosActuales as $oProyectoSolicitud) {

            $oProyecto = new ProyectoAgencia();
            $oProyecto->setOid($oProyectoSolicitud->getProyecto()->getoid());
            $oProyecto->setDs_titulo($oProyectoSolicitud->getDs_titulo());
            $oProyecto->setDs_codigo($oProyectoSolicitud->getDs_codigo());
            $oDirector = new Docente();
            $directorArray = explode(',',$oProyectoSolicitud->getDs_director());
            $oDirector->setDs_apellido($directorArray[0]);
            $oDirector->setDs_nombre($directorArray[1]);
            $oProyecto->setDirector($oDirector);
            $oProyecto->setDt_ini($oProyectoSolicitud->getDt_desdeproyecto());
            $oProyecto->setDt_fin($oProyectoSolicitud->getDt_hastaproyecto());
            $oProyecto->setDs_organismo($oProyectoSolicitud->getDs_organismo());


            $proyectos->addItem($oProyecto);
        }

        $entity->setProyectos( $proyectos );
		
		/*$otrosProyectosManager = new OtrosProyectoSessionManager();
		$entity->setOtrosProyectos( $otrosProyectosManager->getEntities(new CdtSearchCriteria()) );*/
		

		
		return $entity;
		
	}
	
	public function getNewFormInstance(){
		return new CMPSolicitudForm();
	}

	public function getNewEntityInstance(){
		$oSolicitud = new Solicitud();
		
		return $oSolicitud;
	}

	protected function getEntityManager(){
		return ManagerFactory::getSolicitudManager();
	}



	



}
