<?php

/**
 * Representa el layout para el login de CrmMicrojuris
 * 
 * @author Bernardo
 * @since 05-04-2011
 */
class LayoutSmileSignup extends LayoutSmile{


    protected function getXTemplate($menuOptions,$currentMenuGroup='') {
        return new XTemplate(CDT_UI_SMILE_TEMPLATE_SIGNUP);
    }
	
}
