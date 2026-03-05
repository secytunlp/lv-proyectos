<?php

/**
 * DAO para IntegranteAgencia
 *
 * @author Marcos
 * @since 14-08-2023
 */
class IntegranteAgenciaDAO extends EntityDAO {

    public function getTableName(){
        return CYT_TABLE_INTEGRANTE_AGENCIA;
    }

    public function getEntityFactory(){
        return new IntegranteAgenciaFactory();
    }

    public function getFieldsToAdd($entity){


    }




    public function getFromToSelect(){

        $tIntegrante = $this->getTableName();

        $tDocente = CYTSecureDAOFactory::getDocenteDAO()->getTableName();




        $sql  = parent::getFromToSelect();

        $sql .= " LEFT JOIN " . $tDocente . " ON($tIntegrante.nu_documento = $tDocente.nu_documento)";


        return $sql;
    }

    public function getFieldsToSelect(){
        $fields = parent::getFieldsToSelect();


        $tDocente = CYTSecureDAOFactory::getDocenteDAO()->getTableName();
        $fields[] = "$tDocente.cd_docente as " . $tDocente . "_oid ";
        $fields[] = "$tDocente.ds_apellido as " . $tDocente . "_ds_apellido ";
        $fields[] = "$tDocente.ds_nombre as " . $tDocente . "_ds_nombre ";


        return $fields;
    }





}
?>