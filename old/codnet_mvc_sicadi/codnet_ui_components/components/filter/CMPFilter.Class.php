<?php

/**
 * componente filter.
 * 
 * Cada filter tendrá las properties por las cuales
 * se desea filtrar. Por otro lado, tendrá un form para "llenar" dichas properties.
 * Luego utilizará las properties para generar el criterio de búsqueda.
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 22-03-2013
 *
 */
abstract class CMPFilter extends CMPComponent{

	/**
	 * id del filter
	 * @var string
	 */
	private $id;
	
	/**
	 * formulario para construir el filtro
	 * @var CMPForm
	 */
	private $form;

	/**
	 * search
	 * @var string
	 */
	private $search;
	
	/**
	 * action a ejecutar para el form.
	 * @var string
	 */
	private $action;

	/**
	 * orderBy
	 * @var string
	 */
	private $orderBy;
	
	/**
	 * orderType
	 * @var string
	 */
	private $orderType;
	
	/**
	 * row per page
	 * @var string
	 */
	private $rowPerPage;

	/**
	 * page para paginación.
	 * @var unknown_type
	 */
	private $page;
	
	/**
	 * component para el grid
	 * @var string
	 */
	private $component;

	/**
	 * id de la grilla
	 * @var string
	 */
	private $gridId;
	

	/**
	 * fCallback para los filtros de los popups.
	 * @var string
	 */
	private $fCallback;
		
	private $name;
	private $cd_user;
	
	public function __construct( $id="filter", $legend=CDT_CMP_FILTER_CRITERIA){

		$this->setId($id);
		
		$form = new CMPForm($id, CDT_UI_LBL_SEARCH );
		
		
		$fieldset = new FormFieldset( $legend );
		
		$form->addFieldset($fieldset);
		$form->addHidden( FieldBuilder::buildInputHidden ( "search","") );
		$form->addHidden( FieldBuilder::buildInputHidden ( "action","" ) );
		$form->addHidden( FieldBuilder::buildInputHidden ( "component","" ) );
		$form->addHidden( FieldBuilder::buildInputHidden ( "orderBy","" ) );
		$form->addHidden( FieldBuilder::buildInputHidden ( "orderType","" ) );
		$form->addHidden( FieldBuilder::buildInputHidden ( "page","" ) );
		$form->addHidden( FieldBuilder::buildInputHidden ( "rowPerPage",ROW_PER_PAGE ) );
		$form->addHidden( FieldBuilder::buildInputHidden ( "gridId","" ) );
		$form->addHidden( FieldBuilder::buildInputHidden ( "fCallback","" ) );
		
		//properties del form.
    	$form->addProperty("method", "POST");
    	
		$form->setAction("doAction?action=cmp_entitygrid");
		//$form->setOnCancel("clearForm( $('#$id') ); $('#$id').submit()");
		//$form->setCancelLabel(CDT_UI_LBL_SEARCH_ALL);
		
		
    	//$form->setAction("cmp_entitygrid");
    	//$form->addButton( CDT_UI_LBL_SEARCH_ALL, "clearForm( $('#$id') ); search_all_ajax( '$id', 'doAction?action=cmp_entitygrid' );" );
		$form->addButton( CDT_UI_LBL_SEARCH_ALL, "clearForm($id ); search_all_ajax( '$id', 'doAction?action=cmp_entitygrid' );" );
    	//$form->addButton( CDT_UI_LBL_SEARCH_ALL, "clearForm( $('#$id') ); $('#$id').submit();" );
		$form->addButton( CDT_UI_LBL_SEARCH, "resetFilterPage_$id();search_ajax( '$id', 'doAction?action=cmp_entitygrid' );" );
		//$form->addButton( CDT_UI_LBL_SEARCH, "resetFilterPage_$id();$('#$id').submit();" );
    	$form->setSubmitLabel( "");
    	$form->setCancelLabel( "");
		
		//$form->setOnCancel("alert(document.getElementById('formfilter_$id'))");
		$form->setUseAjaxSubmit( true );
		$form->setOnSuccessCallback("showResults");
		
		//$form->setBeforeSubmit("resetFilterPage_$id");
		
		//$form->addButton(CDT_UI_LBL_SEARCH, "resetFilterPage_$id();");
		
		$this->setSearch(1);
		$this->setPage(1);
		$this->setRowPerPage( ROW_PER_PAGE );
		$this->setAction("cmp_entitygrid");
		
		
		$this->setForm($form);
		$this->setName("filter");
		
		$this->setCd_user(CdtSecureUtils::getUserLogged()->getCd_user());
		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see components/CMPComponent::show()
	 */
	public function show( ){
		
		//rellenamos los valores del formulario.
		$this->fillForm();
		
		//mostramos el formulario.
		return $this->getForm()->show();
	}
	
	/**
	 * se construye el criterio de búsqueda a partir
	 * del formulario.
	 * @return CdtSearchCriteria.
	 */
	public function buildCriteria(){
		
		//construimos el criteria con las properties obtenidas.
		$criteria = new CdtSearchCriteria();
		$this->fillCriteria( $criteria );
		
		return $criteria;
	}
	
	/**
	 * se forma el criteria con las properties definidas por
	 * cada filter.
	 * @param $criteria
	 */
	protected function fillCriteria( $criteria ){
	
		$orderBy = ($this->getOrderBy())?$this->getOrderBy():"oid";
		$orderType = ($this->getOrderType())?$this->getOrderType():"DESC";
		//pueden venir varios en el orderField.
		$orderFields = explode("," , $orderBy );
		for( $index=0; $index<count($orderFields); $index++) {
			$criteria->addOrder($orderFields[$index], $orderType);
		}

		$page = ($this->getPage())?$this->getPage():1;
		$criteria->setPage($page);

		$rowPerPage = ($this->getRowPerPage())?$this->getRowPerPage():ROW_PER_PAGE;
		$criteria->setRowPerPage( $rowPerPage);
	}


	protected function fillForm(){
		$this->getForm()->fillInputValues($this);
	}
	
	public function fillProperties(){
		
		$this->getForm()->fillEntityValues($this);
		
		$this->saveProperties();
		
	}
	
	public function fillSavedProperties(){
		
		//$this->getForm()->fillEntitySessionValues($this);
		
		/*
		$this->setPage( $this->getSavedProperty("page") );
		 		
		$this->setRowPerPage( $this->getSavedProperty("rowPerPage") );
		
		$this->setAction( $this->getSavedProperty("action") );
		*/
		
		$this->getForm()->fillEntitySavedFields( $this );
		
		$this->setPage(1);
	}
	
	public function saveProperties(){
		//$this->getForm()->fillEntitySessionValues($this);
		//$this->saveProperty("page", $this->getPage());
		//$this->saveProperty("rowPerPage", $this->getRowPerPage());
		//$this->saveProperty("action", $this->getAction());
		
		$this->getForm()->saveFields( $this );
		
		
		$this->persistMyFilter();
	}
	
	public function saveProperty($name, $value){
		$_SESSION[$this->getId() . "_$name" ] = $value;
		CdtUtils::log("saveProperty($name, $value)", __CLASS__, LoggerLevel::getLevelDebug());
	}
	
	public function getSavedProperty($name){
		
		$value = (isset($_SESSION[$this->getId() . "_$name" ] ))?$_SESSION[$this->getId() . "_$name" ]:null;
		CdtUtils::log("getSavedProperty($name) = $value", __CLASS__, LoggerLevel::getLevelDebug());
		return $value;
	}
	
	protected function addField(FormField $field, $column=1){
		
		$fieldsets = $this->getForm()->getFieldsets();
		$fieldset = $fieldsets[0];
		$fieldset->addField( $field, $column );
	
	}
	
	public function getId()
	{
	    return $this->id;
	}

	public function setId($id)
	{
	    $this->id = $id;
	}

	public function getForm()
	{
	    return $this->form;
	}

	public function setForm($form)
	{
	    $this->form = $form;
	}

	public function getSearch()
	{
	    return $this->search;
	}

	public function setSearch($search)
	{
	    $this->search = $search;
	}

	public function getAction()
	{
	    return $this->action;
	}

	public function setAction($action)
	{
	    $this->action = $action;
	}

	public function getComponent()
	{
	    return $this->component;
	}

	public function setComponent($component)
	{
	    $this->component = $component;
	}

	public function getOrderBy()
	{
	    return $this->orderBy;
	}

	public function setOrderBy($orderBy)
	{
	    $this->orderBy = $orderBy;
	}

	public function getOrderType()
	{
	    return $this->orderType;
	}

	public function setOrderType($orderType)
	{
	    $this->orderType = $orderType;
	}

	public function getRowPerPage()
	{
	    return $this->rowPerPage;
	}

	public function setRowPerPage($rowPerPage)
	{
	    $this->rowPerPage = $rowPerPage;
	}

	public function getPage()
	{
	    return $this->page;
	}

	public function setPage($page)
	{
	    $this->page = $page;
	}
	
	
	public function fillPersistedValues( $value ){
	
	}
	
	public function getFieldsValuesToPersist(){

		//obtenemos los valores de los inputs a guardar.
		$fieldsValues = $this->getForm()->getFieldsValues( $this );
		
		$strFilters = array();
		foreach ($fieldsValues as $key => $value) {
			
			$res = $value;
			if( is_array($value) )
				$res = implode(",", $value) ;
			else{
				$res = "'$value'";
			}
			$strFilters[] =  "$key=$res" ;
		}
		$strFilters = implode(",", $strFilters);
		
		return $strFilters;
	}

	public function persistMyFilter(){

		//$strFilters = $this->getFieldsValuesToPersist();
		
		//CdtUtils::log(" persistiendo filtro " . $this->getId() , __CLASS__, LoggerLevel::getLevelDebug());
		//CdtUtils::log(" user " . CdtSecureUtils::getUserLogged()->getCd_user() , __CLASS__, LoggerLevel::getLevelDebug());
		//CdtUtils::log(" filters " . $strFilters , __CLASS__, LoggerLevel::getLevelDebug());
		//$dao = new CMPFilterDAO();
		//$dao->addEntity($this);
		
	}

	public function getName()
	{
	    return $this->name;
	}

	public function setName($name)
	{
	    $this->name = $name;
	}

	public function getCd_user()
	{
	    return $this->cd_user;
	}

	public function setCd_user($cd_user)
	{
	    $this->cd_user = $cd_user;
	}

	public function getGridId()
	{
	    return $this->gridId;
	}

	

	public function setGridId($gridId)
	{
	    $this->gridId = $gridId;
	}

	public function getFCallback()
	{
	    return $this->fCallback;
	}

	public function setFCallback($fCallback)
	{
	    $this->fCallback = $fCallback;
	}
}