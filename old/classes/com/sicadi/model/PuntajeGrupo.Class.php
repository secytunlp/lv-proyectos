<?php

/**
 * PuntajeGrupo
 *  
 * @author Marcos
 * @since 06-12-2013
 */


class PuntajeGrupo  extends Entity{

    //variables de instancia.
    
	private $ds_puntajegrupo;
	
	
    
    private $nu_max;
    
    private $cd_grupopadre;
    

    public function __construct(){
    	
    	$this->ds_puntajegrupo = "";
    	
    	
    	$this->nu_max = "";
    }
    
    
   

	

	public function getDs_puntajegrupo()
	{
	    return $this->ds_puntajegrupo;
	}

	public function setDs_puntajegrupo($ds_puntajegrupo)
	{
	    $this->ds_puntajegrupo = $ds_puntajegrupo;
	}

	public function getNu_max()
	{
	    return $this->nu_max;
	}

	public function setNu_max($nu_max)
	{
	    $this->nu_max = $nu_max;
	}

	public function getCd_grupopadre()
	{
	    return $this->cd_grupopadre;
	}

	public function setCd_grupopadre($cd_grupopadre)
	{
	    $this->cd_grupopadre = $cd_grupopadre;
	}
}
?>