<?php
/**
 * Acción para cargar localidades en un combo
 * utilizando Ajax.
 *
 * @author bernardo
 * @since 17-03-2010
 *
 */
class CargarLocalidadesAction extends CargarEntidadesAction{


	function CargarLocalidadesAction(){
		$this->ds_id='localidad';
		$this->ds_label='Localidad';
		$this->ds_parentId='provincia';
	}

	public function getEntidades($parent){
		$manager = new LocalizacionManager();
		try{
			$localidades = $manager->getLocalidadesPorProvincia($parent);
		}catch(GenericException $ex){
			$localidades = new ItemCollection();
		}
		return $localidades;
	}

	public function getCode($entidad){
		return $entidad->getCd_localidad();
	}

	public function getDesc($entidad){
		return $entidad->getDs_localidad();
	}

	public function getFuncion(){
		return CDT_GEO_FUNCION_LISTAR_LOCALIDAD;
	}
}