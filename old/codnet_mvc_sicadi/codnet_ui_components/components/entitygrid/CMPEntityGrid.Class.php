<?php

/**
 * componente grilla para entities.
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 22-03-2013
 *
 */
class CMPEntityGrid extends CMPComponent{

	/**
	 * identificador de la grilla.
	 * @var string
	 */
	private $id;
	
	/**
	 * filtro para formar el criteria.
	 * @var CMPFilter
	 */
	private $filter;
	
	/**
	 * layout de la grilla.
	 * @var CdtLayout
	 */
	private $layout;
	
	/**
	 * renderer para dibujar la grilla.
	 * @var IEntityGridRenderer
	 */
	private $renderer;
	
	/**
	 * modelo de la grilla
	 * @var IGridModel
	 */
	private $model;
	
	
	public function __construct(){
		
		$this->setRenderer( new EntityGridRenderer() );	
		
		//vemos si existe en el contexto, sino lo definimos por la clase.
		$gridId =  CdtUtils::getParam("gridId", get_class($this) );
		//$gridId =  get_class($this) ;
		$this->setId( $gridId );
	}

	/**
	 * (non-PHPdoc)
	 * @see components/CMPComponent::show()
	 */	
	public function show( ){
		
		//renderizamos la grilla o sólo los resultados.
		$this->getFilter()->setGridId( $this->getId() );
		
		if( CdtUtils::getParam("search", CdtUtils::getParamPOST("search")) ){
			
			
			$this->getFilter()->fillProperties();
			$this->findEntities();
			$grid = $this->getRenderer()->renderResults( $this );
			
		}else{
			$this->getFilter()->fillSavedProperties();
			//$this->getFilter()->fillProperties();			
			$this->findEntities();
			$grid = $this->getRenderer()->render( $this );			
		} 
			
		
		//return "SEARCH=" . CdtUtils::getParam("search", CdtUtils::getParamPOST("search"));
		return $grid;
			
	}
	
	/**
	 * se renderiza sólo el listado de entities.
	 */
	public function showResults( ){
		
		$this->findEntities();
		
		//renderizamos sólo los resultados.
		return $this->getRenderer()->renderResults( $this );
	}

	/**
	 * se buscan las entities y se setean en el model
	 * de la grilla.
	 */
	protected function findEntities(){
		
		//CdtUtils::log("findEntities", __CLASS__, LoggerLevel::getLevelDebug());
		
		//obtenemos el listado de entities a visualizar.
		$manager = $this->getModel()->getEntityManager();
		
		$criteria = $this->getFilter()->buildCriteria();
		
		$entities = $manager->getEntities( $criteria );
		
		//se las seteamos al modelo.
		$this->getModel()->setEntities( $entities );
		
		$totalRows = $manager->getEntitiesCount (  $criteria );
		$this->getModel()->setTotalRows( $totalRows );
		
	}
	

	public function getId()
	{
	    return $this->id;
	}

	public function setId($id)
	{
	    $this->id = $id;
	}

	public function getFilter()
	{
	    return $this->filter;
	}

	public function setFilter($filter)
	{
	    $this->filter = $filter;
	}

	public function getLayout()
	{
	    return $this->layout;
	}

	public function setLayout($layout)
	{
	    $this->layout = $layout;
	}

	public function getRenderer()
	{
	    return $this->renderer;
	}

	public function setRenderer($renderer)
	{
	    $this->renderer = $renderer;
	}

	public function getModel()
	{
	    return $this->model;
	}

	public function setModel($model)
	{
	    $this->model = $model;
	}

	public function getEntities()
	{
	    return $this->entities;
	}

	public function setEntities($entities)
	{
	    $this->entities = $entities;
	}
}