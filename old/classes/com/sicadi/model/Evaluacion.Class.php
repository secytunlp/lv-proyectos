<?php

/**
 * Evaluacion
 *
 * @author Marcos
 * @since 18-11-2013
 */

class Evaluacion extends Entity{

	//variables de instancia.
	
	private $solicitud;
	
	private $periodo;
	
	private $ds_investigador;
	
	private $ds_facultad;
	
	private $user;

	private $estado;
	
	
	
	private $dt_fecha;
	private $nu_puntaje;
	private $bl_interno;
	private $ds_observacion;
	
	private $nu_max;
	
	private $ds_contenido;
	
	private $modeloPlanilla_oid;
	
	
	private $posgrado_oid;
	private $cargo_oid;
	
	private $antacads;
	private $antotross;
	private $antproduccions;
	private $antjustificacions;
	private $subanteriors;
	
	
	

	public function __construct(){
		 
		$this->solicitud = new Solicitud();
		
		$this->user = new User();
		
		$this->estado = new Estado();
		
		
			
		$this->dt_fecha = "";
		
		$this->nu_puntaje = "";
		
		$this->bl_interno = "";
		
		
		
		
		
		$this->ds_observacion = "";
		
		$this->antacads = new ItemCollection();
		$this->antotross = new ItemCollection();
		$this->antproduccions = new ItemCollection();
		$this->antjustificacions = new ItemCollection();
		$this->subanteriors = new ItemCollection();
	}

	public function getNu_max()
	{
	    return $this->nu_max;
	}

	public function setNu_max($nu_max)
	{
	    $this->nu_max = $nu_max;
	}


	public function getDocente()
	{
	    return $this->docente;
	}

	public function setDocente($docente)
	{
	    $this->docente = $docente;
	}
	
	public function __toString(){
		
		return $this->getUser()->getDs_username();
	}

	 

	public function getSolicitud()
	{
	    return $this->solicitud;
	}

	public function setSolicitud($solicitud)
	{
	    $this->solicitud = $solicitud;
	}

	public function getPeriodo()
	{
	    return $this->periodo;
	}

	public function setPeriodo($periodo)
	{
	    $this->periodo = $periodo;
	}

	public function getDs_investigador()
	{
	    return $this->ds_investigador;
	}

	public function setDs_investigador($ds_investigador)
	{
	    $this->ds_investigador = $ds_investigador;
	}

	public function getDs_facultad()
	{
	    return $this->ds_facultad;
	}

	public function setDs_facultad($ds_facultad)
	{
	    $this->ds_facultad = $ds_facultad;
	}

	public function getUser()
	{
	    return $this->user;
	}

	public function setUser($user)
	{
	    $this->user = $user;
	}

	public function getEstado()
	{
	    return $this->estado;
	}

	public function setEstado($estado)
	{
	    $this->estado = $estado;
	}

	public function getDt_fecha()
	{
	    return $this->dt_fecha;
	}

	public function setDt_fecha($dt_fecha)
	{
	    $this->dt_fecha = $dt_fecha;
	}

	public function getNu_puntaje()
	{
	    return $this->nu_puntaje;
	}

	public function setNu_puntaje($nu_puntaje)
	{
	    $this->nu_puntaje = $nu_puntaje;
	}

	public function getBl_interno()
	{
	    return $this->bl_interno;
	}

	public function setBl_interno($bl_interno)
	{
	    $this->bl_interno = $bl_interno;
	}

	public function getDs_observacion()
	{
	    return $this->ds_observacion;
	}

	public function setDs_observacion($ds_observacion)
	{
	    $this->ds_observacion = $ds_observacion;
	}

	public function getDs_contenido()
	{
	    return $this->ds_contenido;
	}

	public function setDs_contenido($ds_contenido)
	{
	    $this->ds_contenido = $ds_contenido;
	}

	public function getModeloPlanilla_oid()
	{
	    return $this->modeloPlanilla_oid;
	}

	public function setModeloPlanilla_oid($modeloPlanilla_oid)
	{
	    $this->modeloPlanilla_oid = $modeloPlanilla_oid;
	}

	public function getPosgrado_oid()
	{
	    return $this->posgrado_oid;
	}

	public function setPosgrado_oid($posgrado_oid)
	{
	    $this->posgrado_oid = $posgrado_oid;
	}

	public function getCargo_oid()
	{
	    return $this->cargo_oid;
	}

	public function setCargo_oid($cargo_oid)
	{
	    $this->cargo_oid = $cargo_oid;
	}

	public function getAntacads()
	{
	    return $this->antacads;
	}

	public function setAntacads($antacads)
	{
	    $this->antacads = $antacads;
	}

	public function getAntotross()
	{
	    return $this->antotross;
	}

	public function setAntotross($antotross)
	{
	    $this->antotross = $antotross;
	}

	public function getAntproduccions()
	{
	    return $this->antproduccions;
	}

	public function setAntproduccions($antproduccions)
	{
	    $this->antproduccions = $antproduccions;
	}

	public function getAntjustificacions()
	{
	    return $this->antjustificacions;
	}

	public function setAntjustificacions($antjustificacions)
	{
	    $this->antjustificacions = $antjustificacions;
	}

	public function getSubanteriors()
	{
	    return $this->subanteriors;
	}

	public function setSubanteriors($subanteriors)
	{
	    $this->subanteriors = $subanteriors;
	}
}
?>