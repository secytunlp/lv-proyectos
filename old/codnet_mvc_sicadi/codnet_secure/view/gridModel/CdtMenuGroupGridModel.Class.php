<?php 

/** 
 * GridModel para CdtMenuGroup
 *  
 * @author codnet archetype builder
 * @since 29-12-2011
 */ 
class CdtMenuGroupGridModel extends GridModel{

	
	public function __construct( ){

		parent::__construct();
		$this->initModel();
		
	}
	
	private function initModel(){
		
		
		$this->buildModel( "cd_menugroup", CDT_SECURE_LBL_CDTMENUGROUP_CD_MENUGROUP,  20 );
		
		$this->buildModel( "nu_order", CDT_SECURE_LBL_CDTMENUGROUP_NU_ORDER,  15 );
		
		$this->buildModel( "nu_width", CDT_SECURE_LBL_CDTMENUGROUP_NU_WIDTH,  15 );
		
		$this->buildModel( "ds_name", CDT_SECURE_LBL_CDTMENUGROUP_DS_NAME,  60 );
		
		$this->buildModel( "ds_action", CDT_SECURE_LBL_CDTMENUGROUP_DS_ACTION,  80 );
		
		$this->buildModel( "ds_cssclass", CDT_SECURE_LBL_CDTMENUGROUP_DS_CSSCLASS,  30 );
		
				
		//acciones sobre la lista
		$this->buildAction( "add_cdtmenugroup_init", "add_cdtmenugroup_init", CDT_SECURE_MSG_CDTMENUGROUP_TITLE_ADD, "image", "add" );

	}
	
		
	/**
	 * (non-PHPdoc)
	 * @see GridModel::getTitle();
	 */
	function getTitle(){
		return CDT_SECURE_MSG_CDTMENUGROUP_TITLE_LIST;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see GridModel::getEntityManager();
	 */
	public function getEntityManager(){
		return new CdtMenuGroupManager();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see GridModel::getRowActionsModel( $item );
	 */
	public function getRowActionsModel( $item ){
		
		return $this->getDefaultRowActions( $item, "cdtmenugroup", CDT_SECURE_LBL_CDTMENUGROUP, true, true, true, true);
		
	}

	
	/**
	 * (non-PHPdoc)
	 * @see GridModel::getMsgConfirmDelete( $item );
	 */
	protected function getMsgConfirmDelete( $item ){
		
		if(!empty($item)){
			$xtpl = new XTemplate(CDT_SECURE_TEMPLATE_CDTMENUGROUP_DELETE);
	        $xtpl->assign('cd_menugroup', $item->getCd_menugroup() );
	        $xtpl->assign('question', CDT_SECURE_MSG_CONFIRM_DELETE_QUESTION );
	        $xtpl->assign('title_confirm', CDT_SECURE_MSG_CONFIRM_DELETE_TITLE );
	        $xtpl->assign('lbl_code', CDT_SECURE_LBL_CDTMENUGROUP_CD_USER );
	        $xtpl->parse('main');
	        $text = addslashes($xtpl->text('main'));
			return CdtFormatUtils::quitarEnters($text);
		}else{
			return parent::getMsgConfirmDelete( $item );
		}
        
	}
}
?>
