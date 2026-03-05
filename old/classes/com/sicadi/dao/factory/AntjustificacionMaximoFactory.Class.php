<?php

/**
 * Factory para AntjustificacionMaximo
 *  
 * @author Marcos
 * @since 27-05-2014
 */
class AntjustificacionMaximoFactory extends CdtGenericFactory {

    public function build($next) {

        $this->setClassName('AntjustificacionMaximo');
        $antjustificacionmaximo = parent::build($next);
        if(array_key_exists('cd_antjustificacionmaximo',$next)){
        	$antjustificacionmaximo->setOid( $next["cd_antjustificacionmaximo"] );
        }
        
        $factory = new AntjustificacionPlanillaFactory();
        $factory->setAlias( CYT_TABLE_ANTJUSTIFICACION_PLANILLA. "_" );
        $antjustificacionmaximo->setAntjustificacionPlanilla( $factory->build($next) );
        
        $factory = new PuntajeGrupoFactory();
        $factory->setAlias( CYT_TABLE_PUNTAJE_GRUPO. "_" );
        $antjustificacionmaximo->setPuntajeGrupo( $factory->build($next) );

        return $antjustificacionmaximo;
    }

}
?>
