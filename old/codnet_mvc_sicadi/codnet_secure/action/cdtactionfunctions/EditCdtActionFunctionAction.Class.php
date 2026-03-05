<?php 

/**
 * Acción para editar CdtActionFunction.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
abstract class EditCdtActionFunctionAction extends CdtEditAction{

	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getEntity();
	 */
	protected function getEntity(){
		
		//se construye el CdtActionFunction a modificar.
		$oCdtActionFunction = new CdtActionFunction ( );
		
				
		$oCdtActionFunction->setCd_actionfunction ( CdtUtils::getParamPOST('cd_actionfunction') );	
				
		$oCdtActionFunction->setCd_function ( CdtUtils::getParamPOST('cd_function') );	
				
		$oCdtActionFunction->setDs_action ( CdtUtils::getParamPOST('ds_action') );	
		
					
		return $oCdtActionFunction;
	}
	
		
}
