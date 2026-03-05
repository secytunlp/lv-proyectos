<?php 

/**
 * Acción para inicializar el contexto para loguearse en la 
 * web pública.
 * TODO
 * @author bernardo
 * @since 12-09-2011
 * 
 */
class LoginWebInitAction extends OutputAction{

	/**
	 * @return forward.
	 */
	protected function getContenido(){
			
		$xtpl = $this->getXTemplate ();

		$xtpl->assign ( 'WEB_PATH', WEB_PATH );

		
		$xtpl->assign('lbl_username', CDT_SEGURIDAD_REGISTRACION_DS_NOMUSUARIO );
        $xtpl->assign('lbl_password', CDT_SEGURIDAD_REGISTRACION_DS_PASSWORD);
		$xtpl->assign('txt_campos_obligatorios', CDT_SEGURIDAD_MSG_CAMPOS_OBLIGATORIOS);
		$xtpl->assign('btn_ingresar', CDT_SEGURIDAD_MSG_BTN_LOGIN_WEB);
		
		$xtpl->assign('txt_ingrese_username', CDT_SEGURIDAD_MSG_INGRESE_USERNAME);
		$xtpl->assign('txt_ingrese_password', CDT_SEGURIDAD_MSG_INGRESE_PASSWORD);
		$xtpl->assign('txt_recurepar_password', CDT_SEGURIDAD_MSG_RECUPERAR_PASSWORD);
		
		$xtpl->assign('login_web_action', CDT_SEGURIDAD_LOGIN_WEB_ACTION);
		$xtpl->assign('solicitar_clave_init_action', CDT_SEGURIDAD_SOLICITAR_CLAVE_INIT_ACTION);
		
		
		$xtpl->assign('titulo', CDT_SEGURIDAD_MSG_REGISTRAR_USUARIO_TITULO);
		  
		
		$xtpl->assign('ds_username', FormatUtils::getParamPOST('ds_username'));
		        
		$backTo = FormatUtils::getParam('backTo', FormatUtils::getParamPOST('backTo','') );
		
		if(!empty($backTo)){
			$xtpl->assign('backTo', $backTo );
		}
		
		$msj= FormatUtils::getParam('msg','',true,false);
		if ( !empty($msj) ){
			$xtpl->assign('msj', $msj);
			$xtpl->parse ( 'main.msj' );
		}

		$this->parseInfoAdicional($xtpl);
		
		if( CDT_SECURE_REGISTRATION_ENABLED ){
			$xtpl->assign('link_registrarse', CDT_SECURE_MSG_LINK_REGISTRARSE);
			$xtpl->parse ( 'main.registration' );
		}
		
		$xtpl->parse ( 'main' );
		return $xtpl->text ( 'main' );
	}
	
	protected function parseInfoAdicional(XTemplate $xtpl){
		//para que las subclases puedan parsear más información.
	}
	
	public function getXTemplate(){
		return new XTemplate ( CDT_SEGURIDAD_TEMPLATE_WEB_LOGIN );
	}
	
	
	protected function getTitulo(){
		return CDT_SEGURIDAD_MSG_LOGIN_WEB_TITULO;
	}	

	protected function getLayout(){
		$oClass = new ReflectionClass( CDT_SEGURIDAD_LOGIN_WEB_LAYOUT );
		$oLayout = $oClass->newInstance();
		
		return $oLayout;
	}	
	
}