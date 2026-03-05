<?php

/**
 * Manager para Solicitud Proyecto
 *  
 * @author Marcos
 * @since 04-04-2023
 */
class OtrosProyectoManager extends EntityManager{

	public function getDAO(){
		return DAOFactory::getOtrosProyectoDAO();
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
