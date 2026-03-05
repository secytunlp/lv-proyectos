<?php 

/** 
 * DAO para CdtUserGroup 
 *  
 * @author bernardo
 * @since 24-05-2012
 */ 
interface ICdtUserGroupDAO { 

	/**
	 * se persiste la nueva entity
	 * @param CdtUserGroup $oCdtUserGroup entity a persistir.
	 */
	function addCdtUserGroup(CdtUserGroup $oCdtUserGroup);
	
	/**
	 * se modifica la entity
	 * @param CdtUserGroup $oCdtUserGroup entity a modificar.
	 */
	function updateCdtUserGroup(CdtUserGroup $oCdtUserGroup);

	/**
	 * se elimina la entity
	 * @param CdtUserGroup $oCdtUserGroup entity a eliminar.
	 */
	function deleteCdtUserGroup(CdtUserGroup $oCdtUserGroup);

	/**
	 * se obtiene una colecci�n de entities dado el filtro de b�squeda
	 * @param CdtSearchCriteria $oCriteria filtro de b�squeda.
	 * @return ItemCollection[CdtUserGroup]
	 */
	function getCdtUserGroups(CdtSearchCriteria $oCriteria);

	
	/**
	 * se obtiene la cantidad de entities dado el filtro de b�squeda
	 * @param CdtSearchCriteria $oCriteria filtro de b�squeda.
	 * @return int
	 */
	function getCdtUserGroupsCount(CdtSearchCriteria $oCriteria);


	/**
	 * se obtiene un entity dado el filtro de b�squeda
	 * @param CdtSearchCriteria $oCriteria filtro de b�squeda.
	 * @return CdtUserGroup
	 */
	function getCdtUserGroup(CdtSearchCriteria $oCriteria);
} 
?>
