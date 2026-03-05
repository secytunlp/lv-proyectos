<?php

/**
 * Factory para SubanteriorPlanilla
 *  
 * @author Marcos
 * @since 31-08-2017
 */
class SubanteriorPlanillaFactory extends CdtGenericFactory {

    public function build($next) {

        $this->setClassName('SubanteriorPlanilla');
        $subanteriorplanilla = parent::build($next);
        if(array_key_exists('cd_subanteriorplanilla',$next)){
        	$subanteriorplanilla->setOid( $next["cd_subanteriorplanilla"] );
        }
        
        $factory = new SubGrupoFactory();
        $factory->setAlias( CYT_TABLE_SUBGRUPO. "_" );
        $subanteriorplanilla->setSubGrupo( $factory->build($next) );

        return $subanteriorplanilla;
    }

}
?>
