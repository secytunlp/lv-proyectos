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

		$oCdtMenuOption->setCd_function( $next[$aliasMenuOption . "cd_function"] ); 
		$oCdtMenuOption->setCd_menugroup( $next[$aliasMenuOption . "cd_menugroup"] );
		$oCdtMenuOption->setCd_menuoption( $next[$aliasMenuOption . "cd_menuoption"] );
		$oCdtMenuOption->setDs_cssclass( $next[$aliasMenuOption . "ds_cssclass"] );
		$oCdtMenuOption->setDs_description( $next[$aliasMenuOption . "ds_description"] );
		$oCdtMenuOption->setDs_href( $next[$aliasMenuOption . "ds_href"] );
		$oCdtMenuOption->setDs_name( $next[$aliasMenuOption . "ds_name"] );
		$oCdtMenuOption->setNu_order( $next[$aliasMenuOption . "nu_order"] );
		
		//para el caso que se hace el join con el menugroup.
		if(array_key_exists($aliasMenuGroup .'ds_name',$next)){
			
			$oFactory = new CdtMenuGroupFactory( $this->getAliasMenuGroup() );
			$oCdtMenuOption->setCdtMenuGroup( $oFactory->build($next) );
		}
		
		//para el caso que se hace el join con function.
		if(array_key_exists($aliasFunction .'ds_function',$next)){
			
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
