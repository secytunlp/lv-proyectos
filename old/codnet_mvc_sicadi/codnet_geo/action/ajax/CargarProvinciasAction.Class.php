<?php
/**
 * Acción para cargar provincias en un combo
 * utilizando Ajax.
 * 
 * @author bernardo
 * @since 11-03-2010
 *
 */
class CargarProvinciasAction extends CargarEntidadesAction{

	function CargarProvinciasAction(){
		$this->ds_id='provincia';	
		$this->ds_label='Provincia';
		$this->ds_parentId='pais';
	}

	/**
	 * obtiene entidades relacionadas con parent.
	 * @param unknown_type $parent
	 * @return ItemCollection
	 */
	public function getEntidades($parent){
		$manager = new LocalizacionManager();
		try{
			$provincias = $manager->getProvinciasPorPais($parent);
		}catch(GenericException $ex){
			$provincias = new ItemCollection();
		}
		return $provincias;
	}
	
	/**
	 * obtiene el código de la entidad
	 * @param unknown_type $entidad
	 * @return unknown_type
	 */
	public function getCode($entidad){
		return $entidad->getCd_provincia();
	}
	
	/**
	 * otiene la descripción de la entidad 
	 * @param unknown_type $entidad
	 * @return unknown_type
	 */
	public function getDesc($entidad){
		return $entidad->getDs_provincia();
	}
	
	public function getFuncion(){
		return CDT_GEO_FUNCION_LISTAR_PROVINCIA;
	}
	
}