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

		$oCdtActionFunction->setCd_actionfunction( $next[ "cd_actionfunction"] );
		$oCdtActionFunction->setCd_function( $next[ "cd_function"] ); 
		$oCdtActionFunction->setDs_action( $next[ "ds_action"] );
		
		//para el caso que se hace el join con function.
		if(array_key_exists($aliasFunction .'ds_function',$next)){
			
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
