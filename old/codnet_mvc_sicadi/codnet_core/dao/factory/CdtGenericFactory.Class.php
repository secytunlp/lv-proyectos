<?php
/**
 * Construye un objeto utilizando sus propiedades con reflection.
 *  
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 04-03-2010  
 *
 */

class CdtGenericFactory implements ICdtObjectFactory{
	
	//nombre de la clase a construir.
	private $className ;

	private $alias="";
	
	/* Getters & Setters */
	public function getClassName(){
		return $this->className;		
	}
	
	public function setClassName($value){
		$this->className = $value;		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see ICdtObjectFactory::build();
	 */
	public function build($next ){
		
		$oObject = CdtReflectionUtils::newInstance( $this->getClassName() );
		
		if($next){
			foreach ($next as $field => $value) {
			
			$alias = $this->getAlias();
			if( !empty($alias) ){
				$field_prefix = substr($field, 0, (strlen($alias)) ) ;
				if( $field_prefix == $alias ){
					$field = substr($field, strlen($alias) );
				}
			}
			
			try{

				CdtReflectionUtils::doSetter( $oObject, $field, $value );
				
			}catch(ReflectionException $re){
				
			}
		}
		}
		
		return $oObject;
	}

	public function getAlias()
	{
	    return $this->alias;
	}

	public function setAlias($alias)
	{
	    $this->alias = $alias;
	}
}
?>