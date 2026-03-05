<?php

/**
 * Factory para PlanMaximo
 *  
 * @author Marcos
 * @since 05-12-2013
 */
class PlanMaximoFactory extends CdtGenericFactory {

    public function build($next) {

        $this->setClassName('PlanMaximo');
        $planmaximo = parent::build($next);
        if(array_key_exists('cd_planmaximo',$next)){
        	$planmaximo->setOid( $next["cd_planmaximo"] );
        }

        return $planmaximo;
    }

}
?>
