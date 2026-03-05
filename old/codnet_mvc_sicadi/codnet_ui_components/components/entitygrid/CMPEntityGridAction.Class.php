<?php
/**
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 22-03-2013
 *
 */
class CMPEntityGridAction extends CMPComponentAction{


	protected function getLayout(){

		if( CdtUtils::getParam("layout") ){
			
			$oLayout = CdtReflectionUtils::newInstance(CdtUtils::getParam("layout"));
			
		}elseif( CdtUtils::getParam("search", CdtUtils::getParamPOST("search")))
		
			$oLayout = new CdtLayoutBasicAjax();
			
		elseif( defined("CMP_GRID_DEFAULT_LAYOUT") )
				$oLayout = CdtReflectionUtils::newInstance(CMP_GRID_DEFAULT_LAYOUT);
		else	
				$oLayout = CdtReflectionUtils::newInstance(DEFAULT_LAYOUT);
		
				
		return $oLayout;
	}	

	protected function getComponent(){
		
		// Agrega la validación del token CSRF aquí
        if (CdtSecureUtils::isCSRFTokenValid()) {
			$componentClazz = CdtUtils::getParam("component", CdtUtils::getParamPOST("component"));
			
			if( !empty($componentClazz) ){
				
				$component = CdtReflectionUtils::newInstance($componentClazz);
				$inputId = CdtUtils::getParam("inputId", CdtUtils::getParamPOST("gridId", ""));
				$component->setId( $inputId );
				
				$fCallback = CdtUtils::getParam("fCallback", CdtUtils::getParamPOST("fCallback", "") );
				$component->getFilter()->setFCallback( $fCallback );
				
			}
			
			return $component;
		} else {
			throw new GenericException(CDT_SECURE_MSG_INVALID_CSRF);
        }
	}
		
}
?>