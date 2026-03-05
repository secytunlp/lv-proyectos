<?php

/**
 * Acción para listar CdtRegistrations.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
class ListCdtRegistrationsAction extends CMPGridAction {

	protected function getGridModel( CMPGrid $oGrid ){
		return new  CdtRegistrationGridModel();
	}

}
