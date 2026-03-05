<?php

/**
 * Factory para AntjustificacionPlanilla
 *  
 * @author Marcos
 * @since 27-05-2014
 */
class AntjustificacionPlanillaFactory extends CdtGenericFactory {

    public function build($next) {

        $this->setClassName('AntjustificacionPlanilla');
        $antjustificacionplanilla = parent::build($next);
        if(array_key_exists('cd_antjustificacionplanilla',$next)){
        	$antjustificacionplanilla->setOid( $next["cd_antjustificacionplanilla"] );
        }
        
        $factory = new SubGrupoFactory();
        $factory->setAlias( CYT_TABLE_SUBGRUPO. "_" );
        $antjustificacionplanilla->setSubGrupo( $factory->build($next) );

        return $antjustificacionplanilla;
    }

}
?>
