<?php

/**
 * Implementación para renderizar un input text para fechas 
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 26-02-2013
 *
 */
class InputDateRenderer extends InputTextRenderer {

	
		
    protected function getXTemplate() {
    	return new XTemplate(CDT_CMP_TEMPLATE_FORM_INPUT_DATE);
    }

    /**
     * (non-PHPdoc)
     * @see components/form/render/InputTextRenderer::formatValue()
     */
	protected function formatValue( CMPFormInput $input, $value ){

		$res = "";
		if( !empty($value) ){
			$value = str_replace('/', '-', $value);
			$time = strtotime($value);
			$res =  date( $input->getFormat(), $time );
			CdtUtils::log("formatValue($value) => $res", __CLASS__, LoggerLevel::getLevelDebug());
		}
		
		return $res;
	}    
    
	/**
	 * (non-PHPdoc)
	 * @see components/form/render/FormInputRenderer::renderFormat()
	 */
	protected function renderFormat(CMPFormInput $input, XTemplate $xtpl){
		$format = $input->getFormat();
		//pasamos el formato de fecha php al de JQuery.
		$format = str_replace("d", "dd", $format);
		$format = str_replace("m", "mm", $format);
		$format = str_replace("Y", "yy", $format);
		
    	$xtpl->assign("format", $format );
    }
    
	protected function renderCustom(CMPFormInput $input, XTemplate $xtpl){
    	
    	parent::renderCustom($input, $xtpl);
    	
    	if(!$input->getIsEditable())
			$xtpl->assign("custom", " disabled ");
    	
    	if( $input->getChangeMonth() )
    		$xtpl->assign("changeMonth", "true" );    		
    	else 
    		$xtpl->assign("changeMonth", "false" );
    	
    	if( $input->getChangeYear() )
    		$xtpl->assign("changeYear", "true" );    		
    	else 
    		$xtpl->assign("changeYear", "false" );
    	
    		
   		$xtpl->assign("defaultDate", $input->getDefaultDate() );    		
   		$xtpl->assign("numberOfMonths", $input->getNumberOfMonths() );    		
   		
   		$minRangeFor = $input->getMinRangeFor();
   		if( !empty($minRangeFor) ){
   			$xtpl->assign("idMax", $minRangeFor );
   			$xtpl->parse("main.minRange");
   			
   		}
   		
		$maxRangeFor = $input->getMaxRangeFor();
   		if( !empty($maxRangeFor) ){
   			$xtpl->assign("idMin", $maxRangeFor );
   			$xtpl->parse("main.maxRange");
   		}
   		
    }
}