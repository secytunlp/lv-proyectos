<?php

/**
 * Utilidades para los queries.
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 03-05-2010
 */
class CdtQueryUtils{

	public static function buildWHERE_ORDERBY_LIMIT($campoFiltro, $filtro, $campoOrden, $orden, $default_orden, $page, $row_per_page){
		$sql  = self::buildWHERE( $campoFiltro, $filtro);
		$sql .= self::buildORDERBY( $campoOrden, $orden, $default_orden);
		$sql .= self::buildLIMIT( $page, $row_per_page);
		return $sql;
	}
		
	public static function buildWHERE($campoFiltro, $filtro){
		$where = '' ;
		if(!empty($campoFiltro)){
			$where = " WHERE $campoFiltro LIKE '%$filtro%'";
		}
		return $where;		
	}
		
		
	public static function buildORDERBY($campoOrden, $orden, $default_orden){
		$order = '' ;
		if(!empty($campoOrden)){
			$order = " ORDER BY $campoOrden $orden ";
		}else
			$order = " ORDER BY $default_orden ";
		
		return $order;		
	}
	
		
	public static function buildLIMIT($page, $row_per_page){
		$limit='';
		if(!empty($page)){
			$limitInf = (($page - 1) * $row_per_page);
			$limit .= " LIMIT $limitInf,$row_per_page";			
		}	
		return $limit;		
	}
	
		
	
}
