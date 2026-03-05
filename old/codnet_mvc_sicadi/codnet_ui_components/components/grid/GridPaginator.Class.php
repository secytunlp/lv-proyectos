<?php

class GridPaginator extends CdtPaginator{
	
	private $gridId;
	
	//
	// Constructor
	//
	function __construct($url, $numpages, $actualpage, $cssclassotherpage, $cssclassactualpage, $total_rows, $gridId, $row_per_page=null) {
		
		parent::__construct($url, $numpages, $actualpage, $cssclassotherpage, $cssclassactualpage, $total_rows, $row_per_page=null);
		
		$this->setGridId( $gridId );
	}
	
	//
	// Other base methods
	//
	function printPagination() {
		$html = "";
		if ($this->getNumPages () > 1) {
			$html .= "<span class=\"tituloPaginador\">" . CDT_MVC_LBL_PAGINATOR_PAGES   .  ":&nbsp;&nbsp;</span>";
			
			if (($this->getActualPage ()) > 1) {
				$ds_pag_anterior = "&lt;&lt; " . CDT_MVC_LBL_PAGINATOR_PREVIOUS;
				$ant_page = ($this->getActualPage ()) - 1;
				$html .= "<a class=\"" . $this->getCssClassOtherPage () . "\" href=\"go\" onclick=\"javascript: paginate_ajax_" . $this->getGridId()    .  "($ant_page);return false; \">$ds_pag_anterior</a>&nbsp;&nbsp;";
			}
			
			if (($this->getInitPage ()) > 1) {
				$ds_pags_anteriores = "[.....]";
				$ant_pages = $this->getInitPage () - 1;
				$html .= "<a class=\"" . $this->getCssClassOtherPage ()  . "\" href=\"go\" onclick=\"javascript: paginate_ajax_" . $this->getGridId()    .  "($ant_pages);return false; \">$ds_pags_anteriores</a>&nbsp;&nbsp;";
			}
			
			for($i = $this->getInitPage (); $i <= ($this->getEndPage ()); $i ++) {
				if ($i != ($this->getActualPage ())) {
					$html .= "<a class=\"" . $this->getCssClassOtherPage ()  . "\" href=\"go\" onclick=\"javascript: paginate_ajax_" . $this->getGridId()    .  "($i);return false; \">$i</a> ";
				} else {
					$html .= "<span class=\"" . $this->getCssClassActualPage () . "\">$i</span> ";
				}
			} //final del for
			

			if ($this->getNumPages () > $this->getEndPage ()) {
				$ds_pags_siguientes = "[.....]";
				$sig_pages = $this->getEndPage () + 1;
				$html .= "&nbsp;<a class=\"" . $this->getCssClassOtherPage ()  . "\" href=\"go\" onclick=\"javascript: paginate_ajax_" . $this->getGridId()    .  "($sif_pages);return false; \">$ds_pags_siguientes</a>";
			}
			
			if (($this->getActualPage ()) < ($this->getNumPages ())) {
				$ds_pag_siguiente =  CDT_MVC_LBL_PAGINATOR_NEXT . " &gt;&gt;";
				$sig_page = ($this->getActualPage ()) + 1;
				$html .= "&nbsp;&nbsp;<a class=\"" . $this->getCssClassOtherPage ()  . "\" href=\"go\" onclick=\"javascript: paginate_ajax_" . $this->getGridId()    .  "($sig_page);return false; \">$ds_pag_siguiente</a>";
			}
		}
		return $html;
	}


	public function getGridId()
	{
	    return $this->gridId;
	}

	public function setGridId($gridId)
	{
	    $this->gridId = $gridId;
	}
} // class paginador
?>