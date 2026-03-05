<?php

/**
 * Factory para Otros Proyecto
 *  
 * @author Marcos
 * @since 04-04-2023
 */
class OtrosProyectoFactory extends CdtGenericFactory {

    public function build($next) {
		
        $this->setClassName('OtrosProyecto');
        $otrosProyecto = parent::build($next);
    	if(array_key_exists('dt_desde',$next)){
        	$otrosProyecto->setDt_desdeproyecto( $next["dt_desde"] );
        }
    	if(array_key_exists('dt_hasta',$next)){
        	$otrosProyecto->setDt_hastaproyecto( $next["dt_hasta"] );
        }

        $factory = new SolicitudFactory();
        $factory->setAlias( CYT_TABLE_SOLICITUD . "_" );
        $otrosProyecto->setSolicitud( $factory->build($next) );
        
   		$factory = new ProyectoFactory();
        $factory->setAlias( CYT_TABLE_PROYECTO . "_" );
        $otrosProyecto->setProyecto( $factory->build($next) );
        
        
      
        
   		
        
        return $otrosProyecto;
    }
}
?>
