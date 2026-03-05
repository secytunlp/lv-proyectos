<?php 

/**
 * Acción para editar CdtMenuGroup.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
abstract class EditCdtMenuGroupAction extends CdtEditAction{

	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getEntity();
	 */
	protected function getEntity(){
		
		//se construye el CdtMenuGroup a modificar.
		$oCdtMenuGroup = new CdtMenuGroup ( );
		
				
		$oCdtMenuGroup->setCd_menugroup ( CdtUtils::getParamPOST('cd_menugroup') );	
				
		$oCdtMenuGroup->setNu_order ( CdtUtils::getParamPOST('nu_order') );	
				
		$oCdtMenuGroup->setNu_width ( CdtUtils::getParamPOST('nu_width') );	
				
		$oCdtMenuGroup->setDs_name ( CdtUtils::getParamPOST('ds_name') );	
				
		$oCdtMenuGroup->setDs_action ( CdtUtils::getParamPOST('ds_action') );	
				
		$oCdtMenuGroup->setDs_cssclass ( CdtUtils::getParamPOST('ds_cssclass') );	
		
					
		return $oCdtMenuGroup;
	}
	
		
}
