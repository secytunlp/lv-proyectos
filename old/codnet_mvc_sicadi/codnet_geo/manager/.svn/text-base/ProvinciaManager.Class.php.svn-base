<?php 

/**
 * 
 * @author bernardo
 * @since 31-03-2010
 * 
 * Manager para provincias.
 * 
 */
class ProvinciaManager extends ReferenciaManager{

	protected function getReferenciaQuery(){
		return new ProvinciaQuery();
	}

	public function agregarProvincia(Provincia $oProvincia){

		//persistir provincia en la bbdd.
		ProvinciaQuery::insertProvincia( $oProvincia );

	}

	/**
	 * se modifica una provincia.
	 * @param Provincia $oProvincia a modificar.
	 */
	
	public function modificarProvincia(Provincia $oProvincia){

		//persistir los cambios de la provincia en la bbdd.
		ProvinciaQuery::modificarProvincia($oProvincia);

	}


	/**
	 * eliminar una provincia.
	 * @param $cd_provincia identificador de la provincia a eliminar
	 */
	public function eliminarProvincia($cd_provincia){
		$oProvincia = new Provincia();
		$oProvincia->setCd_provincia ( $cd_provincia );
		ProvinciaQuery::eliminarProvincia($oProvincia);
	}

	/**
	 * se listan provinciaes.
	 * @param $criterio
	 * @return unknown_type
	 */
	public function getProvincias(CriterioBusqueda $criterio){

		$provincias = ProvinciaQuery::getprovincias($criterio);

		return $provincias;
	}



	/**
	 * obtiene una provincia específico dado un identificador.
	 * @param $cd_provincia identificador de la provincia a recuperar 
	 * @return unknown_type
	 */
	public function getProvinciaPorId($cd_provincia){
		$criterio = new CriterioBusqueda();
		$criterio->addFiltro('PR.cd_provincia', $cd_provincia, '=');
		$oProvincia =  ProvinciaQuery::getProvincia ( $criterio );
		return $oProvincia;
	}

	/**
	 * obtiene la cantidad de provincias dado un filtro.
	 * @param $filtro filtro de búsqueda. 
	 * @return cantidad de provincias
	 */
	public function getCantidadProvincias( CriterioBusqueda $criterio){
		$cant =  ProvinciaQuery::getCantProvincias( $criterio );
		return $cant;
	}


	//INTERFACE IListar.

	function getEntidades ( CriterioBusqueda $criterio ){
		return  $this->getProvincias( $criterio );
	}

	function getCantidadEntidades ( CriterioBusqueda $criterio){
		return $this->getCantidadProvincias( $criterio );
	}

}