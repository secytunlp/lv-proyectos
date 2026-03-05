<?php

/**
 * Acción para listar CdtActionFunctions.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
class ListCdtActionFunctionsAction extends CMPGridAction {

	protected function getGridModel( CMPGrid $oGrid ){
		return new  CdtActionFunctionGridModel();
	}

}
