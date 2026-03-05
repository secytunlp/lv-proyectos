<?php

/**
 * Acción para listar CdtMenuOptions.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
class ListCdtMenuOptionsAction extends CMPGridAction {

	protected function getGridModel( CMPGrid $oGrid ){
		return new  CdtMenuOptionGridModel();
	}

}
