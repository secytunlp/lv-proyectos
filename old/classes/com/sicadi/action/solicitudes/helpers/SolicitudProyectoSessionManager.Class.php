<?php

/**
 * Helper manager para administrar en sesión los proyectos anteriores de una solicitud
 *  
 * @author Marcos
 * @since 04-04-2023
 */
class SolicitudProyectoSessionManager extends EntityManager{

	public function getDAO(){
		return new SolicitudProyectoSessionDAO();
	}
	
	public function deleteAll() {
    	$this->getDAO()->deleteAll();
    }
    
    public function setEntities( $entities ) {
    	$this->getDAO()->setEntities($entities);
    }
    
    protected function validateOnAdd(Entity $entity){
    	
    	//TODO validaciones	
    }
    
	protected function validateOnUpdate(Entity $entity){
		//TODO validaciones
	}

	protected function validateOnDelete($id){
		//TODO validaciones
	}	
}

?>
