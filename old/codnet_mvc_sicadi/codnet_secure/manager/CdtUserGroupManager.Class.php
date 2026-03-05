<?php 

/** 
 * Manager para CdtUserGroup
 *  
 * @author codnet archetype builder
 * @since 09-11-2011
 */ 
class CdtUserGroupManager implements ICdtList{ 


	/**
	 * dao para CdtUserGroup
	 * @var ICdtUserGroupDAO
	 */
	private $cdtUserGroupDAO;
	
	/**
	 * dao para CdtUserGroupFunction
	 * @var ICdtUserGroupFunctionDAO
	 */
	private $cdtUserGroupFunctionDAO;
	
	public function __construct(){
		
		$this->setCdtUserGroupDAO( CdtSecureFactoryDAO::getCdtUserGroupDAO() );
		$this->setCdtUserGroupFunctionDAO( CdtSecureFactoryDAO::getCdtUserGroupFunctionDAO() );
	}	
	
	/**
	 * se agrega la nueva entity
	 * @param CdtUserGroup $oCdtUserGroup entity a agregar.
	 */
	public function addCdtUserGroup(CdtUserGroup $oCdtUserGroup) { 
		//TODO validaciones; 

		//persistir en la bbdd.
		$this->getCdtUserGroupDAO()->addCdtUserGroup($oCdtUserGroup);
	}


	/**
	 * se modifica la entity
	 * @param CdtUserGroup $oCdtUserGroup entity a modificar.
	 */
	public function updateCdtUserGroup(CdtUserGroup $oCdtUserGroup) { 
		//TODO validaciones; 

		//persistir en la bbdd.
		$this->getCdtUserGroupDAO()->updateCdtUserGroup($oCdtUserGroup);
	}


	/**
	 * se elimina la entity
	 * @param int identificador de la entity a eliminar.
	 */
	public function deleteCdtUserGroup($id) { 

		//se puede eliminar m�ltiple, as� que $id puede ser una colecci�n de ids.
		$ids = explode( ",", $id);
		
		foreach ($ids as $next) {

			//validaciones para el delete.
			$this->validateOnDelete( $next );
			
			//eliminamos las relaciones con las funciones
			$oCdtUserGroup = new CdtUserGroup();
			$oCdtUserGroup->setCd_usergroup( $next );
			$this->getCdtUserGroupFunctionDAO()->deleteCdtUserGroupFunctions( $oCdtUserGroup );
			
			//eliminamos el usergroup.
			$oCdtUserGroup = new CdtUserGroup();
			$oCdtUserGroup->setCd_usergroup($next);
			$this->getCdtUserGroupDAO()->deleteCdtUserGroup($oCdtUserGroup);
		}
		
		
	}

	private function validateOnDelete( $id ){
		
		//validamos que no tenga users asociados.
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter( "UG.cd_usergroup", $id, "=" );
		$oManager = new CdtUserManager();
		$count = $oManager->getCdtUsersCount( $oCriteria );
		if( $count > 0)
			throw new GenericException( CDT_SECURE_MSG_EXCEPTION_CANNOT_DELETE_ITEMS_RELATED );
					
	}
	
	/**
	 * se obtiene una colecci�n de entities dado el filtro de b�squeda
	 * @param CdtSearchCriteria $oCriteria filtro de b�squeda.
	 * @return ItemCollection[CdtUserGroup]
	 */
	public function getCdtUserGroups(CdtSearchCriteria $oCriteria) { 
		return $this->getCdtUserGroupDAO()->getCdtUserGroups($oCriteria); 
	}


	/**
	 * se obtiene la cantidad de entities dado el filtro de b�squeda
	 * @param CdtSearchCriteria $oCriteria filtro de b�squeda.
	 * @return int
	 */
	public function getCdtUserGroupsCount(CdtSearchCriteria $oCriteria) { 
		return $this->getCdtUserGroupDAO()->getCdtUserGroupsCount($oCriteria); 
	}


	/**
	 * se obtiene un entity dado el filtro de b�squeda
	 * @param CdtSearchCriteria $oCriteria filtro de b�squeda.
	 * @return CdtUserGroup
	 */
	public function getCdtUserGroup(CdtSearchCriteria $oCriteria) { 
		return $this->getCdtUserGroupDAO()->getCdtUserGroup($oCriteria); 
	}

	/**
	 * se obtiene un entity dado el id
	 * @param CdtSearchCriteria $oCriteria filtro de b�squeda.
	 * @return CdtUserGroup
	 */
	public function getCdtUserGroupById( $code ) {
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_usergroup', $code, "=", new CdtCriteriaFormatValue());
		
		return $this->getCdtUserGroupDAO()->getCdtUserGroup($oCriteria); 
	}
	
	//	interface ICdtList

	
	/**
	 * (non-PHPdoc)
	 * @see ICdtList::getEntities();
	 */
	public function getEntities( CdtSearchCriteria $oCriteria) { 
		return $this->getCdtUserGroups($oCriteria); 
	}

	
	/**
	 * (non-PHPdoc)
	 * @see ICdtList::getEntitiesCount();
	 */
	public function getEntitiesCount ( CdtSearchCriteria $oCriteria ) { 
		return $this->getCdtUserGroupsCount($oCriteria); 
	}

	public function getCdtUserGroupDAO()
	{
	    return $this->cdtUserGroupDAO;
	}

	public function setCdtUserGroupDAO($cdtUserGroupDAO)
	{
	    $this->cdtUserGroupDAO = $cdtUserGroupDAO;
	}

	public function getCdtUserGroupFunctionDAO()
	{
	    return $this->cdtUserGroupFunctionDAO;
	}

	public function setCdtUserGroupFunctionDAO($cdtUserGroupFunctionDAO)
	{
	    $this->cdtUserGroupFunctionDAO = $cdtUserGroupFunctionDAO;
	}
} 
?>
