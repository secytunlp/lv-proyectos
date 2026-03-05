<?php

/**
 * Factory para SubanteriorMaximo
 *  
 * @author Marcos
 * @since 31-08-2017
 */
class SubanteriorMaximoFactory extends CdtGenericFactory {

    public function build($next) {

        $this->setClassName('SubanteriorMaximo');
        $subanteriormaximo = parent::build($next);
        if(array_key_exists('cd_subanteriormaximo',$next)){
        	$subanteriormaximo->setOid( $next["cd_subanteriormaximo"] );
        }
        
        $factory = new SubanteriorPlanillaFactory();
        $factory->setAlias( CYT_TABLE_SUBANTERIOR_PLANILLA. "_" );
        $subanteriormaximo->setSubanteriorPlanilla( $factory->build($next) );
        
        $factory = new PuntajeGrupoFactory();
        $factory->setAlias( CYT_TABLE_PUNTAJE_GRUPO. "_" );
        $subanteriormaximo->setPuntajeGrupo( $factory->build($next) );

        return $subanteriormaximo;
    }

}
?>
