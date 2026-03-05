<?php

/**
 * Factory para IntegranteAgencia
 *
 * @author Marcos
 * @since 14-08-2023
 */
class IntegranteAgenciaFactory extends CdtGenericFactory {

    public function build($next) {

        $this->setClassName('IntegranteAgencia');
        $integrante = parent::build($next);


        $factory = new DocenteFactory();
        $factory->setAlias(CYT_TABLE_DOCENTE. "_" );
        $integrante->setDocente( $factory->build($next) );






        return $integrante;
    }

}
?>
