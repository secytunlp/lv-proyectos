<?php

/**
 * Factory para Equivalencia
 *  
 * @author Marcos
 * @since 10-04-2023
 */
class EquivalenciaFactory extends CdtGenericFactory {

    public function build($next) {

        $this->setClassName('Equivalencia');
        $equivalencia = parent::build($next);
        if(array_key_exists('cd_equivalencia',$next)){
        	$equivalencia->setOid( $next["cd_equivalencia"] );
        }

        return $equivalencia;
    }

}
?>
