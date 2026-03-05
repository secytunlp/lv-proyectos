<?php

/**
 * Factory para PuntajePosgrado
 *  
 * @author Marcos
 * @since 22-05-2014
 */
class PuntajePosgradoFactory extends CdtGenericFactory {

    public function build($next) {

        $this->setClassName('PuntajePosgrado');
        $puntajeposgrado = parent::build($next);
        if(array_key_exists('cd_puntajeposgrado',$next)){
        	$puntajeposgrado->setOid( $next["cd_puntajeposgrado"] );
        }
        
        
        $factory = new EvaluacionFactory();
        $factory->setAlias( CYT_TABLE_EVALUACION . "_" );
        $puntajeposgrado->setEvaluacion( $factory->build($next) );
        
        $factory = new ModeloPlanillaFactory();
        $factory->setAlias( CYT_TABLE_MODELO_PLANILLA . "_" );
        $puntajeposgrado->setModeloPlanilla( $factory->build($next) );
        
        $factory = new PosgradoMaximoFactory();
        $factory->setAlias( CYT_TABLE_POSGRADOMAXIMO . "_" );
        $puntajeposgrado->setPosgradoMaximo( $factory->build($next) );

        return $puntajeposgrado;
    }

}
?>
