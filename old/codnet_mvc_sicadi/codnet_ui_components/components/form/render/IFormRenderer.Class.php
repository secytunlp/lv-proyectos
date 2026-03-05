<?php

/**
 * Interface que implementarán las clases encargadas de renderizar un formulario 
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 26-02-2013
 *
 */
interface IFormRenderer{

	public function render( CMPForm $oForm );
	
}