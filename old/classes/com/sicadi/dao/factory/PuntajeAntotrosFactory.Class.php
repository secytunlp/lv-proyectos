<?php

/**
 * Factory para PuntajeAntotros
 *  
 * @author Marcos
 * @since 27-05-2014
 */
class PuntajeAntotrosFactory extends CdtGenericFactory {

    public function build($next) {

        $this->setClassName('PuntajeAntotros');
        $puntajeantotros = parent::build($next);
        if(array_key_exists('cd_puntajeantotros',$next)){
        	$puntajeantotros->setOid( $next["cd_puntajeantotros"] );
        }
        
        
        $factory = new EvaluacionFactory();
        $factory->setAlias( CYT_TABLE_EVALUACION . "_" );
        $puntajeantotros->setEvaluacion( $factory->build($next) );
        
        $factory = new ModeloPlanillaFactory();
        $factory->setAlias( CYT_TABLE_MODELO_PLANILLA . "_" );
        $puntajeantotros->setModeloPlanilla( $factory->build($next) );
        
        $factory = new AntotrosMaximoFactory();
        $factory->setAlias( CYT_TABLE_ANTOTROS_MAXIMO . "_" );
        $puntajeantotros->setAntotrosMaximo( $factory->build($next) );

        return $puntajeantotros;
    }

}
?>
