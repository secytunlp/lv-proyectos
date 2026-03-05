<?php 

/** 
 * DAO para CdtMenuGroup 
 *  
 * @author bernardo
 * @since 22-05-2012
 */ 
interface ICdtMenuGroupDAO { 

	/**
	 * se persiste la nueva entity
	 * @param CdtMenuGroup $oCdtMenuGroup entity a persistir.
	 */
	public function addCdtMenuGroup(CdtMenuGroup $oCdtMenuGroup);


	/**
	 * se modifica la entity
	 * @param CdtMenuGroup $oCdtMenuGroup entity a modificar.
	 */
	public function updateCdtMenuGroup(CdtMenuGroup $oCdtMenuGroup);

	/**
	 * se elimina la entity
	 * @param CdtMenuGroup $oCdtMenuGroup entity a eliminar.
	 */
	public function deleteCdtMenuGroup(CdtMenuGroup $oCdtMenuGroup) ;

	/**
	 * se obtiene una colecci�n de entities dado el filtro de b�squeda
	 * @param CdtSearchCriteria $oCriteria filtro de b�squeda.
	 * @return ItemCollection[CdtMenuGroup]
	 */
	public function getCdtMenuGroups(CdtSearchCriteria $oCriteria);

	
	/**
	 * se obtiene la cantidad de entities dado el filtro de b�squeda
	 * @param CdtSearchCriteria $oCriteria filtro de b�squeda.
	 * @return int
	 */
	public function getCdtMenuGroupsCount(CdtSearchCriteria $oCriteria);


	/**
	 * se obtiene un entity dado el filtro de b�squeda
	 * @param CdtSearchCriteria $oCriteria filtro de b�squeda.
	 * @return CdtMenuGroup
	 */
	public function getCdtMenuGroup(CdtSearchCriteria $oCriteria);

} 
?>
