<?php

/**
 * Acción para listar proyectos.
 *
 * @author Marcos
 * @since 13-11-2013
 *
 */
class ListSolicitudesAction extends CMPEntityGridAction{


	protected function getComponent() {

		return new CMPSolicitudGrid();
	}



}
