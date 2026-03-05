<?php 

/** 
 * DAO para CdtUser 
 *  
 * @author bernardo
 * @since 24-05-2012
 */ 
interface ICdtUserDAO { 

	/**
	 * se persiste la nueva entity
	 * @param CdtUser $oCdtUser entity a persistir.
	 */
	public function addCdtUser(CdtUser $oCdtUser);


	
	/**
	 * se modifica la entity
	 * @param CdtUser $oCdtUser entity a modificar.
	 */
	public function updateCdtUser(CdtUser $oCdtUser);
	
	
	/**
	 * se modifica la entity
	 * @param CdtUser $oCdtUser entity a modificar.
	 */
	public function updateAttemps(CdtUser $oCdtUser);
	
	/**
	 * se modifica la password del usuario entity
	 * @param CdtUser $oCdtUser entity a modificar.
	 */
	public function updatePassword(CdtUser $oCdtUser);

	/**
	 * se elimina la entity
	 * @param CdtUser $oCdtUser entity a eliminar.
	 */
	public function deleteCdtUser(CdtUser $oCdtUser);

	/**
	 * se obtiene una colecci�n de entities dado el filtro de b�squeda
	 * @param CdtSearchCriteria $oCriteria filtro de b�squeda.
	 * @return ItemCollection[CdtUser]
	 */
	public function getCdtUsers(CdtSearchCriteria $oCriteria);

	
	/**
	 * se obtiene la cantidad de entities dado el filtro de b�squeda
	 * @param CdtSearchCriteria $oCriteria filtro de b�squeda.
	 * @return int
	 */
	public function getCdtUsersCount(CdtSearchCriteria $oCriteria);


	/**
	 * se obtiene un entity dado el filtro de b�squeda
	 * @param CdtSearchCriteria $oCriteria filtro de b�squeda.
	 * @return CdtUser
	 */
	public function getCdtUser(CdtSearchCriteria $oCriteria);

	/**
	 * se obtiene un user con su grupo dado el filtro de b�squeda
	 * @param CdtSearchCriteria $oCriteria filtro de b�squeda.
	 * @return CdtUser
	 */
	public function getCdtUserWithUserGroup( CdtSearchCriteria $oCriteria );

	
	public function existUsername( CdtUser $oUser );
	
	
	public function existEmail( CdtUser $oUser );
	
} 
?>
