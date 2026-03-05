<?php

/**
 * Factory para PosgradoMaximo
 *  
 * @author Marcos
 * @since 05-12-2013
 */
class PosgradoMaximoFactory extends CdtGenericFactory {

    public function build($next) {

        $this->setClassName('PosgradoMaximo');
        $posgradomaximo = parent::build($next);
        if(array_key_exists('cd_posgradomaximo',$next)){
        	$posgradomaximo->setOid( $next["cd_posgradomaximo"] );
        }
        
        $factory = new PosgradoPlanillaFactory();
        $factory->setAlias( CYT_TABLE_POSGRADOPLANILLA. "_" );
        $posgradomaximo->setPosgradoPlanilla( $factory->build($next) );
        
        $factory = new PuntajeGrupoFactory();
        $factory->setAlias( CYT_TABLE_PUNTAJE_GRUPO. "_" );
        $posgradomaximo->setPuntajeGrupo( $factory->build($next) );

        return $posgradomaximo;
    }

}
?>
