<?php

/**
 * Factory para AntproduccionMaximo
 *  
 * @author Marcos
 * @since 28-05-2014
 */
class AntproduccionMaximoFactory extends CdtGenericFactory {

    public function build($next) {

        $this->setClassName('AntproduccionMaximo');
        $antproduccionmaximo = parent::build($next);
        if(array_key_exists('cd_antproduccionmaximo',$next)){
        	$antproduccionmaximo->setOid( $next["cd_antproduccionmaximo"] );
        }
        
        $factory = new AntproduccionPlanillaFactory();
        $factory->setAlias( CYT_TABLE_ANTPRODUCCION_PLANILLA. "_" );
        $antproduccionmaximo->setAntproduccionPlanilla( $factory->build($next) );
        
        $factory = new PuntajeGrupoFactory();
        $factory->setAlias( CYT_TABLE_PUNTAJE_GRUPO. "_" );
        $antproduccionmaximo->setPuntajeGrupo( $factory->build($next) );

        return $antproduccionmaximo;
    }

}
?>
