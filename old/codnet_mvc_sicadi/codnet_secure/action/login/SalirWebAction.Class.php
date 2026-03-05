<?php 

/**
 * TODO
 * Acción para desloguearse de la web ugc.
 * 
 * @author bernardo
 * @since 12-05-2011
 * 
 */
class SalirWebAction extends Action{

	/**
	 * se desloguea el usuario logueado.
	 * @return forward.
	 */
	public function execute(){
		
		$oUsuario = new Usuario();
		
		$oUsuario->cerrarSesion ();
		
		$forward="salir_web_success";
		return $forward;
	}
	
}