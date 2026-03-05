<?php

/**
 * componente grilla para solicitudes
 *
 * @author Marcos
 * @since 13-11-2013
 *
 */
class CMPSolicitudGrid extends CMPEntityGrid{

	public function __construct(){

		parent::__construct();
		
		$oPeriodoManager = CYTSecureManagerFactory::getPeriodoManager();
		$oPerioActual = $oPeriodoManager->getPeriodoActual(CYT_PERIODO_YEAR);
		
		$filter = new CMPSolicitudFilter();
		$filter->setPeriodo($oPerioActual);
		$filter->saveProperties();
		$this->setFilter( $filter );
		$this->setLayout( new CdtLayoutBasicAjax() );
		$this->setModel( new SolicitudGridModel() );
		//$this->setRenderer( );
	}

}