<?php
/**
 * Interface para buscar un object
 * 
 * @author bernardo
 * @since 31/05/2013
 */
interface IObjectFinder{

	
	/**
	 * Obtiene una entity dado un código.
	 * @param $text código a buscar
	 * @param $parent restricción del universo a buscar. ej: buscando localidades por código, se le podría indicar la provincia
	 */
	function getObjectByCode( $text, $parent=null );

	/**
	 * Dada una entity, retorna el código.
	 * @param $entity
	 */
	function getObjectCode( $entity );
	
	/**
	 * Dada una entity, retorna una descripción
	 * @param $entity
	 */
	function getObjectLabel( $entity );
	
	
}
?>