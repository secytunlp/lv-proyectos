<?php

/**
 * Manager para Solicitud
 *  
 * @author Marcos
 * @since 13-11-2013
 */
class SolicitudManager extends EntityManager{

	public function getDAO(){
		return DAOFactory::getSolicitudDAO();
	}

	public function add(Entity $entity) {
		
		parent::add($entity);
		$managerEstado = CYTSecureManagerFactory::getEstadoManager();
		$oEstado = $managerEstado->getObjectByCode(CYT_ESTADO_SOLICITUD_CREADA);
		$oSolicitudEstado = new SolicitudEstado();
		$oSolicitudEstado->setSolicitud($entity);
		$oSolicitudEstado->setFechaDesde(date(DB_DEFAULT_DATETIME_FORMAT));
		$oSolicitudEstado->setEstado($oEstado);
		$oUser = CdtSecureUtils::getUserLogged();
		$oSolicitudEstado->setUser($oUser);
		$oSolicitudEstado->setFechaUltModificacion(date(DB_DEFAULT_DATETIME_FORMAT));
		$managerSolicitudEstado = CYTSecureManagerFactory::getSolicitudEstadoManager();
		$managerSolicitudEstado->add($oSolicitudEstado);
		
		
		/*$managerLugarTrabajo =  CYTSecureManagerFactory::getLugarTrabajoManager();
		$oLugarTrabajo = $managerLugarTrabajo->getObjectByCode($entity->getLugarTrabajo()->getOid());
		if (!empty($oLugarTrabajo)) {
				$oLugarTrabajo->setDs_direccion($entity->getDs_direccion());
				$oLugarTrabajo->setDs_telefono($entity->getDs_telefono());
				$managerLugarTrabajo->update($oLugarTrabajo);	
		}*/
		
		//agregamos las entidades relacionadas.
		

		
		
		
		//otrosProyectos



		/*$otrosProyectos = $entity->getOtrosProyectos();
		foreach ($otrosProyectos as $oOtrosProyecto) {
			$oOtrosProyecto->setSolicitud( $entity );
			
			$managerOtrosProyecto = ManagerFactory::getOtrosProyectoManager();
			$managerOtrosProyecto->add($oOtrosProyecto);
			
		}*/

		//cargos
		$cargos = $entity->getCargos();


		foreach ($cargos as $oCargo) {
			$oSolicitudCargo = new SolicitudCargo();
			$oSolicitudCargo->setSolicitud( $entity );
			$oSolicitudCargo->setCargo($oCargo->getCargo());
			$oSolicitudCargo->setDeddoc($oCargo->getDeddoc());
			$oSolicitudCargo->setFacultad($oCargo->getFacultad());
			$oSolicitudCargo->setDt_fecha($oCargo->getDt_fecha());
			$oSolicitudCargo->setSituacion($oCargo->getSituacion());
			$managerCargo = ManagerFactory::getSolicitudCargoManager();
			$managerCargo->add($oSolicitudCargo);

		}
		

		//proyectos
		$proyectos = $entity->getProyectos();
		foreach ($proyectos as $oProyecto) {
			$nombreDeClase = get_class($oProyecto);
			$oOtrosProyecto = new OtrosProyecto();
			$oOtrosProyecto->setSolicitud( $entity );
			$oOtrosProyecto->setProyecto($oProyecto);
			$oOtrosProyecto->setDt_desdeproyecto($oProyecto->getDt_ini());
			$oOtrosProyecto->setDt_hastaproyecto($oProyecto->getDt_fin());
			$oOtrosProyecto->setBl_agregado(0);
			$oOtrosProyecto->setDs_codigo($oProyecto->getDs_codigo());
			$oOtrosProyecto->setDs_director($oProyecto->getDirector()->getDs_apellido().', '.$oProyecto->getDirector()->getDs_nombre());
			$oOtrosProyecto->setDs_titulo($oProyecto->getDs_titulo());
			$oOtrosProyecto->setDs_organismo(($nombreDeClase=='ProyectoAgencia')?$oProyecto->getDs_organismo():'UNLP');
			$managerProyecto = ManagerFactory::getOtrosProyectoManager();
			$managerProyecto->add($oOtrosProyecto);
			
		}

		if (($entity->getDt_proyectodesde())||($entity->getDt_proyectohasta())||($entity->getDs_codigoproyecto())||($entity->getDs_directorproyecto())||($entity->getDs_tituloproyecto())||($entity->getDs_organismoproyecto())) {
			$oOtrosProyecto = new OtrosProyecto();
			$oOtrosProyecto->setSolicitud($entity);

			$oOtrosProyecto->setDt_desdeproyecto($entity->getDt_proyectodesde());
			$oOtrosProyecto->setDt_hastaproyecto($entity->getDt_proyectohasta());
			$oOtrosProyecto->setBl_agregado(1);
			$oOtrosProyecto->setDs_codigo($entity->getDs_codigoproyecto());
			$oOtrosProyecto->setDs_director($entity->getDs_directorproyecto());
			$oOtrosProyecto->setDs_titulo($entity->getDs_tituloproyecto());
			$oOtrosProyecto->setDs_organismo($entity->getDs_organismoproyecto());
			$oOtrosProyecto->setDs_archivo($entity->getDs_archivo());
			$managerProyecto = ManagerFactory::getOtrosProyectoManager();
			$managerProyecto->add($oOtrosProyecto);
		}
		
		
		
    }	
	
    
/**
     * se modifica la entity
     * @param (Entity $entity) entity a modificar.
     */
    public function update(Entity $entity) {
    	parent::update($entity);
    	
    	/*$managerLugarTrabajo =  CYTSecureManagerFactory::getLugarTrabajoManager();
		$oLugarTrabajo = $managerLugarTrabajo->getObjectByCode($entity->getLugarTrabajo()->getOid());
		if (!empty($oLugarTrabajo)) {
				$oLugarTrabajo->setDs_direccion($entity->getDs_direccion());
				$oLugarTrabajo->setDs_telefono($entity->getDs_telefono());
				$managerLugarTrabajo->update($oLugarTrabajo);	
		}*/

		//cargos
		$cargoDAO =  DAOFactory::getSolicitudCargoDAO();
		$cargoDAO->deleteSolicitudCargoPorSolicitud($entity->getOid());

		//cargos
		$cargos = $entity->getCargos();


		foreach ($cargos as $oCargo) {
			$oSolicitudCargo = new SolicitudCargo();
			$oSolicitudCargo->setSolicitud( $entity );
			$oSolicitudCargo->setCargo($oCargo->getCargo());
			$oSolicitudCargo->setDeddoc($oCargo->getDeddoc());
			$oSolicitudCargo->setFacultad($oCargo->getFacultad());
			$oSolicitudCargo->setDt_fecha($oCargo->getDt_fecha());
			$oSolicitudCargo->setSituacion($oCargo->getSituacion());
			$managerCargo = ManagerFactory::getSolicitudCargoManager();
			$managerCargo->add($oSolicitudCargo);

		}
		
    	//proyectos
    	$proyectoDAO =  DAOFactory::getOtrosProyectoDAO();
        $proyectoDAO->deleteOtrosProyectoPorSolicitud($entity->getOid());


		/*$proyectos = $entity->getOtrosProyectos();
		foreach ($proyectos as $proyecto) {
			$proyecto->setSolicitud( $entity );
			$managerOtrosProyecto = ManagerFactory::getOtrosProyectoManager();
			$managerOtrosProyecto->add($proyecto);
		}*/
		
    	$proyectos = $entity->getProyectos();
		foreach ($proyectos as $oProyecto) {
			CYTSecureUtils::logObject($oProyecto);
			$oOtrosProyecto = new OtrosProyecto();
			$oOtrosProyecto->setSolicitud( $entity );
			$oOtrosProyecto->setProyecto($oProyecto);
			$oOtrosProyecto->setDt_desdeproyecto($oProyecto->getDt_ini());
			$oOtrosProyecto->setDt_hastaproyecto($oProyecto->getDt_fin());
			$oOtrosProyecto->setBl_agregado(0);
			$oOtrosProyecto->setDs_codigo($oProyecto->getDs_codigo());
			$oOtrosProyecto->setDs_director($oProyecto->getDirector()->getDs_apellido().', '.$oProyecto->getDirector()->getDs_nombre());
			$oOtrosProyecto->setDs_titulo($oProyecto->getDs_titulo());
			$oOtrosProyecto->setDs_organismo($oProyecto->getDs_organismo());
			$managerProyecto = ManagerFactory::getOtrosProyectoManager();
			$managerProyecto->add($oOtrosProyecto);
			
		}

		if (($entity->getDt_proyectodesde())||($entity->getDt_proyectohasta())||($entity->getDs_codigoproyecto())||($entity->getDs_directorproyecto())||($entity->getDs_tituloproyecto())||($entity->getDs_organismoproyecto())) {

			$oOtrosProyecto = new OtrosProyecto();
			$oOtrosProyecto->setSolicitud($entity);

			$oOtrosProyecto->setDt_desdeproyecto($entity->getDt_proyectodesde());
			$oOtrosProyecto->setDt_hastaproyecto($entity->getDt_proyectohasta());
			$oOtrosProyecto->setBl_agregado(1);
			$oOtrosProyecto->setDs_codigo($entity->getDs_codigoproyecto());
			$oOtrosProyecto->setDs_director($entity->getDs_directorproyecto());
			$oOtrosProyecto->setDs_titulo($entity->getDs_tituloproyecto());
			$oOtrosProyecto->setDs_organismo($entity->getDs_organismoproyecto());
			$oOtrosProyecto->setDs_archivo($entity->getDs_archivo());
			$managerProyecto = ManagerFactory::getOtrosProyectoManager();
			$managerProyecto->add($oOtrosProyecto);
		}
		else{
			$dir = CYT_PATH_PDFS.'/'.CYT_PERIODO_YEAR.'/';

			$oUser = CdtSecureUtils::getUserLogged();
			$separarCUIL = explode('-',trim($oUser->getDs_username()));
			$dir .= $separarCUIL[1].'/';

			$handle=opendir($dir);
			while ($archivo = readdir($handle)){
				if ((is_file($dir.$archivo))&&(strchr($archivo,'PROY_')))
				{

					unlink($dir.$archivo);
				}
			}
			closedir($handle);
		}


		
		
        
    }
    
/**
     * se modifica la entity
     * @param (Entity $entity) entity a modificar.
     */
    public function updateWithoutRelations(Entity $entity) {
    	parent::update($entity);
        
    }
    
	/**
     * se elimina la entity
     * @param int identificador de la entity a eliminar.
     */
    public function delete($id) {
    	$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('solicitud_oid', $id, '=');
		$oCriteria->addNull('fechaHasta');
		$managerSolicitudEstadoManager =  CYTSecureManagerFactory::getSolicitudEstadoManager();
		$oSolicitudEstado = $managerSolicitudEstadoManager->getEntity($oCriteria);
    	if (($oSolicitudEstado->getEstado()->getOid()!=CYT_ESTADO_SOLICITUD_CREADA)) {
			
			throw new GenericException( CYT_MSG_SOLICITUD_ELIMINAR_PROHIBIDO);
		}
		else{
		
	    	$oSolicitudEstadoDAO =  CYTSecureDAOFactory::getSolicitudEstadoDAO();
	        $oSolicitudEstadoDAO->deleteSolicitudEstadoPorSolicitud($id);



			//cargos
			$cargoDAO =  DAOFactory::getSolicitudCargoDAO();
			$cargoDAO->deleteSolicitudCargoPorSolicitud($id);
	        

	        
	        $oOtrosProyecto =  DAOFactory::getOtrosProyectoDAO();
	        $oOtrosProyecto->deleteOtrosProyectoPorSolicitud($id);
	        
	    	parent::delete( $id );
	    	
	    	$dir = CYT_PATH_PDFS.'/'.CYT_PERIODO_YEAR.'/';

			$oUser = CdtSecureUtils::getUserLogged();
	        $separarCUIL = explode('-',trim($oUser->getDs_username()));
			$dir .= $separarCUIL[1].'/';
	    	
	    	$handle=opendir($dir);
			while ($archivo = readdir($handle)){
		        if ((is_file($dir.$archivo))){
		         	unlink($dir.$archivo);
				}
			}
			closedir($handle);
		}
		
    }
    
	/**
	 * (non-PHPdoc)
	 * @see classes/com/entities/manager/EntityManager::validateOnAdd()
	 */
    protected function validateOnAdd(Entity $entity){
    	
    	parent::validateOnAdd($entity);
    	$error='';
    	if ((in_array($entity->getDeddoc()->getOid(),explode(",",CYT_DEDICACIONES_SIMPLES))) &&(!$entity->getBl_becario())&&(!$entity->getBl_carrera())){
			$error .= CYT_MSG_SIMPLE_SIN_BECA.'<br>';
		}
		/*if (in_array($entity->getDeddoc()->getOid(),explode(",",CYT_DEDICACIONES_SIMPLES))){
			if ($entity->getLugarTrabajoBeca()->getOid()) {
				if ($entity->getBl_becario()) {
					$managerLugarTrabajo = CYTSecureManagerFactory::getLugarTrabajoManager();
					$oLugarTrabajo = $managerLugarTrabajo->getObjectByCode($entity->getLugarTrabajoBeca()->getOid());
					$encontre = 0;
					while((!$encontre)&&($oLugarTrabajo->getPadre()->getOid()!=0)){
						if ((!$encontre)&&(($oLugarTrabajo->getPadre()->getOid()==CYT_CD_LUGAR_TRABAJO_UNLP_CONICET)||($oLugarTrabajo->getPadre()->getOid()==CYT_CD_LUGAR_TRABAJO_UNLP))){
							
							$encontre = 1;
						}
						$oLugarTrabajo = $managerLugarTrabajo->getObjectByCode($oLugarTrabajo->getPadre()->getOid());
					}
					if (!$encontre) {
						$error .= CYT_MSG_SOLICITUD_LUGAR_TRABAJO_BECA_NO_UNLP.'<br>';
					}
				}
				
			}
			if ($entity->getLugarTrabajoCarrera()->getOid()) {
				if ($entity->getBl_carrera()) {
					$managerLugarTrabajo = CYTSecureManagerFactory::getLugarTrabajoManager();
					$oLugarTrabajo = $managerLugarTrabajo->getObjectByCode($entity->getLugarTrabajoCarrera()->getOid());
					$encontre = 0;
					while((!$encontre)&&($oLugarTrabajo->getPadre()->getOid()!=0)){
						if ((!$encontre)&&(($oLugarTrabajo->getPadre()->getOid()==CYT_CD_LUGAR_TRABAJO_UNLP_CONICET)||($oLugarTrabajo->getPadre()->getOid()==CYT_CD_LUGAR_TRABAJO_UNLP))){
							
							$encontre = 1;
						}
						$oLugarTrabajo = $managerLugarTrabajo->getObjectByCode($oLugarTrabajo->getPadre()->getOid());
					}
					if (!$encontre) {
						$error .= CYT_MSG_SOLICITUD_LUGAR_TRABAJO_CARRERA_NO_UNLP.'<br>';
					}
				
				}
				
			}
			
	    	
		}*/
    	
		if (($entity->getDt_becadesde())||($entity->getDt_becahasta())) {
			
			if(CYTSecureUtils::formatDateToPersist($entity->getDt_becadesde())>CYTSecureUtils::formatDateToPersist($entity->getDt_becahasta())){
	    		$error .= CYT_MSG_BECA_DESDE_MAYOR.'<br>';
	    			
	    	}
			
			if(((CYTSecureUtils::formatDateToPersist($entity->getDt_becadesde())>CYT_FECHA_CIERRE)||(CYTSecureUtils::formatDateToPersist($entity->getDt_becahasta())<CYT_FECHA_CIERRE))){
				$error .= CYT_MSG_BECA_NO_VIGENTE.'<br>';
			}
				
		}
		
		/*if ($entity->getDt_nacimiento()) {
    		if((CYTSecureUtils::edad(CYT_PERIODO_YEAR.CYT_DIA_MES_EDAD,CYTSecureUtils::formatDateToPersist($entity->getDt_nacimiento()))>=CYT_EDAD_TOPE)&&(!$entity->getBl_unlp())){
			
				$msg = CYT_MSG_SOLICITUD_EDAD_MAYOR;
				$edad = CYT_EDAD_TOPE;
				$fechaEdad = CYT_FIN_EDAD.CYT_PERIODO_YEAR;
		    	$params = array ($edad,$fechaEdad );		
			
				$error .= CdtFormatUtils::formatMessage( $msg, $params ).'<br>';
				
			}
		}*/
    	/*$rango = intval(CYT_PERIODO_YEAR)-intval(CYT_YEAR_INGRESO_ATRAS);
		if (($entity->getBl_doctorado())&&($entity->getDt_egresoposgrado())) {
			
			if ((CYTSecureUtils::formatDateToPersist($entity->getDt_egresoposgrado())<CYTSecureUtils::formatDateToPersist(CYT_RANGO_INGRESO.$rango))){
				$error .= CYT_MSG_SOLICITUD_DOCTORADO_ANTERIOR.CYT_RANGO_INGRESO.$rango.'<br>';
			}
		}*/
		if ($entity->getDt_ingreso()) {
			
			/*if (CYTSecureUtils::formatDateToPersist($entity->getDt_ingreso())<CYTSecureUtils::formatDateToPersist(CYT_RANGO_INGRESO.$rango)){
				$error .= CYT_MSG_SOLICITUD_INGRESO_A_LA_CARRERA_ANTERIOR.CYT_RANGO_INGRESO.$rango.'<br>';
			}*/
			
			if (CYTSecureUtils::formatDateToPersist($entity->getDt_ingreso())>CYT_FECHA_CIERRE){
				$error .= CYT_MSG_SOLICITUD_INGRESO_A_LA_CARRERA.'<br>';
			}
		}

		if (($entity->getDt_proyectodesde())||($entity->getDt_proyectohasta())) {

			if(CYTSecureUtils::formatDateToPersist($entity->getDt_proyectodesde())>CYTSecureUtils::formatDateToPersist($entity->getDt_proyectohasta())){
				$error .= CYT_MSG_PROYECTO_DESDE_MAYOR.'<br>';

			}

			if(((CYTSecureUtils::formatDateToPersist($entity->getDt_proyectodesde())>CYT_FECHA_CIERRE)||(CYTSecureUtils::formatDateToPersist($entity->getDt_proyectohasta())<CYT_FECHA_CIERRE))){
				$error .= CYT_MSG_PROYECTOS_FUERA_RANGO.'<br>';
			}

		}
    	
    	if ($error) {
    		throw new GenericException( $error );
    	}
    }
    
    /**
     * (non-PHPdoc)
     * @see classes/com/entities/manager/EntityManager::validateOnUpdate()
     */
	protected function validateOnUpdate(Entity $entity){
	
		parent::validateOnUpdate($entity);

		$error='';
		if ((in_array($entity->getDeddoc()->getOid(),explode(",",CYT_DEDICACIONES_SIMPLES))) &&(!$entity->getBl_becario())&&(!$entity->getBl_carrera())){
			$error .= CYT_MSG_SIMPLE_SIN_BECA.'<br>';
		}
		/*if (in_array($entity->getDeddoc()->getOid(),explode(",",CYT_DEDICACIONES_SIMPLES))){
			if ($entity->getLugarTrabajoBeca()->getOid()) {
				if ($entity->getBl_becario()) {
					$managerLugarTrabajo = CYTSecureManagerFactory::getLugarTrabajoManager();
					$oLugarTrabajo = $managerLugarTrabajo->getObjectByCode($entity->getLugarTrabajoBeca()->getOid());
					$encontre = 0;
					while((!$encontre)&&($oLugarTrabajo->getPadre()->getOid()!=0)){
						if ((!$encontre)&&(($oLugarTrabajo->getPadre()->getOid()==CYT_CD_LUGAR_TRABAJO_UNLP_CONICET)||($oLugarTrabajo->getPadre()->getOid()==CYT_CD_LUGAR_TRABAJO_UNLP))){
							
							$encontre = 1;
						}
						$oLugarTrabajo = $managerLugarTrabajo->getObjectByCode($oLugarTrabajo->getPadre()->getOid());
					}
					if (!$encontre) {
						$error .= CYT_MSG_SOLICITUD_LUGAR_TRABAJO_BECA_NO_UNLP.'<br>';
					}
				}
				
			}
			if ($entity->getLugarTrabajoCarrera()->getOid()) {
				if ($entity->getBl_carrera()) {
					$managerLugarTrabajo = CYTSecureManagerFactory::getLugarTrabajoManager();
					$oLugarTrabajo = $managerLugarTrabajo->getObjectByCode($entity->getLugarTrabajoCarrera()->getOid());
					$encontre = 0;
					while((!$encontre)&&($oLugarTrabajo->getPadre()->getOid()!=0)){
						if ((!$encontre)&&(($oLugarTrabajo->getPadre()->getOid()==CYT_CD_LUGAR_TRABAJO_UNLP_CONICET)||($oLugarTrabajo->getPadre()->getOid()==CYT_CD_LUGAR_TRABAJO_UNLP))){
							
							$encontre = 1;
						}
						$oLugarTrabajo = $managerLugarTrabajo->getObjectByCode($oLugarTrabajo->getPadre()->getOid());
					}
					if (!$encontre) {
						$error .= CYT_MSG_SOLICITUD_LUGAR_TRABAJO_CARRERA_NO_UNLP.'<br>';
					}
				}
				
			}
	    	
		}*/
		if (($entity->getDt_becadesde())||($entity->getDt_becahasta())) {
			
			if(CYTSecureUtils::formatDateToPersist($entity->getDt_becadesde())>CYTSecureUtils::formatDateToPersist($entity->getDt_becahasta())){
	    		$error .= CYT_MSG_BECA_DESDE_MAYOR.'<br>';
	    			
	    	}
			
			if(((CYTSecureUtils::formatDateToPersist($entity->getDt_becadesde())>CYT_FECHA_CIERRE)||(CYTSecureUtils::formatDateToPersist($entity->getDt_becahasta())<CYT_FECHA_CIERRE))){
				$error .= CYT_MSG_BECA_NO_VIGENTE.'<br>';
			}
				
		}
		/*if ($entity->getDt_nacimiento()){
    		if((CYTSecureUtils::edad(CYT_PERIODO_YEAR.CYT_DIA_MES_EDAD,CYTSecureUtils::formatDateToPersist($entity->getDt_nacimiento()))>=CYT_EDAD_TOPE)&&(!$entity->getBl_unlp())){
			
				$msg = CYT_MSG_SOLICITUD_EDAD_MAYOR;
				$edad = CYT_EDAD_TOPE;
				$fechaEdad = CYT_FIN_EDAD.CYT_PERIODO_YEAR;
		    	$params = array ($edad,$fechaEdad );		
			
				$error .= CdtFormatUtils::formatMessage( $msg, $params ).'<br>';
				
			}
		}*/
		/*$rango = intval(CYT_PERIODO_YEAR)-intval(CYT_YEAR_INGRESO_ATRAS);
		if (($entity->getBl_doctorado())&&($entity->getDt_egresoposgrado())) {
			
			if ((CYTSecureUtils::formatDateToPersist($entity->getDt_egresoposgrado())<CYTSecureUtils::formatDateToPersist(CYT_RANGO_INGRESO.$rango))){
				$error .= CYT_MSG_SOLICITUD_DOCTORADO_ANTERIOR.CYT_RANGO_INGRESO.$rango.'<br>';
			}
		}*/
		if ($entity->getDt_ingreso()) {
			
			/*if (CYTSecureUtils::formatDateToPersist($entity->getDt_ingreso())<CYTSecureUtils::formatDateToPersist(CYT_RANGO_INGRESO.$rango)){
				$error .= CYT_MSG_SOLICITUD_INGRESO_A_LA_CARRERA_ANTERIOR.CYT_RANGO_INGRESO.$rango.'<br>';
			}*/
			
			if (CYTSecureUtils::formatDateToPersist($entity->getDt_ingreso())>CYT_FECHA_CIERRE){
				$error .= CYT_MSG_SOLICITUD_INGRESO_A_LA_CARRERA.'<br>';
			}
		}

		if (($entity->getDt_proyectodesde())||($entity->getDt_proyectohasta())) {

			if(CYTSecureUtils::formatDateToPersist($entity->getDt_proyectodesde())>CYTSecureUtils::formatDateToPersist($entity->getDt_proyectohasta())){
				$error .= CYT_MSG_PROYECTO_DESDE_MAYOR.'<br>';

			}

			if(((CYTSecureUtils::formatDateToPersist($entity->getDt_proyectodesde())>CYT_FECHA_CIERRE)||(CYTSecureUtils::formatDateToPersist($entity->getDt_proyectohasta())<CYT_FECHA_CIERRE))){
				$error .= CYT_MSG_PROYECTOS_FUERA_RANGO.'<br>';
			}

		}
		

    	
    	if ($error) {
    		throw new GenericException( $error );
    	}
		
	}

	
	/**
	 * (non-PHPdoc)
	 * @see classes/com/entities/manager/EntityManager::validateOnDelete()
	 */
	protected function validateOnDelete($id){

		parent::validateOnDelete($id);

		
	}	
	
	
	public function send(Entity $entity) {
		$this->validateOnSend($entity);
		//armamos el pdf con la data necesaria.

		
		
		
		$oid = $entity->getOid();
		
		
		$oSolicitudManager = ManagerFactory::getSolicitudManager();
		$oSolicitud = $oSolicitudManager->getObjectByCode($oid);
		$oEstado = new Estado();
		$oEstado->setOid(CYT_ESTADO_SOLICITUD_RECIBIDA);
		$this->cambiarEstado($oSolicitud, $oEstado, '');

		if ($oSolicitud->getEquivalencia()->getOid()!=5){
			$dir = CYT_PATH_PDFS.'/'.CYT_PERIODO_YEAR.'/';

			$oUser = CdtSecureUtils::getUserLogged();
			$separarCUIL = explode('-',trim($oUser->getDs_username()));
			$dir .= $separarCUIL[1].'/';

			$handle=opendir($dir);
			while ($archivo = readdir($handle)){
				if ((is_file($dir.$archivo))&&(strchr($archivo,'INFO'))){
					unlink($dir.$archivo);
				}
			}
			closedir($handle);
		}
		
		/*
		 $pdf = new ViewSolicitudPDF();
		 $oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('solicitud_oid', $oSolicitud->getOid(), '=');
		$oCriteria->addNull('fechaHasta');
		$managerSolicitudEstado =  CYTSecureManagerFactory::getSolicitudEstadoManager();
		$oSolicitudEstado = $managerSolicitudEstado->getEntity($oCriteria);
		
		$pdf->setEstado_oid($oSolicitudEstado->getEstado()->getOid());
		$pdf->setPeriodo_oid($oSolicitud->getPeriodo()->getOid());
		
		
		$oPeriodoManager =  CYTSecureManagerFactory::getPeriodoManager();
		$oPeriodo = $oPeriodoManager->getObjectByCode($oSolicitud->getPeriodo()->getOid());
		$pdf->setYear($oPeriodo->getDs_periodo());
				
		$pdf->setDs_investigador($oSolicitud->getDocente()->getDs_apellido().', '.$oSolicitud->getDocente()->getDs_nombre());
		$pdf->setNu_cuil($oSolicitud->getDocente()->getNu_precuil().'-'.$oSolicitud->getDocente()->getNu_documento().'-'.$oSolicitud->getDocente()->getNu_postcuil());
		
		$pdf->setDs_calle($oSolicitud->getDs_calle());
		$pdf->setNu_nro($oSolicitud->getNu_nro());
		$pdf->setDs_depto($oSolicitud->getDs_depto());
		$pdf->setNu_piso($oSolicitud->getNu_piso());
		$pdf->setNu_cp($oSolicitud->getNu_cp());
		$pdf->setDs_mail($oSolicitud->getDs_mail());
		$pdf->setNu_telefono($oSolicitud->getNu_telefono());
		$pdf->setBl_notificacion($oSolicitud->getBl_notificacion());
		$pdf->setDs_titulogrado($oSolicitud->getDs_titulogrado());
		$pdf->setDt_egresogrado(CYTSecureUtils::formatDateToView($oSolicitud->getDt_egresogrado()));
		$pdf->setDs_tituloposgrado($oSolicitud->getDs_tituloposgrado());
		$pdf->setDt_egresoposgrado(CYTSecureUtils::formatDateToView($oSolicitud->getDt_egresoposgrado()));
		$ds_sigla = ($oSolicitud->getLugarTrabajo()->getDs_sigla())?" (".$oSolicitud->getLugarTrabajo()->getDs_sigla().")":"";
		$pdf->setDs_lugarTrabajo($oSolicitud->getLugarTrabajo()->getDs_unidad().$ds_sigla);
		
		$oLugarTrabajoManager =  CYTSecureManagerFactory::getLugarTrabajoManager();
		$oLugarTrabajo = $oLugarTrabajoManager->getObjectByCode($oSolicitud->getLugarTrabajo()->getOid());
		
		
		
		$pdf->setDs_cargo($oSolicitud->getCargo()->getDs_cargo());
		$pdf->setDs_deddoc($oSolicitud->getDeddoc()->getDs_deddoc());
		$pdf->setDs_facultad($oSolicitud->getFacultad()->getDs_facultad());
		
		$pdf->setBl_becario($oSolicitud->getBl_becario());
		$pdf->setDs_orgbeca($oSolicitud->getDs_orgbeca());
		$pdf->setDs_tipobeca($oSolicitud->getDs_tipobeca());
		$pdf->setDs_periodobeca(CYTSecureUtils::formatDateToView($oSolicitud->getDt_becadesde()).' - '.CYTSecureUtils::formatDateToView($oSolicitud->getDt_becahasta()));
		$ds_sigla = ($oSolicitud->getLugarTrabajoBeca()->getDs_sigla())?" (".$oSolicitud->getLugarTrabajoBeca()->getDs_sigla().")":"";
		$pdf->setDs_lugarTrabajoBeca($oSolicitud->getLugarTrabajoBeca()->getDs_unidad().$ds_sigla);
		
		
		$pdf->setBl_carrera($oSolicitud->getBl_carrera());
		$pdf->setDs_organismo($oSolicitud->getOrganismo()->getDs_organismo());
		$pdf->setDs_carrerainv($oSolicitud->getCarrerainv()->getDs_carrerainv());
		$pdf->setDt_ingreso(CYTSecureUtils::formatDateToView($oSolicitud->getDt_ingreso()));
		$ds_sigla = ($oSolicitud->getLugarTrabajoCarrera()->getDs_sigla())?" (".$oSolicitud->getLugarTrabajoCarrera()->getDs_sigla().")":"";
		$pdf->setDs_lugarTrabajoCarrera($oSolicitud->getLugarTrabajoCarrera()->getDs_unidad().$ds_sigla);

		$pdf->setDs_categoria($oSolicitud->getCategoria()->getDs_categoria());
		$pdf->setDs_equivalencia($oSolicitud->getEquivalencia()->getDs_equivalencia());
		//CYTSecureUtils::logObject($oSolicitud);
		$pdf->setDs_categoriasolicitada($oSolicitud->getCategoriasolicitada()->getDs_categoria());
		


		
		//proyectos.
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_solicitud', $oSolicitud->getOid(), '=');
		$oCriteria->addFilter('bl_agregado', 0, '=');
		$proyectosManager = ManagerFactory::getOtrosProyectoManager();
		$pdf->setProyectos( $proyectosManager->getEntities($oCriteria) );

		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_solicitud', $oSolicitud->getOid(), '=');
		$oCriteria->addFilter('bl_agregado', 1, '=');

		$proyectosManager = ManagerFactory::getOtrosProyectoManager();
		$pdf->setSolicitudProyectos( $proyectosManager->getEntities($oCriteria) );
		

		$pdf->setFacultadplanilla_oid($oSolicitud->getFacultadplanilla()->getOid());
    	($oSolicitud->getFacultadplanilla()->getOid() != CYT_FACULTAD_NO_DECLARADA)?$pdf->setDs_facultadplanilla($oSolicitud->getFacultadplanilla()->getDs_facultad()):$pdf->setDs_facultadplanilla(CYT_MSG_SOLICITUD_UNIVERSIDAD);;
		

    	
		$pdf->title = CYT_MSG_SOLICITUD_PDF_TITLE;
		$pdf->SetFont('Arial','', 13);
		
		// establecemos los mÃ¡rgenes
		$pdf->SetMargins(10, 20 , 10);
		$pdf->setMaxWidth($pdf->w - $pdf->lMargin - $pdf->rMargin);
		//$pdf->SetAutoPageBreak(true,90);
		$pdf->AddPage();
		$pdf->AliasNbPages();
		
		//imprimimos la solicitud.
		$pdf->printSolicitud();
		
		$dir = CYT_PATH_PDFS.'/';
		if (!file_exists($dir)) mkdir($dir, 0777); 
		$dir .= CYT_PERIODO_YEAR.'/';
		if (!file_exists($dir)) mkdir($dir, 0777); 
		$dir .= $oSolicitud->getDocente()->getNu_documento().'/';
		if (!file_exists($dir)) mkdir($dir, 0777);
		
		
		
		$fileName = $dir.CYT_MSG_SOLICITUD_ARCHIVO_NOMBRE.CYTSecureUtils::stripAccents($oSolicitud->getDocente()->getDs_apellido()).'.pdf';;
		$pdf->Output($fileName,'F');
        //$pdf->Output(); 	
	        
		$attachs = array();
		$handle=opendir($dir);
		while ($archivo = readdir($handle))
		{
	        if (is_file($dir.$archivo))
	         {
	         	$attachs[]=$dir.$archivo;
	         }
		}
        
		
		$year = $oPeriodo->getDs_periodo();
			
		
		$subjectMail = htmlspecialchars(CYT_LBL_SOLICITUD_MAIL_SUBJECT.' '.$year, ENT_QUOTES, "UTF-8");
			
		$xtpl = new XTemplate( CYT_TEMPLATE_SOLICITUD_MAIL_ENVIAR );
		$xtpl->assign ( 'img_logo', WEB_PATH.'css/images/image002.gif' );
		$xtpl->assign('solicitud_titulo', $subjectMail);
		$xtpl->assign('year_label', CYT_LBL_SOLICITUD_MAIL_YEAR);
		$xtpl->assign('year', $oPeriodo->getDs_periodo());
		$xtpl->assign('investigador_label', CYT_LBL_SOLICITUD_MAIL_INVESTIGADOR);
		$xtpl->assign('investigador', htmlspecialchars($oSolicitud->getDocente()->getDs_apellido().', '.$oSolicitud->getDocente()->getDs_nombre(), ENT_QUOTES, "UTF-8"));
		$xtpl->parse('main');		
		$bodyMail = $xtpl->text('main');
		
		
		
		
		
		
        if ($oSolicitud->getDs_mail() != "") {
				
         		CYTSecureUtils::sendMail($oSolicitud->getDocente()->getDs_nombre().' '.$oSolicitud->getDocente()->getDs_apellido(), $oSolicitud->getDs_mail(), $subjectMail, $bodyMail, $attachs);
        }
        CYTSecureUtils::sendMail(CDT_POP_MAIL_FROM_NAME, CDT_POP_MAIL_FROM, $subjectMail, $bodyMail, $attachs,$oSolicitud->getDocente()->getDs_nombre().' '.$oSolicitud->getDocente()->getDs_apellido(),$oSolicitud->getDs_mail());*/
	}
	
	protected function validateOnSend(Entity $entity){
	
		$error='';
		
		if ((!$entity->getDs_calle())||(!$entity->getNu_nro())||(!$entity->getNu_cp())||(!$entity->getDs_mail())||(!$entity->getNu_telefono())||(!$entity->getDt_nacimiento())||(!$entity->getDs_orcid())||(!$entity->getDs_sedici())||(!$entity->getDs_scholar())) {
			$error .= CYT_MSG_CAMPOS_REQUERIDOS.' '.CYT_MSG_SOLICITUD_TAB_DOMICILIO.'<br>';
		}
		if ((!$entity->getDs_titulogrado())||(!$entity->getDt_egresogrado())||(!$entity->getLugarTrabajo()->getOid())) {
			$error .= CYT_MSG_CAMPOS_REQUERIDOS.' '.CYT_MSG_SOLICITUD_TAB_UNIVERSIDAD.'<br>';
		}
		/*if (($entity->getBl_becario())&&($entity->getBl_carrera())){
			$error .= CYT_MSG_BECARIO_CARRERA_PROHIBIDO.'<br>';
		}*/
		/*if ((in_array($entity->getDeddoc()->getOid(),explode(",",CYT_DEDICACIONES_SIMPLES))) &&(!$entity->getBl_becario())&&(!$entity->getBl_carrera())){
			$error .= CYT_MSG_SIMPLE_SIN_BECA.'<br>';
		}
		$sinCargo = 0;
		if ((!$entity->getCargo()->getOid())||($entity->getCargo()->getOid()==CYT_CD_CARGO_NO_DECLARADO)) {
			$sinCargo = 1;
			if (($entity->getDeddoc()->getOid())&&($entity->getDeddoc()->getOid()!=CYT_CD_SIN_DEDICACION)) {
				$error .= CYT_MSG_SIN_CARGO_CON_DEDICACION.'<br>';
			}
			
		}
		$sinDeddoc = 0;
		if ((!$entity->getDeddoc()->getOid())||($entity->getDeddoc()->getOid()==CYT_CD_SIN_DEDICACION)) {
			$sinDeddoc = 1;
			if (($entity->getCargo()->getOid())&&($entity->getCargo()->getOid()!=CYT_CD_CARGO_NO_DECLARADO)) {
				$error .= CYT_MSG_SIN_CARGO_CON_DEDICACION.'<br>';
			}
			
		}
		if (($sinCargo)&&($sinDeddoc)&&(!$entity->getBl_unlp())) {
			$error .= CYT_MSG_SIN_CARGO_SIN_BECA.'<br>';
		}*/


		$dir = CYT_PATH_PDFS.'/';
		if (!file_exists($dir)) mkdir($dir, 0777);
		$dir .= CYT_PERIODO_YEAR.'/';
		if (!file_exists($dir)) mkdir($dir, 0777);
		$oUser = CdtSecureUtils::getUserLogged();
		$separarCUIL = explode('-',trim($oUser->getDs_username()));
		$dir .= $separarCUIL[1].'/';
		if (!file_exists($dir)) mkdir($dir, 0777);
		$okCv=0;
		$okResbeca=0;
		$okRescarrera=0;
		$okArchivoproyecto=0;
		$okArchivoinfo1=0;
		$okArchivoinfo2=0;
		$okArchivoinfo3=0;


		$handle=opendir($dir);
		while ($archivo = readdir($handle))
		{
			if ((is_file($dir.$archivo))&&(strchr($archivo,'CV_')))
			{
				$okCv=1;
			}
			if ((is_file($dir.$archivo))&&(strchr($archivo,'RES_')))
			{
				$okResbeca=1;
			}
			if ((is_file($dir.$archivo))&&(strchr($archivo,'CARR_')))
			{
				$okRescarrera=1;
			}
			if ((is_file($dir.$archivo))&&(strchr($archivo,'PROY_')))
			{
				$okArchivoproyecto=1;
			}
			if ((is_file($dir.$archivo))&&(strchr($archivo,'INFO1_')))
			{
				$okArchivoinfo1=1;
			}
			if ((is_file($dir.$archivo))&&(strchr($archivo,'INFO2_')))
			{
				$okArchivoinfo2=1;
			}
			if ((is_file($dir.$archivo))&&(strchr($archivo,'INFO3_')))
			{
				$okArchivoinfo3=1;
			}


		}

		if ($entity->getBl_becario()) {
			if ((!$entity->getDs_orgbeca())||(!$entity->getLugarTrabajoBeca()->getOid())||(!$entity->getDs_tipobeca())||(!$entity->getDt_becadesde())||(!$entity->getDt_becahasta())||(!$entity->getDs_resbeca())) {
				$error .= CYT_MSG_SOLICITUD_TAB_CAMPOS_REQUERIDOS.' '.CYT_MSG_SOLICITUD_TAB_BECARIO.'<br>';
			}
			
				
			if(CYTSecureUtils::formatDateToPersist($entity->getDt_becadesde())>CYTSecureUtils::formatDateToPersist($entity->getDt_becahasta())){
	    		$error .= CYT_MSG_BECA_DESDE_MAYOR.'<br>';		
	    	}
			
			if(((CYTSecureUtils::formatDateToPersist($entity->getDt_becadesde())>CYT_FECHA_CIERRE)||(CYTSecureUtils::formatDateToPersist($entity->getDt_becahasta())<CYT_FECHA_CIERRE))){
				$error .= CYT_MSG_BECA_NO_VIGENTE.'<br>';
			}

			if (!$okResbeca){
				$error .=CYT_MSG_SOLICITUD_RES_PROBLEMA.'<br />';
			}
					
			
		}
		if ($entity->getBl_carrera()) {
			if ((!$entity->getDt_ingreso())||(!$entity->getLugarTrabajoCarrera()->getOid())||(!$entity->getOrganismo()->getOid())||(!$entity->getCarrerainv()->getOid())||(!$entity->getDs_rescarrera())) {
				$error .= CYT_MSG_SOLICITUD_TAB_CAMPOS_REQUERIDOS.' '.CYT_MSG_SOLICITUD_TAB_CARRERAINV.'<br>';
			}
			if ($entity->getDt_ingreso()) {

				if (CYTSecureUtils::formatDateToPersist($entity->getDt_ingreso())>CYT_FECHA_CIERRE){
					$error .= CYT_MSG_SOLICITUD_INGRESO_A_LA_CARRERA.'<br>';
				}
			}
			if (!$okRescarrera){
				$error .=CYT_MSG_SOLICITUD_CARR_PROBLEMA.'<br />';
			}
			
		}

		if ($entity->getCargos()->size()>0) {


		}
		else{
			$error .=CYT_MSG_SOLICITUD_SIN_CARGOS.'<br />';
		}
		if ($entity->getProyectos()->size()>0) {


		}
		else{
			if ($entity->getOtrosproyectos()->size()>0) {
				if ((!$entity->getDt_proyectodesde())||(!$entity->getDt_proyectohasta())||(!$entity->getDs_codigoproyecto())||(!$entity->getgetDs_organismoproyecto())||(!$entity->getDs_directorproyecto())||(!$entity->getDs_tituloproyecto())||(!$entity->getDs_archivo())) {
						$error .= CYT_MSG_SOLICITUD_OTROS_PROYECTOS_REQUERIDOS.'<br>';
					}

					if (!$okArchivoproyecto){
						$error .=CYT_MSG_SOLICITUD_PROY_PROBLEMA.'<br />';
					}

			}
			else{
				$error .=CYT_MSG_SOLICITUD_SIN_PROYECTOS.'<br />';
			}
		}
		
		
		

		/*if ($entity->getBl_director()) {
			$error .= CYT_MSG_SOLICITUD_FUE_DIRCODIR.'<br>';
		}*/
		if ((!$entity->getFacultadplanilla()->getOid())||(!$entity->getEquivalencia()->getOid())||(!$entity->getCategoriasicadi()->getOid())) {
			$error .= CYT_MSG_CAMPOS_REQUERIDOS.' '.CYT_MSG_SOLICITUD_TAB_DESCRIPCION.'<br>';
		}


		if ($entity->getEquivalencia()){
			switch ($entity->getEquivalencia()->getOid()) {
				case CYT_EQUIVALENCIA_SPU:

					if (!in_array($entity->getCategoria()->getOid(),explode(",",CYT_CATS_SPU))){
						$error .= CYT_MSG_SOLICITUD_SIN_SPU.'<br>';
					}
					if ($entity->getCategoria()->getOid()!=$entity->getCategoriasicadi()->getOid()) {
						$error .= CYT_MSG_SOLICITUD_SPU_DISTINTA.'<br>';
					}
					break;
				case CYT_EQUIVALENCIA_EMERITO:
					if (!in_array($entity->getCategoriasicadi()->getOid(),explode(",",CYT_CATS_SUPERIOR))){
						$error .= CYT_MSG_SOLICITUD_EQUIVALENCIA_INDEPENDIENTE_MENOR.'<br>';
					}

					if ($entity->getCarrerainv()->getOid()!=CYT_CARRERAINV_CD_INDEPENDIENTE) {
						$error .= CYT_MSG_SOLICITUD_CAT_CARRERA_ERROR.' '.CYT_MSG_SOLICITUD_TAB_CARRERAINV.'<br>';
					}
					break;
				case CYT_EQUIVALENCIA_INDEPENDIENTE:
					if (!in_array($entity->getCategoriasicadi()->getOid(),explode(",",CYT_CATS_INDEPENDIENTE))){
						$error .= CYT_MSG_SOLICITUD_EQUIVALENCIA_INDEPENDIENTE_MENOR.'<br>';
					}

					if ($entity->getCarrerainv()->getOid()!=CYT_CARRERAINV_CD_INDEPENDIENTE) {
						$error .= CYT_MSG_SOLICITUD_CAT_CARRERA_ERROR.' '.CYT_MSG_SOLICITUD_TAB_CARRERAINV.'<br>';
					}
					break;

				case CYT_EQUIVALENCIA_SUPERIOR:
					if (!in_array($entity->getCategoriasicadi()->getOid(),explode(",",CYT_CATS_SUPERIOR))){
						$error .= CYT_MSG_SOLICITUD_EQUIVALENCIA_SUPERIOR_MENOR.'<br>';
					}

					if (($entity->getCarrerainv()->getOid()!=CYT_CARRERAINV_CD_SUPERIOR)&&($entity->getCarrerainv()->getOid()!=CYT_CARRERAINV_CD_PRINCIPAL)) {
						$error .= CYT_MSG_SOLICITUD_CAT_CARRERA_ERROR.' '.CYT_MSG_SOLICITUD_TAB_CARRERAINV.'<br>';
					}
					break;
				case CYT_EQUIVALENCIA_ADJUNTO:
					if (!in_array($entity->getCategoriasicadi()->getOid(),explode(",",CYT_CATS_ADJUNTO))){
						$error .= CYT_MSG_SOLICITUD_EQUIVALENCIA_ADJUNTO_MENOR.'<br>';
					}

					if ($entity->getCarrerainv()->getOid()!=CYT_CARRERAINV_CD_ADJUNTO) {
						$error .= CYT_MSG_SOLICITUD_CAT_CARRERA_ERROR.' '.CYT_MSG_SOLICITUD_TAB_CARRERAINV.'<br>';
					}
					break;
				case CYT_EQUIVALENCIA_ASISTENTE_3:
					if (!in_array($entity->getCategoriasicadi()->getOid(),explode(",",CYT_CATS_ADJUNTO))){
						$error .= CYT_MSG_SOLICITUD_EQUIVALENCIA_ASISTENTE_MENOR.'<br>';
					}

					if (($entity->getCarrerainv()->getOid()!=CYT_CARRERAINV_CD_ADJUNTO)&&($entity->getCarrerainv()->getOid()!=CYT_CARRERAINV_CD_ASISTENTE)) {
						$error .= CYT_MSG_SOLICITUD_CAT_CARRERA_ERROR.' '.CYT_MSG_SOLICITUD_TAB_CARRERAINV.'<br>';
					}
					if (!$okArchivoinfo1){
						$error .=CYT_MSG_SOLICITUD_INFO1_PROBLEMA.'<br />';
					}
					if (!$okArchivoinfo2){
						$error .=CYT_MSG_SOLICITUD_INFO2_PROBLEMA.'<br />';
					}
					if (!$okArchivoinfo3){
						$error .=CYT_MSG_SOLICITUD_INFO3_PROBLEMA.'<br />';
					}
					if (($entity->getNu_year1()==$entity->getNu_year2())||($entity->getNu_year1()==$entity->getNu_year3())||($entity->getNu_year2()==$entity->getNu_year3())) {
						$error .= CYT_MSG_SOLICITUD_INFORMES_PROBLEMA.'<br>';
					}

					break;
				case CYT_EQUIVALENCIA_ASISTENTE_CPA:
					if (!in_array($entity->getCategoriasicadi()->getOid(),explode(",",CYT_CATS_ASISTENTE))){
						$error .= CYT_MSG_SOLICITUD_EQUIVALENCIA_P_ASISTENTE_MENOR.'<br>';
					}

					if (($entity->getCarrerainv()->getOid()!=CYT_CARRERAINV_CD_P_ADJUNTO)&&($entity->getCarrerainv()->getOid()!=CYT_CARRERAINV_CD_P_ASISTENTE)&&($entity->getCarrerainv()->getOid()!=CYT_CARRERAINV_CD_ASISTENTE)) {
						$error .= CYT_MSG_SOLICITUD_CAT_CARRERA_ERROR.' '.CYT_MSG_SOLICITUD_TAB_CARRERAINV.'<br>';
					}
					if ($entity->getLugarTrabajoCarrera()->getOid()) {
						if ($entity->getBl_carrera()) {
							$managerLugarTrabajo = CYTSecureManagerFactory::getLugarTrabajoManager();
							$oLugarTrabajo = $managerLugarTrabajo->getObjectByCode($entity->getLugarTrabajoCarrera()->getOid());
							$encontre = 0;
							while((!$encontre)&&($oLugarTrabajo->getPadre()->getOid()!=0)){
								if ((!$encontre)&&(($oLugarTrabajo->getPadre()->getOid()==CYT_CD_LUGAR_TRABAJO_UNLP_CONICET)||($oLugarTrabajo->getPadre()->getOid()==CYT_CD_LUGAR_TRABAJO_UNLP))){

									$encontre = 1;
								}
								$oLugarTrabajo = $managerLugarTrabajo->getObjectByCode($oLugarTrabajo->getPadre()->getOid());
							}
							if (!$encontre) {
								$error .= CYT_MSG_SOLICITUD_LUGAR_TRABAJO_CARRERA_NO_UNLP.'<br>';
							}

						}

					}
					break;
				case CYT_EQUIVALENCIA_BECARIO_POSTDOCTORAL:
					if (!in_array($entity->getCategoriasicadi()->getOid(),explode(",",CYT_CATS_ASISTENTE))){
						$error .= CYT_MSG_SOLICITUD_EQUIVALENCIA_POSTDOCTORAL_MENOR.'<br>';
					}

					if (!(strchr($entity->getDs_tipobeca(),'Posdoc'))) {
						$error .= CYT_MSG_SOLICITUD_CAT_BECA_ERROR.' '.CYT_MSG_SOLICITUD_TAB_BECARIO.'<br>';
					}
					if ($entity->getLugarTrabajoBeca()->getOid()) {
						if ($entity->getBl_becario()) {
							$managerLugarTrabajo = CYTSecureManagerFactory::getLugarTrabajoManager();
							$oLugarTrabajo = $managerLugarTrabajo->getObjectByCode($entity->getLugarTrabajoBeca()->getOid());
							$encontre = 0;
							while((!$encontre)&&($oLugarTrabajo->getPadre()->getOid()!=0)){
								if ((!$encontre)&&(($oLugarTrabajo->getPadre()->getOid()==CYT_CD_LUGAR_TRABAJO_UNLP_CONICET)||($oLugarTrabajo->getPadre()->getOid()==CYT_CD_LUGAR_TRABAJO_UNLP))){

									$encontre = 1;
								}
								$oLugarTrabajo = $managerLugarTrabajo->getObjectByCode($oLugarTrabajo->getPadre()->getOid());
							}
							if (!$encontre) {
								$error .= CYT_MSG_SOLICITUD_LUGAR_TRABAJO_BECA_NO_UNLP.'<br>';
							}
						}

					}
					break;
				case CYT_EQUIVALENCIA_BECARIO_DOCTORAL:
					if (!in_array($entity->getCategoriasicadi()->getOid(),explode(",",CYT_CATS_DOCTORAL))){
						$error .= CYT_MSG_SOLICITUD_EQUIVALENCIA_DOCTORAL_MENOR.'<br>';
					}

					if (!$entity->getDs_tipobeca()) {
						$error .= CYT_MSG_SOLICITUD_CAT_BECA_ERROR.' '.CYT_MSG_SOLICITUD_TAB_BECARIO.'<br>';
					}
					if ((strchr($entity->getDs_tipobeca(),'Posdoc'))) {
						$error .= CYT_MSG_SOLICITUD_CAT_BECA_ERROR.' '.CYT_MSG_SOLICITUD_TAB_BECARIO.'<br>';
					}
					if ($entity->getLugarTrabajoBeca()->getOid()) {
						if ($entity->getBl_becario()) {
							$managerLugarTrabajo = CYTSecureManagerFactory::getLugarTrabajoManager();
							$oLugarTrabajo = $managerLugarTrabajo->getObjectByCode($entity->getLugarTrabajoBeca()->getOid());
							$encontre = 0;
							while((!$encontre)&&($oLugarTrabajo->getPadre()->getOid()!=0)){
								if ((!$encontre)&&(($oLugarTrabajo->getPadre()->getOid()==CYT_CD_LUGAR_TRABAJO_UNLP_CONICET)||($oLugarTrabajo->getPadre()->getOid()==CYT_CD_LUGAR_TRABAJO_UNLP))){

									$encontre = 1;
								}
								$oLugarTrabajo = $managerLugarTrabajo->getObjectByCode($oLugarTrabajo->getPadre()->getOid());
							}
							if (!$encontre) {
								$error .= CYT_MSG_SOLICITUD_LUGAR_TRABAJO_BECA_NO_UNLP.'<br>';
							}
						}

					}
					break;

			}
		}

		
		/*if (!$okCv){
			$error .=CYT_MSG_INTEGRANTE_CV_PROBLEMA.'<br />';
		}*/
		
		if ($entity->getFacultadplanilla()->getOid()==CYT_FACULTAD_NO_DECLARADA){
			$error .= CYT_MSG_FACULTAD_NO_DECLARADA.' '.CYT_MSG_SOLICITUD_TAB_DESCRIPCION.'<br>';
		}
		
		/*if((CYTSecureUtils::edad(CYT_PERIODO_YEAR.CYT_DIA_MES_EDAD,CYTSecureUtils::formatDateToPersist($entity->getDt_nacimiento()))>=CYT_EDAD_TOPE)&&(!$entity->getBl_unlp())){
			
			$msg = CYT_MSG_SOLICITUD_EDAD_MAYOR;
			$edad = CYT_EDAD_TOPE;
			$fechaEdad = CYT_FIN_EDAD.CYT_PERIODO_YEAR;
	    	$params = array ($edad,$fechaEdad );		
		
			$error .= CdtFormatUtils::formatMessage( $msg, $params ).'<br>';
			
		}*/
		
		/*$presupuestos = $entity->getPresupuestos();
    	$total = 0;
		foreach ($presupuestos as $oPresupuesto) {
			$total +=$oPresupuesto->getNu_montopresupuesto();
		}
		if (($total<=0)||($total>CYT_MONTO_MAXIMO))  {
    		$error .= CYT_MSG_SOLICITUD_MONTO_DECLARAR.' '.CYTSecureUtils::formatMontoToView(CYT_MONTO_MAXIMO).'<br>';
    	}
    	
		$arrayRangos = new ItemCollection();
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_solicitud', $entity->getOid(), '=');
		$proyectosManager = ManagerFactory::getSolicitudProyectoManager();
		$proyectos = $proyectosManager->getEntities($oCriteria);
		if ($entity->getBl_becario()){
			$beca = new JovenesBeca();
			$beca->setBl_unlp($entity->getBl_unlp());
			$beca->setDt_desde($entity->getDt_becadesde());
			$beca->setDt_hasta($entity->getDt_becahasta());
			$entity->getBecas()->addItem($beca);
		} 
		foreach ($entity->getBecas() as $oBeca) {
			if ($oBeca->getBl_unlp()){
				$oSolicitudProyecto = new SolicitudProyecto();
				$oSolicitudProyecto->setDt_desdeproyecto($oBeca->getDt_desde());
				$oSolicitudProyecto->setDt_hastaproyecto($oBeca->getDt_hasta());
				$proyectos->addItem($oSolicitudProyecto);
			}
		}
		$proyectos->order('dt_desdeproyecto');
		foreach ($proyectos as $oProyecto) {
			$dt_hasta = ($oProyecto->getDt_hastaproyecto()>CYTSecureUtils::formatDateToPersist(CYT_RANGO_INI.CYT_PERIODO_YEAR))?CYTSecureUtils::formatDateToPersist(CYT_RANGO_INI.CYT_PERIODO_YEAR):$oProyecto->getDt_hastaproyecto();
			if ($arrayRangos->size()) {
				$encontre=0;
				foreach ($arrayRangos as $oRango) {
					if (($oProyecto->getDt_desdeproyecto()>=$oRango->getDt_desde())&&($dt_hasta<=$oRango->getDt_hasta())){
						$encontre=1;
						break;
						
					}
					elseif (($oProyecto->getDt_desdeproyecto()<$oRango->getDt_desde())&&($dt_hasta>$oRango->getDt_hasta())){
							$oRango->setDt_desde($oProyecto->getDt_desdeproyecto());
							$oRango->setDt_hasta($dt_hasta);
							$encontre=1;
							break;	
						}
					elseif (($oProyecto->getDt_desdeproyecto()<$oRango->getDt_desde())&&($dt_hasta>$oRango->getDt_desde())&&($dt_hasta<=$oRango->getDt_hasta())){
						$oRango->setDt_desde($oProyecto->getDt_desdeproyecto());
						$encontre=1;
						break;
					}
					elseif (($oProyecto->getDt_desdeproyecto()>=$oRango->getDt_desde())&&($oProyecto->getDt_desdeproyecto()<$oRango->getDt_hasta())&&($dt_hasta>$oRango->getDt_hasta())){
						$oRango->setDt_hasta($dt_hasta);
						$encontre=1;
						break;
					}
				}
				if (!$encontre) {
					$rango = new Rango();
					$rango->setDt_desde($oProyecto->getDt_desdeproyecto());
					$rango->setDt_hasta($dt_hasta);
					$arrayRangos->addItem($rango);
				}
			}
			else{
				$rango = new Rango();
				$rango->setDt_desde($oProyecto->getDt_desdeproyecto());
				$rango->setDt_hasta($dt_hasta);
				$arrayRangos->addItem($rango);
			} 		
		}*/
		
		
		
		
		
		
		/*$yearprod=0;
		foreach ($arrayRangos as $rango){
			CdtUtils::log("Desde: "   . $rango->getDt_desde().' Hasta: '.$rango->getDt_hasta());
			$yearprod +=(CYTSecureUtils::dias($rango->getDt_hasta(),$rango->getDt_desde())/CYT_DIAS_YEAR);
		}
		CdtUtils::log("Años: "   . $yearprod);
		if ($yearprod<CYT_YEARS_PROYECTOS){
			$msg = CYT_MSG_SOLICITUD_MENOR_YEAR;
			$params = array (CYT_YEARS_PROYECTOS );		
			$error .= CdtFormatUtils::formatMessage( $msg, $params ).'<br>';
		}*/
    	
    	
		if ($error) {
    		throw new GenericException( $error );
    	}
	}
	
	public function confirm(Entity $entity, $estado_oid, $motivo='') {
		
		$oid = $entity->getOid();
		
		
		$oSolicitudManager = ManagerFactory::getSolicitudManager();
		$oSolicitud = $oSolicitudManager->getObjectByCode($oid);
		$oEstado = new Estado();
		$oEstado->setOid($estado_oid);
		$this->cambiarEstado($oSolicitud, $oEstado, $motivo);
		
		switch ($estado_oid) {
			case CYT_ESTADO_SOLICITUD_ADMITIDA:
				$ds_subjet = CYT_LBL_SOLICITUD_ADMISION;
				$ds_comment = CYT_LBL_SOLICITUD_ADMISION_COMMENT;
			break;
			case CYT_ESTADO_SOLICITUD_OTORGADA:
				$ds_subjet = CYT_LBL_SOLICITUD_OTORGAMIENTO;
				$ds_comment = '';
			break;
			case CYT_ESTADO_SOLICITUD_NO_ADMITIDA:
				$ds_subjet = '';
				$ds_comment = '<strong>'.htmlspecialchars(CYT_LBL_SOLICITUD_NO_ADMISION_COMMENT).'</strong>: '.htmlspecialchars($motivo);
			break;
			case 11:
				$ds_subjet = CYT_LBL_SOLICITUD_DEFINITIVO;
				$ds_comment = CYT_LBL_SOLICITUD_DEFINITIVO_COMMENT;
				break;
			case 1:
				$ds_subjet = '';
				$ds_comment = '<strong>'.htmlspecialchars(CYT_LBL_SOLICITUD_RECTIFY_COMMENT).'</strong>: '.htmlspecialchars($motivo);
				break;
			
		}
		
        
		$msg = $ds_subjet.CYT_LBL_SOLICITUD_MAIL_SUBJECT;
		
		$oPeriodoManager =  CYTSecureManagerFactory::getPeriodoManager();
		$oPeriodo = $oPeriodoManager->getObjectByCode($oSolicitud->getPeriodo()->getOid());
		
		$year = $oPeriodo->getDs_periodo();
		$yearP = $year+1;
    	$params = array ($year,$yearP );		
		
		$subjectMail = htmlspecialchars(CdtFormatUtils::formatMessage( $msg, $params ), ENT_QUOTES, "UTF-8");
		
		
		$xtpl = new XTemplate( CYT_TEMPLATE_SOLICITUD_MAIL_ENVIAR );
		$xtpl->assign ( 'img_logo', WEB_PATH.'css/smile/images/sicadi_little.png' );
		$xtpl->assign('solicitud_titulo', $subjectMail);
		$xtpl->assign('year_label', CYT_LBL_SOLICITUD_MAIL_YEAR);
		$xtpl->assign('year', $oPeriodo->getDs_periodo());
		$xtpl->assign('investigador_label', CYT_LBL_SOLICITUD_MAIL_INVESTIGADOR);
		$xtpl->assign('investigador', htmlspecialchars($oSolicitud->getDocente()->getDs_apellido().', '.$oSolicitud->getDocente()->getDs_nombre(), ENT_QUOTES, "UTF-8"));
		$xtpl->assign('comment', $ds_comment);
		$xtpl->parse('main');		
		$bodyMail = $xtpl->text('main');
		
		
		
		
		
		
        if ($oSolicitud->getDs_mail() != "") {
				
         		CYTSecureUtils::sendMail($oSolicitud->getDocente()->getDs_nombre().' '.$oSolicitud->getDocente()->getDs_apellido(), $oSolicitud->getDs_mail(), $subjectMail, $bodyMail);
        }
        
	}

	public function cambiarEstado(Solicitud $oSolicitud, Estado $oEstado, $motivo){
		
	 	$oSolicitudEstado = new SolicitudEstado();
		$oSolicitudEstado->setSolicitud($oSolicitud);
		$oSolicitudEstado->setFechaDesde(date(DB_DEFAULT_DATETIME_FORMAT));
		$oSolicitudEstado->setEstado($oEstado);
		$oSolicitudEstado->setMotivo($motivo);
		$oUser = CdtSecureUtils::getUserLogged();
		$oSolicitudEstado->setUser($oUser);
		$oSolicitudEstado->setFechaUltModificacion(date(DB_DEFAULT_DATETIME_FORMAT));
	 	
	 	$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('solicitud_oid', $oSolicitud->getOid(), '=');
		$oCriteria->addNull('fechaHasta');
		$managerSolicitudEstado =  CYTSecureManagerFactory::getSolicitudEstadoManager();
		$solicitudEstado = $managerSolicitudEstado->getEntity($oCriteria);
		if (!empty($solicitudEstado)) {
			if ($solicitudEstado->getEstado()->getOid()!=$oEstado->getOid()) {// si el estado anterior es el mismo que el nuevo no hago nada
				$solicitudEstado->setFechaHasta(date(DB_DEFAULT_DATETIME_FORMAT));
				$solicitudEstado->setUser($oUser);
				$solicitudEstado->setFechaUltModificacion(date(DB_DEFAULT_DATETIME_FORMAT));
				$solicitudEstado->setSolicitud($oSolicitud);
				$managerSolicitudEstado->update($solicitudEstado);
				$managerSolicitudEstado->add($oSolicitudEstado);
			}
		}
		else
			$managerSolicitudEstado->add($oSolicitudEstado);
	 }
	
}
?>
