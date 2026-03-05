<?php 

/** 
 * DAO para CdtRegistration 
 *  
 * @author bernardo
 * @since 24-05-2012
 */ 
interface ICdtRegistrationDAO { 

	/**
	 * se persiste la nueva entity 
	 * @param CdtRegistration $oCdtRegistration entity a persistir.
	 */
	public function addCdtRegistration(CdtRegistration $oCdtRegistration);


	/**
	 * se modifica la entity
	 * @param CdtRegistration $oCdtRegistration entity a modificar.
	 */
	public function updateCdtRegistration(CdtRegistration $oCdtRegistration);

	/**
	 * se elimina la entity
	 * @param CdtRegistration $oCdtRegistration entity a eliminar.
	 */
	public function deleteCdtRegistration(CdtRegistration $oCdtRegistration);

	/**
	 * se obtiene una colecci�n de entities dado el filtro de b�squeda
	 * @param CdtSearchCriteria $oCriteria filtro de b�squeda.
	 * @return ItemCollection[CdtRegistration]
	 */
	public function getCdtRegistrations(CdtSearchCriteria $oCriteria);
	
	/**
	 * se obtiene la cantidad de entities dado el filtro de b�squeda
	 * @param CdtSearchCriteria $oCriteria filtro de b�squeda.
	 * @return int
	 */
	public function getCdtRegistrationsCount(CdtSearchCriteria $oCriteria);
	
	/**
	 * se obtiene un entity dado el filtro de b�squeda
	 * @param CdtSearchCriteria $oCriteria filtro de b�squeda.
	 * @return CdtRegistration
	 */
	public function getCdtRegistration(CdtSearchCriteria $oCriteria);

} 
?>
