<?php

/**
 * Representa un layout
 *
 *
 * Este layout debe mostrar men�es, una superior (Solapas) y uno lateral.
 *
 * vamos a agregar un archivo de configuraci�n donde indicamos para cada acci�n:
 *  - las solapas a mostrar en el men� superior (menugroups)
 *  - la solapa que debe estar activa (menugroup)
 *  - las opciones a mostrar en el men� lateral (menuoptions)
 *
 * (usamos navigation.xml)
 *
 * @author bernardo
 * @since 03-02-2011
 */
class LayoutSmile extends CdtLayout {

    //para segurizar los men�es.
    protected $oUsuario;
    protected $oMenu;

	private $scripts = array();
	private $styles = array();
	
	
    public function show() {
 
    	$this->initScripts();
    	$this->initStyles();
    	
        //buscamos si la acci�n ejecutada tiene una configuraci�n especial
        //para los men�es.
        $currentMenuGroup = '';
        $menuGroups = '';
        $menuOptions = '';

        $navegacion = CdtLoadNavigation::getInstance();
        $accionActual = CdtUtils::getCurrentAction();
        $accion = $navegacion->getActionByName($accionActual);

        if (!empty($accion)) {
            if (array_key_exists('currentMenuGroup', $accion))
                $currentMenuGroup = $accion['currentMenuGroup'];

            if (array_key_exists('menuGroups', $accion))
                $menuGroups = $accion['menuGroups'];

            if (array_key_exists('menuOptions', $accion))
                $menuOptions = $accion['menuOptions'];
        }

        //seteamos el usuario para chequear permisos sobre los men�es.

        if (CdtSecureUtils::isUserLogged())
            $this->oUsuario = CdtSecureUtils::getUserLogged();
        else
            $this->oUsuario = new CdtUser();


        //inicializamos el men�.(TODO dado el usuario?)
        $oManager = new CdtMenuGroupManager();
        $oCriteria = new CdtSearchCriteria();
        $oCriteria->addOrder("nu_order");
        $groups = $oManager->getCdtMenuGroupsWithOptions($oCriteria);
        $oMenu = new CdtMenu();
        $oMenu->setGroups($groups);

        $this->setMenu($oMenu);
        /*
          $oUsuario->setCd_usuario( $_SESSION ["cd_usuarioSession"] );
          $oUsuario->setFunciones( $_SESSION ["funciones"] );
          $oUsuario->setDs_nomusuario( $_SESSION ["ds_usuario"] );
          $this->oUsuario = $oUsuario;
         */

        $xtpl = $this->getXTemplate($menuOptions, $currentMenuGroup);

        $xtpl->assign('titulo', $this->getTitle());
        $xtpl->assign('header', $this->getHeader($menuOptions, $currentMenuGroup));
        /*$xtpl->assign('user', $this->oUsuario->getDs_username());
        $xtpl->assign('exit', CDT_UI_SMILE_MSG_EXIT);*/


        $xtpl->assign('content',  $this->getContent());
        $xtpl->assign('footer', $this->getFooter());
        $this->parseMetaTags($xtpl);
        $this->parseStyles($xtpl);
        $this->parseScripts($xtpl);

        $this->parseException($xtpl);
        $this->parseMensajes($xtpl);

        //seteamos los men�es.
        $this->parseMenuSolapas($xtpl, $menuGroups, $currentMenuGroup);
        //$this->parseMenuSuperiorDerecho($xtpl);
        //$this->parseMenuLateral($xtpl, $menuOptions, $currentMenuGroup);
        /*
          if( !empty($currentMenuGroup) )
          $this->parseMenuSolapas($xtpl, $menuGroups, $currentMenuGroup);

          $this->parseMenuSuperiorDerecho($xtpl);
          $this->parseMenuLateral($xtpl, $menuOptions, $currentMenuGroup);
         */
        $xtpl->parse('main');

        $texto =  $xtpl->text('main');
        
        return $texto;
    }

    protected function getXTemplate($menuOptions,$currentMenuGroup='') {

        //si no tiene menugroupactivo o se indic� que no hay opciones, entonces el template es sin men� lateral.
        if (empty($currentMenuGroup) || (!empty($menuOptions) && ($menuOptions == 'false') ))
            return new XTemplate(CDT_UI_SMILE_TEMPLATE_DEFAULT);
        else
            return new XTemplate(CDT_UI_SMILE_TEMPLATE_MENU);
    }

    /**
     * parsea el menu lateral.
     * @param unknown_type $xtpl
     * @return unknown_type
     */
    protected function parseMenuLateralAction($action, $xtpl) {

        $action->parseMenuLateral($this, $xtpl);
    }

    /**
     * parsea el menu lateral.
     * @param unknown_type $xtpl
     * @return unknown_type
     */
    protected function parseMenuLateral($xtpl, $menuOptions, $currentMenuGroup) {

        if (empty($menuOptions)) {

            $this->parseMenuLateralDeMenuGroupActivo($xtpl, $currentMenuGroup);
        } else {

            //obtenemos la lista de menuoptions (id's).
            $menuoptionsId = explode(",", $menuOptions);

            $menuoptions = $this->oMenu->getMenuOptionsById($menuoptionsId);

            $count = 0;
            foreach ($menuoptions as $key => $option) {

                $count += $this->parseMenuOption($xtpl, $option);
            }
            if ($count > 0)
                $xtpl->parse('main.dock');
        }
    }

    /**
     * parsea el menu lateral.
     * @param unknown_type $xtpl
     * @return unknown_type
     */
    protected function parseMenuLateralDeMenuGroupActivo($xtpl, $currentMenuGroup) {
        //instanciamos el men� por reflection.
        if ($this->oMenu != null) {

            foreach ($this->oMenu->getGroups() as $key => $menuGroup) {

                //buscamos el menugroup.
                if ($currentMenuGroup == $menuGroup->getCd_menugroup()) {

                    //mostramos cada item del menugroup.
                    $count = 0;
                    foreach ($menuGroup->getOptions() as $key => $opcion) {

                        $count += $this->parseMenuOption($xtpl, $opcion);
                    }
                    if ($count > 0)
                        $xtpl->parse('main.dock');
                }
            }
        }
    }

    public function getAction() {
        return $this->oAction;
    }

    public function setAction($action) {
        $this->oAction = $action;
    }

    protected function parseMenuSolapas($xtpl, $menuGroups, $currentMenuGroup) {

        /* si menuGroups est� vac�o, se muestran todos los men�es */
		
        if (empty($menuGroups)) {
            $this->parseMenuSolapasTodas($xtpl, $currentMenuGroup);
        } else {

            //obtenemos la lista de menugroups (id's).
            $menugroupsId = explode(",", $menuGroups);


            if ($this->oMenu != null) {

                foreach ($menugroupsId as $key => $id) {

                    $menuGroup = $oMenu->getMenuGroupById($id);

                    $css_class = $menuGroup->getDs_cssclass();
                    if (!empty($css_class)) {

                        $this->parseMenuGroup($xtpl, $menuGroup, $currentMenuGroup);
                    }
                }
            }
        }
    }

    protected function parseMenuGroup(XTemplate $xtpl, $menuGroup, $currentMenuGroup) {


        $oUser = CdtSecureUtils::getUserLogged();
        if ($menuGroup->hasPermission($oUser)) {

        	$this->parseMenuGroupOptions( $xtpl, $menuGroup);
        	
            $xtpl->assign('css_li_class', $menuGroup->getDs_cssclass());

            if ($currentMenuGroup == $menuGroup->getCd_menugroup())
                $xtpl->assign('css_a_class', "active");
            else
                $xtpl->assign('css_a_class', "");

            $xtpl->assign('href', 'doAction?action=' . $menuGroup->getDs_action());

            	
            $xtpl->assign('action_description', $this->getLabel($menuGroup->getDs_name()));

            
            
            $xtpl->parse('main.menu_solapas');
        }
    }
    
    protected function parseMenuGroupOptions( XTemplate $xtpl, $menuGroup ){
    	
    	$size = $menuGroup->getOptions()->size();
    	foreach ($menuGroup->getOptions() as $menuOption) {
    		
    		$oUser = CdtSecureUtils::getUserLogged();
        	if ($menuOption->hasPermission($oUser)) {
            	$xtpl->assign('css_class', $menuOption->getDs_cssclass());

	            $description = $menuOption->getDs_description();
    	        if (empty($description))
        	        $xtpl->assign('action_description', $this->getLabel($menuOption->getDs_name()));
            	else
              		$xtpl->assign('action_description', $this->getLabel($description));

	            $xtpl->assign('href', $menuOption->getDs_href());
    	        $xtpl->parse('main.menu_solapas.submenu.item');
	    	}
    	}
    	
    	if( $size > 0 ){
    		$xtpl->parse('main.menu_solapas.submenu');
    	}
    }

    protected function parseMenuOption(XTemplate $xtpl, $menuOption) {
        //si la opci�n se parsea (xq el usuario tiene permisos, retorna 1 sino 0.

        $oUser = CdtSecureUtils::getUserLogged();
        if ($menuOption->hasPermission($oUser)) {
            $xtpl->assign('css_class', $menuOption->getDs_cssclass());

            $description = $menuOption->getDs_description();
            if (empty($description))
                $xtpl->assign('action_description', $this->getLabel($menuOption->getDs_name()));
            else
                $xtpl->assign('action_description', $this->getLabel($description));

            $xtpl->assign('href', $menuOption->getDs_href());
            $xtpl->parse('main.dock.menu_lateral');
            return 1;
        }else
            return 0;
    }

    protected function parseMenuSolapasTodas($xtpl, $currentMenuGroup) {

        if (!empty($currentMenuGroup)) {

            if ($this->oMenu != null) {

                foreach ($this->oMenu->getGroups() as $key => $menuGroup) {

                    $css_class = $menuGroup->getDs_cssclass();
                    if (!empty($css_class)) {

                        $this->parseMenuGroup($xtpl, $menuGroup, $currentMenuGroup);
                    }
                }
            }
        }
    }

    protected function parseException(XTemplate $xtpl) {
    	$exception = $this->getException();
        if (!empty($exception)) {
            $msg = $exception->getMessage();
            $xtpl->assign('error_message', $msg);
            $xtpl->parse('main.error_message');
        }
                
    	
    }
    protected function parseMensajes(XTemplate $xtpl) {
        
	    //parseamos errores.
	    if (CdtUtils::hasRequestError()  && $xtpl->existsBlock( "main.error_message" ) ) {
	
            $error = CdtUtils::getRequestError();
            
            $msg = addslashes( urldecode($error['msg']) );
            
            $cod = $error['code'];
            if( !empty($cod) )
             $msg = $cod . " - " . $msg;
            
            $xtpl->assign('error_message', $msg);
            $xtpl->parse('main.error_message');
            
            CdtUtils::log_debug( "LayoutSmile::parseMensajes => ". $msg );
            
            CdtUtils::cleanRequestError();
	    }
	
		//parseamos mensajes.
	    if (CdtUtils::hasRequestInfo()) {
	
	    	
	    	
            $info = CdtUtils::getRequestInfo();
            $msg = addslashes( urldecode($info['msg']) );

            
            $cod = $info['code'];
            if( !empty($cod) )
             $msg = $cod . " - " . $msg;
           
            $xtpl->assign('info_message', $msg);
            $xtpl->parse('main.info_message');
            
            CdtUtils::cleanRequestInfo();
	    }
        
        
    }

    protected function getHeader($menuOptions, $currentMenuGroup) {
        $xtpl = new XTemplate(CDT_UI_SMILE_TEMPLATE_HEADER);
        $this->parseMenuSuperiorDerecho($xtpl);
        $this->parseMenuLateral($xtpl, $menuOptions, $currentMenuGroup);
        $xtpl->assign('user', $this->oUsuario->getDs_username());
        $xtpl->assign('exit', CDT_UI_SMILE_MSG_EXIT);
        $xtpl->parse('main');
        return $xtpl->text('main');
    }

    protected function getFooter() {
        $xtpl = new XTemplate(CDT_UI_SMILE_TEMPLATE_FOOTER);
        $xtpl->parse('main');
        return $xtpl->text('main');
    }

    protected function parseMetaTags($xtpl) {

        $xtpl->assign('http_equiv', 'X-UA-Compatible');
        $xtpl->assign('meta_content', 'IE=7');
        $xtpl->parse('main.meta_tag');

        $xtpl->assign('http_equiv', 'Content-Type');
        
        /*
        if( CDT_UI_UTF8_ENCODE )
        	$xtpl->assign('meta_content', 'text/html; charset=UTF-8');
        else 	
        	$xtpl->assign('meta_content', 'text/html; charset=ISO-8859-1');
        */
        $xtpl->parse('main.meta_tag');
    }

    protected function parseStyles($xtpl) {

    	foreach ($this->getStyles() as $style) {
			$xtpl->assign('css', $style);
        	$xtpl->parse('main.estilo');			
		}
		

    }

    /**
     * la idea es tener una colección de scripts
     * y que el layout los incluye en la renderización.
     * @return array
     */
    protected function initScripts(){
        
    	$scripts = array();
    	
    	//$scripts[] =  WEB_PATH . "js/jquery/jquery-1.7.min.js";
		//$scripts[] =  "https://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js";
    	/*$scripts[] =  WEB_PATH . "js/jquery/jquery-ui-1.7.2.custom.min.js";
    	$scripts[] =  WEB_PATH . "js/jquery/jquery.feedback-1.2.0.js";
    	$scripts[] =  WEB_PATH . "js/jquery/jquery.alerts.js";*/

		$scripts[] =  "https://code.jquery.com/jquery-3.6.0.min.js";
		$scripts[] =  "https://code.jquery.com/ui/1.12.1/jquery-ui.js";

		$scripts[] =  WEB_PATH . "js/jquery/jquery.alerts.js";
    	
    	$scripts[] =  WEB_PATH . "js/jquery/jquery.ui.datepicker-es.js";
    	$scripts[] =  WEB_PATH . "js/jquery/jquery-ui-timepicker-addon.js";
    	//$scripts[] =  WEB_PATH . "js/jquery/jVal.js";
    	$scripts[] =  WEB_PATH . "js/jquery/jquery.form.js";
    	
    	$scripts[] =  WEB_PATH . "js/jquery/jquery.jqEasyCharCounter.min.js";
    	//$scripts[] =  WEB_PATH . "js/jquery/interface.js";
    	
    	$scripts[] =  WEB_PATH . "js/cdt_functions.js";

        $scripts[] =  WEB_PATH . "js/jquery/jquery.inputmask.js";
        $scripts[] =  WEB_PATH . "js/jquery/jquery.inputmask.extensions.js";
        $scripts[] =  WEB_PATH . "js/jquery/jquery.inputmask.regex.extensions.js";
        $scripts[] =  WEB_PATH . "js/jquery/jquery.inputmask.numeric.extensions.js";
        $scripts[] =  WEB_PATH . "js/jquery/jquery.inputmask.date.extensions.js";


    	$this->setScripts($scripts);
    }
    
    public function addScript( $source ){
    	$this->scripts[] = $source;
    }
    
    protected function parseScripts($xtpl) {

		foreach ($this->getScripts() as $script) {
			$xtpl->assign('js', $script);
        	$xtpl->parse('main.script');			
		}

    }

    protected function parseMenuSuperiorDerecho($xtpl) {

        $xtpl->assign('css_class', "desktop");
        $xtpl->assign('action', "home");
        $xtpl->assign('action_description', CDT_UI_SMILE_MSG_HOME);
        $xtpl->parse('main.menu_superior_derecha');

        $xtpl->assign('css_class', "profile");
        $xtpl->assign('action', "edit_cdtuserprofile_init");
        $xtpl->assign('action_description', CDT_SECURE_MSG_CDTUSERPROFILE_TITLE_UPDATE);
        $xtpl->parse('main.menu_superior_derecha');
    }

    public function getMensajeErrorFormateado() {
        $exception = $this->getException();
        if (!empty($exception)) {
            $msg = '<div class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0 .7em;">';
            $msg .= '	<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>';
            $msg .= $exception->getMessage();
            $msg .= '	</p>';
            $msg .= '</div>';
        }else
            $msg='';

        return $msg;
    }

    public function getMenu() {
        return $this->oMenu;
    }

    public function setMenu($oMenu) {
        $this->oMenu = $oMenu;
    }

    public function getTitle() {
        return CDT_MVC_APP_TITLE . " / " . CDT_MVC_APP_SUBTITLE;
    }

    

    public function getScripts()
    {
        return $this->scripts;
    }

    public function setScripts($scripts)
    {
        $this->scripts = $scripts;
    }
    
    /**
     * la idea es tener una colección de styles
     * y que el layout los incluye en la renderización.
     * @return array
     */
    protected function initStyles(){
        
    	$styles = array();
    	
    	/*$styles[] =  WEB_PATH . "css/smile/calendar.css";
    	$styles[] =  WEB_PATH . "css/smile/style_form.css";
    	$styles[] =  WEB_PATH . "css/smile/style_iefix.css";*/
    	$styles[] =  WEB_PATH . "css/smile/css_menu_panel.css";
    	
    	/*$styles[] =  WEB_PATH . "css/smile/estilos.css";
    	$styles[] =  WEB_PATH . "css/smile/screensmall.css";*/
    	$styles[] =  WEB_PATH . "css/smile/jquery-ui-1.7.2.custom.css";
    	$styles[] =  WEB_PATH . "css/smile/jquery.alerts.css";
    	$styles[] =  WEB_PATH . "css/smile/jquery.ui.core.css";
    	/*$styles[] =  WEB_PATH . "css/smile/dock.css";
    	$styles[] =  WEB_PATH . "css/smile/jVal.css";*/
    	$styles[] =  WEB_PATH . "css/smile/cdt_secure/cdt_secure.css";
		$styles[] =  WEB_PATH . "css/smile/cdt_secure/jquery-ui-timepicker-addon.css";
        
        if( defined("CDT_UI_THEME_WEB_PATH")) {
        	$styles[] =  CDT_UI_THEME_WEB_PATH . "styles.css";
        }

        $styles[] =  WEB_PATH . "css/styles.css";
            	
    	$this->setStyles($styles);
    }
    
    public function addStyle( $source ){
    	$this->styles[] = $source;
    }
    

    public function getStyles()
    {
        return $this->styles;
    }

    public function setStyles($styles)
    {
        $this->styles = $styles;
    }
    
    
    public function getLabel($text){
		if( defined( $text )){
        	$label = constant($text);
		}else 
       		$label = $text;
		return $label;
    }
    
}
