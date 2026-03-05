<?php

/**
 * DAO para Solicitud
 *  
 * @author Marcos
 * @since 12-11-2013
 */
class SolicitudDAO extends EntityDAO {

	public function getTableName(){
		return CYT_TABLE_SOLICITUD;
	}
	
	public function getEntityFactory(){
		return new SolicitudFactory();
	}
	
	public function getFieldsToAdd($entity){
		$fieldsValues = array();
		
		$fieldsValues["cd_docente"] = $this->formatIfNull( $entity->getDocente()->getOid(), 'null' );
		$fieldsValues["cd_periodo"] = $this->formatIfNull( $entity->getPeriodo()->getOid(), 'null' );
		$fieldsValues["ds_mail"] = $this->formatString( $entity->getDs_mail() );
		$fieldsValues["bl_notificacion"] = $this->formatIfNull( $entity->getBl_notificacion(), '0' );
		$fieldsValues["nu_telefono"] = $this->formatString( $entity->getNu_telefono() );
		$fieldsValues["dt_fecha"] = $this->formatDate( $entity->getDt_fecha() );
		$fieldsValues["ds_calle"] = $this->formatString( $entity->getDs_calle() );
		$fieldsValues["nu_nro"] = $this->formatString( $entity->getNu_nro() );
		$fieldsValues["nu_piso"] = $this->formatString( $entity->getNu_piso() );
		$fieldsValues["ds_depto"] = $this->formatString( $entity->getDs_depto() );
		$fieldsValues["nu_cp"] = $this->formatString( $entity->getNu_cp() );
		$fieldsValues["dt_nacimiento"] = $this->formatDate( $entity->getDt_nacimiento() );
        $fieldsValues["ds_foto"] = $this->formatString( $entity->getDs_foto() );
        $fieldsValues["ds_otromail"] = $this->formatString( $entity->getDs_otromail() );
        $fieldsValues["ds_genero"] = $this->formatString( $entity->getDs_genero() );
        $fieldsValues["ds_orcid"] = $this->formatString( $entity->getDs_orcid() );
        $fieldsValues["ds_sedici"] = $this->formatString( $entity->getDs_sedici() );
        $fieldsValues["ds_scholar"] = $this->formatString( $entity->getDs_scholar() );
        $fieldsValues["ds_instagram"] = $this->formatString( $entity->getDs_instagram() );
        $fieldsValues["ds_twitter"] = $this->formatString( $entity->getDs_twitter() );
        $fieldsValues["ds_facebook"] = $this->formatString( $entity->getDs_facebook() );

		$fieldsValues["ds_titulogrado"] = $this->formatString( $entity->getDs_titulogrado() );
		$fieldsValues["cd_titulogrado"] = $this->formatIfNull( $entity->getTitulo()->getOid(), 'null' );
		$fieldsValues["dt_egresogrado"] = $this->formatDate( $entity->getDt_egresogrado() );
		$fieldsValues["ds_tituloposgrado"] = $this->formatString( $entity->getDs_tituloposgrado() );
		$fieldsValues["cd_tituloposgrado"] = $this->formatIfNull( $entity->getTituloposgrado()->getOid(), 'null' );
		$fieldsValues["dt_egresoposgrado"] = $this->formatDate( $entity->getDt_egresoposgrado() );
		$fieldsValues["bl_doctorado"] = $this->formatIfNull( $entity->getBl_doctorado(), '0' );
		$fieldsValues["cd_unidad"] = $this->formatIfNull( $entity->getLugarTrabajo()->getOid(), 'null');
		$fieldsValues["cd_cargo"] = $this->formatIfNull( $entity->getCargo()->getOid(), 'null' );
		$fieldsValues["cd_deddoc"] = $this->formatIfNull( $entity->getDeddoc()->getOid(), 'null' );
		$fieldsValues["cd_facultad"] = $this->formatIfNull( $entity->getFacultad()->getOid(), 'null' );
		$fieldsValues["cd_facultadplanilla"] = $this->formatIfNull( $entity->getFacultadplanilla()->getOid(), 'null' );
		$fieldsValues["bl_becario"] = $this->formatIfNull( $entity->getBl_becario(), '0' );
		$fieldsValues["bl_unlp"] = $this->formatIfNull( $entity->getBl_unlp(), '0' );
		$fieldsValues["ds_tipobeca"] = $this->formatString( $entity->getDs_tipobeca() );
		$fieldsValues["ds_orgbeca"] = $this->formatString( $entity->getDs_orgbeca() );
		$fieldsValues["dt_becadesde"] = $this->formatDate( $entity->getDt_becadesde() );
		$fieldsValues["dt_becahasta"] = $this->formatDate( $entity->getDt_becahasta() );
		$fieldsValues["cd_unidadbeca"] = $this->formatIfNull( $entity->getLugarTrabajoBeca()->getOid(), 'null' );
		$fieldsValues["bl_carrera"] = $this->formatIfNull( $entity->getBl_carrera(), '0' );
		$fieldsValues["cd_carrerainv"] = $this->formatIfNull( $entity->getCarrerainv()->getOid(), 'null' );
		$fieldsValues["cd_organismo"] = $this->formatIfNull( $entity->getOrganismo()->getOid(), 'null' );
		$fieldsValues["cd_unidadcarrera"] = $this->formatIfNull( $entity->getLugarTrabajoCarrera()->getOid(), 'null' );
		$fieldsValues["dt_ingreso"] = $this->formatDate( $entity->getDt_ingreso() );
		$fieldsValues["cd_categoria"] = $this->formatIfNull( $entity->getCategoria()->getOid(), 'null' );
        $fieldsValues["cd_equivalencia"] = $this->formatIfNull( $entity->getEquivalencia()->getOid(), 'null' );
        $fieldsValues["cd_categoriasicadi"] = $this->formatIfNull( $entity->getCategoriasicadi()->getOid(), 'null' );
		/*$fieldsValues["bl_director"] = $this->formatIfNull( $entity->getBl_director(), '0' );
		$fieldsValues["ds_objetivo"] = $this->formatString( $entity->getDs_objetivo() );*/
		
		$fieldsValues["ds_curriculum"] = $this->formatString( $entity->getDs_curriculum() );
		$fieldsValues["ds_resbeca"] = $this->formatString( $entity->getDs_resbeca() );
        $fieldsValues["ds_rescarrera"] = $this->formatString( $entity->getDs_rescarrera() );
		$fieldsValues["ds_disciplina"] = $this->formatString( $entity->getDs_disciplina() );

        $fieldsValues["ds_scopus"] = $this->formatString( $entity->getDs_scopus() );

        $fieldsValues["ds_informe1"] = $this->formatString( $entity->getDs_informe1() );
        $fieldsValues["ds_informe2"] = $this->formatString( $entity->getDs_informe2() );
        $fieldsValues["ds_informe3"] = $this->formatString( $entity->getDs_informe3() );
        $fieldsValues["nu_year1"] = $this->formatString( $entity->getNu_year1() );
        $fieldsValues["nu_year2"] = $this->formatString( $entity->getNu_year2() );
        $fieldsValues["nu_year3"] = $this->formatString( $entity->getNu_year3() );


        $fieldsValues["cd_areacarrera"] = $this->formatIfNull( $entity->getAreacarrera()->getOid(), 'null' );
        $fieldsValues["cd_subareacarrera"] = $this->formatIfNull( $entity->getSubareacarrera()->getOid(), 'null' );
		
		return $fieldsValues;
		
	}
	
	public function getFieldsToUpdate($entity){

		$fieldsValues = array();
		
		$fieldsValues["cd_docente"] = $this->formatIfNull( $entity->getDocente()->getOid(), 'null' );
		//$fieldsValues["cd_periodo"] = $this->formatIfNull( $entity->getPeriodo()->getOid(), 'null' );
		$fieldsValues["ds_mail"] = $this->formatString( $entity->getDs_mail() );
		$fieldsValues["bl_notificacion"] = $this->formatIfNull( $entity->getBl_notificacion(), '0' );
		$fieldsValues["nu_telefono"] = $this->formatString( $entity->getNu_telefono() );
		$fieldsValues["dt_fecha"] = $this->formatDate( $entity->getDt_fecha() );
		$fieldsValues["ds_calle"] = $this->formatString( $entity->getDs_calle() );
		$fieldsValues["nu_nro"] = $this->formatString( $entity->getNu_nro() );
		$fieldsValues["nu_piso"] = $this->formatString( $entity->getNu_piso() );
		$fieldsValues["ds_depto"] = $this->formatString( $entity->getDs_depto() );
		$fieldsValues["nu_cp"] = $this->formatString( $entity->getNu_cp() );
		$fieldsValues["dt_nacimiento"] = $this->formatDate( $entity->getDt_nacimiento() );
        $fieldsValues["ds_foto"] = $this->formatString( $entity->getDs_foto() );
        $fieldsValues["ds_otromail"] = $this->formatString( $entity->getDs_otromail() );
        $fieldsValues["ds_genero"] = $this->formatString( $entity->getDs_genero() );
        $fieldsValues["ds_orcid"] = $this->formatString( $entity->getDs_orcid() );
        $fieldsValues["ds_sedici"] = $this->formatString( $entity->getDs_sedici() );
        $fieldsValues["ds_scholar"] = $this->formatString( $entity->getDs_scholar() );
        $fieldsValues["ds_instagram"] = $this->formatString( $entity->getDs_instagram() );
        $fieldsValues["ds_twitter"] = $this->formatString( $entity->getDs_twitter() );
        $fieldsValues["ds_facebook"] = $this->formatString( $entity->getDs_facebook() );
		$fieldsValues["ds_titulogrado"] = $this->formatString( $entity->getDs_titulogrado() );
		$fieldsValues["cd_titulogrado"] = $this->formatIfNull( $entity->getTitulo()->getOid(), 'null' );
		$fieldsValues["dt_egresogrado"] = $this->formatDate( $entity->getDt_egresogrado() );
		$fieldsValues["ds_tituloposgrado"] = $this->formatString( $entity->getDs_tituloposgrado() );
		$fieldsValues["cd_tituloposgrado"] = $this->formatIfNull( $entity->getTituloposgrado()->getOid(), 'null' );
		$fieldsValues["dt_egresoposgrado"] = $this->formatDate( $entity->getDt_egresoposgrado() );
		$fieldsValues["bl_doctorado"] = $this->formatIfNull( $entity->getBl_doctorado(), '0' );
		$fieldsValues["cd_unidad"] = $this->formatIfNull( $entity->getLugarTrabajo()->getOid(), 'null');
		$fieldsValues["cd_cargo"] = $this->formatIfNull( $entity->getCargo()->getOid(), 'null' );
		$fieldsValues["cd_deddoc"] = $this->formatIfNull( $entity->getDeddoc()->getOid(), 'null' );
		$fieldsValues["cd_facultad"] = $this->formatIfNull( $entity->getFacultad()->getOid(), 'null' );
		$fieldsValues["cd_facultadplanilla"] = $this->formatIfNull( $entity->getFacultadplanilla()->getOid(), 'null' );
		$fieldsValues["bl_becario"] = $this->formatIfNull( $entity->getBl_becario(), '0' );
		$fieldsValues["bl_unlp"] = $this->formatIfNull( $entity->getBl_unlp(), '0' );
		$fieldsValues["ds_tipobeca"] = $this->formatString( $entity->getDs_tipobeca() );
		$fieldsValues["ds_orgbeca"] = $this->formatString( $entity->getDs_orgbeca() );
		$fieldsValues["dt_becadesde"] = $this->formatDate( $entity->getDt_becadesde() );
		$fieldsValues["dt_becahasta"] = $this->formatDate( $entity->getDt_becahasta() );
		$fieldsValues["cd_unidadbeca"] = $this->formatIfNull( $entity->getLugarTrabajoBeca()->getOid(), 'null' );
		$fieldsValues["bl_carrera"] = $this->formatIfNull( $entity->getBl_carrera(), '0' );
		$fieldsValues["cd_carrerainv"] = $this->formatIfNull( $entity->getCarrerainv()->getOid(), 'null' );
		$fieldsValues["cd_organismo"] = $this->formatIfNull( $entity->getOrganismo()->getOid(), 'null' );
		$fieldsValues["cd_unidadcarrera"] = $this->formatIfNull( $entity->getLugarTrabajoCarrera()->getOid(), 'null' );
		$fieldsValues["dt_ingreso"] = $this->formatDate( $entity->getDt_ingreso() );
		$fieldsValues["cd_categoria"] = $this->formatIfNull( $entity->getCategoria()->getOid(), 'null' );
        $fieldsValues["cd_equivalencia"] = $this->formatIfNull( $entity->getEquivalencia()->getOid(), 'null' );
        $fieldsValues["cd_categoriasicadi"] = $this->formatIfNull( $entity->getCategoriasicadi()->getOid(), 'null' );
		/*$fieldsValues["bl_director"] = $this->formatIfNull( $entity->getBl_director(), '0' );
		$fieldsValues["ds_objetivo"] = $this->formatString( $entity->getDs_objetivo() );*/
		
		$fieldsValues["ds_curriculum"] = $this->formatString( $entity->getDs_curriculum() );
		$fieldsValues["ds_resbeca"] = $this->formatString( $entity->getDs_resbeca() );
        $fieldsValues["ds_rescarrera"] = $this->formatString( $entity->getDs_rescarrera() );
			
		$fieldsValues["nu_puntaje"] = $this->formatIfNull( $entity->getNu_puntaje(), '0' );
		$fieldsValues["nu_diferencia"] = $this->formatIfNull( $entity->getNu_diferencia(), '0' );
		$fieldsValues["ds_disciplina"] = $this->formatString( $entity->getDs_disciplina() );

        $fieldsValues["ds_scopus"] = $this->formatString( $entity->getDs_scopus() );

        $fieldsValues["nu_year1"] = $this->formatString( $entity->getNu_year1() );
        $fieldsValues["nu_year2"] = $this->formatString( $entity->getNu_year2() );
        $fieldsValues["nu_year3"] = $this->formatString( $entity->getNu_year3() );
        $fieldsValues["ds_informe1"] = $this->formatString( $entity->getDs_informe1() );
        $fieldsValues["ds_informe2"] = $this->formatString( $entity->getDs_informe2() );
        $fieldsValues["ds_informe3"] = $this->formatString( $entity->getDs_informe3() );


        $fieldsValues["cd_areacarrera"] = $this->formatIfNull( $entity->getAreacarrera()->getOid(), 'null' );
        $fieldsValues["cd_subareacarrera"] = $this->formatIfNull( $entity->getSubareacarrera()->getOid(), 'null' );

		return $fieldsValues;
	}
	
	
	public function getIdFieldName(){
		return "cd_solicitud";
	}
	
	
	public function getFromToSelect(){
		
		$tSolicitud = $this->getTableName();
		
		$tDocente = CYTSecureDAOFactory::getDocenteDAO()->getTableName();
		$tFacultad = CYTSecureDAOFactory::getFacultadDAO()->getTableName();
		$tPeriodo = CYTSecureDAOFactory::getPeriodoDAO()->getTableName();
		
		$tCat = CYTSecureDAOFactory::getCatDAO()->getTableName();
		$tEstado = CYTSecureDAOFactory::getEstadoDAO()->getTableName();
		$tSolicitudEstado = CYTSecureDAOFactory::getSolicitudEstadoDAO()->getTableName();
		$tLugarTrabajo = CYTSecureDAOFactory::getLugarTrabajoDAO()->getTableName();
		$tCargo = CYTSecureDAOFactory::getCargoDAO()->getTableName();
		$tDeddoc = CYTSecureDAOFactory::getDeddocDAO()->getTableName();
		$tCarrerainv = CYTSecureDAOFactory::getCarrerainvDAO()->getTableName();
		$tOrganismo = CYTSecureDAOFactory::getOrganismoDAO()->getTableName();
		$tCategoria = CYTSecureDAOFactory::getCategoriaDAO()->getTableName();
        $tEquivalencia = DAOFactory::getEquivalenciaDAO()->getTableName();
        $tCategoria = CYTSecureDAOFactory::getCategoriaDAO()->getTableName();
        $tCategoriasicadi = DAOFactory::getCategoriasicadiDAO()->getTableName();
		
		$tTitulo = CYTSecureDAOFactory::getTituloDAO()->getTableName();

        $tArea = DAOFactory::getAreaDAO()->getTableName();
        $tSubarea = DAOFactory::getSubareaDAO()->getTableName();
		
		
		
        $sql  = parent::getFromToSelect();
       
        $sql .= " LEFT JOIN " . $tFacultad . " FacultadPlanilla ON($tSolicitud.cd_facultadplanilla = FacultadPlanilla.cd_facultad)";
        $sql .= " LEFT JOIN " . $tDocente . " ON($tSolicitud.cd_docente = $tDocente.cd_docente)";
        $sql .= " LEFT JOIN " . $tPeriodo . " ON($tSolicitud.cd_periodo = $tPeriodo.cd_periodo)";
       
        $sql .= " LEFT JOIN " . $tCat . " ON(FacultadPlanilla.cd_cat = $tCat.cd_cat)";
        $sql .= " LEFT JOIN " . $tSolicitudEstado . " ON($tSolicitudEstado.solicitud_oid = $tSolicitud.cd_solicitud)";
        $sql .= " LEFT JOIN " . $tEstado . " ON($tSolicitudEstado.estado_oid = $tEstado.cd_estado)";
        $sql .= " LEFT JOIN " . $tLugarTrabajo . " ON($tSolicitud.cd_unidad = $tLugarTrabajo.cd_unidad)";
        $sql .= " LEFT JOIN " . $tCargo . " ON($tSolicitud.cd_cargo = $tCargo.cd_cargo)";
        $sql .= " LEFT JOIN " . $tDeddoc . " ON($tSolicitud.cd_deddoc = $tDeddoc.cd_deddoc)";
        $sql .= " LEFT JOIN " . $tFacultad . " ON($tSolicitud.cd_facultad = $tFacultad.cd_facultad)";
        $sql .= " LEFT JOIN " . $tLugarTrabajo . " LugarTrabajoBeca ON($tSolicitud.cd_unidadbeca = LugarTrabajoBeca.cd_unidad)";
       	$sql .= " LEFT JOIN " . $tLugarTrabajo . " LugarTrabajoCarrera ON($tSolicitud.cd_unidadcarrera = LugarTrabajoCarrera.cd_unidad)";
       	$sql .= " LEFT JOIN " . $tOrganismo . " ON($tSolicitud.cd_organismo = $tOrganismo.cd_organismo)";
       	$sql .= " LEFT JOIN " . $tCarrerainv . " ON($tSolicitud.cd_carrerainv = $tCarrerainv.cd_carrerainv)";
       	$sql .= " LEFT JOIN " . $tCategoria . " ON($tSolicitud.cd_categoria = $tCategoria.cd_categoria)";
        $sql .= " LEFT JOIN " . $tEquivalencia . " ON($tSolicitud.cd_equivalencia = $tEquivalencia.cd_equivalencia)";
        $sql .= " LEFT JOIN " . $tCategoriasicadi . " ON($tSolicitud.cd_categoriasicadi = $tCategoriasicadi.cd_categoriasicadi)";
       
        $sql .= " LEFT JOIN " . $tTitulo . " ON($tSolicitud.cd_titulogrado = $tTitulo.cd_titulo)";
        $sql .= " LEFT JOIN " . $tTitulo . " Tituloposgrado ON($tSolicitud.cd_tituloposgrado = Tituloposgrado.cd_titulo)";

        $sql .= " LEFT JOIN " . $tArea . " ON($tSolicitud.cd_areacarrera = $tArea.cd_area)";
        $sql .= " LEFT JOIN " . $tSubarea . " ON($tSolicitud.cd_subareacarrera = $tSubarea.cd_subarea)";



        
        
        return $sql;
	}
	
	public function getFieldsToSelect(){
		
		
		$tDocente = CYTSecureDAOFactory::getDocenteDAO()->getTableName();
		$tFacultad = CYTSecureDAOFactory::getFacultadDAO()->getTableName();
		$tFacultadPlanilla = 'FacultadPlanilla';
		$tPeriodo = CYTSecureDAOFactory::getPeriodoDAO()->getTableName();
		
		$tCat = CYTSecureDAOFactory::getCatDAO()->getTableName();
		
		
		$fields = parent::getFieldsToSelect();
        
        $fields[] = "$tDocente.cd_docente as " . $tDocente . "_oid ";
        $fields[] = "$tDocente.ds_nombre as " . $tDocente . "_ds_nombre ";
        $fields[] = "$tDocente.ds_apellido as " . $tDocente . "_ds_apellido ";
        $fields[] = "$tDocente.nu_documento as " . $tDocente . "_nu_documento ";
        $fields[] = "$tDocente.nu_precuil as " . $tDocente . "_nu_precuil ";
        $fields[] = "$tDocente.nu_postcuil as " . $tDocente . "_nu_postcuil ";
                
        $fields[] = "$tFacultad.cd_facultad as " . $tFacultad . "_oid ";
        $fields[] = "$tFacultad.ds_facultad as " . $tFacultad . "_ds_facultad ";
        
        $fields[] = "$tFacultadPlanilla.cd_facultad as " . $tFacultadPlanilla . "_oid ";
        $fields[] = "$tFacultadPlanilla.ds_facultad as " . $tFacultadPlanilla . "_ds_facultad ";
        
        $fields[] = "$tPeriodo.cd_periodo as " . $tPeriodo . "_oid ";
        $fields[] = "$tPeriodo.ds_periodo as " . $tPeriodo . "_ds_periodo ";
        
       
        
        $fields[] = "$tCat.cd_cat as " . $tCat . "_oid ";
        $fields[] = "$tCat.ds_cat as " . $tCat . "_ds_cat ";
        
        $tEstado = CYTSecureDAOFactory::getEstadoDAO()->getTableName();
		$fields[] = "$tEstado.cd_estado as " . $tEstado . "_oid ";
        $fields[] = "$tEstado.ds_estado as " . $tEstado . "_ds_estado ";
        
        $tSolicitudEstado = CYTSecureDAOFactory::getSolicitudEstadoDAO()->getTableName();
		$fields[] = "$tSolicitudEstado.oid as " . $tSolicitudEstado . "_oid ";
        $fields[] = "$tSolicitudEstado.fechaDesde as " . $tSolicitudEstado . "_fechaDesde ";
        $fields[] = "$tSolicitudEstado.fechaHasta as " . $tSolicitudEstado . "_fechaHasta ";
        
        $tLugarTrabajo = CYTSecureDAOFactory::getLugarTrabajoDAO()->getTableName();
        $fields[] = "$tLugarTrabajo.cd_unidad as " . $tLugarTrabajo . "_oid ";
        $fields[] = "$tLugarTrabajo.ds_unidad as " . $tLugarTrabajo . "_ds_unidad ";
        $fields[] = "$tLugarTrabajo.ds_sigla as " . $tLugarTrabajo . "_ds_sigla ";
        
        $tCargo = CYTSecureDAOFactory::getCargoDAO()->getTableName();
        $fields[] = "$tCargo.cd_cargo as " . $tCargo . "_oid ";
        $fields[] = "$tCargo.ds_cargo as " . $tCargo . "_ds_cargo ";
        
        $tDeddoc = CYTSecureDAOFactory::getDeddocDAO()->getTableName();
        $fields[] = "$tDeddoc.cd_deddoc as " . $tDeddoc . "_oid ";
        $fields[] = "$tDeddoc.ds_deddoc as " . $tDeddoc . "_ds_deddoc ";
        
        $tFacultad = CYTSecureDAOFactory::getFacultadDAO()->getTableName();
        $fields[] = "$tFacultad.cd_facultad as " . $tFacultad . "_oid ";
        $fields[] = "$tFacultad.ds_facultad as " . $tFacultad . "_ds_facultad ";
        
        
        $fields[] = "LugarTrabajoBeca.cd_unidad as LugarTrabajoBeca_oid ";
        $fields[] = "LugarTrabajoBeca.ds_unidad as LugarTrabajoBeca_ds_unidad ";
        $fields[] = "LugarTrabajoBeca.ds_sigla as LugarTrabajoBeca_ds_sigla ";
        
        $fields[] = "LugarTrabajoCarrera.cd_unidad as LugarTrabajoCarrera_oid ";
        $fields[] = "LugarTrabajoCarrera.ds_unidad as LugarTrabajoCarrera_ds_unidad ";
        $fields[] = "LugarTrabajoCarrera.ds_sigla as LugarTrabajoCarrera_ds_sigla ";
        
        $tCarrerainv = CYTSecureDAOFactory::getCarrerainvDAO()->getTableName();
        $fields[] = "$tCarrerainv.cd_carrerainv as " . $tCarrerainv . "_oid ";
        $fields[] = "$tCarrerainv.ds_carrerainv as " . $tCarrerainv . "_ds_carrerainv ";
        
        $tOrganismo = CYTSecureDAOFactory::getOrganismoDAO()->getTableName();
        $fields[] = "$tOrganismo.cd_organismo as " . $tOrganismo . "_oid ";
        $fields[] = "$tOrganismo.ds_organismo as " . $tOrganismo . "_ds_organismo ";
        
        $tCategoria = CYTSecureDAOFactory::getCategoriaDAO()->getTableName();
        $fields[] = "$tCategoria.cd_categoria as " . $tCategoria . "_oid ";
        $fields[] = "$tCategoria.ds_categoria as " . $tCategoria . "_ds_categoria ";

        $tEquivalencia = DAOFactory::getEquivalenciaDAO()->getTableName();
        $fields[] = "$tEquivalencia.cd_equivalencia as " . $tEquivalencia . "_oid ";
        $fields[] = "$tEquivalencia.ds_equivalencia as " . $tEquivalencia . "_ds_equivalencia ";

        $tCategoriasicadi = DAOFactory::getCategoriasicadiDAO()->getTableName();
        $fields[] = "$tCategoriasicadi.cd_categoriasicadi as " . $tCategoriasicadi . "_oid ";
        $fields[] = "$tCategoriasicadi.ds_categoriasicadi as " . $tCategoriasicadi . "_ds_categoriasicadi ";
        
        $tTitulo = CYTSecureDAOFactory::getTituloDAO()->getTableName();
        $fields[] = "$tTitulo.cd_titulo as " . $tTitulo . "_oid ";
        $fields[] = "$tTitulo.ds_titulo as " . $tTitulo . "_ds_titulo ";
        
        $fields[] = "Tituloposgrado.cd_titulo as Tituloposgrado_oid ";
        $fields[] = "Tituloposgrado.ds_titulo as Tituloposgrado_ds_titulo ";

        $tArea = DAOFactory::getAreaDAO()->getTableName();
        $fields[] = "$tArea.cd_area as " . $tArea . "_oid ";
        $fields[] = "$tArea.ds_area as " . $tArea . "_ds_area ";

        $tSubarea = DAOFactory::getSubareaDAO()->getTableName();
        $fields[] = "$tSubarea.cd_subarea as " . $tSubarea . "_oid ";
        $fields[] = "$tSubarea.ds_subarea as " . $tSubarea . "_ds_subarea ";


        return $fields;
	}
	
	

	
	
}
?>