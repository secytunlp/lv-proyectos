<?php

/**
 * Encargado de renderizar la grilla 
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 22-03-2013
 *
 */
class EntityGridRenderer implements IEntityGridRenderer {

	/**
	 * grilla a renderizar.
	 * @var CMPEntityGrid
	 */
	private $grid;
	
	
	/**
	 * (non-PHPdoc)
	 * @see components/entitygrid/render/IEntityGridRenderer::render()
	 */
	public function render(CMPEntityGrid $grid){
	
		
		$this->setGrid($grid);		
		
		//template donde renderizar la grilla.
		$xtpl = $this->getXTemplate();

		$xtpl->assign('grid_style', CDT_CMP_GRID_STYLE_CSS );
		$xtpl->assign('rich_grid_style', CDT_CMP_GRID_RICH_STYLE_CSS );
		
		$xtpl->assign("filterId", $grid->getFilter()->getId());
		$xtpl->assign('gridId', $this->getGrid()->getId() );
		
		//renderizamos el filter.
		$filter = $grid->getFilter()->show();
		
		$xtpl->assign("cmp_filter", $filter);

	    //$this->renderStyle($grid, $xtpl);
    
	    $this->renderLabels( $xtpl);
	    	    
		$this->renderActions( $xtpl);
		
		//renderizamos los resultados.		
		$results = $this->renderResults($grid);
		$xtpl->assign("search_first_time", $results);
		
		$xtpl->assign("title",  $grid->getModel()->getTitle());
		
		$xtpl->parse("main");
		return $xtpl->text("main");
		
	}
	
	public function renderLabels(XTemplate $xtpl) {

        $xtpl->assign('msgSelectOnlyOneItem', CDT_CMP_GRID_MSG_SELECT_ONLY_ONE_ITEM);
        $xtpl->assign('lbl_clear', CDT_CMP_GRID_LBL_CLEAR);
    }
	
	/**
	 * (non-PHPdoc)
	 * @see components/entitygrid/render/IEntityGridRenderer::renderResults()
	 */
	public function renderResults(CMPEntityGrid $grid){
		
		
		$this->setGrid($grid);
	
		$xtpl = $this->getResultsXTemplate();

		//$this->renderLabels( $xtpl_result);
		$this->renderActions( $xtpl);
		$this->renderHeader( $xtpl );
		
		$xtpl->assign('gridId', $this->getGrid()->getId() );
		$xtpl->assign("filterId", $grid->getFilter()->getId());
		
		$xtpl->assign('msgSelectOnlyOneItem', CDT_CMP_GRID_MSG_SELECT_ONLY_ONE_ITEM);
		
        $this->renderGridHeader( $xtpl);

        $this->renderRowsHeader( $xtpl);

        $this->renderRows( $xtpl);

        $this->renderRowsFooter( $xtpl);

        $this->renderGridFooter( $xtpl);
        
        $this->parseMessages( $xtpl );
        
		$this->renderFooter( $xtpl );
		
		$xtpl->parse("main");
        return $xtpl->text("main");
		
	}
	
	public function renderStyle(CMPEntityGrid $grid, XTemplate $xtpl){
	
		$xtpl->assign('grid_style', CDT_CMP_GRID_STYLE_CSS );
	}
	
	public function renderHeader( XTemplate $xtpl) {
    	
    	$header = $this->getGrid()->getModel()->getHeaderContent();
    	
    	if(!empty ($header)){
    		$xtpl->assign('header', $header );
    		$xtpl->parse('main.header');
    	}
    	
    	
    }
    
	public function renderFooter( XTemplate $xtpl) {
    	
    	$footer = $this->getGrid()->getModel()->getFooterContent();
    	
    	if(!empty ($footer)){
    		$xtpl->assign('footer', $footer );
    		$xtpl->parse('main.footer');
    	}
    	
    	
    }
	
    protected function getResultsXTemplate() {
        return new XTemplate(CDT_CMP_TEMPLATE_ENTITYGRID_RESULTS);
    }

    protected function getXTemplate() {

		$xtemplate = '';
        if (CdtUtils::getParam("search"))
            $xtemplate = $this->getResultsXTemplate();
        else{
			try {
				//CYTSecureUtils::logObject($_SESSION["sicadi"]);
				$xtemplate = new XTemplate(CDT_CMP_TEMPLATE_ENTITYGRID);
				
			} catch (Exception $e) {
				echo 'Excepci�n capturada: ',  $e->getMessage(), "\n";
			}
		}
     return $xtemplate;
    }

	public function renderActions( XTemplate $xtpl) {
        
    	$model = $this->getGrid()->getModel();
    	
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

	public function renderRowActions( $item, XTemplate $xtpl) {

    	$model = $this->getGrid()->getModel();

        $actions = $model->getRowActionsModel($item);
        if (!empty($item))
            $itemId = $model->getEntityId($item);
        else
            $itemId = "";

        foreach ($actions as $oActionModel) {

            $this->renderRowAction($xtpl, $oActionModel, $itemId);
        }
    }

	public function renderRows(XTemplate $xtpl) {

        $model = $this->getGrid()->getModel();

        $this->renderRowActions("", $xtpl);

        foreach ($model->getEntities() as $item) {
            $this->renderRow($item, $xtpl);
        }
    }

    public function renderRow($item, XTemplate $xtpl) {

        //$this->renderRowActions( $model, $item, $xtpl );

        $model = $this->getGrid()->getModel();

        //parseamos el id.
        $xtpl->assign('itemId', $model->getEntityId($item));

        for ($index = 0; $index < $model->getColumnCount(); $index++) {

        	$this->renderColumn( $item, $index, $xtpl);
        	
        }

        
        $xtpl->assign('row_class', $model->getRowStyleClass($item));
        if( $this->hasCheckbok() )
			$xtpl->parse('main.row.checkbox');
        $xtpl->parse('main.row');
    }

    protected function renderRowAction(XTemplate $xtpl, GridActionModel $oActionModel, $itemId) {
		$callback_action = '';	
		$callback_url = '';	
		$callback_confirm='';
        $gridId = $this->getGrid()->getId();

        $ds_action = $oActionModel->getDs_action();
        $bl_targetblank = $oActionModel->getBl_targetblank();
        $bl_allPageItems = $oActionModel->getBl_allPageItems();

        $getSelected = "";
        if( $bl_allPageItems )
        	$getSelected = "getAllPageItemsSelected_$gridId()";
        else
        	$getSelected = "getSelected_$gridId()";
        
        if ($oActionModel->getBl_popUp()) {
            $title = $oActionModel->getDs_label();
            $height = $oActionModel->getNu_heightPopup();
            $width = $oActionModel->getNu_widthPopup();
			$callback_action = "loadPopup_".$gridId;	
			$callback_url = 'doAction?action='.$ds_action.'&id=';	
            $ds_callback = "loadPopup_$gridId('doAction?action=$ds_action&id=' +  $getSelected, '$title', $height, $width )";
        } else {

            $ds_callback = $oActionModel->getDs_callback();
            if (empty($ds_callback)){
            	if (!$bl_targetblank){	
					$callback_action = "loadContent";	
					$callback_url = 'doAction?action='.$ds_action.'&id=';	
                	$ds_callback = "loadContent('doAction?action=$ds_action&id=' + $getSelected )";
            	}
            	else{	
					$callback_action = "loadTargetBlank";	
					$callback_url = 'doAction?action='.$ds_action.'&id=';	
                	$ds_callback = "loadTargetBlank('doAction?action=$ds_action&id=' + $getSelected )";
            	}
            }else{
            	$ds_callback .= "( $getSelected )";
            }
        }
		
        $message = $oActionModel->getDs_confirmationMsg();
        if (!empty($message)) {
			$callback_action = 'confirm_action';	
			$callback_url = $ds_callback;	
			
            $confirm= CDT_CMP_GRID_MSG_CONFIRM_DELETE_TITLE;
			$callback_confirm=$message.'|'.$confirm;
        	$ds_callback = " confirm_action( '$message' , '$confirm',  function(){ $ds_callback } );";
        }

        if (!$oActionModel->getBl_multiple()) {
            $ds_callback = " checkOnlyOneSelected_$gridId( function(){ $ds_callback } );  ";
        }

        $xtpl->assign('divClass', $oActionModel->getDs_style());
        $xtpl->assign('getSelected', $getSelected);
		$xtpl->assign('functionCallback', $ds_callback);
		$xtpl->assign('callback_action', $callback_action);
		$xtpl->assign('callback_confirm', $callback_confirm);
		$xtpl->assign('callback_url', $callback_url);
        $xtpl->assign('title', $oActionModel->getDs_label());

        $action = explode(" ", $oActionModel->getDs_label());
        $xtpl->assign('label', $action[0]);
        $xtpl->parse('main.barra_opcion.label_opcion');

        $xtpl->parse('main.barra_opcion');
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
    protected function getPaginator($num_rows, $orderType, $filterField, $filterValue, $orderField, $page, $rowPerPage=ROW_PER_PAGE) {
    					   
        $gridId = $this->getGrid()->getId();

        if(empty($rowPerPage))
        	$rowPerPage = ROW_PER_PAGE;
        $num_pages = ceil($num_rows / $rowPerPage);

        if(empty($page))
        	$page = 1;
        
        $url = "";

        $cssclassotherpage = 'paginadorOtraPagina';
        $cssclassactualpage = 'paginadorPaginaActual';

        $log = "num_rows: $num_rows, orderType: $orderType, filterField: $filterField, filterValue: $filterValue, orderField: $orderField, page: $page, rowPerPage: $rowPerPage";
        $log .= " num_pages: $num_pages";
        //CdtUtils::log( $log, __CLASS__, LoggerLevel::getLevelInfo());
        
        return new RichGridPaginator($url, $num_pages, $page, $cssclassotherpage, $cssclassactualpage, $num_rows, $gridId);
    }

    /**
     * se parse el paginador.
     * @param XTemplate $xtpl template donde se parsea el paginador.
     * @param Paginator $oPaginator paginador a parsear.
     */
    protected function parsePaginator(XTemplate $xtpl, CdtPaginator $oPaginator) {
        $xtpl->assign('pagination', $oPaginator->printPagination());
        $xtpl->parse('main.pagination');
    }


	public function getGrid()
	{
	    return $this->grid;
	}

	public function setGrid($grid)
	{
	    $this->grid = $grid;
	}
	
	public function renderGridHeader( XTemplate $xtpl) {
        $gridId = $this->getGrid()->getId();
    	$model = $this->getGrid()->getModel();
    	$filter = $this->getGrid()->getFilter();
    	
        $page = $filter->getPage();
        $orderType = $filter->getOrderType();
        $orderField = $filter->getOrderBy();
		$rowPerPage = $filter->getRowPerPage();

        $rows = $model->getTotalRows();
        $paginator = $this->getPaginator($rows, $orderType, "", "", $orderField, $page, $rowPerPage );
		//$paginator = $this->getPaginator(10, "ASC", "", "", "oid", 1);
        $this->parsePaginator($xtpl, $paginator);
        
    }

    public function renderGridFooter( XTemplate $xtpl ) {

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
	    
	public function renderRowsHeader( XTemplate $xtpl) {

    	$gridId = $this->getGrid()->getId();
    	$model = $this->getGrid()->getModel();

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
	            //$orderType = CdtUtils::getParam("orderType_$gridId", "DESC");
	            $orderType = $this->getGrid()->getFilter()->getOrderType();
	            
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
	            	$orderType = $this->getGrid()->getFilter()->getOrderType();
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
			            $orderType = $this->getGrid()->getFilter()->getOrderType();
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
    	
    	$model = $this->getGrid()->getModel();
    	
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
}