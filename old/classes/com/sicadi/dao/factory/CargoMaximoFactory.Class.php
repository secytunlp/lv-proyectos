<?php

/**
 * Factory para CargoMaximo
 *  
 * @author Marcos
 * @since 05-12-2013
 */
class CargoMaximoFactory extends CdtGenericFactory {

    public function build($next) {

        $this->setClassName('CargoMaximo');
        $cargomaximo = parent::build($next);
        if(array_key_exists('cd_cargomaximo',$next)){
        	$cargomaximo->setOid( $next["cd_cargomaximo"] );
        }
        
        $factory = new CargoPlanillaFactory();
        $factory->setAlias( CYT_TABLE_CARGO_PLANILLA. "_" );
        $cargomaximo->setCargoPlanilla( $factory->build($next) );

        return $cargomaximo;
    }

}
?>
