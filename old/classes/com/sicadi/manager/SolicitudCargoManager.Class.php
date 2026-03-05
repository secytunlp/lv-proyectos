<?php

/**
 * Manager para Solicitud Cargo
 *  
 * @author Marcos
 * @since 12-06-2023
 */
class SolicitudCargoManager extends EntityManager{

	public function getDAO(){
		return DAOFactory::getSolicitudCargoDAO();
	}

	public function add(Entity $entity) {
    	
		parent::add($entity);
		
    }	
    
     public function update(Entity $entity) {
     	
     	
		parent::update($entity);
     }

    
    
    
	/**
     * se elimina la entity
     * @param int identificador de la entity a eliminar.
     */
    public function delete($id) {
        
		parent::delete( $id );
		
    	
    }
	
	
	
	
}
?>
