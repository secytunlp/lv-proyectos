<?php

/**
 * Representa un layout de la forma:
 *	{<meta-tags>, <scripts>, <estilos>} 
 *  <header>
 *  <content>
 *  <footer>
 * 
 * 
 * @author bernardo
 * @since 06-04-2010
 */
class LayoutSmilePanel extends LayoutSmile{



	public function show(){
		
		//buscamos los par�metros para configurar los men�es.
		//para los men�es.
		$currentMenuGroup = CdtUtils::getParam('currentMenuGroup');
		$menuGroups = CdtUtils::getParam('menuGroups');
		$menuOptions = CdtUtils::getParam('menuOptions');

		//seteamos el usuario para chequear permisos sobre los men�es.
		$oUsuario = CdtSecureUtils::getUserLogged();
				
		$xtpl = $this->getXTemplate ( $currentMenuGroup, $menuOptions);
		
		$xtpl->assign('titulo', $this->getTitle());
		$xtpl->assign('header', $this->getHeader($menuOptions,$currentMenuGroup));
		$xtpl->assign('user', $oUsuario->getDs_username() );
		$xtpl->assign('content', $this->getContent());
		$xtpl->assign('footer', $this->getFooter());
		$this->parseMetaTags($xtpl);
		$this->parseStyles($xtpl);
		$this->parseScripts($xtpl);
		
		$this->parseMensajes($xtpl);
		
		//seteamos los men�es.
		$this->parseMenuSolapas($xtpl, $menuGroups, $currentMenuGroup);
		$this->parseMenuSuperiorDerecho($xtpl);
		$this->parseMenuLateral($xtpl, $menuOptions, $currentMenuGroup);
		
		$xtpl->parse('main');

		return  $xtpl->text('main') ;
	}
	
}
