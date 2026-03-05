<?php 

/**
 * Acción para inicializar el contexto para editar
 * una localidad.
 * 
 * @author bernardo
 * @since 15-04-2010
 * 
 */
abstract class EditarProvinciaInitAction  extends EditarInitAction{

	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/action/generic/EditarInitAction#getXTemplate()
	 */
	protected function getXTemplate(){
		return new XTemplate ( CDT_GEO_TEMPLATE_EDITAR_PROVINCIA );
	}

	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/action/generic/EditarInitAction#getEntidad()
	 */
	protected function getEntidad(){
		return new Provincia();
	}
		
	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/action/generic/EditarInitAction#parseEntidad($entidad, $xtpl)
	 */
	protected function parseEntidad($entidad, XTemplate $xtpl){
		$oProvincia = FormatUtils::ifEmpty($entidad, new Provincia());
		//se muestra la localidad.
		$this->parseProvincia( $oProvincia , $xtpl);
		$this->parsePaises( $oProvincia->getCd_pais(), $xtpl );
		//$this->parseProvincias( $oProvincia->getProvincia()->getCd_pais(), $oProvincia->getProvincia()->getCd_provincia(), $xtpl );
	}
	
	
	protected function parseProvincia(Provincia $oProvincia, XTemplate $xtpl){
		//se muestra el localidad.
		$xtpl->assign ( 'cd_provincia', $oProvincia->getCd_provincia());
		$xtpl->assign ( 'ds_provincia', stripslashes ( $oProvincia->getDs_provincia () ) );
		$xtpl->assign ( 'cd_pais', stripslashes ( $oProvincia->getCd_pais() ) );
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