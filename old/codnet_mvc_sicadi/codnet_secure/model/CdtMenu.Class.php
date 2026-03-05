<?php
/**
 * Men�.
 * 
 * @author bernardo
 * @since 19-03-2010
 *
 */
class CdtMenu{

	protected $groups;
	
	//M�todo constructor 
	

	function __construct() {
		
		$this->groups = new ItemCollection();
		
		//$menuManager = new MenuManager();
		//$this->groups = $menuManager->getMenuGroups();
		
	}
	
	
	
	
	function getActiveMenu(){
		$menuActivo = null;
		foreach($this->getGroups() as $key => $group) {
			//$menuActivo = $group->getMenuActivo();
			if($menuActivo!=null)
				break;
		}
		
		return $menuActivo;
		
	}
	
	
	function getMenuGroupById( $id ){

		foreach ($this->getGroups() as $menuGroup) {
			
			if( $menuGroup->getCd_menugroup() == $id )
				return $menuGroup;
			 
		}
		return null;
	}
	
	function getMenuOptionsById( $menuoptionsId ){
		$opciones=array();
		foreach ($menuoptionsId as $id) {
			
			foreach ($this->getGroups() as $menuGroup) {
				
				foreach ($menuGroup->getOptions() as $option) {
				
					if( $option->getCd_menuoption() == $id )
						$opciones[] = $option;
					
				}
			}	
		}
		
		return $opciones;
	}
	

	public function getGroups()
	{
	    return $this->groups;
	}

	public function setGroups($groups)
	{
	    $this->groups = $groups;
	}
}

	