<?php

/**
 * Acción para listar CdtUserGroupFunctions.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
class ListCdtUserGroupFunctionsAction extends CMPGridAction {

	protected function getGridModel( CMPGrid $oGrid ){
		return new  CdtUserGroupFunctionGridModel();
	}

}
