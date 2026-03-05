<?php 

/**
 * 
 * Las acciones son disparadas por el actionController 
 * a partir de las peticiones (request).
 * Cada acción está destinada a realizar una tarea
 * específica en la aplicación. También decide el destino
 * posible de acuerdo al resultado obtenido (forward).
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 03-03-2010
 */
abstract class CdtAction{

	//parámetros utilizados para el forward.
	private $ds_forward_params=null;

		
	//Métodos Get 
	
	public function getDs_forward_params(){
		return $this->ds_forward_params;
	}
	
		
	//Métodos Set 
	
	public function setDs_forward_params($value){
		$this->ds_forward_params = $value;
	}
	
	//Funciones.
	
	/**
	 * Se ejecuta la acción.
	 * Cada acción concreta implementará una funcionalidad específica.
	 * 
	 * @return forward indica el camino a seguir.
	 * @throws GenericException excepción genéria ante alguna posible falla.
	 */
	public abstract function execute();

	/**
	 * Ante una falla se lanza una forward por excepción. 
	 * @param GenericException $ex excepción que indica el fallo
	 * @param string $forward el camino a seguir
	 * @throws FailureException excepción por fallo
	 * @deprecated
	 */
	protected function doForwardException(GenericException $ex, $forward){
		
		$map = new CdtActionMapHelper();
		$ds_forward = $map->getForward( $forward );
		
		$pos_accion = strpos( $ds_forward, "action" );
		
		if( $pos_accion  )
			$ds_actionName = CdtUtils::getActionFromUrl( $ds_forward );
		else		
			$ds_actionName = $ds_forward;

		throw new FailureException( $ds_actionName, $ex->getMessage(), $ex->getCode() );
	}		



}