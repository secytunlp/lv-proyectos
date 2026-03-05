<?php 

/**
 * Acci�n para inicializar el contexto para editar CdtActionFunction.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
abstract class EditCdtActionFunctionInitAction  extends CdtEditInitAction{

	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::getXTemplate();
	 */
	protected function getXTemplate(){
		return new XTemplate ( CDT_SECURE_TEMPLATE_CDTACTIONFUNCTION_EDIT );		
	}

	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::getEntity();
	 */
	protected function getEntity(){
		
		//se construye el CdtActionFunction a modificar.
		$oCdtActionFunction = new CdtActionFunction ( );
	
				
		$oCdtActionFunction->setCd_actionfunction ( CdtUtils::getParamPOST('cd_actionfunction') );	
				
		$oCdtActionFunction->setCd_function ( CdtUtils::getParamPOST('cd_function') );	
				
		$oCdtActionFunction->setDs_action ( CdtUtils::getParamPOST('ds_action') );	
		
		
		return $oCdtActionFunction;
	}	
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::parseEntity();
	 */
	protected function parseEntity($entity, XTemplate $xtpl){
		
		$oCdtActionFunction = CdtFormatUtils::ifEmpty($entity, new CdtActionFunction());

		//parseamos la entity
		
		
		
				
		$xtpl->assign ( 'cd_actionfunction', stripslashes ( $oCdtActionFunction->getCd_actionfunction () ) );
		$xtpl->assign ( 'cd_actionfunction_label', CDT_SECURE_LBL_CDTACTIONFUNCTION_CD_ACTIONFUNCTION );
		$xtpl->assign ( 'cd_actionfunction_required', '*' );
		$xtpl->assign ( 'cd_actionfunction_required_msg', CDT_SECURE_MSG_CDTACTIONFUNCTION_CD_ACTIONFUNCTION_REQUIRED );
				
		$xtpl->assign ( 'ds_action', stripslashes ( $oCdtActionFunction->getDs_action () ) );
		$xtpl->assign ( 'ds_action_label', CDT_SECURE_LBL_CDTACTIONFUNCTION_DS_ACTION );
		$xtpl->assign ( 'ds_action_required', '*' );
		$xtpl->assign ( 'ds_action_required_msg', CDT_SECURE_MSG_CDTACTIONFUNCTION_DS_ACTION_REQUIRED );
		
		
		//parseamos las relaciones de la entity
		
		$xtpl->assign ( 'cd_function_label', CDT_SECURE_LBL_CDTACTIONFUNCTION_CD_FUNCTION );
		$selected =  $oCdtActionFunction->getCdtFunction();
		
		$oFindObject = CdtSecureComponentsFactory::getFindObjectCdtFunction( $selected, 'cd_function', true,  CDT_SECURE_MSG_CDTACTIONFUNCTION_CD_FUNCTION_REQUIRED  );

		$xtpl->assign('CdtFunction_find', $oFindObject->show() );
		
		
		
		//parseamos el action submit.
		$xtpl->assign('submit',  $this->getSubmitAction() );
		
		$xtpl->assign ( 'lbl_save', CDT_SECURE_LBL_SAVE);
		$xtpl->assign ( 'lbl_cancel', CDT_SECURE_LBL_CANCEL);
		$xtpl->assign ( 'msg_required_fields', CDT_SECURE_MSG_REQUIRED_FIELDS);
		
	}

	/**
	 * retorna el action para el submit.
	 * @return string
	 */
	protected abstract function getSubmitAction();
	
	
	protected function parseCdtFunction($selected, XTemplate $xtpl ){
	
		$manager = new CdtFunctionManager();
		$oCriteria = new CdtSearchCriteria();
		$cdtfunctions = $manager->getCdtFunctions( $oCriteria );
		
		$xtpl->assign ( 'lbl_select', CDT_SECURE_LBL_SELECT );
		
		foreach($cdtfunctions as $key => $oCdtFunction) {
		
			$xtpl->assign ( 'ds_CdtFunction', $oCdtFunction->getCd_function() );
			$xtpl->assign ( 'cd_CdtFunction', FormatUtils::selected($oCdtFunction->getCd_function(), $selected ) );
			
			$xtpl->parse ( 'main.cdtfunctions_option' );
		}	
	}
	

}
