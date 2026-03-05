<?php

/**
 * Factory para Presupuesto
 *  
 * @author Marcos
 * @since 22-11-2013
 */
class PresupuestoFactory extends CdtGenericFactory {

    public function build($next) {

        $this->setClassName('Presupuesto');
        $presupuesto = parent::build($next);
        $presupuesto->setNu_montopresupuesto($next['nu_monto']);
        $presupuestoArray = explode('|', $presupuesto->getDs_presupuesto());
        $presupuesto->setDs_presupuesto($presupuestoArray[0]);
    	switch ($presupuesto->getDs_presupuesto()) {
				case CYT_CD_VIATICO:
				$presupuesto->setDs_dias($presupuestoArray[1]);
				$presupuesto->setDs_lugar($presupuestoArray[2]);
				break;
				
				case CYT_DS_PASAJE:
				
				$presupuesto->setDs_pasajes($presupuestoArray[1]);
				$presupuesto->setDs_destino($presupuestoArray[2]);
				break;
				
				case CYT_CD_INSCRIPCION:
				$presupuesto->setDs_inscripcion($presupuestoArray[1]);
				break;
				
				case CYT_CD_OTROS:
				$presupuesto->setDs_otros($presupuestoArray[1]);
				break;
			}
                
        $factory = new SolicitudFactory();
        $factory->setAlias( CYT_TABLE_SOLICITUD. "_" );
        $presupuesto->setSolicitud( $factory->build($next) );
        
        $factory = new TipoPresupuestoFactory();
        $factory->setAlias( CYT_TABLE_TIPO_PRESUPUESTO. "_" );
        $presupuesto->setTipoPresupuesto( $factory->build($next) );
		
        return $presupuesto;
    }

}
?>
