################################### Migrar DB #################################################
############### Personas
SELECT cd_docente,`ds_nombre`,`ds_apellido`,`nu_documento`,CONCAT(nu_precuil,'-',LPAD(nu_documento,8,'0'),'-',nu_postcuil) AS CUIL,`ds_sexo`,`ds_calle`,`nu_nro`,`nu_piso`,`ds_depto`,`ds_localidad`,`cd_provincia`,`nu_cp`,`nu_telefono`,`ds_mail`,`dt_nacimiento` FROM `docente` WHERE 1

############### Investigadores
SELECT `cd_docente` as id,`nu_ident` as ident,`cd_docente` as persona_id,`cd_categoria` as categoria_id,`cd_categoriasicadi` as sicadi_id,`cd_carrerainv` as carrerainv_id,`cd_organismo` as organismo_id,`cd_facultad` as facultad_id,`cd_cargo` as cargo_id,`ds_deddoc` as deddoc,`cd_universidad` as universidad_id,`cd_titulo` as titulo_id,`cd_titulopost` as titulopost_id,`cd_unidad` as unidad_id,
       CASE `ds_orgbeca`
           WHEN 'U.N.L.P' THEN 'UNLP'
           WHEN 'U.N.L.P.' THEN 'UNLP'
           WHEN 'Otro' THEN 'OTRA'
           WHEN 'Consejo de Invest. Científicas de la Provincia de Bs As' THEN 'CIC'
           WHEN 'Consejo Nac. Invest. Científicas y Técnicas' THEN 'CONICET'
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
           ELSE
               CASE
                   WHEN bl_becaEstimulo = 1 THEN 'CIN'
                   ELSE ds_orgbeca
                   END
           END AS institucion,
       CASE `ds_tipobeca`
           WHEN 'POSTGRADO/DOCTORADO' THEN 'Beca doctoral'
           WHEN 'POSTGRADO/DOCTORADO' THEN 'Beca posdoctoral'
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
           WHEN 'BECARIO TIPO 1' THEN 'TIPO I'
           WHEN 'POSTGRADO/ESPECIALIZACION TIPO II' THEN 'TIPO II'
           WHEN 'FORMACIÓN SUPERIOR EN LA INVESTIGACIÓN Y DESARROLLO ARTÍSTICO, CIENTÍFICO Y TECNOLÓGICO' THEN 'Formación Superior'
           WHEN 'BECA DE RETENCIÓN DE POSGRADUADO' THEN 'RETENCION DE POSTGRADUADO'
           WHEN 'BECA INTERNA DE POSGRADO' THEN 'Beca doctoral'
           WHEN 'TIPO B-DOCTORADO' THEN 'TIPO B'
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
           WHEN 'CIC 1' THEN 'Beca inicial'
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
           WHEN 'BECA DE ESTUDIO PARA GRADUADOS UNIVERSITARIOS (CIC)' THEN 'Beca inicial'
           WHEN 'POS DOCTORAL' THEN 'Beca posdoctoral'
           WHEN 'BECAS CIN' THEN 'EVC'
           WHEN 'Beca Interna Doctoral' THEN 'Beca doctoral'
           WHEN 'BECA DE PERFECCIONAMIENTO (UNNE)' THEN 'BECA DE PERFECCIONAMIENTO'
           WHEN 'BECTA INTERNA TIPO I' THEN 'TIPO I'
           WHEN 'TIPO B-MAESTRIA' THEN 'TIPO B (MAESTRÍA)'
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
           WHEN 'TIPO I INTERNA' THEN 'TIPO I'
           WHEN 'Beca incial' THEN 'Beca inicial'
           WHEN 'Becas internas postdoctorales' THEN 'Beca posdoctoral'
           WHEN 'Beca Inicial FONCyTL' THEN 'Beca inicial'
           WHEN 'BECA TIPO 1 DOCTORADO' THEN 'TIPO I'
           WHEN 'Concursos de becas internas doctorales y destinadas a postulantes provenientes de paises latinoamericanos' THEN 'TIPO I'
           WHEN 'BECA INTERNA DE POSTGRADO TIPO I (3 AÑOS)' THEN 'TIPO I'
           WHEN 'Beca Interna Doctoral (área ?KE1 TIERRA?)' THEN 'Beca doctoral'
           WHEN 'POS-DOCTORAL (INTERNA)' THEN 'Beca posdoctoral'
           WHEN 'Beca Interna Dcotoral' THEN 'Beca doctoral'
           WHEN 'POSGRADO TIPO II' THEN 'TIPO II'
           WHEN 'BECARIO POSTDOCTORAL' THEN 'Beca posdoctoral'
           WHEN 'BECA BIANUAL.ESTUDIOS DE MAESTRIA.' THEN 'Beca maestría'
           WHEN 'BECA INTERNA POSDOCTORAL' THEN 'Beca posdoctoral'
           WHEN 'POSTGRADO/DOCTORADO BECA TIPO B' THEN 'TIPO B'
           WHEN 'INICICIACIÓN' THEN 'Iniciación'
           WHEN 'POSTGRADO TIPO I AVG' THEN 'TIPO I'
           WHEN 'A' THEN 'TIPO A'
           ELSE
               CASE
                   WHEN bl_becaEstimulo = 1 THEN 'EVC'
                   ELSE ds_tipobeca
                   END
           END AS beca,`nu_materias` as materias, `nu_totalMat` as total, `ds_carrera` as carrera
FROM `docente`
         LEFT JOIN `deddoc` ON `docente`.`cd_deddoc` = `deddoc`.`cd_deddoc`
    LIMIT
    0,1000;

################################ Unidades

SELECT `cd_unidad` as id,`cd_tipounidad` as tipo, `cd_padre` as padre_id, `bl_hijos` as hijos, `ds_unidad` as nombre, `ds_codigo` as codigo, `ds_sigla` as sigla, `ds_direccion` as direccion, `ds_mail` as email, `ds_telefono` as telefono, `cd_facultad` as facultad_id, `bl_activa` as activa FROM `unidad` WHERE 1
