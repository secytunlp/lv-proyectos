<?php

class RichGridPaginator extends GridPaginator{
	
	//
	// Constructor
	//
	function __construct($url, $numpages, $actualpage, $cssclassotherpage, $cssclassactualpage, $total_rows, $gridId, $row_per_page=null) {
		
		parent::__construct($url, $numpages, $actualpage, $cssclassotherpage, $cssclassactualpage, $total_rows, $gridId, $row_per_page=null);
		
	}
	
	protected function getXTemplate(){
		return new XTemplate( CDT_CMP_TEMPLATE_RICH_PAGINATOR );
	}
	//
	// Other base methods
	//
	function printPagination() {
		
		
		$xtpl = $this->getXTemplate();
		
		$xtpl->assign( "gridId", $this->getGridId() );
		$xtpl->assign( "lbl_pages", CDT_CMP_GRID_LBL_PAGINATOR_PAGES );
		$xtpl->assign( "lbl_previous", CDT_CMP_GRID_LBL_PAGINATOR_PREVIOUS );
		$xtpl->assign( "lbl_next", CDT_CMP_GRID_LBL_PAGINATOR_NEXT );
		$xtpl->assign( "lbl_firstPage", CDT_CMP_GRID_LBL_PAGINATOR_FIRST_PAGE );
		$xtpl->assign( "lbl_lastPage", CDT_CMP_GRID_LBL_PAGINATOR_LAST_PAGE );
		
		
		if ($this->getNumPages () > 1) {
			
			if (($this->getActualPage ()) > 1) {
				$ds_pag_anterior = "&lt;&lt; " . CDT_UI_LBL_PAGINATOR_PREVIOUS;
				$ant_page = ($this->getActualPage ()) - 1;

				$xtpl->assign( "previousPage", $ant_page  );
				$xtpl->parse( "main.previous_page" );
				
				$xtpl->assign( "firstPage", 1 );
				$xtpl->parse( "main.menu_pages.first_page" );
				
			}else{
				$xtpl->parse( "main.previous_page_disabled");
				$xtpl->parse( "main.menu_pages.first_page_disabled" );
			}
			
			if (($this->getInitPage ()) > 1) {
				$ds_pags_anteriores = "[.....]";
				$ant_pages = $this->getInitPage () - 1;
			}
			

			if ($this->getNumPages () > $this->getEndPage ()) {
				$ds_pags_siguientes = "[.....]";
				$sig_pages = $this->getEndPage () + 1;
			}
			
			if (($this->getActualPage ()) < ($this->getNumPages ())) {
				$ds_pag_siguiente =  CDT_UI_LBL_PAGINATOR_NEXT . " &gt;&gt;";
				$sig_page = ($this->getActualPage ()) + 1;
				
				$xtpl->assign( "nextPage", $sig_page );
				$xtpl->parse( "main.next_page" );		
				
				$xtpl->assign( "lastPage", $this->getNumPages () );
				$xtpl->parse( "main.menu_pages.last_page" );
			}else{
				$xtpl->parse( "main.next_page_disabled");
				$xtpl->parse( "main.menu_pages.last_page_disabled" );
			}
		
			$xtpl->parse( "main.menu_pages" );
		}
		
		if ($this->getNumPages () >= 1) {
		
			$limitInf = (($this->getActualPage ()-1)*($this->getNumRowsPerPage()))+1;
			$limitSup = ((($limitInf-1)+$this->getNumRowsPerPage())<$this->getTotalRows())?($limitInf-1)+$this->getNumRowsPerPage():$this->getTotalRows(); 
			
			$xtpl->assign( "rangeFrom", $limitInf );
			$xtpl->assign( "rangeTo", $limitSup );
			$xtpl->assign( "totalRows", $this->getTotalRows() );
		}else{
			$xtpl->assign( "rangeFrom", 0 );
			$xtpl->assign( "rangeTo", 0 );
			$xtpl->assign( "totalRows", 0 );
			
		}
		
		$xtpl->parse("main");
		
		return $xtpl->text("main");
	}

} // class paginador
?>