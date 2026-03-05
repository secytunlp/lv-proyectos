<?php

/**
 * Encargado de renderizar la grilla en xls 
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 23-12-2011
 *
 */
class GridXlsRenderer implements IGridRenderer{

	private $oGrid;
	
	public function render( CMPGrid $oGrid  ){

		//seteamos la grilla a renderizar.
		$this->setGrid( $oGrid );
		
		$xtpl = $this->getXTemplate();

		$model = $oGrid->getGridModel();
		
		
		$this->renderResults( $model, $oGrid, $xtpl );
			
		$xtpl->assign("title", $model->getTitle() );
		
		$xtpl->parse( 'main' );
		$content = $xtpl->text( 'main' );

		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		
		header("Content-Disposition: attachment; filename=" . $model->getExportTitle() . ".xls");
		CdtUIUtils::setCharset("application/vnd.ms-excel");
		
		echo CdtUIUtils::encodeCharactersXls($content);
		
	}
	
	protected function getCdtSearchCriteria( CMPGrid $oGrid, GridModel $oGridModel ){
		
		$oCriteria = $oGrid->getCriteria();
		$oCriteria->setPage( null );
		$oCriteria->setRowPerPage( null );
		return $oCriteria;
		
	}
	public function renderResults( IGridModel  $model, CMPGrid $oGrid, XTemplate $xtpl ){
		
			$gridId = $oGrid->getId();
			
			$xtpl->assign( 'gridId', $gridId );
			
			$this->renderRowsHeader( $model, $gridId, $xtpl );
			
			$entities = $model->getEntityManager()->getEntities ( $this->getCdtSearchCriteria($oGrid, $model) );
			
			$model->setEntities( $entities );
			
			$this->renderRows( $model, $gridId, $xtpl );
			
			$this->renderRowsFooter( $model, $xtpl );

	}

	public function renderRowsHeader( IGridModel  $model, $gridId, XTemplate $xtpl ){
		
		for( $index=0; $index<$model->getColumnCount() ;$index++ ){
		
			$oColumnModel = $model->getColumnModel( $index );
			
			$xtpl->assign('label', $oColumnModel->getDs_label() );
			
			$xtpl->parse('main.TH' );
			
		}
		
	}
	
	public function renderRows( IGridModel  $model, $gridId, XTemplate $xtpl ){

		foreach ($model->getEntities() as $item) {
			$this->renderRow( $model,  $gridId, $item, $xtpl );
		} 
		
	}
	
	
	public function renderRowsFooter( IGridModel  $model, XTemplate $xtpl ){
		
	}

	public function renderColumn( $item, $index, XTemplate $xtpl) {
    	
    	$model = $this->getGrid()->getGridModel();
    	
    	$oColumnModel = $model->getColumnModel($index);

        $value = $model->getValue($item, $index);

        //$value = $oColumnModel->getFormat()->format($value, $item);
        $value = $oColumnModel->getFormat()->format($value);

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
        //$cssStyle = "mso-number-format:'0.00'";
        $xtpl->assign('column_class', $cssClass);
        $xtpl->assign('column_style', $cssStyle);
        $xtpl->assign('value', $value);
        
        $xtpl->parse('main.row.column');
    }
    
	public function renderRow( IGridModel  $model,  $gridId, $item, XTemplate $xtpl ){
		
		//parseamos el id.
		$xtpl->assign ( 'itemId', $model->getEntityId($item) );
		
		for( $index=0; $index<$model->getColumnCount() ;$index++ ){
		
			/*
			$oColumnModel = $model->getColumnModel( $index );
		
			$value = $model->getValue( $item, $index );
			
			$value = $oColumnModel->getFormat()->format( $value );
			
			$xtpl->assign ( 'value', $value );
			$xtpl->parse('main.row.column' );*/
			
			$this->renderColumn( $item, $index, $xtpl);
			
		}
			
		$xtpl->parse('main.row');
				
	}
	
	protected function getXTemplate(){
		return new XTemplate( CDT_CMP_TEMPLATE_GRID_XLS_RESULTS );
	}
	

	public function getGrid()
	{
	    return $this->oGrid;
	}

	public function setGrid($oGrid)
	{
	    $this->oGrid = $oGrid;
	}
}