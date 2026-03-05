<?php

/**
 * Layout para gestión
 *
 * @author Marcos
 * @since
 */
class LayoutCYT extends LayoutSmile {

	protected function parseMetaTags($xtpl) {}

	protected function initStyles(){
		parent::initStyles();

		$this->addStyle( WEB_PATH . "css/entitygrid/entitygrid.css");
		$this->addStyle( WEB_PATH . "css/filter/filter.css");
		//$this->addStyle( WEB_PATH . "css/form/form.css");
		
		//$this->addStyle( WEB_PATH . "css/fancybox/jquery.fancybox.css?v=2.1.4" );
        $this->addStyle("https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css");
		//$this->addStyle( WEB_PATH . "css/viajes.css");
        $this->addStyle( WEB_PATH . "css/styles.css");

	}
	 
	protected function initScripts(){
		parent::initScripts();
		 
		$this->addScript( WEB_PATH . "js/form/form.js" );
		$this->addScript( WEB_PATH . "js/form/formAjax.js" );
		$this->addScript( WEB_PATH . "js/jquery/jquery.form.js");
		$this->addScript( WEB_PATH . "js/jquery/jquery.tablednd.0.7.min.js");
		$this->addScript(WEB_PATH . "js/jquery/jquery.tablescroll.js");
        $this->addScript("https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js");
		$this->addScript(WEB_PATH . "js/viajes.js");
		$this->addScript(WEB_PATH . "js/soft.js");
		//$this->addScript(WEB_PATH . "js/jquery/jquery-1.9.1.js");
		//$this->addScript(WEB_PATH . "js/jquery/jquery-ui-1.10.2.js");
		$this->addScript( WEB_PATH . "js/jquery/jquery.mousewheel-3.0.6.pack.js" );
    	//$this->addScript(WEB_PATH . "js/fancybox/jquery.fancybox.js?v=2.1.4");

	}
	
	protected function getFooter() {
        $xtpl = new XTemplate(CYT_UI_SMILE_TEMPLATE_FOOTER);
        $xtpl->parse('main');
        return $xtpl->text('main');
    }


}
