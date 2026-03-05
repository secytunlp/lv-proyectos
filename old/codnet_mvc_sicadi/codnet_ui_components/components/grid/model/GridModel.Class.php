<?php

/**
 * Modelo para la grilla
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 14-12-2011
 *
 */
abstract class GridModel implements IGridModel{

	private $entities;
	private $totalRows;
	private $columnsModel;
	private $filtersModel;
	private $actionsModel;
	private $rowActionsModel;
	private $filterExpressionTemplate;

	public function __construct(){
		$this->entities = new ItemCollection();
		$this->columnsModel = new ItemCollection();
		$this->filtersModel = new ItemCollection();
		$this->actionsModel = new ItemCollection();
		$this->rowActionsModel = new ItemCollection();
		$this->filterExpressionTemplate = "";

	}

	public function setEntities( ItemCollection $items){
		$this->entities = $items;
	}

	public function setColumnsModel( $model ){
		$this->columnsModel = $model;
	}

	public function getColumnCount( ){
		return $this->columnsModel->size();
	}

	public function addColumn( GridColumnModel $column ){
		$this->columnsModel->addItem( $column );
	}

	public function getColumnModel( $columnIndex ){

		return $this->columnsModel->getObjectByIndex( $columnIndex );
	}
	
	public function getColumnModelByName( $name ){

		foreach ($this->columnsModel as $oColumnModel) {
				
			if( $oColumnModel->getDs_name() == $name )
			return $oColumnModel;
		}
	}

	/**
	 * le setea un group a un conjunto de columnas
	 * @param string $ds_group nombre del group
	 * @param array $columnNames arreglo con los nombres de las columnas
	 */
	public function setGroupToColumns( $ds_group, array $columnNames ){
	
		foreach ($columnNames as $name) {
			
			$oColumnModel = $this->getColumnModelByName($name);
			$oColumnModel->setDs_group( $ds_group );
		}
		
	}

	public function getValueAt($rowIndex, $columnIndex){
		$oObject = $this->entities->getObjectByIndex($rowIndex);
		return $this->getValue($oObject, $columnIndex);
	}

	public function getEntities(){
		return $this->entities;
	}

	public function getRowsCount(){
		return $this->entities->size();
	}


	public function getValue($anObject, $columnIndex){

		$cModel = $this->getColumnModel( $columnIndex );
		
		if( $cModel != null ){
			$columnName = $this->getColumnModel( $columnIndex )->getDs_field();
			//return CdtReflectionUtils::doGetter( $anObject, $columnName );
	
			//pueden ser varios getters, separados por ",".
			$getters = explode(",", $columnName );
			
			$values = array();
			foreach ($getters as $getter) {
				$values[] = CdtReflectionUtils::doGetter( $anObject, trim($getter) );
			}
					
			return implode(" ", $values );
		}else{
			return "empty $columnIndex";			
		}
		
	}


	public function getEntityId( $anObject ){
			
		return $this->getValue( $anObject, 0);
			
	}

	public function getDefaultOrderField(){
		return $this->getColumnModel( 0 )->getDs_field();
	}

	public function getDefaultOrderType(){
		return "DESC";
	}
	
	public function getDefaultFilterField(){
		return $this->getFilterModel( 0 )->getDs_name();
	}

	public function buildGridColumnModel( $name, $label, $width, $sqlField="", $format=null){
		$oColumn = new GridColumnModel();
		$oColumn->setDs_field( $name );
		$oColumn->setDs_sqlField( $sqlField );
		$oColumn->setDs_name( $name );
		$oColumn->setDs_label( $label );
		$oColumn->setBl_visible( true );
		$oColumn->setNu_width( $width );

		if( empty( $format ))
		$format = new GridValueFormat();
			
		$oColumn->setFormat( $format );

		$this->addColumn( $oColumn );

	}

	public function buildFilterModel( $name, $label, $width, $sqlField="", $format=null, $type="", $bl_hidden=false, $ds_value="", $ds_operator="LIKE", $oCriteriaFormatValue = null){

		$oFilter = new GridFilterModel();
		$oFilter->setDs_field( $name );
		$oFilter->setDs_sqlField( $sqlField );
		$oFilter->setDs_name( $name );
		$oFilter->setDs_label( $label );
		$oFilter->setBl_hidden( $bl_hidden );
		$oFilter->setDs_value( $ds_value );

		if(empty($type))
		$type= CDT_CMP_GRID_FILTER_TYPE_STRING ;
		$oFilter->setType( $type );
			
		if(empty($ds_operator))
		$ds_operator="LIKE";
		$oFilter->setDs_operator( $ds_operator );

		if( $oCriteriaFormatValue == null )
		$oCriteriaFormatValue= new CdtCriteriaFormatLikeValue();
		$oFilter->setCriteriaFormatValue( $oCriteriaFormatValue );


		if( empty( $format ))
		$format = new GridValueFormat();
			
		$oFilter->setFormat( $format );

		$this->addFilter( $oFilter );
	}

	public function buildFilterHiddenModel( $name, $sqlField="", $ds_value="", $ds_operator="=", $oCriteriaFormatValue = null){

		if( $oCriteriaFormatValue == null )
		$oCriteriaFormatValue= new CdtCriteriaFormatValue();

		if(empty($ds_operator))
		$ds_operator="=";		
		$this->buildFilterModel( $name, $name, 15, $sqlField, null, null, true, $ds_value, $ds_operator, $oCriteriaFormatValue);			
	}

	public function buildModel( $name, $label, $width, $sqlField="", $format=null, $type="", $bl_hidden=false, $ds_value="", $ds_operator="LIKE", $oCriteriaFormatValue = null){
		if(empty($ds_operator))
		$ds_operator="LIKE";

		$this->buildGridColumnModel( $name, $label, $width, $sqlField, $format );		
		$this->buildFilterModel( $name, $label, $width, $sqlField, $format, $type, $bl_hidden, $ds_value, $ds_operator, $oCriteriaFormatValue );

	}

	public function buildAction( $action, $name, $label, $ds_image, $ds_style,  $bl_multiple=false, $ds_callback=""){
		$oAction = new GridActionModel();
		$oAction->setDs_action( $action );
		$oAction->setDs_name( $name );
		$oAction->setDs_label( $label );
		$oAction->setBl_multiple( $bl_multiple );
		$oAction->setDs_image( $ds_image );
		$oAction->setDs_style( $ds_style );
		$oAction->setDs_callback( $ds_callback );
		$this->addAction( $oAction );
	}

	public function buildRowAction( $action, $name, $label, $ds_image, $ds_style="", $ds_callback="", $bl_multiple=false, $confirmMsg="", $isPopup=false, $nu_heightPopup=500, $nu_widthPopup=750){
		$oAction = new GridActionModel();
		$oAction->setDs_action( $action );
		$oAction->setDs_name( $name );
		$oAction->setDs_label( $label );
		$oAction->setBl_multiple( $bl_multiple );
		$oAction->setDs_image( $ds_image );
		$oAction->setDs_style( $ds_style );
		$oAction->setDs_callback( $ds_callback );
		$oAction->setDs_confirmationMsg( $confirmMsg );
		$oAction->setBl_popUp( $isPopup );
		$oAction->setNu_heightPopup(  $nu_heightPopup );
		$oAction->setNu_widthPopup(  $nu_widthPopup );
		return $oAction ;
	}

	public function getFiltersCount( ){
		return $this->filtersModel->size();
	}

	public function addFilter( GridFilterModel $filter ){
		$this->filtersModel->addItem( $filter );
	}

	public function getFilterModel( $index ){

		return $this->filtersModel->getObjectByIndex( $index );
	}

	public function getFilterModelByName( $name ){
		foreach ($this->filtersModel as $oFilterModel) {				
			if( $oFilterModel->getDs_name() == $name )
			return $oFilterModel;
		}
	}

	public function getTotalRows()
	{
		return $this->totalRows;
	}

	public function setTotalRows($totalRows)
	{
		$this->totalRows = $totalRows;
	}

	public function getColumnsModel()
	{
		return $this->columnsModel;
	}

	public function getFiltersModel()
	{
		return $this->filtersModel;
	}

	public function setFiltersModel($filtersModel)
	{
		$this->filtersModel = $filtersModel;
	}


	public function getActionsModel()
	{
		return $this->actionsModel;
	}

	public function setActionsModel($actionsModel)
	{
		$this->actionsModel = $actionsModel;
	}

	public function getActionsCount( ){
		return $this->actionsModel->size();
	}

	public function addAction( GridActionModel $action ){
		$this->actionsModel->addItem( $action );
	}

	public function getActionModel( $index ){

		return $this->actionsModel->getObjectByIndex( $index );
	}

	public function getRowActionsModel( $item ){
		return $this->rowActionsModel;
	}

	public function setRowActionsModel($actionsModel)
	{
		$this->rowActionsModel = $actionsModel;
	}

	public function getRowActionsCount( ){
		return $this->rowActionsModel->size();
	}

	public function addRowAction( GridActionModel $action ){
		$this->rowActionsModel->addItem( $action );
	}

	public function getRowActionModel( $index ){

		return $this->rowActionsModel->getObjectByIndex( $index );
	}

	public function getCustomFilters(){
		return "";
	}

	protected function getDefaultRowActions($item, $ds_entityName, $ds_entityLabel, $view=true, $update=true, $delete=true, $bl_multiple_delete=false, $nu_heightPopup=500, $nu_widthPopup=750 ){
		$actions = new ItemCollection();

		if( $view)
		$actions->addItem( $this->buildViewAction( $item, $ds_entityName, $ds_entityLabel, $nu_heightPopup, $nu_widthPopup ) );

		if( $update )
		$actions->addItem( $this->buildUpdateAction( $item, $ds_entityName, $ds_entityLabel ) );

		if( $delete )
		$actions->addItem( $this->buildDeleteAction( $item, $ds_entityName, $ds_entityLabel, $this->getMsgConfirmDelete( $item ), $bl_multiple_delete ) );

		return $actions;

	}

	protected function buildViewAction( $item, $ds_entityName, $ds_entityLabel, $nu_heightPopup=500, $nu_widthPopup=750){
		$action = $this->buildRowAction( "view_$ds_entityName", "view_$ds_entityName", CDT_CMP_GRID_MSG_VIEW . " $ds_entityLabel", CDT_UI_IMG_SEARCH, "view", "", false, "", true, $nu_heightPopup, $nu_widthPopup ) ;
		return $action;
	}

	protected function buildUpdateAction( $item, $ds_entityName, $ds_entityLabel){
		$nu_heightPopup=''; 
		$nu_widthPopup=''; 
		$action = $this->buildRowAction( "update_" . $ds_entityName . "_init", "update_$ds_entityName" . "_init", CDT_CMP_GRID_MSG_EDIT . " $ds_entityLabel", CDT_UI_IMG_EDIT, "edit", "", false, "", false, $nu_heightPopup, $nu_widthPopup ) ;
		return $action;
	}


	protected function buildDeleteAction( $item, $ds_entityName, $ds_entityLabel, $msg, $bl_multiple_delete){
		$nu_heightPopup=0;
		$nu_widthPopup=0;
		$action =  $this->buildRowAction( "delete_$ds_entityName", "delete_$ds_entityName", CDT_CMP_GRID_MSG_DELETE . " $ds_entityLabel", CDT_UI_IMG_DELETE, "delete", "delete_items('delete_$ds_entityName')", $bl_multiple_delete, $msg, false, $nu_heightPopup, $nu_widthPopup) ;
		return $action;
	}

	protected function getMsgConfirmDelete( $item ){
		if(!empty($item)){
			$id = $this->getEntityId( $item );
		}else{
			$id="";
		}

		$msg = CDT_CMP_GRID_MSG_CONFIRM_DELETE_QUESTION;
		$params[] = $id;
		$msg = CdtFormatUtils::formatMessage($msg, $params);

		return CdtFormatUtils::quitarEnters($msg);
	}

	public function resetActions(){
		$this->actionsModel = new ItemCollection();
	}

	public function resetRowActions(){
		$this->rowActionsModel = new ItemCollection();
	}
	public function resetFiltersModels(){
		$this->filtersModel = new ItemCollection();
	}

	public function setFilterModelOptions( $filterName, $options, $type){
		$oFilterModel = $this->getFilterModelByName( $filterName );
		$oFilterModel->setItems( $options );
		$oFilterModel->setType( $type );
	}

	public function enhanceCriteria( CdtSearchCriteria $oCriteria ){}
	
	public function getRowStyleClass( $item ){
		return "grid_row_class";
	}
	
	public function getHeaderContent(){
		return "";
	}
	
	public function getFooterContent(){
		return "";
	}
	
	public function buildExportPdfAction( $gridId ){
	
		$callback = "export_"  . $gridId . "( 'cmp_grid_pdf' ); return false;";
		
		$this->buildAction( "none", "pdf", CDT_UI_LBL_EXPORT_PDF, "image", "pdf", false, $callback );	
	}
	
	public function buildExportXlsAction( $gridId ){
	
		$callback = "export_"  . $gridId . "( 'cmp_grid_xls' ); return false;"; 
			
		$this->buildAction( "none", "xls", CDT_UI_LBL_EXPORT_XLS, "image", "excel", false, $callback );
			
	}
	
	public function getExportTitle(){
		return  str_replace(" ", "_", $this->getTitle() );
	}
}