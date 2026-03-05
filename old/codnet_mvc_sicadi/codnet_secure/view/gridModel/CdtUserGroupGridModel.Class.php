<?php 

/** 
 * GridModel para CdtUserGroup
 *  
 * @author codnet archetype builder
 * @since 29-12-2011
 */ 
class CdtUserGroupGridModel extends GridModel{

	
	public function __construct( ){

		parent::__construct();
		$this->initModel();
		
	}
	
	private function initModel(){
		
		
		$this->buildModel( "cd_usergroup", CDT_SECURE_LBL_CDTUSERGROUP_CD_USERGROUP,  80 );
		
		$this->buildModel( "ds_usergroup", CDT_SECURE_LBL_CDTUSERGROUP_DS_USERGROUP,  80 );
		
				
		//acciones sobre la lista
		$this->buildAction( "add_cdtusergroup_init", "add_cdtusergroup_init", CDT_SECURE_MSG_CDTUSERGROUP_TITLE_ADD, "image", "add" );

	}
	
		
	/**
	 * (non-PHPdoc)
	 * @see GridModel::getTitle();
	 */
	function getTitle(){
		return CDT_SECURE_MSG_CDTUSERGROUP_TITLE_LIST;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see GridModel::getEntityManager();
	 */
	public function getEntityManager(){
		return new CdtUserGroupManager();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see GridModel::getRowActionsModel( $item );
	 */
	public function getRowActionsModel( $item ){
		
		$actions = $this->getDefaultRowActions( $item, "cdtusergroup", CDT_SECURE_LBL_CDTUSERGROUP, true, true, true, true);
		
		$action = $this->buildRowAction( "assign_usergroup_functions_init", "assign_usergroup_functions_init", CDT_SECURE_LBL_USERGROUP_FUNCTIONS_EDIT, CDT_UI_IMG_EDIT, "edit_functions" ) ;
		$actions->addItem( $action );
		
		return $actions;
		
	}

	
	/**
	 * (non-PHPdoc)
	 * @see GridModel::getMsgConfirmDelete( $item );
	 */
	protected function getMsgConfirmDelete( $item ){
		
		if(!empty($item)){
			$xtpl = new XTemplate(CDT_SECURE_TEMPLATE_CDTUSERGROUP_DELETE);
	        $xtpl->assign('cd_usergroup', $item->getCd_usergroup() );
	        $xtpl->assign('question', CDT_SECURE_MSG_CONFIRM_DELETE_QUESTION );
	        $xtpl->assign('title_confirm', CDT_SECURE_MSG_CONFIRM_DELETE_TITLE );
	        $xtpl->assign('lbl_code', CDT_SECURE_LBL_CDTUSERGROUP_CD_USER );
	        $xtpl->parse('main');
	        $text = addslashes($xtpl->text('main'));
			return CdtFormatUtils::quitarEnters($text);
		}else{
			return parent::getMsgConfirmDelete( $item );
		}
        
	}
}
?>
