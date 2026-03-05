<?php

/**
 * IntegranteAgencia
 *
 * @author Marcos
 * @since 14-08-2023
 */

class IntegranteAgencia extends Entity{

	//variables de instancia.


    private $proyecto;
    private $dt_alta;
    private $dt_baja;

    private $tipoinvestigador;

    private $nu_documento;

    private $ds_tipo;

    private $docente;

	public function __construct(){



        $this->proyecto = new Proyecto();
        $this->dt_baja = '';
        $this->tipoinvestigador = new Tipoinvestigador();

        $this->dt_alta = '';

        $this->docente = new Docente();

		
		
	}

    /**
     * @return Proyecto
     */
    public function getProyecto()
    {
        return $this->proyecto;
    }

    /**
     * @param Proyecto $proyecto
     */
    public function setProyecto($proyecto)
    {
        $this->proyecto = $proyecto;
    }

    /**
     * @return string
     */
    public function getDt_alta()
    {
        return $this->dt_alta;
    }

    /**
     * @param $dt_alta
     */
    public function setDt_alta($dt_alta)
    {
        $this->dt_alta = $dt_alta;
    }

    /**
     * @return string
     */
    public function getDt_baja()
    {
        return $this->dt_baja;
    }

    /**
     * @param $dt_baja
     */
    public function setDt_baja($dt_baja)
    {
        $this->dt_baja = $dt_baja;
    }

    /**
     * @return Tipoinvestigador
     */
    public function getTipoinvestigador()
    {
        return $this->tipoinvestigador;
    }

    /**
     * @param Tipoinvestigador $tipoinvestigador
     */
    public function setTipoinvestigador($tipoinvestigador)
    {
        $this->tipoinvestigador = $tipoinvestigador;
    }

    /**
     * @return mixed
     */
    public function getNu_documento()
    {
        return $this->nu_documento;
    }

    /**
     * @param mixed $nu_documento
     */
    public function setNu_documento($nu_documento)
    {
        $this->nu_documento = $nu_documento;
    }

    /**
     * @return mixed
     */
    public function getDs_tipo()
    {
        return $this->ds_tipo;
    }

    /**
     * @param mixed $ds_tipo
     */
    public function setDs_tipo($ds_tipo)
    {
        $this->ds_tipo = $ds_tipo;
    }

    /**
     * @return Docente
     */
    public function getDocente()
    {
        return $this->docente;
    }

    /**
     * @param Docente $docente
     */
    public function setDocente($docente)
    {
        $this->docente = $docente;
    }



	

	 

	


}
?>