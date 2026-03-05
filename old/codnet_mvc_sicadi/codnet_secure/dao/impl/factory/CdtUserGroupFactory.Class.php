<?php 

/** 
 * Factory para CdtUserGroup
 *  
 * @author codnet archetype builder
 * @since 26-10-2011
 */ 
class CdtUserGroupFactory implements ICdtObjectFactory{  

	private $aliasUserGroup;
	
	public function __construct( $aliasUserGroup=""){

		$this->setAliasUserGroup( $aliasUserGroup );	
	} 
	
	public function build($next) { 

		$aliasUserGroup = ($this->getAliasUserGroup())? $this->getAliasUserGroup(). "_" : "";
		
		$oUserGroup = new CdtUserGroup();
		
		$oUserGroup->setDs_usergroup( $next [$aliasUserGroup . CDT_SECURE_TABLE_CDTUSERGROUP_DS_USERGROUP] );
		$oUserGroup->setCd_usergroup( $next [$aliasUserGroup .CDT_SECURE_TABLE_CDTUSERGROUP_CD_USERGROUP] );
				 
		return $oUserGroup;
	}
	
	public function getAliasUserGroup()
	{
	    return $this->aliasUserGroup;
	}

	public function setAliasUserGroup($aliasUserGroup)
	{
	    $this->aliasUserGroup = $aliasUserGroup;
	}	
} 
?>
