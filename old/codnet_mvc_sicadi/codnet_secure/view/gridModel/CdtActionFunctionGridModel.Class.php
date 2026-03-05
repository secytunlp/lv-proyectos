<?php 

/** 
 * GridModel para CdtActionFunction
 *  
 * @author codnet archetype builder
 * @since 29-12-2011
 */ 
class CdtActionFunctionGridModel extends GridModel{

	
	public function __construct( ){

		parent::__construct();
		$this->initModel();
		
	}
	
	private function initModel(){
		
		
		$this->buildModel( "cd_actionfunction", CDT_SECURE_LBL_CDTACTIONFUNCTION_CD_ACTIONFUNCTION,  80 );
		
		$this->buildModel( "ds_function", CDT_SECURE_LBL_CDTACTIONFUNCTION_CD_FUNCTION,  80, "F.ds_function" );
		
		$this->buildModel( "ds_action", CDT_SECURE_LBL_CDTACTIONFUNCTION_DS_ACTION,  80 );
		
				
		//acciones sobre la lista
		$this->buildAction( "add_cdtactionfunction_init", "add_cdtactionfunction_init", CDT_SECURE_MSG_CDTACTIONFUNCTION_TITLE_ADD, "image", "add" );

	}
	
		
	/**
	 * (non-PHPdoc)
	 * @see GridModel::getTitle();
	 */
	function getTitle(){
		return CDT_SECURE_MSG_CDTACTIONFUNCTION_TITLE_LIST;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see GridModel::getEntityManager();
	 */
	public function getEntityManager(){
		return new CdtActionFunctionManager();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see GridModel::getRowActionsModel( $item );
	 */
	public function getRowActionsModel( $item ){
		
		return $this->getDefaultRowActions( $item, "cdtactionfunction", CDT_SECURE_LBL_CDTACTIONFUNCTION, true, true, true, true,350);
	}

	
	/**
	 * (non-PHPdoc)
	 * @see GridModel::getMsgConfirmDelete( $item );
	 */
	protected function getMsgConfirmDelete( $item ){
		
		if(!empty($item)){
			$xtpl = new XTemplate(CDT_SECURE_TEMPLATE_CDTACTIONFUNCTION_DELETE);
	        $xtpl->assign('cd_actionfunction', $item->getCd_actionfunction() );
	        $xtpl->assign('question', CDT_SECURE_MSG_CONFIRM_DELETE_QUESTION );
	        $xtpl->assign('title_confirm', CDT_SECURE_MSG_CONFIRM_DELETE_TITLE );
	        $xtpl->assign('lbl_code', CDT_SECURE_LBL_CDTACTIONFUNCTION_CD_USER );
	        $xtpl->parse('main');
	        $text = addslashes($xtpl->text('main'));
			return CdtFormatUtils::quitarEnters($text);
		}else{
			return parent::getMsgConfirmDelete( $item );
		}
        
	}
}
?>
