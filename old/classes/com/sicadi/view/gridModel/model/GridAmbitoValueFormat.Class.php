<?php

/**
 * Formato para renderizar un ambito en las grillas
 *
 * @author Marcos
 * @since 17-11-2013
 *
 */
class GridAmbitoValueFormat extends GridValueFormat {

	public function __construct() {

		parent::__construct();
	}

	public function format($value, $item=null) {
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_solicitud', $value, '=');
		$managerAmbito =  ManagerFactory::getAmbitoManager();
		$oAmbitos = $managerAmbito->getEntities($oCriteria);
		$res = '';
		foreach ($oAmbitos as $oAmbito) {
			$res .=$oAmbito->getDs_ciudad().' / '.$oAmbito->getDs_pais().' / ('.CYTSecureUtils::formatDateToView($oAmbito->getDt_desde()).'-'.CYTSecureUtils::formatDateToView($oAmbito->getDt_hasta()).') ';
		}
		return $res ;
	}

}