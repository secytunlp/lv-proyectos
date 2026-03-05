<?php 

/** 
 * GridModel para CdtUser
 *  
 * @author codnet archetype builder
 * @since 29-12-2011
 */ 
class CdtUserGridModel extends GridModel{

	
	public function CdtUserGridModel( ){

		parent::__construct();
		$this->initModel();
		
	}
	
	protected function initModel(){
		
		
		$this->buildModel( "cd_user", CDT_SECURE_LBL_CDTUSER_CD_USER,  15 );
		
		$this->buildModel( "ds_username", CDT_SECURE_LBL_CDTUSER_DS_USERNAME,  20 );
		
		//$this->buildGridColumnModel("nu_attemps", CDT_SECURE_LBL_CDTUSER_NU_ATTEMPS, 15);
        //$this->getColumnModelByName("nu_attemps")->setFormat(new GridAttempsValueFormat());
        
		$this->buildModel( "ds_name", CDT_SECURE_LBL_CDTUSER_DS_NAME,  50 );
		
		$this->buildModel( "ds_email", CDT_SECURE_LBL_CDTUSER_DS_EMAIL,  70 );
		
		$this->buildModel( "ds_usergroup", CDT_SECURE_LBL_CDTUSER_CD_USERGROUP,  40, "UG.ds_usergroup" );
		
		$this->buildModel( "ds_phone", CDT_SECURE_LBL_CDTUSER_DS_PHONE,  30 );
		
		$this->buildModel( "ds_address", CDT_SECURE_LBL_CDTUSER_DS_ADDRESS,  60 );
		
		
				
		//acciones sobre la lista
		$this->buildAction( "add_cdtuser_init", "add_cdtuser_init", CDT_SECURE_MSG_CDTUSER_TITLE_ADD, "image", "add" );

	}
	
		
	/**
	 * (non-PHPdoc)
	 * @see GridModel::getTitle();
	 */
	function getTitle(){
		return CDT_SECURE_MSG_CDTUSER_TITLE_LIST;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see GridModel::getEntityManager();
	 */
	public function getEntityManager(){
		return new CdtUserManager();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see GridModel::getRowActionsModel( $item );
	 */
	public function getRowActionsModel( $item ){
		
		$actions = $this->getDefaultRowActions( $item, "cdtuser", CDT_SECURE_LBL_CDTUSER, true, true, true, true);

		$msgConfirm = $this->getMsgConfirmUnblock( $item );
		$action =  $this->buildRowAction("unblock_cdtuser", "unblock_cdtuser", CDT_SECURE_MSG_CDTUSER_TITLE_UNBLOCK, CDT_UI_IMG_EDIT, "unblock_cdtuser", "doAction('unblock_cdtuser')", false, $msgConfirm, false, 500, 750);
		//$action =  $this->buildRowAction( "unblock_cdtuser", "unblock_cdtuser", CDT_SECURE_MSG_CDTUSER_TITLE_UNBLOCK, CDT_UI_IMG_EDIT, "unblock_cdtuser", "delete_items('delete_$ds_entityName')", $bl_multiple_delete, $msg, false, $nu_heightPopup, $nu_widthPopup) ;
		$actions->addItem($action);
        
		return $actions;
	}

	
	/**
	 * (non-PHPdoc)
	 * @see GridModel::getMsgConfirmDelete( $item );
	 */
	protected function getMsgConfirmDelete( $item ){
		
		if(!empty($item)){
			$xtpl = new XTemplate(CDT_SECURE_TEMPLATE_CDTUSER_DELETE);
	        $xtpl->assign('cd_user', $item->getCd_user() );
	        $xtpl->assign('question', CDT_SECURE_MSG_CONFIRM_DELETE_QUESTION );
	        $xtpl->assign('title_confirm', CDT_SECURE_MSG_CONFIRM_DELETE_TITLE );
	        $xtpl->assign('lbl_code', CDT_SECURE_LBL_CDTUSER_CD_USER );
	        $xtpl->parse('main');
	        $text = addslashes($xtpl->text('main'));
			return CdtFormatUtils::quitarEnters($text);
		}else{
			return parent::getMsgConfirmDelete( $item );
		}
        
	}


	/**
	 */
	protected function getMsgConfirmUnblock( $item ){
		
		if(!empty($item)){
			$xtpl = new XTemplate(CDT_SECURE_TEMPLATE_CDTUSER_DELETE);
	        $xtpl->assign('cd_user', $item->getCd_user() );
	        $xtpl->assign('question', CDT_SECURE_MSG_CONFIRM_UNBLOCK_QUESTION );
	        $xtpl->assign('title_confirm', CDT_SECURE_MSG_CONFIRM_UNBLOCK_TITLE );
	        $xtpl->assign('lbl_code', CDT_SECURE_LBL_CDTUSER_CD_USER );
	        $xtpl->parse('main');
	        $text = addslashes($xtpl->text('main'));
			return CdtFormatUtils::quitarEnters($text);
		}else{
			return CDT_SECURE_MSG_CONFIRM_UNBLOCK_QUESTION;
		}
        
	}
	
    /**
     * redefinimos para cambiar el estilo de la fila cuando el
     * user está bloqueado.	
     * @param CdtUser $item
     */
	public function getRowStyleClass( $item ){
		
		$attemps = $item->getNu_attemps();
		if($attemps == CDT_SECURE_LOGIN_MAX_ATTEMPS ){
			return "grid_row_userblocked";
		}
		
        return parent::getRowStyleClass( $item );
		
	}
	
}
?>
