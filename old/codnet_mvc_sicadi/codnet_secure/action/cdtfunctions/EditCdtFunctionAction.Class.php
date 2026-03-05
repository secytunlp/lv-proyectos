<?php 

/**
 * Acción para editar CdtFunction.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
abstract class EditCdtFunctionAction extends CdtEditAction{

	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getEntity();
	 */
	protected function getEntity(){
		
		//se construye el CdtFunction a modificar.
		$oCdtFunction = new CdtFunction ( );
		
				
		$oCdtFunction->setCd_function ( CdtUtils::getParamPOST('cd_function') );	
				
		$oCdtFunction->setDs_function ( CdtUtils::getParamPOST('ds_function') );	
		
					
		return $oCdtFunction;
	}
	
		
}
