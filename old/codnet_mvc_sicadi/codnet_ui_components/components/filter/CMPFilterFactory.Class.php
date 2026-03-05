<?php

/**
 * Factory para CMPFilter
 *  
 * @author Bernardo
 * @since 23-05-2013
 */
class CMPFilterFactory implements ICdtObjectFactory{

	private $filter;
	
	
	public function __construct(CMPFilter $filter){
		$this->filter = $filter;
	}
	
    public function build($next) {

    	$this->filter->setName( $next["filter_name"] );
        $this->filter->setId( $next["filter_id"] );
        $this->filter->setCd_user( $next["cd_user"] );
        
        $values = $next["filter_values"];
        
        
        $this->filter->fillPersistedValues( $value );
        
    }
}
?>
