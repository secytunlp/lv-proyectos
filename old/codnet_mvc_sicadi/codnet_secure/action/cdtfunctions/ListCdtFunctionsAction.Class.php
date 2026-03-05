<?php

/**
 * Acción para listar CdtFunctions.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
class ListCdtFunctionsAction extends CMPGridAction {

	protected function getGridModel( CMPGrid $oGrid ){
		return new  CdtFunctionGridModel();
	}

}
