<?php 

/**
 * Acción para editar una localidad.
 * 
 * @author bernardo
 * @since 22-04-2010
 * 
 */
abstract class EditarProvinciaAction extends EditarAction{

	/**
	 * (non-PHPdoc)
	 * @see clases/com/cdt/action/generic/EditarAction#getEntidad()
	 */
	protected function getEntidad(){
		$oProvincia = new Provincia();

		if (isset ( $_POST ['cd_provincia'] ))
			$oProvincia->setCd_provincia (  FormatUtils::getParamPOST('cd_provincia')  );
		
		if (isset ( $_POST ['ds_provincia'] ))
			$oProvincia->setDs_provincia (  FormatUtils::getParamPOST('ds_provincia')  );
		
		if (isset ( $_POST ['cd_pais'] ))
			$oProvincia->setCd_pais (  FormatUtils::getParamPOST('cd_pais') );
		
		return $oProvincia;
	}
}