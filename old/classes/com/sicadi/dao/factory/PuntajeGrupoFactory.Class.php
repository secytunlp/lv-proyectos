<?php

/**
 * Factory para PuntajeGrupo
 *  
 * @author Marcos
 * @since 06-12-2013
 */
class PuntajeGrupoFactory extends CdtGenericFactory {

    public function build($next) {

        $this->setClassName('PuntajeGrupo');
        $puntajegrupo = parent::build($next);
        if(array_key_exists('cd_puntajegrupo',$next)){
        	$puntajegrupo->setOid( $next["cd_puntajegrupo"] );
        }

        return $puntajegrupo;
    }

}
?>
