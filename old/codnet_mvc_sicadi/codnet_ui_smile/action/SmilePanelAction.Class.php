<?php

/**
 * Acci�n para mostrar un panel de control.
 *
 * @author bernardo
 * @since 08-03-2011
 *
 */
class SmilePanelAction extends CdtPanelAction{

	/**
	 * template donde parsear la salida.
	 * @return unknown_type
	 */
	protected function getXTemplate(){
		return new XTemplate( CDT_UI_SMILE_TEMPLATE_PANEL );
	}

	/**
	 * (non-PHPdoc)
	 * @see CdtPanelAction::parsePanel();
	 */
	protected function parsePanel( XTemplate $xtpl ){

		if( CdtUtils::hasRequestError() ){
			
			$error = CdtUtils::getRequestError();
			$msg  = urldecode( $error['msg'] );
			$xtpl->assign('error_message', $msg ) ;
			$xtpl->parse ( 'main.error_message' );
			
		}
		
		
		$currentMenuGroup = CdtUtils::getParam('currentMenuGroup');

		$menuOptions = CdtUtils::getParam('menuOptions');


		//recuperamos las opciones de men� (todas).
		$oManager = new CdtMenuGroupManager();
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addOrder("nu_order");
		$groups = $oManager->getCdtMenuGroupsWithOptions( $oCriteria );

		$oMenu = new CdtMenu();
		$oMenu->setGroups( $groups );
		

		//si no hay una solapa activa, mostramos todo el men�.
		if( empty($currentMenuGroup) ){

			$this->parseMenu( $xtpl, $oMenu );

		}else{
				
			//si hay opciones definidas las mostramos, sino mostramos las opciones del men� activo.
			if( empty( $menuOptions) ){
					
				$this->parseMenuGroup( $xtpl, $oMenu, $currentMenuGroup);
					
			}else{
					
				$this->parseMenuGroupOptions( $xtpl, $oMenu, $currentMenuGroup, $menuOptions );
			}
		}
	}

	/**
	 * se parsea una men� group espec�fico.
	 *
	 */
	protected function parseMenuGroupOptions( XTemplate $xtpl, $oMenu, $currentMenuGroup, $menuOptions, $title=''){

		if( empty( $title ) ){
			$menuGroup = $oMenu->getMenuGroupById( $currentMenuGroup );
			$title = $menuGroup->getDs_name();
		}

		$options = $oMenu->getMenuOptionsById( explode(",", $menuOptions ));

		$this->parseOptions($xtpl, $options, $title );

	}

	/**
	 * se parsea una men� group espec�fico.
	 *
	 */
	protected function parseMenuGroup( XTemplate $xtpl, $oMenu, $currentMenuGroup, $title=''){

		$menuGroup = $oMenu->getMenuGroupById( $currentMenuGroup );

		if( !empty( $menuGroup) ){

			if( empty( $title))
				$title = $menuGroup->getDs_name();
				
			//mostramos cada item del menugroup.
			$this->parseOptions($xtpl, $menuGroup->getOptions(), $this->getLabel($title) );

		}
	}


	function parseOptions(XTemplate $xtpl, $options, $title='' ){

		//recuperamos el usuario de sessi�n para chequear los permisos sobre el men�.
		$oUser = CdtSecureUtils::getUserLogged();
		
		//TODO chequer permisos del usuario.

		$count = 0;
		
		foreach($options as $key => $option){

			if( $option->hasPermission( $oUser ) ){
				$xtpl->assign('li_class', $option->getDs_cssclass());
	
				$description = $option->getDs_description();
	
				if(empty($description))
					$xtpl->assign('label', $this->getLabel($option->getDs_name()));
				else
					$xtpl->assign('label', $this->getLabel($description) );
					
				$xtpl->assign('href', $option->getDs_href());
				$xtpl->parse('main.group.item');
				
				$count++;
			}
		}
		if($count>0){
			$xtpl->assign('ds_menugroup', $this->getLabel($title));
			$xtpl->parse('main.group' );
		}
		

	}


	/**
	 * se parsea todo el men�.
	 *
	 */
	protected function parseMenu( XTemplate $xtpl, $oMenu ){

		foreach( $oMenu->getGroups() as $menuGroup ){

			//mostramos cada item del menugroup.
			$this->parseOptions($xtpl, $menuGroup->getOptions(), $menuGroup->getDs_name() );
		}
			
			
	}


	function getMenuGroupPorId( $groups, $id ){

		foreach ($groups as $menuGroup) {
			
			if( $menuGroup->getCd_menugroup() == $id )
				return $menuGroup;
			 
		}
		return null;
	}
	
	function getMenuOptionsPorId( $groups, $menuoptionsId ){
		$options=array();
		foreach ($menuoptionsId as $id) {
			
			foreach ($groups as $menuGroup) {
				
				foreach ($menuGroup->getOptions() as $option) {
				
					if( $option->getCd_menuoption() == $id )
						$opctons[] = $option;
					
				}
			}	
		}
		
		return $opciones;
	}
	
	public function getLabel($text){
		if( defined( $text )){
        	$label = constant($text);
		}else 
       		$label = $text;
		return $label;
    }
}