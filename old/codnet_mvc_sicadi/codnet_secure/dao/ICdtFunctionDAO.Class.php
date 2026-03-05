<?php 

/** 
 * DAO para CdtFunction 
 *  
 * @author bernardo
 * @since 24-05-2012
 */ 
interface ICdtFunctionDAO { 

	/**
	 * se persiste la nueva entity
	 * @param CdtFunction $oCdtFunction entity a persistir.
	 */
	public function addCdtFunction(CdtFunction $oCdtFunction);


	/**
	 * se modifica la entity
	 * @param CdtFunction $oCdtFunction entity a modificar.
	 */
	public function updateCdtFunction(CdtFunction $oCdtFunction) ;
	
	/**
	 * se elimina la entity
	 * @param CdtFunction $oCdtFunction entity a eliminar.
	 */
	public function deleteCdtFunction(CdtFunction $oCdtFunction);

	/**
	 * se obtiene una colecci�n de entities dado el filtro de b�squeda
	 * @param CdtSearchCriteria $oCriteria filtro de b�squeda.
	 * @return ItemCollection[CdtFunction]
	 */
	public function getCdtFunctions(CdtSearchCriteria $oCriteria);
	
	/**
	 * se obtiene la cantidad de entities dado el filtro de b�squeda
	 * @param CdtSearchCriteria $oCriteria filtro de b�squeda.
	 * @return int
	 */
	public function getCdtFunctionsCount(CdtSearchCriteria $oCriteria);
	
	/**
	 * se obtiene un entity dado el filtro de b�squeda
	 * @param CdtSearchCriteria $oCriteria filtro de b�squeda.
	 * @return CdtFunction
	 */
	public function getCdtFunction(CdtSearchCriteria $oCriteria);

	/**
	 * funciones asociadas al grupo del usuario.
	 * @param CdtUser $oUser
	 * @return ItemCollecion[CdtFunction]
	 */
	public function getCdtUserFunctions(CdtUser $oUser);

	/**
	 * funciones asociadas al grupo de usuario.
	 * @param CdtUser $oUser
	 * @return ItemCollecion[CdtFunction]
	 */
	public function getCdtUserGroupFunctions(CdtUserGroup $oUserGroup);
} 
?>
