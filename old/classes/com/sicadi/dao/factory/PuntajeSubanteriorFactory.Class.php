<?php

/**
 * Factory para PuntajeSubanterior
 *  
 * @author Marcos
 * @since 31-08-2017
 */
class PuntajeSubanteriorFactory extends CdtGenericFactory {

    public function build($next) {

        $this->setClassName('PuntajeSubanterior');
        $puntajesubanterior = parent::build($next);
        if(array_key_exists('cd_puntajesubanterior',$next)){
        	$puntajesubanterior->setOid( $next["cd_puntajesubanterior"] );
        }
        
        
        $factory = new EvaluacionFactory();
        $factory->setAlias( CYT_TABLE_EVALUACION . "_" );
        $puntajesubanterior->setEvaluacion( $factory->build($next) );
        
        $factory = new ModeloPlanillaFactory();
        $factory->setAlias( CYT_TABLE_MODELO_PLANILLA . "_" );
        $puntajesubanterior->setModeloPlanilla( $factory->build($next) );
        
        $factory = new SubanteriorMaximoFactory();
        $factory->setAlias( CYT_TABLE_SUBANTERIOR_MAXIMO . "_" );
        $puntajesubanterior->setSubanteriorMaximo( $factory->build($next) );

        return $puntajesubanterior;
    }

}
?>
