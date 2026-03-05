<?php 

/**
 * 
 * @author bernardo
 * @since 31-03-2010
 * 
 * Manager para paises.
 * 
 */
class PaisManager extends ReferenciaManager{

	protected function getReferenciaQuery(){
		return new PaisQuery();
	}

	public function agregarPais(Pais $oPais){

		//persistir pais en la bbdd.
		PaisQuery::insertPais( $oPais );

	}

	/**
	 * se modifica una pais.
	 * @param Pais $oPais a modificar.
	 */
	
	public function modificarPais(Pais $oPais){

		//persistir los cambios de la pais en la bbdd.
		PaisQuery::modificarPais($oPais);

	}


	/**
	 * eliminar una pais.
	 * @param $cd_pais identificador de la pais a eliminar
	 */
	public function eliminarPais($cd_pais){
		$oPais = new Pais();
		$oPais->setCd_pais ( $cd_pais );
		PaisQuery::eliminarPais($oPais);
	}

	/**
	 * se listan paises.
	 * @param $criterio
	 * @return unknown_type
	 */
	public function getPaises(CriterioBusqueda $criterio=null){

		if( empty($criterio))
			$criterio = new CriterioBusqueda();
		$paises = PaisQuery::getpaises($criterio);

		return $paises;
	}



	/**
	 * obtiene una pais específico dado un identificador.
	 * @param $cd_pais identificador de la pais a recuperar 
	 * @return unknown_type
	 */
	public function getPaisPorId($cd_pais){
		$criterio = new CriterioBusqueda();
		$criterio->addFiltro('PA.cd_pais', $cd_pais, '=');
		$oPais =  PaisQuery::getPais ( $criterio );
		return $oPais;
	}

	/**
	 * obtiene la cantidad de paises dado un filtro.
	 * @param $filtro filtro de búsqueda. 
	 * @return cantidad de paises
	 */
	public function getCantidadPaises( CriterioBusqueda $criterio){
		$cant =  PaisQuery::getCantPaises( $criterio );
		return $cant;
	}


	//INTERFACE IListar.

	function getEntidades ( CriterioBusqueda $criterio ){
		return  $this->getPaises( $criterio );
	}

	function getCantidadEntidades ( CriterioBusqueda $criterio){
		return $this->getCantidadPaises( $criterio );
	}

}