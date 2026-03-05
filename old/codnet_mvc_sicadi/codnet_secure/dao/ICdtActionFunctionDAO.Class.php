<?php 

/** 
 * DAO para CdtActionFunction 
 *  
 * @author bernardo
 * @since 24-05-2012
 */ 
interface ICdtActionFunctionDAO { 

	/**
	 * se persiste la nueva entity
	 * @param CdtActionFunction $oCdtActionFunction entity a persistir.
	 */
	function addCdtActionFunction(CdtActionFunction $oCdtActionFunction) ;


	/**
	 * se modifica la entity
	 * @param CdtActionFunction $oCdtActionFunction entity a modificar.
	 */
	function updateCdtActionFunction(CdtActionFunction $oCdtActionFunction);

	/**
	 * se elimina la entity
	 * @param CdtActionFunction $oCdtActionFunction entity a eliminar.
	 */
	function deleteCdtActionFunction(CdtActionFunction $oCdtActionFunction) ;

	/**
	 * se obtiene una colecci�n de entities dado el filtro de b�squeda
	 * @param CdtSearchCriteria $oCriteria filtro de b�squeda.
	 * @return ItemCollection[CdtActionFunction]
	 */
	function getCdtActionFunctions(CdtSearchCriteria $oCriteria);

	
	/**
	 * se obtiene la cantidad de entities dado el filtro de b�squeda
	 * @param CdtSearchCriteria $oCriteria filtro de b�squeda.
	 * @return int
	 */
	function getCdtActionFunctionsCount(CdtSearchCriteria $oCriteria);


	/**
	 * se obtiene un entity dado el filtro de b�squeda
	 * @param CdtSearchCriteria $oCriteria filtro de b�squeda.
	 * @return CdtActionFunction
	 */
	function getCdtActionFunction(CdtSearchCriteria $oCriteria);
	
} 
?>
