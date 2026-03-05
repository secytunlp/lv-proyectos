<?php

class CMPAutocompleteAction extends CdtOutputAction{


	protected function getOutputContent(){
		
		if( isset( $_GET['query'] ) && $_GET['query'] != "" && $_GET['requestClass'] != ""
			&& $_GET['itemLabel'] != "" && $_GET['itemCode'] != ""){
			
		    $text =  CdtUtils::getParam('query') ;
		    
		    //clase y m�todo a invocar para obtener los items.
			$requestClazz =  CdtUtils::getParam('requestClass') ;
			$requestMethod =  CdtUtils::getParam('requestMethod') ;
			$itemClazz =  CdtUtils::getParam('itemClass') ;
			$itemCode =  CdtUtils::getParam('itemCode') ;
			$itemLabel =  CdtUtils::getParam('itemLabel') ;
			$itemField =  CdtUtils::getParam('itemField') ;
			$itemAttrCallback =  CdtUtils::getParam('itemAttrCallback') ;
			$itemAttrList =  CdtUtils::getParam('itemAttrList') ;
			
			$oAutocomplete = new CMPAutocomplete();
			$oAutocomplete->setRequestClass( $requestClazz );
			$oAutocomplete->setRequestMethod( $requestMethod );
			$oAutocomplete->setItemClass( $itemClazz );
			$oAutocomplete->setItemLabel( $itemLabel );
			$oAutocomplete->setItemField( $itemField );
			$oAutocomplete->setItemCode( $itemCode );
			$oAutocomplete->setItemAttributesCallback( $itemAttrCallback );
			$oAutocomplete->setItemAttributesList( $itemAttrList );
			
		    return $oAutocomplete->getItems( $text );
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