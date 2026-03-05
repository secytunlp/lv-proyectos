<?php
/**
 * 
 * Construye un objeto a partir de un arreglo asociativo (nombre->valor).
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 04-03-2010
 * 
 */
interface ICdtObjectFactory {
	
	/**
	 * construye un objeto dado un arreglo asociativo.
	 * @param $next arreglo asociativo (nombre=>valor).
	 * @return objeto mapeado.
	 */
	public function build($next);
	
}
?>