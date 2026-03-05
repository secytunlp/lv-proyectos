<?php
/**
 * 
 * @author bernardo
 * @since 09-03-2010
 * 
 * Factory para provincia.
 *
 */
class ProvinciaFactory implements ObjectFactory{

	/**
	 * construye una provincia. 
	 * @param $next
	 * @return unknown_type
	 */
	public function build($next){
		$oProvincia = new Provincia();
		$oProvincia->setCd_provincia( $next ['cd_provincia'] );
		$oProvincia->setDs_provincia( $next ['ds_provincia'] );
		$oProvincia->setCd_pais( $next ['cd_pais'] );		
		
		//para el caso de join con pais.
		if(array_key_exists('ds_pais',$next)){
			$paisFactory = new PaisFactory();
			$oProvincia->setPais( $paisFactory->build($next) );	
		}		
		
		return $oProvincia;
	}
}
?>