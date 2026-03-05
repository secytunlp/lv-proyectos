<?php

/**
 * Solicitud
 *
 * @author Marcos
 * @since 13-11-2013
 */

class Solicitud extends Entity{

    //variables de instancia.

    private $estado;

    private $docente;
    private $periodo;
    private $ds_investigador;
    private $nu_cuil;
    private $ds_mail;
    private $nu_telefono;
    private $dt_fecha;
    private $ds_calle;
    private $nu_nro;
    private $nu_piso;
    private $ds_depto;
    private $nu_cp;
    private $dt_nacimiento;
    private $titulo;
    private $ds_titulogrado;
    private $dt_egresogrado;
    private $tituloposgrado;
    private $ds_tituloposgrado;
    private $dt_egresoposgrado;
    private $bl_doctorado;
    private $lugarTrabajo;
    private $cargo;
    private $deddoc;
    private $facultad;
    private $bl_becario;
    private $bl_unlp;
    private $ds_tipobeca;
    private $ds_orgbeca;
    private $lugarTrabajoBeca;
    private $dt_becadesde;
    private $dt_becahasta;
    private $ds_resbeca;


    private $facultadplanilla;
    private $bl_carrera;
    private $carrerainv;
    private $organismo;
    private $lugarTrabajoCarrera;
    private $dt_ingreso;
    private $ds_rescarrera;


    private $categoria;
    private $equivalencia;
    private $categoriasicadi;
    private $bl_director;


    private $ds_objetivo;
    private $ds_curriculum;
    private $ds_justificacion;
    private $nu_puntaje;
    private $nu_diferencia;


    private $ds_observaciones;
    private $bl_notificacion;

    private $cat;

    private $proyectos;

    private $proyectosAgencia;

    private $cargos;

    private $becas;

    private $otrosProyectos;

    private $ds_disciplina;

    private $ds_codigoproyecto;
    private $ds_organismoproyecto;
    private $ds_directorproyecto;
    private $ds_tituloproyecto;
    private $dt_proyectodesde;
    private $dt_proyectohasta;


    private $ds_archivo;

    private $ds_otromail;
    private $ds_genero;
    private $ds_foto;
    private $ds_orcid;
    private $ds_sedici;
    private $ds_scholar;
    private $ds_instagram;
    private $ds_twitter;
    private $ds_facebook;


private $ds_scopus;
private $ds_experticiaD;
private $ds_claveD1;
private $ds_claveD2;
private $ds_claveD3;
private $ds_claveD4;
private $ds_claveD5;
private $ds_claveD6;
private $ds_experticiaB;
private $ds_claveB1;
private $ds_claveB2;
private $ds_claveB3;
private $ds_claveB4;
private $ds_claveB5;
private $ds_claveB6;
private $ds_experticiaC;
private $ds_claveC1;
private $ds_claveC2;
private $ds_claveC3;
private $ds_claveC4;
private $ds_claveC5;
private $ds_claveC6;

    private $ds_informe1;
    private $ds_informe2;
    private $ds_informe3;

    private $nu_year1;
    private $nu_year2;
    private $nu_year3;

    private $areabeca;
    private $subareabeca;

    private $areacarrera;
    private $subareacarrera;


    public function __construct(){


        $this->docente = new Docente();

        $this->periodo = new Periodo();

        $this->cat = new Cat();

        $this->ds_investigador = '';

        $this->nu_cuil = '';

        $this->ds_mail = '';
        $this->nu_telefono = '';
        $this->dt_fecha = '';
        $this->dt_nacimiento = '';

        $this->ds_calle = '';
        $this->nu_nro = '';
        $this->nu_piso = '';
        $this->ds_depto = '';
        $this->nu_cp = '';
        $this->ds_titulogrado = '';
        $this->titulo = new Titulo();
        //$this->dt_grado = '';
        $this->tituloposgrado = new Titulo();
        $this->ds_tituloposgrado = '';
        //$this->dt_posgrado = '';
        $this->bl_doctorado = 0;
        $this->lugarTrabajo = new LugarTrabajo();

        $this->lugarTrabajoBeca = new LugarTrabajo();
        $this->lugarTrabajoCarrera = new LugarTrabajo();


        $this->cargo = new Cargo();

        $this->deddoc = New DedDoc();

        $this->facultad = new Facultad();
        $this->facultadplanilla = new Facultad();

        $this->bl_becario = 0;
        $this->bl_unlp = 0;
        $this->ds_tipobeca = '';
        $this->ds_orgbeca = '';

        $this->dt_becadesde = '';
        $this->dt_becahasta = '';
        $this->bl_carrera = 0;
        $this->carrerainv = new CarreraInv();

        $this->organismo = new Organismo();


        $this->dt_ingreso = '';
        $this->categoria = new Categoria();
        $this->categoriasicadi = new Categoriasicadi();
        $this->equivalencia = new Equivalencia();

        $this->bl_director = 0;

        $this->ds_objetivo = '';

        $this->ds_justificacion = '';



        $this->ds_observaciones = '';
        $this->bl_notificacion = 0;


        $this->proyectos= new ItemCollection();
        $this->proyectosAgencia= new ItemCollection();
        $this->becas= new ItemCollection();
        $this->otrosProyectos= new ItemCollection();

        $this->cargos= new ItemCollection();

        $this->ds_disciplina = "";

        $this->areabeca = new Area();
        $this->subareabeca = new Subarea();

        $this->areacarrera = new Area();
        $this->subareacarrera = new Subarea();

    }

    /**
     * @return ItemCollection
     */
    public function getCargos()
    {
        return $this->cargos;
    }

    /**
     * @param ItemCollection $cargos
     */
    public function setCargos($cargos)
    {
        $this->cargos = $cargos;
    }













    public function getEstado()
    {
        return $this->estado;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    public function getDocente()
    {
        return $this->docente;
    }

    public function setDocente($docente)
    {
        $this->docente = $docente;
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

    public function getNu_cuil()
    {
        return $this->nu_cuil;
    }

    public function setNu_cuil($nu_cuil)
    {
        $this->nu_cuil = $nu_cuil;
    }

    public function getDs_mail()
    {
        return $this->ds_mail;
    }

    public function setDs_mail($ds_mail)
    {
        $this->ds_mail = $ds_mail;
    }

    public function getNu_telefono()
    {
        return $this->nu_telefono;
    }

    public function setNu_telefono($nu_telefono)
    {
        $this->nu_telefono = $nu_telefono;
    }

    public function getDt_fecha()
    {
        return $this->dt_fecha;
    }

    public function setDt_fecha($dt_fecha)
    {
        $this->dt_fecha = $dt_fecha;
    }

    public function getDs_calle()
    {
        return $this->ds_calle;
    }

    public function setDs_calle($ds_calle)
    {
        $this->ds_calle = $ds_calle;
    }

    public function getNu_nro()
    {
        return $this->nu_nro;
    }

    public function setNu_nro($nu_nro)
    {
        $this->nu_nro = $nu_nro;
    }

    public function getNu_piso()
    {
        return $this->nu_piso;
    }

    public function setNu_piso($nu_piso)
    {
        $this->nu_piso = $nu_piso;
    }

    public function getDs_depto()
    {
        return $this->ds_depto;
    }

    public function setDs_depto($ds_depto)
    {
        $this->ds_depto = $ds_depto;
    }

    public function getNu_cp()
    {
        return $this->nu_cp;
    }

    public function setNu_cp($nu_cp)
    {
        $this->nu_cp = $nu_cp;
    }

    public function getDt_nacimiento()
    {
        return $this->dt_nacimiento;
    }

    public function setDt_nacimiento($dt_nacimiento)
    {
        $this->dt_nacimiento = $dt_nacimiento;
    }

    public function getTitulo()
    {
        return $this->titulo;
    }

    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }

    public function getDs_titulogrado()
    {
        return $this->ds_titulogrado;
    }

    public function setDs_titulogrado($ds_titulogrado)
    {
        $this->ds_titulogrado = $ds_titulogrado;
    }

    public function getDt_egresogrado()
    {
        return $this->dt_egresogrado;
    }

    public function setDt_egresogrado($dt_egresogrado)
    {
        $this->dt_egresogrado = $dt_egresogrado;
    }

    public function getTituloposgrado()
    {
        return $this->tituloposgrado;
    }

    public function setTituloposgrado($tituloposgrado)
    {
        $this->tituloposgrado = $tituloposgrado;
    }

    public function getDs_tituloposgrado()
    {
        return $this->ds_tituloposgrado;
    }

    public function setDs_tituloposgrado($ds_tituloposgrado)
    {
        $this->ds_tituloposgrado = $ds_tituloposgrado;
    }

    public function getDt_egresoposgrado()
    {
        return $this->dt_egresoposgrado;
    }

    public function setDt_egresoposgrado($dt_egresoposgrado)
    {
        $this->dt_egresoposgrado = $dt_egresoposgrado;
    }

    public function getBl_doctorado()
    {
        return $this->bl_doctorado;
    }

    public function setBl_doctorado($bl_doctorado)
    {
        $this->bl_doctorado = $bl_doctorado;
    }

    public function getLugarTrabajo()
    {
        return $this->lugarTrabajo;
    }

    public function setLugarTrabajo($lugarTrabajo)
    {
        $this->lugarTrabajo = $lugarTrabajo;
    }

    public function getCargo()
    {
        return $this->cargo;
    }

    public function setCargo($cargo)
    {
        $this->cargo = $cargo;
    }

    public function getDeddoc()
    {
        return $this->deddoc;
    }

    public function setDeddoc($deddoc)
    {
        $this->deddoc = $deddoc;
    }

    public function getFacultad()
    {
        return $this->facultad;
    }

    public function setFacultad($facultad)
    {
        $this->facultad = $facultad;
    }

    public function getBl_becario()
    {
        return $this->bl_becario;
    }

    public function setBl_becario($bl_becario)
    {
        $this->bl_becario = $bl_becario;
    }

    public function getBl_unlp()
    {
        return $this->bl_unlp;
    }

    public function setBl_unlp($bl_unlp)
    {
        $this->bl_unlp = $bl_unlp;
    }

    public function getDs_tipobeca()
    {
        return $this->ds_tipobeca;
    }

    public function setDs_tipobeca($ds_tipobeca)
    {
        $this->ds_tipobeca = $ds_tipobeca;
    }

    public function getDs_orgbeca()
    {
        return $this->ds_orgbeca;
    }

    public function setDs_orgbeca($ds_orgbeca)
    {
        $this->ds_orgbeca = $ds_orgbeca;
    }

    public function getLugarTrabajoBeca()
    {
        return $this->lugarTrabajoBeca;
    }

    public function setLugarTrabajoBeca($lugarTrabajoBeca)
    {
        $this->lugarTrabajoBeca = $lugarTrabajoBeca;
    }

    public function getDt_becadesde()
    {
        return $this->dt_becadesde;
    }

    public function setDt_becadesde($dt_becadesde)
    {
        $this->dt_becadesde = $dt_becadesde;
    }

    public function getDt_becahasta()
    {
        return $this->dt_becahasta;
    }

    public function setDt_becahasta($dt_becahasta)
    {
        $this->dt_becahasta = $dt_becahasta;
    }

    public function getFacultadplanilla()
    {
        return $this->facultadplanilla;
    }

    public function setFacultadplanilla($facultadplanilla)
    {
        $this->facultadplanilla = $facultadplanilla;
    }

    public function getBl_carrera()
    {
        return $this->bl_carrera;
    }

    public function setBl_carrera($bl_carrera)
    {
        $this->bl_carrera = $bl_carrera;
    }

    public function getCarrerainv()
    {
        return $this->carrerainv;
    }

    public function setCarrerainv($carrerainv)
    {
        $this->carrerainv = $carrerainv;
    }

    public function getOrganismo()
    {
        return $this->organismo;
    }

    public function setOrganismo($organismo)
    {
        $this->organismo = $organismo;
    }

    public function getLugarTrabajoCarrera()
    {
        return $this->lugarTrabajoCarrera;
    }

    public function setLugarTrabajoCarrera($lugarTrabajoCarrera)
    {
        $this->lugarTrabajoCarrera = $lugarTrabajoCarrera;
    }

    public function getDt_ingreso()
    {
        return $this->dt_ingreso;
    }

    public function setDt_ingreso($dt_ingreso)
    {
        $this->dt_ingreso = $dt_ingreso;
    }

    public function getCategoria()
    {
        return $this->categoria;
    }

    public function setCategoria($categoria)
    {
        $this->categoria = $categoria;
    }

    public function getBl_director()
    {
        return $this->bl_director;
    }

    public function setBl_director($bl_director)
    {
        $this->bl_director = $bl_director;
    }

    public function getDs_objetivo()
    {
        return $this->ds_objetivo;
    }

    public function setDs_objetivo($ds_objetivo)
    {
        $this->ds_objetivo = $ds_objetivo;
    }

    public function getDs_curriculum()
    {
        return $this->ds_curriculum;
    }

    public function setDs_curriculum($ds_curriculum)
    {
        $this->ds_curriculum = $ds_curriculum;
    }

    public function getDs_justificacion()
    {
        return $this->ds_justificacion;
    }

    public function setDs_justificacion($ds_justificacion)
    {
        $this->ds_justificacion = $ds_justificacion;
    }

    public function getNu_puntaje()
    {
        return $this->nu_puntaje;
    }

    public function setNu_puntaje($nu_puntaje)
    {
        $this->nu_puntaje = $nu_puntaje;
    }

    public function getNu_diferencia()
    {
        return $this->nu_diferencia;
    }

    public function setNu_diferencia($nu_diferencia)
    {
        $this->nu_diferencia = $nu_diferencia;
    }

    public function getPresupuestos()
    {
        return $this->presupuestos;
    }

    public function setPresupuestos($presupuestos)
    {
        $this->presupuestos = $presupuestos;
    }

    public function getDs_observaciones()
    {
        return $this->ds_observaciones;
    }

    public function setDs_observaciones($ds_observaciones)
    {
        $this->ds_observaciones = $ds_observaciones;
    }

    public function getBl_notificacion()
    {
        return $this->bl_notificacion;
    }

    public function setBl_notificacion($bl_notificacion)
    {
        $this->bl_notificacion = $bl_notificacion;
    }

    public function getCat()
    {
        return $this->cat;
    }

    public function setCat($cat)
    {
        $this->cat = $cat;
    }

    public function getProyectos()
    {
        return $this->proyectos;
    }

    public function setProyectos($proyectos)
    {
        $this->proyectos = $proyectos;
    }

    public function getBecas()
    {
        return $this->becas;
    }

    public function setBecas($becas)
    {
        $this->becas = $becas;
    }

    public function getOtrosProyectos()
    {
        return $this->otrosProyectos;
    }

    public function setOtrosProyectos($otrosProyectos)
    {
        $this->otrosProyectos = $otrosProyectos;
    }

    public function __toString(){

        return $this->getDocente()->getDs_apellido().' '.$this->getDocente()->getDs_nombre();
    }



    public function getDs_disciplina()
    {
        return $this->ds_disciplina;
    }

    public function setDs_disciplina($ds_disciplina)
    {
        $this->ds_disciplina = $ds_disciplina;
    }

    /**
     * @return mixed
     */
    public function getDs_resbeca()
    {
        return $this->ds_resbeca;
    }

    /**
     * @param mixed $ds_resbeca
     */
    public function setDs_resbeca($ds_resbeca)
    {
        $this->ds_resbeca = $ds_resbeca;
    }

    /**
     * @return mixed
     */
    public function getDs_rescarrera()
    {
        return $this->ds_rescarrera;
    }

    /**
     * @param mixed $ds_rescarrera
     */
    public function setDs_rescarrera($ds_rescarrera)
    {
        $this->ds_rescarrera = $ds_rescarrera;
    }

    /**
     * @return mixed
     */
    public function getDs_archivo()
    {
        return $this->ds_archivo;
    }

    /**
     * @param mixed $ds_archivo
     */
    public function setDs_archivo($ds_archivo)
    {
        $this->ds_archivo = $ds_archivo;
    }
    /**
     * @return mixed
     */
    public function getDs_codigoproyecto()
    {
        return $this->ds_codigoproyecto;
    }

    /**
     * @param mixed $ds_codigoproyecto
     */
    public function setDs_codigoproyecto($ds_codigoproyecto)
    {
        $this->ds_codigoproyecto = $ds_codigoproyecto;
    }

    /**
     * @return mixed
     */
    public function getDs_organismoproyecto()
    {
        return $this->ds_organismoproyecto;
    }

    /**
     * @param mixed $ds_organismoproyecto
     */
    public function setDs_organismoproyecto($ds_organismoproyecto)
    {
        $this->ds_organismoproyecto = $ds_organismoproyecto;
    }

    /**
     * @return mixed
     */
    public function getDs_directorproyecto()
    {
        return $this->ds_directorproyecto;
    }

    /**
     * @param mixed $ds_directorproyecto
     */
    public function setDs_directorproyecto($ds_directorproyecto)
    {
        $this->ds_directorproyecto = $ds_directorproyecto;
    }

    /**
     * @return mixed
     */
    public function getDs_tituloproyecto()
    {
        return $this->ds_tituloproyecto;
    }

    /**
     * @param mixed $ds_tituloproyecto
     */
    public function setDs_tituloproyecto($ds_tituloproyecto)
    {
        $this->ds_tituloproyecto = $ds_tituloproyecto;
    }

    /**
     * @return mixed
     */
    public function getDt_proyectodesde()
    {
        return $this->dt_proyectodesde;
    }

    /**
     * @param mixed $dt_proyectodesde
     */
    public function setDt_proyectodesde($dt_proyectodesde)
    {
        $this->dt_proyectodesde = $dt_proyectodesde;
    }

    /**
     * @return mixed
     */
    public function getDt_proyectohasta()
    {
        return $this->dt_proyectohasta;
    }

    /**
     * @param mixed $dt_proyectohasta
     */
    public function setDt_proyectohasta($dt_proyectohasta)
    {
        $this->dt_proyectohasta = $dt_proyectohasta;
    }

    /**
     * @return mixed
     */
    public function getEquivalencia()
    {
        return $this->equivalencia;
    }

    /**
     * @param mixed $equivalencia
     */
    public function setEquivalencia($equivalencia)
    {
        $this->equivalencia = $equivalencia;
    }

    /**
     * @return mixed
     */
    public function getCategoriasicadi()
    {
        return $this->categoriasicadi;
    }

    /**
     * @param mixed $categoriasicadi
     */
    public function setCategoriasicadi($categoriasicadi)
    {
        //CYTSecureUtils::logObject($categoriasicadi);
        $this->categoriasicadi = $categoriasicadi;
    }

    /**
     * @return mixed
     */
    public function getDs_genero()
    {
        return $this->ds_genero;
    }

    /**
     * @param mixed $ds_genero
     */
    public function setDs_genero($ds_genero)
    {
        $this->ds_genero = $ds_genero;
    }

    /**
     * @return mixed
     */
    public function getDs_foto()
    {
        return $this->ds_foto;
    }

    /**
     * @param mixed $ds_foto
     */
    public function setDs_foto($ds_foto)
    {
        $this->ds_foto = $ds_foto;
    }

    /**
     * @return mixed
     */
    public function getDs_orcid()
    {
        return $this->ds_orcid;
    }

    /**
     * @param mixed $ds_orcid
     */
    public function setDs_orcid($ds_orcid)
    {
        $this->ds_orcid = $ds_orcid;
    }

    /**
     * @return mixed
     */
    public function getDs_scholar()
    {
        return $this->ds_scholar;
    }

    /**
     * @param mixed $ds_scholar
     */
    public function setDs_scholar($ds_scholar)
    {
        $this->ds_scholar = $ds_scholar;
    }

    /**
     * @return mixed
     */
    public function getDs_instagram()
    {
        return $this->ds_instagram;
    }

    /**
     * @param mixed $ds_instagram
     */
    public function setDs_instagram($ds_instagram)
    {
        $this->ds_instagram = $ds_instagram;
    }

    /**
     * @return mixed
     */
    public function getDs_twitter()
    {
        return $this->ds_twitter;
    }

    /**
     * @param mixed $ds_twitter
     */
    public function setDs_twitter($ds_twitter)
    {
        $this->ds_twitter = $ds_twitter;
    }

    /**
     * @return mixed
     */
    public function getDs_facebook()
    {
        return $this->ds_facebook;
    }

    /**
     * @param mixed $ds_facebook
     */
    public function setDs_facebook($ds_facebook)
    {
        $this->ds_facebook = $ds_facebook;
    }

    /**
     * @return mixed
     */
    public function getDs_otromail()
    {
        return $this->ds_otromail;
    }

    /**
     * @param mixed $ds_otromail
     */
    public function setDs_otromail($ds_otromail)
    {
        $this->ds_otromail = $ds_otromail;
    }

    /**
     * @return mixed
     */
    public function getDs_sedici()
    {
        return $this->ds_sedici;
    }

    /**
     * @param mixed $ds_sedici
     */
    public function setDs_sedici($ds_sedici)
    {
        $this->ds_sedici = $ds_sedici;
    }

    /**
     * @return mixed
     */
    public function getDs_scopus()
    {
        return $this->ds_scopus;
    }

    /**
     * @param mixed $ds_scopus
     */
    public function setDs_scopus($ds_scopus)
    {
        $this->ds_scopus = $ds_scopus;
    }

    /**
     * @return mixed
     */
    public function getDs_experticiaD()
    {
        return $this->ds_experticiaD;
    }

    /**
     * @param mixed $ds_experticiaD
     */
    public function setDs_experticiaD($ds_experticiaD)
    {
        $this->ds_experticiaD = $ds_experticiaD;
    }

    /**
     * @return mixed
     */
    public function getDs_claveD1()
    {
        return $this->ds_claveD1;
    }

    /**
     * @param mixed $ds_claveD1
     */
    public function setDs_claveD1($ds_claveD1)
    {
        $this->ds_claveD1 = $ds_claveD1;
    }

    /**
     * @return mixed
     */
    public function getDs_claveD2()
    {
        return $this->ds_claveD2;
    }

    /**
     * @param mixed $ds_claveD2
     */
    public function setDs_claveD2($ds_claveD2)
    {
        $this->ds_claveD2 = $ds_claveD2;
    }

    /**
     * @return mixed
     */
    public function getDs_claveD3()
    {
        return $this->ds_claveD3;
    }

    /**
     * @param mixed $ds_claveD3
     */
    public function setDs_claveD3($ds_claveD3)
    {
        $this->ds_claveD3 = $ds_claveD3;
    }

    /**
     * @return mixed
     */
    public function getDs_claveD4()
    {
        return $this->ds_claveD4;
    }

    /**
     * @param mixed $ds_claveD4
     */
    public function setDs_claveD4($ds_claveD4)
    {
        $this->ds_claveD4 = $ds_claveD4;
    }

    /**
     * @return mixed
     */
    public function getDs_claveD5()
    {
        return $this->ds_claveD5;
    }

    /**
     * @param mixed $ds_claveD5
     */
    public function setDs_claveD5($ds_claveD5)
    {
        $this->ds_claveD5 = $ds_claveD5;
    }

    /**
     * @return mixed
     */
    public function getDs_claveD6()
    {
        return $this->ds_claveD6;
    }

    /**
     * @param mixed $ds_claveD6
     */
    public function setDs_claveD6($ds_claveD6)
    {
        $this->ds_claveD6 = $ds_claveD6;
    }

    /**
     * @return mixed
     */
    public function getDs_experticiaB()
    {
        return $this->ds_experticiaB;
    }

    /**
     * @param mixed $ds_experticiaB
     */
    public function setDs_experticiaB($ds_experticiaB)
    {
        $this->ds_experticiaB = $ds_experticiaB;
    }

    /**
     * @return mixed
     */
    public function getDs_claveB1()
    {
        return $this->ds_claveB1;
    }

    /**
     * @param mixed $ds_claveB1
     */
    public function setDs_claveB1($ds_claveB1)
    {
        $this->ds_claveB1 = $ds_claveB1;
    }

    /**
     * @return mixed
     */
    public function getDs_claveB2()
    {
        return $this->ds_claveB2;
    }

    /**
     * @param mixed $ds_claveB2
     */
    public function setDs_claveB2($ds_claveB2)
    {
        $this->ds_claveB2 = $ds_claveB2;
    }

    /**
     * @return mixed
     */
    public function getDs_claveB3()
    {
        return $this->ds_claveB3;
    }

    /**
     * @param mixed $ds_claveB3
     */
    public function setDs_claveB3($ds_claveB3)
    {
        $this->ds_claveB3 = $ds_claveB3;
    }

    /**
     * @return mixed
     */
    public function getDs_claveB4()
    {
        return $this->ds_claveB4;
    }

    /**
     * @param mixed $ds_claveB4
     */
    public function setDs_claveB4($ds_claveB4)
    {
        $this->ds_claveB4 = $ds_claveB4;
    }

    /**
     * @return mixed
     */
    public function getDs_claveB5()
    {
        return $this->ds_claveB5;
    }

    /**
     * @param mixed $ds_claveB5
     */
    public function setDs_claveB5($ds_claveB5)
    {
        $this->ds_claveB5 = $ds_claveB5;
    }

    /**
     * @return mixed
     */
    public function getDs_claveB6()
    {
        return $this->ds_claveB6;
    }

    /**
     * @param mixed $ds_claveB6
     */
    public function setDs_claveB6($ds_claveB6)
    {
        $this->ds_claveB6 = $ds_claveB6;
    }

    /**
     * @return mixed
     */
    public function getDs_experticiaC()
    {
        return $this->ds_experticiaC;
    }

    /**
     * @param mixed $ds_experticiaC
     */
    public function setDs_experticiaC($ds_experticiaC)
    {
        $this->ds_experticiaC = $ds_experticiaC;
    }

    /**
     * @return mixed
     */
    public function getDs_claveC1()
    {
        return $this->ds_claveC1;
    }

    /**
     * @param mixed $ds_claveC1
     */
    public function setDs_claveC1($ds_claveC1)
    {
        $this->ds_claveC1 = $ds_claveC1;
    }

    /**
     * @return mixed
     */
    public function getDs_claveC2()
    {
        return $this->ds_claveC2;
    }

    /**
     * @param mixed $ds_claveC2
     */
    public function setDs_claveC2($ds_claveC2)
    {
        $this->ds_claveC2 = $ds_claveC2;
    }

    /**
     * @return mixed
     */
    public function getDs_claveC3()
    {
        return $this->ds_claveC3;
    }

    /**
     * @param mixed $ds_claveC3
     */
    public function setDs_claveC3($ds_claveC3)
    {
        $this->ds_claveC3 = $ds_claveC3;
    }

    /**
     * @return mixed
     */
    public function getDs_claveC4()
    {
        return $this->ds_claveC4;
    }

    /**
     * @param mixed $ds_claveC4
     */
    public function setDs_claveC4($ds_claveC4)
    {
        $this->ds_claveC4 = $ds_claveC4;
    }

    /**
     * @return mixed
     */
    public function getDs_claveC5()
    {
        return $this->ds_claveC5;
    }

    /**
     * @param mixed $ds_claveC5
     */
    public function setDs_claveC5($ds_claveC5)
    {
        $this->ds_claveC5 = $ds_claveC5;
    }

    /**
     * @return mixed
     */
    public function getDs_claveC6()
    {
        return $this->ds_claveC6;
    }

    /**
     * @param mixed $ds_claveC6
     */
    public function setDs_claveC6($ds_claveC6)
    {
        $this->ds_claveC6 = $ds_claveC6;
    }

    /**
     * @return mixed
     */
    public function getDs_informe1()
    {
        return $this->ds_informe1;
    }

    /**
     * @param mixed $ds_informe1
     */
    public function setDs_informe1($ds_informe1)
    {
        $this->ds_informe1 = $ds_informe1;
    }

    /**
     * @return mixed
     */
    public function getDs_informe2()
    {
        return $this->ds_informe2;
    }

    /**
     * @param mixed $ds_informe2
     */
    public function setDs_informe2($ds_informe2)
    {
        $this->ds_informe2 = $ds_informe2;
    }

    /**
     * @return mixed
     */
    public function getDs_informe3()
    {
        return $this->ds_informe3;
    }

    /**
     * @param mixed $ds_informe3
     */
    public function setDs_informe3($ds_informe3)
    {
        $this->ds_informe3 = $ds_informe3;
    }

    /**
     * @return mixed
     */
    public function getNu_year1()
    {
        return $this->nu_year1;
    }

    /**
     * @param mixed $nu_year1
     */
    public function setNu_year1($nu_year1)
    {
        $this->nu_year1 = $nu_year1;
    }

    /**
     * @return mixed
     */
    public function getNu_year2()
    {
        return $this->nu_year2;
    }

    /**
     * @param mixed $nu_year2
     */
    public function setNu_year2($nu_year2)
    {
        $this->nu_year2 = $nu_year2;
    }

    /**
     * @return mixed
     */
    public function getNu_year3()
    {
        return $this->nu_year3;
    }

    /**
     * @param mixed $nu_year3
     */
    public function setNu_year3($nu_year3)
    {
        $this->nu_year3 = $nu_year3;
    }

    /**
     * @return Area
     */
    public function getAreabeca()
    {
        return $this->areabeca;
    }

    /**
     * @param Area $areabeca
     */
    public function setAreabeca($areabeca)
    {
        $this->areabeca = $areabeca;
    }

    /**
     * @return Subarea
     */
    public function getSubareabeca()
    {
        return $this->subareabeca;
    }

    /**
     * @param Subarea $subareabeca
     */
    public function setSubareabeca($subareabeca)
    {
        $this->subareabeca = $subareabeca;
    }

    /**
     * @return Area
     */
    public function getAreacarrera()
    {
        return $this->areacarrera;
    }

    /**
     * @param Area $areacarrera
     */
    public function setAreacarrera($areacarrera)
    {
        $this->areacarrera = $areacarrera;
    }

    /**
     * @return Subarea
     */
    public function getSubareacarrera()
    {
        return $this->subareacarrera;
    }

    /**
     * @param Subarea $subareacarrera
     */
    public function setSubareacarrera( $subareacarrera)
    {
        $this->subareacarrera = $subareacarrera;
    }

    /**
     * @return ItemCollection
     */
    public function getProyectosAgencia()
    {
        return $this->proyectosAgencia;
    }

    /**
     * @param ItemCollection $proyectosAgencia
     */
    public function setProyectosAgencia( $proyectosAgencia)
    {
        $this->proyectosAgencia = $proyectosAgencia;
    }



}
?>