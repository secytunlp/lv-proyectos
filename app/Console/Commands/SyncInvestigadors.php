<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncInvestigadors extends Command
{
    protected $signature = 'sync:investigadors';
    protected $description = 'Sincroniza investigadores desde DB origen a investigadors';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Iniciando sincronización de investigadors...');

        $skippedRows = [];
        $totalFilas = 0;
        $totalInsertadas = 0;
        $totalOmitidas = 0;

        DB::connection('mysql_origen')
                    ->table('docente as d')
                    ->leftJoin('deddoc as dd', 'd.cd_deddoc', '=', 'dd.cd_deddoc')
                    ->selectRaw("
                d.cd_docente as id,
                d.nu_ident as ident,
                d.cd_docente as persona_id,
                d.cd_categoria as categoria_id,
                d.cd_categoriasicadi as sicadi_id,
                CASE d.cd_carrerainv WHEN '11' THEN null WHEN '10' THEN null ELSE d.cd_carrerainv END as carrerainv_id,
                CASE d.cd_organismo WHEN '7' THEN null ELSE d.cd_organismo END as organismo_id,
                CASE d.cd_facultad WHEN '574' THEN null ELSE d.cd_facultad END as facultad_id,
                CASE d.cd_cargo WHEN '6' THEN null ELSE d.cd_cargo END as cargo_id,
                CASE dd.ds_deddoc
                    WHEN 's/d' THEN null
                    WHEN 'SI-1' THEN 'Simple'
                    WHEN 'SE-1' THEN 'Semi Exclusiva'
                    ELSE dd.ds_deddoc
                END as deddoc,
                d.cd_universidad as universidad_id,
                CASE d.cd_titulo WHEN '9999' THEN null ELSE d.cd_titulo END as titulo_id,
                CASE d.cd_titulopost WHEN '9999' THEN null ELSE d.cd_titulopost END as titulopost_id,
                d.cd_unidad as unidad_id,
                CASE `ds_orgbeca`
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
               CASE `ds_tipobeca`
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
                d.nu_materias as materias,
                d.nu_totalMat as total,
                d.ds_carrera as carrera
            ")
            ->orderBy('d.cd_docente')
            ->chunk(1000, function ($rows) use (&$totalFilas, &$totalInsertadas, &$totalOmitidas, &$skippedRows) {

                $totalFilas += count($rows);

                $data = collect($rows)->map(function ($row) use (&$skippedRows, &$totalOmitidas) {

                    // Validaciones básicas: persona_id o ident vacíos
                    if (empty($row->persona_id) || empty($row->ident)) {
                        $skippedRows[] = [
                            'id' => $row->id,
                            'persona_id' => $row->persona_id,
                            'ident' => $row->ident,
                            'motivo' => 'Falta persona_id o ident',
                            'institucion' => $row->institucion,
                            'beca' => $row->beca,
                        ];
                        $totalOmitidas++;
                        return null;
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
                            'persona_id' => $row->persona_id,
                            'ident' => $row->ident,
                            'motivo' => 'Beca inválida',
                            'institucion' => $row->institucion,
                            'beca' => $row->beca,
                        ];
                        $totalOmitidas++;
                    }

                    if (is_null($institucionFinal) && !empty($row->institucion)) {
                        $skippedRows[] = [
                            'id' => $row->id,
                            'persona_id' => $row->persona_id,
                            'ident' => $row->ident,
                            'motivo' => 'Institución inválida',
                            'institucion' => $row->institucion,
                            'beca' => $row->beca,
                        ];
                        $totalOmitidas++;
                    }

                    return [
                        'id' => $row->id,
                        'ident' => $row->ident,
                        'persona_id' => $row->persona_id,
                        'categoria_id' => $row->categoria_id ?: null,
                        'sicadi_id' => $row->sicadi_id ?: null,
                        'carrerainv_id' => $row->carrerainv_id ?: null,
                        'organismo_id' => $row->organismo_id ?: null,
                        'facultad_id' => $row->facultad_id ?: null,
                        'cargo_id' => $row->cargo_id ?: null,
                        'deddoc' => $row->deddoc ?: null,
                        'universidad_id' => $row->universidad_id ?: null,
                        'titulo_id' => $row->titulo_id ?: null,
                        'titulopost_id' => $row->titulopost_id ?: null,
                        'unidad_id' => $row->unidad_id ?: null,
                        'institucion' => $institucionFinal,
                        'beca' => $becaFinal,
                        'materias' => $row->materias ?: null,
                        'total' => $row->total ?: null,
                        'carrera' => $row->carrera ?: null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->filter()->toArray();

                if (!empty($data)) {
                    DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=0');
                    DB::connection('mysql')
                        ->table('investigadors')
                        ->upsert(
                            $data,
                            ['id'], // clave única
                            [
                                'ident','persona_id','categoria_id','sicadi_id','carrerainv_id',
                                'organismo_id','facultad_id','cargo_id','deddoc','universidad_id',
                                'titulo_id','titulopost_id','unidad_id','institucion','beca',
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
        $this->info("Filas omitidas: $totalOmitidas");

        if (!empty($skippedRows)) {
            $this->info("Detalle de filas omitidas:");
            foreach ($skippedRows as $skip) {
                $this->line(
                    "ID {$skip['id']} - Persona: {$skip['persona_id']} - Ident: {$skip['ident']} - Motivo: {$skip['motivo']} - Institución: {$skip['institucion']} - Beca: {$skip['beca']}"
                );
            }
        }
    }
}
