<?php 

/** 
 * GridModel para CdtRegistration
 *  
 * @author codnet archetype builder
 * @since 29-12-2011
 */ 
class CdtRegistrationGridModel extends GridModel{

	
	public function __construct( ){

		parent::__construct();
		$this->initModel();
		
	}
	
	private function initModel(){
		
		
		$this->buildModel( "cd_registration", CDT_SECURE_LBL_CDTREGISTRATION_CD_REGISTRATION,  80 );
		
		$this->buildModel( "ds_activationcode", CDT_SECURE_LBL_CDTREGISTRATION_DS_ACTIVATIONCODE,  80 );
		
		$this->buildModel( "dt_date", CDT_SECURE_LBL_CDTREGISTRATION_DT_DATE,  80 );
		
		$this->buildModel( "ds_username", CDT_SECURE_LBL_CDTREGISTRATION_DS_USERNAME,  80 );
		
		$this->buildModel( "ds_name", CDT_SECURE_LBL_CDTREGISTRATION_DS_NAME,  80 );
		
		$this->buildModel( "ds_email", CDT_SECURE_LBL_CDTREGISTRATION_DS_EMAIL,  80 );
		
		$this->buildModel( "ds_password", CDT_SECURE_LBL_CDTREGISTRATION_DS_PASSWORD,  80 );
		
		$this->buildModel( "ds_phone", CDT_SECURE_LBL_CDTREGISTRATION_DS_PHONE,  80 );
		
		$this->buildModel( "ds_address", CDT_SECURE_LBL_CDTREGISTRATION_DS_ADDRESS,  80 );
		
				
		//acciones sobre la lista
		$this->buildAction( "add_cdtregistration_init", "add_cdtregistration_init", CDT_SECURE_MSG_CDTREGISTRATION_TITLE_ADD, "image", "add" );

	}
	
		
	/**
	 * (non-PHPdoc)
	 * @see GridModel::getTitle();
	 */
	function getTitle(){
		return CDT_SECURE_MSG_CDTREGISTRATION_TITLE_LIST;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see GridModel::getEntityManager();
	 */
	public function getEntityManager(){
		return new CdtRegistrationManager();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see GridModel::getRowActionsModel( $item );
	 */
	public function getRowActionsModel( $item ){
		
		return $this->getDefaultRowActions( $item, "cdtregistration", CDT_SECURE_LBL_CDTREGISTRATION);
		
	}

	
	/**
	 * (non-PHPdoc)
	 * @see GridModel::getMsgConfirmDelete( $item );
	 */
	protected function getMsgConfirmDelete( $item ){
		
		if(!empty($item)){
			$xtpl = new XTemplate(CDT_SECURE_TEMPLATE_CDTREGISTRATION_DELETE);
	        $xtpl->assign('cd_registration', $item->getCd_registration() );
	        $xtpl->assign('question', CDT_SECURE_MSG_CONFIRM_DELETE_QUESTION );
	        $xtpl->assign('title_confirm', CDT_SECURE_MSG_CONFIRM_DELETE_TITLE );
	        $xtpl->assign('lbl_code', CDT_SECURE_LBL_CDTREGISTRATION_CD_USER );
	        $xtpl->parse('main');
	        $text = addslashes($xtpl->text('main'));
			return CdtFormatUtils::quitarEnters($text);
		}else{
			return parent::getMsgConfirmDelete( $item );
		}
        
	}
}
?>
