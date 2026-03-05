<?php

/**
 * Factory para PosgradoPlanilla
 *  
 * @author Marcos
 * @since 22-05-2014
 */
class PosgradoPlanillaFactory extends CdtGenericFactory {

    public function build($next) {

        $this->setClassName('PosgradoPlanilla');
        $posgradoplanilla = parent::build($next);
        if(array_key_exists('cd_posgradoplanilla',$next)){
        	$posgradoplanilla->setOid( $next["cd_posgradoplanilla"] );
        }

        return $posgradoplanilla;
    }

}
?>
