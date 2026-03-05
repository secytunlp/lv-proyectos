<?php 

/** 
 * Manager para CdtMenuOption
 *  
 * @author codnet archetype builder
 * @since 09-11-2011
 */ 
class CdtMenuOptionManager implements ICdtList{ 

	/**
	 * dao para CdtMenuOption
	 * @var ICdtMenuOptionDAO
	 */
	private $cdtMenuOptionDAO;
	
	public function __construct(){
		
		$this->setCdtMenuOptionDAO( CdtSecureFactoryDAO::getCdtMenuOptionDAO() );
		
	}
	
	/**
	 * se agrega la nueva entity
	 * @param CdtMenuOption $oCdtMenuOption entity a agregar.
	 */
	public function addCdtMenuOption(CdtMenuOption $oCdtMenuOption) { 
		//TODO validaciones; 

		//persistir en la bbdd.
		$this->getCdtMenuOptionDAO()->addCdtMenuOption($oCdtMenuOption);
	}


	/**
	 * se modifica la entity
	 * @param CdtMenuOption $oCdtMenuOption entity a modificar.
	 */
	public function updateCdtMenuOption(CdtMenuOption $oCdtMenuOption) { 
		//TODO validaciones; 

		//persistir en la bbdd.
		$this->getCdtMenuOptionDAO()->updateCdtMenuOption($oCdtMenuOption);
	}


	/**
	 * se elimina la entity
	 * @param int identificador de la entity a eliminar.
	 */
	public  function deleteCdtMenuOption($ids) { 

		//puede venir m�s de un id.
		$array_ids = explode(",", $ids);
		foreach ($array_ids as $id) {
			$oCdtMenuOption = new CdtMenuOption();
			$oCdtMenuOption->setCd_menuoption($id);
			$this->getCdtMenuOptionDAO()->deleteCdtMenuOption($oCdtMenuOption);
		}
		
	}

	
	/**
	 * se obtiene una colecci�n de entities dado el filtro de b�squeda
	 * @param CdtSearchCriteria $oCriteria filtro de b�squeda.
	 * @return ItemCollection[CdtMenuOption]
	 */
	public function getCdtMenuOptions(CdtSearchCriteria $oCriteria) { 
		return $this->getCdtMenuOptionDAO()->getCdtMenuOptions($oCriteria); 
	}


	/**
	 * se obtiene la cantidad de entities dado el filtro de b�squeda
	 * @param CdtSearchCriteria $oCriteria filtro de b�squeda.
	 * @return int
	 */
	public function getCdtMenuOptionsCount(CdtSearchCriteria $oCriteria) { 
		return $this->getCdtMenuOptionDAO()->getCdtMenuOptionsCount($oCriteria); 
	}


	/**
	 * se obtiene un entity dado el filtro de b�squeda
	 * @param CdtSearchCriteria $oCriteria filtro de b�squeda.
	 * @return CdtMenuOption
	 */
	public function getCdtMenuOption(CdtSearchCriteria $oCriteria) { 
		return $this->getCdtMenuOptionDAO()->getCdtMenuOption($oCriteria); 
	}

	//	interface ICdtList

	
	/**
	 * (non-PHPdoc)
	 * @see ICdtList::getEntities();
	 */
	public function getEntities( CdtSearchCriteria $oCriteria) { 
		return $this->getCdtMenuOptions($oCriteria); 
	}

	
	/**
	 * (non-PHPdoc)
	 * @see ICdtList::getEntitiesCount();
	 */
	public function getEntitiesCount ( CdtSearchCriteria $oCriteria ) { 
		return $this->getCdtMenuOptionsCount($oCriteria); 
	}

	public function getCdtMenuOptionDAO()
	{
	    return $this->cdtMenuOptionDAO;
	}

	public function setCdtMenuOptionDAO($cdtMenuOptionDAO)
	{
	    $this->cdtMenuOptionDAO = $cdtMenuOptionDAO;
	}

} 
?>
