<?php

/**
 * Filtro para validar la seguridad.
 * 
 * @author bernardo
 * @since 04-07-2011
 */
class CdtSecureWebFilter extends CdtSecureFilter{

		
	protected function getAccionLogin(){
		return CDT_SEGURIDAD_LOGIN_WEB_INIT_ACTION;
	}
	
	protected function getAccionSinPermisos(){
		return CDT_SEGURIDAD_ACCESO_WEB_DENEGADO_ACTION;
	}
	
}