<?php

/**
 * Acción para listar CdtMenuGroups.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
class ListCdtMenuGroupsAction extends CMPGridAction {

	protected function getGridModel( CMPGrid $oGrid ){
		return new  CdtMenuGroupGridModel();
	}

}
