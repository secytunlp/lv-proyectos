<?php

/**
 * Factory para Solicitud Cargo
 *  
 * @author Marcos
 * @since 12-06-2023
 */
class SolicitudCargoFactory extends CdtGenericFactory {

    public function build($next) {
		
        $this->setClassName('SolicitudCargo');
        $solicitudCargo = parent::build($next);


        $factory = new SolicitudFactory();
        $factory->setAlias( CYT_TABLE_SOLICITUD . "_" );
        $solicitudCargo->setSolicitud( $factory->build($next) );
        
   		$factory = new CargoFactory();
        $factory->setAlias( CYT_TABLE_CARGO . "_" );
        $solicitudCargo->setCargo( $factory->build($next) );


        $factory = new DeddocFactory();
        $factory->setAlias( CYT_TABLE_DEDDOC . "_" );
        $solicitudCargo->setDeddoc( $factory->build($next) );

        $factory = new FacultadFactory();
        $factory->setAlias( CYT_TABLE_FACULTAD . "_" );
        $solicitudCargo->setFacultad( $factory->build($next) );
      
        
   		
        
        return $solicitudCargo;
    }
}
?>
