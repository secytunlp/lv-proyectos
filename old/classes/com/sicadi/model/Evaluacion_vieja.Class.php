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
	
	
	private $nu_posgradomaximo;
	private $cd_posgradomaximo;
	
	private $nu_max;
	
	private $nu_antacadmaximo;
	private $nu_topeA2;
	private $ds_descripcionA2;
	private $nu_puntajeA2;
	private $nu_puntajeantacadA2;
	
	private $nu_topeA3;
	private $ds_descripcionA3;
	private $nu_puntajeA3;
	private $nu_puntajeantacadA3;
	
	private $ds_descripcionA4;
	private $bl_posgrado;
	private $nu_puntajeantacadA4;
	
	private $ds_descripcionA5;
	private $nu_puntajeA5;
	private $nu_puntajeantacadA5;
	
	private $nu_topeA6;
	private $ds_descripcionA6;
	private $nu_puntajeA6;
	private $nu_puntajeantacadA6;
	
	private $nu_cargomaximo;
	private $cd_cargomaximo;
	
	private $nu_antotrosmaximo;
	private $nu_topeC1;
	private $ds_descripcionC1;
	private $nu_puntajeC1;
	private $nu_puntajeantotrosC1;
	
	private $ds_grupoC2;
	
	private $ds_descripcionC2_1;
	private $nu_puntajeC2_1;
	private $nu_puntajeantotrosC2_1;
	
	private $ds_descripcionC2_2;
	private $nu_puntajeC2_2;
	private $nu_puntajeantotrosC2_2;
	
	private $ds_descripcionC2_3;
	private $nu_puntajeC2_3;
	private $nu_puntajeantotrosC2_3;
	
	private $nu_topeC3;
	private $ds_descripcionC3;
	private $nu_puntajeC3;
	private $nu_puntajeantotrosC3;
	
	private $ds_descripcionC4;
	private $nu_puntajeC4;
	private $nu_puntajeantotrosC4;
	
	private $nu_antproduccionmaximo;
	
	private $ds_grupoD1;
	private $nu_topeD1_1;
	private $ds_descripcionD1_1;
	private $nu_puntajeD1_1;
	private $nu_puntajeantproduccionD1_1;
	
	private $nu_topeD1_2;
	private $ds_descripcionD1_2;
	private $nu_puntajeD1_2;
	private $nu_puntajeantproduccionD1_2;
	
	private $ds_grupoD2;
	private $nu_topeD2_1;
	private $ds_descripcionD2_1;
	private $nu_puntajeD2_1;
	private $nu_puntajeantproduccionD2_1;
	
	private $nu_topeD2_2;
	private $ds_descripcionD2_2;
	private $nu_puntajeD2_2;
	private $nu_puntajeantproduccionD2_2;
	
	private $nu_topeD2_3;
	private $ds_descripcionD2_3;
	private $nu_puntajeD2_3;
	private $nu_puntajeantproduccionD2_3;
	
	private $nu_topeD2_4;
	private $ds_descripcionD2_4;
	private $nu_puntajeD2_4;
	private $nu_puntajeantproduccionD2_4;
	
	private $nu_topeD2_5;
	private $ds_descripcionD2_5;
	private $nu_puntajeD2_5;
	private $nu_puntajeantproduccionD2_5;
	
	private $ds_grupoD3;
	private $ds_descripcionD3_1;
	private $nu_puntajeD3_1;
	private $nu_puntajeantproduccionD3_1;
	
	private $ds_descripcionD3_2;
	private $nu_puntajeD3_2;
	private $nu_puntajeantproduccionD3_2;
	
	private $ds_descripcionD3_3;
	private $nu_puntajeD3_3;
	private $nu_puntajeantproduccionD3_3;
	
	private $ds_descripcionD3_4;
	private $nu_puntajeD3_4;
	private $nu_puntajeantproduccionD3_4;
	
	private $ds_descripcionD3_5;
	private $nu_puntajeD3_5;
	private $nu_puntajeantproduccionD3_5;
	
	private $ds_grupoD4;
	private $ds_descripcionD4_1;
	private $nu_puntajeD4_1;
	private $nu_puntajeantproduccionD4_1;
	
	private $ds_grupoD5;
	private $ds_descripcionD5_1;
	private $nu_puntajeD5_1;
	private $nu_cantantproduccionD5_1;
	private $nu_puntajeantproduccionD5_1;
	
	private $ds_descripcionD5_2;
	private $nu_puntajeD5_2;
	private $nu_cantantproduccionD5_2;
	private $nu_puntajeantproduccionD5_2;
	
	private $ds_descripcionD5_3;
	private $nu_puntajeD5_3;
	private $nu_cantantproduccionD5_3;
	private $nu_puntajeantproduccionD5_3;
	
	private $ds_grupoD6;
	private $ds_descripcionD6_1;
	private $nu_puntajeD6_1;
	private $nu_puntajeantproduccionD6_1;
	
	private $ds_descripcionD6_2;
	private $nu_puntajeD6_2;
	private $nu_puntajeantproduccionD6_2;
	
	private $ds_descripcionD6_3;
	private $nu_puntajeD6_3;
	private $nu_puntajeantproduccionD6_3;
	
	private $ds_descripcionD6_4;
	private $nu_puntajeD6_4;
	private $nu_puntajeantproduccionD6_4;
	
	private $ds_grupoD7;
	private $nu_topeD7;
	private $ds_descripcionD7;
	private $nu_puntajeD7;
	private $nu_puntajeantproduccionD7;
	
	private $ds_grupoD8;
	private $ds_descripcionD8;
	private $nu_puntajeD8;
	private $nu_puntajeantproduccionD8;
	
	private $nu_antjustificacionmaximo;
	private $nu_topeE1;
	private $ds_descripcionE1;
	private $nu_puntajeE1;
	private $nu_puntajeantjustificacionE1;
	

	public function __construct(){
		 
		$this->solicitud = new Solicitud();
		
		$this->user = new User();
		
		$this->estado = new Estado();
		
		
			
		$this->dt_fecha = "";
		
		$this->nu_puntaje = "";
		
		$this->bl_interno = "";
		
		
		
		
		
		$this->ds_observacion = "";
	}




	

	 

	

	

	

	public function getSolicitud()
	{
	    return $this->solicitud;
	}

	public function setSolicitud($solicitud)
	{
	    $this->solicitud = $solicitud;
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

	public function getNu_posgradomaximo()
	{
	    return $this->nu_posgradomaximo;
	}

	public function setNu_posgradomaximo($nu_posgradomaximo)
	{
	    $this->nu_posgradomaximo = $nu_posgradomaximo;
	}

	public function getCd_posgradomaximo()
	{
	    return $this->cd_posgradomaximo;
	}

	public function setCd_posgradomaximo($cd_posgradomaximo)
	{
	    $this->cd_posgradomaximo = $cd_posgradomaximo;
	}

	public function getNu_max()
	{
	    return $this->nu_max;
	}

	public function setNu_max($nu_max)
	{
	    $this->nu_max = $nu_max;
	}

	public function getNu_antacadmaximo()
	{
	    return $this->nu_antacadmaximo;
	}

	public function setNu_antacadmaximo($nu_antacadmaximo)
	{
	    $this->nu_antacadmaximo = $nu_antacadmaximo;
	}

	public function getNu_topeA2()
	{
	    return $this->nu_topeA2;
	}

	public function setNu_topeA2($nu_topeA2)
	{
	    $this->nu_topeA2 = $nu_topeA2;
	}

	public function getDs_descripcionA2()
	{
	    return $this->ds_descripcionA2;
	}

	public function setDs_descripcionA2($ds_descripcionA2)
	{
	    $this->ds_descripcionA2 = $ds_descripcionA2;
	}

	public function getNu_puntajeA2()
	{
	    return $this->nu_puntajeA2;
	}

	public function setNu_puntajeA2($nu_puntajeA2)
	{
	    $this->nu_puntajeA2 = $nu_puntajeA2;
	}

	public function getNu_puntajeantacadA2()
	{
	    return $this->nu_puntajeantacadA2;
	}

	public function setNu_puntajeantacadA2($nu_puntajeantacadA2)
	{
	    $this->nu_puntajeantacadA2 = $nu_puntajeantacadA2;
	}



	public function getNu_topeA3()
	{
	    return $this->nu_topeA3;
	}

	public function setNu_topeA3($nu_topeA3)
	{
	    $this->nu_topeA3 = $nu_topeA3;
	}

	public function getDs_descripcionA3()
	{
	    return $this->ds_descripcionA3;
	}

	public function setDs_descripcionA3($ds_descripcionA3)
	{
	    $this->ds_descripcionA3 = $ds_descripcionA3;
	}

	public function getNu_puntajeA3()
	{
	    return $this->nu_puntajeA3;
	}

	public function setNu_puntajeA3($nu_puntajeA3)
	{
	    $this->nu_puntajeA3 = $nu_puntajeA3;
	}

	public function getNu_puntajeantacadA3()
	{
	    return $this->nu_puntajeantacadA3;
	}

	public function setNu_puntajeantacadA3($nu_puntajeantacadA3)
	{
	    $this->nu_puntajeantacadA3 = $nu_puntajeantacadA3;
	}

	public function getDs_descripcionA4()
	{
	    return $this->ds_descripcionA4;
	}

	public function setDs_descripcionA4($ds_descripcionA4)
	{
	    $this->ds_descripcionA4 = $ds_descripcionA4;
	}

	public function getBl_posgrado()
	{
	    return $this->bl_posgrado;
	}

	public function setBl_posgrado($bl_posgrado)
	{
	    $this->bl_posgrado = $bl_posgrado;
	}

	public function getNu_puntajeantacadA4()
	{
	    return $this->nu_puntajeantacadA4;
	}

	public function setNu_puntajeantacadA4($nu_puntajeantacadA4)
	{
	    $this->nu_puntajeantacadA4 = $nu_puntajeantacadA4;
	}

	public function getDs_descripcionA5()
	{
	    return $this->ds_descripcionA5;
	}

	public function setDs_descripcionA5($ds_descripcionA5)
	{
	    $this->ds_descripcionA5 = $ds_descripcionA5;
	}

	public function getNu_puntajeA5()
	{
	    return $this->nu_puntajeA5;
	}

	public function setNu_puntajeA5($nu_puntajeA5)
	{
	    $this->nu_puntajeA5 = $nu_puntajeA5;
	}

	public function getNu_puntajeantacadA5()
	{
	    return $this->nu_puntajeantacadA5;
	}

	public function setNu_puntajeantacadA5($nu_puntajeantacadA5)
	{
	    $this->nu_puntajeantacadA5 = $nu_puntajeantacadA5;
	}

	public function getNu_topeA6()
	{
	    return $this->nu_topeA6;
	}

	public function setNu_topeA6($nu_topeA6)
	{
	    $this->nu_topeA6 = $nu_topeA6;
	}

	public function getDs_descripcionA6()
	{
	    return $this->ds_descripcionA6;
	}

	public function setDs_descripcionA6($ds_descripcionA6)
	{
	    $this->ds_descripcionA6 = $ds_descripcionA6;
	}

	public function getNu_puntajeA6()
	{
	    return $this->nu_puntajeA6;
	}

	public function setNu_puntajeA6($nu_puntajeA6)
	{
	    $this->nu_puntajeA6 = $nu_puntajeA6;
	}

	public function getNu_puntajeantacadA6()
	{
	    return $this->nu_puntajeantacadA6;
	}

	public function setNu_puntajeantacadA6($nu_puntajeantacadA6)
	{
	    $this->nu_puntajeantacadA6 = $nu_puntajeantacadA6;
	}

	public function getNu_cargomaximo()
	{
	    return $this->nu_cargomaximo;
	}

	public function setNu_cargomaximo($nu_cargomaximo)
	{
	    $this->nu_cargomaximo = $nu_cargomaximo;
	}

	public function getCd_cargomaximo()
	{
	    return $this->cd_cargomaximo;
	}

	public function setCd_cargomaximo($cd_cargomaximo)
	{
	    $this->cd_cargomaximo = $cd_cargomaximo;
	}

	public function getNu_antotrosmaximo()
	{
	    return $this->nu_antotrosmaximo;
	}

	public function setNu_antotrosmaximo($nu_antotrosmaximo)
	{
	    $this->nu_antotrosmaximo = $nu_antotrosmaximo;
	}

	public function getNu_topeC1()
	{
	    return $this->nu_topeC1;
	}

	public function setNu_topeC1($nu_topeC1)
	{
	    $this->nu_topeC1 = $nu_topeC1;
	}

	public function getDs_descripcionC1()
	{
	    return $this->ds_descripcionC1;
	}

	public function setDs_descripcionC1($ds_descripcionC1)
	{
	    $this->ds_descripcionC1 = $ds_descripcionC1;
	}

	public function getNu_puntajeC1()
	{
	    return $this->nu_puntajeC1;
	}

	public function setNu_puntajeC1($nu_puntajeC1)
	{
	    $this->nu_puntajeC1 = $nu_puntajeC1;
	}

	public function getNu_puntajeantotrosC1()
	{
	    return $this->nu_puntajeantotrosC1;
	}

	public function setNu_puntajeantotrosC1($nu_puntajeantotrosC1)
	{
	    $this->nu_puntajeantotrosC1 = $nu_puntajeantotrosC1;
	}

	public function getDs_grupoC2()
	{
	    return $this->ds_grupoC2;
	}

	public function setDs_grupoC2($ds_grupoC2)
	{
	    $this->ds_grupoC2 = $ds_grupoC2;
	}

	public function getDs_descripcionC2_1()
	{
	    return $this->ds_descripcionC2_1;
	}

	public function setDs_descripcionC2_1($ds_descripcionC2_1)
	{
	    $this->ds_descripcionC2_1 = $ds_descripcionC2_1;
	}

	public function getNu_puntajeC2_1()
	{
	    return $this->nu_puntajeC2_1;
	}

	public function setNu_puntajeC2_1($nu_puntajeC2_1)
	{
	    $this->nu_puntajeC2_1 = $nu_puntajeC2_1;
	}

	public function getNu_puntajeantotrosC2_1()
	{
	    return $this->nu_puntajeantotrosC2_1;
	}

	public function setNu_puntajeantotrosC2_1($nu_puntajeantotrosC2_1)
	{
	    $this->nu_puntajeantotrosC2_1 = $nu_puntajeantotrosC2_1;
	}

	public function getDs_descripcionC2_2()
	{
	    return $this->ds_descripcionC2_2;
	}

	public function setDs_descripcionC2_2($ds_descripcionC2_2)
	{
	    $this->ds_descripcionC2_2 = $ds_descripcionC2_2;
	}

	public function getNu_puntajeC2_2()
	{
	    return $this->nu_puntajeC2_2;
	}

	public function setNu_puntajeC2_2($nu_puntajeC2_2)
	{
	    $this->nu_puntajeC2_2 = $nu_puntajeC2_2;
	}

	public function getNu_puntajeantotrosC2_2()
	{
	    return $this->nu_puntajeantotrosC2_2;
	}

	public function setNu_puntajeantotrosC2_2($nu_puntajeantotrosC2_2)
	{
	    $this->nu_puntajeantotrosC2_2 = $nu_puntajeantotrosC2_2;
	}

	public function getDs_descripcionC2_3()
	{
	    return $this->ds_descripcionC2_3;
	}

	public function setDs_descripcionC2_3($ds_descripcionC2_3)
	{
	    $this->ds_descripcionC2_3 = $ds_descripcionC2_3;
	}

	public function getNu_puntajeC2_3()
	{
	    return $this->nu_puntajeC2_3;
	}

	public function setNu_puntajeC2_3($nu_puntajeC2_3)
	{
	    $this->nu_puntajeC2_3 = $nu_puntajeC2_3;
	}

	public function getNu_puntajeantotrosC2_3()
	{
	    return $this->nu_puntajeantotrosC2_3;
	}

	public function setNu_puntajeantotrosC2_3($nu_puntajeantotrosC2_3)
	{
	    $this->nu_puntajeantotrosC2_3 = $nu_puntajeantotrosC2_3;
	}

	public function getNu_topeC3()
	{
	    return $this->nu_topeC3;
	}

	public function setNu_topeC3($nu_topeC3)
	{
	    $this->nu_topeC3 = $nu_topeC3;
	}

	public function getDs_descripcionC3()
	{
	    return $this->ds_descripcionC3;
	}

	public function setDs_descripcionC3($ds_descripcionC3)
	{
	    $this->ds_descripcionC3 = $ds_descripcionC3;
	}

	public function getNu_puntajeC3()
	{
	    return $this->nu_puntajeC3;
	}

	public function setNu_puntajeC3($nu_puntajeC3)
	{
	    $this->nu_puntajeC3 = $nu_puntajeC3;
	}

	public function getNu_puntajeantotrosC3()
	{
	    return $this->nu_puntajeantotrosC3;
	}

	public function setNu_puntajeantotrosC3($nu_puntajeantotrosC3)
	{
	    $this->nu_puntajeantotrosC3 = $nu_puntajeantotrosC3;
	}

	public function getDs_descripcionC4()
	{
	    return $this->ds_descripcionC4;
	}

	public function setDs_descripcionC4($ds_descripcionC4)
	{
	    $this->ds_descripcionC4 = $ds_descripcionC4;
	}

	public function getNu_puntajeC4()
	{
	    return $this->nu_puntajeC4;
	}

	public function setNu_puntajeC4($nu_puntajeC4)
	{
	    $this->nu_puntajeC4 = $nu_puntajeC4;
	}

	public function getNu_puntajeantotrosC4()
	{
	    return $this->nu_puntajeantotrosC4;
	}

	public function setNu_puntajeantotrosC4($nu_puntajeantotrosC4)
	{
	    $this->nu_puntajeantotrosC4 = $nu_puntajeantotrosC4;
	}

	public function getNu_antproduccionmaximo()
	{
	    return $this->nu_antproduccionmaximo;
	}

	public function setNu_antproduccionmaximo($nu_antproduccionmaximo)
	{
	    $this->nu_antproduccionmaximo = $nu_antproduccionmaximo;
	}

	public function getDs_grupoD1()
	{
	    return $this->ds_grupoD1;
	}

	public function setDs_grupoD1($ds_grupoD1)
	{
	    $this->ds_grupoD1 = $ds_grupoD1;
	}

	public function getDs_descripcionD1_1()
	{
	    return $this->ds_descripcionD1_1;
	}

	public function setDs_descripcionD1_1($ds_descripcionD1_1)
	{
	    $this->ds_descripcionD1_1 = $ds_descripcionD1_1;
	}

	public function getNu_puntajeD1_1()
	{
	    return $this->nu_puntajeD1_1;
	}

	public function setNu_puntajeD1_1($nu_puntajeD1_1)
	{
	    $this->nu_puntajeD1_1 = $nu_puntajeD1_1;
	}

	public function getNu_puntajeantproduccionD1_1()
	{
	    return $this->nu_puntajeantproduccionD1_1;
	}

	public function setNu_puntajeantproduccionD1_1($nu_puntajeantproduccionD1_1)
	{
	    $this->nu_puntajeantproduccionD1_1 = $nu_puntajeantproduccionD1_1;
	}

	public function getNu_topeD1_1()
	{
	    return $this->nu_topeD1_1;
	}

	public function setNu_topeD1_1($nu_topeD1_1)
	{
	    $this->nu_topeD1_1 = $nu_topeD1_1;
	}

	public function getNu_topeD1_2()
	{
	    return $this->nu_topeD1_2;
	}

	public function setNu_topeD1_2($nu_topeD1_2)
	{
	    $this->nu_topeD1_2 = $nu_topeD1_2;
	}

	public function getDs_descripcionD1_2()
	{
	    return $this->ds_descripcionD1_2;
	}

	public function setDs_descripcionD1_2($ds_descripcionD1_2)
	{
	    $this->ds_descripcionD1_2 = $ds_descripcionD1_2;
	}

	public function getNu_puntajeD1_2()
	{
	    return $this->nu_puntajeD1_2;
	}

	public function setNu_puntajeD1_2($nu_puntajeD1_2)
	{
	    $this->nu_puntajeD1_2 = $nu_puntajeD1_2;
	}

	public function getNu_puntajeantproduccionD1_2()
	{
	    return $this->nu_puntajeantproduccionD1_2;
	}

	public function setNu_puntajeantproduccionD1_2($nu_puntajeantproduccionD1_2)
	{
	    $this->nu_puntajeantproduccionD1_2 = $nu_puntajeantproduccionD1_2;
	}

	public function getDs_grupoD2()
	{
	    return $this->ds_grupoD2;
	}

	public function setDs_grupoD2($ds_grupoD2)
	{
	    $this->ds_grupoD2 = $ds_grupoD2;
	}

	public function getNu_topeD2_1()
	{
	    return $this->nu_topeD2_1;
	}

	public function setNu_topeD2_1($nu_topeD2_1)
	{
	    $this->nu_topeD2_1 = $nu_topeD2_1;
	}

	public function getDs_descripcionD2_1()
	{
	    return $this->ds_descripcionD2_1;
	}

	public function setDs_descripcionD2_1($ds_descripcionD2_1)
	{
	    $this->ds_descripcionD2_1 = $ds_descripcionD2_1;
	}

	public function getNu_puntajeD2_1()
	{
	    return $this->nu_puntajeD2_1;
	}

	public function setNu_puntajeD2_1($nu_puntajeD2_1)
	{
	    $this->nu_puntajeD2_1 = $nu_puntajeD2_1;
	}

	public function getNu_puntajeantproduccionD2_1()
	{
	    return $this->nu_puntajeantproduccionD2_1;
	}

	public function setNu_puntajeantproduccionD2_1($nu_puntajeantproduccionD2_1)
	{
	    $this->nu_puntajeantproduccionD2_1 = $nu_puntajeantproduccionD2_1;
	}

	public function getNu_topeD2_2()
	{
	    return $this->nu_topeD2_2;
	}

	public function setNu_topeD2_2($nu_topeD2_2)
	{
	    $this->nu_topeD2_2 = $nu_topeD2_2;
	}

	public function getDs_descripcionD2_2()
	{
	    return $this->ds_descripcionD2_2;
	}

	public function setDs_descripcionD2_2($ds_descripcionD2_2)
	{
	    $this->ds_descripcionD2_2 = $ds_descripcionD2_2;
	}

	public function getNu_puntajeD2_2()
	{
	    return $this->nu_puntajeD2_2;
	}

	public function setNu_puntajeD2_2($nu_puntajeD2_2)
	{
	    $this->nu_puntajeD2_2 = $nu_puntajeD2_2;
	}

	public function getNu_puntajeantproduccionD2_2()
	{
	    return $this->nu_puntajeantproduccionD2_2;
	}

	public function setNu_puntajeantproduccionD2_2($nu_puntajeantproduccionD2_2)
	{
	    $this->nu_puntajeantproduccionD2_2 = $nu_puntajeantproduccionD2_2;
	}

	public function getNu_topeD2_3()
	{
	    return $this->nu_topeD2_3;
	}

	public function setNu_topeD2_3($nu_topeD2_3)
	{
	    $this->nu_topeD2_3 = $nu_topeD2_3;
	}

	public function getDs_descripcionD2_3()
	{
	    return $this->ds_descripcionD2_3;
	}

	public function setDs_descripcionD2_3($ds_descripcionD2_3)
	{
	    $this->ds_descripcionD2_3 = $ds_descripcionD2_3;
	}

	public function getNu_puntajeD2_3()
	{
	    return $this->nu_puntajeD2_3;
	}

	public function setNu_puntajeD2_3($nu_puntajeD2_3)
	{
	    $this->nu_puntajeD2_3 = $nu_puntajeD2_3;
	}

	public function getNu_puntajeantproduccionD2_3()
	{
	    return $this->nu_puntajeantproduccionD2_3;
	}

	public function setNu_puntajeantproduccionD2_3($nu_puntajeantproduccionD2_3)
	{
	    $this->nu_puntajeantproduccionD2_3 = $nu_puntajeantproduccionD2_3;
	}

	public function getNu_topeD2_4()
	{
	    return $this->nu_topeD2_4;
	}

	public function setNu_topeD2_4($nu_topeD2_4)
	{
	    $this->nu_topeD2_4 = $nu_topeD2_4;
	}

	public function getDs_descripcionD2_4()
	{
	    return $this->ds_descripcionD2_4;
	}

	public function setDs_descripcionD2_4($ds_descripcionD2_4)
	{
	    $this->ds_descripcionD2_4 = $ds_descripcionD2_4;
	}

	public function getNu_puntajeD2_4()
	{
	    return $this->nu_puntajeD2_4;
	}

	public function setNu_puntajeD2_4($nu_puntajeD2_4)
	{
	    $this->nu_puntajeD2_4 = $nu_puntajeD2_4;
	}

	public function getNu_puntajeantproduccionD2_4()
	{
	    return $this->nu_puntajeantproduccionD2_4;
	}

	public function setNu_puntajeantproduccionD2_4($nu_puntajeantproduccionD2_4)
	{
	    $this->nu_puntajeantproduccionD2_4 = $nu_puntajeantproduccionD2_4;
	}

	public function getNu_topeD2_5()
	{
	    return $this->nu_topeD2_5;
	}

	public function setNu_topeD2_5($nu_topeD2_5)
	{
	    $this->nu_topeD2_5 = $nu_topeD2_5;
	}

	public function getDs_descripcionD2_5()
	{
	    return $this->ds_descripcionD2_5;
	}

	public function setDs_descripcionD2_5($ds_descripcionD2_5)
	{
	    $this->ds_descripcionD2_5 = $ds_descripcionD2_5;
	}

	public function getNu_puntajeD2_5()
	{
	    return $this->nu_puntajeD2_5;
	}

	public function setNu_puntajeD2_5($nu_puntajeD2_5)
	{
	    $this->nu_puntajeD2_5 = $nu_puntajeD2_5;
	}

	public function getNu_puntajeantproduccionD2_5()
	{
	    return $this->nu_puntajeantproduccionD2_5;
	}

	public function setNu_puntajeantproduccionD2_5($nu_puntajeantproduccionD2_5)
	{
	    $this->nu_puntajeantproduccionD2_5 = $nu_puntajeantproduccionD2_5;
	}

	public function getDs_grupoD3()
	{
	    return $this->ds_grupoD3;
	}

	public function setDs_grupoD3($ds_grupoD3)
	{
	    $this->ds_grupoD3 = $ds_grupoD3;
	}

	public function getDs_descripcionD3_1()
	{
	    return $this->ds_descripcionD3_1;
	}

	public function setDs_descripcionD3_1($ds_descripcionD3_1)
	{
	    $this->ds_descripcionD3_1 = $ds_descripcionD3_1;
	}

	public function getNu_puntajeD3_1()
	{
	    return $this->nu_puntajeD3_1;
	}

	public function setNu_puntajeD3_1($nu_puntajeD3_1)
	{
	    $this->nu_puntajeD3_1 = $nu_puntajeD3_1;
	}

	public function getNu_puntajeantproduccionD3_1()
	{
	    return $this->nu_puntajeantproduccionD3_1;
	}

	public function setNu_puntajeantproduccionD3_1($nu_puntajeantproduccionD3_1)
	{
	    $this->nu_puntajeantproduccionD3_1 = $nu_puntajeantproduccionD3_1;
	}

	public function getDs_descripcionD3_2()
	{
	    return $this->ds_descripcionD3_2;
	}

	public function setDs_descripcionD3_2($ds_descripcionD3_2)
	{
	    $this->ds_descripcionD3_2 = $ds_descripcionD3_2;
	}

	public function getNu_puntajeD3_2()
	{
	    return $this->nu_puntajeD3_2;
	}

	public function setNu_puntajeD3_2($nu_puntajeD3_2)
	{
	    $this->nu_puntajeD3_2 = $nu_puntajeD3_2;
	}

	public function getNu_puntajeantproduccionD3_2()
	{
	    return $this->nu_puntajeantproduccionD3_2;
	}

	public function setNu_puntajeantproduccionD3_2($nu_puntajeantproduccionD3_2)
	{
	    $this->nu_puntajeantproduccionD3_2 = $nu_puntajeantproduccionD3_2;
	}

	public function getDs_descripcionD3_3()
	{
	    return $this->ds_descripcionD3_3;
	}

	public function setDs_descripcionD3_3($ds_descripcionD3_3)
	{
	    $this->ds_descripcionD3_3 = $ds_descripcionD3_3;
	}

	public function getNu_puntajeD3_3()
	{
	    return $this->nu_puntajeD3_3;
	}

	public function setNu_puntajeD3_3($nu_puntajeD3_3)
	{
	    $this->nu_puntajeD3_3 = $nu_puntajeD3_3;
	}

	public function getNu_puntajeantproduccionD3_3()
	{
	    return $this->nu_puntajeantproduccionD3_3;
	}

	public function setNu_puntajeantproduccionD3_3($nu_puntajeantproduccionD3_3)
	{
	    $this->nu_puntajeantproduccionD3_3 = $nu_puntajeantproduccionD3_3;
	}

	public function getDs_descripcionD3_4()
	{
	    return $this->ds_descripcionD3_4;
	}

	public function setDs_descripcionD3_4($ds_descripcionD3_4)
	{
	    $this->ds_descripcionD3_4 = $ds_descripcionD3_4;
	}

	public function getNu_puntajeD3_4()
	{
	    return $this->nu_puntajeD3_4;
	}

	public function setNu_puntajeD3_4($nu_puntajeD3_4)
	{
	    $this->nu_puntajeD3_4 = $nu_puntajeD3_4;
	}

	public function getNu_puntajeantproduccionD3_4()
	{
	    return $this->nu_puntajeantproduccionD3_4;
	}

	public function setNu_puntajeantproduccionD3_4($nu_puntajeantproduccionD3_4)
	{
	    $this->nu_puntajeantproduccionD3_4 = $nu_puntajeantproduccionD3_4;
	}

	public function getDs_descripcionD3_5()
	{
	    return $this->ds_descripcionD3_5;
	}

	public function setDs_descripcionD3_5($ds_descripcionD3_5)
	{
	    $this->ds_descripcionD3_5 = $ds_descripcionD3_5;
	}

	public function getNu_puntajeD3_5()
	{
	    return $this->nu_puntajeD3_5;
	}

	public function setNu_puntajeD3_5($nu_puntajeD3_5)
	{
	    $this->nu_puntajeD3_5 = $nu_puntajeD3_5;
	}

	public function getNu_puntajeantproduccionD3_5()
	{
	    return $this->nu_puntajeantproduccionD3_5;
	}

	public function setNu_puntajeantproduccionD3_5($nu_puntajeantproduccionD3_5)
	{
	    $this->nu_puntajeantproduccionD3_5 = $nu_puntajeantproduccionD3_5;
	}

	public function getDs_grupoD4()
	{
	    return $this->ds_grupoD4;
	}

	public function setDs_grupoD4($ds_grupoD4)
	{
	    $this->ds_grupoD4 = $ds_grupoD4;
	}

	public function getDs_descripcionD4_1()
	{
	    return $this->ds_descripcionD4_1;
	}

	public function setDs_descripcionD4_1($ds_descripcionD4_1)
	{
	    $this->ds_descripcionD4_1 = $ds_descripcionD4_1;
	}

	public function getNu_puntajeD4_1()
	{
	    return $this->nu_puntajeD4_1;
	}

	public function setNu_puntajeD4_1($nu_puntajeD4_1)
	{
	    $this->nu_puntajeD4_1 = $nu_puntajeD4_1;
	}

	public function getNu_puntajeantproduccionD4_1()
	{
	    return $this->nu_puntajeantproduccionD4_1;
	}

	public function setNu_puntajeantproduccionD4_1($nu_puntajeantproduccionD4_1)
	{
	    $this->nu_puntajeantproduccionD4_1 = $nu_puntajeantproduccionD4_1;
	}

	public function getDs_grupoD5()
	{
	    return $this->ds_grupoD5;
	}

	public function setDs_grupoD5($ds_grupoD5)
	{
	    $this->ds_grupoD5 = $ds_grupoD5;
	}

	public function getDs_descripcionD5_1()
	{
	    return $this->ds_descripcionD5_1;
	}

	public function setDs_descripcionD5_1($ds_descripcionD5_1)
	{
	    $this->ds_descripcionD5_1 = $ds_descripcionD5_1;
	}

	public function getNu_puntajeD5_1()
	{
	    return $this->nu_puntajeD5_1;
	}

	public function setNu_puntajeD5_1($nu_puntajeD5_1)
	{
	    $this->nu_puntajeD5_1 = $nu_puntajeD5_1;
	}

	public function getNu_puntajeantproduccionD5_1()
	{
	    return $this->nu_puntajeantproduccionD5_1;
	}

	public function setNu_puntajeantproduccionD5_1($nu_puntajeantproduccionD5_1)
	{
	    $this->nu_puntajeantproduccionD5_1 = $nu_puntajeantproduccionD5_1;
	}

	public function getNu_cantantproduccionD5_1()
	{
	    return $this->nu_cantantproduccionD5_1;
	}

	public function setNu_cantantproduccionD5_1($nu_cantantproduccionD5_1)
	{
	    $this->nu_cantantproduccionD5_1 = $nu_cantantproduccionD5_1;
	}

	public function getDs_descripcionD5_2()
	{
	    return $this->ds_descripcionD5_2;
	}

	public function setDs_descripcionD5_2($ds_descripcionD5_2)
	{
	    $this->ds_descripcionD5_2 = $ds_descripcionD5_2;
	}

	public function getNu_puntajeD5_2()
	{
	    return $this->nu_puntajeD5_2;
	}

	public function setNu_puntajeD5_2($nu_puntajeD5_2)
	{
	    $this->nu_puntajeD5_2 = $nu_puntajeD5_2;
	}

	public function getNu_cantantproduccionD5_2()
	{
	    return $this->nu_cantantproduccionD5_2;
	}

	public function setNu_cantantproduccionD5_2($nu_cantantproduccionD5_2)
	{
	    $this->nu_cantantproduccionD5_2 = $nu_cantantproduccionD5_2;
	}

	public function getNu_puntajeantproduccionD5_2()
	{
	    return $this->nu_puntajeantproduccionD5_2;
	}

	public function setNu_puntajeantproduccionD5_2($nu_puntajeantproduccionD5_2)
	{
	    $this->nu_puntajeantproduccionD5_2 = $nu_puntajeantproduccionD5_2;
	}

	public function getDs_descripcionD5_3()
	{
	    return $this->ds_descripcionD5_3;
	}

	public function setDs_descripcionD5_3($ds_descripcionD5_3)
	{
	    $this->ds_descripcionD5_3 = $ds_descripcionD5_3;
	}

	public function getNu_puntajeD5_3()
	{
	    return $this->nu_puntajeD5_3;
	}

	public function setNu_puntajeD5_3($nu_puntajeD5_3)
	{
	    $this->nu_puntajeD5_3 = $nu_puntajeD5_3;
	}

	public function getNu_cantantproduccionD5_3()
	{
	    return $this->nu_cantantproduccionD5_3;
	}

	public function setNu_cantantproduccionD5_3($nu_cantantproduccionD5_3)
	{
	    $this->nu_cantantproduccionD5_3 = $nu_cantantproduccionD5_3;
	}

	public function getNu_puntajeantproduccionD5_3()
	{
	    return $this->nu_puntajeantproduccionD5_3;
	}

	public function setNu_puntajeantproduccionD5_3($nu_puntajeantproduccionD5_3)
	{
	    $this->nu_puntajeantproduccionD5_3 = $nu_puntajeantproduccionD5_3;
	}

	public function getDs_grupoD6()
	{
	    return $this->ds_grupoD6;
	}

	public function setDs_grupoD6($ds_grupoD6)
	{
	    $this->ds_grupoD6 = $ds_grupoD6;
	}

	public function getDs_descripcionD6_1()
	{
	    return $this->ds_descripcionD6_1;
	}

	public function setDs_descripcionD6_1($ds_descripcionD6_1)
	{
	    $this->ds_descripcionD6_1 = $ds_descripcionD6_1;
	}

	public function getNu_puntajeD6_1()
	{
	    return $this->nu_puntajeD6_1;
	}

	public function setNu_puntajeD6_1($nu_puntajeD6_1)
	{
	    $this->nu_puntajeD6_1 = $nu_puntajeD6_1;
	}

	public function getNu_puntajeantproduccionD6_1()
	{
	    return $this->nu_puntajeantproduccionD6_1;
	}

	public function setNu_puntajeantproduccionD6_1($nu_puntajeantproduccionD6_1)
	{
	    $this->nu_puntajeantproduccionD6_1 = $nu_puntajeantproduccionD6_1;
	}

	public function getDs_descripcionD6_2()
	{
	    return $this->ds_descripcionD6_2;
	}

	public function setDs_descripcionD6_2($ds_descripcionD6_2)
	{
	    $this->ds_descripcionD6_2 = $ds_descripcionD6_2;
	}

	public function getNu_puntajeD6_2()
	{
	    return $this->nu_puntajeD6_2;
	}

	public function setNu_puntajeD6_2($nu_puntajeD6_2)
	{
	    $this->nu_puntajeD6_2 = $nu_puntajeD6_2;
	}

	public function getNu_puntajeantproduccionD6_2()
	{
	    return $this->nu_puntajeantproduccionD6_2;
	}

	public function setNu_puntajeantproduccionD6_2($nu_puntajeantproduccionD6_2)
	{
	    $this->nu_puntajeantproduccionD6_2 = $nu_puntajeantproduccionD6_2;
	}

	public function getDs_descripcionD6_3()
	{
	    return $this->ds_descripcionD6_3;
	}

	public function setDs_descripcionD6_3($ds_descripcionD6_3)
	{
	    $this->ds_descripcionD6_3 = $ds_descripcionD6_3;
	}

	public function getNu_puntajeD6_3()
	{
	    return $this->nu_puntajeD6_3;
	}

	public function setNu_puntajeD6_3($nu_puntajeD6_3)
	{
	    $this->nu_puntajeD6_3 = $nu_puntajeD6_3;
	}

	public function getNu_puntajeantproduccionD6_3()
	{
	    return $this->nu_puntajeantproduccionD6_3;
	}

	public function setNu_puntajeantproduccionD6_3($nu_puntajeantproduccionD6_3)
	{
	    $this->nu_puntajeantproduccionD6_3 = $nu_puntajeantproduccionD6_3;
	}

	public function getDs_descripcionD6_4()
	{
	    return $this->ds_descripcionD6_4;
	}

	public function setDs_descripcionD6_4($ds_descripcionD6_4)
	{
	    $this->ds_descripcionD6_4 = $ds_descripcionD6_4;
	}

	public function getNu_puntajeD6_4()
	{
	    return $this->nu_puntajeD6_4;
	}

	public function setNu_puntajeD6_4($nu_puntajeD6_4)
	{
	    $this->nu_puntajeD6_4 = $nu_puntajeD6_4;
	}

	public function getNu_puntajeantproduccionD6_4()
	{
	    return $this->nu_puntajeantproduccionD6_4;
	}

	public function setNu_puntajeantproduccionD6_4($nu_puntajeantproduccionD6_4)
	{
	    $this->nu_puntajeantproduccionD6_4 = $nu_puntajeantproduccionD6_4;
	}

	public function getNu_topeD7()
	{
	    return $this->nu_topeD7;
	}

	public function setNu_topeD7($nu_topeD7)
	{
	    $this->nu_topeD7 = $nu_topeD7;
	}

	public function getDs_descripcionD7()
	{
	    return $this->ds_descripcionD7;
	}

	public function setDs_descripcionD7($ds_descripcionD7)
	{
	    $this->ds_descripcionD7 = $ds_descripcionD7;
	}

	public function getNu_puntajeD7()
	{
	    return $this->nu_puntajeD7;
	}

	public function setNu_puntajeD7($nu_puntajeD7)
	{
	    $this->nu_puntajeD7 = $nu_puntajeD7;
	}

	public function getNu_puntajeantproduccionD7()
	{
	    return $this->nu_puntajeantproduccionD7;
	}

	public function setNu_puntajeantproduccionD7($nu_puntajeantproduccionD7)
	{
	    $this->nu_puntajeantproduccionD7 = $nu_puntajeantproduccionD7;
	}

	public function getDs_descripcionD8()
	{
	    return $this->ds_descripcionD8;
	}

	public function setDs_descripcionD8($ds_descripcionD8)
	{
	    $this->ds_descripcionD8 = $ds_descripcionD8;
	}

	public function getNu_puntajeD8()
	{
	    return $this->nu_puntajeD8;
	}

	public function setNu_puntajeD8($nu_puntajeD8)
	{
	    $this->nu_puntajeD8 = $nu_puntajeD8;
	}

	public function getNu_puntajeantproduccionD8()
	{
	    return $this->nu_puntajeantproduccionD8;
	}

	public function setNu_puntajeantproduccionD8($nu_puntajeantproduccionD8)
	{
	    $this->nu_puntajeantproduccionD8 = $nu_puntajeantproduccionD8;
	}

	public function getNu_antjustificacionmaximo()
	{
	    return $this->nu_antjustificacionmaximo;
	}

	public function setNu_antjustificacionmaximo($nu_antjustificacionmaximo)
	{
	    $this->nu_antjustificacionmaximo = $nu_antjustificacionmaximo;
	}

	public function getNu_topeE1()
	{
	    return $this->nu_topeE1;
	}

	public function setNu_topeE1($nu_topeE1)
	{
	    $this->nu_topeE1 = $nu_topeE1;
	}

	public function getDs_descripcionE1()
	{
	    return $this->ds_descripcionE1;
	}

	public function setDs_descripcionE1($ds_descripcionE1)
	{
	    $this->ds_descripcionE1 = $ds_descripcionE1;
	}

	public function getNu_puntajeE1()
	{
	    return $this->nu_puntajeE1;
	}

	public function setNu_puntajeE1($nu_puntajeE1)
	{
	    $this->nu_puntajeE1 = $nu_puntajeE1;
	}

	public function getNu_puntajeantjustificacionE1()
	{
	    return $this->nu_puntajeantjustificacionE1;
	}

	public function setNu_puntajeantjustificacionE1($nu_puntajeantjustificacionE1)
	{
	    $this->nu_puntajeantjustificacionE1 = $nu_puntajeantjustificacionE1;
	}

	public function getDs_grupoD7()
	{
	    return $this->ds_grupoD7;
	}

	public function setDs_grupoD7($ds_grupoD7)
	{
	    $this->ds_grupoD7 = $ds_grupoD7;
	}

	public function getDs_grupoD8()
	{
	    return $this->ds_grupoD8;
	}

	public function setDs_grupoD8($ds_grupoD8)
	{
	    $this->ds_grupoD8 = $ds_grupoD8;
	}

	public function getPeriodo()
	{
	    return $this->periodo;
	}

	public function setPeriodo($periodo)
	{
	    $this->periodo = $periodo;
	}
}
?>