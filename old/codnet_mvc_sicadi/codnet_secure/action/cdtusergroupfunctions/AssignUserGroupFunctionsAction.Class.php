<?php

/**
 * Acción para editar CdtUserGroupFunction.
 *
 * @author codnet archetype builder
 * @since 29-12-2011
 *
 */
class AssignUserGroupFunctionsAction extends CdtEditAction{

	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getEntity();
	 */
	protected function getEntity(){

		$oCdtUserGroup = new CdtUserGroup ( );
		$oCdtUserGroup->setCd_usergroup ( CdtUtils::getParamPOST('cd_usergroup') );
		$oCdtUserGroup->setDs_usergroup ( CdtUtils::getParamPOST('ds_usergroup') );

		$functions = CdtUtils::getParamPOST('functions');
			
		return array("usergroup" => $oCdtUserGroup, "functions" => $functions);
	}

	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::edit();
	 */
	protected function edit($entity){
		$manager = new CdtUserGroupFunctionManager();
		$manager->assignCdtUserGroupFunctions( $entity["usergroup"],  $entity["functions"]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getForwardSuccess();
	 */
	protected function getForwardSuccess(){
		return 'assign_usergroup_functions_success';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getForwardError();
	 */
	protected function getForwardError(){
		return 'assign_usergroup_functions_error';
	}
	

}
