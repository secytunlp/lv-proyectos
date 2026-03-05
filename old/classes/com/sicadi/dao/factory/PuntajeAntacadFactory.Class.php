<?php

/**
 * Factory para PuntajeAntacad
 *  
 * @author Marcos
 * @since 23-05-2014
 */
class PuntajeAntacadFactory extends CdtGenericFactory {

    public function build($next) {

        $this->setClassName('PuntajeAntacad');
        $puntajeantacad = parent::build($next);
        if(array_key_exists('cd_puntajeantacad',$next)){
        	$puntajeantacad->setOid( $next["cd_puntajeantacad"] );
        }
        
        
        $factory = new EvaluacionFactory();
        $factory->setAlias( CYT_TABLE_EVALUACION . "_" );
        $puntajeantacad->setEvaluacion( $factory->build($next) );
        
        $factory = new ModeloPlanillaFactory();
        $factory->setAlias( CYT_TABLE_MODELO_PLANILLA . "_" );
        $puntajeantacad->setModeloPlanilla( $factory->build($next) );
        
        $factory = new AntacadMaximoFactory();
        $factory->setAlias( CYT_TABLE_ANTACAD_MAXIMO . "_" );
        $puntajeantacad->setAntacadMaximo( $factory->build($next) );

        return $puntajeantacad;
    }

}
?>
