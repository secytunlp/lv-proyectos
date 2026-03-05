<?php 

/**
 * Acción para editar CdtMenuOption.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
abstract class EditCdtMenuOptionAction extends CdtEditAction{

	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getEntity();
	 */
	protected function getEntity(){
		
		//se construye el CdtMenuOption a modificar.
		$oCdtMenuOption = new CdtMenuOption ( );
		
				
		$oCdtMenuOption->setCd_menuoption ( CdtUtils::getParamPOST('cd_menuoption') );	
				
		$oCdtMenuOption->setDs_name ( CdtUtils::getParamPOST('ds_name') );	
				
		$oCdtMenuOption->setDs_href ( CdtUtils::getParamPOST('ds_href') );	
				
		$oCdtMenuOption->setCd_function ( CdtUtils::getParamPOST('cd_function') );	
				
		$oCdtMenuOption->setNu_order ( CdtUtils::getParamPOST('nu_order') );	
				
		$oCdtMenuOption->setCd_menugroup ( CdtUtils::getParamPOST('cd_menugroup') );	
				
		$oCdtMenuOption->setDs_cssclass ( CdtUtils::getParamPOST('ds_cssclass') );	
				
		$oCdtMenuOption->setDs_description ( CdtUtils::getParamPOST('ds_description') );	
		
					
		return $oCdtMenuOption;
	}
	
		
}
