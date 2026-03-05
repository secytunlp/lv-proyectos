<?php
/**
 * 
 * @author bernardo
 * @since 09-03-2010
 * 
 * Factory para localidad.
 *
 */
class LocalidadFactory implements ObjectFactory{

	/**
	 * construye una localidad. 
	 * @param $next
	 * @return unknown_type
	 */
	public function build($next){
		$oLocalidad = new Localidad();
		$oLocalidad->setCd_localidad( $next ['cd_localidad'] );
		$oLocalidad->setDs_localidad( $next ['ds_localidad'] );
		$oLocalidad->setCd_provincia( $next ['cd_provincia'] );		
		
		//para el caso de join con provincia.
		if(array_key_exists('ds_provincia',$next)){
			$provinciaFactory = new ProvinciaFactory();
			$oLocalidad->setProvincia( $provinciaFactory->build($next) );	
		}		
		
		return $oLocalidad;
	}
}
?>