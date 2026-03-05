<?php

/**
 * Factory para PuntajeCargo
 *  
 * @author Marcos
 * @since 05-12-2013
 */
class PuntajeCargoFactory extends CdtGenericFactory {

    public function build($next) {

        $this->setClassName('PuntajeCargo');
        $puntajecargo = parent::build($next);
        if(array_key_exists('cd_puntajecargo',$next)){
        	$puntajecargo->setOid( $next["cd_puntajecargo"] );
        }
        
        $factory = new CargoMaximoFactory();
        $factory->setAlias( CYT_TABLE_CARGO_MAXIMO . "_" );
        $puntajecargo->setCargoMaximo( $factory->build($next) );

        return $puntajecargo;
    }

}
?>
