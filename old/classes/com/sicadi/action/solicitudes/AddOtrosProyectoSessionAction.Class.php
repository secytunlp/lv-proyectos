<?php

/**
 * Acción para dar de alta un proyecto anterior de solicitud.
 * El alta es sólo en sesión para ir armando la solicitud.
 *
 * @author Marcos
 * @since 04-04-2023
 *
 */
class AddOtrosProyectoSessionAction extends AddEntityAction{

    protected function edit( $entity ){

        parent::edit( $entity );

        //vamos a retornar por json las otrosProyectos de la solicitud.

        //usamos el renderer para reutilizar lo que mostramos de las otrosProyectos.
        $renderer = new CMPSolicitudFormRenderer();
        $otrosProyectos = array();
        foreach ($this->getEntityManager()->getEntities(new CdtSearchCriteria()) as $otrosProyecto) {

            $otrosProyecto->setDt_desdeproyecto(CYTSecureUtils::formatDateToPersist($otrosProyecto->getDt_desdeproyecto()));
            $otrosProyecto->setDt_hastaproyecto(CYTSecureUtils::formatDateToPersist($otrosProyecto->getDt_hastaproyecto()));

            $otrosProyectos[] = $renderer->buildArrayOtrosProyecto($otrosProyecto);
        }
        //CYTSecureUtils::logObject($otrosProyectos);
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