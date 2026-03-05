<?php

/**
 * Interface que implementarán las clases encargadas de renderizar un input 
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 26-02-2013
 *
 */
interface IFormInputRenderer{

	public function render( CMPFormInput $oFormInput );
	
}