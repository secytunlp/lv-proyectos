<?php

/**
 * Helper DAO para administrar en sesión los proyectos anteriores de una 
 * solicitud.
 *  
 * @author Marcos
 * @since 04-04-2023
 */
class SolicitudProyectoSessionDAO extends EntityDAO {

	public function getFieldsToAdd($entity){}
	
	public function getFieldsToUpdate($entity){}
	
	public function getId($entity){}
		
	public function getIdFieldName(){}
	
	public function setId($entity, $id){}
	
	public function getTableName(){}
	
	public function getEntityFactory(){}
	
	public function getVariableSessionName(){
		return "solicitudproyectos";
	}
	
    /**
     * se persiste la nueva entity
     * @param $entity entity a persistir.
     */
    public function addEntity( $entity, $idConn=0 ) {
    	
    	$proyectos = unserialize( $_SESSION[ $this->getVariableSessionName() ] );
    	
    	if( empty($proyectos) )
    		$proyectos = new ItemCollection();
    	$entity->setBl_agregado(1);
    	$this->validateOnAdd($entity);
    	$proyectos->addItem($entity);
        
        $_SESSION[$this->getVariableSessionName()] = serialize($proyectos);
        
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
    	
    	$proyectos = unserialize( $_SESSION[$this->getVariableSessionName()] );
    	
    	
    	$nuevosProyectos = new ItemCollection();
    	foreach ($proyectos as $oProyecto) {
    		
    		if( $oProyecto->getDs_codigo() != $oid ){
    			$nuevosProyectos->addItem($oProyecto);
    		}
    	}
    	
        $_SESSION[$this->getVariableSessionName()] = serialize($nuevosProyectos);
    	
    }

    /**
     * quitamos todos los proyectos de sesión
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
			$proyectos = unserialize( $_SESSION[$this->getVariableSessionName()] );
		else 
			$proyectos = new ItemCollection();	

		return $proyectos;
    }

    /**
     * se obtiene la cantidad de entities dado el filtro de búsqueda
     * @param CdtSearchCriteria $oCriteria filtro de búsqueda.
     * @return int
     */
    public function getEntitiesCount(CdtSearchCriteria $oCriteria, $idConn=0) {
        
    	$proyectos = unserialize($this->getVariableSessionName() );

        return $proyectos->size();
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
    
/**
	 * (non-PHPdoc)
	 * @see classes/com/entities/manager/EntityManager::validateOnAdd()
	 */
    protected function validateOnAdd(Entity $entity){
    	
    	$error = '';
    		
   
    	if(CYTSecureUtils::formatDateToPersist($entity->getDt_desdeproyecto())>CYTSecureUtils::formatDateToPersist($entity->getDt_hastaproyecto())){
    		$error .= CYT_MSG_PROYECTO_DESDE_MAYOR.'<br>';
    			
    	}
    	
    	
    	if((CYTSecureUtils::formatDateToPersist($entity->getDt_hastaproyecto())>CYTSecureUtils::formatDateToPersist(CYT_PROYECTO_RANGO_FIN))){
    			$error .= CYT_MSG_PROYECTOS_FUERA_RANGO.'<br>';
    			
    		}
    		
    		
    		
    	
    	if ($error) {
    		throw new GenericException( $error );
    	}
    	
    }
		
}
?>