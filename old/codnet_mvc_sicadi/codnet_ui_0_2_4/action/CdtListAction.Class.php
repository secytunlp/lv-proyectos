<?php

/**
 * Acción para listar entidades.
 * Cada subclase definirá la entidad concreta listar. 
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 05-03-2010
 *
 */
abstract class CdtListAction extends CdtOutputAction{

	//table model asociado a las entidades
	protected $tableModel;

	//para determinar si hay filtros o no.
	protected $hasFilters = false;

	
	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getOutputContent();
	 */
	protected function getOutputContent(){

		//armamos el search criteria
		$oCriteria = $this->getCdtSearchCriteria();

		try{
			//obtenemos las entidades a listar.
			$entities = $this->getEntityManager()->getEntities ( $oCriteria );
			$num_rows = $this->getEntityManager()->getEntitiesCount (  $oCriteria );

			//$entities = new ItemCollection();
			
			
		}catch(GenericException $ex){
			//capturamos la excepción para terminar de parsear el contenido y luego la volvemos a lanzar para mostrar el error.
			$entities = new ItemCollection();
			$num_rows = 0;
			$this->getLayoutInstance()->setException( $ex );
		}
		
		//obtenemos el xtemplate para parsear la salida.
		$xtpl = $this->getXTemplate();
		
		//web path
		$xtpl->assign('WEB_PATH', WEB_PATH);

		//labels
		$xtpl->assign( 'lbl_order_by', CDT_UI_LBL_ORDER_BY );
		$xtpl->assign( 'lbl_search', CDT_UI_LBL_SEARCH );
		$xtpl->assign( 'lbl_search_all', CDT_UI_LBL_SEARCH_ALL);
				
		//parseamos las entidades en el template.
		$this->parseEntities( $xtpl, $entities, $num_rows, $oCriteria );
		
		//título del listado.
		$xtpl->assign( 'title', $this->getTitleList() );
		
		$xtpl->parse( 'main' );
		$content = $xtpl->text( 'main' );		
		return $content;

	}
	
	
	
	/**
	 * se parsea el listado de entidades.
	 * @param XTemplate $xtpl template donde se parsea el listado.
	 * @param ItemCollection $entities
	 * @param int $num_rows cantidad de entidades
	 * @param unknown_type $oCriteria
	 */
	protected function parseEntities(XTemplate $xtpl, $entities, $num_rows, CdtSearchCriteria $oCriteria){

		//recuperamos los parámetros de la paginación.
		$filterValue = $this->getCriteriaFilterValue();
		$page = $this->getCriteriaPage();
		$orderType = $this->getCriteriaOrderType();
		$orderField = $this->getCriteriaOrderField();
		$filterField = $this->getCriteriaFilterField();
		
		//los setemos en el template.
		$xtpl->assign( 'orderField', $orderField );
		$xtpl->assign( 'orderType', $ordenType );
		$xtpl->assign( 'filterField', $filterField );
		$xtpl->assign( 'filterValue', $filterValue );
		
		//parseamos la acción asociada al listado.
		$xtpl->assign( 'actionList', $this->getActionList() );
		$xtpl->parse( 'main.hidden');

		//armamos el query string (para la paginación y la ordenación).
		$query_string = $this->getQueryString( $filterValue, $filterField)."id=".CdtUtils::getParam('id') . $this->getAdditionalFiltersQueryString()."&";

			
		//seteamos el table model.
		$this->tableModel = $this->getEntitiesTableModel( $entities );

		//botones sobre el listado.
		$this->parseOptions( $xtpl );

		//filtros de búsqueda
		$this->parseFilterOptions( $xtpl , $filterField);

		$this->parseAdditionalFilters( $xtpl );

		//si se mostraron filtros, parseamos para que se vean.
		if( $this->hasFilters )
			$xtpl->parse('main.table_buttons');

		//manejo de errores.
		$this->parseErrors($xtpl);

		//construimos el paginador y lo parseamos.
		$oPaginator = $this->getPaginator($num_rows, $orderType, $filterField, $filterValue, $orderField, $page);
		
		$this->parseResults( $xtpl, $oPaginator, $entities, $oCriteria, $query_string );
		
	}
	
	protected function parseResults( XTemplate $xtpl, CdtPaginator $oPaginator, ItemCollection $entities, CdtSearchCriteria $oCriteria, $query_string){
		
		if(! CdtUtils::getParam("search")){
			
			$xtpl_result = new XTemplate(CDT_UI_TEMPLATE_LIST_ENTITIES_RESULTS);
		
			$this->parsePaginator( $xtpl_result , $oPaginator );
				
			//header del listado.
			$this->parseHeader( $xtpl_result , $entities, $oCriteria );

			//encabezados (ths) de la tabla.
			$this->parseTHs( $xtpl_result , $query_string );

			//se parsean los elementos a mostrar
			$this->parseRows( $xtpl_result , $entities);

			//footer del listado.
			$this->parseFooter( $xtpl_result , $entities, $oCriteria );
			
			$xtpl_result->parse("main");
			$xtpl->assign("search_first_time", $xtpl_result->text("main") );
			
		}else{
		
			$this->parsePaginator( $xtpl, $oPaginator );
					
			//header del listado.
			$this->parseHeader( $xtpl, $entities, $oCriteria );
	
			//encabezados (ths) de la tabla.
			$this->parseTHs( $xtpl, $query_string );
	
			//se parsean los elementos a mostrar
			$this->parseRows( $xtpl , $entities);
	
			//footer del listado.
			$this->parseFooter( $xtpl, $entities, $oCriteria );				
		}	
	}
	
	/**
	 * se arma el criterio de búsqueda para filtrar el listado.
	 * @return CdtSearchCriteria
	 */
	protected function getCdtSearchCriteria(){
		
		//recuperamos los parámetros.
		$filterValue = $this->getCriteriaFilterValue();
		$page = $this->getCriteriaPage();
		$orderType = $this->getCriteriaOrderType();
		$orderField = $this->getCriteriaOrderField();
		$filterField = $this->getCriteriaFilterField();
		
		//armamos el criteria
		$oCriteria = new CdtSearchCriteria();

		if( !empty( $filterField ))
			$this->parseSelectedFilter($oCriteria,$filterField, $filterValue);

		$oCriteria->addOrder($orderField, $orderType);
		$oCriteria->setPage($page);
		$oCriteria->setRowPerPage(ROW_PER_PAGE);
		
		return $oCriteria;
	}


	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getXTemplate();
	 */
	protected function getXTemplate(){

		if( CdtUtils::getParam("search"))
			return new XTemplate(CDT_UI_TEMPLATE_LIST_ENTITIES_RESULTS);
		else	
			return new XTemplate(CDT_UI_TEMPLATE_LIST_ENTITIES);
	}

	protected function getLayout(){
		
		if( CdtUtils::getParam("search"))
			$oLayout = new CdtLayoutBasicAjax();
		else
			$oLayout = parent::getLayout();
		
		return $oLayout;
	}	
	

	/**
	 * Entidad que implementa la interfaz ICdtList
	 * para poder obtener las entidades a visualizar.
	 * @return ICdtList
	 */
	protected abstract function getEntityManager();

	/**
	 * table model para describir el listado.
	 * @param ItemCollecion $items
	 * @return CdtEntitiesTableModel
	 */
	protected abstract function getEntitiesTableModel( ItemCollection $items );


	/**
	 * campo de ordenamiento por default.
	 * @return string
	 */
	protected abstract function getDefaultOrderField();

	/**
	 * campo de filtro por default.
	 * @return string
	 */
	protected function getDefaultFilterField(){
		return $this->getDefaultOrderField();
	}

	/**
	 * action asociada al listado
	 * @return string
	 */
	protected abstract function getActionList();

	/**
	 * action para exportar las entidades a pdf
	 * @return string
	 */
	protected function getActionPdf(){
		return '';
	}

	/**
	 * action para exportar las entidades a xls
	 * @return string
	 */
	protected function getActionXls(){
		return '';
	}
	
	/**
	 * forward por error.
	 * @return string
	 */
	protected abstract function getForwardError();

	
	
	/**
	 * se parsean la colección de items dentro del template.
	 * @param XTemplate $xtpl template donde parsear los items.
	 * @param ItemCollection $items items a ser parseados.
	 */
	protected function parseRows(XTemplate $xtpl, ItemCollection $items){

		$even = false;

		foreach ($items as $key=> $item){

			//parseamos el item -- main.row.column
			$this->parseItem( $xtpl, $item );

			//acciones sobre el item
			$this->parseActions( $xtpl, $item );

			//estilo de la fila.
			$row_class = ( $even )? $this->getRowClassEven() : $this->getRowClassOdd();
			$xtpl->assign('rowClass', $row_class );

			$xtpl->parse('main.row' );

			$even = !$even;
		}

		$this->parseOpcionesBarra( $xtpl  );
	}

	

	protected function getAdditionalFiltersQueryString(){
		return "";
	}
	
	/**
	 * se parsea un filtro de búsqueda.
	 * @param $xtpl template a parsear.
	 * @param $value valor para el filtro
	 * @param $label descripción del filtro.
	 * @param $selected valor seleccionado. 
	 */
	protected function parseFilter(XTemplate $xtpl, $value, $label, $selected=''){
		
		$xtpl->assign ( 'filterField', CdtFormatUtils::selected( $value, $selected) );
		//$xtpl->assign ( 'filterValue',  $value );
		$xtpl->assign ( 'filterLabel', $label );
		//$xtpl->parse ( 'main.botones_tabla.combo_filtros.opcion_filtro' );
		$xtpl->parse ( 'main.table_buttons.filters_combobox.filter_option' );
	}

	/**
	 * parsea el valor de un item. (el valor de una columna en una fila).
	 * @param $xtpl template a parsear.
	 * @param $value valor a parsear.
	 * @return none.
	 */
	protected function parseItemValue(XTemplate $xtpl, $value){
		$xtpl->assign ( 'value', $value );
		$xtpl->parse('main.row.column' );
	}

	/**
	 * parsea una acción en la fila corriente.
	 * @param XTemplate $xtpl template a parsear.
	 * @param string $onclick evento del onclick.
	 * @param string $href href del link.
	 * @param string $img imagen del link.
	 * @param string $title descripción del link.
	 * @param string $target target del link 
	 */
	protected function parseAction(XTemplate $xtpl, $onclick, $href, $img, $title,$target='_self'){

		$link = ( empty($href) )? $onclick : $href ;

		//TODO check permisos
		//if( $this->tienePermisoLink( $link ) ){
		$xtpl->assign('onclick', $onclick );
		$xtpl->assign('href', $href );
		$xtpl->assign('target', $target );
		$xtpl->assign('img', $img );
		$xtpl->assign('title', $title );
		$xtpl->parse('main.row.action' );
		//}

	}

	protected function parseOpcionBarra(XTemplate $xtpl, $functionCallback, $title,$divClass='delete'){

		$xtpl->assign('divClass', $divClass );
		$xtpl->assign('functionCallback', $functionCallback);
		$xtpl->assign('title', $title );
		$xtpl->parse('main.barra_opcion' );

	}

	/**
	 * se parsea un botón sobre el listado.
	 * es un botón que aparece arriba del listado.
	 * @param XTemplate $xtpl template a parsear.
	 * @param string $name nombre del botón
	 * @param string $label label del botón.
	 * @param string $action acción al ejecutar en el onlick
	 * @param string $style estilo css asociado al botón.
	 * @return none.
	 */
	protected function parseOption(XTemplate $xtpl, $name, $label, $action, $style='add'){

		//TODO check permisos
		//if( $this->tienePermisoAccion( $action ) ){
		$xtpl->assign('name', $name );
		$xtpl->assign('label', $label );
		$xtpl->assign('action', $action );
		$xtpl->assign('liClass', $style );
		//$xtpl->parse('main.opcion' );
		$xtpl->parse('main.option' );
		//}
	}

	/**
	 * se parsean los filtros adicionales
	 * (específicos de cada listado).
	 * @param $xtpl template donde se parsean los filtros adicionales.
	 */
	protected function parseAdditionalFilters( XTemplate $xtpl ){
		$filters = $this->getAdditionalFilters();
		if( !empty( $filters )){
			$xtpl->assign( 'additionFilters', $filters );
			//$xtpl->parse('main.botones_tabla.filtrosEspeciales');
			$xtpl->parse('main.table_buttons.additional_filters');
			$this->hasFilters = true;
		}

	}

	/**
	 * se obtienen los filtros adicionales
	 * (específicos de cada listado).
	 * @return string
	 */
	protected function getAdditionalFilters(){
		return '';
	}

	/**
	 * se parsea un header para el listado.
	 * @param $xtpl template donde se parsea el footer.
	 * @param $entities entidades que se están parseando
	 * @param $oCriteria criterio de búsqueda de las entidades.
	 */
	protected function parseHeader( XTemplate $xtpl, ItemCollection $entities, CdtSearchCriteria $oCriteria ){
		$xtpl->assign( 'header', $this->getHeader($entities, $oCriteria));
		$xtpl->parse('main.header');

	}

	/**
	 * obtiene el header.
	 * @param $entities entidades que se están parseando
	 * @param $oCriteria criterio de búsqueda de las entidades.
	 * @return string
	 * @return string
	 */
	protected function getHeader( ItemCollection $entities, CdtSearchCriteria $oCriteria ){
		return '';
	}

	/**
	 * se parsea un footer para el listado.
	 * @param $xtpl template donde se parsea el footer.
	 * @param $entities entidades que se están parseando
	 * @param $oCriteria criterio de búsqueda de las entidades.
	 */
	protected function parseFooter( XTemplate $xtpl, ItemCollection $entities, CdtSearchCriteria $oCriteria){
		$xtpl->assign( 'footer', $this->getFooter($entities, $oCriteria));
		$xtpl->parse('main.footer');
	}

	/**
	 * obtiene el footer.
	 * @param $entities entidades que se están parseando
	 * @param $oCriteria criterio de búsqueda de las entidades.
	 * @return string
	 */
	protected function getFooter( ItemCollection $entities, CdtSearchCriteria $oCriteria ){
		return '';
	}

	/**
	 * parsea las acciones por default de un listado:
	 *   - ver detalles.
	 *   - modificar.
	 *   - eliminar.
	 * @param XTemplate $xtpl template sobre el cual se parsea.
	 * @param object $entity entidad sobre la cual se realizan las acciones.
	 * @param string $id identificador de la entidad.
	 * @param string $ds_entity nombre de la entidad
	 * @param string $lbl_entity label para la entidad.
	 * @param boolean $view se debe parsearse la opción "view"
	 * @param boolean $update se debe parsearse la opción "update"
	 * @param boolean $delete se debe parsearse la opción "delete"
	 */
	protected function parseDefaultActions(XTemplate $xtpl, $entity, $id, $ds_entity, $lbl_entity=null, $view=true, $update=true, $delete=true){

		if( empty($lbl_entity))
			$lbl_entity = $ds_entity;

		if($view){
			$url = 'doAction?action=view_' . $ds_entity . '&id=' . $id;
			$onclick = "javascript: loadPopup('$url') ; return false;";
			$this->parseAction( $xtpl, $onclick, '', CDT_UI_IMG_SEARCH , $lbl_entity . " details");
		}

		if($update){
			$href = 'doAction?action=update_'. $ds_entity .  '_init&id=' . $id;
			$this->parseAction( $xtpl, '', $href, CDT_UI_IMG_EDIT, $lbl_entity . " update ");
		}

		if($delete){
			$onclick = "javascript: confirmDelete('" . $this->getMsgConfirmDelete($entity) . "', this,'doAction?action=delete_". $ds_entity . "&id=" . $id . "'); return false;" ;
			$this->parseAction( $xtpl, $onclick, '', CDT_UI_IMG_DELETE , $lbl_entity . " delete");
		}
	}

	protected function parseDefaultOpcionesBarra(XTemplate $xtpl, $ds_entity, $lbl_entity=null, $view=true, $update=true, $delete=true){

		if( empty($lbl_entity))
			$lbl_entity = $ds_entity;

		if($view){
			
			$url = 'doAction?action=view_' . $ds_entity . '&id=';
			$onclick = "loadPopup('$url' + getSelected() ); ";
			
			$this->parseOpcionBarra($xtpl, $onclick, "view",$divClass='view');
			
		}

		if($update){
			$url = 'doAction?action=update_' . $ds_entity . '_init&id=';
			$onclick = "loadContent('$url' + getSelected() ); ";
			$this->parseOpcionBarra($xtpl, $onclick, "edit",$divClass='edit');
			//$href = 'doAction?action=update_'. $ds_entity .  '_init&id=' . $id;
			//$this->parseAction( $xtpl, '', $href, CDT_UI_IMG_EDIT, $lbl_entity . " update ");
		}

		if($delete){
			$url = 'doAction?action=delete_' . $ds_entity . '_init&id=';
			$onclick = "loadContent('$url' + getSelected() ); ";
			//TODO hay que hacer un action delete_init que traiga el mensaje con lo que se va a eliminar. 
			$this->parseOpcionBarra($xtpl, $onclick, "delete",$divClass='delete');
			
			//$onclick = "javascript: confirmDelete('" . $this->getMsgConfirmDelete($entity) . "', this,'doAction?action=delete_". $ds_entity . "&id=" . $id . "'); return false;" ;
			//$this->parseAction( $xtpl, $onclick, '', CDT_UI_IMG_DELETE , $lbl_entity . " delete");
		}
	}
	/**
	 * retorna el mensaje para la confirmación para eliminar una entidad.
	 * @param unknown_type $entity entidad a eliminar.
	 * @return string.
	 */
	protected function getMsgConfirmDelete($entity){
		return "Do you confirm delete entity?";
	}
	
	/**
	 * título para el listado
	 * @return string.
	 */
	protected function getTitleList(){
		return $this->getOutputTitle();
	}
	

	/**
	 * retorna la página para la paginación de los resultados
	 * @return string
	 */
	protected function getCriteriaPage(){
		return  CdtUtils::getParam('page',1);
	}


	/**
	 * retorna el filtro para la búsqueda
	 * @return string
	 */
	protected function getCriteriaFilterValue(){
		return  CdtUtils::getParam('filterValue', "");
	}
	
	/**
	 * retorna el campo filtro para la búsqueda
	 * @return string
	 */
	protected function getCriteriaFilterField(){
		return  CdtUtils::getParam('filterField', "");
	}
	
	/**
	 * retorna el tipo de orden para la búsqueda
	 * @return string
	 */
	protected function getCriteriaOrderType(){
		return  CdtUtils::getParam('orderType', 'DESC');
	}
	
	/**
	 * retorna el campo de orden para la búsqueda
	 * @return string
	 */
	protected function getCriteriaOrderField(){
		return  CdtUtils::getParam('orderField', $this->getDefaultOrderField());
	}

	/**
	 * retorna el query string para los valores dados.
	 * @param string $filterValue
	 * @param string $filterField
	 * @return string
	 */
	protected function getQueryString($filterValue, $filterField){
		//return "?filterValue=$filterValue&filterField=$filterField&";
		
		return "?search=" . CdtUtils::getParam("search") . "&";
	}

	/**
	 * se parsea el filtro seleccionado
	 * @param CdtSearchCriteria $oCriteria criterio de búsqueda 
	 * @param string $filterField campo por el cual filtrar
	 * @param string $filterValue valor a filtrar
	 */
	protected function parseSelectedFilter(CdtSearchCriteria $oCriteria,$filterField, $filterValue){

		//se hace un trato especial si el prefijo es de una fecha "dt_"
		
		
		if(substr( $filterField,0,3) == 'dt_' ){
			if( !empty( $filter ))
			$oCriteria->addFilter($filterField, $filterValue, '=', new CdtCriteriaFormatDateValue());
		}
		else
			$oCriteria->addFilter($filterField, $filterValue, 'LIKE', new CdtCriteriaFormatLikeValue());
	}
	
	/**
	 * se parsean los errores.
	 * @param XTemplate $xtpl
	 * @deprecated
	 */
	protected function parseErrors( XTemplate $xtpl ){
		
		if( CdtUtils::hasRequestError() ){
		
			$error = CdtUtils::getRequestError();
			
			$msg  = $error['msg'];
			$code = $error['code'];
			
			$xtpl->assign ( 'msg', $msg );
			$xtpl->assign ( 'code', $code );
			$xtpl->assign ( 'classMsg', 'msjerror' );
			$xtpl->parse ( 'main.msg' );
			
		}
		
		
	}

	/**
	 * clase para el estilo de las filas pares
	 * @return string
	 */
	protected function getRowClassEven(){
		return "";
	}

	/**
	 * clase para el estilo de las filas impares
	 * @return string
	 */
	protected function getRowClassOdd(){
		return "color-b";
	}
	

	/**
	 * se parsea la entidad en el xtemplate.
	 * @param $xtpl Xtemplate asociado al listado.
	 * @param $entity entidad a parsear.
	 */
	protected function parseItem(XTemplate $xtpl, $entity){
		
		$values = $this->getValues($entity);
		
		$count = count($values);
		
		//parseamos el id.
		$xtpl->assign ( 'itemId', $this->tableModel->getItemId($entity) );
		
		for($index=0;$index<$count;$index++) 
			
			$this->parseItemValue( $xtpl, $values[$index]);
		

	}

	/**
	 * se retorna una lista con los valores de las columnas de la fila corriente.
	 * @return array(string)
	 */
	protected function getValues($item){
		
		for ($i=0; $i < $this->tableModel->getColumnCount(); $i++)

			$values[]=  $this->tableModel->getValue($item, $i) ;
		
		return $values;
	}


	/**
	 * se parsean los opciones: botones sobre la tabla.
	 * @param XTemplate $xtpl template donde se parsean los botones.
	 */
	protected function parseOptions(XTemplate $xtpl){

		//botones sobre el listado para la exportacón.
		$xls = $this->getActionXls();
		if(!empty ( $xls) ){
			$xtpl->assign ( 'actionXls', $xls );
			$xtpl->assign( 'lbl_export_xls', CDT_UI_LBL_EXPORT_XLS );
			$xtpl->parse ( 'main.export_xls' );
		}

		$pdf = $this->getActionPdf();
		if( !empty( $pdf )){
			$xtpl->assign ( 'actionPdf', $pdf );
			$xtpl->assign( 'lbl_export_pdf', CDT_UI_LBL_EXPORT_PDF );
			$xtpl->parse ( 'main.export_pdf' );
		}

		//demás botones definidos por las subclases.
		$options = $this->getOptions();
		$count = count($options);

		for($index=0;$index<$count;$index++) {

			$name = $options[$index]['name'];
			$label = $options[$index]['label'];
			$action = $options[$index]['action'];
			$style = $options[$index]['style'];
			
			$this->parseOption( $xtpl, $name, $label, $action, $style);
		}
	}


	/**
	 * se retorna una lista con los botones sobre la tabla.
	 * 
	 * cada elemento de la lista deberá ser un array de la forma:
	 *    - vector['name']='nombre del botón'
	 *    - vector['label']='label del botón'
	 *    - vector['action']='action asociada al botón'
	 *    - vector['style']='estilo css asociado al botón'
	 * se puede usar el método buildButton($xtpl, $name, $label, $action, $style) para formar dicho arreglo.   
	 * @return array( array(string, string, string) )
	 */
	protected function getOptions(){
		return array();
	}

	/**
	 * se construye un botón
	 * @param string $name
	 * @param string $label
	 * @param string $action
	 * @param string $style
	 * @return array(string, string, string)
	 */
	protected function buildOption( $name, $label, $action, $style='add'){
		$option['name']= $name;
		$option['label']= $label;
		$option['action']= $action;
		$option['style']= $style;
		return $option;
	}

	/**
	 * se parsean las opciones del filtro de búsqueda:
	 * opciones del combo.
	 * @param XTemplate $xtpl template donde se parsean los filtros
	 * @param $filterField campo por el cual se filtra (es el seleccionado del combo).
	 */
	protected function parseFilterOptions(XTemplate $xtpl, $filterField){

		$filters = $this->getFilters();
		
		$count = count($filters);

		for($index=0;$index<$count;$index++) {

			$order = $filters[$index]['order'];
			$label = $filters[$index]['label'];

			$this->parseFilter( $xtpl, $order, $label, $filterField);
		}

		//imprimimos el combo y el campo de texto para la búsqueda siempre y 
		//cuando se haya definido algún filtro.
		if($count>0){
			//$xtpl->parse ( 'main.botones_tabla.combo_filtros' );
			$xtpl->parse ( 'main.table_buttons.filters_combobox' );
			$this->hasFilters = true;
		}

	}

	/**
	 * se retorna una lista con los filtros de búsqueda.
	 * cada elemento de la lista deberá ser un array de la forma:
	 *    - vector['order']='campo orden'
	 *    - vector['label']='label de la ordenación'
	 * se puede usar el método buildFiltro(order, label) para formar dicho arreglo.   
	 * @return array( array/(string, string) )
	 */
	protected function getFilters(){
		
		//por default armamos los filters con los ths del tableModel.
		
		$filters = array();
		
		$ths = $this->tableModel->getTHs();
		$count = count($ths);
		for($index=0;$index<$count;$index++) {
			$label = $ths[$index]['orderLabel'];
			$order = $ths[$index]['orderField'];
			
			$filter = $this->buildFilter( $order, $label   );
			
			$filters[] = $filter;
		}
		return $filters;
	}

	
	/**
	 * se construye un filtro
	 * @param string $order
	 * @param string $label
	 * @return array(string, string)
	 */
	protected function buildFilter($order, $label){
		$filter['order']= $order;
		$filter['label']= $label;
		return $filter;
	}

	/**
	 * se parsean los encabezados de las columnas del listado.
	 * @param XTemplate $xtpl template donde se parsean los ths
	 * @param string $query_string
	 */
	protected function parseTHs(XTemplate $xtpl, $query_string){

		$ths = $this->tableModel->getTHs();
		$count = count($ths);
		for($index=0;$index<$count;$index++) {
			$label = $ths[$index]['label'];
			$orderField = $ths[$index]['orderField'];
			$orderLabel = $ths[$index]['orderLabel'];
			$orderType = CdtUtils::getParam("orderType", "DESC");

			if($orderType=="DESC")
				$orderType = "ASC";
			else	
				$orderType = "DESC";
			
			$this->parseTH( $xtpl, $query_string, $label, $orderField, $orderLabel, $orderType);
		}

	}
	
	/**
	 * se parsea un header del listado.
	 * @param XTemplate $xtpl template a parsear.
	 * @param string $query_string query de búsqueda para mantener los filtros.
	 * @param string $label label del encabezado.
	 * @param string $orderField campo por el cual ordenar el listado al cliquear en el encabezado.
	 * @param string $orderLabel descripción de la ordenación.
	 * @param string $orderType método de ordenación (ASC, DESC).
	 */
	protected function parseTH(XTemplate $xtpl, $query_string, $label, $orderField,  $orderLabel, $orderType = "DESC"){

		$xtpl->assign('queryString', $query_string );
		$xtpl->assign('label', $label );
		$xtpl->assign('orderField', $orderField );
		$xtpl->assign('orderLabel', $orderLabel );
		$xtpl->assign('orderType', $orderType );
		
		$xtpl->assign('actionList', $this->getActionList() );
		
		$xtpl->parse('main.TH' );

	}
	

	/**
	 * se parsean las acciones sobre los elementos del listado.
	 * @param XTemplate $xtpl template donde se parsean las acciones
	 * @param string $item items del listado
	 *
	 * redefinir siguiendo el ejemplo:
	 * 		$xtpl->assign('onclick', 'una funcion' );
	 * 		$xtpl->assign('href', 'un link' );
	 * 		$xtpl->assign('img', 'una imagen' );
	 * 		$xtpl->assign('title', 'un title' );
	 * 		$xtpl->parse('main.row.accion' );
	 *
	 * 		$xtpl->assign('onclick', 'otra funcion' );
	 * 		$xtpl->assign('href', 'otro link' );
	 * 		$xtpl->assign('img', 'otra imagen' );
	 * 		$xtpl->assign('title', 'otro title' );
	 * 		$xtpl->parse('main.row.accion' );
	 */
	protected function parseActions(XTemplate $xtpl, $item){
		
		$actions = $this->getActions($item);
		
		$count = count($actions);
		
		for($index=0;$index<$count;$index++) {
			
			$onclick = $actions[$index]['onclick'];
			$href = $actions[$index]['href'];
			$img = $actions[$index]['img'];
			$title = $actions[$index]['title'];
			
			$this->parseAction( $xtpl, $onclick, $href, $img, $title);
		}

	}

	/**
	 * se retorna una lista con las acciones de cada columna.
	 * cada elemento de la lista deberá ser un array de la forma:
	 *    - accion['onclick']='evento para el onclick'
	 *    - accion['href']='link'
	 *    - accion['img']='imagen'
	 *    - accion['title']='título para la imagen'
	 * se puede usar el método buildAction(onclick, href, img, title) para formar dicho arreglo.   
	 * @return array( array(string, string, string, string) ) 
	 */
	protected function getActions($item){}

	protected function parseOpcionesBarra(XTemplate $xtpl ){
	
		$opciones = $this->getOpcionesBarra();
		
		$count = count($opciones);
		
		for($index=0;$index<$count;$index++) {
			
			$callback = $actions[$index]['$callback'];
			$divClass = $actions[$index]['divClass'];
			$title = $actions[$index]['title'];
			
			$this->parseOpcionBarra( $xtpl, $callback, $title, $divClass);
		}
		
	}
	protected function getOpcionesBarra(){}
	
	/**
	 * construye un action para un elemento del listado.
	 * @param string $onclick
	 * @param string $href
	 * @param string $img
	 * @param string $title
	 * @return string
	 */
	protected function buildAction( $onclick, $href, $img, $title ){
		
		$action['onclick']= $onclick;
		$action['href']= $href;
		$action['img']= $img;
		$action['title']= $title;
		
		return $action;
	}

	/**
	 * se construye el paginador.
	 * @param unknown_type $num_rows número de filas
	 * @param unknown_type $orderType tipo de orden 
	 * @param unknown_type $filterField campo a filtrar
	 * @param unknown_type $filterValue valor del filtro
	 * @param unknown_type $orderField campo de ordenación
	 * @param unknown_type $page página actual de la paginación
	 */
	protected function getPaginator($num_rows, $orderType, $filterField, $filterValue, $orderField, $page){
		
		$num_pages = ceil ( $num_rows / ROW_PER_PAGE );

		$url = $this->getUrlPaginator( $orderType, $filterField, $filterValue, $orderField );
		
		$cssclassotherpage = 'paginadorOtraPagina';
		$cssclassactualpage = 'paginadorPaginaActual';

		return new CdtListPaginator ( $url, $num_pages, $page, $cssclassotherpage, $cssclassactualpage, $num_rows );
	}

	/**
	 * se construye la url para el paginador.
	 * @param unknown_type $orderType tipo de orden 
	 * @param unknown_type $filterField campo a filtrar
	 * @param unknown_type $filterValue valor del filtro
	 * @param unknown_type $orderField campo de ordenación
	 * @return string
	 */
	protected function getUrlPaginator( $orderType, $filterField, $filterValue, $orderField ){
		$url = 'doAction?action='. $this->getActionList() . '&orderType=' . $orderType. '&filterField=' . $filterField . '&filterValue=' . $filterValue. '&orderField=' . $orderField . $this->getAdditionalFiltersQueryString();
		return $url;
	}

	/**
	 * se parse el paginador.
	 * @param XTemplate $xtpl template donde se parsea el paginador.
	 * @param Paginator $oPaginator paginador a parsear.
	 */
	protected function parsePaginator( XTemplate $xtpl, CdtPaginator $oPaginator ){
		
		$xtpl->assign ( 'results', $oPaginator->printResults () );
		$xtpl->parse ( 'main.paginator_results' );
		$xtpl->assign ( 'pages',  $oPaginator->printPagination () );
		$xtpl->parse ( 'main.paginator_pages' );
	}	
}