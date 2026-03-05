<?php 

/**
 * Acci�n para inicializar el contexto para editar CdtFunction.
 * 
 * @author codnet archetype builder
 * @since 29-12-2011
 * 
 */
abstract class EditCdtFunctionInitAction  extends CdtEditInitAction{

	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::getXTemplate();
	 */
	protected function getXTemplate(){
		return new XTemplate ( CDT_SECURE_TEMPLATE_CDTFUNCTION_EDIT );		
	}

	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::getEntity();
	 */
	protected function getEntity(){
		
		//se construye el CdtFunction a modificar.
		$oCdtFunction = new CdtFunction ( );
	
				
		$oCdtFunction->setCd_function ( CdtUtils::getParamPOST('cd_function') );	
				
		$oCdtFunction->setDs_function ( CdtUtils::getParamPOST('ds_function') );	
		
		
		return $oCdtFunction;
	}	
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::parseEntity();
	 */
	protected function parseEntity($entity, XTemplate $xtpl){
		
		$oCdtFunction = CdtFormatUtils::ifEmpty($entity, new CdtFunction());

		//parseamos la entity
		
		
		
				
		$xtpl->assign ( 'cd_function', stripslashes ( $oCdtFunction->getCd_function () ) );
		$xtpl->assign ( 'cd_function_label', CDT_SECURE_LBL_CDTFUNCTION_CD_FUNCTION );
		$xtpl->assign ( 'cd_function_required', '*' );
		$xtpl->assign ( 'cd_function_required_msg', CDT_SECURE_MSG_CDTFUNCTION_CD_FUNCTION_REQUIRED );
				
		$xtpl->assign ( 'ds_function', stripslashes ( $oCdtFunction->getDs_function () ) );
		$xtpl->assign ( 'ds_function_label', CDT_SECURE_LBL_CDTFUNCTION_DS_FUNCTION );
		$xtpl->assign ( 'ds_function_required', '*' );
		$xtpl->assign ( 'ds_function_required_msg', CDT_SECURE_MSG_CDTFUNCTION_DS_FUNCTION_REQUIRED );
		
		
		//parseamos las relaciones de la entity
		
		
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
	
	

}
