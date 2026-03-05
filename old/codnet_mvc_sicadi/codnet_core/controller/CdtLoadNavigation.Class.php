<?php

/**
 * Carga el mapeo de actions y forwards desde un xml.
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 26-04-2010
 * 
 */
class CdtLoadNavigation{

	//instancia para singleton.
	private static $instancia;
	
	private $defaultActions;
	private $actions;
	private $forwards;
	private $filters;
	
	private function __construct(){
		
		 $this->forwards = array();
		 $this->actions = array();
		 $this->defaultActions = array();
		 $this->filters = array();
	}

	public static function getInstance(){
		
		if (  !self::$instancia instanceof self ) {
			
			//chequeamos si está en cache
			$cache = CdtCache::getInstance();
			$cacheKey = "myCdtLoadNavigation";
			if($cache->contains($cacheKey) )
				self::$instancia = $cache->fetch($cacheKey);
			else{
			
				self::$instancia = new self;
				self::$instancia->load();
			
				$cache->save($cacheKey, self::$instancia );
			}
		}
		
		return self::$instancia;
	}
		
	/**
	 * carga desde un xml las actions y los forwards para el controller.
	 */
	public function load(){
		
		//levantamos el xml de navegaci�n.
		$xml = simplexml_load_file(APP_PATH .  'includes/navigation.xml');
		
		/* se cargan las actions por default. */
		foreach ($xml->default_action as $actionDefault) {
			$newAction = array();
			foreach ($actionDefault->attributes() as $key=>$value) {
				$newAction[$key] = $value . '';	
			}
			$this->defaultActions[] = $newAction;
		}
		
		/* se cargan las actions. */
		foreach ($xml->action as $action) {
			$newAction = array();
			foreach ($action->attributes() as $key=>$value) {
				$newAction[$key] = $value . '';	
			}
			$this->actions[] = $newAction;
		}
		
		/* se cargan los forwards. */
		foreach ($xml->forward as $forward) {
			$newForward = array();
			foreach ($forward->attributes() as $key=>$value) {
				$newForward[$key] = $value . '';	
			}
			$this->forwards[] = $newForward;
		}
		
		/* se cargan los filtros. */
		foreach ($xml->filter as $filter) {
			$newFilter = array();
			foreach ($filter->attributes() as $key=>$value) {
				$newFilter[$key] = $value . '';	
			}
			$this->filters[] = $newFilter;
		}
		
	}
	
	
	public function getDefaultActions(){
		return $this->defaultActions;
	}	
	
	public function getActions(){
		return $this->actions;
	}	
	
	public function getForwards(){
		return $this->forwards;
	}	
	
	public function getFilters(){
		return $this->filters;
	}	
	
	/**
	 * retorna un action dado su nombre
	 * @param unknown_type $name nombre del action
	 * @return array(string,string)
	 */
	public function getActionByName( $name ){

		foreach ($this->getActions() as $action) {
			
			if( $action['name'] == $name )
				return $action;
			 
		}

		//si no est� la buscamos en las actions default.
		foreach ($this->getDefaultActions() as $defaultAction) {
			$entity = $defaultAction['entity'];
			$plural = $defaultAction['plural'];

			//obtenemos las actions implicadas.
			$entityActions = $this->getDefaultActionOfEntity($entity, $plural );
			//vemos si corresponde a alguna de ellas.
			foreach( $entityActions as $action ){
				if($action == $name){
					return $defaultAction;
				}
			}
		}
		
		return null;
	}
	
	/**
	 * retorna las actions por default dada una entity.
	 * @param string $ds_entity
	 * @param string $ds_entityPlural
	 * @return array
	 */
	public function getDefaultActionOfEntity($ds_entity, $ds_entityPlural=null){
		
		if($ds_entityPlural==null)
			$ds_entityPlural = $ds_entity;
			
		$ds_entity_capital_letter = strtoupper( substr($ds_entity,0,1) ) . substr($ds_entity,1);
		$ds_entityPlural_capital_letter = strtoupper( substr($ds_entityPlural,0,1) ) . substr($ds_entityPlural,1);
		
		//add init
		$actions[] = 'add_'.$ds_entity.'_init';
		//add.
		$actions[] = 'add_'.$ds_entity.'';
		//update init.
		$actions[] = 'update_'.$ds_entity.'_init';
		//update.
		$actions[] = 'update_'.$ds_entity.'';
		//list.
		$actions[] = 'list_'.$ds_entityPlural;
		//delete .
		$actions[] = 'delete_'.$ds_entity.'';
		//view.
		$actions[] = 'view_'.$ds_entity.'';		

		return $actions;
	}
	
	
	
}