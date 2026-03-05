<?php

/**
 * Helper DAO para administrar en sesión los cargos de una
 * solicitud.
 *
 * @author Marcos
 * @since 09-06-2023
 */
class SolicitudCargoSessionDAO extends EntityDAO {

    public function getFieldsToAdd($entity){}

    public function getFieldsToUpdate($entity){}

    public function getId($entity){}

    public function getIdFieldName(){}

    public function setId($entity, $id){}

    public function getTableName(){}

    public function getEntityFactory(){}

    public function getVariableSessionName(){
        return "solicitud_cargos";
    }

    /**
     * se persiste la nueva entity
     * @param $entity entity a persistir.
     */
    public function addEntity( $entity, $idConn=0 ) {

        $cargos = unserialize( $_SESSION[ $this->getVariableSessionName() ] );

        if( empty($cargos) )
            $cargos = new ItemCollection();
        //if (!$cargos->existObjectComparator($entity, new FacultadComparator())) {
        $cargos->addItem($entity);
        //}

        $_SESSION[$this->getVariableSessionName()] = serialize($cargos);

    }

    /**
     */
    public function setEntities( $entities, $idConn=0 ) {

        $_SESSION[$this->getVariableSessionName()] = serialize($entities);

    }

    /**
     * se modifica la entity
     * @param $entity entity a modificar.
     */
    public function updateEntity($entity, $idConn=0) {
        //TODO
    }

    /**
     * se elimina la entity
     * @param $id identificador de la entity a eliminar.
     */
    public function deleteEntity($oid, $idConn=0) {

        $oid = urldecode($oid);

        $solicitudCargos = unserialize( $_SESSION[$this->getVariableSessionName()] );

        //el oid representaría la facultad??
        $nuevosCargos = new ItemCollection();
        foreach ($solicitudCargos as $oSolicitudCargo) {

            if( $oSolicitudCargo->getCargo()->getOid() != $oid ){
                $nuevosCargos->addItem($oSolicitudCargo);
            }
        }

        $_SESSION[$this->getVariableSessionName()] = serialize($nuevosCargos);

    }

    /**
     * se selecciona la entity
     * @param $ id identificador de la entity a eliminar.
     */
    public function selectEntity($oid,$checked, $idConn=0) {

        $oid = urldecode($oid);

        $solicitudCargos = unserialize( $_SESSION[$this->getVariableSessionName()] );

        //el oid representaría la facultad??
        $nuevosCargos = new ItemCollection();
        foreach ($solicitudCargos as $oSolicitudCargo) {

            if( $oSolicitudCargo->getCargo()->getOid() == $oid ){
                $seleccionado = ($checked=='true')?1:0;
                //CdtUtils::log('Marcos: '.$checked.' - '.$seleccionado);
                $oSolicitudCargo->setBl_seleccionado($seleccionado);
            }
            //else $oSolicitudCargo->setBl_seleccionado(0);
            $nuevosCargos->addItem($oSolicitudCargo);
        }

        $_SESSION[$this->getVariableSessionName()] = serialize($nuevosCargos);

    }

    /**
     * quitamos todos los cargos de sesión
     */
    public function deleteAll() {
        unset( $_SESSION[$this->getVariableSessionName()] ) ;

    }
    /**
     * se obtiene una colección de entities dado el filtro de búsqueda
     * @param CdtSearchCriteria $oCriteria filtro de búsqueda.
     * @return ItemCollection
     */
    public function getEntities(CdtSearchCriteria $oCriteria, $idConn=0) {

        if(isset($_SESSION[$this->getVariableSessionName()]))
            $cargos = unserialize( $_SESSION[$this->getVariableSessionName()] );
        else
            $cargos = new ItemCollection();

        return $cargos;
    }

    /**
     * se obtiene la cantidad de entities dado el filtro de búsqueda
     * @param CdtSearchCriteria $oCriteria filtro de búsqueda.
     * @return int
     */
    public function getEntitiesCount(CdtSearchCriteria $oCriteria, $idConn=0) {

        $cargos = unserialize($this->getVariableSessionName() );

        return $cargos->size();
    }

    /**
     * se obtiene un entity dado el filtro de búsqueda
     * @param CdtSearchCriteria $oCriteria filtro de búsqueda.
     * @return Entity
     */
    public function getEntity(CdtSearchCriteria $oCriteria, $idConn=0) {
        //TODO
    }

    public function getEntityById($id) {
        //TODO
    }

}
?>