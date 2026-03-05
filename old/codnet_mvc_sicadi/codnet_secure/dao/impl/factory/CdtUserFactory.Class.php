<?php 

/** 
 * Factory para CdtUser
 *  
 * @author codnet archetype builder
 * @since 26-10-2011
 */ 
class CdtUserFactory implements ICdtObjectFactory{ 

	private $aliasUser;
	private $aliasUserGroup;
	
	public function __construct( $aliasUser="", $aliasUserGroup=""){
		$this->setAliasUser( $aliasUser );
		$this->setAliasUserGroup( $aliasUserGroup );	
	} 
	public function build($next) { 

		$aliasUser = ($this->getAliasUser())? $this->getAliasUser() . "_" : "";
		$aliasUserGroup = ($this->getAliasUserGroup())? $this->getAliasUserGroup(). "_" : "";
		
		$oUser = new CdtUser();
		
		$oUser->setDs_username ( $next [$aliasUser . CDT_SECURE_TABLE_CDTUSER_DS_USERNAME] );
		$oUser->setCd_user( $next [$aliasUser .CDT_SECURE_TABLE_CDTUSER_CD_USER] );
		$oUser->setCd_usergroup ( $next [$aliasUser .CDT_SECURE_TABLE_CDTUSER_CD_USERGROUP] );
		$oUser->setDs_name ( $next [$aliasUser .CDT_SECURE_TABLE_CDTUSER_DS_NAME] );
		$oUser->setDs_email ( $next [$aliasUser .CDT_SECURE_TABLE_CDTUSER_DS_EMAIL] );
		$oUser->setDs_password ( $next [$aliasUser .CDT_SECURE_TABLE_CDTUSER_DS_PASSWORD] );
		$oUser->setDs_phone ( $next [$aliasUser .CDT_SECURE_TABLE_CDTUSER_DS_PHONE] );
		$oUser->setDs_address ( $next [$aliasUser .CDT_SECURE_TABLE_CDTUSER_DS_ADDRESS] );
		$oUser->setDs_ips ( $next [$aliasUser .CDT_SECURE_TABLE_CDTUSER_DS_IPS] );
		$oUser->setNu_attemps( $next [$aliasUser .CDT_SECURE_TABLE_CDTUSER_NU_ATTEMPS] );
		
		//para el caso que se hace el join con el usergroup.
		if(array_key_exists($aliasUserGroup .CDT_SECURE_TABLE_CDTUSERGROUP_DS_USERGROUP,$next)){
			
			$oFactory = new CdtUserGroupFactory( $this->getAliasUserGroup() );
			$oUser->setCdtUserGroup( $oFactory->build($next) );
		}
		
		return $oUser;
	}

	public function getAliasUser()
	{
	    return $this->aliasUser;
	}

	public function setAliasUser($aliasUser)
	{
	    $this->aliasUser = $aliasUser;
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
