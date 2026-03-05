<?php

/**
 * ProyectoAgencia
 *
 * @author Marcos
 * @since 14-08-2023
 */

class ProyectoAgencia extends Entity{

	//variables de instancia.

	
	private $dt_ini;
	
	private $dt_fin;
	
	private $ds_codigo;
	
	private $ds_titulo;
	
	private $director;

    private $ds_organismo;
	


	public function __construct(){
		 
	
			
		$this->dt_ini = "";
		
		$this->dt_fin = "";
		
		$this->ds_codigo = "";
		
		$this->ds_titulo = "";

        $this->ds_organismo = "";
		
		$this->director = new Docente();


		
		
	}

    /**
     * @return string
     */
    public function getDt_ini()
    {
        return $this->dt_ini;
    }

    /**
     * @param $dt_ini
     */
    public function setDt_ini($dt_ini)
    {
        $this->dt_ini = $dt_ini;
    }

    /**
     * @return string
     */
    public function getDt_fin()
    {
        return $this->dt_fin;
    }

    /**
     * @param $dt_fin
     */
    public function setDt_fin($dt_fin)
    {
        $this->dt_fin = $dt_fin;
    }

    /**
     * @return string
     */
    public function getDs_codigo()
    {
        return $this->ds_codigo;
    }

    /**
     * @param $ds_codigo
     */
    public function setDs_codigo($ds_codigo)
    {
        $this->ds_codigo = $ds_codigo;
    }

    /**
     * @return string
     */
    public function getDs_titulo()
    {
        return $this->ds_titulo;
    }

    /**
     * @param $ds_titulo
     */
    public function setDs_titulo($ds_titulo)
    {
        $this->ds_titulo = $ds_titulo;
    }

    /**
     * @return string
     */
    public function getDirector()
    {
        return $this->director;
    }

    /**
     * @param $ds_director
     */
    public function setDirector($director)
    {
        $this->director = $director;
    }

    /**
     * @return string
     */
    public function getDs_organismo()
    {
        return $this->ds_organismo;
    }

    /**
     * @param $ds_organismo
     */
    public function setDs_organismo($ds_organismo)
    {
        $this->ds_organismo = $ds_organismo;
    }






	

	 

	


}
?>