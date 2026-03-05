<?php 

/**
 * Acción para editar CdtUserGroup.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
abstract class EditCdtUserGroupAction extends CdtEditAction{

	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getEntity();
	 */
	protected function getEntity(){
		
		//se construye el CdtUserGroup a modificar.
		$oCdtUserGroup = new CdtUserGroup ( );
		
				
		$oCdtUserGroup->setCd_usergroup ( CdtUtils::getParamPOST('cd_usergroup') );	
				
		$oCdtUserGroup->setDs_usergroup ( CdtUtils::getParamPOST('ds_usergroup') );	
		
					
		return $oCdtUserGroup;
	}
	
		
}
