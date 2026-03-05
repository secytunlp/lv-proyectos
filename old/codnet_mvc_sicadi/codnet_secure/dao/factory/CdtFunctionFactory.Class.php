<?php 

/** 
 * Factory para CdtFunction
 *  
 * @author codnet archetype builder
 * @since 09-11-2011
 */ 
class CdtFunctionFactory implements ICdtObjectFactory{ 

	private $aliasFunction;
	
	public function __construct( $aliasFunction=""){
		$this->setAliasFunction( $aliasFunction );
	} 
	
	public function build($next) { 

		$aliasFunction = ($this->getAliasFunction())? $this->getAliasFunction(). "_" : "";
		
		$oCdtFunction  = new CdtFunction();

		$oCdtFunction->setCd_function( $next[$aliasFunction . "cd_function"] );
		$oCdtFunction->setDs_function( $next[$aliasFunction . "ds_function"] );
		
		return $oCdtFunction;
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
