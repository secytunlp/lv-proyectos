<?php 

/**
 * Interfaz que deberán implementar los "managers"
 * para ser utilizadas en el CdtListarAction.
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 05-03-2010
 * 
 */
interface ICdtList{

	/**
	 * lista entidades dado un filtro.
	 * @param CdtSearchCriteria $oCriteria
	 * @return ItemCollection
	 */
	function getEntities ( CdtSearchCriteria $oCriteria);
	
	/**
	 * obtiene la cantidad de entidades dado un filtro.
	 * @param CdtSearchCriteria $oCriteria filtro de búsqueda
	 * @return ItemCollection
	 */
	function getEntitiesCount ( CdtSearchCriteria $oCriteria );
	
	
}