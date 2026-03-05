<?php

/**
 * Acción para listar entidades.
 * Cada subclase definirá la entidad concreta listar. 
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 05-03-2010
 *
 */
interface ICdtListAction {

	
	/**
	 * se arma el criterio de búsqueda para filtrar el listado.
	 * @return CdtSearchCriteria
	 */
	public function getCdtSearchCriteria();

	/**
	 * Entidad que implementa la interfaz ICdtList
	 * para poder obtener las entidades a visualizar.
	 * @return ICdtList
	 */
	public function getEntityManager();

	/**
	 * table model para describir el listado.
	 * @param ItemCollecion $items
	 * @return CdtEntitiesTableModel
	 */
	public function getEntitiesTableModel( ItemCollection $items );


	/**
	 * action asociada al listado
	 * @return string
	 */
	public function getActionList();

	
	/**
	 * forward por error.
	 * @return string
	 */
	public function getForwardError();
	

	/**
	 * obtiene el header.
	 * @param $entities entidades que se están parseando
	 * @param $oCriteria criterio de búsqueda de las entidades.
	 * @return string
	 * @return string
	 */
	public function getHeader( ItemCollection $entities, CdtSearchCriteria $oCriteria );

	/**
	 * obtiene el footer.
	 * @param $entities entidades que se están parseando
	 * @param $oCriteria criterio de búsqueda de las entidades.
	 * @return string
	 */
	public function getFooter( ItemCollection $entities, CdtSearchCriteria $oCriteria );

	/**
	 * título para el listado
	 * @return string.
	 */
	public function getTitleList();
	

	/**
	 * clase para el estilo de las filas pares
	 * @return string
	 */
	public function getRowClassEven();

	/**
	 * clase para el estilo de las filas impares
	 * @return string
	 */
	public function getRowClassOdd();
	
	/**
	 * se retorna una lista con los filtros de búsqueda.
	 * cada elemento de la lista deberá ser un array de la forma:
	 *    - vector['order']='campo orden'
	 *    - vector['label']='label de la ordenación'
	 * se puede usar el método buildFiltro(order, label) para formar dicho arreglo.   
	 * @return array( array/(string, string) )
	 */
	public function getFilters();

}