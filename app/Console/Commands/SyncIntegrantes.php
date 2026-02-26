<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncIntegrantes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:integrantes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza integrante desde DB origen a integrantes';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Iniciando sincronización...');
        $skippedRows = [];
        $totalFilas = 0;
        $totalInsertadas = 0;
        $totalOmitidas = 0;
        DB::connection('mysql_origen')
            ->table('integrante')
            ->leftJoin('proyecto', 'integrante.cd_proyecto', '=', 'proyecto.cd_proyecto')
            ->leftJoin('docente', 'integrante.cd_docente', '=', 'docente.cd_docente')
            ->leftJoin('deddoc', 'docente.cd_deddoc', '=', 'deddoc.cd_deddoc')

            ->selectRaw("
                integrante.oid as id,integrante.cd_docente as investigador_id,integrante.cd_proyecto as proyecto_id,
    CASE integrante.cd_tipoinvestigador
        WHEN 1 THEN 'Director'
        WHEN 2 THEN 'Codirector'
        WHEN 3 THEN 'Investigador Formado'
        WHEN 4 THEN 'Investigador En Formación'
        WHEN 5 THEN 'Becario, Tesista'
        WHEN 6 THEN 'Colaborador'
        ELSE '' END as tipo,
    CASE integrante.dt_alta
        WHEN '0000-00-00' THEN NULL
        ELSE integrante.dt_alta END as alta,
       CASE integrante.dt_baja
           WHEN '0000-00-00' THEN NULL
           ELSE integrante.dt_baja END as baja,
       CASE integrante.dt_cambioHS
           WHEN '0000-00-00' THEN NULL
           ELSE integrante.dt_cambioHS END as cambio,
        integrante.nu_horasinv as horas, integrante.nu_horasinvAnt as horas_anteriores,
    CASE integrante.cd_estado
        WHEN 1 THEN 'Alta Creada'
        WHEN 2 THEN 'Alta Recibida'
        WHEN 3 THEN ''
        WHEN 4 THEN 'Baja Creada'
        WHEN 5 THEN 'Baja Recibida'
        WHEN 6 THEN 'Cambio Creado'
        WHEN 7 THEN 'Cambio Recibido'
        WHEN 8 THEN 'Cambio Hs. Creado'
        WHEN 9 THEN 'Cambio Hs. Recibido'
        WHEN 10 THEN 'Cambio Tipo Creado'
        WHEN 11 THEN 'Cambio Tipo Recibido'
END as estado, integrante.ds_curriculum as curriculum, integrante.ds_actividades as actividades, integrante.ds_consecuencias as consecuencias
       , integrante.ds_motivos as motivos, integrante.ds_cyt as cyt, integrante.ds_reduccionHS as reduccion, integrante.ds_mail as email,
    integrante.cd_categoria as categoria_id, integrante.cd_categoriasicadi as sicadi_id,
    CASE `ds_deddoc`
           WHEN 's/d' THEN null
           WHEN 'SI-1' THEN 'Simple'
           WHEN 'SE-1' THEN 'Semi Exclusiva'
           ELSE ds_deddoc END as deddoc,
    CASE integrante.cd_cargo
           WHEN '6' THEN null
           ELSE integrante.cd_cargo END as cargo_id,
       CASE integrante.dt_cargo
           WHEN '0000-00-00' THEN NULL
           ELSE integrante.dt_cargo END as alta_cargo,
    CASE integrante.cd_facultad
           WHEN '574' THEN null
           ELSE integrante.cd_facultad END as facultad_id,integrante.cd_unidad as unidad_id,
    CASE integrante.cd_carrerainv
           WHEN '11' THEN null
           WHEN '10' THEN null
           ELSE integrante.cd_carrerainv END as carrerainv_id,
    CASE integrante.cd_organismo
           WHEN '7' THEN null
           else integrante.cd_organismo END as organismo_id,
       CASE integrante.dt_carrerainv
           WHEN '0000-00-00' THEN NULL
           ELSE integrante.dt_carrerainv END as ingreso_carrerainv
    ,integrante.cd_universidad as universidad_id,
    CASE integrante.ds_orgbeca
           WHEN 'U.N.L.P' THEN 'UNLP'
                   WHEN 'U.N.L.P.' THEN 'UNLP'
                   WHEN 'Otro' THEN 'OTRA'
                   WHEN 'Consejo de Invest. Científicas de la Provincia de Bs As' THEN 'CIC'
                   WHEN 'Consejo Nac. Invest. Científicas y Técnicas' THEN 'CONICET'
                   WHEN 'CONSEJO NACIONAL DE INVESTIGACIONES CIENTÍFICAS Y TÉCNICAS' THEN 'CONICET'
                   WHEN 'UNIVERSIDAD NACIONAL DE LA PLATA' THEN 'UNLP'
                   WHEN 'CONSEJO DE INV. CIENTÍFICAS DE LA PROVINCIA DE BUENOS AIRES' THEN 'CIC'
                   WHEN 'CONSEJO NACIONAL DE INVESTIGACIONES CIENTÍFICAS Y TÉCNICAS (CONICET)' THEN 'CONICET'
                   WHEN 'ANPCYT-UNLP' THEN 'ANPCyT'
                   WHEN 'SEC. CIENCIA Y TÉCNICA (UNLP)' THEN 'UNLP'
                   WHEN 'UNIV.NAC.DE LA PLATA / FAC.DE PERIODISMO Y COMUNICACION SOCIAL' THEN 'UNLP'
                   WHEN 'FACULTAD DE HUMANIDADES Y CIENCIAS DE LA EDUCACIÓN' THEN 'UNLP'
                   WHEN 'AGENCIA' THEN 'ANPCyT'
                   WHEN 'CONVENIO COMISIÓN NACIONAL DE ACTIVIDADES ESPACIALES' THEN 'OTRA'
                   WHEN 'Consejo Nac. Invest. Cient' THEN 'CONICET'
                   WHEN 'CONSEJO NACIONAL DE INVESTIGACIONES CIENTÍFICAS Y TÉCNICAS (CONICET)' THEN 'CONICET'
                   WHEN 'CONSEJO DE INVESTIGACIÓN CIENTÍFICAS DE LA PROVINCIA DE BUENOS AIRES' THEN 'CIC'
                   WHEN 'COMISIÓN DE INVESTIGACIONES CIENTÍFICAS. PCIA. DE BUENOS AIRES.' THEN 'CIC'
                   WHEN 'UNIVERSIDAD NACIONAL DE LA PLATA -FAHCE' THEN 'UNLP'
                   WHEN 'UNLP FBA' THEN 'UNLP'
                   WHEN 'COMISION DE INVESTIGACIONES CIENTIFICAS BS AS' THEN 'CIC'
                   WHEN 'FACULTAD DE INGENIERIA' THEN 'UNLP'
                   WHEN 'CONCEJO DE INVEST. CIENTÍFICAS DE LA PROVINCIA DE BUENOS AIRES' THEN 'CIC'
                   WHEN 'FACULTAD DE CIENCIAS VETERINARIAS UNLP' THEN 'UNLP'
                   WHEN 'UNAM, MÉXICO' THEN 'OTRA'
                   WHEN 'AGENCIA NACIONAL DE PROMOCIÓN CIENTÍFICA Y TECNOLÓGICA' THEN 'ANPCyT'
                   WHEN 'FCAG-UNLP' THEN 'UNLP'
                   WHEN 'INTA' THEN 'OTRA'
                   WHEN 'CONICET / DAAD' THEN 'CONICET'
                   WHEN 'CICPBA' THEN 'CIC'
                   WHEN 'CONSEJO NACIONAL DE INVESTIGACIONES CIENTÍFICAS' THEN 'CONICET'
                   WHEN 'CINDECA-CONICET-UNLP' THEN 'CONICET'
                   WHEN 'FCNYM' THEN 'UNLP'
                   WHEN 'FACULTAD DE INGENIERIA, UNLP' THEN 'UNLP'
                   WHEN 'DIVISIÓN PALEONTOLOGÍA VERTEBRADOS, MUSEO DE LA PLATA' THEN 'UNLP'
                   WHEN 'COMISIÓN DE INVESTIGACIONES CIENTÍFICAS BS.AS' THEN 'CIC'
                   WHEN 'FACULTAD DE CIENCIAS AGRARIAS Y FORESTALES' THEN 'UNLP'
                   WHEN 'UNLP FBA' THEN 'UNLP'
                   WHEN 'FONCYT' THEN 'OTRA'
                   WHEN 'COMISIÓN DE INVESTIGACIONES CIENTÍFICAS DE LA PROVINCIA DE BUENOS AIRES' THEN 'CIC'
                   WHEN 'ANCYPT' THEN 'ANPCyT'
                   WHEN 'FAU-UNLP' THEN 'UNLP'
                   WHEN 'CENTRO DE INVESTIGACIONES URBANAS Y TERRITORIALES (CIUT)' THEN 'OTRA'
                   WHEN 'FACULTAD DE ARQUITECTURA Y URBANISMO-UNLP' THEN 'UNLP'
                   WHEN 'UNLP-CIN' THEN 'CIN'
                   WHEN 'CONSEJO INTERUNIVERSITARIO NACIONAL' THEN 'CIN'
                   WHEN 'COPNICET' THEN 'CONICET'
                   WHEN 'CIC PBA' THEN 'CIC'
                   WHEN 'CIC. PBA' THEN 'CIC'
                   WHEN 'DAAD' THEN 'OTRA'
                   WHEN 'COMISIÓN DE INVESTIGACIONES CIENTÍFICAS (CIC)' THEN 'CIC'
                   WHEN 'CONSEJO NAC.DE INVEST.CIENTIF.Y TECNICAS' THEN 'CONICET'
                   WHEN 'CICBA' THEN 'CIC'
                   WHEN 'UNIVERSIDAD POLITECNICA DE CATALUNIA' THEN 'OTRA'
                   WHEN 'INSTITUTO DE ASTROFÍSICA CANARIAS' THEN 'OTRA'
                   ELSE
                       CASE
                           WHEN bl_becaEstimulo = 1 THEN 'CIN'
                           ELSE ds_orgbeca
                           END
                   END AS institucion,
       CASE integrante.ds_tipobeca
           WHEN 'POSTGRADO/DOCTORADO' THEN 'Beca doctoral'
                   WHEN 'POSTGRADO/DOCTORADO' THEN 'Beca posdoctoral'
                   WHEN 'Postdoctorado' THEN 'Beca posdoctoral'
                   WHEN 'BECA INTERNA DE POSTGRADO TIPO II' THEN 'TIPO II'
                   WHEN 'BECA INTERNA DE POSTGRADO TIPO II' THEN 'TIPO II'
                   WHEN 'Agencia 2' THEN 'Beca superior'
                   WHEN 'POSDOCTORAL' THEN 'Beca posdoctoral'
                   WHEN 'B' THEN 'TIPO B'
                   WHEN 'B DE UNLP' THEN 'TIPO B'
                   WHEN 'POST-DOCTORADO' THEN 'Beca posdoctoral'
                   WHEN 'II' THEN 'TIPO II'
                   WHEN 'I' THEN 'TIPO I'
                   WHEN 'POSTDOCTORAL' THEN 'Beca posdoctoral'
                   WHEN 'BECA DOCTORAL TIPO II' THEN 'TIPO II'
                   WHEN 'PERFECCIONAMIENTO' THEN 'BECA DE PERFECCIONAMIENTO'
                   WHEN 'DOCTORAL' THEN 'Beca doctoral'
                   WHEN 'POSTGRADO' THEN 'Beca doctoral'
                   WHEN 'POSTGRADO TIPO II' THEN 'TIPO II'
                   WHEN 'BEWCA DOCTORAL TIPO II' THEN 'TIPO II'
                   WHEN 'BECA DOCTORAL INTERNA' THEN 'Beca doctoral'
                   WHEN 'POSGRADO INTERNA TIPO I' THEN 'TIPO I'
                   WHEN 'BECA DE POSTGRADO TIPO II' THEN 'TIPO II'
                   WHEN 'DOCTORAL TIPO II' THEN 'TIPO II'
                   WHEN 'BECA TIPO II' THEN 'TIPO II'
                   WHEN 'BECARIO DOCTORAL TIPO II' THEN 'TIPO II'
                   WHEN 'INTERNA DE POSGRADO TIPO 1' THEN 'TIPO I'
                   WHEN '2' THEN 'TIPO II'
                   WHEN 'DOCTORAL TIPO I' THEN 'TIPO I'
                   WHEN 'POST-DOCTORAL' THEN 'Beca posdoctoral'
                   WHEN 'BECA DE POSGRADO TIPO II' THEN 'TIPO II'
                   WHEN 'CATEGORÍA II' THEN 'TIPO II'
                   WHEN 'I' THEN 'TIPO I'
                   WHEN 'BECARIO TIPO 1' THEN 'TIPO I'
                   WHEN 'POSTGRADO/ESPECIALIZACION TIPO II' THEN 'TIPO II'
                   WHEN 'FORMACIÓN SUPERIOR EN LA INVESTIGACIÓN Y DESARROLLO ARTÍSTICO, CIENTÍFICO Y TECNOLÓGICO' THEN 'Formación Superior'
                   WHEN 'FORMACIóN SUPERIOR' THEN 'Formación Superior'
                   WHEN 'BECA DE RETENCIÓN DE POSGRADUADO' THEN 'RETENCION DE POSTGRADUADO'
                   WHEN 'Programa de Retención de Doctores de la UNLP' THEN 'RETENCION DE POSTGRADUADO'
                   WHEN 'Retención de doctores' THEN 'RETENCION DE POSTGRADUADO'
                   WHEN 'Beca de Investigación: Programa de Retención de Doctores' THEN 'RETENCION DE POSTGRADUADO'
                   WHEN 'Beca de Investigación: Retención de Doctores UNLP' THEN 'RETENCION DE POSTGRADUADO'
                   WHEN 'CATEGORIA LL' THEN 'RETENCION DE POSTGRADUADO'
                   WHEN 'BECA DE POSTGRADUADO' THEN 'RETENCION DE POSTGRADUADO'
                   WHEN 'Postgraduados' THEN 'RETENCION DE POSTGRADUADO'
                   WHEN 'BECA INTERNA DE POSGRADO' THEN 'Beca doctoral'
                   WHEN 'Doctorado en Ingenieria' THEN 'Beca doctoral'
                   WHEN 'Doctoral CONICET/UNLP' THEN 'Beca doctoral'
                   WHEN 'TIPO B-DOCTORADO' THEN 'TIPO B'
                   WHEN 'POSTGRADO/DOCTORADO BECA TIPO B' THEN 'TIPO B'
                   WHEN 'BECARIO TIPO I' THEN 'TIPO I'
                   WHEN 'BECA INTERNA TIPO II' THEN 'TIPO II'
                   WHEN 'BECA POSDOCOTRAL' THEN 'Beca doctoral'
                   WHEN 'BECA INTERNA DE POSGRADO TIPO II' THEN 'TIPO II'
                   WHEN 'POSTGRADO TIPO I' THEN 'TIPO I'
                   WHEN 'POSTGRADO/DOCTORADO BECA TIPO B' THEN 'TIPO B'
                   WHEN 'POST DOCTORAL' THEN 'Beca posdoctoral'
                   WHEN 'TIPO 2' THEN 'TIPO II'
                   WHEN 'POSGRADO' THEN 'Beca doctoral'
                   WHEN 'POSDOCTORADO' THEN 'Beca posdoctoral'
                   WHEN 'BECA POSTODOCTORAL INTERNA' THEN 'Beca posdoctoral'
                   WHEN 'BECA DE POSGRADO TIPO 1' THEN 'TIPO I'
                   WHEN 'POSGRADUADOS (RETENCIÓN DE RRHH)' THEN 'RETENCION DE POSTGRADUADO'
                   WHEN 'II, DOCTORADO' THEN 'TIPO II'
                   WHEN 'BECA DE SCYT TIPO B' THEN 'TIPO B'
                   WHEN 'CONICET 1' THEN 'TIPO I'
                   WHEN 'II DOCTORAL' THEN 'TIPO II'
                   WHEN 'ENTRENAMIENTO' THEN 'Beca de entrenamiento'
                   WHEN 'BECA DOCTORAL PG T II 11' THEN 'TIPO II'
                   WHEN 'UNLP 1' THEN 'TIPO A'
                   WHEN 'Agencia 1' THEN 'Beca inicial'
                   WHEN 'Agencia 1' THEN 'Beca Inicial FONCyT'
                   WHEN 'RETENCIÓN DE GRADUADOS FORMADOS POR LA UNLP' THEN 'RETENCION DE POSTGRADUADO'
                   WHEN 'BECA POSTGRADO TIPO II CON PAISES LATINOAMERICANOSI' THEN 'TIPO II'
                   WHEN 'DOCTORAL I' THEN 'TIPO I'
                   WHEN 'Beca Interna Postdoctoral' THEN 'Beca posdoctoral'
                   WHEN 'DE GRADO' THEN 'Beca doctoral'
                   WHEN 'Programa de retencion de Doctores' THEN 'RETENCION DE POSTGRADUADO'
                   WHEN 'BECA DE POSGRADO TIPO I' THEN 'TIPO I'
                   WHEN 'BECA DE POSTGRADO TIPO II, CONICET' THEN 'TIPO II'
                   WHEN 'BECA DOCTORAL TIPO I' THEN 'TIPO I'
                   WHEN 'RETENCIÓN DE POSTGRADUADOS FORMADOS' THEN 'RETENCION DE POSTGRADUADO'
                   WHEN 'TIPO B DE UNLP' THEN 'TIPO B'
                   WHEN 'B - POSGRADO/MAESTRIA' THEN 'TIPO B (MAESTRÍA)'
                   WHEN 'BECA POS DOC' THEN 'Beca posdoctoral'
                   WHEN 'BECA POSTDOCOTORAL' THEN 'Beca posdoctoral'
                   WHEN 'BECA POSTDOCTORAL' THEN 'Beca posdoctoral'
                   WHEN 'BECA POSGRADO TIPO B' THEN 'TIPO B'
                   WHEN 'BECA INTERNA POSGRADOTIPO II' THEN 'TIPO II'
                   WHEN 'BECA DE POSGRADO TIPO A' THEN 'TIPO A'
                   WHEN 'POSGRADO TIPO I' THEN 'TIPO I'
                   WHEN 'INCIACIÓN - A ' THEN 'TIPO A'
                   WHEN 'DOCTORADO' THEN 'Beca doctoral'
                   WHEN 'DE PERFECCIONAMIENTO' THEN 'BECA DE PERFECCIONAMIENTO'
                   WHEN 'BECA DOCTORAL TIPO 1' THEN 'TIPO I'
                   WHEN 'POSGRADO TIPO 2' THEN 'TIPO II'
                   WHEN 'BECA INTERNA DE POSGRADO TIPO I' THEN 'TIPO I'
                   WHEN 'INTERNA DE POSGRADO TIPO I' THEN 'TIPO I'
                   WHEN 'BECA ESTÍMULO A LAS VOCACIONES CIENTÍFICAS' THEN 'EVC'
                   WHEN '&#61607;	BECA DE ENTRENAMIENTO PARA ALUMNOS UNIVERSITARIOS' THEN 'Beca de entrenamiento'
                   WHEN 'POSTGRADO TIPO A' THEN 'TIPO A'
                   WHEN 'DOCTORAL DE FINALIZACIÓN' THEN 'Beca finalización del doctorado'
                   WHEN 'Finalización' THEN 'Beca finalización del doctorado'
                   WHEN 'Finalizacion de doctorado (tipo II)' THEN 'Beca finalización del doctorado'
                   WHEN 'finalización del doctorado' THEN 'Beca finalización del doctorado'
                   WHEN 'finalización del doctorado' THEN 'Beca finalización del doctorado'
                   WHEN 'Finalización de Doctorado' THEN 'Beca finalización del doctorado'
                   WHEN 'FINALIZACION DEL DOCTORADO' THEN 'Beca finalización del doctorado'
                   WHEN 'finalización doctorado' THEN 'Beca finalización del doctorado'
                   WHEN 'FINALIZACIÓN DOCTORADO' THEN 'Beca finalización del doctorado'
                   WHEN 'Finalización doctorado' THEN 'Beca finalización del doctorado'
                   WHEN 'Finalización doctorado' THEN 'Beca finalización del doctorado'
                   WHEN 'POSGRADO-DOCTORAL' THEN 'Beca doctoral'
                   WHEN 'POSGRADO TIPO 1' THEN 'TIPO I'
                   WHEN 'UNLP 2' THEN 'TIPO B'
                   WHEN 'BECA TIPO A' THEN 'TIPO A'
                   WHEN 'BECA POSTGRADO TIPO I' THEN 'TIPO I'
                   WHEN 'INICIAL' THEN 'Beca inicial'
                   WHEN 'BECA DE POSDOCTORAL' THEN 'Beca posdoctoral'
                   WHEN 'BECARIO POSTGRADO TIPO I' THEN 'TIPO I'
                   WHEN 'BECA PERFECCIONAMIENTO' THEN 'BECA DE PERFECCIONAMIENTO'
                   WHEN 'INTERNA  TIPO A' THEN 'TIPO A'
                   WHEN 'Finalización de Doctorado' THEN 'Beca finalización del doctorado'
                   WHEN 'BECA DE ENTRENAMIENTO' THEN 'Beca de entrenamiento'
                   WHEN 'BECA POSTGRADO TIPO I (DOCTORAL)' THEN 'TIPO I'
                   WHEN 'INTERNA DE POSTGRADO TIPO I' THEN 'TIPO I'
                   WHEN 'BECA INTERNA DE POSTGRADO TIPO I (3 AÑOS)' THEN 'TIPO I'
                   WHEN 'BECA INTERNA DE POSTGRADO TIPO I (ASPIRANTE)' THEN 'TIPO I'
                   WHEN 'INICIACIÓN TIPO I' THEN 'TIPO I'
                   WHEN 'INTERNA DE POSTGRADO TIPO II' THEN 'TIPO II'
                   WHEN 'BECA DE POSTGRADO TIPO I' THEN 'TIPO I'
                   WHEN 'BECA SUPERIOR  AGENCIA NACIONAL DE PROMOCIÓN CIENTÍFICA Y TECNOLÓGICA' THEN 'Beca superior'
                   WHEN 'BECA A LAS VOCACIONES CIENTÍFICAS' THEN 'EVC'
                   WHEN 'BECA DE INICIACIÓN DEL CONICET (INTERNA POSTGRADO TIPO I CON PAÍSES LATINOAMERICANOS)' THEN 'TIPO I'
                   WHEN 'BECA DE INICIACIÓN' THEN 'Iniciación'
                   WHEN 'BECA TIPO I' THEN 'TIPO I'
                   WHEN 'I DOCTORAL' THEN 'TIPO I'
                   WHEN 'BECA DE ESTIMULO A LA INVESTIGACIÓN' THEN 'EVC'
                   WHEN 'BECA DE ENTRENAMIENTO PARA JOVENES INVESTIGADORES' THEN 'Beca de entrenamiento'
                   WHEN 'CIC 1' THEN 'Beca doctoral'
                   WHEN 'BECARIO TIPO II' THEN 'TIPO II'
                   WHEN 'ENTRENAMIENTO EN INVESTIGACION' THEN 'Beca de entrenamiento'
                   WHEN 'TIPO B (DOCTORADO)' THEN 'TIPO B (DOCTORADO)'
                   WHEN 'BECARIO DE POSTGRADO TIPO I' THEN 'TIPO I'
                   WHEN 'TIPO I PAISES LATINOAMERICANOS' THEN 'TIPO I'
                   WHEN 'POSTDOCTORAL LATINOAMERICANO' THEN 'TIPO II'
                   WHEN 'BECAS DE ESTÍMULO A LAS VOCACIONES CIENTÍFICAS 2011' THEN 'EVC'
                   WHEN 'BECA INTERNA DE ENTRENAMIENTO EN INVESTIGACIÓN' THEN 'Beca de entrenamiento'
                   WHEN 'BECARIO DE INICIACIÓN' THEN 'Iniciación'
                   WHEN '1' THEN 'TIPO I'
                   WHEN 'DOCTORAL TIPO A' THEN 'TIPO A'
                   WHEN 'BECARIO DOCTORAL TIPO I' THEN 'TIPO I'
                   WHEN 'DOCTORAL, TIPO 1' THEN 'TIPO I'
                   WHEN 'BECA INTERNA TIPO A' THEN 'TIPO A'
                   WHEN 'BECA LATINOAMERICANA DOCTORAL TIPO I' THEN 'TIPO I'
                   WHEN 'ESTIMULO DE VOCACIONES CIENÍTICAS' THEN 'EVC'
                   WHEN 'BECAS DE ENTRENAMIENTO FAU' THEN 'Beca de entrenamiento'
                   WHEN 'INTERNA TIPO I' THEN 'TIPO I'
                   WHEN 'POSGRADO 1' THEN 'TIPO I'
                   WHEN 'BECA DE ESTUDIO PARA GRADUADOS UNIVERSITARIOS (CIC)' THEN 'Beca doctoral'
                   WHEN 'ESTUDIO' THEN 'Beca doctoral'
                   WHEN 'POS DOCTORAL' THEN 'Beca posdoctoral'
                   WHEN 'BECAS CIN' THEN 'EVC'
                   WHEN 'Beca Interna Doctoral' THEN 'Beca doctoral'
                   WHEN 'BECA DE PERFECCIONAMIENTO (UNNE)' THEN 'BECA DE PERFECCIONAMIENTO'
                   WHEN 'BECTA INTERNA TIPO I' THEN 'TIPO I'
                   WHEN 'TIPO B-MAESTRIA' THEN 'TIPO B (MAESTRÍA)'
                   WHEN 'Tipo A- Maestría' THEN 'Tipo A - Maestría'
                   WHEN 'Tipo A- Doctorado' THEN 'Tipo A - Doctorado'
                   WHEN 'BECA DE ESTÍMULO A LAS VOCACIONES CIENTÍFICAS' THEN 'EVC'
                   WHEN 'BECA DE ENTRENAMIENTO ALUMNOS' THEN 'Beca de entrenamiento'
                   WHEN 'POSGRADO TIPO I CON PAÍSES LATINOAMERICANOS' THEN 'TIPO I'
                   WHEN 'POSGRADO/ DOCTORADO' THEN 'Beca doctoral'
                   WHEN 'PERFECCIONAMIENTO TIPO I' THEN 'TIPO I'
                   WHEN 'DE POSGRADO TIPO I' THEN 'TIPO I'
                   WHEN 'POSTDOCTORAL FONTAR' THEN 'Beca posdoctoral'
                   WHEN 'BECA INTERNA DE INVESTIGACIÓN CIENTÍFICA DE GRADUADOS DE INICIACIÓN' THEN 'Iniciación'
                   WHEN 'DE POS GRADO TIPO I' THEN 'TIPO I'
                   WHEN 'BECA INTERNA DE POSTGRADO TIPO I' THEN 'TIPO I'
                   WHEN 'VOCACIONES CIENTÍFICAS' THEN 'EVC'
                   WHEN 'Beca Doctoral Conicet' THEN 'Beca doctoral'
                   WHEN 'ESTÍMULO A LAS VOCACIONES CIENTÍFICAS' THEN 'EVC'
                   WHEN 'ESTÍMULO VOCACIONES CIENTÍFICAS' THEN 'EVC'
                   WHEN 'TIPO II DOCTORAL' THEN 'TIPO II'
                   WHEN 'BECA INTERNA TIPO I' THEN 'TIPO I'
                   WHEN 'TIPO B (MAESTRíA)' THEN 'TIPO B (MAESTRÍA)'
                   WHEN 'TIPO I-DOCTORAL' THEN 'TIPO I'
                   WHEN 'TIPO 1' THEN 'TIPO I'
                   WHEN 'ESTIMULO' THEN 'EVC'
                   WHEN 'TIPO I CON PAÍSES LATINOAMERICANOS' THEN 'TIPO I'
                   WHEN 'NIVEL INICIAL' THEN 'Beca inicial'
                   WHEN 'POSTGRADO/ESPECIALIZACÓN TIPO I' THEN 'TIPO I'
                   WHEN 'POSGRADO II' THEN 'TIPO II'
                   WHEN 'POSGRADO INICIAL' THEN 'Beca inicial'
                   WHEN 'BECA POSGRADO TIPO I' THEN 'TIPO I'
                   WHEN 'POSGRADO-DOCTORADO' THEN 'Beca doctoral'
                   WHEN 'Beca Boctoral' THEN 'Beca doctoral'
                   WHEN 'PUE' THEN 'Beca doctoral'
                   WHEN 'TIPO I INTERNA' THEN 'TIPO I'
                   WHEN 'Beca incial' THEN 'Beca inicial'
                   WHEN 'Beca de nivel inicial' THEN 'Beca inicial'
                   WHEN 'Becas internas postdoctorales' THEN 'Beca posdoctoral'
                   WHEN 'Beca Inicial FONCyT' THEN 'Beca inicial'
                   WHEN 'BECA TIPO 1 DOCTORADO' THEN 'TIPO I'
                   WHEN 'Concursos de becas internas doctorales y destinadas a postulantes provenientes de paises latinoamericanos' THEN 'TIPO I'
                   WHEN 'BECA INTERNA DE POSTGRADO TIPO I (3 AÑOS)' THEN 'TIPO I'
                   WHEN 'Beca Interna Doctoral (área ?KE1 TIERRA?)' THEN 'Beca doctoral'
                   WHEN 'POS-DOCTORAL (INTERNA)' THEN 'Beca posdoctoral'
                   WHEN 'Beca Interna Dcotoral' THEN 'Beca doctoral'
                   WHEN 'POSGRADO TIPO II' THEN 'TIPO II'
                   WHEN 'BECARIO POSTDOCTORAL' THEN 'Beca posdoctoral'
                   WHEN 'BECA BIANUAL.ESTUDIOS DE MAESTRIA.' THEN 'Beca maestría'
                   WHEN 'Maestría' THEN 'Beca maestría'
                   WHEN 'BECA INTERNA POSDOCTORAL' THEN 'Beca posdoctoral'
                   WHEN 'POSTGRADO/DOCTORADO BECA TIPO B' THEN 'TIPO B'
                   WHEN 'INICICIACIÓN' THEN 'Iniciación'
                   WHEN 'INICIACIóN' THEN 'Iniciación'

                   WHEN 'POSTGRADO TIPO I AVG' THEN 'TIPO I'
                   WHEN 'A' THEN 'TIPO A'
                   WHEN 'Cofinanciadas CIC-UNLP' THEN 'Beca Cofinanciada (UNLP-CIC)'
                   ELSE
                   CASE
                       WHEN bl_becaEstimulo = 1 THEN 'EVC'
                       ELSE ds_tipobeca
                       END
                   END AS beca,
    CASE
       WHEN integrante.bl_becaEstimulo = 1 THEN
           CASE integrante.dt_becaEstimulo
               WHEN '0000-00-00' THEN NULL
               ELSE integrante.dt_becaEstimulo END

       ELSE CASE integrante.dt_beca
                WHEN '0000-00-00' THEN NULL
                ELSE integrante.dt_beca END END as alta_beca,
    CASE
       WHEN integrante.bl_becaEstimulo = 1 THEN  CASE integrante.dt_becaEstimuloHasta
                                                     WHEN '0000-00-00' THEN NULL
                                                     ELSE integrante.dt_becaEstimuloHASTA END
       ELSE CASE integrante.dt_becaHasta
                WHEN '0000-00-00' THEN NULL
                ELSE integrante.dt_becaHasta END END as baja_beca, integrante.ds_resolucionBeca as resolucion,


       CASE integrante.cd_titulo
           WHEN '9999' THEN null
           ELSE integrante.cd_titulo END as titulo_id,
       CASE integrante.cd_titulopost
           WHEN '9999' THEN null
           ELSE integrante.cd_titulopost END as titulopost_id,
       integrante.nu_materias as materias, integrante.nu_totalMat as total, integrante.ds_carrera as carrera")
            ->orderBy('cd_proyecto')
            ->chunk(1000, function ($rows) use (&$totalFilas, &$totalInsertadas, &$totalOmitidas, &$skippedRows){
                $totalFilas += count($rows);
                $data = collect($rows)->map(function ($row) use (&$skippedRows, &$totalOmitidas) {

                    $estadosValidos = [
                        'Alta Creada','Alta Recibida','','Baja Creada','Baja Recibida','Cambio Creado','Cambio Recibido','Cambio Hs. Creado','Cambio Hs. Recibido','Cambio Tipo Creado','Cambio Tipo Recibido'
                    ];

                    $estadoRow = trim((string)$row->estado);

                    if (!empty($estadoRow) && !in_array($estadoRow, $estadosValidos)) {
                        // Solo omitimos si tiene valor y no está en la lista
                        $skippedRows[] = [
                            'id' => $row->id,
                            'motivo' => 'Estado inválida',
                            'estado' => $row->estado,
                            'tipo' => $row->tipo,
                            'deddoc' => $row->deddoc,
                            'investigacion' => $row->investigacion,
                            'institucion' => $row->institucion,
                            'beca' => $row->beca,
                        ];
                        $totalOmitidas++;
                        return null; // omite la fila
                    }

                    $estadoFinal = empty($estadoRow) ? null : $estadoRow;

                    // 🧹 LIMPIEZA DE FECHA
                    $alta = $row->alta;

                    if (
                        empty($alta) ||
                        $alta === '0000-00-00' ||
                        $alta === '0000-00-00 00:00:00'
                    ) {
                        $alta = null;
                    }

                    $baja = $row->baja;

                    if (
                        empty($baja) ||
                        $baja === '0000-00-00' ||
                        $baja === '0000-00-00 00:00:00'
                    ) {
                        $baja = null;
                    }

                    $cambio = $row->cambio;

                    if (
                        empty($cambio) ||
                        $cambio === '0000-00-00' ||
                        $cambio === '0000-00-00 00:00:00'
                    ) {
                        $cambio = null;
                    }

                    $tiposValidos = [
                        '','Director','Codirector','Investigador Formado','Investigador En Formación','Becario, Tesista','Colaborador'
                    ];

                    $tipoRow = trim((string)$row->tipo);

                    if (!empty($tipoRow) && !in_array($tipoRow, $tiposValidos)) {
                        // Solo omitimos si tiene valor y no está en la lista
                        $skippedRows[] = [
                            'id' => $row->id,
                            'motivo' => 'Tipo inválida',
                            'estado' => $row->estado,
                            'tipo' => $row->tipo,
                            'deddoc' => $row->deddoc,
                            'investigacion' => $row->investigacion,
                            'institucion' => $row->institucion,
                            'beca' => $row->beca,
                        ];
                        $totalOmitidas++;
                        return null; // omite la fila
                    }

                    $tipoFinal = empty($tipoRow) ? null : $tipoRow;

                    $email = filter_var($row->email, FILTER_VALIDATE_EMAIL) ? $row->email : null;

                    $valoresValidos = ['Exclusiva','Semi Exclusiva','Simple'];
                    $deddocRow = trim((string)$row->deddoc);

                    if (!empty($deddocRow) && !in_array($deddocRow, $valoresValidos)) {
                        // Solo omitimos si tiene valor y no está en la lista
                        $skippedRows[] = [
                            'id' => $row->id,
                            'motivo' => 'Deddoc inválida',
                            'estado' => $row->estado,
                            'tipo' => $row->tipo,
                            'deddoc' => $row->deddoc,
                            'institucion' => $row->institucion,
                            'beca' => $row->beca,
                        ];
                        $totalOmitidas++;
                        return null; // omite la fila
                    }

                    $deddocFinal = empty($deddocRow) ? null : $deddocRow;

                    $alta_cargo = $row->alta_cargo;

                    if (
                        empty($alta_cargo) ||
                        $alta_cargo === '0000-00-00' ||
                        $alta_cargo === '0000-00-00 00:00:00'
                    ) {
                        $alta_cargo = null;
                    }

                    $ingreso_carrerainv = $row->ingreso_carrerainv;

                    if (
                        empty($ingreso_carrerainv) ||
                        $ingreso_carrerainv === '0000-00-00' ||
                        $ingreso_carrerainv === '0000-00-00 00:00:00'
                    ) {
                        $ingreso_carrerainv = null;
                    }

                    // Validación de beca
                    $becaValidas = [
                        'Beca inicial','Beca superior','Beca de entrenamiento','Beca doctoral','Beca posdoctoral','Beca finalización del doctorado','Beca maestría','Formación Superior','Iniciación','TIPO I','TIPO II','TIPO A','Tipo A - Maestría','Tipo A - Doctorado','Beca Cofinanciada (UNLP-CIC)','Especial de Maestría','TIPO B','TIPO B (DOCTORADO)','TIPO B (MAESTRÍA)','BECA DE PERFECCIONAMIENTO','CONICET 2','RETENCION DE POSTGRADUADO','EVC'
                    ];
                    // normalizar array válido (una sola vez idealmente, fuera del map)
                    $becaValidasNorm = array_map(function($b) {
                        return mb_strtoupper(trim($b));
                    }, $becaValidas);

// normalizar valor entrante
                    $becaRow = mb_strtoupper(trim((string)$row->beca));

// comparar
                    $becaFinal = in_array($becaRow, $becaValidasNorm) ? trim($row->beca) : null;

                    // Validación de institución
                    $institucionValidas = ['ANPCyT','CIC','CONICET','UNLP','OTRA','CIN'];

// normalizar una sola vez
                    $institucionValidasNorm = array_flip(array_map(function($i) {
                        return mb_strtoupper(trim($i));
                    }, $institucionValidas));

// normalizar valor entrante
                    $institucionRow = mb_strtoupper(trim((string)$row->institucion));

// comparar
                    $institucionFinal = isset($institucionValidasNorm[$institucionRow])
                        ? trim($row->institucion)
                        : null;

                    // Omitir si beca o institucion inválida
                    if (is_null($becaFinal) && !empty($row->beca)) {
                        $skippedRows[] = [
                            'id' => $row->id,
                            'motivo' => 'Beca inválida',
                            'estado' => $row->estado,
                            'tipo' => $row->tipo,
                            'deddoc' => $row->deddoc,
                            'investigacion' => $row->investigacion,
                            'institucion' => $row->institucion,
                            'beca' => $row->beca,
                        ];
                        $totalOmitidas++;
                    }

                    if (is_null($institucionFinal) && !empty($row->institucion)) {
                        $skippedRows[] = [
                            'id' => $row->id,
                            'motivo' => 'Institucion inválida',
                            'estado' => $row->estado,
                            'tipo' => $row->tipo,
                            'deddoc' => $row->deddoc,
                            'investigacion' => $row->investigacion,
                            'institucion' => $row->institucion,
                            'beca' => $row->beca,
                        ];
                        $totalOmitidas++;
                    }

                    $alta_beca = $row->alta_beca;

                    if (
                        empty($alta_beca) ||
                        $alta_beca === '0000-00-00' ||
                        $alta_beca === '0000-00-00 00:00:00'
                    ) {
                        $alta_beca = null;
                    }

                    $baja_beca = $row->baja_beca;

                    if (
                        empty($baja_beca) ||
                        $baja_beca === '0000-00-00' ||
                        $baja_beca === '0000-00-00 00:00:00'
                    ) {
                        $baja_beca = null;
                    }

                    return [
                        'id' => $row->id,
                        'investigador_id' => $row->investigador_id ?: null,
                        'proyecto_id' => $row->proyecto_id ?: null,
                        'tipo' => $tipoFinal,
                        'alta' => $alta,
                        'baja' => $baja,
                        'cambio' => $cambio,
                        'horas' => $row->horas ?: null,
                        'horas_anteriores' => $row->horas_anteriores ?: null,
                        'estado' => $estadoFinal,
                        'curriculum' => trim($row->curriculum),
                        'actividades' => trim($row->actividades),
                        'consecuencias' => trim($row->consecuencias),
                        'motivos' => trim($row->motivos),
                        'cyt' => trim($row->cyt),
                        'reduccion' => trim($row->reduccion),
                        'email' => $email,
                        'categoria_id' => $row->categoria_id ?: null,
                        'sicadi_id' => $row->sicadi_id ?: null,
                        'dedoc' => $deddocFinal,
                        'cargo_id' => $row->cargo_id ?: null,
                        'alta_cargo' => $alta_cargo,
                        'facultad_id' => $row->facultad_id ?: null,
                        'unidad_id' => $row->unidad_id ?: null,
                        'carrerainv_id' => $row->carrerainv_id ?: null,
                        'organismo_id' => $row->organismo_id ?: null,
                        'ingreso_carrerainv' => $ingreso_carrerainv,
                        'universidad_id' => $row->universidad_id ?: null,
                        'institucion' => $institucionFinal,
                        'beca' => $becaFinal,
                        'alta_beca' => $alta_beca,
                        'baja_beca' => $baja_beca,
                        'resolucion' => trim($row->resolucion),
                        'titulo_id' => $row->titulo_id ?: null,
                        'titulopost_id' => $row->titulopost_id ?: null,
                        'materias' => trim($row->materias),
                        'total' => trim($row->total),
                        'carrera' => trim($row->carrera),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->filter()->toArray();

                if (!empty($data)) {
                    DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=0');

                DB::connection('mysql')
                    ->table('proyectos')
                    ->upsert(
                        $data,
                        ['id'],
                        [
                            'investigador_id','proyecto_id','tipo','alta','baja','cambio','horas','horas_anteriores','estado',
                            'curriculum','actividades','consecuencias','motivos','cyt','reduccion','email',
                            'categoria_id','sicadi_id','deddoc','cargo_id','alta_cargo','facultad_id','unidad_id','carrerainv_id','organismo_id',
                            'ingreso_carrerainv','universidad_id','institucion','beca','alta_beca','baja_beca','resolucion','titulo_id','titulopost_id',
                            'materias','total','carrera'
                        ]
                    );
                    DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=1');
                    $totalInsertadas += count($data);
                }
            });

        $this->info('Sincronización finalizada ✔');

        $this->info("Total filas leídas: $totalFilas");
        $this->info("Filas insertadas/actualizadas: $totalInsertadas");
        $this->info("Filas omitidas (proyectos): $totalOmitidas");

        if (!empty($skippedRows)) {
            $this->info("Detalle de proyectos omitidos:");

            foreach ($skippedRows as $skip) {
                $this->line(
                    "ID {$skip['id']} - Motivo: {$skip['motivo']} - Estado: {$skip['estado']} - Tipo: {$skip['tipo']} - Deddoc: {$skip['deddoc']} - Beca: {$skip['beca']} - Institucion: {$skip['institucion']}"
                );
            }
        }
    }
}
