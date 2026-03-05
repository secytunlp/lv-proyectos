<?php

/**
 * Factory para CargoPlanilla
 *  
 * @author Marcos
 * @since 05-12-2013
 */
class CargoPlanillaFactory extends CdtGenericFactory {

    public function build($next) {

        $this->setClassName('CargoPlanilla');
        $cargoplanilla = parent::build($next);
        if(array_key_exists('cd_cargoplanilla',$next)){
        	$cargoplanilla->setOid( $next["cd_cargoplanilla"] );
        }

        return $cargoplanilla;
    }

}
?>
