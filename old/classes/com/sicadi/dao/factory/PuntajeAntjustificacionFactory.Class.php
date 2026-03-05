<?php

/**
 * Factory para PuntajeAntjustificacion
 *  
 * @author Marcos
 * @since 27-05-2014
 */
class PuntajeAntjustificacionFactory extends CdtGenericFactory {

    public function build($next) {

        $this->setClassName('PuntajeAntjustificacion');
        $puntajeantjustificacion = parent::build($next);
        if(array_key_exists('cd_puntajeantjustificacion',$next)){
        	$puntajeantjustificacion->setOid( $next["cd_puntajeantjustificacion"] );
        }
        
        
        $factory = new EvaluacionFactory();
        $factory->setAlias( CYT_TABLE_EVALUACION . "_" );
        $puntajeantjustificacion->setEvaluacion( $factory->build($next) );
        
        $factory = new ModeloPlanillaFactory();
        $factory->setAlias( CYT_TABLE_MODELO_PLANILLA . "_" );
        $puntajeantjustificacion->setModeloPlanilla( $factory->build($next) );
        
        $factory = new AntjustificacionMaximoFactory();
        $factory->setAlias( CYT_TABLE_ANTJUSTIFICACION_MAXIMO . "_" );
        $puntajeantjustificacion->setAntjustificacionMaximo( $factory->build($next) );

        return $puntajeantjustificacion;
    }

}
?>
