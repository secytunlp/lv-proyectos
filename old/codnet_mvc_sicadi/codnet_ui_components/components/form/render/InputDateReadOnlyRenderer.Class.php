<?php

/**
 * Implementación para renderizar un input para solo lectura. 
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 04-03-2013
 *
 */
class InputDateReadOnlyRenderer extends InputReadOnlyRenderer {

	protected function formatValue( CMPFormInput $input, $value ){
		
		$dateRenderer = new InputDateRenderer();
		return $dateRenderer->formatValue( $input , $value);
	}    	
}