<?php

/**
 * Representa un layout.
 * Un layout es una estructura que forma la salida en respuesta a
 * un request.
 * La idea de los layouts es para definir la estructura de salida de
 * un grupo de acciones, así, podemos unificar la estructura de todas
 * las acciones utilizando el mismo layout.
 * Otra ventaja de los layouts es que podemos utilizar la misma acción
 * con distintos layouts para obtener los mismos resultados en distintos
 * formatos, donde el formato lo da el layout. 
 *   
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 07-04-2010
 */
abstract class CdtLayout{

	//contenido del layout.
	private $content;
	//título del layout.
	private $title;
	//GenericExcpetion, para manejar mensajes por excepciones.
	private $exception;
	
	/**
	 * retorna la salida formateada por el layout.
	 * @return unknown_type
	 */
	public abstract function show();
	
	/**
	 * formatea la salida de un mensaje.
	 * @param string $message mensaje a formatear.
	 */
	public function formatMessage( $message ){
		if( !empty( $message) ){	
			$msg  = '<div style="margin-top: 20px; padding: 0 .7em;">' ;
			$msg .= '	<p><span " style="float: left; margin-right: .3em;"></span>';
			$msg .= $message;
			$msg .= '	</p>';
			$msg .= '</div>';
		}else $msg='';
		
		return $msg;		
	}	
	
	/* Gettes y Setters */
	
	public function getContent()
	{
	    return $this->content;
	}

	public function setContent($content)
	{
	    $this->content = $content;
	}

	public function getTitle()
	{
	    return $this->title;
	}

	public function setTitle($title)
	{
	    $this->title = $title;
	}
		
	public function setException(GenericException $exception){
		$this->exception = $exception;
	}
	
	public function getException(){
		return $this->exception;
	}


}
