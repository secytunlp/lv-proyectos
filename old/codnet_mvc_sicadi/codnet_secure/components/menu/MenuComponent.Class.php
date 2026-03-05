<?php

/**
 * Componente menú.
 * 
 * @author bernardo
 * @since 19-03-2010
 */
class MenuComponent{
	
	protected $menu;
	private $oUsuario;
	private $funciones;
		
	public function MenuComponent(Menu $value=null, Usuario $oUsuario){
		$this->menu = $value;
		$this->setUsuario( $oUsuario );
	}
	
	public function show(){
		return $this->getMenuHTML($this->menu);
	}
	
	public function setMenu(Menu $value){
		$this->menu = $value;
	}
	
	public function setUsuario( Usuario $oUsuario ){
		$this->oUsuario = $oUsuario;
		//buscamos las funciones que puede realizar el usuario.
		// (para no ir a la bbdd por cada opción de menú ).
		//$perfilManager = new PerfilManager();
		//$this->funciones = $perfilManager->getFuncionesDeUsuario( $oUsuario );	
	}
	
	
	/**
	 * Toma un menú y lo parsea a formato html.
	 * 
	 * @return menu.html parseado.
	 */
	private function getMenuHTML(Menu $menu){
		
		$xtpl_menu = new XTemplate( CDT_SEGURIDAD_TEMPLATE_MENU );

		$xtpl_menu->assign('cssmenu', WEB_PATH.'css/menu.css');
		$xtpl_menu->assign('js', WEB_PATH.'js/ypSlideOutMenusC.js');
		
		$count=0;
		$x_position = 33;
		
		foreach($menu->getGrupos() as $key => $grupo) {
			if( $grupo->tieneAcceso( $this->oUsuario->getFunciones() ) ){
				$count++;
				$this->buildMenuGroup( $xtpl_menu, $grupo, $count, $x_position );
				$x_position += $grupo->getWidth();
			}			
		}

		//mostramos la navegación.
		if($menu->getMenuActivo()!=null){
			$xtpl_menu->assign('navegacion', $menu->getMenuActivo()->getDs_nombre() );
			$xtpl_menu->parse('main.navegacion');
		}
		
		$xtpl_menu->parse('main');
		
		return $xtpl_menu->text('main');	
	}
	
	protected function buildMenu(XTemplate $xtpl_menu, MenuGroup $menuGroup, $count, $x_position){
		$id_menuGroup = 'id'. $count;
		$nombre_menuGroup = 'nombre'. $count;
		
		//construimos el menú.				
		$xtpl_menu->assign('id_menu', $id_menuGroup);
		$xtpl_menu->assign('nombre_menu', $nombre_menuGroup);
		$xtpl_menu->assign('x', $x_position);
		$xtpl_menu->assign('h', '400');
		$xtpl_menu->parse('main.menu');
		
		//construimos el menú bar.
		$xtpl_menu->assign('id_menubar', $id_menuGroup);
		$xtpl_menu->assign('nombre_menubar', $nombre_menuGroup);
		$xtpl_menu->assign('ds_menubar', $menuGroup->getNombre());
		$xtpl_menu->parse('main.menubar');

		//construimos el menú container
		$xtpl_menu->assign('id_menucontainer', $nombre_menuGroup. 'Container');
		$xtpl_menu->assign('id_menucontent', $nombre_menuGroup. 'Content');
	}

	protected function buildOpciones(XTemplate $xtpl_menu, MenuGroup $menuGroup){
		//construimos las opciones de menú.		
		foreach($menuGroup->getOpciones() as $key => $opcion) {
			if( $opcion->tieneAcceso( $this->oUsuario->getFunciones() ) ){
				$xtpl_menu->assign('href_menuoption', $opcion->getHref());
				$xtpl_menu->assign('ds_menuoption', $opcion->getNombre());
				$xtpl_menu->parse('main.menucontainer.menucontent.menuoption');
			}			
		}		
	}
	

	
	protected function buildMenuGroup(XTemplate $xtpl_menu, MenuGroup $menuGroup, $count, $x_position){
		
		//construimos el menú.		
		$this->buildMenu($xtpl_menu, $menuGroup, $count, $x_position);
		
		//construimos las opciones de menú.		
		$this->buildOpciones($xtpl_menu, $menuGroup);
		
		$xtpl_menu->parse('main.menucontainer.menucontent');
		$xtpl_menu->parse('main.menucontainer');
		
	}
	
}
