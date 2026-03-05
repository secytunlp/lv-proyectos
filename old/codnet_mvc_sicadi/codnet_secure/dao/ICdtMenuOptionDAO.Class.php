<?php 

/** 
 * DAO para CdtMenuOption 
 *  
 * @author bernardo
 * @since 24-05-2012
 */ 
interface ICdtMenuOptionDAO { 

	
	/**
	 * se persiste la nueva entity
	 * @param CdtMenuOption $oCdtMenuOption entity a persistir.
	 */
	public function addCdtMenuOption(CdtMenuOption $oCdtMenuOption);


	/**
	 * se modifica la entity
	 * @param CdtMenuOption $oCdtMenuOption entity a modificar.
	 */
	public function updateCdtMenuOption(CdtMenuOption $oCdtMenuOption);

	/**
	 * se elimina la entity
	 * @param CdtMenuOption $oCdtMenuOption entity a eliminar.
	 */
	public function deleteCdtMenuOption(CdtMenuOption $oCdtMenuOption);

	/**
	 * se obtiene una colecci�n de entities dado el filtro de b�squeda
	 * @param CdtSearchCriteria $oCriteria filtro de b�squeda.
	 * @return ItemCollection[CdtMenuOption]
	 */
	public function getCdtMenuOptions(CdtSearchCriteria $oCriteria);
	
	/**
	 * se obtiene la cantidad de entities dado el filtro de b�squeda
	 * @param CdtSearchCriteria $oCriteria filtro de b�squeda.
	 * @return int
	 */
	public function getCdtMenuOptionsCount(CdtSearchCriteria $oCriteria);


	/**
	 * se obtiene un entity dado el filtro de b�squeda
	 * @param CdtSearchCriteria $oCriteria filtro de b�squeda.
	 * @return CdtMenuOption
	 */
	public function getCdtMenuOption(CdtSearchCriteria $oCriteria);

} 
?>
