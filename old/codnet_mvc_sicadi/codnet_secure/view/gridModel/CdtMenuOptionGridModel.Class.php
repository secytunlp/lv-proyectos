<?php 

/** 
 * GridModel para CdtMenuOption
 *  
 * @author codnet archetype builder
 * @since 29-12-2011
 */ 
class CdtMenuOptionGridModel extends GridModel{

	
	public function __construct( ){

		parent::__construct();
		$this->initModel();
		
	}
	
	private function initModel(){
		
		
		$this->buildModel( "cd_menuoption", CDT_SECURE_LBL_CDTMENUOPTION_CD_MENUOPTION,  20 );
		
		$this->buildModel( "ds_name", CDT_SECURE_LBL_CDTMENUOPTION_DS_NAME,  40, "MO.ds_name" );
		
		$this->buildModel( "ds_href", CDT_SECURE_LBL_CDTMENUOPTION_DS_HREF,  80, "MO.ds_href" );
		
		$this->buildModel( "ds_function", CDT_SECURE_LBL_CDTMENUOPTION_CD_FUNCTION,  40, "F.ds_function"  );
		
		$this->buildModel( "nu_order", CDT_SECURE_LBL_CDTMENUOPTION_NU_ORDER,  15, "MO.nu_order" );
		
		$this->buildModel( "ds_menugroup", CDT_SECURE_LBL_CDTMENUOPTION_CD_MENUGROUP,  30, "MG.ds_name" );
		
		$this->buildModel( "ds_cssclass", CDT_SECURE_LBL_CDTMENUOPTION_DS_CSSCLASS, 20, "MO.ds_cssclass" );
		
		$this->buildModel( "ds_description", CDT_SECURE_LBL_CDTMENUOPTION_DS_DESCRIPTION,  40 );
		
				
		//acciones sobre la lista
		$this->buildAction( "add_cdtmenuoption_init", "add_cdtmenuoption_init", CDT_SECURE_MSG_CDTMENUOPTION_TITLE_ADD, "image", "add" );

	}
			
	/**
	 * (non-PHPdoc)
	 * @see GridModel::getTitle();
	 */
	function getTitle(){
		return CDT_SECURE_MSG_CDTMENUOPTION_TITLE_LIST;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see GridModel::getEntityManager();
	 */
	public function getEntityManager(){
		return new CdtMenuOptionManager();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see GridModel::getRowActionsModel( $item );
	 */
	public function getRowActionsModel( $item ){
		
		return $this->getDefaultRowActions( $item, "cdtmenuoption", CDT_SECURE_LBL_CDTMENUOPTION, true, true, true, true);
		
	}

	
	/**
	 * (non-PHPdoc)
	 * @see GridModel::getMsgConfirmDelete( $item );
	 */
	protected function getMsgConfirmDelete( $item ){
		
		if(!empty($item)){
			$xtpl = new XTemplate(CDT_SECURE_TEMPLATE_CDTMENUOPTION_DELETE);
	        $xtpl->assign('cd_menuoption', $item->getCd_menuoption() );
	        $xtpl->assign('question', CDT_SECURE_MSG_CONFIRM_DELETE_QUESTION );
	        $xtpl->assign('title_confirm', CDT_SECURE_MSG_CONFIRM_DELETE_TITLE );
	        $xtpl->assign('lbl_code', CDT_SECURE_LBL_CDTMENUOPTION_CD_USER );
	        $xtpl->parse('main');
	        $text = addslashes($xtpl->text('main'));
			return CdtFormatUtils::quitarEnters($text);
		}else{
			return parent::getMsgConfirmDelete( $item );
		}
        
	}
}
?>
