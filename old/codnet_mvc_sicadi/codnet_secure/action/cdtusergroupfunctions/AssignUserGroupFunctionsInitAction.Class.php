<?php 

/**
 * Acci�n para inicializar el contexto para modificar
 * un CdtUserGroupFunction.
 *  
 * @author codnet archetype builder
 * @since 29-12-2011
 *  
 */
class AssignUserGroupFunctionsInitAction extends CdtEditInitAction{

	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::getXTemplate();
	 */
	protected function getXTemplate(){
		return new XTemplate ( CDT_SECURE_TEMPLATE_USERGROUPFUNCTION_ASSIGN );		
	}

	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::getEntity();
	 */
	protected function getEntity(){
		
		$oCdtUserGroup = null;

		//recuperamos dado su identifidor.
		$cd_usergroup = CdtUtils::getParam('id');
			
		if (!empty( $cd_usergroup )) {
						
			$oCriteria = new CdtSearchCriteria();
			$oCriteria->addFilter('cd_usergroup', $cd_usergroup, '=');
			
			//recuperamos el usergroup
			$manager = new CdtUserGroupManager();
			$oCdtUserGroup = $manager->getCdtUserGroup( $oCriteria );
			
			//recuperamos las funciones asociadas al usergroup
			$manager = new CdtFunctionManager();
			$functions = array();
			$cdtfunctions = $manager->getCdtUserGroupFunctions( $oCdtUserGroup );
			foreach ($cdtfunctions as $oFunction) {
				$functions[] = $oFunction->getCd_function();
			}
			
		}else{
		
			$oCdtUserGroup = new CdtUserGroup( );
			$oCdtUserGroup->setCd_usergroup ( CdtUtils::getParamPOST('cd_usergroup') );	
			$oCdtUserGroup->setDs_usergroup ( CdtUtils::getParamPOST('ds_usergroup') );
	
			//$functions = unserialize( CdtUtils::getParamSESSION("usergroup_functions") ) ;
				
			$functions = CdtUtils::getParamPOST('functions');
			if(empty($functions))
				$functions = array();
		
		}
		
		
		return array("usergroup" => $oCdtUserGroup, "functions" => $functions);
	}	
	
	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::parseEntity();
	 */
	protected function parseEntity($entity, XTemplate $xtpl){
		
		$oCdtUserGroup =$entity["usergroup"];
		$functions = $entity["functions"];
		
		//parseamos la entity
				
		$xtpl->assign ( 'cd_usergroup', $oCdtUserGroup->getCd_usergroup () );
		$xtpl->assign ( 'ds_usergroup', $oCdtUserGroup->getDs_usergroup () );
		$xtpl->assign ( 'ds_usergroup_label', CDT_SECURE_LBL_CDTUSERGROUP_DS_USERGROUP );
		
		
		//parseamos las funciones
		$this->parseCdtFunctions( $functions, $xtpl );
		
		$xtpl->assign ( 'lbl_save', CDT_SECURE_LBL_SAVE);
		$xtpl->assign ( 'lbl_cancel', CDT_SECURE_LBL_CANCEL);
		$xtpl->assign ( 'msg_required_fields', CDT_SECURE_MSG_REQUIRED_FIELDS);
		
	}
	
	protected function parseCdtFunctions($functions, XTemplate $xtpl ){
	
		$manager = new CdtFunctionManager();
		$cdtFunctions = $manager->getCdtFunctions( new CdtSearchCriteria() );
		
		$index=0;
		$iterador=1;
		foreach($cdtFunctions as $oCdtFunction) {
		
			if($index==8){
				$index=0;
				$xtpl->parse ( 'main.row' );
			}
			
			$xtpl->assign ( 'ds_function', $oCdtFunction->getDs_function() );

			$xtpl->assign ( 'cd_function', $oCdtFunction->getCd_function() );
			
			$xtpl->assign ( 'cd_function',  $oCdtFunction->getCd_function() ) ;

            $xtpl->assign ( 'input_name',  'check_'.$iterador ) ;
			
			if( $this->exists( $oCdtFunction,$functions ) )
				$xtpl->assign ( 'checked', "checked" );
			else	
				$xtpl->assign ( 'checked', "" );
				
			$index++;
            $iterador++;
			
			$xtpl->parse ( 'main.row.functions_option' );
		}

		if( $index > 0 )
			$xtpl->parse ( 'main.row' );
	}
	
	private function exists( CdtFunction $oFunction,$functions=null){
		
		if(empty($functions))
			return false;

		$exist = false;
		foreach($functions as $next) {
			$exist =  ( $next == $oFunction->getCd_function() );
			if($exist)
				return true;
		}
		return false;
	}
	/**
	 * (non-PHPdoc)
	 * @see CdtEditInitAction::getOutputTitle();
	 */
	protected function getOutputTitle(){
		return CDT_SECURE_MSG_USERGROUPFUNCTION_TITLE_ASSIGN;
	}

}
