<?php

/**
 * Acción para listar CdtUserGroups.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
class ListCdtUserGroupsAction extends CMPGridAction {

	protected function getGridModel( CMPGrid $oGrid ){
		return new  CdtUserGroupGridModel();
	}

}
