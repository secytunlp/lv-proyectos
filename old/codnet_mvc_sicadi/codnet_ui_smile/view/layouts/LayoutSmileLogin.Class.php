<?php

/**
 * Representa el layout para el login de CrmMicrojuris
 * 
 * @author Bernardo
 * @since 05-04-2011
 */
class LayoutSmileLogin extends LayoutSmile{


	protected function parseStyles($xtpl) {
 		
 		parent::parseStyles($xtpl);
 		
        $xtpl->assign('css', WEB_PATH . "css/smile/login-box.css");
        $xtpl->parse('main.estilo');
    }
    
    protected function getXTemplate($menuOptions, $currentMenuGroup='') {
        return new XTemplate(CDT_UI_SMILE_TEMPLATE_LOGIN);
    }
	
}
