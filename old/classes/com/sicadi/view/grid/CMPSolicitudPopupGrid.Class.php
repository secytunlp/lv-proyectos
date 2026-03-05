<?php

/**
 * componente grilla para solicitud
 *
 * @author Marcos
 * @since 16-11-2013
 *
 */
class CMPSolicitudPopupGrid extends CMPSolicitudGrid{

	public function __construct(){

		parent::__construct();

		$this->setRenderer( new FindEntityPopupRenderer() );
		
		//vemos si viene la provincia por parámetro
		$filter = $this->getFilter();
		$filter->setComponent( get_class($this) );
		
	}

}