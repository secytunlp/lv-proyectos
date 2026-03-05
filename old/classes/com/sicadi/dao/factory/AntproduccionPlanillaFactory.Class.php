<?php

/**
 * Factory para AntproduccionPlanilla
 *  
 * @author Marcos
 * @since 28-05-2014
 */
class AntproduccionPlanillaFactory extends CdtGenericFactory {

    public function build($next) {

        $this->setClassName('AntproduccionPlanilla');
        $antproduccionplanilla = parent::build($next);
        if(array_key_exists('cd_antproduccionplanilla',$next)){
        	$antproduccionplanilla->setOid( $next["cd_antproduccionplanilla"] );
        }
        
        $factory = new SubGrupoFactory();
        $factory->setAlias( CYT_TABLE_SUBGRUPO. "_" );
        $antproduccionplanilla->setSubGrupo( $factory->build($next) );

        return $antproduccionplanilla;
    }

}
?>
