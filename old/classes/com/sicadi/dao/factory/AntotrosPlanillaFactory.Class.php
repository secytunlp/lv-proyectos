<?php

/**
 * Factory para AntotrosPlanilla
 *  
 * @author Marcos
 * @since 27-05-2014
 */
class AntotrosPlanillaFactory extends CdtGenericFactory {

    public function build($next) {

        $this->setClassName('AntotrosPlanilla');
        $antotrosplanilla = parent::build($next);
        if(array_key_exists('cd_antotrosplanilla',$next)){
        	$antotrosplanilla->setOid( $next["cd_antotrosplanilla"] );
        }
        
        $factory = new SubGrupoFactory();
        $factory->setAlias( CYT_TABLE_SUBGRUPO. "_" );
        $antotrosplanilla->setSubGrupo( $factory->build($next) );

        return $antotrosplanilla;
    }

}
?>
