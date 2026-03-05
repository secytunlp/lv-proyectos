<?php

/**
 * Factory para AntacadMaximo
 *  
 * @author Marcos
 * @since 23-05-2014
 */
class AntacadMaximoFactory extends CdtGenericFactory {

    public function build($next) {

        $this->setClassName('AntacadMaximo');
        $antacadmaximo = parent::build($next);
        if(array_key_exists('cd_antacadmaximo',$next)){
        	$antacadmaximo->setOid( $next["cd_antacadmaximo"] );
        }
        
        $factory = new AntacadPlanillaFactory();
        $factory->setAlias( CYT_TABLE_ANTACAD_PLANILLA. "_" );
        $antacadmaximo->setAntacadPlanilla( $factory->build($next) );
        
        $factory = new PuntajeGrupoFactory();
        $factory->setAlias( CYT_TABLE_PUNTAJE_GRUPO. "_" );
        $antacadmaximo->setPuntajeGrupo( $factory->build($next) );

        return $antacadmaximo;
    }

}
?>
