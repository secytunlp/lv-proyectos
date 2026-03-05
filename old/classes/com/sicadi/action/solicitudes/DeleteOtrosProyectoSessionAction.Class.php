<?php 

/**
 * Acción para quitar un proyecto de solicitud.
 * Es sólo en sesión para ir armando la solicitud.
 * 
 * @author Marcos
 * @since 04-04-2023
 * 
 */
class DeleteOtrosProyectoSessionAction extends EditEntityAction{

	protected function edit( $entity ){
		
		$otrosProyecto = CdtUtils::getParam("item_oid");
		
		//el oid representa el dato "otrosProyecto" ya que no hay entity relacionada
		$this->getEntityManager()->delete( $otrosProyecto );

		
		//vamos a retornar por json los otrosProyectos de la encomienda.
		
		//usamos el renderer para reutilizar lo que mostramos de los otrosProyectos.
		$renderer = new CMPSolicitudFormRenderer();
		$otrosProyectos = array();
		foreach ($this->getEntityManager()->getEntities(new CdtSearchCriteria()) as $otrosProyecto) {
			$otrosProyecto->setDt_desdeproyecto(CYTSecureUtils::formatDateToPersist($otrosProyecto->getDt_desdeproyecto()));
			$otrosProyecto->setDt_hastaproyecto(CYTSecureUtils::formatDateToPersist($otrosProyecto->getDt_hastaproyecto()));
			$otrosProyectos[] = $renderer->buildArrayOtrosProyecto($otrosProyecto);
		}		
		
		return array("otrosProyectos" => $otrosProyectos,
						"otrosProyectoColumns" => $renderer->getOtrosProyectoColumns(),
						"otrosProyectoColumnsAlign" => $renderer->getOtrosProyectoColumnsAlign(),
						"otrosProyectoColumnsLabels" => $renderer->getOtrosProyectoColumnsLabels());
	}


	/**
	 * (non-PHPdoc)
	 * @see classes/com/gestion/action/entities/EditEntityAction::getNewFormInstance()
	 */
	public function getNewFormInstance(){
		return new CMPOtrosProyectoForm();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see classes/com/gestion/action/entities/EditEntityAction::getNewEntityInstance()
	 */
	public function getNewEntityInstance(){
		return new OtrosProyecto();
	}
	
	protected function getEntityManager(){
		return new OtrosProyectoSessionManager();
	}

}
