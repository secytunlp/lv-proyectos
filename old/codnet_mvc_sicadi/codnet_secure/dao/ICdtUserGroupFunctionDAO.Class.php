<?php 

/** 
 * DAO para CdtUserGroupFunction 
 *  
 * @author bernardo
 * @since 24-05-2012
 */ 
interface ICdtUserGroupFunctionDAO { 

	/**
	 * se persiste la nueva entity
	 * @param CdtUserGroupFunction $oCdtUserGroupFunction entity a persistir.
	 */
	function addCdtUserGroupFunction(CdtUserGroupFunction $oCdtUserGroupFunction);


	/**
	 * se modifica la entity
	 * @param CdtUserGroupFunction $oCdtUserGroupFunction entity a modificar.
	 */
	function updateCdtUserGroupFunction(CdtUserGroupFunction $oCdtUserGroupFunction);

	/**
	 * se elimina la entity
	 * @param CdtUserGroupFunction $oCdtUserGroupFunction entity a eliminar.
	 */
	function deleteCdtUserGroupFunction(CdtUserGroupFunction $oCdtUserGroupFunction);

	/**
	 * se obtiene una colecci�n de entities dado el filtro de b�squeda
	 * @param CdtSearchCriteria $oCriteria filtro de b�squeda.
	 * @return ItemCollection[CdtUserGroupFunction]
	 */
	function getCdtUserGroupFunctions(CdtSearchCriteria $oCriteria);

	
	/**
	 * se obtiene la cantidad de entities dado el filtro de b�squeda
	 * @param CdtSearchCriteria $oCriteria filtro de b�squeda.
	 * @return int
	 */
	function getCdtUserGroupFunctionsCount(CdtSearchCriteria $oCriteria);


	/**
	 * se obtiene un entity dado el filtro de b�squeda
	 * @param CdtSearchCriteria $oCriteria filtro de b�squeda.
	 * @return CdtUserGroupFunction
	 */
	function getCdtUserGroupFunction(CdtSearchCriteria $oCriteria);

	function deleteCdtUserGroupFunctions(CdtUserGroup $oCdtUserGroup);
		
} 
?>
