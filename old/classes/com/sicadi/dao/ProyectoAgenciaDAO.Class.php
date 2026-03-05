<?php

/**
 * DAO para ProyectoAgencia
 *
 * @author Marcos
 * @since 14-08-2023
 */
class ProyectoAgenciaDAO extends EntityDAO {

    public function getTableName(){
        return CYT_TABLE_PROYECTO_AGENCIA;
    }

    public function getEntityFactory(){
        return new ProyectoAgenciaFactory();
    }

    public function getFieldsToAdd($entity){


    }



    public function getIdFieldName(){
        return "cd_proyecto";
    }


    public function getFromToSelect(){

        $tProyecto = $this->getTableName();

        $tDocente = CYTSecureDAOFactory::getDocenteDAO()->getTableName();

        $tIntegrante = DAOFactory::getIntegranteAgenciaDAO()->getTableName();



        $sql  = parent::getFromToSelect();


        $sql .= " LEFT JOIN " . $tIntegrante . " ON($tProyecto.cd_proyecto = $tIntegrante.cd_proyecto)";
        $sql .= " LEFT JOIN " . $tDocente . " ON($tIntegrante.nu_documento = $tDocente.nu_documento)";


        $sql .= " LEFT JOIN " . $tIntegrante . " DIR ON($tProyecto.cd_proyecto = DIR.cd_proyecto)";
        $sql .= " LEFT JOIN " . $tDocente . " DOCDIR ON(DIR.nu_documento = DOCDIR.nu_documento)";




        return $sql;
    }

    public function getFieldsToSelect(){



        $tFacultad = CYTSecureDAOFactory::getFacultadDAO()->getTableName();


        $fields = parent::getFieldsToSelect();



        $tIntegrante = DAOFactory::getIntegranteAgenciaDAO()->getTableName();
        $fields[] = "$tIntegrante.dt_alta as " . $tIntegrante . "_dt_alta ";
        $fields[] = "$tIntegrante.dt_baja as " . $tIntegrante . "_dt_baja ";







        $tDocente = 'DOCDIR';
        $fields[] = "$tDocente.cd_docente as " . $tDocente . "_oid ";
        $fields[] = "$tDocente.ds_apellido as " . $tDocente . "_ds_apellido ";
        $fields[] = "$tDocente.ds_nombre as " . $tDocente . "_ds_nombre ";





        return $fields;
    }





}
?>