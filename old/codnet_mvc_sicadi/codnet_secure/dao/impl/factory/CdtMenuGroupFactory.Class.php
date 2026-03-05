<?php 

/** 
 * Factory para CdtMenuGroup
 *  
 * @author codnet archetype builder
 * @since 09-11-2011
 */ 
class CdtMenuGroupFactory implements ICdtObjectFactory{ 

	private $aliasMenuGroup;
	
	public function __construct( $aliasMenuGroup=""){
		$this->setAliasMenuGroup( $aliasMenuGroup );
	} 
	
	public function build($next) { 

		$aliasMenuGroup = ($this->getAliasMenuGroup())? $this->getAliasMenuGroup(). "_" : "";
		
		$oCdtMenuGroup  = new CdtMenuGroup();

		$oCdtMenuGroup->setCd_menugroup( $next[$aliasMenuGroup . CDT_SECURE_TABLE_CDTMENUGROUP_CD_MENUGROUP] );
		$oCdtMenuGroup->setDs_action( $next[$aliasMenuGroup . CDT_SECURE_TABLE_CDTMENUGROUP_DS_ACTION] );
		$oCdtMenuGroup->setDs_cssclass( $next[$aliasMenuGroup . CDT_SECURE_TABLE_CDTMENUGROUP_DS_CSSCLASS] );
		$oCdtMenuGroup->setDs_name( $next[$aliasMenuGroup . CDT_SECURE_TABLE_CDTMENUGROUP_DS_NAME] );
		$oCdtMenuGroup->setNu_order( $next[$aliasMenuGroup . CDT_SECURE_TABLE_CDTMENUGROUP_NU_ORDER] );
		$oCdtMenuGroup->setNu_width( $next[$aliasMenuGroup . CDT_SECURE_TABLE_CDTMENUGROUP_NU_WIDTH] );
		
		return $oCdtMenuGroup;
	}


	public function getAliasMenuGroup()
	{
	    return $this->aliasMenuGroup;
	}

	public function setAliasMenuGroup($aliasMenuGroup)
	{
	    $this->aliasMenuGroup = $aliasMenuGroup;
	}
}
?>
