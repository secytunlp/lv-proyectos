<?php

/**
 * Formato para renderizar una observacion en las grillas
 *
 * @author Marcos
 * @since 17-11-2013
 *
 */
class GridObservacionesValueFormat extends GridValueFormat {

	public function __construct() {

		parent::__construct();
	}

	public function format($value, $item=null) {
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('solicitud_oid', $value, '=');
		$oCriteria->addNull('fechaHasta');
		$managerSolicitudEstado =  CYTSecureManagerFactory::getSolicitudEstadoManager();
		$oSolicitudEstado = $managerSolicitudEstado->getEntity($oCriteria);
		if ($oSolicitudEstado) {
			$res = $oSolicitudEstado->getMotivo();
		}
		
		
		return $res ;
	}

}