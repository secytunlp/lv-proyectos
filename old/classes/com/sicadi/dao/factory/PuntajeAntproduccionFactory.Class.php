<?php

/**
 * Factory para PuntajeAntproduccion
 *  
 * @author Marcos
 * @since 27-05-2014
 */
class PuntajeAntproduccionFactory extends CdtGenericFactory {

    public function build($next) {

        $this->setClassName('PuntajeAntproduccion');
        $puntajeantproduccion = parent::build($next);
        if(array_key_exists('cd_puntajeantproduccion',$next)){
        	$puntajeantproduccion->setOid( $next["cd_puntajeantproduccion"] );
        }
        
        
        $factory = new EvaluacionFactory();
        $factory->setAlias( CYT_TABLE_EVALUACION . "_" );
        $puntajeantproduccion->setEvaluacion( $factory->build($next) );
        
        $factory = new ModeloPlanillaFactory();
        $factory->setAlias( CYT_TABLE_MODELO_PLANILLA . "_" );
        $puntajeantproduccion->setModeloPlanilla( $factory->build($next) );
        
        $factory = new AntproduccionMaximoFactory();
        $factory->setAlias( CYT_TABLE_ANTPRODUCCION_MAXIMO . "_" );
        $puntajeantproduccion->setAntproduccionMaximo( $factory->build($next) );

        return $puntajeantproduccion;
    }

}
?>
