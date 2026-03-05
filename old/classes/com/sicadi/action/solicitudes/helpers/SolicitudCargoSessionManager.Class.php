<?php

/**
 * Helper manager para administrar en sesión los cargos de una solicitud
 *
 * @author Marcos
 * @since 09-06-2023
 */
class SolicitudCargoSessionManager extends EntityManager{

	public function getDAO(){
		return new SolicitudCargoSessionDAO();
	}

	public function select($oid,$checked) {
		$this->getDAO()->selectEntity($oid,$checked);
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
