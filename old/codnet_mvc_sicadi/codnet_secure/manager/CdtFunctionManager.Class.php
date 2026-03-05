<?php 

/** 
 * Manager para CdtFunction
 *  
 * @author codnet archetype builder
 * @since 09-11-2011
 */ 
class CdtFunctionManager implements ICdtList{ 

	/**
	 * dao para CdtFunction
	 * @var ICdtFunctionDAO
	 */
	private $cdtFunctionDAO;
	
	public function __construct(){
		
		$this->setCdtFunctionDAO( CdtSecureFactoryDAO::getCdtFunctionDAO() );
		
	}
	
	/**
	 * se agrega la nueva entity
	 * @param CdtFunction $oCdtFunction entity a agregar.
	 */
	public function addCdtFunction(CdtFunction $oCdtFunction) { 
		//TODO validaciones; 

		//persistir en la bbdd.
		$this->getCdtFunctionDAO()->addCdtFunction($oCdtFunction);
	}


	/**
	 * se modifica la entity
	 * @param CdtFunction $oCdtFunction entity a modificar.
	 */
	public function updateCdtFunction(CdtFunction $oCdtFunction) { 
		//TODO validaciones; 

		//persistir en la bbdd.
		$this->getCdtFunctionDAO()->updateCdtFunction($oCdtFunction);
	}


	/**
	 * se elimina la entity
	 * @param int identificador de la entity a eliminar.
	 */
	public function deleteCdtFunction($id) { 
		
		$ids = explode( ",", $id);
		
		foreach ($ids as $next) {
			
			//validaciones; 
			$this->validateOnDelete( $next );
			
			$oCdtFunction = new CdtFunction();
			$oCdtFunction->setCd_function($next);
			$this->getCdtFunctionDAO()->deleteCdtFunction($oCdtFunction);	
		}
		
		
	}

	private function validateOnDelete( $id ){
		
		//validamos que no tenga usergroups asociados.
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter( "F.cd_function", $id, "=" );
		$oManager = new CdtUserGroupFunctionManager();
		$count = $oManager->getCdtUserGroupFunctionsCount( $oCriteria );
		if( $count > 0)
			throw new GenericException( CDT_SECURE_MSG_EXCEPTION_CANNOT_DELETE_ITEMS_RELATED );
					
		//validamos que no tenga actions asociadas.
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter( "F.cd_function", $id, "=" );
		$oManager = new CdtActionFunctionManager();
		$count = $oManager->getCdtActionFunctionsCount( $oCriteria );
		if( $count > 0)
			throw new GenericException( CDT_SECURE_MSG_EXCEPTION_CANNOT_DELETE_ITEMS_RELATED );
		
		//validamos que no tenga menuoptions asociados.
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter( "F.cd_function", $id, "=" );
		$oManager = new CdtMenuOptionManager();
		$count = $oManager->getEntitiesCount( $oCriteria );
		if( $count > 0)
			throw new GenericException( CDT_SECURE_MSG_EXCEPTION_CANNOT_DELETE_ITEMS_RELATED );
						
	}
		
	/**
	 * se obtiene una colecci�n de entities dado el filtro de b�squeda
	 * @param CdtSearchCriteria $oCriteria filtro de b�squeda.
	 * @return ItemCollection[CdtFunction]
	 */
	public function getCdtFunctions(CdtSearchCriteria $oCriteria) { 
		return $this->getCdtFunctionDAO()->getCdtFunctions($oCriteria); 
	}


	/**
	 * se obtiene la cantidad de entities dado el filtro de b�squeda
	 * @param CdtSearchCriteria $oCriteria filtro de b�squeda.
	 * @return int
	 */
	public function getCdtFunctionsCount(CdtSearchCriteria $oCriteria) { 
		return $this->getCdtFunctionDAO()->getCdtFunctionsCount($oCriteria); 
	}


	/**
	 * se obtiene un entity dado el filtro de b�squeda
	 * @param CdtSearchCriteria $oCriteria filtro de b�squeda.
	 * @return CdtFunction
	 */
	public function getCdtFunction(CdtSearchCriteria $oCriteria) { 
		return $this->getCdtFunctionDAO()->getCdtFunction($oCriteria); 
	}

	//	interface ICdtList

	
	/**
	 * (non-PHPdoc)
	 * @see ICdtList::getEntities();
	 */
	public function getEntities( CdtSearchCriteria $oCriteria) { 
		return $this->getCdtFunctions($oCriteria); 
	}

	
	/**
	 * (non-PHPdoc)
	 * @see ICdtList::getEntitiesCount();
	 */
	public function getEntitiesCount ( CdtSearchCriteria $oCriteria ) { 
		return $this->getCdtFunctionsCount($oCriteria); 
	}

	public function getCdtUserGroupFunctions(CdtUserGroup $oUserGroup) {
		return $this->getCdtFunctionDAO()->getCdtUserGroupFunctions( $oUserGroup );
	}
	
	public function getCdtFunctionDAO()
	{
	    return $this->cdtFunctionDAO;
	}

	public function setCdtFunctionDAO($cdtFunctionDAO)
	{
	    $this->cdtFunctionDAO = $cdtFunctionDAO;
	}	
} 
?>
