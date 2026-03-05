<?php

/**
 * Filtro para validar la seguridad.
 * 
 * @author bernardo
 * @since 04-07-2011
 */
class CdtSecureFilter implements ICdtActionFilter{

	
	/**
	 * (non-PHPdoc)
	 * @see ICdtActionFilter::apply();
	 */
	public function apply( $ds_action_name, CdtAction $oAction){
		
		$validate = true;
		
		$oActionFunction = $this->getActionFunction( $ds_action_name );
		if($oActionFunction!=null){
			
			if(!CdtSecureUtils::isUserLogged())
				$this->loginRequired();

			$oUser = CdtSecureUtils::getUserLogged();
				
			//chequeamos el permiso para ejecutar la acción.
			$validate = CdtSecureUtils::hasPermission ( $oUser, $oActionFunction->getCd_function() );

			if (!$validate)
				//si no tiene permiso, forward a la página de acceso denegado.
				throw new FailureException( $this->getActionOnAccessDenied() , CDT_SECURE_MSG_PERMISSION_DENIED );
		}else{
				
		}	
	}
	
	protected function loginRequired(){ 
		
		$_GET['backTo'] = $_SERVER['REQUEST_URI'];
		
		throw new FailureException( $this->getLoginAction(), CDT_SECURE_MSG_LOGIN_REQUIRED );
						
	}	
		
	public function getActionFunction( $ds_action_name ){
		
		$manager = new CdtActionFunctionManager();
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter( 'ds_action', $ds_action_name, "=", new CdtCriteriaFormatStringValue() );
		$oAF = $manager->getCdtActionFunction( $oCriteria );
		return $oAF;
	}
	
	protected function getLoginAction(){
		return CDT_SECURE_LOGIN_INIT_ACTION;
	}
	
	protected function getActionOnAccessDenied(){
		return CDT_SECURE_ACCESS_DENIED_ACTION;
	}
	
}