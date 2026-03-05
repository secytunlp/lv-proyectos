<?php

/**
 * Factory para DAOs
 *
 * @author Marcos
 * @since 13-11-2013
 */
class DAOFactory{

	
	
	public static function getSolicitudDAO(){
		return new SolicitudDAO();
	}
	
	public static function getOtrosProyectoDAO(){
		return new OtrosProyectoDAO();
	}

	public static function getProyectoAgenciaDAO(){
		return new ProyectoAgenciaDAO();
	}

	public static function getIntegranteAgenciaDAO(){
		return new IntegranteAgenciaDAO();
	}
	
	public static function getPresupuestoDAO(){
		return new PresupuestoDAO();
	}
	
	public static function getModeloPlanillaDAO(){
		return new ModeloPlanillaDAO();
	}
	
	
	public static function getCargoMaximoDAO(){
		return new CargoMaximoDAO();
	}
	
	public static function getCargoPlanillaDAO(){
		return new CargoPlanillaDAO();
	}
	
	public static function getPuntajeCargoDAO(){
		return new PuntajeCargoDAO();
	}
	
	public static function getPuntajeGrupoDAO(){
		return new PuntajeGrupoDAO();
	}
	
	public static function getPosgradoPlanillaDAO(){
		return new PosgradoPlanillaDAO();
	}
	
	public static function getPosgradoMaximoDAO(){
		return new PosgradoMaximoDAO();
	}
	
	public static function getPuntajePosgradoDAO(){
		return new PuntajePosgradoDAO();
	}
	
	public static function getAntacadPlanillaDAO(){
		return new AntacadPlanillaDAO();
	}
	
	public static function getAntacadMaximoDAO(){
		return new AntacadMaximoDAO();
	}
	
	public static function getPuntajeAntacadDAO(){
		return new PuntajeAntacadDAO();
	}
	

	
	public static function getAntotrosPlanillaDAO(){
		return new AntotrosPlanillaDAO();
	}
	
	public static function getAntotrosMaximoDAO(){
		return new AntotrosMaximoDAO();
	}
	
	public static function getPuntajeAntotrosDAO(){
		return new PuntajeAntotrosDAO();
	}
	
	public static function getSubGrupoDAO(){
		return new SubGrupoDAO();
	}
	
	public static function getAntjustificacionPlanillaDAO(){
		return new AntjustificacionPlanillaDAO();
	}
	
	public static function getAntjustificacionMaximoDAO(){
		return new AntjustificacionMaximoDAO();
	}
	
	public static function getPuntajeAntjustificacionDAO(){
		return new PuntajeAntjustificacionDAO();
	}
	
	public static function getAntproduccionPlanillaDAO(){
		return new AntproduccionPlanillaDAO();
	}
	
	public static function getAntproduccionMaximoDAO(){
		return new AntproduccionMaximoDAO();
	}
	
	public static function getPuntajeAntproduccionDAO(){
		return new PuntajeAntproduccionDAO();
	}
	
	public static function getSubanteriorPlanillaDAO(){
		return new SubanteriorPlanillaDAO();
	}
	
	public static function getSubanteriorMaximoDAO(){
		return new SubanteriorMaximoDAO();
	}
	
	public static function getPuntajeSubanteriorDAO(){
		return new PuntajeSubanteriorDAO();
	}

	public static function getEquivalenciaDAO(){
		return new EquivalenciaDAO();
	}

	public static function getCategoriasicadiDAO(){
		return new CategoriasicadiDAO();
	}

	public static function getAlfabeticoDAO(){
		return new AlfabeticoDAO();
	}

	public static function getSolicitudCargoDAO(){
		return new SolicitudCargoDAO();
	}

	public static function getAreaDAO(){
		return new AreaDAO();
	}

	public static function getSubareaDAO(){
		return new SubareaDAO();
	}
}
?>
