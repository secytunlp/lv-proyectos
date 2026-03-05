<?php

/**
 * Utilidades para el sistema.
 *
 * @author Marcos
 * @since 13-11-2013
 */
class CYTUtils {
	

	public static function getMotivosItems() {

		return CYTSecureUtils::getFilterOptionItems( ManagerFactory::getMotivoManager(), "oid", "ds_letra","","","","cd_motivo");

	}
	
	public static function getMotivosDescripcionItems() {

		return CYTSecureUtils::getFilterOptionItems( ManagerFactory::getMotivoManager(), "oid", "ds_motivo","","","","cd_motivo");

	}

    public static function getEquivalenciasItems() {

        return CYTSecureUtils::getFilterOptionItems( ManagerFactory::getEquivalenciaManager(), "oid", "ds_equivalencia");

    }

    public static function getAreasItems() {

        return CYTSecureUtils::getFilterOptionItems( ManagerFactory::getAreaManager(), "oid", "ds_area");

    }

    public static function getSubareasItems($cd_area) {

        $oCriteria = new CdtSearchCriteria();
        if ($cd_area!=null){
            $oCriteria->addFilter('cd_area', $cd_area, '=');
        }


        return CYTSecureUtils::getFilterOptionItems( ManagerFactory::getSubareaManager(), "oid", "ds_subarea","","","","cd_subarea",$oCriteria);

    }


    public static function getCategoriasItems($mostradas="") {
		$oCriteria = new CdtSearchCriteria();
        if ($mostradas) {
            
            /*$filter = new CdtSimpleExpression("cd_categoriasicadi in (".$mostradas.")");
            $oCriteria->setExpresion($filter);*/
			$values = array_map(function($value) {
				return intval($value); // Convierte a número entero (puedes ajustarlo según el tipo de dato)
			}, explode(',', $mostradas));

			// Construir una cadena de marcadores de posición para la consulta
			$placeholders = implode(',', array_fill(0, count($values), '?'));

			$oCriteria = new CdtSearchCriteria();
			$filterExpression = "cd_categoriasicadi IN ($placeholders)";

			$oCriteria->addFilterWithPlaceholders($filterExpression, $values);
        }

        return CYTSecureUtils::getFilterOptionItems( ManagerFactory::getCategoriasicadiManager(), "oid", "ds_categoriasicadi","","","","cd_categoriasicadi",$oCriteria);

    }

    public static function getLugarTrabajoItems() {

        $oCriteria = new CdtSearchCriteria();

            $oCriteria->addFilter('bl_activa', 1, '=');
        $oCriteria->addOrder('ds_unidad','ASC');


        return CYTSecureUtils::getFilterOptionItems( CYTSecureManagerFactory::getLugarTrabajoManager(), "oid", "ds_completo","","","","cd_unidad",$oCriteria);

    }

    public static function getYearItems() {
        $years = array();
        for($i = date("Y"); $i >= date("Y") - 15; $i--){

            $years[$i] = $i;
        }
        return $years;



    }

}
