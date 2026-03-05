<?php
/**
 * Action obtener una entity
 * La entity encontrado es retornada en formato json.
 *  
 * @author bernardo
 * @since 30/05/2013
 */
class CMPFindEntityByCodeAction extends CdtOutputAction{

	const ERROR_NO_PARAMS = 1;
	const ERROR_ENTITY_NOT_FOUND = 2;
	
	/**
	 * obtiene la entity dados los parámtros
	 */
	protected function getOutputContent(){
		
		$result = array();
		
		if( isset( $_GET['query'] ) && $_GET['query'] != ""&& $_GET['finder'] != ""){
			
		    $text =  CdtUtils::getParam('query') ;
			
		    $parent =  CdtUtils::getParam('parent', null) ;
		    
		    $finderClazz =  CdtUtils::getParam('finder') ;
		    
		    $attributes =  CdtUtils::getParam('attributes', null) ;
		    
		    $finder = CdtReflectionUtils::newInstance( $finderClazz );
		    
		    $result = $this->find( $finder,  $text, $attributes, $parent );
		}else{
			
			$result["error"] = array( "code" => self::ERROR_NO_PARAMS , "msg" => CDT_CMP_FORM_FINDENTITY_NO_PARAMS);
		}
		
		return $result;
	}

	public function getOutputTitle(){
		return "";
	}
	

	protected function getLayout(){
		return new CdtLayoutJson();
	}
	
	protected function find( IObjectFinder $finder,  $text, $attributes=null, $parent=null ){
		
		//obtenemos la entity
		$entity = $finder->getObjectByCode($text, $parent);
		
		if( $entity != null ){
			$result["entity"]["oid"] = $finder->getObjectCode($entity);
			$result["entity"]["label"] = $finder->getObjectLabel($entity);
			$result["entity"]['attributes'] =  $finder->getObjectAttributes($entity, $attributes);
		}else{
			$msg = CdtFormatUtils::formatMessage(CDT_CMP_FORM_FINDENTITY_NO_ENTITY_FOUND, array("'$text'"));
			$result["error"] = array( "code" => self::ERROR_ENTITY_NOT_FOUND , "msg" => $msg);
		}

		return $result;
	}
	

}
?>