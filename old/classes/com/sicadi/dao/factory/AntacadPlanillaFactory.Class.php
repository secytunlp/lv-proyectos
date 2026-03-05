<?php

/**
 * Factory para AntacadPlanilla
 *  
 * @author Marcos
 * @since 23-05-2014
 */
class AntacadPlanillaFactory extends CdtGenericFactory {

    public function build($next) {

        $this->setClassName('AntacadPlanilla');
        $antacadplanilla = parent::build($next);
        if(array_key_exists('cd_antacadplanilla',$next)){
        	$antacadplanilla->setOid( $next["cd_antacadplanilla"] );
        }

        return $antacadplanilla;
    }

}
?>
