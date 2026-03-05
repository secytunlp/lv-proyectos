<?php
/**
 * Este factory crea una colección de objetos a partir
 * del resultado de un query.
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 04-03-2010
 *
 */

class CdtResultFactory {

	/**
	 * mapea los resultados de una consulta en una colección
	 * @param ICdtDatabase $db manejador de bbdd.
	 * @param $result resultados de un query.
	 * @param ICdtObjectFactory $factory construye el objeto específico.
	 * @return ItemCollection
	 */
	public static function toCollection( ICdtDatabase $db, $result, ICdtObjectFactory $factory){
		
		$colection = new ItemCollection();
		
		while ( $next = $db->sql_fetchassoc ( $result ) ) {
			
			$oNext = $factory->build($next);
			$colection->addItem($oNext);
		}
		
		return $colection;
	}

	/**
	 * mapea los resultados en una colección donde el índice para
	 * acceder a cada objeto es su identificador.
	 * @param $db manejador de bbdd.
	 * @param $result resultados de un query.
	 * @param $factory construye el objeto específico.
	 * @return itemCollection
	 * @deprecated
	 */
	public static function toCollectionWithCode($db, $result, ObjectCodeFactory $factory){
		$collection = new ItemCollection();
		while ( $next = $db->sql_fetchassoc ( $result ) ) {
			$oNext = $factory->build($next);
			$oNextCode = $factory->getCode($oNext);
			$collection->addItem($oNext, $oNextCode);
		}
		return $collection;
	}
	
	/**
	 * dado una arreglo asociativo, construye una colección de objetos.
	 * @param array $resultArray arreglo asociativo
	 * @param ICdtObjectFactory $factory construye el objeto específico.
	 */
	public static function arrayToCollection($resultArray, ICdtObjectFactoryy $factory){
		$collection = new ItemCollection();
		
		foreach ($resultArray as $next) {
			$oNext = $factory->build($next);
			$collection->addItem($oNext);
		}
		return $collection;
	}

	
}
?>