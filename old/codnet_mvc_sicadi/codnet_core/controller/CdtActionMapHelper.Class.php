<?php

/**
 * Contiene el mapeo de las acciones.
 * Utilizada por el CdtActionController para:
 *  1- buscar las acciones a ejecutar.
 *  2- realizar los forwards.
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 02-03-2010
 * 
 */
class CdtActionMapHelper{
	
	//array con el mapeo de las acciones. 
	private $actionMap =  array(); //( [nombre_accion] = clase_accion )
	
	//array con el mapeo de los forwards.
	private $forwardMap =  array(); // ( [nombre] = url )
	
	//array con el mapeo de los filters.
	private $filterMap =  array(); // ( [nombre_filter] = clase_filter )
	
	//Método constructor
	
	public function __construct(){

		$navegation = CdtLoadNavigation::getInstance();

		//inicializamos las acciones por default.
		foreach ($navegation->getDefaultActions() as $defaulAction) {
			$this->initDefaultActions($defaulAction['entity'], $defaulAction['plural']);
		}
		
		//inicializamos las acciones.
		foreach ($navegation->getActions() as $action) {
			$this->setAction($action['name'], $action['class']);
		}
		
		//inicializamos los forwards.
		foreach ($navegation->getForwards() as $forward) {
			$this->setForward($forward['name'], $forward['url']);
		}

		//inicializamos los filtros.
		foreach ($navegation->getFilters() as $filter) {
			$this->setFilter($filter['name'], $filter);
		}
	}

	
	
	/**
	 * retorna la clase asociada al action.
	 * @param string $ds_actionName nombre del action
	 * @return string clase
	 */
	function getAction($ds_actionName) {
		
		if (isset($this->actionMap[$ds_actionName])) {
			//CdtUtils::log('Mapeada: '.$ds_actionName);
			return $this->actionMap[$ds_actionName];
		} else {
			CdtUtils::log('no Mapeada: '.$ds_actionName);
			// Manejar el caso en el que la clave no existe, por ejemplo, retornando un valor predeterminado o lanzando una excepción.
			// Ejemplo de retorno de un valor predeterminado:
			return 'CdtAccessDeniedAction';
			
			// Ejemplo de lanzamiento de excepción:
			// throw new Exception('La acción no existe');
		}
	}
	
	/**
	 * retorna un forward dado su key.
	 * @param string $ds_key key del forward
	 * @return array(string, string)
	 */
	function getForward($ds_key) {
		if( array_key_exists($ds_key, $this->forwardMap ) )
			return $this->forwardMap[$ds_key];
		else '';	
	}
	
	/**
	 * retorna el fitlro dado su key
	 * @param string $ds_key key del filtro.
	 * @return array(string. string)
	 */
	function getFilter($ds_key) {
		return $this->filterMap[$ds_key];
	}
	
	/**
	 * retorna la clase asociada a un filtro
	 * @param $ds_key key de un filtro.
	 * @return string clase
	 */
	function getFilterClase($ds_key) {
		return $this->filterMap[$ds_key]['class'];
	}

	/**
	 * retorna el urlPattern asociado a un filtro
	 * @param $ds_key key de un filtro.
	 * @return string
	 */
	function getFilterUrlPattern($ds_key) {
		return $this->filterMap[$ds_key]['urlPattern'];
	}
	
	/**
	 * retorna la colección de filtros
	 * @return array( array(string, string) )
	 */
	function getFilters() {
		return $this->filterMap;
	}

	/**
	 * retorna la colección de actions
	 * @return array( array(string, string) )
	 */
	function getActions() {
		return $this->actionMap;
	}	
	
	
	//Métodos Set

	/**
	 * agrega un nuevo action
	 * @param string $ds_actionName nombre del action
	 * @param string $ds_actionClass clase asociaada al action
	 */
	function setAction($ds_actionName,$ds_actionClass){
		$this->actionMap[$ds_actionName]=$ds_actionClass;		
	}	
	
	/**
	 * agrega un nuevo forward
	 * @param string $ds_forward nombre del forward
	 * @param string $ds_url url del forward
	 */
	function setForward($ds_forward,$ds_url){
		$this->forwardMap[$ds_forward]=$ds_url;
	}

	/**
	 * agregar un nuevo filter
	 * @param string $ds_filterName nombre del filter
	 * @param array(string,string) $filter (nombre, clase)
	 */
	function setFilter($ds_filterName,$filter){
		$this->filterMap[$ds_filterName]=$filter;
	}
	
	//Funciones
		
	
	/**
	 * setea las actions default List/Add/Update/Delete/View para una entity.
	 * 
	 * @param string $ds_entity nombre de la entidad.
	 * @param string $ds_entityPlural nombre de la entidad en plural
	 */
	protected function initDefaultActions($ds_entity, $ds_entityPlural=null){
		if($ds_entityPlural==null)
			$ds_entityPlural = $ds_entity;
			
		$ds_entity_capital_letter = strtoupper( substr($ds_entity,0,1) ) . substr($ds_entity,1);
		$ds_entityPlural_capital_letter = strtoupper( substr($ds_entityPlural,0,1) ) . substr($ds_entityPlural,1);
		
		//add init
		$this->setAction('add_'.$ds_entity.'_init', 'Add'.$ds_entity_capital_letter.'InitAction');
				
		//add.
		$this->setAction('add_'.$ds_entity.'', 'Add'.$ds_entity_capital_letter.'Action');
		$this->setForward('add_'.$ds_entity.'_error','doAction?action=add_'.$ds_entity.'_init');
		$this->setForward('add_'.$ds_entity.'_success','doAction?action=list_'.$ds_entityPlural);
		
		//update init.
		$this->setAction('update_'.$ds_entity.'_init', 'Update'.$ds_entity_capital_letter.'InitAction');
		
		//update.
		$this->setAction('update_'.$ds_entity.'', 'Update'.$ds_entity_capital_letter.'Action');
		$this->setForward('update_'.$ds_entity.'_error','doAction?action=update_'.$ds_entity.'_init');
		$this->setForward('update_'.$ds_entity.'_success','doAction?action=list_'.$ds_entityPlural);
		
		//lis.
		$this->setAction('list_'.$ds_entityPlural, 'List'.$ds_entityPlural_capital_letter.'Action');
		$this->setForward('list_'.$ds_entityPlural.'_error','doAction?action=list_'.$ds_entityPlural);
		
		//delete .
		$this->setAction('delete_'.$ds_entity.'', 'Delete'.$ds_entity_capital_letter.'Action');
		$this->setForward('delete_'.$ds_entity.'_error','doAction?action=list_'.$ds_entityPlural);
		$this->setForward('delete_'.$ds_entity.'_success','doAction?action=list_'.$ds_entityPlural);
		
		//ver.
		$this->setAction('view_'.$ds_entity.'', 'View'.$ds_entity_capital_letter.'Action');		
		
	}
	
}