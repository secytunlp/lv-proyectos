<?php 

/** 
 * Factory para CdtActionFunction
 *  
 * @author codnet archetype builder
 * @since 09-11-2011
 */ 
class CdtActionFunctionFactory implements ICdtObjectFactory{ 

	private $aliasFunction;
	
	public function __construct( $aliasFunction=""){
		$this->setAliasFunction( $aliasFunction );
	} 
 

	public function build($next) { 

		$aliasFunction = ($this->getAliasFunction())? $this->getAliasFunction(). "_" : "";
		
		$oCdtActionFunction  = new CdtActionFunction();

		$oCdtActionFunction->setCd_actionfunction( $next[ CDT_SECURE_TABLE_CDTACTIONFUNCTION_CD_ACTIONFUNCTION] );
		$oCdtActionFunction->setCd_function( $next[ CDT_SECURE_TABLE_CDTACTIONFUNCTION_CD_FUNCTION] ); 
		$oCdtActionFunction->setDs_action( $next[ CDT_SECURE_TABLE_CDTACTIONFUNCTION_DS_ACTION] );
		
		//para el caso que se hace el join con function.
		if(array_key_exists($aliasFunction . CDT_SECURE_TABLE_CDTFUNCTION_DS_FUNCTION ,$next)){
			
			$oFactory = new CdtFunctionFactory( $this->getAliasFunction() );
			$oCdtActionFunction->setCdtFunction( $oFactory->build($next) );
		}
		 
		return $oCdtActionFunction;
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
