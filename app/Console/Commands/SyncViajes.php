<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncViajes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:viajes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza solicitudjovenes desde DB origen a jovens';

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
            ->table('solicitud')
            ->leftJoin('cyt_solicitud_estado', 'solicitud.cd_solicitud', '=', 'cyt_solicitud_estado.solicitud_oid')
            ->leftJoin('estado', 'cyt_solicitud_estado.estado_oid', '=', 'estado.cd_estado')
            ->leftJoin('deddoc', 'solicitud.cd_deddoc', '=', 'deddoc.cd_deddoc')
            ->leftJoin('motivo', 'solicitud.cd_motivo', '=', 'motivo.cd_motivo')
            ->leftJoin('tipoinvestigador', 'solicitud.cd_tipoinvestigador', '=', 'tipoinvestigador.cd_tipoinvestigador')


            ->selectRaw("
                solicitud.`cd_solicitud` as id,solicitud.`cd_docente` as investigador_id,solicitud.`cd_proyecto1` as proyecto1_id,solicitud.`cd_proyecto2` as proyecto2_id,solicitud.`cd_periodo` as periodo_id,
       estado.ds_estado as estado, solicitud.ds_mail as email,CAST(solicitud.bl_notificacion AS UNSIGNED) as notificacion, solicitud.nu_telefono as telefono, CASE solicitud.dt_fecha
                                                                                                       WHEN '0000-00-00' THEN null
                                                                                                       ELSE solicitud.dt_fecha END as fecha,
       solicitud.ds_calle as calle,
       solicitud.nu_nro as nro, solicitud.nu_piso as piso, solicitud.ds_depto as depto, solicitud.nu_cp as cp, solicitud.cd_titulogrado as titulo_id,
       solicitud.ds_titulogrado as titulogrado,
       solicitud.cd_unidad as unidad_id,
       CASE solicitud.`cd_cargo`
           WHEN '6' THEN null
           ELSE solicitud.cd_cargo END as cargo_id,
       CASE deddoc.`ds_deddoc`
           WHEN 's/d' THEN null
           WHEN 'SI-1' THEN 'Simple'
           WHEN 'SE-1' THEN 'Semi Exclusiva'
           ELSE deddoc.ds_deddoc END as deddoc, CASE solicitud.`cd_facultad`
                                                    WHEN '574' THEN null
                                                    ELSE cd_facultad END as facultad_id, solicitud.cd_facultadplanilla as facultadplanilla_id,
       CASE solicitud.`cd_carrerainv`
           WHEN '11' THEN null
           WHEN '10' THEN null
           ELSE solicitud.cd_carrerainv END as carrerainv_id,
       CASE solicitud.`cd_organismo`
           WHEN '7' THEN null
           else solicitud.cd_organismo END as organismo_id, CASE solicitud.dt_ingreso WHEN '0000-00-00' THEN null ELSE solicitud.dt_ingreso END as ingreso_carrera,
       solicitud.cd_unidadcarrera as unidadcarrera_id,
       solicitud.bl_unlp as unlp,
       CASE solicitud.`ds_orgbeca`
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

                           ds_orgbeca

                   END AS institucion,
               CASE solicitud.`ds_tipobeca`
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
                   WHEN 'Beca Inicial FONCyT' THEN 'Beca inicial'
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

                       ds_tipobeca

                   END AS beca
       solicitud.ds_periodobeca as periodobeca,
       solicitud.cd_unidadbeca as unidadbeca_id,
       solicitud.cd_categoria as categoria_id,
       solicitud.cd_categoriasicadi as sicadi_id,
       motivo.ds_motivo as motivo,
       tipoinvestigador.ds_tipoinvestigador as tipo,
       solicitud.nu_monto as monto,
       solicitud.nu_puntaje as puntaje, solicitud.nu_diferencia as diferencia,
       solicitud.ds_curriculum as curriculum,
       solicitud.ds_trabajo as trabajo,
       solicitud.ds_aceptacion as aceptacion,
       solicitud.ds_titulotrabajo as titulotrabajo,
       solicitud.ds_autorestrabajo as autores,
       solicitud.ds_lugartrabajo as lugartrabajo,
       solicitud.ds_congreso as congresonombre,
       CASE solicitud.dt_fechatrabajo WHEN '0000-00-00' THEN null ELSE solicitud.dt_fechatrabajo END as trabajodesde,
       CASE solicitud.dt_fechatrabajohasta WHEN '0000-00-00' THEN null ELSE solicitud.dt_fechatrabajohasta END as trabajohasta,


       solicitud.ds_invitaciongrupo as invitacion,

       solicitud.ds_aval as aval,
       solicitud.ds_actividades as actividades,
       solicitud.ds_convenio as convenioB,
       solicitud.ds_cvprofesor as cvprofesor,
       solicitud.ds_profesor as profesor,
       solicitud.ds_lugarprofesor as lugarprofesor,
       CAST(solicitud.bl_congreso AS UNSIGNED) as congreso,
       CAST(solicitud.bl_nacional AS UNSIGNED) as nacional,
       solicitud.ds_convenio as convenioC,
       solicitud.ds_disciplina as disciplina,
       solicitud.ds_googleScholar  as scholar,
       solicitud.ds_linkreunion as link,solicitud.ds_resumentrabajo as resumen,solicitud.ds_relevanciatrabajo as relevancia,solicitud.ds_modalidadtrabajo as modalidad,solicitud.ds_libros as libros,solicitud.ds_compilados as compilados,
       solicitud.ds_capitulos as capitulos,solicitud.ds_articulos as articulos,solicitud.ds_congresos as congresos,solicitud.ds_patentes as patentes,solicitud.ds_intelectuales as intelectuales,solicitud.ds_informes as informes,
       solicitud.ds_tesis as tesis,solicitud.ds_tesinas as tesinas,solicitud.ds_becas as becas,solicitud.ds_observaciones as observaciones, solicitud.ds_objetivoC as objetivosC,solicitud.ds_planC as planC,
       solicitud.ds_relacionProyectoC as relacionProyectoC,solicitud.ds_aportesC as aportesC,solicitud.ds_actividadesC as actividadesC,solicitud.ds_generalB as generalB,
       solicitud.ds_especificoB as especificoB,solicitud.ds_actividadesB as actividadesB,solicitud.ds_cronogramaB as cronogramaB,solicitud.ds_aportesB as aportesB,solicitud.ds_relevanciaB as relevanciaB,
       solicitud.ds_relevanciaA as relevanciaA,solicitud.ds_justificacionB as justificacionB")
            ->whereNull('cyt_solicitud_estado.fechaHasta')
            ->orderBy('solicitud.cd_solicitud')
            ->chunk(1000, function ($rows) use (&$totalFilas, &$totalInsertadas, &$totalOmitidas, &$skippedRows){
                $totalFilas += count($rows);
                $data = collect($rows)->map(function ($row) use (&$skippedRows, &$totalOmitidas) {




                    $estadosValidos = [
                        'Creada','Recibida','Admitida','No Admitida','Otorgada-No-Rendida','En evaluación','No otorgada','Evaluada','Otorgada-Rendida','Otorgada-Renunciada','Retirada','Otorgada-Devuelta'
                    ];

                    $estadoRow = trim((string)$row->estado);

                    if (!empty($estadoRow) && !in_array($estadoRow, $estadosValidos)) {
                        // Solo omitimos si tiene valor y no está en la lista
                        $skippedRows[] = [
                            'id' => $row->id,
                            'motivo' => 'Estado inválida',
                            'estado' => $row->estado,
                            'tipo' => null,
                            'deddoc' => null,
                            'motivo' => null,
                            'institucion' => null,
                            'beca' => null,
                        ];
                        $totalOmitidas++;
                        return null; // omite la fila
                    }

                    $estadoFinal = empty($estadoRow) ? null : $estadoRow;

                    $email = filter_var($row->email, FILTER_VALIDATE_EMAIL) ? $row->email : null;

                    // 🧹 LIMPIEZA DE FECHA
                    $fecha = $row->fecha;

                    if (
                        empty($fecha) ||
                        $fecha === '0000-00-00' ||
                        $fecha === '0000-00-00 00:00:00'
                    ) {
                        $fecha = null;
                    }



                    $egresogrado = $row->egresogrado;

                    if (
                        empty($egresogrado) ||
                        $egresogrado === '0000-00-00' ||
                        $egresogrado === '0000-00-00 00:00:00'
                    ) {
                        $egresogrado = null;
                    }



                    $valoresValidos = ['Exclusiva','Semi Exclusiva','Simple'];
                    $deddocRow = trim((string)$row->deddoc);

                    if (!empty($deddocRow) && !in_array($deddocRow, $valoresValidos)) {
                        // Solo omitimos si tiene valor y no está en la lista
                        $skippedRows[] = [
                            'id' => $row->id,
                            'motivo' => 'Deddoc inválida',
                            'estado' => null,
                            'tipo' => null,
                            'deddoc' => $row->deddoc,
                            'motivo' => null,
                            'institucion' => null,
                            'beca' => null,
                        ];
                        $totalOmitidas++;
                        return null; // omite la fila
                    }

                    $deddocFinal = empty($deddocRow) ? null : $deddocRow;

                    $ingreso_cargo = $row->ingreso_cargo;

                    if (
                        empty($ingreso_cargo) ||
                        $ingreso_cargo === '0000-00-00' ||
                        $ingreso_cargo === '0000-00-00 00:00:00'
                    ) {
                        $ingreso_cargo = null;
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
                    if (is_null($becaFinal)) {
                        $skippedRows[] = [

                            'id' => $row->id,

                            'motivo' => 'Beca inválida',
                            'estado' => null,
                            'tipo' => null,
                            'deddoc' => null,
                            'motivo' => null,
                            'institucion' => $row->institucion,
                            'beca' => $row->beca,
                        ];
                        $totalOmitidas++;
                        return null;
                    }

                    if (is_null($institucionFinal)) {
                        $skippedRows[] = [
                            'id' => $row->id,
                            'motivo' => 'Institución inválida',
                            'estado' => null,
                            'tipo' => null,
                            'deddoc' => null,
                            'motivo' => null,
                            'institucion' => $row->institucion,
                            'beca' => $row->beca,
                        ];
                        $totalOmitidas++;
                        return null;
                    }


                    $ingreso_carrerainv = $row->ingreso_carrerainv;

                    if (
                        empty($ingreso_carrerainv) ||
                        $ingreso_carrerainv === '0000-00-00' ||
                        $ingreso_carrerainv === '0000-00-00 00:00:00'
                    ) {
                        $ingreso_carrerainv = null;
                    }

                    $tipoValidos = ['Investigador Formado','Investigador En Formación'];
                    $tipoRow = trim((string)$row->tipo);

                    if (!empty($tipoRow) && !in_array($tipoRow, $tipoValidos)) {
                        // Solo omitimos si tiene valor y no está en la lista
                        $skippedRows[] = [
                            'id' => $row->id,
                            'motivo' => 'tipo inválida',
                            'estado' => null,
                            'tipo' => $row->tipo,
                            'deddoc' => null,
                            'motivo' => null,
                            'institucion' => null,
                            'beca' => null,
                        ];
                        $totalOmitidas++;
                        return null; // omite la fila
                    }

                    $tipoFinal = empty($tipoRow) ? null : $tipoRow;

                    $motivoValidos = ['Investigador Formado','Investigador En Formación'];
                    $motivoRow = trim((string)$row->motivo);

                    if (!empty($motivoRow) && !in_array($motivoRow, $motivoValidos)) {
                        // Solo omitimos si tiene valor y no está en la lista
                        $skippedRows[] = [
                            'id' => $row->id,
                            'motivo' => 'motivo inválida',
                            'estado' => null,
                            'tipo' => null,
                            'deddoc' => null,
                            'motivo' => $row->motivo,
                            'institucion' => null,
                            'beca' => null,
                        ];
                        $totalOmitidas++;
                        return null; // omite la fila
                    }

                    $motivoFinal = empty($motivoRow) ? null : $motivoRow;


                    $trabajo_desde = $row->trabajo_desde;

                    if (
                        empty($trabajo_desde) ||
                        $trabajo_desde === '0000-00-00' ||
                        $trabajo_desde === '0000-00-00 00:00:00'
                    ) {
                        $trabajo_desde = null;
                    }

                    $trabajo_hasta = $row->trabajo_hasta;

                    if (
                        empty($trabajo_hasta) ||
                        $trabajo_hasta === '0000-00-00' ||
                        $trabajo_hasta === '0000-00-00 00:00:00'
                    ) {
                        $trabajo_hasta = null;
                    }





                    return [
                        'id' => $row->id,
                        'investigador_id' => $row->investigador_id ?: null,
                        'periodo_id' => $row->periodo_id ?: null,
                        'proyecto1_id' => $row->proyecto1_id ?: null,
                        'proyecto2_id' => $row->proyecto2_id ?: null,
                        'estado' => $estadoFinal,
                        'email' => $email,
                        'notificacion' => $row->notificacion ?: 0,
                        'telefono' => trim($row->telefono),
                        'fecha' => $fecha,
                        'calle' => trim($row->calle),
                        'nro' => trim($row->nro),
                        'piso' => trim($row->piso),
                        'depto' => trim($row->depto),
                        'cp' => $row->cp ?: null,
                        'titulo_id' => $row->titulo_id ?: null,
                        'egresogrado' => $egresogrado,
                        'unidad_id' => $row->unidad_id ?: null,
                        'cargo_id' => $row->cargo_id ?: null,
                        'deddoc' => $deddocFinal,
                        'ingreso_cargo' => $ingreso_cargo,
                        'facultad_id' => $row->facultad_id ?: null,
                        'facultadplanilla_id' => $row->facultadplanilla_id ?: null,
                        'carrerainv_id' => $row->carrerainv_id ?: null,
                        'organismo_id' => $row->organismo_id ?: null,
                        'ingreso_carrerainv' => $ingreso_carrerainv,
                        'unidadcarrera_id' => $row->unidadcarrera_id ?: null,
                        'beca' => $becaFinal,
                        'institucion' => $institucionFinal,
                        'periodobeca' => trim($row->periodobeca),
                        'unlp' => $row->unlp ?: 0,
                        'unidadbeca_id' => $row->unidadbeca_id ?: null,
                        'categoria_id' => $row->categoria_id ?: null,
                        'sicadi_id' => $row->sicadi_id ?: null,
                        'tipo' => $tipoFinal,
                        'motivo' => $motivoFinal,
                        'objetivo' => trim($row->objetivo),
                        'curriculum' => trim($row->curriculum),
                        'trabajo' => trim($row->trabajo),
                        'aceptacion' => trim($row->aceptacion),
                        'titulotrabajo' => trim($row->titulotrabajo),
                        'autores' => trim($row->autores),
                        'congresonombre' => trim($row->congresonombre),
                        'lugartrabajo' => trim($row->lugartrabajo),
                        'trabajodesde' => $trabajo_desde,
                        'trabajohasta' => $trabajo_hasta,
                        'resumen' => trim($row->resumen),
                        'relevancia' => trim($row->relevancia),
                        'invitacion' => trim($row->invitacion),
                        'modalidad' => trim($row->modalidad),
                        'aval' => trim($row->aval),
                        'actividades' => trim($row->actividades),
                        'convenioB' => trim($row->convenioB),
                        'cvprofesor' => trim($row->cvprofesor),
                        'profesor' => trim($row->profesor),
                        'lugarprofesor' => trim($row->lugarprofesor),
                        'libros' => trim($row->libros),
                        'compilados' => trim($row->compilados),
                        'capitulos' => trim($row->capitulos),
                        'articulos' => trim($row->articulos),
                        'congresos' => trim($row->congresos),
                        'patentes' => trim($row->patentes),
                        'intelectuales' => trim($row->intelectuales),
                        'informes' => trim($row->informes),
                        'congreso' => $row->congreso ?: 0,
                        'tesis' => trim($row->tesis),
                        'tesinas' => trim($row->tesinas),
                        'nacional' => $row->nacional ?: 0,
                        'becas' => trim($row->becas),
                        'objetivosC' => trim($row->objetivosC),
                        'planC' => trim($row->planC),
                        'relacionproyectoC' => trim($row->relacionproyectoC),
                        'aportesC' => trim($row->aportesC),
                        'actividadesC' => trim($row->actividadesC),
                        'convenioC' => trim($row->convenioC),
                        'generalB' => trim($row->generalB),
                        'especificoB' => trim($row->especificoB),
                        'actividadesB' => trim($row->actividadesB),
                        'cronogramaB' => trim($row->cronogramaB),
                        'aportesB' => trim($row->aportesB),
                        'relevanciaB' => trim($row->relevanciaB),
                        'relevanciaA' => trim($row->relevanciaA),
                        'scholar' => trim($row->scholar),
                        'link' => trim($row->link),
                        'monto' => is_numeric($row->monto) ? (float)$row->monto : null,
                        'puntaje' => is_numeric($row->puntaje) ? (float)$row->puntaje : null,
                        'diferencia' => is_numeric($row->diferencia) ? (float)$row->diferencia : null,
                        'observaciones' => trim($row->observaciones),


                        'justificacionB' => trim($row->justificacionB),


                        'disciplina' => trim($row->disciplina),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->filter()->toArray();
                try {
                    if (!empty($data)) {
                        DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=0');

                    DB::connection('mysql')
                        ->table('viajes')
                        ->upsert(
                            $data,
                            ['id'],
                            [
                                'investigador_id','periodo_id','proyecto1_id','proyecto2_id','estado','email','fecha','egresogrado','notificacion','cp',
                                'curriculum','telefono','calle','nro','piso','depto',
                                'institucion','facultadplanilla_id','deddoc','cargo_id','ingreso_cargo','facultad_id','unidad_id','carrerainv_id','organismo_id',
                                'ingreso_carrerainv','periodobeca','unidadcarrera_id','unidadbeca_id','unlp','categoria_id','sicadi_id','tipo',
                                'motivo','trabajo','aceptacion','titulotrabajo','autores','congresonombre','lugartrabajo','trabajodesde','trabajohasta',
                                'resumen','relevancia','invitacion','modalidad','aval','actividades','convenioB','cvprofesor','profesor','lugarprofesor',
                                'libros','compilados','capitulos','articulos','congresos','patentes','intelectuales','informes','congreso','tesis',
                                'tesinas','nacional','becas','objetivosC','planC','relacionproyectoC','aportesC','actividadesC','convenioC','generalB',
                                'especificoB','actividadesB','cronogramaB','aportesB','relevanciaB','relevanciaA','scholar','link','monto','observaciones','titulo_id','beca',
                                'puntaje','diferencia','justificacionB','objetivo','disciplina','updated_at'
                            ]
                        );
                        DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=1');
                        $totalInsertadas += count($data);
                    }
                } catch (\Illuminate\Database\QueryException $e) {
                    // Revisar si es error de duplicado
                    if ($e->errorInfo[0] == '23000' && $e->errorInfo[1] == 1062) {
                        $skippedRows[] = [
                            'id' => null, // No siempre hay un id único si falla todo el batch
                            'motivo' => 'Error duplicado: ' . $e->getMessage(),
                            'estado' => null,
                            'tipo' => null,
                            'deddoc' => null,
                            'institucion' => null,
                            'beca' => null,
                        ];
                        $totalOmitidas += count($data); // Omitimos todo el batch que falló
                    } else {
                        // si es otro error, relanzarlo
                        throw $e;
                    }
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
