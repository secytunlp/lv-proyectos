<?php 

/** 
 * Factory para CdtMenuOption
 *  
 * @author codnet archetype builder
 * @since 09-11-2011
 */ 
class CdtMenuOptionFactory implements ICdtObjectFactory{ 

	private $aliasMenuOption;
	private $aliasMenuGroup;
	private $aliasFunction;
	
	public function __construct( $aliasMenuOption="", $aliasMenuGroup="", $aliasFunction=""){
		$this->setAliasMenuGroup( $aliasMenuGroup );
		$this->setAliasMenuOption( $aliasMenuOption );	
		$this->setAliasFunction( $aliasFunction );
	} 
	
	public function build($next) { 

		$aliasMenuOption = ($this->getAliasMenuOption())? $this->getAliasMenuOption() . "_" : "";
		$aliasMenuGroup = ($this->getAliasMenuGroup())? $this->getAliasMenuGroup(). "_" : "";
		$aliasFunction = ($this->getAliasFunction())? $this->getAliasFunction(). "_" : "";
		
		
		$oCdtMenuOption  = new CdtMenuOption();

		$oCdtMenuOption->setCd_function( $next[$aliasMenuOption . CDT_SECURE_TABLE_CDTMENUOPTION_CD_FUNCTION] ); 
		$oCdtMenuOption->setCd_menugroup( $next[$aliasMenuOption . CDT_SECURE_TABLE_CDTMENUOPTION_CD_MENUGROUP] );
		$oCdtMenuOption->setCd_menuoption( $next[$aliasMenuOption . CDT_SECURE_TABLE_CDTMENUOPTION_CD_MENUOPTION] );
		$oCdtMenuOption->setDs_cssclass( $next[$aliasMenuOption . CDT_SECURE_TABLE_CDTMENUOPTION_DS_CSSCLASS] );
		$oCdtMenuOption->setDs_description( $next[$aliasMenuOption . CDT_SECURE_TABLE_CDTMENUOPTION_DS_DESCRIPTION] );
		$oCdtMenuOption->setDs_href( $next[$aliasMenuOption . CDT_SECURE_TABLE_CDTMENUOPTION_DS_HREF] );
		$oCdtMenuOption->setDs_name( $next[$aliasMenuOption . CDT_SECURE_TABLE_CDTMENUOPTION_DS_NAME] );
		$oCdtMenuOption->setNu_order( $next[$aliasMenuOption . CDT_SECURE_TABLE_CDTMENUOPTION_NU_ORDER] );
		
		//para el caso que se hace el join con el menugroup.
		if(array_key_exists($aliasMenuGroup .CDT_SECURE_TABLE_CDTMENUGROUP_DS_NAME,$next)){
			
			$oFactory = new CdtMenuGroupFactory( $this->getAliasMenuGroup() );
			$oCdtMenuOption->setCdtMenuGroup( $oFactory->build($next) );
		}
		
		//para el caso que se hace el join con function.
		if(array_key_exists($aliasFunction .CDT_SECURE_TABLE_CDTFUNCTION_DS_FUNCTION,$next)){
			
			$oFactory = new CdtFunctionFactory( $this->getAliasFunction() );
			$oCdtMenuOption->setCdtFunction( $oFactory->build($next) );
		}
		
		return $oCdtMenuOption;
	}
	

	public function getAliasMenuOption()
	{
	    return $this->aliasMenuOption;
	}

	public function setAliasMenuOption($aliasMenuOption)
	{
	    $this->aliasMenuOption = $aliasMenuOption;
	}

	public function getAliasMenuGroup()
	{
	    return $this->aliasMenuGroup;
	}

	public function setAliasMenuGroup($aliasMenuGroup)
	{
	    $this->aliasMenuGroup = $aliasMenuGroup;
	}

	public function getAliasFunction()
	{
	    return $this->aliasFunction;
	}

	public function setAliasFunction($aliasFunction)
	{
	    $this->aliasFunction = $aliasFunction;
	}
} 
?>
