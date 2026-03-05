<?php 

/** 
 * GridModel para CdtUserGroupFunction
 *  
 * @author codnet archetype builder
 * @since 29-12-2011
 */ 
class CdtUserGroupFunctionGridModel extends GridModel{

	
	public function CdtUserGroupFunctionGridModel( ){

		parent::__construct();
		$this->initModel();
		
	}
	
	private function initModel(){
		
		
		$this->buildModel( "cd_usergroup_function", CDT_SECURE_LBL_CDTUSERGROUPFUNCTION_CD_USERGROUP_FUNCTION,  80 );
		
		$this->buildModel( "ds_usergroup", CDT_SECURE_LBL_CDTUSERGROUPFUNCTION_CD_USERGROUP,  80, "UG.ds_usergroup"  );
		
		$this->buildModel( "ds_function", CDT_SECURE_LBL_CDTUSERGROUPFUNCTION_CD_FUNCTION,  80, "F.ds_function"  );
		
				
		//acciones sobre la lista
		$this->buildAction( "add_cdtusergroupfunction_init", "add_cdtusergroupfunction_init", CDT_SECURE_MSG_CDTUSERGROUPFUNCTION_TITLE_ADD, "image", "add" );

	}
	
		
	/**
	 * (non-PHPdoc)
	 * @see GridModel::getTitle();
	 */
	function getTitle(){
		return CDT_SECURE_MSG_CDTUSERGROUPFUNCTION_TITLE_LIST;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see GridModel::getEntityManager();
	 */
	public function getEntityManager(){
		return new CdtUserGroupFunctionManager();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see GridModel::getRowActionsModel( $item );
	 */
	public function getRowActionsModel( $item ){
		
		return $this->getDefaultRowActions( $item, "cdtusergroupfunction", CDT_SECURE_LBL_CDTUSERGROUPFUNCTION, true, true, true, true);
		
	}

	
	/**
	 * (non-PHPdoc)
	 * @see GridModel::getMsgConfirmDelete( $item );
	 */
	protected function getMsgConfirmDelete( $item ){
		
		if(!empty($item)){
			$xtpl = new XTemplate(CDT_SECURE_TEMPLATE_CDTUSERGROUPFUNCTION_DELETE);
	        $xtpl->assign('cd_usergroup_function', $item->getCd_usergroup_function() );
	        $xtpl->assign('question', CDT_SECURE_MSG_CONFIRM_DELETE_QUESTION );
	        $xtpl->assign('title_confirm', CDT_SECURE_MSG_CONFIRM_DELETE_TITLE );
	        $xtpl->assign('lbl_code', CDT_SECURE_LBL_CDTUSERGROUPFUNCTION_CD_USER );
	        $xtpl->parse('main');
	        $text = addslashes($xtpl->text('main'));
			return CdtFormatUtils::quitarEnters($text);
		}else{
			return parent::getMsgConfirmDelete( $item );
		}
        
	}
}
?>
