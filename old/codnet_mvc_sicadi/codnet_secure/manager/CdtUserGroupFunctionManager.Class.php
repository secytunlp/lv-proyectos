<?php 

/** 
 * Manager para CdtUserGroupFunction
 *  
 * @author codnet archetype builder
 * @since 09-11-2011
 */ 
class CdtUserGroupFunctionManager implements ICdtList{ 

	/**
	 * dao para CdtUserGroupFunction
	 * @var ICdtUserGroupFunctionDAO
	 */
	private $cdtUserGroupFunctionDAO;
	
		
	public function __construct(){
		
		$this->setCdtUserGroupFunctionDAO( CdtSecureFactoryDAO::getCdtUserGroupFunctionDAO() );
	}	
	
	/**
	 * se agrega la nueva entity
	 * @param CdtUserGroupFunction $oCdtUserGroupFunction entity a agregar.
	 */
	public function addCdtUserGroupFunction(CdtUserGroupFunction $oCdtUserGroupFunction) { 
		//TODO validaciones; 

		//persistir en la bbdd.
		$this->getCdtUserGroupFunctionDAO()->addCdtUserGroupFunction($oCdtUserGroupFunction);
	}


	/**
	 * se modifica la entity
	 * @param CdtUserGroupFunction $oCdtUserGroupFunction entity a modificar.
	 */
	public function updateCdtUserGroupFunction(CdtUserGroupFunction $oCdtUserGroupFunction) { 
		//TODO validaciones; 

		//persistir en la bbdd.
		$this->getCdtUserGroupFunctionDAO()->updateCdtUserGroupFunction($oCdtUserGroupFunction);
	}


	/**
	 * se elimina la entity
	 * @param int identificador de la entity a eliminar.
	 */
	public function deleteCdtUserGroupFunction($id) { 
	
		//se puede eliminar m�ltiple, as� que $id puede ser una colecci�n de ids.
		$ids = explode( ",", $id);
		
		foreach ($ids as $next) {

			$oCdtUserGroupFunction = new CdtUserGroupFunction();
			$oCdtUserGroupFunction->setCd_usergroup_function($next);
			$this->getCdtUserGroupFunctionDAO()->deleteCdtUserGroupFunction($oCdtUserGroupFunction);
		} 

		
	}

	
	/**
	 * se obtiene una colecci�n de entities dado el filtro de b�squeda
	 * @param CdtSearchCriteria $oCriteria filtro de b�squeda.
	 * @return ItemCollection[CdtUserGroupFunction]
	 */
	public function getCdtUserGroupFunctions(CdtSearchCriteria $oCriteria) { 
		return $this->getCdtUserGroupFunctionDAO()->getCdtUserGroupFunctions($oCriteria); 
	}


	/**
	 * se obtiene la cantidad de entities dado el filtro de b�squeda
	 * @param CdtSearchCriteria $oCriteria filtro de b�squeda.
	 * @return int
	 */
	public function getCdtUserGroupFunctionsCount(CdtSearchCriteria $oCriteria) { 
		return $this->getCdtUserGroupFunctionDAO()->getCdtUserGroupFunctionsCount($oCriteria); 
	}


	/**
	 * se obtiene un entity dado el filtro de b�squeda
	 * @param CdtSearchCriteria $oCriteria filtro de b�squeda.
	 * @return CdtUserGroupFunction
	 */
	public function getCdtUserGroupFunction(CdtSearchCriteria $oCriteria) { 
		return $this->getCdtUserGroupFunctionDAO()->getCdtUserGroupFunction($oCriteria); 
	}

	//	interface ICdtList

	
	/**
	 * (non-PHPdoc)
	 * @see ICdtList::getEntities();
	 */
	public function getEntities( CdtSearchCriteria $oCriteria) { 
		return $this->getCdtUserGroupFunctions($oCriteria); 
	}

	
	/**
	 * (non-PHPdoc)
	 * @see ICdtList::getEntitiesCount();
	 */
	public function getEntitiesCount ( CdtSearchCriteria $oCriteria ) { 
		return $this->getCdtUserGroupFunctionsCount($oCriteria); 
	}


	public function assignCdtUserGroupFunctions(CdtUserGroup $oUserGroup, $functions ){
		
		//eliminamos las funciones que ten�a asignadas.
		$this->getCdtUserGroupFunctionDAO()->deleteCdtUserGroupFunctions( $oUserGroup );
		
		//agregamos las nuevas.
		if( !empty($functions) ){
			foreach ($functions as $id) {
				
				$oCdtUserGroupFunction = new CdtUserGroupFunction();
				$oCdtUserGroupFunction->setCdtUserGroup( $oUserGroup );
				$oCdtUserGroupFunction->setCd_function( $id );
				
				$this->getCdtUserGroupFunctionDAO()->addCdtUserGroupFunction( $oCdtUserGroupFunction );
			}
		}
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
