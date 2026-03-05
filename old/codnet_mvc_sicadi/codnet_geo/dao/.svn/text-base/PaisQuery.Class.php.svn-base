<?php
/**
 * Acceso a datos para pais
 * @author bernardo
 * @since 11-03-2010
 *
 */	
class PaisQuery {

	static function getPaisPorId(Pais $obj) {
		$db = DbManager::getConnection();
		$cd_pais = $obj->getCd_pais ();
		$sql = "SELECT cd_pais, ds_pais FROM ". CDT_GEO_TABLA_PAIS . " WHERE cd_pais = $cd_pais";
		
		$result = $db->sql_query ( $sql );
		if(!$result)//hubo un error en la bbdd.
		throw new DBException();

		if ($db->sql_numrows () > 0) {
			$pais = $db->sql_fetchassoc ( $result );
			$factory = new PaisFactory();
			$obj = $factory->build($pais);
		}
		$db->sql_freeresult($result);
		return $obj;
	}

	static function insertPais(Pais $obj) {
		$db = DbManager::getConnection();
		$ds_pais = $obj->getDs_pais();

		$sql  = "INSERT INTO ". CDT_GEO_TABLA_PAIS . " (ds_pais) ";
		$sql .= " VALUES ('$ds_pais') ";
		$result = $db->sql_query ( $sql );
		if(!$result)//hubo un error en la bbdd.
		throw new DBException($db->sql_error());
	}

	static function modificarPais(Pais $obj) {
		$db = DbManager::getConnection();
		$cd_pais = $obj->getCd_pais();
		$ds_pais = $obj->getDs_pais();
		$sql  = "UPDATE ". CDT_GEO_TABLA_PAIS . " SET cd_pais=$cd_pais, ds_pais='$ds_pais' ";
		$sql .= " WHERE cd_pais = ". $cd_pais;
		$result = $db->sql_query ( $sql );
		if(!$result)//hubo un error en la bbdd.
		throw new DBException($db->sql_error());
	}


	static function getCantPaises( CriterioBusqueda $criterio) {
		$db = DbManager::getConnection();
		$sql = "SELECT count(*) as count FROM ". CDT_GEO_TABLA_PAIS . " ";
		$sql .= $criterio->buildFiltroSinPaginar();

		$result = $db->sql_query ( $sql );
		if(!$result)//hubo un error en la bbdd.
		throw new DBException($db->sql_error());

		$next = $db->sql_fetchassoc ( $result );
		$cant = $next['count'];
		$db->sql_freeresult($result);
		return (( int ) $cant);
	}

	static function eliminarPais(Pais $obj) {
		$db = DbManager::getConnection();
		$cd_pais = $obj->getCd_pais ();
		$sql = "DELETE FROM ". CDT_GEO_TABLA_PAIS . " WHERE cd_pais = $cd_pais";
		$result = $db->sql_query ( $sql );
		if(!$result)//hubo un error en la bbdd.
		throw new DBException($db->sql_error());
	}

	static function getpaises(CriterioBusqueda $criterio) {
		$db = DbManager::getConnection();

		$sql = "SELECT PA.* FROM ". CDT_GEO_TABLA_PAIS . " PA";
		$sql .= $criterio->buildFiltro();
		$result = $db->sql_query ( $sql );
		if(!$result)//hubo un error en la bbdd.
		throw new DBException($db->sql_error());

		$paises = ResultFactory::toCollection($db,$result,new PaisFactory());
		$db->sql_freeresult($result);
		return $paises;
	}

	static function getPais( CriterioBusqueda $criterio) {
		$db = DbManager::getConnection();
		$sql = "SELECT * FROM ". CDT_GEO_TABLA_PAIS . " PA ";
		$sql .= $criterio->buildFiltro();
		$result = $db->sql_query ( $sql );
		if(!$result)//hubo un error en la bbdd.
		throw new DBException($db->sql_error());

		$pais = new Pais();
		if ($db->sql_numrows () > 0) {
			$temp = $db->sql_fetchassoc ( $result );
			$factory = new PaisFactory();
			$pais = $factory->build($temp);
		}
		$db->sql_freeresult($result);
		return $pais;
	}
}
?>