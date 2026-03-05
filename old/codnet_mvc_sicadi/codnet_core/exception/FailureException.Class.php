<?php

/**
 * Excepción de error en las acciones.
 * 
 * Usamos esta excepción cuando no queremos que el controller nos saque
 * del request, sino que en lugar de hacer un forward a una url, ejecute 
 * una acción indicada.
 * 
 * @author bernardo
 * @since 19-05-2011
 */
class FailureException extends GenericException{
	
	private $ds_actionName;
	
	public function __construct($ds_action_name, $msg=null, $cod=0){

		parent::__construct($msg, $cod);
		
		$this->ds_actionName = $ds_action_name;
		
	}
	
	public function getDs_actionName(){ 
		return $this->ds_actionName;	
	}
	
}
