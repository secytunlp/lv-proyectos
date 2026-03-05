<?php

class CMPFindObjectAction extends CdtOutputAction{


	protected function getOutputContent(){

		//clase y método a invocar para obtener los items.
		$requestClazz =  CdtUtils::getParam('requestClass') ;
		$requestMethod =  CdtUtils::getParam('requestMethod') ;
		$itemClazz =  CdtUtils::getParam('itemClass') ;
		$itemCode =  CdtUtils::getParam('itemCode') ;
		$itemLabel =  CdtUtils::getParam('itemLabel') ;
		$itemAttrCallback =  CdtUtils::getParam('itemAttrCallback') ;
		$functionCallback =  CdtUtils::getParam('functionCallback') ;
		$inputName =  CdtUtils::getParam('inputName') ;
		$text = CdtUtils::getParam('query') ;
			

		$oFindObject = new CMPFindObject();
		$oFindObject->setRequestClass( $requestClazz );
		$oFindObject->setRequestMethod( $requestMethod );
		$oFindObject->setItemClass( $itemClazz );
		$oFindObject->setItemLabel( $itemLabel );
		$oFindObject->setItemCode( $itemCode );
		$oFindObject->setItemAttributesCallback( $itemAttrCallback );
		$oFindObject->setFunctionCallback( $functionCallback );
		return $oFindObject->findItem( $text );
	}

	public function getOutputTitle(){
		return "";
	}
	

	protected function getLayout(){
		return new CdtLayoutJson();
	}		

}
?>