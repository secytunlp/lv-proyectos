<?php

/**
 * Factory para Solicitud
 *  
 * @author Marcos
 * @since 13-11-2013
 */
class SolicitudFactory extends CdtGenericFactory {

    public function build($next) {

        $this->setClassName('Solicitud');
        $solicitud = parent::build($next);
        if(array_key_exists('cd_solicitud',$next)){
        	$solicitud->setOid( $next["cd_solicitud"] );
        }
        
        $factory = new DocenteFactory();
        $factory->setAlias( CYT_TABLE_DOCENTE . "_" );
        $solicitud->setDocente( $factory->build($next) );
        
        $factory = new PeriodoFactory();
        $factory->setAlias( CYT_TABLE_PERIODO . "_" );
        $solicitud->setPeriodo( $factory->build($next) );
        
        
        
        
        $factory = new FacultadFactory();
        $factory->setAlias( CYT_TABLE_FACULTAD . "_" );
        $solicitud->setFacultad( $factory->build($next) );
        
        $factory = new FacultadFactory();
        $factory->setAlias( "FacultadPlanilla_" );
        $solicitud->setFacultadPlanilla( $factory->build($next) );
        
        $factory = new CatFactory();
        $factory->setAlias( CYT_TABLE_CAT . "_" );
        $solicitud->setCat( $factory->build($next) );
        
        $factory = new EstadoFactory();
        $factory->setAlias( CYT_TABLE_ESTADO. "_" );
        $solicitud->setEstado( $factory->build($next) );
        
        $factory = new CargoFactory();
        $factory->setAlias( CYT_TABLE_CARGO . "_" );
        $solicitud->setCargo( $factory->build($next) );
        
        $factory = new DeddocFactory();
        $factory->setAlias( CYT_TABLE_DEDDOC . "_" );
        $solicitud->setDeddoc( $factory->build($next) );
        
        $factory = new LugarTrabajoFactory();
        $factory->setAlias( CYT_TABLE_LUGAR_TRABAJO . "_" );
        $solicitud->setLugarTrabajo( $factory->build($next) );
        
        $factory = new LugarTrabajoFactory();
        $factory->setAlias( "LugarTrabajoBeca_" );
        $solicitud->setLugarTrabajoBeca( $factory->build($next) );
        
        $factory = new LugarTrabajoFactory();
        $factory->setAlias( "LugarTrabajoCarrera_" );
        $solicitud->setLugarTrabajoCarrera( $factory->build($next) );
        
        $factory = new CarrerainvFactory();
        $factory->setAlias( CYT_TABLE_CARRERAINV . "_" );
        $solicitud->setCarrerainv( $factory->build($next) );
        
        $factory = new OrganismoFactory();
        $factory->setAlias( CYT_TABLE_ORGANISMO . "_" );
        $solicitud->setOrganismo( $factory->build($next) );
        
        $factory = new CategoriaFactory();
        $factory->setAlias( CYT_TABLE_CATEGORIA . "_" );
        $solicitud->setCategoria( $factory->build($next) );

        $factory = new CategoriasicadiFactory();
        $factory->setAlias( CYT_TABLE_CATEGORIA_SICADI. "_" );
        $solicitud->setCategoriasicadi( $factory->build($next) );


        $factory = new EquivalenciaFactory();
        $factory->setAlias( CYT_TABLE_EQUIVALENCIA . "_" );
        $solicitud->setEquivalencia( $factory->build($next) );
        
        
        $factory = new TituloFactory();
        $factory->setAlias( CYT_TABLE_TITULO . "_" );
        $solicitud->setTitulo( $factory->build($next) );
        
        $factory = new TituloFactory();
        $factory->setAlias( "Tituloposgrado_" );
        $solicitud->setTituloposgrado( $factory->build($next) );

        $factory = new AreaFactory();
        $factory->setAlias( CYT_TABLE_AREA . "_" );
        $solicitud->setAreacarrera( $factory->build($next) );

        $factory = new SubareaFactory();
        $factory->setAlias( CYT_TABLE_SUBAREA . "_" );
        $solicitud->setSubareacarrera( $factory->build($next) );

        return $solicitud;
    }

}
?>
