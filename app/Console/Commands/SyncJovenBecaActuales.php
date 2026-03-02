<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncJovenBecaActuales extends Command
{
    protected $signature = 'sync:jovenbecaactuales';
    protected $description = 'Sincroniza becas UNLP desde DB origen a investigadors';

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
                    ->table('solicitudjovenes')

                    ->selectRaw("
                solicitudjovenes.`cd_solicitud` as joven_id,CASE solicitudjovenes.ds_orgbeca
                                                       WHEN 'U.N.L.P' THEN 'UNLP'
                                                       WHEN 'U.N.L.P.' THEN 'UNLP'
                                                       WHEN 'Otro' THEN 'OTRA'
                                                       WHEN 'Consejo de Invest. Científicas de la Provincia de Bs As' THEN 'CIC'
                                                       WHEN 'COMISIÓN DE INVESTIGACIONES CIENTÍFICAS (CIC) PROVINCIA DE BUENOS AIRES' THEN 'CIC'
                                                       WHEN 'COMISIÓN DE INVETIGACIONES CIENTÍFICAS' THEN 'CIC'
                                                       WHEN 'Comisión de Investigaciones Científicas (CIC-PBA)' THEN 'CIC'
                                                       WHEN 'Comisión de Investigaciones Científicas' THEN 'CIC'
                                                       WHEN 'Comision de investigaciones cientificas' THEN 'CIC'
                                                       WHEN 'Consejo Nac. Invest. Científicas y Técnicas' THEN 'CONICET'
                                                       WHEN 'CONSEJO NACIONAL DE INVESTIGACIONES CIENTIFICAS Y TECNOLÓGICAS (CONICET)' THEN 'CONICET'
                                                       WHEN 'CENTRO DE INVESTIGACIONES CIENTÍFICAS-CIC-' THEN 'CIC'
                                                       WHEN 'UNIVERSIDAD NACIONAL DE LA PLATA' THEN 'UNLP'
                                                       WHEN 'CONSEJO DE INV. CIENTÍFICAS DE LA PROVINCIA DE BUENOS AIRES' THEN 'CIC'
                                                       WHEN 'COMISIÓN DE INVEST. CIENTIFICAS DE LA PROVINCIA DE BS AS' THEN 'CIC'
                                                       WHEN 'CIC (COMISIÓN DE INVESTIGACIONES CIENTÍFICAS)' THEN 'CIC'
                                                       WHEN 'COMISION INVESTIGACION CIENTIFICA DE LA PROVINCIA DE BUENOS AIRES' THEN 'CIC'
                                                       WHEN 'COMISION DE INVESTIGACIONES CIENTÍFICAS DE LA PCIA. DE BUENOS AIRES' THEN 'CIC'
                                                       WHEN 'COMISIÓN DE INVESTIGACIONES CIENTÍFICAS DE LA PROVINCIA DE BUENOS AIRES (CIC)' THEN 'CIC'
                                                       WHEN 'COMISIÓN DE INVESTIGACIONES CIENTÍFICAS - PROV. DE BS. AS.' THEN 'CIC'
                                                       WHEN 'Comisión de Investigaciones Científicas CIC' THEN 'CIC'
                                                       WHEN 'CONSEJO NACIONAL DE INVESTIGACIONES CIENTÍFICAS Y TÉCNICAS (CONICET)' THEN 'CONICET'
                                                       WHEN 'CONSEJO NACIONAL DE INVESTIGACIONES CIENTÍFICAS Y TÉCNICAS' THEN 'CONICET'
                                                       WHEN 'COMISIÓN NACIONAL DE INVESTIGACIONES CIENTÍFICAS Y TÉCNICAS- CONICET' THEN 'CONICET'
                                                       WHEN 'CONSEJO NACIONAL DE INVESTIGACIONES CIENTÍFICAS Y TÉCNICAS-CONICET-' THEN 'CONICET'
                                                       WHEN 'CONSEJO NACIONAL DE INVESTIGACIONES CIENTÍFICAS Y TECNOLÓGICAS' THEN 'CONICET'
                                                       WHEN 'CONSEJO DE INVESTIGACIONES CIENTÍFICAS Y TÉCNICAS (CONICET)' THEN 'CONICET'
                                                       WHEN 'CONSEJO NACIONAL DE INVESTIGACIONES CIENTÍFICAS Y TÉCNICAS, CONICET' THEN 'CONICET'
                                                       WHEN 'CONSEJO NACIONAL DE INVESTIGACIONES CIENTÍFICAS Y TÉCNICAS.' THEN 'CONICET'
                                                       WHEN 'CONSEJO NACIONAL DE INVESTIGACIONES CIENTÍFICAS Y TÉCNICAS CONICET' THEN 'CONICET'
                                                       WHEN 'INSTITUTO DE FÍSICA LA PLATA, CONICET' THEN 'CONICET'
                                                       WHEN 'CONICET (CONSEJO NACIONAL DE INVESTIGACIONES CIENTÍFICAS Y TÉCNICAS)' THEN 'CONICET'
                                                       WHEN 'CONSEJO NACIONAL DE INVESTIGACIONES CIENTÍFICAS Y TÉCNICAS - CONICET' THEN 'CONICET'
                                                       WHEN 'CENT.DE INV EN FERMENTACIONES INDUSTRIALES (I)- CONICET' THEN 'CONICET'
                                                       WHEN 'LABORATORIO DE INVESTIGACIÓN Y DESARROLLO DE MÉTODOS ANALÍTICOS (LIDMA) - FACULTAD DE CIENCIAS EXACTAS - UNLP' THEN 'CONICET'
                                                       WHEN 'CONICET - CCT LA PLATA' THEN 'CONICET'
                                                       WHEN 'CONSEJO  NAC. INVEST. CIENTÍFICAS Y  TÉCNICAS CONICET' THEN 'CONICET'
                                                       WHEN 'COMISIÓN NACIONAL DE INVESTIGACIONES CIENTÍFICAS Y TÉCNICAS' THEN 'CONICET'
                                                       WHEN 'COMISIÓN NACIONAL DE INVESTIGACIONES CIENTÍFICAS - CONICET' THEN 'CONICET'
                                                       WHEN 'COMISIÓN DE INVESTIGACIONES CIENTIFICAS' THEN 'CONICET'
                                                       WHEN 'UNLP - CONICET' THEN 'CONICET'
                                                       WHEN 'INFIVE -UNLP- CONICET' THEN 'CONICET'
                                                       WHEN 'conicet' THEN 'CONICET'
                                                       WHEN 'Conicet' THEN 'CONICET'
                                                       WHEN 'CONICET-FACULTAD DE CIENCIAS EXACTAS' THEN 'CONICET'
                                                       WHEN 'CINDECA - CONICET - UNLP' THEN 'CONICET'
                                                       WHEN 'Instituto de Biotecnología y Biología Molecular' THEN 'CONICET'
                                                       WHEN 'Instituto de Investigaciones en Humanidades y Ciencias de la Educación' THEN 'CONICET'


                                                       WHEN 'Centro de Investigación y Desarrollo en Criotecnología de Alimentos (CIDCA)' THEN 'CONICET'
                                                       WHEN 'Consejo Nac. Inv. Científicas y Técnicas' THEN 'CONICET'
                                                       WHEN 'CONICET Consejo Nacional de Investigaciones Científicas y Técnicas' THEN 'CONICET'
                                                       WHEN 'INSTITUTO DE ASTROFISICA DE LA PLATA' THEN 'CONICET'
                                                       WHEN 'Consejo Superior de Investigaciones Científicas y Tecnicas. CONICET' THEN 'CONICET'
                                                       WHEN 'INSTITUTO DE LIMNOLOGIA (CONICET/UNLP) (ILPLA)' THEN 'CONICET'
                                                       WHEN 'INIBIOLP' THEN 'CONICET'
                                                       WHEN 'Centro de Investigaciones Cardiovasculares' THEN 'CONICET'
                                                       WHEN 'IGEVET CONICET FCV UNLP' THEN 'CONICET'
                                                       WHEN 'CONICET-UNLP' THEN 'CONICET'
                                                       WHEN 'Centro de Química inorgánica,' THEN 'CONICET'
                                                       WHEN 'Centro de Investigación en Ciencia y Tecnología de Alimentos (CIDCA)' THEN 'CONICET'
                                                       WHEN 'CIDCA (CONICET - CIC - UNLP - Fac. de Cs. Exactas)' THEN 'CONICET'
                                                       WHEN 'Comisión Nacional de Investigaciones Científicas y Técnicas (CONICET)' THEN 'CONICET'
                                                       WHEN 'CENTRO DE INVESTIGACIONES EN CRIOTECNOLOGIA DE ALIMENTOS' THEN 'CONICET'
                                                       WHEN 'Consejo Nacional de Investigaciones Científicas y Técnicas-CONICET' THEN 'CONICET'
                                                       WHEN 'Instituto de Investigaciones Fisicoquímicas teóricas y Aplicadas, INIFTA-CONICET-UNLP' THEN 'CONICET'
                                                       WHEN 'Consejo Nacional de Investigaciones Científicas y Técnicas de Argentina' THEN 'CONICET'
                                                       WHEN 'Centro de Investigaciones Cardiovasculares. CONICET-UNLP' THEN 'CONICET'
                                                       WHEN 'Instituto de investigaciones y politicas del ambiente construido (IIPAC CONICET-UNLP) Facultad de Arquitectura y Urbanismo, UNLP' THEN 'CONICET'
                                                       WHEN 'CONICET - UNLP' THEN 'CONICET'
                                                       WHEN 'Centro de Investigaciones Geológicas' THEN 'CONICET'
                                                       WHEN 'Instituto LEICI' THEN 'CONICET'
                                                       WHEN 'Universidad Nacional de La Plata - Instituto de Física de La Plata' THEN 'CONICET'
                                                       WHEN 'Instituto de Fisiología Vegetal' THEN 'CONICET'
                                                       WHEN 'CONICET-UNLP-CEPAVE' THEN 'CONICET'
                                                       WHEN 'Universidad Nacional de La Plata (Beca CONICET)' THEN 'CONICET'
                                                       WHEN 'INSTITUTO DE GENETICA VET ING FERNANDO NOEL DULOUT  (IGEVET)' THEN 'CONICET'
                                                       WHEN 'Instituto de Investigaciones en Humanidades y Ciencias Sociales (IdIHCS)' THEN 'CONICET'
                                                       WHEN 'CONICET, UNLP' THEN 'CONICET'

                                                       WHEN 'CCT La Plata - CONICET' THEN 'CONICET'
                                                       WHEN 'CONICET-UNLP' THEN 'CONICET'
                                                       WHEN 'INSTITUTO DE ASTROFISICA LA PLATA (IALP)' THEN 'CONICET'
                                                       WHEN 'CEQUINOR (UNLP-CONICET)' THEN 'CONICET'
                                                       WHEN 'CONICET - Consejo Nacional de Investigaciones Científicas y Técnicas' THEN 'CONICET'
                                                       WHEN 'CENTRO CIENTIFICO TECNOLOGICO CONICET - LA PLATA (CCT-CONICET - LA PLATA) ; CONSEJO NACIONAL DE INVESTIGACIONES CIENTIFICAS Y TECNICAS' THEN 'CONICET'
                                                       WHEN 'ANPCYT-UNLP' THEN 'ANPCyT'
                                                       WHEN 'SEC. CIENCIA Y TÉCNICA (UNLP)' THEN 'UNLP'
                                                       WHEN 'UNIV.NAC.DE LA PLATA / FAC.DE PERIODISMO Y COMUNICACION SOCIAL' THEN 'UNLP'
                                                       WHEN 'FACULTAD DE HUMANIDADES Y CIENCIAS DE LA EDUCACIÓN' THEN 'UNLP'
                                                       WHEN 'AGENCIA' THEN 'ANPCyT'
                                                       WHEN 'CONVENIO COMISIÓN NACIONAL DE ACTIVIDADES ESPACIALES' THEN 'OTRA'
                                                       WHEN 'Fundación Bunge y Born Argentina' THEN 'OTRA'
                                                       WHEN 'MINCYT PICT 2013- 03175' THEN 'OTRA'
                                                       WHEN 'Bunge y Born' THEN 'OTRA'
                                                       WHEN 'SENESCYT-Ecuador' THEN 'OTRA'
                                                       WHEN 'INIFTA' THEN 'OTRA'

                                                       WHEN 'Consejo Nac. Invest. Cient' THEN 'CONICET'
                                                       WHEN 'CONSEJO NACIONAL DE INVESTIGACIONES CIENTÍFICAS Y TÉCNICAS (CONICET)' THEN 'CONICET'
                                                       WHEN 'CONSEJO DE INVESTIGACIÓN CIENTÍFICAS DE LA PROVINCIA DE BUENOS AIRES' THEN 'CIC'
                                                       WHEN 'UNIVERSIDAD NACIONAL DE LA PLATA -FAHCE' THEN 'UNLP'
                                                       WHEN 'UNLP FBA' THEN 'UNLP'
                                                       WHEN 'COMISION DE INVESTIGACIONES CIENTIFICAS BS AS' THEN 'CIC'
                                                       WHEN 'FACULTAD DE INGENIERIA' THEN 'UNLP'
                                                       WHEN 'CONCEJO DE INVEST. CIENTÍFICAS DE LA PROVINCIA DE BUENOS AIRES' THEN 'CIC'
                                                       WHEN 'FACULTAD DE CIENCIAS VETERINARIAS UNLP' THEN 'UNLP'
                                                       WHEN 'UNAM, MÉXICO' THEN 'OTRA'
                                                       WHEN 'AGENCIA NACIONAL DE PROMOCIÓN CIENTÍFICA Y TECNOLÓGICA' THEN 'ANPCyT'
                                                       WHEN 'ANPYCT – PICT.' THEN 'ANPCyT'
                                                       WHEN 'Agencia Nacional de Promoción de la Investigación, el Desarrollo Tecnológico y la Innovación' THEN 'ANPCyT'
                                                       WHEN 'Agencia Nacional de Promoción de Ciencia y Técnica' THEN 'ANPCyT'
                                                       WHEN 'Agencia Nacional de Promoción de la Investigación, el Desarrollo Tecnológico y la Innovación' THEN 'ANPCyT'
                                                       WHEN 'Agencia de Promoción Científica y tecnologica' THEN 'ANPCyT'

                                                       WHEN 'Agencia i+d+i' THEN 'ANPCyT'
                                                       WHEN 'AGENCIA DE PROMOCION CIENTIFICA' THEN 'ANPCyT'
                                                       WHEN 'ANPCYT - UNLP' THEN 'ANPCyT'
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
                                                       WHEN 'ANCYPT' THEN 'ANPCyT'
                                                       WHEN 'FAU-UNLP' THEN 'UNLP'
                                                       WHEN 'CENTRO DE INVESTIGACIONES URBANAS Y TERRITORIALES (CIUT)' THEN 'OTRA'
                                                       WHEN 'FACULTAD DE ARQUITECTURA Y URBANISMO-UNLP' THEN 'UNLP'
                                                       WHEN 'FACULTAD DE CIENCIAS EXACTAS. UNLP' THEN 'UNLP'
                                                       WHEN 'FACULTAD DE CIENCIAS NATURALES Y MUSEO' THEN 'UNLP'
                                                       WHEN 'FACULTAD DE CIENCIAS EXACTAS, UNIVERSIDAD NACIONAL DE LA PLATA' THEN 'UNLP'
                                                       WHEN 'AGENCIA - UNLP (BECA CONJUNTA)' THEN 'ANPCyT'
                                                       WHEN 'UNLP- PROGRAMA DE RETENCION DE RECURSOS HUMANOS' THEN 'UNLP'
                                                       WHEN 'FACULTAD DE BELLAS ARTES' THEN 'UNLP'
                                                       WHEN 'Facultad de Ciencias Exactas' THEN 'UNLP'
                                                       WHEN 'Facultad de Ciencias Veterinarias, Universidad Nacional de La Plata' THEN 'UNLP'
                                                       WHEN 'Facultad de Ciencias Médicas' THEN 'UNLP'
                                                       WHEN 'Facultad de Ciencias Naturales y Museo - Universidad Nacional de La Plata' THEN 'UNLP'
                                                       WHEN 'Facultad de Ciencias Exactas- UNLP' THEN 'UNLP'
                                                       WHEN 'Facultad de Ciencias Veterinarias' THEN 'UNLP'
                                                       WHEN 'Facultad de Periodismo y Comunicación Social' THEN 'UNLP'
                                                       WHEN 'IBBM (UNLP)' THEN 'UNLP'
                                                       WHEN 'IITREE-LAT' THEN 'UNLP'
                                                       WHEN 'IFLP/UNLP' THEN 'UNLP'
                                                       WHEN 'Facultad de Ingeniería - UNLP' THEN 'UNLP'
                                                       WHEN 'UNLP-CIN' THEN 'CIN'
                                                       WHEN 'CONSEJO INTERUNIVERSITARIO NACIONAL' THEN 'CIN'
                                                       WHEN 'COPNICET' THEN 'CONICET'
                                                       WHEN 'CIC PBA' THEN 'CIC'
                                                       WHEN 'CIC. PBA' THEN 'CIC'
                                                       WHEN 'DAAD' THEN 'OTRA'
                                                       WHEN 'COMISIÓN DE INVESTIGACIONES CIENTÍFICAS (CIC)' THEN 'CIC'
                                                       WHEN 'CONSEJO NAC.DE INVEST.CIENTIF.Y TECNICAS' THEN 'CONICET'
                                                       WHEN 'CIDEPINT' THEN 'CONICET'
                                                       WHEN 'CIDCA' THEN 'CONICET'
                                                       WHEN 'CEPAVE' THEN 'CONICET'
                                                       WHEN 'IdIHCS' THEN 'CONICET'
                                                       WHEN 'CONCIET' THEN 'CONICET'
                                                       WHEN 'CONCET' THEN 'CONICET'
                                                       WHEN 'CONICET-INTA' THEN 'CONICET'
                                                       WHEN 'CICBA' THEN 'CIC'
                                                       WHEN 'CEQUINOR' THEN 'CONICET'
                                                       WHEN 'Telefónica' THEN 'OTRA'
                                                       WHEN 'Academia Nacional de Ciencias Exactas, Físicas y Naturales' THEN 'OTRA'
                                                       WHEN 'FONCIT' THEN 'OTRA'
                                                       ELSE solicitudjovenes.ds_orgbeca

    END AS institucion,
       CASE solicitudjovenes.ds_tipobeca
           WHEN 'POSTGRADO/DOCTORADO' THEN 'Beca doctoral'
           WHEN 'BECA INTERNA DOCTORAL TEMAS ESTRATÉGICOS' THEN 'Beca doctoral'
           WHEN 'POSGRADO/DOCTORAL' THEN 'Beca doctoral'
           WHEN 'Becas Internas Doctorales para Temas Estratégicos 2021.' THEN 'Beca doctoral'
           WHEN 'Becario Doctoral' THEN 'Beca doctoral'
           WHEN '10%' THEN 'Beca doctoral'
           WHEN '4° año Beca Internal Doctoral' THEN 'Beca doctoral'
           WHEN '50%' THEN 'Beca doctoral'
           WHEN '70%' THEN 'Beca doctoral'
           WHEN '80' THEN 'Beca doctoral'
           WHEN 'Avanzado' THEN 'Beca doctoral'
           WHEN 'AVANZADO (cuarto año)' THEN 'Beca doctoral'
           WHEN 'BECA' THEN 'Beca doctoral'
           WHEN 'Beca de Estudio - Doctoral' THEN 'Beca doctoral'
           WHEN 'Beca de Postgrado' THEN 'Beca doctoral'
           WHEN 'BECA DOCTORADO' THEN 'Beca doctoral'
           WHEN 'BECA DOCTORAL' THEN 'Beca doctoral'

           WHEN 'Beca de apoyo a la investigación de graduados' THEN 'Beca doctoral'
           WHEN 'Beca de doctorado' THEN 'Beca doctoral'
           WHEN 'Beca de Doctorado' THEN 'Beca doctoral'
           WHEN 'POSTGRADO/DOCTORADO' THEN 'Beca posdoctoral'
           WHEN '2 año Pos Doctorado' THEN 'Beca posdoctoral'
           WHEN 'Beca postdoctoral cofinanciada' THEN 'Beca posdoctoral'
           WHEN 'Postdoctorado' THEN 'Beca posdoctoral'
           WHEN 'BECA INTERNA DE POSTGRADO TIPO II' THEN 'TIPO II'
           WHEN 'POSGRADO TIPO I +POSGRADO TIPO II' THEN 'TIPO II'
           WHEN 'BECA INTERNA POSTGRADO TIPO II' THEN 'TIPO II'
           WHEN 'BECA DE POSGRADO INTERNA TIPO II' THEN 'TIPO II'
           WHEN 'BECA DE DOCTORADO TIPO II' THEN 'TIPO II'
           WHEN 'BECA DE RETENCIÓN DE POSGRADUADOS' THEN 'RETENCION DE POSTGRADUADO'
           WHEN 'RETENCIÓN DE POSTGRADUADOS FORMADOS POR LA UNLP' THEN 'RETENCION DE POSTGRADUADO'
           WHEN 'Programa de Retención de Doctores de la UNLP' THEN 'RETENCION DE POSTGRADUADO'
           WHEN 'Retención de doctores' THEN 'RETENCION DE POSTGRADUADO'
           WHEN 'Agencia 2' THEN 'Beca superior'
           WHEN 'Beca de Estudio (posgrado)' THEN 'Beca superior'
           WHEN 'POSDOCTORAL' THEN 'Beca posdoctoral'
           WHEN 'B' THEN 'TIPO B'
           WHEN 'B DE UNLP' THEN 'TIPO B'
           WHEN 'POST-DOCTORADO' THEN 'Beca posdoctoral'
           WHEN 'II' THEN 'TIPO II'
           WHEN 'I' THEN 'TIPO I'
           WHEN 'POSTDOCTORAL' THEN 'Beca posdoctoral'
           WHEN 'BECA DOCTORAL TIPO II' THEN 'TIPO II'
           WHEN 'PERFECCIONAMIENTO' THEN 'BECA DE PERFECCIONAMIENTO'
           WHEN 'BECA DE PERFECCIONAMIENTO (PRÓRROGA ESPECIAL)' THEN 'BECA DE PERFECCIONAMIENTO'
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
           WHEN 'BECARIO TIPO 1' THEN 'TIPO I'
           WHEN 'POSTGRADO/ESPECIALIZACION TIPO II' THEN 'TIPO II'
           WHEN 'FORMACIÓN SUPERIOR EN LA INVESTIGACIÓN Y DESARROLLO ARTÍSTICO, CIENTÍFICO Y TECNOLÓGICO' THEN 'Formación Superior'
           WHEN 'FORMACIóN SUPERIOR' THEN 'Formación Superior'
           WHEN 'BECA DE RETENCIÓN DE POSGRADUADO' THEN 'RETENCION DE POSTGRADUADO'
           WHEN 'RETENCIÓN DE POSGRADUADOS' THEN 'RETENCION DE POSTGRADUADO'
           WHEN 'BECA DE POSTGRADUADO' THEN 'RETENCION DE POSTGRADUADO'
           WHEN 'Postgraduados' THEN 'RETENCION DE POSTGRADUADO'
           WHEN 'BECA INTERNA DE POSGRADO' THEN 'Beca doctoral'
           WHEN 'TEMAS ESTRATÉGICOS' THEN 'Beca doctoral'
           WHEN 'Tercer Año' THEN 'Beca doctoral'
           WHEN 'Tipo 2 de CONICET (Avance = 80%)' THEN 'TIPO II'
           WHEN 'Segundo año' THEN 'Beca doctoral'
           WHEN 'TIPO B-DOCTORADO' THEN 'TIPO B (DOCTORADO)'
           WHEN 'TIPO B- DOCTORADO' THEN 'TIPO B (DOCTORADO)'
           WHEN 'Tipo B- Doctorado' THEN 'TIPO B (DOCTORADO)'
           WHEN 'TIPO B (PRORROGA)' THEN 'TIPO B'
           WHEN 'BECARIO TIPO I' THEN 'TIPO I'
           WHEN 'BECA INTERNA TIPO II' THEN 'TIPO II'
           WHEN 'BECA POSDOCOTRAL' THEN 'Beca posdoctoral'
           WHEN 'Superior' THEN 'Beca superior'
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
           WHEN 'CATEGORIA LL' THEN 'RETENCION DE POSTGRADUADO'
           WHEN 'II, DOCTORADO' THEN 'TIPO II'
           WHEN 'BECA DE SCYT TIPO B' THEN 'TIPO B'
           WHEN 'CONICET 1' THEN 'TIPO I'
           WHEN 'II DOCTORAL' THEN 'TIPO II'
           WHEN 'ENTRENAMIENTO' THEN 'Beca de entrenamiento'
           WHEN 'BECA DOCTORAL PG T II 11' THEN 'TIPO II'
           WHEN 'UNLP 1' THEN 'TIPO A'
           WHEN 'Agencia 1' THEN 'Beca inicial'
           WHEN 'BECA DE ESTUDIO' THEN 'Beca inicial'
           WHEN 'Inicial de doctorado' THEN 'Beca inicial'
           WHEN 'Doctoral Inicial' THEN 'Beca inicial'
           WHEN 'Beca de estudio doctoral CIC' THEN 'Beca doctoral'
           WHEN 'INICIO' THEN 'Beca doctoral'
           WHEN 'Interna  Doctoral' THEN 'Beca doctoral'
           WHEN 'ESTUDIO' THEN 'Beca doctoral'
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
           WHEN 'TIPO B- MAESTRíA' THEN 'TIPO B (MAESTRÍA)'
           WHEN 'BECA POS DOC' THEN 'Beca posdoctoral'
           WHEN 'BECA POSTDOCOTORAL' THEN 'Beca posdoctoral'
           WHEN 'BECA POSTDOCTORAL' THEN 'Beca posdoctoral'
           WHEN 'BECA POSGRADO TIPO B' THEN 'TIPO B'
           WHEN 'BECA INTERNA POSGRADOTIPO II' THEN 'TIPO II'
           WHEN 'BECA DE POSGRADO TIPO A' THEN 'TIPO A'
           WHEN 'POSGRADO TIPO I' THEN 'TIPO I'
           WHEN 'INCIACIÓN - A ' THEN 'TIPO A'
           WHEN 'DOCTORADO' THEN 'Beca doctoral'
           WHEN 'Beca interna' THEN 'Beca doctoral'
           WHEN 'Beca Interna de Doctorado' THEN 'Beca doctoral'
           WHEN 'DE PERFECCIONAMIENTO' THEN 'BECA DE PERFECCIONAMIENTO'
           WHEN 'Beca de Perfeccionamiento' THEN 'BECA DE PERFECCIONAMIENTO'
           WHEN 'BECA DOCTORAL TIPO 1' THEN 'TIPO I'
           WHEN 'POSGRADO TIPO 2' THEN 'TIPO II'
           WHEN 'Tipo II' THEN 'TIPO II'
           WHEN 'TIPO II (2 AÑOS)' THEN 'TIPO II'
           WHEN 'BECA INTERNA DE POSGRADO TIPO I' THEN 'TIPO I'
           WHEN 'TIPO I (INICIACIÓN)' THEN 'TIPO I'
           WHEN 'INTERNA DE POSGRADO TIPO I' THEN 'TIPO I'
           WHEN 'BECA ESTÍMULO A LAS VOCACIONES CIENTÍFICAS' THEN 'EVC'
           WHEN '&#61607;	BECA DE ENTRENAMIENTO PARA ALUMNOS UNIVERSITARIOS' THEN 'Beca de entrenamiento'
           WHEN 'POSTGRADO TIPO A' THEN 'TIPO A'
           WHEN 'DOCTORAL DE FINALIZACIÓN' THEN 'Beca finalización del doctorado'
           WHEN 'Tipo II (Finalización de Doctorado)' THEN 'Beca finalización del doctorado'
           WHEN 'Beca de Finalización de Doctorado' THEN 'Beca finalización del doctorado'
           WHEN 'beca de finalización de doctorado' THEN 'Beca finalización del doctorado'
           WHEN 'INTERNA DE FIN DE DOCTORADO' THEN 'Beca finalización del doctorado'
           WHEN 'Beca finalizacion doctorado' THEN 'Beca finalización del doctorado'
           WHEN 'Interna de finalización de Doctorado' THEN 'Beca finalización del doctorado'
           WHEN 'De finalización de Doctorado' THEN 'Beca finalización del doctorado'
           WHEN 'Beca de Investigación: Programa de Retención de Doctores' THEN 'RETENCION DE POSTGRADUADO'
           WHEN 'Beca de Investigación: Retención de Doctores UNLP' THEN 'RETENCION DE POSTGRADUADO'
           WHEN 'POSGRADO-DOCTORAL' THEN 'Beca doctoral'
           WHEN 'Beca interna doctoral CONICET' THEN 'Beca doctoral'
           WHEN 'Beca Interna Doctoral de Conicet' THEN 'Beca doctoral'
           WHEN 'Beca Interna de Postgrado' THEN 'Beca doctoral'
           WHEN 'POSGRADO TIPO 1' THEN 'TIPO I'
           WHEN 'UNLP 2' THEN 'TIPO B'
           WHEN 'BECA TIPO A' THEN 'TIPO A'
           WHEN 'BECA POSTGRADO TIPO I' THEN 'TIPO I'
           WHEN 'INICIAL' THEN 'Beca inicial'
           WHEN 'BECA DE POSDOCTORAL' THEN 'Beca posdoctoral'
           WHEN 'Beca Interna de Posdoctorado' THEN 'Beca posdoctoral'
           WHEN 'BECARIO POSTGRADO TIPO I' THEN 'TIPO I'
           WHEN 'BECA PERFECCIONAMIENTO' THEN 'BECA DE PERFECCIONAMIENTO'
           WHEN 'INTERNA  TIPO A' THEN 'TIPO A'
           WHEN 'Finalización de Doctorado' THEN 'Beca finalización del doctorado'
           WHEN 'Beca Interna de Finalización de Doctorado' THEN 'Beca finalización del doctorado'
           WHEN 'Beca Interna de Finalización del Doctorado' THEN 'Beca finalización del doctorado'
           WHEN 'BECA INTERNA DE FINALIZACION DE DOCTORADO' THEN 'Beca finalización del doctorado'
           WHEN 'Tipo II - Finalización de doctorado' THEN 'Beca finalización del doctorado'
           WHEN 'Finalización' THEN 'Beca finalización del doctorado'
           WHEN 'Finalizacion de doctorado (tipo II)' THEN 'Beca finalización del doctorado'
           WHEN 'finalización del doctorado' THEN 'Beca finalización del doctorado'
           WHEN 'Finalización del Doctorado' THEN 'Beca finalización del doctorado'
           WHEN 'FINALIZACION DEL DOCTORADO' THEN 'Beca finalización del doctorado'
           WHEN 'finalización doctorado' THEN 'Beca finalización del doctorado'
           WHEN 'FINALIZACIÓN DOCTORADO' THEN 'Beca finalización del doctorado'
           WHEN 'Finalización doctorado' THEN 'Beca finalización del doctorado'
           WHEN 'Finalización doctorado' THEN 'Beca finalización del doctorado'
           WHEN 'BECA DE ENTRENAMIENTO' THEN 'Beca de entrenamiento'
           WHEN 'BECA POSTGRADO TIPO I (DOCTORAL)' THEN 'TIPO I'
           WHEN 'INTERNA DE POSTGRADO TIPO I' THEN 'TIPO I'
           WHEN 'BECA INTERNA DE POSTGRADO TIPO I (ASPIRANTE)' THEN 'TIPO I'
           WHEN 'INICIACIÓN TIPO I' THEN 'TIPO I'
           WHEN 'INTERNA DE POSTGRADO TIPO II' THEN 'TIPO II'
           WHEN 'BECA INTERNA DE POSGRADO TIPO 2' THEN 'TIPO II'
           WHEN 'BECA INTERNA DE POSGRADO TIPO II.' THEN 'TIPO II'
           WHEN 'TIPO II - CONICET' THEN 'TIPO II'
           WHEN 'TIPO2' THEN 'TIPO II'
           WHEN 'TIPOII' THEN 'TIPO II'
           WHEN 'BECA DE POSTGRADO TIPO I' THEN 'TIPO I'
           WHEN 'BECA SUPERIOR  AGENCIA NACIONAL DE PROMOCIÓN CIENTÍFICA Y TECNOLÓGICA' THEN 'Beca superior'
           WHEN 'Posgrado Nivel Superior (Posdoctoral)' THEN 'Beca superior'
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
           WHEN 'Beca Postgrado Interna Tipo I' THEN 'TIPO I'
           WHEN 'POSTDOCTORAL LATINOAMERICANO' THEN 'TIPO II'
           WHEN 'BECA POSTGRADO TIPO II' THEN 'TIPO II'
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
           WHEN 'POS DOCTORAL' THEN 'Beca posdoctoral'
           WHEN 'BECAS CIN' THEN 'EVC'
           WHEN 'Beca Interna Doctoral' THEN 'Beca doctoral'
           WHEN 'BECA DE PERFECCIONAMIENTO (UNNE)' THEN 'BECA DE PERFECCIONAMIENTO'
           WHEN 'BECTA INTERNA TIPO I' THEN 'TIPO I'
           WHEN 'TIPO B-MAESTRIA' THEN 'TIPO B (MAESTRÍA)'
           WHEN 'Tipo A- Maestría' THEN 'Tipo A - Maestría'
           WHEN 'Tipo A- Doctorado' THEN 'Tipo A - Doctorado'
           WHEN 'TIPO A (Doctorado)' THEN 'Tipo A - Doctorado'
           WHEN 'Tipo A - Doctorado' THEN 'Tipo A - Doctorado'
           WHEN 'TIPO A DOCTORAL' THEN 'Tipo A - Doctorado'
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
           WHEN 'Beca de nivel inicial' THEN 'Beca inicial'
           WHEN 'POSTGRADO/ESPECIALIZACÓN TIPO I' THEN 'TIPO I'
           WHEN 'POSGRADO II' THEN 'TIPO II'
           WHEN 'POSGRADO INICIAL' THEN 'Beca inicial'
           WHEN 'BECA POSGRADO TIPO I' THEN 'TIPO I'
           WHEN 'POSGRADO-DOCTORADO' THEN 'Beca doctoral'
           WHEN 'Beca Boctoral' THEN 'Beca doctoral'
           WHEN 'TIPO I INTERNA' THEN 'TIPO I'
           WHEN 'Beca incial' THEN 'Beca inicial'
           WHEN 'Becas internas postdoctorales' THEN 'Beca posdoctoral'
           WHEN 'Beca Interna Postdoctoral Temas Estratégicos' THEN 'Beca posdoctoral'
           WHEN 'Beca Inicial FONCyTL' THEN 'Beca inicial'
           WHEN 'BECA TIPO 1 DOCTORADO' THEN 'TIPO I'
           WHEN 'Concursos de becas internas doctorales y destinadas a postulantes provenientes de paises latinoamericanos' THEN 'TIPO I'
           WHEN 'BECA INTERNA DE POSTGRADO TIPO I (3 AÑOS)' THEN 'TIPO I'
           WHEN 'Beca Interna Doctoral (área ?KE1 TIERRA?)' THEN 'Beca doctoral'
           WHEN 'POS-DOCTORAL (INTERNA)' THEN 'Beca posdoctoral'
           WHEN 'BECA POSDOCTORAL' THEN 'Beca posdoctoral'
           WHEN 'Beca Posdoctoral' THEN 'Beca posdoctoral'
           WHEN 'Beca Posdoctoral' THEN 'Beca posdoctoral'
           WHEN 'BECA POSDOCTORAL (CONICET)' THEN 'Beca posdoctoral'
           WHEN 'Beca posdoctoral CONICET' THEN 'Beca posdoctoral'
           WHEN 'Beca Posdoctoral Interna' THEN 'Beca posdoctoral'
           WHEN 'BECA POSDOCTORAL INTERNA CONICET' THEN 'Beca posdoctoral'
           WHEN 'Beca postdoctoral de reinsercion' THEN 'Beca posdoctoral'
           WHEN 'BECA POSTDOCTORAL INTERNA' THEN 'Beca posdoctoral'
           WHEN 'BECARIO POSDOCTORAL' THEN 'Beca posdoctoral'
           WHEN 'Becario Post-doctoral' THEN 'Beca posdoctoral'
           WHEN 'Podoctoral' THEN 'Beca posdoctoral'
           WHEN 'Pos-Doctorado' THEN 'Beca posdoctoral'
           WHEN 'POS-DOCTORAL' THEN 'Beca posdoctoral'
           WHEN 'POSDOC' THEN 'Beca posdoctoral'
           WHEN 'posdoc' THEN 'Beca posdoctoral'
           WHEN 'Posdocotral' THEN 'Beca posdoctoral'
           WHEN 'Posdoctoral (extensión)' THEN 'Beca posdoctoral'
           WHEN 'Posdoctoral Extraordinaria hasta Alta CIC' THEN 'Beca posdoctoral'
           WHEN 'Posdoctoral interna' THEN 'Beca posdoctoral'
           WHEN 'POSDOCTORAL INTERNA' THEN 'Beca posdoctoral'
           WHEN 'POSDOCTORAL TEMAS ESTRATÉGICOS' THEN 'Beca posdoctoral'
           WHEN 'POSTODC' THEN 'Beca posdoctoral'
           WHEN 'Beca Interna Dcotoral' THEN 'Beca doctoral'
           WHEN 'Último año' THEN 'Beca doctoral'
           WHEN 'Única' THEN 'Beca doctoral'
           WHEN 'Primer año' THEN 'Beca doctoral'
           WHEN 'posgrado/doctorado' THEN 'Beca doctoral'
           WHEN 'Post Doctorado' THEN 'Beca posdoctoral'
           WHEN 'Post Grado' THEN 'Beca doctoral'
           WHEN 'Becario' THEN 'Beca doctoral'
           WHEN 'BECARIO DOCTORAL' THEN 'Beca doctoral'
           WHEN 'Becario Doctoral' THEN 'Beca doctoral'
           WHEN 'Doctorado en Ingenieria' THEN 'Becario Doctoral'
           WHEN 'caurto año' THEN 'Beca doctoral'
           WHEN 'Cuarto año' THEN 'Beca doctoral'
           WHEN 'Docotoral' THEN 'Beca doctoral'
           WHEN 'Becario Doctoral, quinto año' THEN 'Beca doctoral'
           WHEN 'Beca doctoral unidad ejecutora' THEN 'Beca doctoral'
           WHEN 'Beca Doctoral de Temas Estragicos' THEN 'Beca doctoral'
           WHEN 'Beca Doctoral Temas Estratégicos' THEN 'Beca doctoral'
           WHEN 'Beca Doctoral- TEMAS EST.' THEN 'Beca doctoral'
           WHEN 'Beca Interna doctoral: cuarto año' THEN 'Beca doctoral'
           WHEN 'Beca Interna Doctoral para Temas Estratégicos' THEN 'Beca doctoral'
           WHEN 'Planes estrátegicos' THEN 'Beca doctoral'
           WHEN 'DOCTORAL - 3° AÑO' THEN 'Beca doctoral'
           WHEN 'Doctoral - 4° año' THEN 'Beca doctoral'
           WHEN 'Doctoral - CONICET (Doctorado ya finalizado)' THEN 'Beca doctoral'
           WHEN 'Doctoral CONICET' THEN 'Beca doctoral'
           WHEN 'Doctoral interna' THEN 'Beca doctoral'
           WHEN 'Doctoral Temas Estrategicos' THEN 'Beca doctoral'
           WHEN 'Interna Doctoral' THEN 'Beca doctoral'
           WHEN 'INTERNA DOCTORAL' THEN 'Beca doctoral'
           WHEN 'INTERNA DOCTORAL TEMAS ESTRATÉGICOS' THEN 'Beca doctoral'
           WHEN 'Interna doctoral. Tema estratégico' THEN 'Beca doctoral'
           WHEN 'Interna Doctorales' THEN 'Beca doctoral'
           WHEN 'Interna para Temas Estratégicos' THEN 'Beca doctoral'
           WHEN 'Doctoral CONICET/UNLP' THEN 'Beca doctoral'
           WHEN 'Incompleto' THEN 'Beca doctoral'
           WHEN 'Doctotral' THEN 'Beca doctoral'
           WHEN 'DOTORAL' THEN 'Beca doctoral'
           WHEN 'En curso' THEN 'Beca doctoral'
           WHEN 'en proceso' THEN 'Beca doctoral'
           WHEN 'POSTGRADO TIPO II (2 AÑOS)' THEN 'TIPO II'
           WHEN 'Postgrado Tipo II Conicet' THEN 'TIPO II'
           WHEN 'POSTGRADO DOCTORAL (TIPO II)' THEN 'TIPO II'
           WHEN 'POSGRADO TIPO II' THEN 'TIPO II'
           WHEN 'BECARIO POSTDOCTORAL' THEN 'Beca posdoctoral'
           WHEN 'Interna Postdoctoral' THEN 'Beca posdoctoral'
           WHEN 'Postdoc' THEN 'Beca posdoctoral'
           WHEN 'POSTDOC' THEN 'Beca posdoctoral'
           WHEN 'Postdoctoral (extensión por ingreso a CIC)' THEN 'Beca posdoctoral'
           WHEN 'Postdoctoral CONICET' THEN 'Beca posdoctoral'
           WHEN 'Postdoctoral extraordinaria' THEN 'Beca posdoctoral'
           WHEN 'Postdoctoral Interna' THEN 'Beca posdoctoral'
           WHEN 'POSTDOCTORAL INTERNA (2 AÑOS)' THEN 'Beca posdoctoral'
           WHEN 'Postdoctoral UE' THEN 'Beca posdoctoral'
           WHEN 'BECA BIANUAL.ESTUDIOS DE MAESTRIA.' THEN 'Beca maestría'
           WHEN 'Postgrado/Maestría' THEN 'Beca maestría'
           WHEN 'Maestría' THEN 'Beca maestría'
           WHEN 'BECA INTERNA POSDOCTORAL' THEN 'Beca posdoctoral'
           WHEN 'Interna Posdoctoral' THEN 'Beca posdoctoral'
           WHEN 'POSTGRADO/DOCTORADO BECA TIPO B' THEN 'TIPO B'
           WHEN 'Beca Tipo B' THEN 'TIPO B'
           WHEN 'Beca tipo B para doctorado' THEN 'TIPO B'
           WHEN 'INICICIACIÓN' THEN 'Iniciación'
           WHEN 'INICIACIóN' THEN 'Iniciación'
           WHEN 'BECA POSTDOCTORAL OTORGADA POR ANPYCT – PICT.' THEN 'Beca superior'
           WHEN 'Doctorado - Inicial' THEN 'Beca inicial'
           WHEN 'POSTGRADO TIPO I AVG' THEN 'TIPO I'
           WHEN 'BECA DOCTORAL INTERNA TIPO I' THEN 'TIPO I'
           WHEN 'Beca Interna Doctoral Tipo 1' THEN 'TIPO I'
           WHEN 'Beca interna doctoral tipo I' THEN 'TIPO I'
           WHEN 'Becas internas de postgrado Tipo I' THEN 'TIPO I'
           WHEN 'Beca Doctoral interna TIPO II' THEN 'TIPO II'
           WHEN 'BECA DOCTORAL TIPO II. CONICET' THEN 'TIPO II'
           WHEN 'BECA INTERNA TIPO II CONICET' THEN 'TIPO II'
           WHEN 'Beca Interna Doctoral Tipo II' THEN 'TIPO II'
           WHEN 'Iniciación-Finalización' THEN 'TIPO II'
           WHEN 'BECA POSGRADO TIPO II' THEN 'TIPO II'
           WHEN 'BECA TIPO II CONICET' THEN 'TIPO II'
           WHEN 'CONICET TIPO II' THEN 'TIPO II'
           WHEN 'DE POSTGRADO TIPO II' THEN 'TIPO II'
           WHEN 'DOCTORADO TIPO 2' THEN 'TIPO II'
           WHEN 'Doctorado Tipo II' THEN 'TIPO II'
           WHEN 'DOCTORADO TIPO II' THEN 'TIPO II'
           WHEN 'DOCTORAL (POSGRADO TIPO II)' THEN 'TIPO II'
           WHEN 'Doctoral tipo 2' THEN 'TIPO II'
           WHEN 'BECA DOCTORAL TIPO 2. CONICET' THEN 'TIPO II'
           WHEN 'DOCTORAL TIPO II (PERFECCIONAMIENTO)' THEN 'TIPO II'
           WHEN 'INTERNA DE POSGRADO TIPO II' THEN 'TIPO II'
           WHEN 'Interna posgrado TIPÖ II' THEN 'TIPO II'
           WHEN 'INTERNA TIPO II' THEN 'TIPO II'
           WHEN 'PG TII' THEN 'TIPO II'
           WHEN 'PG TIPO II' THEN 'TIPO II'
           WHEN 'PGTII' THEN 'TIPO II'
           WHEN 'Posgrado - Tipo II' THEN 'TIPO II'
           WHEN 'POSTGRADO DOCTORAL (TIPO II)' THEN 'TIPO II'
           WHEN 'POSGRADO DE TIPO II' THEN 'TIPO II'
           WHEN 'A' THEN 'TIPO A'
           WHEN 'Cofinanciadas CIC-UNLP' THEN 'Beca Cofinanciada (UNLP-CIC)'
           WHEN 'Beca Doctoral Cofinanciada UNLP-CIC' THEN 'Beca Cofinanciada (UNLP-CIC)'
           ELSE solicitudjovenes.ds_tipobeca
           END AS beca, dt_becadesde as desde, dt_becahasta as hasta, bl_unlp as unlp, solicitudjovenes.cd_unidadbeca as unidad, CONCAT(solicitudjovenes.ds_orgbeca,' - ',solicitudjovenes.ds_tipobeca) as original

            ")
            ->where('solicitudjovenes.bl_becario', 1)
            ->where('solicitudjovenes.ds_tipobeca', '!=', 'No declarado')
            ->where('solicitudjovenes.ds_tipobeca', '!=', '')
            ->whereNotNull('solicitudjovenes.ds_tipobeca')
            ->whereNotNull('solicitudjovenes.ds_orgbeca')
            ->orderBy('solicitudjovenes.cd_solicitud')

            ->chunk(1000, function ($rows) use (&$totalFilas, &$totalInsertadas, &$totalOmitidas, &$skippedRows) {

                $totalFilas += count($rows);

                $data = collect($rows)->map(function ($row) use (&$skippedRows, &$totalOmitidas) {

                    // Validaciones básicas: persona_id o ident vacíos


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

                            'joven_id' => $row->joven_id,

                            'motivo' => 'Beca inválida',
                            'institucion' => $row->institucion,
                            'beca' => $row->beca,
                        ];
                        $totalOmitidas++;
                        return null;
                    }

                    if (is_null($institucionFinal)) {
                        $skippedRows[] = [
                            'joven_id' => $row->joven_id,

                            'motivo' => 'Institución inválida',
                            'institucion' => $row->institucion,
                            'beca' => $row->beca,
                        ];
                        $totalOmitidas++;
                        return null;
                    }

                    // 🧹 LIMPIEZA DE FECHA
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
                        'joven_id' => $row->joven_id,
                        'institucion' => $institucionFinal,
                        'beca' => $becaFinal,
                        'unlp' => $row->unlp ?: 0,
                        'desde' => $desde,
                        'hasta' => $hasta,
                        'original' => $row->original ?: null,
                        'agregada' => 0,
                        'actual' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->filter()->toArray();

                if (!empty($data)) {
                    DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=0');
                    DB::connection('mysql')
                        ->table('joven_becas')
                        ->upsert(
                            $data,
                            ['joven_id','institucion','beca','desde','hasta'], // clave única
                            [
                                'unlp','original','agregada','actual','updated_at'
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
                    "joven: {$skip['joven_id']} - Motivo: {$skip['motivo']} - Beca: {$skip['beca']} - Institucion: {$skip['institucion']}"
                );
            }
        }
    }
}
