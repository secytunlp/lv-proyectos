<?php

class CMPEntityAutocompleteAction extends CdtOutputAction{


	protected function getOutputContent(){
		
		if( isset( $_GET['query'] ) && $_GET['query'] != "" && $_GET['component'] != "" ){
			
		    $text =  CdtUtils::getParam('query') ;
			$automcompleteClazz =  CdtUtils::getParam('component') ;

			$parent =  CdtUtils::getParam('parent') ;

			$autocomplete = CdtReflectionUtils::newInstance($automcompleteClazz);
			
		    return $autocomplete->getItems( $text, $parent );
		}
		return "";
	}

	public function getOutputTitle(){
		return "";
	}
	

	protected function getLayout(){
		//return new CdtLayoutBasic();
		return new CdtLayoutBasicAjax();
	}	
}
?>