<?php

/**
 * Esta action la utilizamos para renderizar los componentes.
 * 
 * @author Bernardo
 * @since 22/03/2012
 */
class CMPComponentAction extends CdtOutputAction{


	protected function getOutputContent(){
		
		$component = $this->getComponent();
		
		if( $component !=null ){
			
		    return $component->show();
		}
		return "error";
	}

	protected function getComponent(){
		
		$componentClazz = CdtUtils::getParam("component", CdtUtils::getParamPOST("component"));
		
		if( !empty($componentClazz) ){
			
			$component = CdtReflectionUtils::newInstance($componentClazz);
			
		    
		}
		return $component;
	}
	
	protected function getLayout(){
		return new CdtLayoutBasicAjax();
		
	}

	protected function getOutputTitle(){
		return "component";
	}
}
?>