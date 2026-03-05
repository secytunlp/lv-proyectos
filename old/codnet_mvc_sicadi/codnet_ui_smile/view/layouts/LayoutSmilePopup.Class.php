<?php

/**
 * Representa el layout para un popup:
 * 
 * sin header ni footer.
 *  
 * @author Bernardo
 * @since 11-04-2011
 */
class LayoutSmilePopup extends LayoutPopup {

	private function getXTemplate(){
		return new XTemplate (CDT_UI_SMILE_TEMPLATE_POPUP);
	}
	
    protected function parseMetaTags($xtpl) {
        $xtpl->assign('http_equiv', 'X-UA-Compatible');
        $xtpl->assign('meta_content', 'IE=7');
        $xtpl->parse('main.meta_tag');

        $xtpl->assign('http-equiv', 'Content-Type');
        //$xtpl->assign('meta_content', 'text/html; charset=ISO-8859-1');
        $xtpl->parse('main.meta_tag');
    }

    protected function parseStyles($xtpl) {
    }

    protected function parseScripts($xtpl) {
    }

    public function setMenu($html) {
        return " ";
    }


}
