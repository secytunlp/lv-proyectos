<?php 

/**
 * 
 * @author bernardo
 * @since 31-03-2010
 * 
 * Manager para localidades.
 * 
 */
class LocalidadManager extends ReferenciaManager{

	protected function getReferenciaQuery(){
		return new LocalidadQuery();
	}

	public function agregarLocalidad(Localidad $oLocalidad){

		//persistir ocalidad en la bbdd.
		LocalidadQuery::insertLocalidad( $oLocalidad );

	}

	/**
	 * se modifica una localidad.
	 * @param Localidad $oLocalidad a modificar.
	 */
	public function modificarLocalidad(Localidad $oLocalidad){

		//persistir los cambios de la localidad en la bbdd.
		LocalidadQuery::modificarLocalidad($oLocalidad);

	}


	/**
	 * eliminar una localidad.
	 * @param $cd_localidad identificador de la localidad a eliminar
	 */
	public function eliminarLocalidad($cd_localidad){
		$oLocalidad = new Localidad();
		$oLocalidad->setCd_localidad ( $cd_localidad );
		LocalidadQuery::eliminarLocalidad($oLocalidad);
	}

	/**
	 * se listan localidades.
	 * @param $criterio
	 * @return unknown_type
	 */
	public function getLocalidades(CriterioBusqueda $criterio){

		$localidades = LocalidadQuery::getlocalidades($criterio);

		return $localidades;
	}



	/**
	 * obtiene una localidad específico dado un identificador.
	 * @param $cd_localidad identificador de la localidad a recuperar 
	 * @return unknown_type
	 */
	public function getLocalidadPorId($cd_localidad){
		$criterio = new CriterioBusqueda();
		$criterio->addFiltro('L.cd_localidad', $cd_localidad, '=');
		$oLocalidad =  LocalidadQuery::getLocalidad ( $criterio );
		return $oLocalidad;
	}

	/**
	 * obtiene la cantidad de localidades dado un filtro.
	 * @param $filtro filtro de búsqueda. 
	 * @return cantidad de localidades
	 */
	public function getCantidadLocalidades( CriterioBusqueda $criterio){
		$cant =  LocalidadQuery::getCantLocalidades( $criterio );
		return $cant;
	}


	//INTERFACE IListar.

	function getEntidades ( CriterioBusqueda $criterio ){
		return  $this->getLocalidades( $criterio );
	}

	function getCantidadEntidades ( CriterioBusqueda $criterio){
		return $this->getCantidadLocalidades( $criterio );
	}

}