<?php

/**
 * Factory para Alfabetico
 *  
 * @author Marcos
 * @since 09-06-2023
 */
class AlfabeticoFactory extends CdtGenericFactory {

    public function build($next) {

        $this->setClassName('Alfabetico');
        $alfabetico = parent::build($next);

        

        
        $factory = new CargoFactory();
        $factory->setAlias( CYT_TABLE_CARGO . "_" );
        $alfabetico->setCargo( $factory->build($next) );
        
        $factory = new DeddocFactory();
        $factory->setAlias( CYT_TABLE_DEDDOC . "_" );
        $alfabetico->setDeddoc( $factory->build($next) );
        
        $factory = new FacultadFactory();
        $factory->setAlias( CYT_TABLE_FACULTAD . "_" );
        $alfabetico->setFacultad( $factory->build($next) );
        

       
        return $alfabetico;
    }

}
?>
