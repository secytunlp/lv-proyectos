<?php 

/**
 * Acción para visualizar una localidad.
 *  
 * @author bernardo
 * @since 18-03-2010
 * 
 */
class VerLocalidadAction extends OutputAction{

	public function getXTemplate(){
		return new XTemplate ( CDT_GEO_TEMPLATE_VER_LOCALIDAD );
	}
	
	/**
	 * consulta una localidad.
	 * @return forward.
	 */
	protected function getContenido(){
			
		$xtpl = $this->getXTemplate();
		
		if (isset ( $_GET ['id'] )) {
			$cd_localidad = FormatUtils::getParam('id');
			
			$manager = new LocalidadManager();
			
			try{
				$oLocalidad = $manager->getLocalidadPorId ( $cd_localidad );
			}catch(GenericException $ex){
				$oLocalidad = new Localidad();
				//TODO ver si se muestra un mensaje de error.
			}			

			//se muestra la localidad.
			$xtpl->assign ( 'cd_localidad', $oLocalidad->getCd_localidad());
			$xtpl->assign ( 'ds_localidad', stripslashes ( $oLocalidad->getDs_localidad()) );
			$xtpl->assign ( 'cd_provincia', stripslashes ( $oLocalidad->getCd_provincia () ) );
			$xtpl->assign ( 'ds_provincia', stripslashes ( $oLocalidad->getDs_provincia() ) );
			$xtpl->assign ( 'ds_pais', stripslashes ( $oLocalidad->getDs_pais() ) );
						
		}
		
		$xtpl->assign ( 'titulo', 'Detalle de localidad' );
		$xtpl->parse ( 'main' );
		return $xtpl->text ( 'main' );
	}

	public function getTitulo(){
		return "Detalle de Localidad";
	}
	
	
}