<?php

/**
 * Acción para listar CdtUsers.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
class ListCdtUsersAction extends CMPGridAction {

	protected function getGridModel( CMPGrid $oGrid ){
		
		return new  CdtUserGridModel();
	}

}
