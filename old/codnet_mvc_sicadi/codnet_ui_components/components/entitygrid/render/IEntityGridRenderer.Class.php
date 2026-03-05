<?php

/**
 * Interface que implementarán las clases encargadas 
 * de renderizar una grilla.
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 22-03-2013
 *
 */
interface IEntityGridRenderer{

	/**
	 * renderiza la grilla.
	 * @param CMPEntityGrid $grid
	 */
	public function render( CMPEntityGrid $grid );
	
	/**
	 * renderiza sólo el resultado de la grilla.
	 * @param CMPEntityGrid $grid
	 */
	public function renderResults( CMPEntityGrid $grid );
	
}