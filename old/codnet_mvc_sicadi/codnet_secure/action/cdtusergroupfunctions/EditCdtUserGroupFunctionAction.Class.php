<?php 

/**
 * Acción para editar CdtUserGroupFunction.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
abstract class EditCdtUserGroupFunctionAction extends CdtEditAction{

	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getEntity();
	 */
	protected function getEntity(){
		
		//se construye el CdtUserGroupFunction a modificar.
		$oCdtUserGroupFunction = new CdtUserGroupFunction ( );
		
				
		$oCdtUserGroupFunction->setCd_usergroup_function ( CdtUtils::getParamPOST('cd_usergroup_function') );	
				
		$oCdtUserGroupFunction->setCd_usergroup ( CdtUtils::getParamPOST('cd_usergroup') );	
				
		$oCdtUserGroupFunction->setCd_function ( CdtUtils::getParamPOST('cd_function') );	
		
					
		return $oCdtUserGroupFunction;
	}
	
		
}
