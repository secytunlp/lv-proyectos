<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncIntegranteEstados extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:integranteestados';

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
            ->table('cyt_integrante_estado')
            ->leftJoin('cyt_user', 'cyt_integrante_estado.user_oid', '=', 'cyt_user.oid')
            ->leftJoin('integrante', 'integrante.oid', '=', 'cyt_integrante_estado.integrante_oid')
            ->leftJoin('docente', 'integrante.cd_docente', '=', 'docente.cd_docente')
            ->leftJoin('deddoc', 'docente.cd_deddoc', '=', 'deddoc.cd_deddoc')

            ->selectRaw("
                cyt_integrante_estado.oid as id,cyt_integrante_estado.integrante_oid as integrante_id,
       CASE cyt_integrante_estado.user_oid
           WHEN 1 THEN '2'
           ELSE NULL END as user_id, cyt_user.ds_name as user_name,
       CASE cyt_integrante_estado.tipoInvestigador_oid
           WHEN 1 THEN 'Director'
           WHEN 2 THEN 'Codirector'
           WHEN 3 THEN 'Investigador Formado'
           WHEN 4 THEN 'Investigador En Formación'
           WHEN 5 THEN 'Becario, Tesista'
           WHEN 6 THEN 'Colaborador'
           ELSE '' END as tipo,
       CASE cyt_integrante_estado.dt_alta
           WHEN '0000-00-00' THEN NULL
           ELSE cyt_integrante_estado.dt_alta END as alta,
       CASE cyt_integrante_estado.dt_baja
           WHEN '0000-00-00' THEN NULL
           ELSE cyt_integrante_estado.dt_baja END as baja,
       CASE cyt_integrante_estado.dt_cambio
           WHEN '0000-00-00' THEN NULL
           ELSE cyt_integrante_estado.dt_cambio END as cambio,
       cyt_integrante_estado.nu_horasinv as horas,
       CASE cyt_integrante_estado.estado_oid
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
           END as estado, cyt_integrante_estado.ds_consecuencias as consecuencias
    , cyt_integrante_estado.ds_motivos as motivos, cyt_integrante_estado.ds_reduccionHS as reduccion,
       cyt_integrante_estado.categoria_oid as categoria_id, cyt_integrante_estado.categoriasicadi_oid as sicadi_id,
       CASE `ds_deddoc`
           WHEN 's/d' THEN null
           WHEN 'SI-1' THEN 'Simple'
           WHEN 'SE-1' THEN 'Semi Exclusiva'
           ELSE ds_deddoc END as deddoc,
       CASE cyt_integrante_estado.cargo_oid
           WHEN '6' THEN null
           ELSE cyt_integrante_estado.cargo_oid END as cargo_id,

       CASE cyt_integrante_estado.facultad_oid
           WHEN '574' THEN null
           ELSE cyt_integrante_estado.facultad_oid END as facultad_id,
       CASE cyt_integrante_estado.carrerainv_oid
           WHEN '11' THEN null
           WHEN '10' THEN null
           ELSE cyt_integrante_estado.carrerainv_oid END as carrerainv_id,
       CASE cyt_integrante_estado.organismo_oid
           WHEN '7' THEN null
           else cyt_integrante_estado.organismo_oid END as organismo_id,
       CASE cyt_integrante_estado.ds_orgbeca
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
                   WHEN 'ANPCY' THEN 'ANPCyT'
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
                   WHEN 'FONCYT' THEN 'OTRA'
                   WHEN 'COMISIÓN DE INVESTIGACIONES CIENTÍFICAS DE LA PROVINCIA DE BUENOS AIRES' THEN 'CIC'
                   WHEN 'COMISION DE INVESTIGACIONES CIENTIFICAS DE LA PROVINCIA DE BUENOS AIRES (CIC)' THEN 'CIC'
                   WHEN 'CONSEJO DE INVESTIGACIONES CIENTÍFICAS DE LA PROV DE BS. AS.' THEN 'CIC'
                   WHEN 'ANCYPT' THEN 'ANPCyT'
                   WHEN 'FAU-UNLP' THEN 'UNLP'
                   WHEN 'CENTRO DE INVESTIGACIONES URBANAS Y TERRITORIALES (CIUT)' THEN 'OTRA'
                   WHEN 'FACULTAD DE ARQUITECTURA Y URBANISMO-UNLP' THEN 'UNLP'
                   WHEN ' UNLP FBA' THEN 'UNLP'
                   WHEN 'FAU UNLP' THEN 'UNLP'
                   WHEN 'UNLP-CIN' THEN 'CIN'
                   WHEN 'CONSEJO INTERUNIVERSITARIO NACIONAL' THEN 'CIN'
                   WHEN 'COPNICET' THEN 'CONICET'
                   WHEN 'CONCICET' THEN 'CONICET'
                   WHEN 'CIC PBA' THEN 'CIC'
                   WHEN 'CIC. PBA' THEN 'CIC'
                   WHEN 'DAAD' THEN 'OTRA'
                   WHEN 'COMISIÓN DE INVESTIGACIONES CIENTÍFICAS (CIC)' THEN 'CIC'
                   WHEN 'CONSEJO NAC.DE INVEST.CIENTIF.Y TECNICAS' THEN 'CONICET'
                   WHEN 'CICBA' THEN 'CIC'
                   WHEN 'UNIVERSIDAD POLITECNICA DE CATALUNIA' THEN 'OTRA'
                   WHEN 'INSTITUTO DE ASTROFÍSICA CANARIAS' THEN 'OTRA'
                   WHEN 'UNIVERSIDAD NACIONAL DE LUJAN' THEN 'OTRA'
                   WHEN 'UNIVERSIDADE FEDERAL DO MINAS GERAIS' THEN 'OTRA'
                   WHEN 'UNNE' THEN 'OTRA'
                   WHEN 'UNIV.NAC.DE SALTA / CONSEJO DE INVESTIGACION' THEN 'OTRA'
                   WHEN 'UNIVERSITE DENIS DIDEROT - PARIS 7. FRANCIA' THEN 'OTRA'
                   WHEN 'UNIV.NAC.DE SAN MARTIN / INST.DE CS.DE LA REHABILITACION Y EL MOVIMIENTO' THEN 'OTRA'
                   WHEN 'UNIV.NAC.DE SALTA / CONSEJO DE INVESTIGACION' THEN 'OTRA'
                   WHEN 'SENESCYT (SECRETARÍA NACIONAL DE CIENCIA Y TECNOLOGÍA &#65533; ECUADOR)' THEN 'OTRA'
                   ELSE
                       CASE
                           WHEN cyt_integrante_estado.bl_becaEstimulo = 1 THEN 'CIN'
                           ELSE cyt_integrante_estado.ds_orgbeca
                           END
                   END AS institucion,
       CASE cyt_integrante_estado.ds_tipobeca
           WHEN 'POSTGRADO/DOCTORADO' THEN 'Beca doctoral'
                   WHEN 'POSTGRADO/DOCTORADO' THEN 'Beca posdoctoral'
                   WHEN 'Postdoctorado' THEN 'Beca posdoctoral'
                   WHEN 'BECA INTERNA DE POSTGRADO TIPO II' THEN 'TIPO II'
                   WHEN 'BECA INTERNA DE POSTGRADO TIPO II' THEN 'TIPO II'
                   WHEN 'Agencia 2' THEN 'Beca superior'
                   WHEN 'POSDOCTORAL' THEN 'Beca posdoctoral'
                   WHEN 'BECARIO POSDOCTORAL' THEN 'Beca posdoctoral'
                   WHEN 'B' THEN 'TIPO B'
                   WHEN 'B DE UNLP' THEN 'TIPO B'
                   WHEN 'POST-DOCTORADO' THEN 'Beca posdoctoral'
                   WHEN 'POS-DOCTORAL' THEN 'Beca posdoctoral'
                   WHEN 'II' THEN 'TIPO II'
                   WHEN 'I' THEN 'TIPO I'
                   WHEN 'BECA INTERNA DE POSTGRADO TIPO 1' THEN 'TIPO I'
                   WHEN 'BECA PG T1' THEN 'TIPO I'
                   WHEN 'TIPO 1 PARA DOCTORADO' THEN 'TIPO I'
                   WHEN 'CONICET I' THEN 'TIPO I'
                   WHEN 'FORMACIÓN DOCTORAL TIPO I' THEN 'TIPO I'
                   WHEN 'POSTDOCTORAL' THEN 'Beca posdoctoral'
                   WHEN 'BECA DOCTORAL TIPO II' THEN 'TIPO II'
                   WHEN 'BECARIO POSTGRADO TIPO II' THEN 'TIPO II'
                   WHEN 'PERFECCIONAMIENTO' THEN 'BECA DE PERFECCIONAMIENTO'
                   WHEN 'PERFECCCIONAMIENTO' THEN 'BECA DE PERFECCIONAMIENTO'
                   WHEN 'DOCTORAL' THEN 'Beca doctoral'
                   WHEN 'POSTGRADO' THEN 'Beca doctoral'
                   WHEN 'BECARIO DOCTORANDO' THEN 'Beca doctoral'
                   WHEN 'POSTGRADO TIPO II' THEN 'TIPO II'
                   WHEN 'BECARIO POSTGRADO TIPO II' THEN 'TIPO II'
                   WHEN 'BECA INTERNA POSGRADO TIPO II' THEN 'TIPO II'
                   WHEN 'BEWCA DOCTORAL TIPO II' THEN 'TIPO II'
                   WHEN 'BECA DOCTORAL INTERNA' THEN 'Beca doctoral'
                   WHEN 'POSGRADO INTERNA TIPO I' THEN 'TIPO I'
                   WHEN 'POSTGRADO TIPO 1' THEN 'TIPO I'
                   WHEN 'TIPO 1 DOCTORAL' THEN 'TIPO I'
                   WHEN 'BECARIA DE POSGRADO TIPO I' THEN 'TIPO I'
                   WHEN 'BECA INTERNA DOCTORAL TIPO I' THEN 'TIPO I'
                   WHEN 'BECA DE POSTGRADO TIPO II' THEN 'TIPO II'
                   WHEN 'DOCTORAL TIPO II' THEN 'TIPO II'
                   WHEN 'BECA TIPO II' THEN 'TIPO II'
                   WHEN 'BECARIO DOCTORAL TIPO II' THEN 'TIPO II'
                   WHEN 'TIPO II, CONICET' THEN 'TIPO II'
                   WHEN 'INTERNA DE POSGRADO TIPO 1' THEN 'TIPO I'
                   WHEN 'BECA DE POSTGRADO TIPO 1' THEN 'TIPO I'
                   WHEN 'BECA INTERNA DE POSTGRADO TIPO I (3 AÑOS) CON PAÍSES LATINOAMERICANOS' THEN 'TIPO I'
                   WHEN '2' THEN 'TIPO II'
                   WHEN 'DOCTORAL TIPO I' THEN 'TIPO I'
                   WHEN ' I' THEN 'TIPO I'
                   WHEN 'TIPO 1 DOCTORAL' THEN 'TIPO I'
                   WHEN 'POSDOCTORAL I' THEN 'TIPO I'
                   WHEN 'TIPO1' THEN 'TIPO I'
                   WHEN 'POST-DOCTORAL' THEN 'Beca posdoctoral'
                   WHEN 'BECARIO POSDOCTORAL' THEN 'Beca posdoctoral'
                   WHEN 'BECA DE POSGRADO TIPO II' THEN 'TIPO II'
                   WHEN 'CATEGORÍA II' THEN 'TIPO II'
                   WHEN 'I' THEN 'TIPO I'
                   WHEN 'BECARIO TIPO 1' THEN 'TIPO I'
                   WHEN 'POSTGRADO/ESPECIALIZACION TIPO II' THEN 'TIPO II'
                   WHEN 'FORMACIÓN SUPERIOR EN LA INVESTIGACIÓN Y DESARROLLO ARTÍSTICO, CIENTÍFICO Y TECNOLÓGICO' THEN 'Formación Superior'
                   WHEN 'FORMACIóN SUPERIOR' THEN 'Formación Superior'
                   WHEN 'BECA DE RETENCIÓN DE POSGRADUADO' THEN 'RETENCION DE POSTGRADUADO'
                   WHEN 'RETENCIÓN DE POSGRADUADO' THEN 'RETENCION DE POSTGRADUADO'
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
                   WHEN 'TIPO II INTERNA' THEN 'TIPO II'
                   WHEN 'POSTGRADO TIPO I' THEN 'TIPO I'
                   WHEN 'BECA DE DOCTORADO TIPO I' THEN 'TIPO I'
                   WHEN 'POSTGRADO/DOCTORADO BECA TIPO B' THEN 'TIPO B'
                   WHEN ' POSTGRADO/DOCTORADO BECA TIPO B' THEN 'TIPO B'
                   WHEN 'POST DOCTORAL' THEN 'Beca posdoctoral'
                   WHEN 'TIPO 2' THEN 'TIPO II'
                   WHEN 'BECA DOCTIRAL TIPO II' THEN 'TIPO II'
                   WHEN 'POSGRADO' THEN 'Beca doctoral'
                   WHEN 'BECA DOC 15' THEN 'Beca doctoral'
                   WHEN 'POSDOCTORADO' THEN 'Beca posdoctoral'
                   WHEN 'BECA POSTODOCTORAL INTERNA' THEN 'Beca posdoctoral'
                   WHEN 'BECA DE POSGRADO TIPO 1' THEN 'TIPO I'
                   WHEN 'POSGRADUADOS (RETENCIÓN DE RRHH)' THEN 'RETENCION DE POSTGRADUADO'
                   WHEN 'II, DOCTORADO' THEN 'TIPO II'
                   WHEN 'BECA DE SCYT TIPO B' THEN 'TIPO B'
                   WHEN 'CONICET 1' THEN 'TIPO I'
                   WHEN 'BECA TIPO 1' THEN 'TIPO I'
                   WHEN 'BECARIO DE POSGRADO TIPO I' THEN 'TIPO I'
                   WHEN 'I' THEN 'TIPO I'
                   WHEN 'II DOCTORAL' THEN 'TIPO II'
                   WHEN 'ENTRENAMIENTO' THEN 'Beca de entrenamiento'
                   WHEN 'BECA DOCTORAL PG T II 11' THEN 'TIPO II'
                   WHEN 'UNLP 1' THEN 'TIPO A'
                   WHEN 'Agencia 1' THEN 'Beca inicial'
                   WHEN 'Beca Inicial FONCyT' THEN 'Beca inicial'
                   WHEN 'RETENCIÓN DE GRADUADOS FORMADOS POR LA UNLP' THEN 'RETENCION DE POSTGRADUADO'
                   WHEN 'DE RETENCION DE POSGRADUADOS' THEN 'RETENCION DE POSTGRADUADO'
                   WHEN 'BECA POSTGRADO TIPO II CON PAISES LATINOAMERICANOSI' THEN 'TIPO II'
                   WHEN 'DOCTORAL I' THEN 'TIPO I'
                   WHEN 'CONICET TIPO I' THEN 'TIPO I'
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
                   WHEN 'BECA DE ESTÍMULO A VOCACIONES CIENTÍFICAS' THEN 'EVC'
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
                   WHEN ' Finalización de Doctorado' THEN 'Beca finalización del doctorado'
                   WHEN 'Beca finalizacion del doctorado' THEN 'Beca finalización del doctorado'
                   WHEN 'Finalizacion de doctorado (tipo II)' THEN 'Beca finalización del doctorado'
                   WHEN 'POSGRADO-DOCTORAL' THEN 'Beca doctoral'
                   WHEN 'POSGRADO TIPO 1' THEN 'TIPO I'
                   WHEN 'BECA TIPO I PARA LA REALIZACIÓN DE DOCTORADO' THEN 'TIPO I'
                   WHEN 'LATINOAMERICANA TIPO I' THEN 'TIPO I'
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
                   WHEN 'BECA INICIACION' THEN 'Iniciación'
                   WHEN 'BECA TIPO I' THEN 'TIPO I'
                   WHEN 'I DOCTORAL' THEN 'TIPO I'
                   WHEN 'BECA DE ESTIMULO A LA INVESTIGACIÓN' THEN 'EVC'
                   WHEN 'BECA DE ENTRENAMIENTO PARA JOVENES INVESTIGADORES' THEN 'Beca de entrenamiento'
                   WHEN 'CIC 1' THEN 'Beca doctoral'
                   WHEN 'BECARIO TIPO II' THEN 'TIPO II'
                   WHEN 'POSGRADO DOCTORAL TIPO II' THEN 'TIPO II'
                   WHEN 'ENTRENAMIENTO EN INVESTIGACION' THEN 'Beca de entrenamiento'
                   WHEN 'TIPO B (DOCTORADO)' THEN 'TIPO B (DOCTORADO)'
                   WHEN 'BECARIO DE POSTGRADO TIPO I' THEN 'TIPO I'
                   WHEN 'TIPO I PAISES LATINOAMERICANOS' THEN 'TIPO I'
                   WHEN 'POSTDOCTORAL LATINOAMERICANO' THEN 'TIPO II'
                   WHEN 'DOCTORAL- TIPO II' THEN 'TIPO II'
                   WHEN 'BECAS DE ESTÍMULO A LAS VOCACIONES CIENTÍFICAS 2011' THEN 'EVC'
                   WHEN 'BECA INTERNA DE ENTRENAMIENTO EN INVESTIGACIÓN' THEN 'Beca de entrenamiento'
                   WHEN 'BECARIO DE INICIACIÓN' THEN 'Iniciación'
                   WHEN '1' THEN 'TIPO I'
                   WHEN 'DOCTORAL TIPO A' THEN 'TIPO A'
                   WHEN 'BECARIO DOCTORAL TIPO I' THEN 'TIPO I'
                   WHEN 'DOCTORAL TIPOI' THEN 'TIPO I'
                   WHEN 'DOCTORAL, TIPO 1' THEN 'TIPO I'
                   WHEN 'BECA INTERNA TIPO A' THEN 'TIPO A'
                   WHEN 'INVESTIGACION TIPO \"A\"' THEN 'TIPO A'
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
                   WHEN 'BECA POSGRADO' THEN 'TIPO I'
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
                   WHEN 'Beca maestria' THEN 'Beca maestría'
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
                       WHEN cyt_integrante_estado.bl_becaEstimulo = 1 THEN 'EVC'
                       ELSE cyt_integrante_estado.ds_tipobeca
                       END
                   END AS beca,
    cyt_integrante_estado.fechaDesde as desde, cyt_integrante_estado.fechaHasta as hasta, cyt_integrante_estado.motivo as comentarios")
            ->orderBy('cyt_integrante_estado.oid')
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
                            'tipo' => null,
                            'deddoc' => null,
                            'institucion' => null,
                            'beca' => null,
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
                            'estado' => null,
                            'tipo' => $row->tipo,
                            'deddoc' => null,
                            'institucion' => null,
                            'beca' => null,
                        ];
                        $totalOmitidas++;
                        return null; // omite la fila
                    }

                    $tipoFinal = empty($tipoRow) ? null : $tipoRow;



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
                            'institucion' => null,
                            'beca' => null,
                        ];
                        $totalOmitidas++;
                        return null; // omite la fila
                    }

                    $deddocFinal = empty($deddocRow) ? null : $deddocRow;



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
                            'estado' => null,
                            'tipo' => null,
                            'deddoc' => null,
                            'institucion' => null,
                            'beca' => $row->beca,
                        ];
                        $totalOmitidas++;
                    }

                    if (is_null($institucionFinal) && !empty($row->institucion)) {
                        $skippedRows[] = [
                            'id' => $row->id,
                            'motivo' => 'Institucion inválida',
                            'estado' => null,
                            'tipo' => null,
                            'deddoc' => null,
                            'institucion' => $row->institucion,
                            'beca' => null,
                        ];
                        $totalOmitidas++;
                    }

                    $desde = $row->desde;

                    if (
                        empty($desde) ||
                        $desde === '0000-00-00' ||
                        $desde === '0000-00-00 00:00:00'
                    ) {
                        $desde = null;
                    }

                    $hasta = $row->hasta;

                    if (
                        empty($hasta) ||
                        $hasta === '0000-00-00' ||
                        $hasta === '0000-00-00 00:00:00'
                    ) {
                        $hasta = null;
                    }



                    return [
                        'id' => $row->id,
                        'integrante_id' => $row->integrante_id ?: null,
                        'user_id' => $row->user_id ?: null,
                        'user_name' => trim($row->user_name),
                        'tipo' => $tipoFinal,
                        'alta' => $alta,
                        'baja' => $baja,
                        'cambio' => $cambio,
                        'horas' => $row->horas ?: null,
                        'estado' => $estadoFinal,
                        'consecuencias' => trim($row->consecuencias),
                        'motivos' => trim($row->motivos),
                        'reduccion' => trim($row->reduccion),
                        'categoria_id' => $row->categoria_id ?: null,
                        'sicadi_id' => $row->sicadi_id ?: null,
                        'deddoc' => $deddocFinal,
                        'cargo_id' => $row->cargo_id ?: null,

                        'facultad_id' => $row->facultad_id ?: null,
                        'unidad_id' => $row->unidad_id ?: null,
                        'carrerainv_id' => $row->carrerainv_id ?: null,
                        'organismo_id' => $row->organismo_id ?: null,

                        'institucion' => $institucionFinal,
                        'beca' => $becaFinal,
                        'desde' => $desde,
                        'hasta' => $hasta,
                        'comentarios' => trim($row->comentarios),

                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->filter()->toArray();
                try {
                    if (!empty($data)) {
                        DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=0');

                    DB::connection('mysql')
                        ->table('integrante_estados')
                        ->upsert(
                            $data,
                            ['id'],
                            [
                                'integrante_id','user_id','user_name','tipo','alta','baja','cambio','horas','estado',
                                'consecuencias','motivos','reduccion',
                                'categoria_id','sicadi_id','deddoc','cargo_id','alta_cargo','facultad_id','unidad_id','carrerainv_id','organismo_id',
                                'institucion','beca','desde','hasta','comentarios'
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
