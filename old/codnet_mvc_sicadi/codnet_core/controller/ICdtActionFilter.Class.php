<?php

/**
 * Un filtro se ejecuta antes de las acciones.
 * La principal funcionalidad de los filtros es realizar validaciones.
 * Todo lo que necesitemos ejecutar antes de una acción lo podemos realizar con un filtro.
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 04-07-2011
 */
interface ICdtActionFilter{

	/**
	 * se aplica un filtro para un action dado.
	 * @param string $ds_action_name nombre del action
	 * @param CdtAction $oAction action al cual aplicar el filtro
	 */
	function apply( $ds_actionName, CdtAction $oAction );
	
}