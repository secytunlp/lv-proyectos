<?php 

/**
 * Acción para inicializar el contexto para editar
 * una localidad.
 * 
 * @author bernardo
 * @since 15-04-2010
 * 
 */
abstract class EditarLocalidadInitAction  extends EditarInitAction{

	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/action/generic/EditarInitAction#getXTemplate()
	 */
	protected function getXTemplate(){
		return new XTemplate ( CDT_GEO_TEMPLATE_EDITAR_LOCALIDAD );
	}

	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/action/generic/EditarInitAction#getEntidad()
	 */
	protected function getEntidad(){
		return new Localidad();
	}
		
	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/action/generic/EditarInitAction#parseEntidad($entidad, $xtpl)
	 */
	protected function parseEntidad($entidad, XTemplate $xtpl){
		$oLocalidad = FormatUtils::ifEmpty($entidad, new Localidad());
		//se muestra la localidad.
		$this->parseLocalidad( $oLocalidad , $xtpl);
		$this->parsePaises( $oLocalidad->getProvincia()->getCd_pais(), $xtpl );
		$this->parseProvincias( $oLocalidad->getProvincia()->getCd_pais(), $oLocalidad->getProvincia()->getCd_provincia(), $xtpl );
	}
	
	
	protected function parseLocalidad(Localidad $oLocalidad, XTemplate $xtpl){
		//se muestra el localidad.
		$xtpl->assign ( 'cd_localidad', $oLocalidad->getCd_localidad());
		$xtpl->assign ( 'ds_localidad', stripslashes ( $oLocalidad->getDs_localidad () ) );
		$xtpl->assign ( 'cd_provincia', stripslashes ( $oLocalidad->getCd_provincia() ) );
	}

	protected function parsePaises($cd_selected='', XTemplate $xtpl){
		//recupera y parsea países.
		$localizacionManager = new LocalizacionManager();
		$paises = $localizacionManager->getPaises();
		
		foreach($paises as $key => $pais) {
			$xtpl->assign ( 'ds_pais', $pais->getDs_pais() );
			$xtpl->assign ( 'cd_pais', FormatUtils::selected($pais->getCd_pais(), $cd_selected)  );
			$xtpl->parse ( 'main.option_pais' );
		}
	}
	
	protected function parseProvincias($cd_pais, $cd_selected='', XTemplate $xtpl){
		//recupera y parsea provincias.
		$localizacionManager = new LocalizacionManager();
		$cd_pais= FormatUtils::ifEmpty( $cd_pais, 0);
		$provincias = $localizacionManager->getProvinciasPorPais($cd_pais);
		
		foreach($provincias as $key => $provincia) {
			$xtpl->assign ( 'ds_provincia', $provincia->getDs_provincia() );
			$xtpl->assign ( 'cd_provincia', FormatUtils::selected($provincia->getCd_provincia(), $cd_selected)  );
			$xtpl->parse ( 'main.option_provincia' );
		}
	}
	
}