<?php
/**
 * Acceso a datos para provincia
 * @author bernardo
 * @since 11-03-2010
 *
 */	
class ProvinciaQuery {

	static function getProvinciaPorId(Provincia $obj) {
		$db = DbManager::getConnection();
		$cd_provincia = $obj->getCd_provincia ();
		$sql = "SELECT PR.cd_provincia, PR.ds_provincia, PA.cd_pais, PA.ds_pais FROM ". CDT_GEO_TABLA_PROVINCIA . " PR";
		$sql .= " LEFT JOIN ". CDT_GEO_TABLA_PAIS . " PA ON PA.cd_pais=PR.cd_pais ";
		$sql .= " WHERE PR.cd_provincia = $cd_provincia";
		$result = $db->sql_query ( $sql );
		if(!$result)//hubo un error en la bbdd.
		throw new DBException();

		if ($db->sql_numrows ( $result ) > 0) {
			$prov = $db->sql_fetchassoc ( $result );
			$factory = new ProvinciaFactory();
			$obj = $factory->build($prov);
		}

		$db->sql_freeresult($res);
		return $obj;
	}

	static function insertProvincia(Provincia $obj) {
		$db = DbManager::getConnection();
		$ds_provincia = $obj->getDs_provincia();
		$cd_pais = $obj->getCd_pais();

		$sql  = "INSERT INTO ". CDT_GEO_TABLA_PROVINCIA . " (ds_provincia, cd_pais) ";
		$sql .= " VALUES ('$ds_provincia', '$cd_pais') ";
		$result = $db->sql_query ( $sql );
		if(!$result)//hubo un error en la bbdd.
		throw new DBException($db->sql_error());
	}

	static function eliminarProvincia(Provincia $obj) {
		$db = DbManager::getConnection();
		$cd_provincia = $obj->getCd_provincia ();
		$sql = "DELETE FROM ". CDT_GEO_TABLA_PROVINCIA . " WHERE cd_provincia = $cd_provincia";
		$result = $db->sql_query ( $sql );
		if(!$result)//hubo un error en la bbdd.
		throw new DBException($db->sql_error());
	}

	static function modificarProvincia(Provincia $obj) {
		$db = DbManager::getConnection();
		$cd_provincia = $obj->getCd_Provincia();
		$ds_provincia = $obj->getDs_provincia();
		$cd_pais = FormatUtils::ifEmpty($obj->getCd_pais(), 'null');
		$sql  = "UPDATE ". CDT_GEO_TABLA_PROVINCIA . " SET cd_provincia=$cd_provincia, ds_provincia='$ds_provincia', cd_pais = $cd_pais";
		$sql .= " WHERE cd_provincia = ". $cd_provincia;
		$result = $db->sql_query ( $sql );
		if(!$result)//hubo un error en la bbdd.
		throw new DBException($db->sql_error());
	}

	static function getProvincias(CriterioBusqueda $criterio) {
		$db = DbManager::getConnection();

		$sql = "SELECT PA.*, PR.* FROM ". CDT_GEO_TABLA_PROVINCIA . " PR";
		$sql .= " LEFT JOIN ". CDT_GEO_TABLA_PAIS . " PA ON PR.cd_pais=PA.cd_pais ";
		$sql .= $criterio->buildFiltro();
		$result = $db->sql_query ( $sql );
		if(!$result)//hubo un error en la bbdd.
		throw new DBException($db->sql_error());

		$provincias = ResultFactory::toCollection($db,$result,new ProvinciaFactory());
		$db->sql_freeresult($result);
		return $provincias;
	}

	static function getCantProvincias( CriterioBusqueda $criterio) {
		$db = DbManager::getConnection();
		$sql = "SELECT count(*) as count FROM ". CDT_GEO_TABLA_PROVINCIA . " ";
		$sql .= $criterio->buildFiltroSinPaginar();

		$result = $db->sql_query ( $sql );
		if(!$result)//hubo un error en la bbdd.
		throw new DBException($db->sql_error());

		$next = $db->sql_fetchassoc ( $result );
		$cant = $next['count'];
		$db->sql_freeresult($result);
		return (( int ) $cant);
	}

	static function getProvinciasDePais(Pais $obj) {
		$db = DbManager::getConnection();
		$cd_pais = $obj->getCd_pais ();
		$sql = "SELECT PR.cd_provincia, PR.ds_provincia FROM ". CDT_GEO_TABLA_PROVINCIA . " PR WHERE cd_pais = $cd_pais";
		$result = $db->sql_query ( $sql );
		if(!$result)//hubo un error en la bbdd.
		throw new DBException();
		$provincias = ResultFactory::toCollection($db,$result,new ProvinciaFactory());
		$db->sql_freeresult($res);
		return $provincias;
	}

	static function getProvincia( CriterioBusqueda $criterio) {
		$db = DbManager::getConnection();
		$sql = "SELECT * FROM ". CDT_GEO_TABLA_PROVINCIA . " PR";
		$sql .= " LEFT JOIN ". CDT_GEO_TABLA_PAIS . " PA ON PA.cd_pais=PR.cd_pais";
		$sql .= $criterio->buildFiltro();

		$result = $db->sql_query ( $sql );
		if(!$result)//hubo un error en la bbdd.
		throw new DBException($db->sql_error());

		$provincia = new Provincia();
		if ($db->sql_numrows () > 0) {
			$temp = $db->sql_fetchassoc ( $result );
			$factory = new ProvinciaFactory();
			$provincia = $factory->build($temp);
		}
		$db->sql_freeresult($result);
		return $provincia;
	}
}
?>