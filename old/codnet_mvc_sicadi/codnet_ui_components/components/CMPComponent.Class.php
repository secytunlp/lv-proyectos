<?php

abstract class CMPComponent{

	public abstract function show();

	protected function getLayout(){
		return new CdtLayoutBasic();
	}
	
	public function getTitle(){
		return "";
	}

	public function getValue($item, $method, $itemClass=""){
			
		if(!empty($itemClass)){
			
			/*
			
			$method = "get" . ucfirst( $method );

			$reflectionMethod = new ReflectionMethod( get_class( $item ) , $method);

			//return utf8_encode($reflectionMethod->invoke( $item ));
			return $reflectionMethod->invoke( $item );
			
			*/
			
			return CdtReflectionUtils::doGetter( $item, $method );
			
		}else
			
			return $item[$method];
	}

	public function invokeMethod($clazz, $method, $params ){
		
		$oClass = new ReflectionClass($clazz);
		$oInstance = $oClass->newInstance();

		$reflectionMethod = new ReflectionMethod( get_class( $oInstance ) , $method);

		$value = $reflectionMethod->invoke( $oInstance, $params );

		return $value;
		
	}
	
	
	
}
?>