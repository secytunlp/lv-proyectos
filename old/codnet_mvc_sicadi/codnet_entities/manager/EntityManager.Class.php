<?php

/**
 * Manager para las entities
 *  
 * @author Bernardo
 * @since 05-03-2013
 */
abstract class EntityManager implements ICdtList, IObjectFinder{

	/**
	 * obtiene el dao para las entities.
	 * return EntityDAO
	 */
	public abstract function getDAO();
	
    /**
     * se agrega la nueva entity
     * @param Entity $entity entity a agregar.
     */
    public function add(Entity $entity) {
    	
        $this->validateOnAdd( $entity );
        
        $this->getDAO()->addEntity($entity);
    }

    /**
     * se modifica la entity
     * @param (Entity $entity) entity a modificar.
     */
    public function update(Entity $entity) {
    	
    	$this->validateOnUpdate( $entity );
    	
        //persistir en la bbdd.
        $this->getDAO()->updateEntity($entity);
    }
    
    public function getMsgOidRequired(){
    	return CDT_ENTITIES_MSG_ENTITY_OID_REQUIRED;
    }
    

    /**
     * se elimina la entity
     * @param int identificador de la entity a eliminar.
     */
    public function delete($id) {
        
    	$this->validateOnDelete( $id );
    	
        $this->getDAO()->deleteEntity($id);
    }


	public function getEntityById($id) {
		
        return $this->getDAO()->getEntityById($id);
        
    }
    
    /**
     * se obtiene un entity dado el filtro de búsqueda
     * @param CdtSearchCriteria $oCriteria filtro de búsqueda.
     * @return Cliente
     */
    public function getEntity(CdtSearchCriteria $oCriteria) {
    	$oCriteria->setTableName( $this->getDAO()->getTableName() );
        return $this->getDAO()->getEntity($oCriteria);
    }

    //	interface ICdtList

    /**
     * (non-PHPdoc)
     * @see ICdtList::getEntities();
     */
    public function getEntities(CdtSearchCriteria $oCriteria) {
    	$oCriteria->setTableName( $this->getDAO()->getTableName() );
        return $this->getDAO()->getEntities($oCriteria);
    }

    /**
     * (non-PHPdoc)
     * @see ICdtList::getEntitiesCount();
     */
    public function getEntitiesCount(CdtSearchCriteria $oCriteria) {
    	$oCriteria->setTableName( $this->getDAO()->getTableName() );
        return $this->getDAO()->getEntitiesCount($oCriteria);
    }

    
    protected function validateOnAdd(Entity $entity){
    	
    }
    
	protected function validateOnUpdate(Entity $entity){
	
		if ($entity == null || ($entity->getOid() == "")) {
            throw new GenericException( $this->getMsgOidRequired() );
        }
		
	}

	protected function validateOnDelete($id){
		
		if ( empty($id) ) {
            throw new GenericException( $this->getMsgOidRequired() );
        }
	}


	/**
	 * (non-PHPdoc)
	 * @see interfaces/IObjectFinder::getObjectByCode()
	 */
	function getObjectByCode( $text, $parent=null ){
		
		return self::getEntityById( $text );	
	}

	/**
	 * (non-PHPdoc)
	 * @see interfaces/IObjectFinder::getObjectCode()
	 */
	function getObjectCode( $entity ){
		return $entity->getOid();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see interfaces/IObjectFinder::getObjectLabel()
	 */
	function getObjectLabel( $entity ){
		return $entity . "";
	}

	/**
	 * (non-PHPdoc)
	 * @see interfaces/IObjectFinder::getObjectAttributes()
	 */
	function getObjectAttributes( $entity, $attributes ){
		$result = array();
		if(!empty($attributes)){

			$attributes = explode("," , $attributes );

			foreach ($attributes as $attribute) {
				$attribute = trim($attribute);
				
				$value = CdtReflectionUtils::doGetter( $entity, $attribute );
				$result[$attribute] = $value   ;
			}
		}
		return $result;
	}
}
?>
