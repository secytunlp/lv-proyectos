<?php

/**
 * Provee m�todos para utilizar reflection.
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 13-09-2011
 */
class CdtReflectionUtils{


	/**
	 * se obtiene una instance de la clase $className
	 * @param unknown_type $className nombre de la clase a instanciar.
	 */
	public static function newInstance( $className ){
		
		$oClass = new ReflectionClass($className);
		$oInstance = $oClass->newInstance();
		
		return $oInstance;
	}

	/**
	 * Se invoca el setter del $fieldName sobre la instancia $oInstance.
	 * @param unknown_type $oInstance
	 * @param unknown_type $methodName
	 * @param unknown_type $args
	 */
	public static function doSetter( $oInstance, $field, $value=null ){

		if( $oInstance == null )
			return ;
			
		//TODO tratar los args.
		/*
		$method = self::buildSetter( $field );
		
		self::invoke( $oInstance, $method, $value );
		*/
		
		/*
		 * hay que obtener el "último" objeto para realizarle
		 * el setter.
		 * si viene algo del estilo: 
		 *   doSetter( $persona, "domicilio.calle.nombre", "Belgrano" )
		 * sería lo siguiente: 
		 *   $persona->getDomicilio()->getCalle()->setNombre( "Belgrano" )
		 */
		
		
		$methods = explode(".", $field );
		
		if( count($methods) > 1  ){
			
			$field =  $methods[ count($methods)-1 ];
			$getters =  array_slice($methods,0,count($methods)-1);
			$getters = implode(".", $getters ) ;
			$oInstance = self::doGetter( $oInstance, $getters );
		}
		
	
		$method = self::buildSetter( $field );
		
		self::invoke( $oInstance, $method, $value );
			
	}

	/**
	 * Se invoca el getter sobre la instancia $oInstance.
	 * Pueden ser varias properties, esto se indica separando por ",". Por
	 * ejemplo: "descripcion,cliente.nombre,cliente.apellido".
	 * @param $oInstance instancia para obtener las properties.
	 * @param $field una o varias propertiesi de la instancia. Pueden ser anidadas tipo ognl (cliente.nombre)
	 * @param $glue string para unir cuando son varias properties.
	 */
	public static function doGetter( $oInstance, $field, $glue=" " ){

		if( $oInstance == null )
			return "";

		/**
		 * invocamos un doGetter por cada uno de los getters indicados
		 * separados por ",". 
		 * Por ejemplo: si viene "cliente.nombre,cliente.apellido", vamos
		 * a invocar el doGetter para "cliente.nombre" y luego para "cliente.apellido".
		 */
		$result=array();
		$getters = explode(",", $field );
		if( count($getters)>1 ){
			for($index=0;   $index < count( $getters ) ; $index++) {
				$subfield = $getters[$index];
				$result[] = self::doGetter($oInstance, $subfield);
			}
			$res = implode($glue, $result);
			//CdtUtils::log("doGetter $field: $res", __CLASS__, LoggerLevel::getLevelDebug() );
			return trim( $res );
		}
			
		/**
		 * se obtiene el getter de una property anidada.
		 * Por ejemplo, "cliente.nombre"
		 */
		$methods = explode(".", $field );		
		if( count($methods) > 1  ){
			
			$value = $oInstance;
			for($index=0;   $index < count( $methods ) ; $index++) {
				
				$field = $methods[ $index ];
				
				$method = self::buildGetter( $field );
				$value = self::invoke( $value, $method );
			}
			
		}else{
			$method = self::buildGetter( $field );
		
			$value = self::invoke( $oInstance, $method );
			
			
		}
		return $value;
		
		/*
		FIXME
		$getters = explode(",", $field );
		$result = array();
		foreach ($getters as $getter) {

			$methods = explode(".", $getter );
			
			if( count($methods) > 1  ){
				
				$value = $oInstance;
				for($index=0;   $index < count( $methods ) ; $index++) {
					
					$field = trim( $methods[ $index ] );
					
					$method = self::buildGetter( $field );
					$value = self::invoke( $value, $method );
				}
				
			}else{
				$method = self::buildGetter( $field );
			
				$value = self::invoke( $oInstance, $method );
				
			}
			$result[] = $value;
		}
		
		return implode($glue, $result);
		*/
			
	}
	
	/**
	 * Se invoca el m�todo $methoName sobre la instancia $oInstance.
	 * @param unknown_type $oInstance
	 * @param unknown_type $methodName
	 * @param unknown_type $args
	 */
	public static function invoke( $oInstance, $methodName, $args=null ){

		//TODO tratar los args.
		$reflectionMethod = new ReflectionMethod( get_class( $oInstance ) , $methodName);
		$value = $reflectionMethod->invoke( $oInstance, $args );
			
		return $value;
	}


	/**
	 * forma el getter para un atributo.
	 * @param string $field
	 */
	public static function buildGetter( $field ){

		return "get" . ucfirst( $field );
	}
	
	/**
	 * forma el setter para un atributo.
	 * @param string $field
	 */
	public static function buildSetter( $field ){

		return "set" . ucfirst( $field );
	}	
}
