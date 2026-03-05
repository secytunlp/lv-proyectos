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
		if (isset($next[$aliasMenuGroup . "cd_menugroup"])) {
			$oCdtMenuGroup->setCd_menugroup( $next[$aliasMenuGroup . "cd_menugroup"] );
		}
		if (isset($next[$aliasMenuGroup . "ds_action"])) {
			$oCdtMenuGroup->setDs_action( $next[$aliasMenuGroup . "ds_action"] );
		}
		if (isset($next[$aliasMenuGroup . "ds_cssclass"])) {
			$oCdtMenuGroup->setDs_cssclass( $next[$aliasMenuGroup . "ds_cssclass"] );
		}
		if (isset($next[$aliasMenuGroup . "ds_name"])) {
			$oCdtMenuGroup->setDs_name( $next[$aliasMenuGroup . "ds_name"] );
		}
		if (isset($next[$aliasMenuGroup . "nu_order"])) {
			$oCdtMenuGroup->setNu_order( $next[$aliasMenuGroup . "nu_order"] );
		}
		if (isset($next[$aliasMenuGroup . "nu_width"])) {
			$oCdtMenuGroup->setNu_width( $next[$aliasMenuGroup . "nu_width"] );
		}
		
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
