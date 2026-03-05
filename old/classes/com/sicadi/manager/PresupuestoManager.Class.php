<?php

/**
 * Manager para Presupuesto
 *  
 * @author Marcos
 * @since 22-11-2013
 */
class PresupuestoManager extends EntityManager{

	public function getDAO(){
		return DAOFactory::getPresupuestoDAO();
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
