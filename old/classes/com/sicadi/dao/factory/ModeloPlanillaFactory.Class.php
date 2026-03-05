<?php

/**
 * Factory para ModeloPlanilla
 *  
 * @author Marcos
 * @since 05-12-2013
 */
class ModeloPlanillaFactory extends CdtGenericFactory {

    public function build($next) {

        $this->setClassName('ModeloPlanilla');
        $modeloplanilla = parent::build($next);
        if(array_key_exists('cd_modeloplanilla',$next)){
        	$modeloplanilla->setOid( $next["cd_modeloplanilla"] );
        }

        $factory = new PeriodoFactory();
        $factory->setAlias( CYT_TABLE_PERIODO . "_" );
        $modeloplanilla->setPeriodo( $factory->build($next) );
        
        return $modeloplanilla;
    }

}
?>
