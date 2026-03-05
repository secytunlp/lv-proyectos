<?php

/**
 * Cargos Alfabetico
 *
 * @author Marcos
 * @since 09-06-2023
 */

class Alfabetico extends Entity{

	//variables de instancia.


	
	private $dni;
	

	  
	private $cargo;  
	
	private $dt_fecha;
	
	private $deddoc;
	
	private $facultad;

    private $clase;
	  
	private $escalafon;
	
	private $situacion;
	

	public function __construct(){
		 

		
		$this->cargo = new Cargo();
        $this->deddoc = new Deddoc();
        $this->facultad = new Facultad();
		  

		
	}

    /**
     * @return mixed
     */
    public function getDni()
    {
        return $this->dni;
    }

    /**
     * @param mixed $dni
     */
    public function setDni($dni)
    {
        $this->dni = $dni;
    }



    /**
     * @return Cargo
     */
    public function getCargo(): Cargo
    {
        return $this->cargo;
    }

    /**
     * @param Cargo $cargo
     */
    public function setCargo(Cargo $cargo)
    {
        $this->cargo = $cargo;
    }

    /**
     * @return mixed
     */
    public function getDt_fecha()
    {
        return $this->dt_fecha;
    }

    /**
     * @param mixed $dt_fecha
     */
    public function setDt_fecha($dt_fecha)
    {
        $this->dt_fecha = $dt_fecha;
    }

    /**
     * @return Deddoc
     */
    public function getDeddoc(): Deddoc
    {
        return $this->deddoc;
    }

    /**
     * @param Deddoc $deddoc
     */
    public function setDeddoc(Deddoc $deddoc)
    {
        $this->deddoc = $deddoc;
    }

    /**
     * @return Facultad
     */
    public function getFacultad(): Facultad
    {
        return $this->facultad;
    }

    /**
     * @param Facultad $facultad
     */
    public function setFacultad(Facultad $facultad)
    {
        $this->facultad = $facultad;
    }

    /**
     * @return mixed
     */
    public function getClase()
    {
        return $this->clase;
    }

    /**
     * @param mixed $clase
     */
    public function setClase($clase)
    {
        $this->clase = $clase;
    }

    /**
     * @return mixed
     */
    public function getEscalafon()
    {
        return $this->escalafon;
    }

    /**
     * @param mixed $escalafon
     */
    public function setEscalafon($escalafon)
    {
        $this->escalafon = $escalafon;
    }

    /**
     * @return mixed
     */
    public function getSituacion()
    {
        return $this->situacion;
    }

    /**
     * @param mixed $situacion
     */
    public function setSituacion($situacion)
    {
        $this->situacion = $situacion;
    }






	


}
?>