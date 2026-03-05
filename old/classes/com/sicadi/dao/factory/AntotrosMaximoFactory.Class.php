<?php

/**
 * Factory para AntotrosMaximo
 *  
 * @author Marcos
 * @since 27-05-2014
 */
class AntotrosMaximoFactory extends CdtGenericFactory {

    public function build($next) {

        $this->setClassName('AntotrosMaximo');
        $antotrosmaximo = parent::build($next);
        if(array_key_exists('cd_antotrosmaximo',$next)){
        	$antotrosmaximo->setOid( $next["cd_antotrosmaximo"] );
        }
        
        $factory = new AntotrosPlanillaFactory();
        $factory->setAlias( CYT_TABLE_ANTOTROS_PLANILLA. "_" );
        $antotrosmaximo->setAntotrosPlanilla( $factory->build($next) );
        
        $factory = new PuntajeGrupoFactory();
        $factory->setAlias( CYT_TABLE_PUNTAJE_GRUPO. "_" );
        $antotrosmaximo->setPuntajeGrupo( $factory->build($next) );

        return $antotrosmaximo;
    }

}
?>
