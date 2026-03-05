<?php

/**
 * Encargado de renderizar la grilla 
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 14-12-2011
 *
 */
class GridRenderer implements IGridRenderer {

	private $oGrid;
	
	
	public function renderStyle(CMPGrid $oGrid, XTemplate $xtpl){
	
		$xtpl->assign('grid_style', CDT_CMP_GRID_STYLE_CSS );
	}
	
	public function render(CMPGrid $oGrid) {

		//seteamos la grilla a renderizar.
		$this->setGrid( $oGrid );
		 
        $xtpl = $this->getXTemplate();

        $this->renderStyle($oGrid, $xtpl);
        
        //labels
        $this->renderLabels( $xtpl );

        $xtpl->assign('gridId', $oGrid->getId() );

        $this->renderHidden( $xtpl);

        $this->renderFilters( $xtpl);

        $this->renderActions( $xtpl);

        if (CdtUtils::getParam("search")) {

        	$this->renderHeader( $xtpl );
        	
            $this->renderResults( $xtpl);
            
            $this->parseMessages( $xtpl );
            
            $this->renderFooter( $xtpl );
            
        } else {

            //la primera vez levantamos los resultados y los mostramos.
            $xtpl_result = $this->getResultsXTemplate();

            $this->renderLabels( $xtpl_result);
            $this->renderActions( $xtpl_result);
            $this->renderHeader( $xtpl_result );
            $this->renderResults( $xtpl_result);
			$this->renderFooter( $xtpl_result );
            $xtpl_result->parse("main");
            $xtpl->assign("search_first_time", $xtpl_result->text("main"));
        }

        $xtpl->assign("title", $oGrid->getGridModel()->getTitle());

        $xtpl->parse('main');
        $content = $xtpl->text('main');
        return $content;
    }

    protected function getResultsXTemplate() {
        return new XTemplate(CDT_CMP_TEMPLATE_GRID_RESULTS);
    }

    protected function getXTemplate() {

        if (CdtUtils::getParam("search"))
            return $this->getResultsXTemplate();
        else
            return new XTemplate(CDT_CMP_TEMPLATE_GRID);
    }

    public function renderLabels( XTemplate $xtpl) {
        $xtpl->assign('lbl_order_by', CDT_UI_LBL_ORDER_BY);
        $xtpl->assign('lbl_search', CDT_UI_LBL_SEARCH);
        $xtpl->assign('lbl_search_all', CDT_UI_LBL_SEARCH_ALL);
    }

    public function renderHidden( XTemplate $xtpl) {

    	$gridId = $this->getGrid()->getId();
    	$model = $this->getGrid()->getGridModel();
    	
    	//$xtpl->assign('gridId', $gridId );
    	
        $filterValue = CdtUtils::getParam("filterValue_$gridId", "");
        $page = CdtUtils::getParam("page_$gridId", 1);
        $orderType = CdtUtils::getParam("orderType_$gridId", "");
        $orderField = CdtUtils::getParam("orderField_$gridId", "");
        $filterField = CdtUtils::getParam("filterField_$gridId", "");

        //los setemos en el template.
        $xtpl->assign("orderField", $orderField);
        $xtpl->assign("orderType", $orderType);
        $xtpl->assign("filterField", $filterField);
        $xtpl->assign("filterValue", $filterValue);
        $xtpl->assign("gridModelClazz", get_class($model));
        $xtpl->assign("rendererClazz", get_class($this));
        $xtpl->assign('actionList', $this->getActionList());
        $xtpl->parse('main.hidden');
        
        
        //tambi�n seteamos como hidden los filtros que as� lo indiquen.
        foreach ($this->getGrid()->getGridModel()->getFiltersModel() as $oFilterModel ) {
        	
        	if( $oFilterModel->getBl_hidden() ){
        	
        		$name = $oFilterModel->getDs_name();
                $value = $oFilterModel->getDs_value();
				
                if($value != 0 && $value != "0" && empty($value)){
                	//el get convierte "." por "_" as� que lo convertimos.
            		$value = CdtUtils::getParam(str_replace(".", "_", $name) . "_" . $gridId, "");
                }
                
        		$xtpl->assign("name", $name . "_$gridId");
			    $xtpl->assign("id", $name . "_$gridId");
        		$xtpl->assign("value", $value);
        		$xtpl->parse("main.extra_hidden");
        		
        	}
        	
        }
        
        
        
    }

    public function getActionList() {
        return "cmp_grid";
    }

    
    public function renderHeader( XTemplate $xtpl) {
    	
    	$header = $this->oGrid->getGridModel()->getHeaderContent();
    	
    	if(!empty ($header)){
    		$xtpl->assign('header', $header );
    		$xtpl->parse('main.header');
    	}
    	
    	
    }
    
	public function renderFooter( XTemplate $xtpl) {
    	
    	$footer = $this->oGrid->getGridModel()->getFooterContent();
    	
    	if(!empty ($footer)){
    		$xtpl->assign('footer', $footer );
    		$xtpl->parse('main.footer');
    	}
    	
    	
    }
    
    public function renderResults( XTemplate $xtpl) {

    	$xtpl->assign('gridId', $this->getGrid()->getId() );

        $this->renderGridHeader( $xtpl);

        $this->renderRowsHeader( $xtpl);

        $this->renderRows( $xtpl);

        $this->renderRowsFooter( $xtpl);

        $this->renderGridFooter( $xtpl);
    }

    public function renderFilters( XTemplate $xtpl) {

    	$gridId = $this->getGrid()->getId();
    	$model = $this->getGrid()->getGridModel();
    	
        $selected = CdtUtils::getParam("filterField_$gridId", "");

        //$xtpl->assign('', '' );
        for ($index = 0; $index < $model->getFiltersCount(); $index++) {

            $oFilterModel = $model->getFilterModel($index);

            $name = $oFilterModel->getDs_name();
            
            $xtpl->assign('filterField', CdtFormatUtils::selected($name, $selected));
            $xtpl->assign('filterLabel', $oFilterModel->getDs_label());
            $xtpl->parse('main.table_buttons.filters.filters_combobox.filter_option');
        }
        $xtpl->parse('main.table_buttons.filters.filters_combobox');

        $this->renderCustomFilters( $xtpl);

        $xtpl->parse('main.table_buttons.filters');
        $xtpl->parse('main.table_buttons');
    }

    /**
     * para filtros definidos espec�ficamente por cada grilla.
     * @param $model
     * @param $xtpl
     */
    protected function renderCustomFilters( XTemplate $xtpl ) {

    	$model = $this->getGrid()->getGridModel();
    	
        $html = $model->getCustomFilters();
        
        if (!empty($html)) {
            $xtpl->assign('additionalFilters', $model->getCustomFilters());
            $xtpl->parse('main.table_buttons.additional_filters');
        }
    }

    public function renderCustomFilter( $label, $filterName, $filterValue, XTemplate $xtpl, $format="valueString"){
    	
    	$xtpl->assign("label", $label);
     	$xtpl->assign("filterName", $filterName);
     	$xtpl->assign("filterValue", $filterValue);
     	$xtpl->parse('main.table_buttons.customFilter.' . $format);
     	$xtpl->parse('main.table_buttons.customFilter');
    }
    
    public function renderActions( XTemplate $xtpl) {
        //$xtpl->assign('', '' );
    	$model = $this->getGrid()->getGridModel();
    	
        for ($index = 0; $index < $model->getActionsCount(); $index++) {

            $oActionModel = $model->getActionModel($index);

            $xtpl->assign('name', $oActionModel->getDs_name());
            $xtpl->assign('label', $oActionModel->getDs_label());
            $xtpl->assign('action', $oActionModel->getDs_action());
            $xtpl->assign('callback', $oActionModel->getDs_callback());
            $xtpl->assign('liClass', $oActionModel->getDs_style());
            $xtpl->parse('main.action');
        }
    }

    public function renderRows( XTemplate $xtpl) {

    	$model = $this->getGrid()->getGridModel();
    	
        foreach ($model->getEntities() as $item) {
            $this->renderRow( $item, $xtpl);
        }
    }

    public function renderRowsHeader( XTemplate $xtpl) {

    	$gridId = $this->getGrid()->getId();
    	$model = $this->getGrid()->getGridModel();

    	//primero tenemos que chequear si hay grupos de encabezados.
    	//vamos a agrupar por grupos.
    	//por ahora consideramos un �nico nivel de grupos.
    	$groups = array();
    	for ($index = 0; $index < $model->getColumnCount(); $index++) {

            $oColumnModel = $model->getColumnModel($index);
            
            //si tiene grupo la agregamos al arreglo del grupo.
            if( $oColumnModel->hasGroup() )
            	$groups[ $oColumnModel->getDs_group() ][] = $oColumnModel ;
            
    	}	
    	
    	//si hay grupos, los encabezados que no est�n agrupados, deben tener rowspan
    	//como vamos a considerar un �nica nivel de grupos, ser�a rowspan=2
    	$rowspan = (count( $groups ) > 0 )? 2 :1;
    	$xtpl->assign('group_levels', $rowspan);
    	
    	
    	//parseamos el primer nivel de headers.
        for ($index = 0; $index < $model->getColumnCount(); $index++) {

        	$oColumnModel = $model->getColumnModel($index);

        	
        	if( !$oColumnModel->hasGroup() ){
        	
        		$field = $oColumnModel->getDs_sqlField();
            	if (empty($field))
                	$field = $oColumnModel->getDs_field();

        		$xtpl->assign('label', $oColumnModel->getDs_label());
				$xtpl->assign('group_levels', $rowspan);
	            $xtpl->assign('orderField', $field);
	            $xtpl->assign('orderLabel', $oColumnModel->getDs_label());
	            $orderType = CdtUtils::getParam("orderType_$gridId", "DESC");
	            if ($orderType == "DESC")
	                $orderType = "ASC";
	            else
	                $orderType = "DESC";
	            $xtpl->assign('orderType', $orderType);
	
	            $xtpl->assign('actionList', "cmp_grid");
	
	            $xtpl->parse('main.TH.SIMPLE');
        		$xtpl->parse('main.TH');
        	}else{
        	
        	
        		//si es la primer columna del grupo, parseamos el header del grupo.
        		$group = $groups[ $oColumnModel->getDs_group() ];
        		$oFirstColumnModel = $group[0];
        		
        		if( $oFirstColumnModel->getDs_name() == $oColumnModel->getDs_name()  ){
        			
        			//primero mostramos el header del grupo.
        			$xtpl->assign('group_label', $oFirstColumnModel->getDs_group());
        			$xtpl->assign('group_size', count( $group ) );
        			
					$field = $oFirstColumnModel->getDs_sqlField();
            		if (empty($field))
                		$field = $oFirstColumnModel->getDs_field();

        			$xtpl->assign('orderField', $field);
		            $xtpl->assign('orderLabel', $oFirstColumnModel->getDs_label());
		            $orderType = CdtUtils::getParam("orderType_$gridId", "DESC");
		            if ($orderType == "DESC")
		                $orderType = "ASC";
		            else
		                $orderType = "DESC";
		            $xtpl->assign('orderType', $orderType);
		            
        			$xtpl->parse('main.TH.GROUP');
        			$xtpl->parse('main.TH');
        		}
        		
        	
        	}
		}
		
		//parseamos el segundo nivel de headers.
    	for ($index = 0; $index < $model->getColumnCount(); $index++) {

        	$oColumnModel = $model->getColumnModel($index);

        	
        	if( $oColumnModel->hasGroup() ){

        		//si es la primer columna del grupo, parseamos todo el grupo.
        		//sino no hacemos nada.
        		$group = $groups[ $oColumnModel->getDs_group() ];
        		$oFirstColumnModel = $group[0];
        		
        		if( $oFirstColumnModel->getDs_name() == $oColumnModel->getDs_name()  ){

        			foreach ($group as $oSubColumnModel) {
        			
						$field = $oSubColumnModel->getDs_sqlField();
            			if (empty($field))
                			$field = $oSubColumnModel->getDs_field();
        					
                		$xtpl->assign('label', $oSubColumnModel->getDs_label());
						$xtpl->assign('orderField', $field);
			            $xtpl->assign('orderLabel', $oSubColumnModel->getDs_label());
			            $orderType = CdtUtils::getParam("orderType_$gridId", "DESC");
			            if ($orderType == "DESC")
			                $orderType = "ASC";
			            else
			                $orderType = "DESC";
			            $xtpl->assign('orderType', $orderType);
			
			            $xtpl->assign('actionList', "cmp_grid");
		        			
		        			
			            $xtpl->parse('main.SUB_HEADER.TH');
		            
        			}
		            
        		}    		
        	}
		}
		
		if(count( $groups ) > 0 )
			$xtpl->parse('main.SUB_HEADER');

			
		if( $this->hasCheckbok() )
			$xtpl->parse('main.TH_CHECKBOX');
    }

    public function renderRowsFooter( XTemplate $xtpl) {

    }

    public function renderColumn( $item, $index, XTemplate $xtpl) {
    	
    	$model = $this->getGrid()->getGridModel();
    	
    	$oColumnModel = $model->getColumnModel($index);

        $value = $model->getValue($item, $index);

        $value = $oColumnModel->getFormat()->format($value, $item);

        $cssClass = $oColumnModel->getDs_cssClass();
        $cssStyle = $oColumnModel->getDs_cssStyle();

        $textAlign = $oColumnModel->getTextAlign();
        
        if(!empty($textAlign)){
        
        	switch ($textAlign) {
        		case CDT_CMP_GRID_TEXTALIGN_CENTER: $cssStyle .= ";text-align:center;";
        		;break;
        		case CDT_CMP_GRID_TEXTALIGN_LEFT: $cssStyle .= ";text-align:left;";
        		;break;
        		case CDT_CMP_GRID_TEXTALIGN_RIGHT: $cssStyle .= ";text-align:right;";
        		;break;
        		default:
        			;
        		break;
        	}
        
        }
        
        $xtpl->assign('column_class', $cssClass);
        $xtpl->assign('column_style', $cssStyle);
        $xtpl->assign('value', $value);
        
        $xtpl->parse('main.row.column');
    }
    
    public function renderRow( $item, XTemplate $xtpl) {

    	$model = $this->getGrid()->getGridModel();
    	
        $this->renderRowActions( $item, $xtpl);

        //parseamos el id.
        $xtpl->assign('itemId', $model->getEntityId($item));

        for ($index = 0; $index < $model->getColumnCount(); $index++) {

        	$this->renderColumn( $item, $index, $xtpl);
        	
        	/*
            $oColumnModel = $model->getColumnModel($index);

            $value = $model->getValue($item, $index);

            $value = $oColumnModel->getFormat()->format($value, $item);

            $xtpl->assign('value', $value);
            $xtpl->parse('main.row.column');
            */
        }

        $xtpl->assign('row_class', $model->getRowStyleClass($item));      

        if( $this->hasCheckbok() )
			$xtpl->parse('main.row.checkbox');
        
        $xtpl->parse('main.row');
    }

    public function renderRowActions( $item, XTemplate $xtpl) {

    	$model = $this->getGrid()->getGridModel();

        $actions = $model->getRowActionsModel($item);
        if (!empty($item))
            $itemId = $model->getEntityId($item);
        else
            $itemId = "";

        foreach ($actions as $oActionModel) {

            $this->renderRowAction($xtpl, $oActionModel, $itemId);
        }
    }

    protected function renderRowAction(XTemplate $xtpl, GridActionModel $oActionModel, $itemId) {

    	$gridId = $this->getGrid()->getId();
    	
        $ds_action = $oActionModel->getDs_action();

        $ds_callback = $oActionModel->getDs_callback();
        if (empty($ds_callback)) {
            if ($oActionModel->getBl_popUp())
                $ds_callback = "loadPopup_$gridId('doAction?action=$ds_action&id=$itemId' )";
            else
                $ds_callback = "loadContent('doAction?action=$ds_action&id=$itemId' )";

            $message = $oActionModel->getDs_confirmationMsg();
            if (!empty($message)) {
				$confirm= CDT_CMP_GRID_MSG_CONFIRM_DELETE_TITLE;
                $ds_callback = " confirm_action( '$message' , '$confirm',  function(){ $ds_callback } );";
            }
        }

        $ds_callback .= ";return false;";

        $xtpl->assign('onclick', $ds_callback);
        $xtpl->assign('href', "go");
        $xtpl->assign('target', "");
        $xtpl->assign('img', $oActionModel->getDs_image());
        $xtpl->assign('title', $oActionModel->getDs_label());
        $xtpl->assign('row_action_class', $oActionModel->getDs_style());
        $xtpl->parse('main.row.action');
    }

    public function renderGridHeader( XTemplate $xtpl) {
        $gridId = $this->getGrid()->getId();
    	$model = $this->getGrid()->getGridModel();
    	
    	$filterValue = CdtUtils::getParam("filterValue_$gridId", "");
        $page = CdtUtils::getParam("page_$gridId", 1);
        $orderType = CdtUtils::getParam("orderType_$gridId", "");
        $orderField = CdtUtils::getParam("orderField_$gridId", "");
        $filterField = CdtUtils::getParam("filterField_$gridId", "");

        $rows = $model->getTotalRows();
        $paginator = $this->getPaginator($rows, $orderType, $filterField, $filterValue, $orderField, $page );
        $this->parsePaginator($xtpl, $paginator);
        
    }

    public function renderGridFooter( XTemplate $xtpl ) {

    }

    /**
     * se construye el paginador.
     * @param unknown_type $num_rows n�mero de filas
     * @param unknown_type $orderType tipo de orden
     * @param unknown_type $filterField campo a filtrar
     * @param unknown_type $filterValue valor del filtro
     * @param unknown_type $orderField campo de ordenaci�n
     * @param unknown_type $page p�gina actual de la paginaci�n
     */
    protected function getPaginator($num_rows, $orderType, $filterField, $filterValue, $orderField, $page ) {

    	$gridId = $this->getGrid()->getId();
    	
        $num_pages = ceil($num_rows / ROW_PER_PAGE);

        $url = "";

        $cssclassotherpage = 'paginadorOtraPagina';
        $cssclassactualpage = 'paginadorPaginaActual';

        return new GridPaginator($url, $num_pages, $page, $cssclassotherpage, $cssclassactualpage, $num_rows, $gridId);
    }

    /**
     * se parse el paginador.
     * @param XTemplate $xtpl template donde se parsea el paginador.
     * @param Paginator $oPaginator paginador a parsear.
     */
    protected function parsePaginator(XTemplate $xtpl, CdtPaginator $oPaginator) {

        $xtpl->assign('results', $oPaginator->printResults());
        $xtpl->parse('main.paginator_results');
        $xtpl->assign('pages', $oPaginator->printPagination());
        $xtpl->parse('main.paginator_pages');
    }



	public function getGrid()
	{
	    return $this->oGrid;
	}

	public function setGrid($oGrid)
	{
	    $this->oGrid = $oGrid;
	}
	
	protected function parseMessages(XTemplate $xtpl) {
        
	    //parseamos errores.
	    if (CdtUtils::hasRequestError()) {
	
	    	
            $error = CdtUtils::getRequestError();
            
            $msg = addslashes( urldecode($error['msg']) );
            
            $cod = $error['code'];
            if( !empty($cod) )
             $msg = $cod . " - " . $msg;
            
            $xtpl->assign('error_message', $msg);
            $xtpl->parse('main.error_message');
            
            CdtUtils::cleanRequestError();
	    	
            
	    }
	
		//parseamos mensajes.
	    if (CdtUtils::hasRequestInfo()) {
	
	    	
            $info = CdtUtils::getRequestInfo();
            $msg = addslashes( urldecode($info['msg']) );
            
            $cod = $error['code'];
            if( !empty($cod) )
             $msg = $cod . " - " . $msg;
            
            $xtpl->assign('info_message', $msg);
            $xtpl->parse('main.info_message');
            
            CdtUtils::cleanRequestInfo();
            
	    }
	}

	protected function hasCheckbok(){
		return true;
	}
	
}