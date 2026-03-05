<?php

/**
 * Encargado de renderizar la grilla 
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 14-12-2011
 *
 */
class RichGridRenderer extends GridRenderer {

	public function renderStyle(CMPGrid $oGrid, XTemplate $xtpl){
		parent::renderStyle($oGrid, $xtpl);
		$xtpl->assign('rich_grid_style', CDT_CMP_GRID_RICH_STYLE_CSS );
	}
	
    public function renderLabels(XTemplate $xtpl) {

        $xtpl->assign('msgSelectOnlyOneItem', CDT_CMP_GRID_MSG_SELECT_ONLY_ONE_ITEM);
        $xtpl->assign('lbl_clear', CDT_CMP_GRID_LBL_CLEAR);
        parent::renderLabels($xtpl);
    }

    protected function getResultsXTemplate() {
        return new XTemplate(CDT_CMP_TEMPLATE_RICH_GRID_RESULTS);
    }

    protected function getXTemplate() {

        if (CdtUtils::getParam("search"))
            return $this->getResultsXTemplate();
        else
            return new XTemplate(CDT_CMP_TEMPLATE_RICH_GRID);
    }

    public function renderFilters(XTemplate $xtpl) {
        $gridId = $this->getGrid()->getId();
        $model = $this->getGrid()->getGridModel();

        //recuperamos el filtro por default.
        $defaultFilterFieldName = $model->getDefaultFilterField();

        $xtpl->assign('msg_invalid_number', CDT_CMP_GRID_MSG_INVALID_NUMBER );
        $xtpl->assign('lbl_default_search_by', CDT_CMP_GRID_MSG_DEFAULT_SEARCH_BY );
        $xtpl->assign('lbl_result_search', CDT_CMP_GRID_MSG_RESULT_SEARCH );
        
        if ($model->getFiltersCount() > 0) {
            for ($index = 0; $index < $model->getFiltersCount(); $index++) {

                $oFilterModel = $model->getFilterModel($index);

                if (!$oFilterModel->getBl_hidden()) {

                    $name = $oFilterModel->getDs_name();
                    $id = $oFilterModel->getDs_id();
                    if( empty($id) )
                    	$id = $name;
                    $type = $oFilterModel->getType();
                    $pattern = $oFilterModel->getFormat()->getPattern();

                    if ($type == CDT_CMP_GRID_FILTER_TYPE_DATE || $type == CDT_CMP_GRID_FILTER_TYPE_DATETIME || $type == CDT_CMP_GRID_FILTER_TYPE_TIME)
                        $pattern = CdtDateUtils::phpToJQueryPattern($pattern);

                        
                    //chequeamos si es el filtro por default.
                    if ($name == $defaultFilterFieldName) {
                    	
                        $xtpl->assign('defaultFilterLabel', $oFilterModel->getDs_label());
                        $xtpl->assign('filterValue', CdtUtils::getParam("filterField_" . $gridId, ""));
                        $xtpl->assign('pattern', $pattern);
                        $xtpl->parse('main.table_buttons.filters.default_search');
                        $xtpl->parse('main.table_buttons.filters.default_' . $type);
                    }


                    $xtpl->assign('filterFieldId', $id);
                    $xtpl->assign('filterFieldName', $name);
                    $xtpl->assign('filterLabel', $oFilterModel->getDs_label());
                    $xtpl->assign('filterValue', CdtUtils::getParam($name . "_" . $gridId, ""));


                    //vemos si tiene opciones para mostrar (puede ser combo, checkbox, radio...)
                    if ($oFilterModel->hasOptions()) {

                        foreach ($oFilterModel->getOptions() as $optionValue => $optionLabel) {
                            $xtpl->assign('optionLabel', $optionLabel);
                            $xtpl->assign('optionValue', $optionValue);
                            $xtpl->assign('optionValueSelected', CdtFormatUtils::selected($optionValue, $filterValue));
                            $xtpl->parse('main.table_buttons.filters.menu_filters.filter.type_' . $type . '.filter_option');
                        }
                    }


                    //parseamos de acuerdo al tipo del filtro.
                    $xtpl->assign('pattern', $pattern);
                    $xtpl->parse('main.table_buttons.filters.menu_filters.filter.type_' . $type);

                    $xtpl->parse('main.table_buttons.filters.menu_filters.filter');



                    //$xtpl->parse ( 'main.clear_filter.filter_option' );
                }
            }
            $xtpl->parse('main.table_buttons.filters.filters_combobox');
            $xtpl->parse('main.table_buttons.filters.menu_filters');


            $xtpl->parse("main.clear_filter");
            $this->renderCustomFilters($xtpl);

            $xtpl->parse('main.table_buttons.filters');
            $xtpl->parse('main.table_buttons');
        }
    }

    public function renderRows(XTemplate $xtpl) {

        $model = $this->getGrid()->getGridModel();

        $this->renderRowActions("", $xtpl);

        foreach ($model->getEntities() as $item) {
            $this->renderRow($item, $xtpl);
        }
    }

    public function renderRow($item, XTemplate $xtpl) {

        //$this->renderRowActions( $model, $item, $xtpl );

        $model = $this->getGrid()->getGridModel();

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
            
        	
            $oColumnModel = $model->getColumnModel($index);

            $value = $model->getValue($item, $index);

            $value = $oColumnModel->getFormat()->format($value, $item);

            $xtpl->assign('value', $value);
            $xtpl->parse('main.row.column');*/
        }

        
        $xtpl->assign('row_class', $model->getRowStyleClass($item));
        if( $this->hasCheckbok() )
			$xtpl->parse('main.row.checkbox');
        $xtpl->parse('main.row');
    }

    protected function renderRowAction(XTemplate $xtpl, GridActionModel $oActionModel, $itemId) {

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
            $ds_callback = "loadPopup_$gridId('doAction?action=$ds_action&id=' +  $getSelected, '$title', $height, $width )";
        } else {

            $ds_callback = $oActionModel->getDs_callback();
            
            if (empty($ds_callback)){
            	if (!$bl_targetblank){	
                	$ds_callback = "loadContent('doAction?action=$ds_action&id=' + $getSelected )";
            	}
            	else{	
                	$ds_callback = "loadTargetBlank('doAction?action=$ds_action&id=' + $getSelected )";
            	}
            }else{
            	$ds_callback .= "( $getSelected )";
            }
        }
        $message = $oActionModel->getDs_confirmationMsg();
        if (!empty($message)) {

            $confirm= CDT_CMP_GRID_MSG_CONFIRM_DELETE_TITLE;
        	$ds_callback = " confirm_action( '$message' , '$confirm',  function(){ $ds_callback } );";
        }

        if (!$oActionModel->getBl_multiple()) {
            $ds_callback = " checkOnlyOneSelected_$gridId( function(){ $ds_callback } );  ";
        }

        $xtpl->assign('divClass', $oActionModel->getDs_style());
        $xtpl->assign('functionCallback', $ds_callback);
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
    protected function getPaginator($num_rows, $orderType, $filterField, $filterValue, $orderField, $page) {

        $gridId = $this->getGrid()->getId();

        $num_pages = ceil($num_rows / ROW_PER_PAGE);

        $url = "";

        $cssclassotherpage = 'paginadorOtraPagina';
        $cssclassactualpage = 'paginadorPaginaActual';

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

}