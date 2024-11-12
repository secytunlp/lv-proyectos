################################### Migrar DB #################################################
############### Personas
SELECT cd_docente as id,`ds_nombre` as nombre,`ds_apellido` as apellido,`nu_documento` as documento,CONCAT(nu_precuil,'-',LPAD(nu_documento,8,'0'),'-',nu_postcuil) AS cuil,`ds_sexo` as genero,`ds_calle` as calle,`nu_nro` as nro,`nu_piso` as piso,`ds_depto` as depto,`ds_localidad` as localidad,`cd_provincia` as provincia_id,`nu_cp` as cp,`nu_telefono` as telefono,`ds_mail` as email,`dt_nacimiento` as nacimiento
FROM `docente`
ORDER BY cd_docente
    LIMIT
    0,5000;
############### Investigadores
SELECT `cd_docente` as id,`nu_ident` as ident,`cd_docente` as persona_id,`cd_categoria` as categoria_id,`cd_categoriasicadi` as sicadi_id,
       CASE`cd_carrerainv`
            WHEN '11' THEN null
            WHEN '10' THEN null
           ELSE cd_carrerainv END as carrerainv_id,
       CASE `cd_organismo`
           WHEN '7' THEN null
           else cd_organismo END as organismo_id,
       CASE `cd_facultad`
           WHEN '574' THEN null
           ELSE cd_facultad END as facultad_id,
       CASE `cd_cargo`
           WHEN '6' THEN null
           ELSE cd_cargo END as cargo_id,
       CASE `ds_deddoc`
           WHEN 's/d' THEN null
           WHEN 'SI-1' THEN 'Simple'
           WHEN 'SE-1' THEN 'Semi Exclusiva'
           ELSE ds_deddoc END as deddoc,`cd_universidad` as universidad_id,
       CASE `cd_titulo`
           WHEN '9999' THEN null
           ELSE cd_titulo END as titulo_id,
       CASE `cd_titulopost`
           WHEN '9999' THEN null
           ELSE cd_titulopost END as titulopost_id,`cd_unidad` as unidad_id,
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
           WHEN 'Finalización' THEN 'Beca finalización del doctorado'
           WHEN 'Finalizacion de doctorado (tipo II)' THEN 'Beca finalización del doctorado'
           WHEN 'finalización del doctorado' THEN 'Beca finalización del doctorado'
           WHEN 'Finalización del Doctorado' THEN 'Beca finalización del doctorado'
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
           END AS beca,`nu_materias` as materias, `nu_totalMat` as total, `ds_carrera` as carrera
FROM `docente`
         LEFT JOIN `deddoc` ON `docente`.`cd_deddoc` = `deddoc`.`cd_deddoc`
    LIMIT
    0,5000;

################################ Unidades

SELECT `cd_unidad` as id,`cd_tipounidad` as tipo, `cd_padre` as padre_id, `bl_hijos` as hijos, `ds_unidad` as nombre, `ds_codigo` as codigo, `ds_sigla` as sigla, `ds_direccion` as direccion, `ds_mail` as email, `ds_telefono` as telefono, `cd_facultad` as facultad_id, `bl_activa` as activa
FROM `unidad` WHERE 1

################################ Titulos de grado

SELECT cd_docente as investigador_id,cd_titulo as titulo_id
FROM `docente`
WHERE cd_titulo is not null and cd_titulo != '9999';

################################ Titulos de posgrado

SELECT cd_docente as investigador_id,cd_titulopost as titulo_id
FROM `docente`
WHERE cd_titulopost is not null and cd_titulopost != '9999';



################################ Cargos

SELECT `cd_docente` as investigador_id,cargos_alfabetico.cd_cargo as cargo_id, ds_deddoc as deddoc, dt_fecha as ingreso,cargos_alfabetico.cd_facultad as facultad_id
FROM `cargos_alfabetico`
INNER JOIN docente ON cargos_alfabetico.dni = docente.nu_documento
LEFT JOIN `deddoc` ON `cargos_alfabetico`.`cd_deddoc` = `deddoc`.`cd_deddoc`
WHERE cargos_alfabetico.escalafon = 'Docente' AND cargos_alfabetico.situacion != 'Licencia sin goce de sueldos' AND cargos_alfabetico.situacion != 'Renuncia' AND cargos_alfabetico.situacion != 'Jubilación'
AND cargos_alfabetico.cd_facultad in (177 ,179 ,174 ,180,169 ,187,175,167,181,177,173, 170,172, 171,165,176,168,1220) AND cargos_alfabetico.cd_cargo in (1,2,3,4,5,14);

################################ Carreras

SELECT cd_docente as investigador_id,cd_carrerainv as carrerainv_id, cd_organismo as organismo_id
FROM `docente`
WHERE cd_carrerainv is not null and cd_carrerainv in (1,2,3,4,5,6,8,9,12,13) AND cd_organismo is not null and cd_organismo in (1,2,3,4,5,8,9);

################################ Categorias SPU

SELECT cd_docente as investigador_id,cd_categoria as categoria_id, cd_univcat as universidad_id
FROM `docente`
WHERE cd_categoria is not null and cd_categoria in (6,7,8,9,10);

################################ Categorias SICADI

SELECT cd_docente as investigador_id,cd_categoria as sicadi_id, '2023' as year
FROM `docente`
WHERE cd_categoria is not null and cd_categoria in (6,7,8,9,10);

################################ Becas UNLP

SELECT cd_docente as investigador_id,'UNLP' as institucion, CASE `ds_tipobeca`
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
                                                                WHEN 'BECARIO TIPO 1' THEN 'TIPO I'
                                                                WHEN 'POSTGRADO/ESPECIALIZACION TIPO II' THEN 'TIPO II'
                                                                WHEN 'FORMACIÓN SUPERIOR EN LA INVESTIGACIÓN Y DESARROLLO ARTÍSTICO, CIENTÍFICO Y TECNOLÓGICO' THEN 'Formación Superior'
                                                                WHEN 'FORMACIóN SUPERIOR' THEN 'Formación Superior'
                                                                WHEN 'BECA DE RETENCIÓN DE POSGRADUADO' THEN 'RETENCION DE POSTGRADUADO'
                                                                WHEN 'BECA DE POSTGRADUADO' THEN 'RETENCION DE POSTGRADUADO'
                                                                WHEN 'Postgraduados' THEN 'RETENCION DE POSTGRADUADO'
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
                                                                WHEN 'Maestría' THEN 'Beca maestría'
                                                                WHEN 'BECA INTERNA POSDOCTORAL' THEN 'Beca posdoctoral'
                                                                WHEN 'POSTGRADO/DOCTORADO BECA TIPO B' THEN 'TIPO B'
                                                                WHEN 'INICICIACIÓN' THEN 'Iniciación'
                                                                WHEN 'INICIACIóN' THEN 'Iniciación'

                                                                WHEN 'POSTGRADO TIPO I AVG' THEN 'TIPO I'
                                                                WHEN 'A' THEN 'TIPO A'
                                                                WHEN 'Cofinanciadas CIC-UNLP' THEN 'Beca Cofinanciada (UNLP-CIC)'
                                                                ELSE ds_tipobeca
                                                                END AS beca, dt_desde as desde, dt_hasta as hasta, bl_unlp as unlp
FROM `beca`

############### Otras becas
SELECT cd_docente as investigador_id,
       CASE `ds_orgbeca`
           WHEN 'Otro' THEN 'OTRA'
           WHEN 'Consejo de Invest. Científicas de la Provincia de Bs As' THEN 'CIC'
           WHEN 'Consejo Nac. Invest. Científicas y Técnicas' THEN 'CONICET'
           WHEN 'CONSEJO DE INV. CIENTÍFICAS DE LA PROVINCIA DE BUENOS AIRES' THEN 'CIC'
           WHEN 'CONSEJO NACIONAL DE INVESTIGACIONES CIENTÍFICAS Y TÉCNICAS (CONICET)' THEN 'CONICET'
           WHEN 'CONSEJO NACIONAL DE INVESTIGACIONES CIENTÍFICAS Y TÉCNICAS' THEN 'CONICET'
           WHEN 'ANPCYT-UNLP' THEN 'ANPCyT'
           WHEN 'AGENCIA' THEN 'ANPCyT'
           WHEN 'CONVENIO COMISIÓN NACIONAL DE ACTIVIDADES ESPACIALES' THEN 'OTRA'
           WHEN 'Consejo Nac. Invest. Cient' THEN 'CONICET'
           WHEN 'CONSEJO DE INVESTIGACIÓN CIENTÍFICAS DE LA PROVINCIA DE BUENOS AIRES' THEN 'CIC'
           WHEN 'COMISION DE INVESTIGACIONES CIENTIFICAS BS AS' THEN 'CIC'
           WHEN 'CONCEJO DE INVEST. CIENTÍFICAS DE LA PROVINCIA DE BUENOS AIRES' THEN 'CIC'
           WHEN 'UNAM, MÉXICO' THEN 'OTRA'
           WHEN 'AGENCIA NACIONAL DE PROMOCIÓN CIENTÍFICA Y TECNOLÓGICA' THEN 'ANPCyT'
           WHEN 'INTA' THEN 'OTRA'
           WHEN 'CONICET / DAAD' THEN 'CONICET'
           WHEN 'CICPBA' THEN 'CIC'
           WHEN 'CONSEJO NACIONAL DE INVESTIGACIONES CIENTÍFICAS' THEN 'CONICET'
           WHEN 'CINDECA-CONICET-UNLP' THEN 'CONICET'
           WHEN 'COMISIÓN DE INVESTIGACIONES CIENTÍFICAS BS.AS' THEN 'CIC'
           WHEN 'FONCYT' THEN 'OTRA'
           WHEN 'COMISIÓN DE INVESTIGACIONES CIENTÍFICAS DE LA PROVINCIA DE BUENOS AIRES' THEN 'CIC'
           WHEN 'ANCYPT' THEN 'ANPCyT'
           WHEN 'CENTRO DE INVESTIGACIONES URBANAS Y TERRITORIALES (CIUT)' THEN 'OTRA'
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
           WHEN 'BECARIO TIPO 1' THEN 'TIPO I'
           WHEN 'POSTGRADO/ESPECIALIZACION TIPO II' THEN 'TIPO II'
           WHEN 'FORMACIÓN SUPERIOR EN LA INVESTIGACIÓN Y DESARROLLO ARTÍSTICO, CIENTÍFICO Y TECNOLÓGICO' THEN 'Formación Superior'
           WHEN 'FORMACIóN SUPERIOR' THEN 'Formación Superior'
           WHEN 'BECA DE RETENCIÓN DE POSGRADUADO' THEN 'RETENCION DE POSTGRADUADO'
           WHEN 'BECA DE POSTGRADUADO' THEN 'RETENCION DE POSTGRADUADO'
           WHEN 'Postgraduados' THEN 'RETENCION DE POSTGRADUADO'
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
           END AS beca, CASE
    WHEN dt_beca IS NOT NULL THEN dt_beca
    WHEN dt_becaEstimulo IS NOT NULL THEN dt_becaEstimulo
    ELSE NULL
END AS desde, CASE
           WHEN dt_becaHasta  IS NOT NULL THEN dt_becaHasta
           WHEN dt_becaEstimuloHasta IS NOT NULL THEN dt_becaEstimuloHasta
           ELSE NULL
END AS hasta, 0 as unlp
FROM `docente`
WHERE (bl_becario = 1 or bl_becaEstimulo = 1)
  AND ds_tipobeca != 'No declarado'
    AND ds_tipobeca != ''
    AND ds_tipobeca IS NOT NULL
    AND (ds_orgbeca IS NOT NULL
         OR (ds_orgbeca NOT LIKE '%unlp%'
             AND ds_orgbeca NOT LIKE '%U.N.L.P%'
             AND ds_orgbeca NOT LIKE '%DE LA PLATA%'
             AND ds_orgbeca NOT LIKE '%FACULTAD%'
             AND ds_orgbeca NOT LIKE '%FCNYM%'
             AND ds_orgbeca NOT LIKE '%FUNDACION CIENCIAS EXACTAS%')
        );

############### Proyectos

SELECT proyecto.cd_proyecto as id, CASE tipoacreditacion.ds_tipoacreditacion
            WHEN 'Proyectos I+D' THEN 'I+D'
            ELSE tipoacreditacion.ds_tipoacreditacion END as tipo, estadoproyecto.ds_estado as estado, proyecto.ds_codigo as codigo, proyecto.ds_codigoSIGEVA as sigeva,
       proyecto.ds_titulo as titulo,
       proyecto.dt_ini as inicio, proyecto.dt_fin as fin, proyecto.cd_facultad as facultad_id, proyecto.nu_duracion as duracion,
       proyecto.cd_unidad as unidad_id, proyecto.cd_campo as campo_id, proyecto.cd_disciplina as disciplina_id, proyecto.cd_especialidad as especialidad_id,
       CASE proyecto.ds_tipo
           WHEN 'A' THEN 'Aplicada'
           WHEN 'B' THEN 'Básica'
           WHEN 'D' THEN 'Desarrollo'
           WHEN 'C' THEN 'Creación' END AS investigacion, proyecto.ds_linea as linea, proyecto.ds_abstract1 as resumen,
    proyecto.ds_clave1 as clave1, proyecto.ds_clave2 as clave2, proyecto.ds_clave3 as clave3, proyecto.ds_clave4 as clave4, proyecto.ds_clave5 as clave5,
    proyecto.ds_clave6 as clave6, proyecto.ds_claveeng1 as key1, proyecto.ds_claveeng2 as key2, proyecto.ds_claveeng3 as key3, proyecto.ds_claveeng4 as key4,
    proyecto.ds_claveeng5 as key5, proyecto.ds_claveeng6 as key6

FROM `proyecto`
         LEFT JOIN tipoacreditacion on proyecto.cd_tipoacreditacion = tipoacreditacion.cd_tipoacreditacion
LEFT JOIN estadoproyecto ON proyecto.cd_estado = estadoproyecto.cd_estado

######################### integrantes
SELECT integrante.oid as id,integrante.cd_docente as investigador_id,integrante.cd_proyecto as proyecto_id,
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
                   WHEN integrante.bl_becaEstimulo = 1 THEN 'CIN'
                   ELSE integrante.ds_orgbeca
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
           WHEN 'BECARIO TIPO 1' THEN 'TIPO I'
           WHEN 'POSTGRADO/ESPECIALIZACION TIPO II' THEN 'TIPO II'
           WHEN 'FORMACIÓN SUPERIOR EN LA INVESTIGACIÓN Y DESARROLLO ARTÍSTICO, CIENTÍFICO Y TECNOLÓGICO' THEN 'Formación Superior'
           WHEN 'FORMACIóN SUPERIOR' THEN 'Formación Superior'
           WHEN 'BECA DE RETENCIÓN DE POSGRADUADO' THEN 'RETENCION DE POSTGRADUADO'
           WHEN 'BECA DE POSTGRADUADO' THEN 'RETENCION DE POSTGRADUADO'
           WHEN 'Postgraduados' THEN 'RETENCION DE POSTGRADUADO'
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
                   WHEN integrante.bl_becaEstimulo = 1 THEN 'EVC'
                   ELSE integrante.ds_tipobeca
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
       integrante.nu_materias as materias, integrante.nu_totalMat as total, integrante.ds_carrera as carrera
FROM integrante
LEFT JOIN proyecto ON integrante.cd_proyecto = proyecto.cd_proyecto
LEFT JOIN docente ON integrante.cd_docente = docente.cd_docente
LEFT JOIN `deddoc` ON `docente`.`cd_deddoc` = `deddoc`.`cd_deddoc`
limit 0, 5000

######################### estados
SELECT cyt_integrante_estado.integrante_oid as integrante_id,
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
           WHEN 'FORMACIóN SUPERIOR' THEN 'Formación Superior'
           WHEN 'BECA DE RETENCIÓN DE POSGRADUADO' THEN 'RETENCION DE POSTGRADUADO'
           WHEN 'BECA DE POSTGRADUADO' THEN 'RETENCION DE POSTGRADUADO'
           WHEN 'Postgraduados' THEN 'RETENCION DE POSTGRADUADO'
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
       cyt_integrante_estado.fechaDesde as desde, cyt_integrante_estado.fechaHasta as hasta, cyt_integrante_estado.motivo as comentarios
FROM cyt_integrante_estado
         LEFT JOIN cyt_user ON cyt_integrante_estado.user_oid = cyt_user.oid
         LEFT JOIN integrante ON integrante.oid = cyt_integrante_estado.integrante_oid
         LEFT JOIN docente ON integrante.cd_docente = docente.cd_docente
         LEFT JOIN `deddoc` ON `docente`.`cd_deddoc` = `deddoc`.`cd_deddoc`
    limit 0, 5000


INSERT INTO `joven_planillas` (`id`, `nombre`, `maximo`, `periodo_id`) VALUES
    (1, 'Planilla', 100, 2),
    (2, 'Planilla', 100, 3),
    (3, 'Planilla', 100, 4),
    (4, 'Planilla', 100, 5),
    (5, 'Planilla', 100, 6),
    (6, 'Planilla', 100, 7),
    (7, 'Planilla', 100, 8),
    (8, 'Planilla', 100, 9),
    (9, 'Planilla', 100, 10),
    (10, 'Planilla', 100, 12),
    (11, 'Planilla', 100, 13),
    (12, 'Planilla', 100, 14),
    (13, 'Planilla', 100, 15);

INSERT INTO `joven_planilla_posgrados` (`id`, `nombre`) VALUES
                                                                                  (1, 'Doctorado'),
                                                                                  (2, 'Maestría'),
                                                                                  (3, 'Especialización'),
                                                                                  (4, 'Sin posgrado');

INSERT INTO `joven_planilla_posgrado_maxs` (`joven_planilla_id`, `id`, `joven_planilla_posgrado_id`, `nombre`, `maximo`) VALUES
                                                                                                                                (1, 1, 1,'A', 3),
                                                                                                                                (1, 2, 2,'A', 2),
                                                                                                                                (1, 3, 3,'A', 1),
                                                                                                                                (1, 4, 4,'A', 0),
                                                                                                                                (2, 5, 1,'A', 3),
                                                                                                                                (2, 6, 2,'A', 2),
                                                                                                                                (2, 7, 3,'A', 1),
                                                                                                                                (2, 8, 4,'A', 0),
                                                                                                                                (3, 9, 1,'A', 4),
                                                                                                                                (3, 10, 2,'A', 2),
                                                                                                                                (3, 11, 3,'A', 1),
                                                                                                                                (3, 12, 4,'A', 0),
                                                                                                                                (4, 13, 1,'A', 5),
                                                                                                                                (4, 14, 2,'A', 3),
                                                                                                                                (4, 15, 3,'A', 1),
                                                                                                                                (4, 16, 4,'A', 0),
                                                                                                                                (5, 17, 1,'A', 5),
                                                                                                                                (5, 18, 2,'A', 3),
                                                                                                                                (5, 19, 3,'A', 1),
                                                                                                                                (5, 20, 4,'A', 0),
                                                                                                                                (6, 21, 1,'A', 5),
                                                                                                                                (6, 22, 2,'A', 3),
                                                                                                                                (6, 23, 3,'A', 1),
                                                                                                                                (6, 24, 4,'A', 0),
                                                                                                                                (7, 25, 1,'A', 5),
                                                                                                                                (7, 26, 2,'A', 3),
                                                                                                                                (7, 27, 3,'A', 1),
                                                                                                                                (7, 28, 4,'A', 0),
                                                                                                                                (8, 29, 1,'A', 5),
                                                                                                                                (8, 30, 2,'A', 3),
                                                                                                                                (8, 31, 3,'A', 1),
                                                                                                                                (8, 32, 4,'A', 0),
                                                                                                                                (9, 33, 1,'A', 5),
                                                                                                                                (9, 34, 2,'A', 3),
                                                                                                                                (9, 35, 3,'A', 1),
                                                                                                                                (9, 36, 4,'A', 0),
                                                                                                                                (10, 37, 1,'A', 5),
                                                                                                                                (10, 38, 2,'A', 3),
                                                                                                                                (10, 39, 3,'A', 1),
                                                                                                                                (10, 40, 4,'A', 0),
                                                                                                                                (11, 41, 1,'A', 5),
                                                                                                                                (11, 42, 2,'A', 3),
                                                                                                                                (11, 43, 3,'A', 1),
                                                                                                                                (11, 44, 4,'A', 0),
                                                                                                                                (12, 45, 1,'A', 5),
                                                                                                                                (12, 46, 2,'A', 3),
                                                                                                                                (12, 47, 3,'A', 1),
                                                                                                                                (12, 48, 4,'A', 0),
                                                                                                                                (13, 49, 1,'A', 5),
                                                                                                                                (13, 50, 2,'A', 3),
                                                                                                                                (13, 51, 3,'A', 1),
                                                                                                                                (13, 52, 4,'A', 0);

INSERT INTO `joven_evaluacion_planilla_antecedentes_academicos` (`id`, `nombre`, `pdf`) VALUES
                                                                                                (1, 'Becas obtenidas: #puntaje# por c/año de beca de postgrado. El año en curso se considera como año completo de Beca.', 'Becas obtenidas'),
                                                                                                (2, 'Cursos: #puntaje# por c/curso de postgrado de más de 30 hs. aprobado.', 'Cursos'),
                                                                                                (3, 'El \"Factor de eficiencia\" F lo calcula automáticamente el Sistema<br> Sólo se debe tildar si el solicitante ya obtuvo el grado académico superior de la especialidad', 'Relación entre la fecha de egreso del título de grado y su situación académica actual (años de egreso / avance en el Postgrado / avance como Becario)'),
                                                                                                (4, 'Si el postulante es actualmente becario de la U.N.L.P. o fue becario de la U.N.L.P. por un período mayor a 2 años', 'Desempeño como Becario UNLP'),
                                                                                                (5, 'Distinciones / premios / otras becas (no declarados en el punto A2)', 'Distinciones / Premios'),
                                                                                                (6, 'Otros antecedentes académicos (distinciones, premios, etc.)', 'Otros antecedentes académicos (distinciones, premios, etc.)'),
                                                                                                (7, 'El solicitante obtuvo el grado académico superior', 'Relación entre la fecha de egreso del título de grado y su situación académica actual (años de egreso / avance en el Postgrado / avance como Becario)'),
                                                                                                (8, 'Otros antecedentes académicos (distinciones, premios, estancias y pasantías)', 'Otros antecedentes académicos (distinciones, premios, estancias y pasantías)'),
                                                                                                (9, 'Cursos: #puntaje# por c/curso de postgrado de más de 25 hs. aprobado.', 'Cursos');

INSERT INTO `evaluacion_grupos` (`id`, `nombre`, `maximo`, `padre_id`) VALUES
                                                                                                 (1, 'AFprod1', 25, NULL),
                                                                                                 (2, 'AFprod2', 8, NULL),
                                                                                                 (3, 'AFprod3', 7, NULL),
                                                                                                 (4, 'AFform1', 5, NULL),
                                                                                                 (5, 'AFform2', 5, NULL),
                                                                                                 (6, 'AnFprod1', 15, NULL),
                                                                                                 (7, 'AnFprod2', 20, NULL),
                                                                                                 (8, 'AnFprod3', 5, NULL),
                                                                                                 (9, 'AnFform1', 2.5, NULL),
                                                                                                 (10, 'AnFform2', 2.5, NULL),
                                                                                                 (11, 'BFprod1', 12, NULL),
                                                                                                 (12, 'BFprod2', 5, NULL),
                                                                                                 (13, 'BFprod3', 5, NULL),
                                                                                                 (14, 'BFform1', 2.5, NULL),
                                                                                                 (15, 'BFform2', 2.5, NULL),
                                                                                                 (16, 'BnFprod1', 8, NULL),
                                                                                                 (17, 'BnFprod2', 10, NULL),
                                                                                                 (18, 'BnFprod3', 2, NULL),
                                                                                                 (19, 'BnFform1', 1, NULL),
                                                                                                 (20, 'BnFform2', 1, NULL),
                                                                                                 (21, 'Cprod1', 12, NULL),
                                                                                                 (22, 'Cprod2', 4, NULL),
                                                                                                 (23, 'Cprod3', 4, NULL),
                                                                                                 (24, 'Cform1', 2.5, NULL),
                                                                                                 (25, 'Cform2', 2.5, NULL),
                                                                                                 (26, 'AFeven1', 25, NULL),
                                                                                                 (27, 'AnFeven1', 30, NULL),
                                                                                                 (28, 'BFeven1', 20, NULL),
                                                                                                 (29, 'BnFeven1', 25, NULL),
                                                                                                 (30, 'Ceven1', 25, NULL),
                                                                                                 (31, 'A', 22, NULL),
                                                                                                 (32, 'C', 5, NULL),
                                                                                                 (33, 'D', 55, NULL),
                                                                                                 (34, 'E', 8, NULL),
                                                                                                 (35, 'D', 48, NULL),
                                                                                                 (36, 'E', 15, NULL),
                                                                                                 (37, 'AFprod3', 12, NULL),
                                                                                                 (38, 'AnFprod1', 20, NULL),
                                                                                                 (39, 'AnFprod2', 15, NULL),
                                                                                                 (40, 'AnFprod3', 10, NULL),
                                                                                                 (41, 'BFprod2', 4, NULL),
                                                                                                 (42, 'BFprod3', 6, NULL),
                                                                                                 (43, 'BnFprod1', 10, NULL),
                                                                                                 (44, 'BnFprod2', 5, NULL),
                                                                                                 (45, 'BnFprod3', 5, NULL),
                                                                                                 (46, 'Cprod2', 3, NULL),
                                                                                                 (47, 'Cprod3', 5, NULL),

                                                                                                 (55, 'F', 15, NULL),

                                                                                                 (57, 'AFprod2', 10, NULL),
                                                                                                 (58, 'AFprod3', 15, NULL),
                                                                                                 (59, 'AFform1', 6, NULL),
                                                                                                 (60, 'AFform2', 4, NULL),

                                                                                                 (63, 'BFprod2', 8, NULL),
                                                                                                 (64, 'BFprod3', 10, NULL),
                                                                                                 (65, 'BFform1', 6, NULL),
                                                                                                 (66, 'BFform2', 4, NULL),

                                                                                                 (68, 'BnFprod2', 15, NULL),
                                                                                                 (69, 'BnFprod3', 10, NULL),

                                                                                                 (71, 'Cprod2', 8, NULL),
                                                                                                 (72, 'Cprod3', 10, NULL),
                                                                                                 (73, 'Cform1', 6, NULL),
                                                                                                 (74, 'Cform2', 4, NULL),
                                                                                                 (75, 'AFprod1_Padre', 45, NULL),
                                                                                                 (76, 'AnFprod1_Padre', 45, NULL),
                                                                                                 (77, 'BFprod1_Padre', 37, NULL),
                                                                                                 (78, 'BnFprod1_Padre', 40, NULL),
                                                                                                 (79, 'Cprod1_Padre', 35, NULL),
                                                                                                 (80, 'D', 45, NULL);


INSERT INTO `evaluacion_grupos` (`id`, `nombre`, `maximo`, `padre_id`) VALUES
                                                                           (48, 'C', 3, 32),
                                                                           (49, 'Sin Puntaje C', 0, 32),
                                                                           (50, 'D1', 15, 35),
                                                                           (51, 'D2', 20, 35),
                                                                           (52, 'D3', 40, 35),
                                                                           (53, 'Sin Puntaje D', 0, 35),
                                                                           (54, 'D5', 40, 35),
                                                                           (56, 'AFprod1', 35, 75),
                                                                           (61, 'AnFprod1', 35, 76),
                                                                           (62, 'BFprod1', 30, 77),
                                                                           (67, 'BnFprod1', 35, 78),
                                                                           (70, 'Cprod1', 30, 79),
                                                                           (81, 'D1', 15, 80),
                                                                           (82, 'D2', 20, 80),
                                                                           (83, 'D3', 40, 80),
                                                                           (84, 'Sin Puntaje D', 0, 80),
                                                                           (85, 'D5', 40, 80);

INSERT INTO `joven_evaluacion_planilla_ant_acad_maxs` (`joven_evaluacion_planilla_id`, `id`, `joven_evaluacion_planilla_ant_acad_id`, `maximo`, `evaluacion_grupo_id`, `minimo`, `tope`) VALUES
                                                                                                                                                  (1, 1, 1, 3, 31, 3, 12),
                                                                                                                                                  (1, 2, 2, 2, 31, 2, 8),
                                                                                                                                                  (1, 3, 3, 0, 31, 0, 0),
                                                                                                                                                  (1, 4, 4, 2, 31, 2, 2),
                                                                                                                                                  (1, 5, 5, 0, 31, 0, 2),
                                                                                                                                                  (2, 6, 1, 3, 31, 3, 12),
                                                                                                                                                  (2, 7, 2, 2, 31, 2, 8),
                                                                                                                                                  (2, 8, 3, 0, 31, 0, 0),
                                                                                                                                                  (2, 9, 4, 2, 31, 2, 2),
                                                                                                                                                  (2, 10, 5, 0, 31, 0, 2),
                                                                                                                                                  (3, 11, 1, 3, 31, 3, 12),
                                                                                                                                                  (3, 12, 2, 1, 31, 1, 6),
                                                                                                                                                  (3, 13, 3, 0, 31, 0, 0),
                                                                                                                                                  (3, 14, 4, 2, 31, 2, 2),
                                                                                                                                                  (3, 15, 5, 0, 31, 0, 2),
                                                                                                                                                  (4, 16, 1, 3, 31, 3, 12),
                                                                                                                                                  (4, 17, 2, 1, 31, 1, 6),
                                                                                                                                                  (4, 18, 3, 0, 31, 0, 0),
                                                                                                                                                  (4, 19, 4, 2, 31, 2, 2),
                                                                                                                                                  (4, 20, 6, 0, 31, 0, 2),
                                                                                                                                                  (5, 21, 1, 3, 31, 3, 12),
                                                                                                                                                  (5, 22, 2, 1, 31, 1, 6),
                                                                                                                                                  (5, 23, 3, 0, 31, 0, 0),
                                                                                                                                                  (5, 24, 4, 2, 31, 2, 2),
                                                                                                                                                  (5, 25, 6, 0, 31, 0, 2),
                                                                                                                                                  (6, 26, 1, 3, 31, 3, 12),
                                                                                                                                                  (6, 27, 2, 1, 31, 1, 6),
                                                                                                                                                  (6, 28, 3, 0, 31, 0, 0),
                                                                                                                                                  (6, 29, 4, 2, 31, 2, 2),
                                                                                                                                                  (6, 30, 6, 0, 31, 0, 2),
                                                                                                                                                  (7, 31, 1, 3, 31, 3, 12),
                                                                                                                                                  (7, 32, 2, 1, 31, 1, 6),
                                                                                                                                                  (7, 33, 7, 0, 31, 0, 0),
                                                                                                                                                  (7, 34, 4, 2, 31, 2, 2),
                                                                                                                                                  (7, 35, 8, 0, 31, 0, 2),
                                                                                                                                                  (8, 36, 1, 3, 31, 3, 12),
                                                                                                                                                  (8, 37, 9, 1, 31, 1, 6),
                                                                                                                                                  (8, 38, 7, 0, 31, 0, 0),
                                                                                                                                                  (8, 39, 4, 2, 31, 2, 2),
                                                                                                                                                  (8, 40, 8, 0, 31, 0, 2),
                                                                                                                                                  (9, 41, 1, 3, 31, 3, 12),
                                                                                                                                                  (9, 42, 9, 1, 31, 1, 6),
                                                                                                                                                  (9, 43, 7, 0, 31, 0, 0),
                                                                                                                                                  (9, 44, 4, 2, 31, 2, 2),
                                                                                                                                                  (9, 45, 8, 0, 31, 0, 2),
                                                                                                                                                  (10, 46, 1, 3, 31, 3, 12),
                                                                                                                                                  (10, 47, 9, 1, 31, 1, 6),
                                                                                                                                                  (10, 48, 7, 0, 31, 0, 0),
                                                                                                                                                  (10, 49, 4, 2, 31, 2, 2),
                                                                                                                                                  (10, 50, 8, 0, 31, 0, 2),
                                                                                                                                                  (11, 51, 1, 3, 31, 3, 12),
                                                                                                                                                  (11, 52, 9, 1, 31, 1, 6),
                                                                                                                                                  (11, 53, 7, 0, 31, 0, 0),
                                                                                                                                                  (11, 54, 4, 2, 31, 2, 2),
                                                                                                                                                  (11, 55, 8, 0, 31, 0, 2),
                                                                                                                                                  (12, 56, 1, 3, 31, 3, 12),
                                                                                                                                                  (12, 57, 9, 1, 31, 1, 6),
                                                                                                                                                  (12, 58, 7, 0, 31, 0, 0),
                                                                                                                                                  (12, 59, 4, 2, 31, 2, 2),
                                                                                                                                                  (12, 60, 8, 0, 31, 0, 2),
                                                                                                                                                  (13, 61, 1, 3, 31, 3, 12),
                                                                                                                                                  (13, 62, 9, 1, 31, 1, 6),
                                                                                                                                                  (13, 63, 7, 0, 31, 0, 0),
                                                                                                                                                  (13, 64, 4, 2, 31, 2, 2),
                                                                                                                                                  (13, 65, 8, 0, 31, 0, 2);

INSERT INTO `puntajeposgrado` (`cd_puntajeposgrado`, `cd_evaluacion`, `cd_modeloplanilla`, `cd_posgradomaximo`) VALUES
                                                                                                                    (195, 1, 1, 0),
                                                                                                                    (162, 2, 1, 0),
                                                                                                                    (21, 3, 1, 2),
                                                                                                                    (126, 4, 1, 4),
                                                                                                                    (119, 5, 1, 4),
                                                                                                                    (102, 6, 1, 4),
                                                                                                                    (219, 7, 1, 4),
                                                                                                                    (95, 8, 1, 4),
                                                                                                                    (12, 9, 1, 4),
                                                                                                                    (16, 10, 1, 4),
                                                                                                                    (200, 11, 1, 1),
                                                                                                                    (189, 12, 1, 0),
                                                                                                                    (46, 13, 1, 4),
                                                                                                                    (250, 14, 1, 0),
                                                                                                                    (134, 15, 1, 0),
                                                                                                                    (98, 16, 1, 4),
                                                                                                                    (120, 17, 1, 4),
                                                                                                                    (222, 18, 1, 4),
                                                                                                                    (144, 19, 1, 2),
                                                                                                                    (255, 20, 1, 2),
                                                                                                                    (223, 21, 1, 4),
                                                                                                                    (167, 22, 1, 4),
                                                                                                                    (177, 23, 1, 3),
                                                                                                                    (186, 24, 1, 3),
                                                                                                                    (221, 26, 1, 4),
                                                                                                                    (94, 27, 1, 4),
                                                                                                                    (217, 28, 1, 4),
                                                                                                                    (58, 29, 1, 4),
                                                                                                                    (115, 30, 1, 4),
                                                                                                                    (44, 31, 1, 1),
                                                                                                                    (22, 32, 1, 1),
                                                                                                                    (191, 33, 1, 0),
                                                                                                                    (199, 34, 1, 3),
                                                                                                                    (224, 35, 1, 4),
                                                                                                                    (32, 36, 1, 4),
                                                                                                                    (214, 37, 1, 0),
                                                                                                                    (69, 38, 1, 0),
                                                                                                                    (51, 39, 1, 4),
                                                                                                                    (64, 40, 1, 4),
                                                                                                                    (145, 41, 1, 4),
                                                                                                                    (128, 42, 1, 4),
                                                                                                                    (6, 43, 1, 4),
                                                                                                                    (68, 44, 1, 4),
                                                                                                                    (183, 45, 1, 4),
                                                                                                                    (2, 46, 1, 4),
                                                                                                                    (88, 47, 1, 0),
                                                                                                                    (204, 48, 1, 4),
                                                                                                                    (53, 49, 1, 1),
                                                                                                                    (8, 50, 1, 4),
                                                                                                                    (173, 51, 1, 4),
                                                                                                                    (118, 52, 1, 4),
                                                                                                                    (77, 53, 1, 1),
                                                                                                                    (165, 54, 1, 1),
                                                                                                                    (25, 55, 1, 1),
                                                                                                                    (168, 56, 1, 1),
                                                                                                                    (209, 57, 1, 1),
                                                                                                                    (179, 58, 1, 4),
                                                                                                                    (130, 59, 1, 1),
                                                                                                                    (226, 60, 1, 1),
                                                                                                                    (7, 61, 1, 4),
                                                                                                                    (197, 62, 1, 2),
                                                                                                                    (207, 63, 1, 1),
                                                                                                                    (198, 64, 1, 1),
                                                                                                                    (9, 65, 1, 1),
                                                                                                                    (228, 66, 1, 0),
                                                                                                                    (89, 67, 1, 4),
                                                                                                                    (150, 68, 1, 4),
                                                                                                                    (211, 69, 1, 4),
                                                                                                                    (63, 70, 1, 2),
                                                                                                                    (181, 71, 1, 2),
                                                                                                                    (15, 72, 1, 4),
                                                                                                                    (147, 73, 1, 4),
                                                                                                                    (203, 74, 1, 4),
                                                                                                                    (97, 75, 1, 4),
                                                                                                                    (247, 76, 1, 4),
                                                                                                                    (184, 77, 1, 4),
                                                                                                                    (76, 78, 1, 4),
                                                                                                                    (136, 79, 1, 4),
                                                                                                                    (4, 80, 1, 0),
                                                                                                                    (11, 81, 1, 0),
                                                                                                                    (3, 82, 1, 4),
                                                                                                                    (137, 83, 1, 4),
                                                                                                                    (81, 84, 1, 4),
                                                                                                                    (10, 85, 1, 4),
                                                                                                                    (106, 86, 1, 2),
                                                                                                                    (39, 87, 1, 2),
                                                                                                                    (107, 88, 1, 2),
                                                                                                                    (169, 89, 1, 2),
                                                                                                                    (47, 90, 1, 1),
                                                                                                                    (176, 91, 1, 1),
                                                                                                                    (123, 92, 1, 4),
                                                                                                                    (5, 93, 1, 4),
                                                                                                                    (67, 94, 1, 3),
                                                                                                                    (116, 95, 1, 3),
                                                                                                                    (143, 96, 1, 4),
                                                                                                                    (14, 97, 1, 4),
                                                                                                                    (146, 99, 1, 4),
                                                                                                                    (252, 100, 1, 4),
                                                                                                                    (182, 101, 1, 0),
                                                                                                                    (201, 102, 1, 4),
                                                                                                                    (91, 103, 1, 4),
                                                                                                                    (156, 104, 1, 4),
                                                                                                                    (99, 105, 1, 2),
                                                                                                                    (127, 106, 1, 2),
                                                                                                                    (161, 107, 1, 4),
                                                                                                                    (185, 108, 1, 4),
                                                                                                                    (101, 109, 1, 0),
                                                                                                                    (159, 110, 1, 0),
                                                                                                                    (59, 111, 1, 0),
                                                                                                                    (227, 112, 1, 4),
                                                                                                                    (61, 113, 1, 0),
                                                                                                                    (178, 114, 1, 4),
                                                                                                                    (104, 115, 1, 2),
                                                                                                                    (19, 116, 1, 2),
                                                                                                                    (28, 117, 1, 0),
                                                                                                                    (121, 118, 1, 4),
                                                                                                                    (170, 119, 1, 4),
                                                                                                                    (72, 120, 1, 4),
                                                                                                                    (114, 121, 1, 4),
                                                                                                                    (132, 122, 1, 0),
                                                                                                                    (171, 123, 1, 4),
                                                                                                                    (13, 124, 1, 4),
                                                                                                                    (85, 125, 1, 0),
                                                                                                                    (31, 126, 1, 1),
                                                                                                                    (230, 127, 1, 1),
                                                                                                                    (113, 128, 1, 1),
                                                                                                                    (17, 129, 1, 1),
                                                                                                                    (157, 130, 1, 4),
                                                                                                                    (40, 131, 1, 4),
                                                                                                                    (112, 132, 1, 1),
                                                                                                                    (105, 133, 1, 1),
                                                                                                                    (110, 134, 1, 1),
                                                                                                                    (96, 135, 1, 1),
                                                                                                                    (48, 136, 1, 1),
                                                                                                                    (20, 137, 1, 1),
                                                                                                                    (54, 138, 1, 1),
                                                                                                                    (231, 139, 1, 1),
                                                                                                                    (140, 140, 1, 1),
                                                                                                                    (122, 141, 1, 1),
                                                                                                                    (138, 142, 1, 1),
                                                                                                                    (175, 143, 1, 1),
                                                                                                                    (133, 144, 1, 0),
                                                                                                                    (29, 145, 1, 4),
                                                                                                                    (60, 146, 1, 1),
                                                                                                                    (93, 147, 1, 1),
                                                                                                                    (42, 148, 1, 4),
                                                                                                                    (79, 149, 1, 4),
                                                                                                                    (41, 150, 1, 4),
                                                                                                                    (26, 151, 1, 0),
                                                                                                                    (213, 152, 1, 0),
                                                                                                                    (18, 153, 1, 0),
                                                                                                                    (202, 154, 1, 1),
                                                                                                                    (27, 155, 1, 1),
                                                                                                                    (111, 156, 1, 1),
                                                                                                                    (125, 157, 1, 1),
                                                                                                                    (124, 158, 1, 0),
                                                                                                                    (142, 159, 1, 4),
                                                                                                                    (108, 160, 1, 1),
                                                                                                                    (117, 161, 1, 1),
                                                                                                                    (65, 162, 1, 0),
                                                                                                                    (56, 163, 1, 0),
                                                                                                                    (135, 164, 1, 1),
                                                                                                                    (57, 165, 1, 1),
                                                                                                                    (30, 166, 1, 4),
                                                                                                                    (216, 167, 1, 4),
                                                                                                                    (158, 168, 1, 1),
                                                                                                                    (23, 169, 1, 1),
                                                                                                                    (34, 170, 1, 4),
                                                                                                                    (129, 171, 1, 4),
                                                                                                                    (75, 172, 1, 1),
                                                                                                                    (205, 173, 1, 1),
                                                                                                                    (172, 174, 1, 1),
                                                                                                                    (180, 175, 1, 1),
                                                                                                                    (33, 176, 1, 1),
                                                                                                                    (225, 177, 1, 1),
                                                                                                                    (164, 179, 1, 1),
                                                                                                                    (35, 180, 1, 4),
                                                                                                                    (86, 181, 1, 4),
                                                                                                                    (70, 182, 1, 1),
                                                                                                                    (192, 183, 1, 1),
                                                                                                                    (78, 184, 1, 1),
                                                                                                                    (45, 185, 1, 1),
                                                                                                                    (152, 186, 1, 1),
                                                                                                                    (24, 187, 1, 1),
                                                                                                                    (215, 188, 1, 3),
                                                                                                                    (36, 189, 1, 1),
                                                                                                                    (131, 190, 1, 1),
                                                                                                                    (80, 191, 1, 1),
                                                                                                                    (206, 192, 1, 1),
                                                                                                                    (74, 193, 1, 1),
                                                                                                                    (163, 194, 1, 0),
                                                                                                                    (194, 196, 1, 0),
                                                                                                                    (154, 197, 1, 1),
                                                                                                                    (208, 198, 1, 1),
                                                                                                                    (37, 199, 1, 1),
                                                                                                                    (87, 200, 1, 1),
                                                                                                                    (38, 201, 1, 1),
                                                                                                                    (66, 202, 1, 1),
                                                                                                                    (196, 203, 1, 1),
                                                                                                                    (55, 204, 1, 2),
                                                                                                                    (148, 205, 1, 2),
                                                                                                                    (103, 206, 1, 2),
                                                                                                                    (220, 207, 1, 2),
                                                                                                                    (50, 208, 1, 0),
                                                                                                                    (249, 209, 1, 0),
                                                                                                                    (90, 210, 1, 4),
                                                                                                                    (82, 211, 1, 4),
                                                                                                                    (166, 212, 1, 4),
                                                                                                                    (155, 213, 1, 4),
                                                                                                                    (100, 214, 1, 4),
                                                                                                                    (84, 215, 1, 0),
                                                                                                                    (188, 216, 1, 0),
                                                                                                                    (212, 217, 1, 4),
                                                                                                                    (43, 218, 1, 1),
                                                                                                                    (73, 219, 1, 1),
                                                                                                                    (193, 220, 1, 4),
                                                                                                                    (190, 221, 1, 0),
                                                                                                                    (153, 222, 1, 1),
                                                                                                                    (229, 223, 1, 4),
                                                                                                                    (174, 224, 1, 4),
                                                                                                                    (49, 225, 1, 1),
                                                                                                                    (52, 226, 1, 1),
                                                                                                                    (218, 227, 1, 1),
                                                                                                                    (149, 228, 1, 1),
                                                                                                                    (151, 229, 1, 4),
                                                                                                                    (92, 230, 1, 1),
                                                                                                                    (71, 231, 1, 1),
                                                                                                                    (139, 232, 1, 1),
                                                                                                                    (62, 233, 1, 1),
                                                                                                                    (187, 234, 1, 2),
                                                                                                                    (83, 235, 1, 2),
                                                                                                                    (254, 236, 1, 4),
                                                                                                                    (109, 237, 1, 0),
                                                                                                                    (141, 238, 1, 4),
                                                                                                                    (210, 239, 1, 4),
                                                                                                                    (160, 240, 1, 4),
                                                                                                                    (241, 241, 1, 2),
                                                                                                                    (232, 242, 1, 4),
                                                                                                                    (258, 243, 1, 0),
                                                                                                                    (245, 244, 1, 4),
                                                                                                                    (260, 245, 1, 4),
                                                                                                                    (242, 246, 1, 4),
                                                                                                                    (233, 247, 1, 4),
                                                                                                                    (239, 248, 1, 1),
                                                                                                                    (235, 249, 1, 4),
                                                                                                                    (259, 250, 1, 0),
                                                                                                                    (244, 251, 1, 4),
                                                                                                                    (251, 252, 1, 4),
                                                                                                                    (237, 253, 1, 1),
                                                                                                                    (240, 254, 1, 1),
                                                                                                                    (248, 255, 1, 1),
                                                                                                                    (234, 256, 1, 4),
                                                                                                                    (236, 257, 1, 1),
                                                                                                                    (256, 258, 1, 0),
                                                                                                                    (238, 259, 1, 4),
                                                                                                                    (246, 260, 1, 4),
                                                                                                                    (261, 261, 1, 4),
                                                                                                                    (243, 262, 1, 0),
                                                                                                                    (257, 263, 1, 4),
                                                                                                                    (253, 264, 1, 4),
                                                                                                                    (290, 266, 2, 8),
                                                                                                                    (436, 267, 2, 8),
                                                                                                                    (319, 268, 2, 6),
                                                                                                                    (348, 269, 2, 6),
                                                                                                                    (326, 270, 2, 5),
                                                                                                                    (435, 271, 2, 5),
                                                                                                                    (420, 272, 2, 8),
                                                                                                                    (488, 273, 2, 8),
                                                                                                                    (493, 274, 2, 5),
                                                                                                                    (460, 275, 2, 5),
                                                                                                                    (530, 276, 2, 8),
                                                                                                                    (369, 277, 2, 8),
                                                                                                                    (333, 278, 2, 5),
                                                                                                                    (417, 279, 2, 5),
                                                                                                                    (323, 280, 2, 8),
                                                                                                                    (313, 281, 2, 0),
                                                                                                                    (382, 282, 2, 8),
                                                                                                                    (306, 283, 2, 8),
                                                                                                                    (440, 284, 2, 5),
                                                                                                                    (452, 285, 2, 5),
                                                                                                                    (462, 286, 2, 5),
                                                                                                                    (534, 287, 2, 5),
                                                                                                                    (386, 288, 2, 5),
                                                                                                                    (341, 289, 2, 5),
                                                                                                                    (526, 290, 2, 5),
                                                                                                                    (455, 291, 2, 5),
                                                                                                                    (328, 292, 2, 8),
                                                                                                                    (535, 293, 2, 8),
                                                                                                                    (270, 294, 2, 5),
                                                                                                                    (340, 295, 2, 5),
                                                                                                                    (520, 296, 2, 5),
                                                                                                                    (267, 297, 2, 5),
                                                                                                                    (422, 298, 2, 5),
                                                                                                                    (300, 299, 2, 5),
                                                                                                                    (528, 300, 2, 5),
                                                                                                                    (456, 301, 2, 5),
                                                                                                                    (274, 302, 2, 8),
                                                                                                                    (536, 303, 2, 5),
                                                                                                                    (464, 304, 2, 5),
                                                                                                                    (307, 305, 2, 5),
                                                                                                                    (322, 306, 2, 8),
                                                                                                                    (285, 307, 2, 8),
                                                                                                                    (522, 308, 2, 5),
                                                                                                                    (296, 309, 2, 5),
                                                                                                                    (329, 310, 2, 5),
                                                                                                                    (379, 311, 2, 5),
                                                                                                                    (492, 312, 2, 8),
                                                                                                                    (457, 313, 2, 0),
                                                                                                                    (491, 314, 2, 5),
                                                                                                                    (370, 315, 2, 5),
                                                                                                                    (482, 316, 2, 5),
                                                                                                                    (477, 317, 2, 5),
                                                                                                                    (527, 318, 2, 5),
                                                                                                                    (273, 319, 2, 5),
                                                                                                                    (293, 320, 2, 8),
                                                                                                                    (510, 321, 2, 0),
                                                                                                                    (396, 322, 2, 5),
                                                                                                                    (434, 323, 2, 5),
                                                                                                                    (407, 324, 2, 5),
                                                                                                                    (346, 325, 2, 5),
                                                                                                                    (470, 326, 2, 8),
                                                                                                                    (489, 327, 2, 8),
                                                                                                                    (438, 328, 2, 8),
                                                                                                                    (459, 329, 2, 8),
                                                                                                                    (523, 330, 2, 8),
                                                                                                                    (453, 331, 2, 0),
                                                                                                                    (356, 332, 2, 8),
                                                                                                                    (537, 333, 2, 8),
                                                                                                                    (358, 334, 2, 5),
                                                                                                                    (343, 335, 2, 5),
                                                                                                                    (439, 336, 2, 5),
                                                                                                                    (385, 337, 2, 5),
                                                                                                                    (425, 338, 2, 5),
                                                                                                                    (515, 339, 2, 5),
                                                                                                                    (334, 340, 2, 5),
                                                                                                                    (344, 341, 2, 5),
                                                                                                                    (518, 342, 2, 5),
                                                                                                                    (355, 343, 2, 5),
                                                                                                                    (327, 344, 2, 5),
                                                                                                                    (350, 345, 2, 5),
                                                                                                                    (478, 346, 2, 5),
                                                                                                                    (479, 347, 2, 5),
                                                                                                                    (284, 348, 2, 0),
                                                                                                                    (368, 349, 2, 8),
                                                                                                                    (486, 350, 2, 8),
                                                                                                                    (399, 351, 2, 8),
                                                                                                                    (447, 352, 2, 8),
                                                                                                                    (297, 353, 2, 8),
                                                                                                                    (432, 354, 2, 8),
                                                                                                                    (299, 355, 2, 8),
                                                                                                                    (281, 356, 2, 0),
                                                                                                                    (367, 357, 2, 8),
                                                                                                                    (487, 358, 2, 8),
                                                                                                                    (414, 359, 2, 8),
                                                                                                                    (448, 360, 2, 8),
                                                                                                                    (315, 361, 2, 0),
                                                                                                                    (433, 362, 2, 8),
                                                                                                                    (280, 364, 2, 0),
                                                                                                                    (524, 365, 2, 8),
                                                                                                                    (485, 366, 2, 8),
                                                                                                                    (314, 367, 2, 8),
                                                                                                                    (449, 368, 2, 8),
                                                                                                                    (383, 369, 2, 0),
                                                                                                                    (431, 370, 2, 8),
                                                                                                                    (517, 371, 2, 0),
                                                                                                                    (509, 372, 2, 5),
                                                                                                                    (339, 373, 2, 5),
                                                                                                                    (336, 374, 2, 0),
                                                                                                                    (454, 375, 2, 0),
                                                                                                                    (508, 376, 2, 5),
                                                                                                                    (316, 377, 2, 5),
                                                                                                                    (345, 378, 2, 0),
                                                                                                                    (437, 379, 2, 8),
                                                                                                                    (378, 380, 2, 0),
                                                                                                                    (295, 381, 2, 8),
                                                                                                                    (500, 382, 2, 5),
                                                                                                                    (451, 383, 2, 0),
                                                                                                                    (360, 384, 2, 5),
                                                                                                                    (444, 385, 2, 5),
                                                                                                                    (516, 386, 2, 5),
                                                                                                                    (342, 387, 2, 5),
                                                                                                                    (338, 388, 2, 5),
                                                                                                                    (352, 389, 2, 5),
                                                                                                                    (380, 390, 2, 0),
                                                                                                                    (292, 391, 2, 8),
                                                                                                                    (514, 392, 2, 5),
                                                                                                                    (351, 393, 2, 5),
                                                                                                                    (337, 394, 2, 5),
                                                                                                                    (318, 395, 2, 5),
                                                                                                                    (501, 396, 2, 8),
                                                                                                                    (289, 397, 2, 8),
                                                                                                                    (387, 398, 2, 5),
                                                                                                                    (317, 399, 2, 5),
                                                                                                                    (269, 400, 2, 8),
                                                                                                                    (450, 401, 2, 8),
                                                                                                                    (551, 402, 2, 5),
                                                                                                                    (463, 403, 2, 5),
                                                                                                                    (263, 404, 2, 8),
                                                                                                                    (494, 405, 2, 0),
                                                                                                                    (461, 406, 2, 5),
                                                                                                                    (276, 407, 2, 5),
                                                                                                                    (442, 408, 2, 5),
                                                                                                                    (277, 409, 2, 8),
                                                                                                                    (410, 410, 2, 8),
                                                                                                                    (282, 411, 2, 5),
                                                                                                                    (504, 412, 2, 5),
                                                                                                                    (271, 414, 2, 5),
                                                                                                                    (381, 415, 2, 0),
                                                                                                                    (279, 416, 2, 8),
                                                                                                                    (353, 417, 2, 0),
                                                                                                                    (401, 418, 2, 5),
                                                                                                                    (287, 419, 2, 5),
                                                                                                                    (465, 420, 2, 5),
                                                                                                                    (286, 421, 2, 5),
                                                                                                                    (359, 422, 2, 5),
                                                                                                                    (398, 423, 2, 5),
                                                                                                                    (423, 424, 2, 5),
                                                                                                                    (354, 425, 2, 5),
                                                                                                                    (412, 426, 2, 5),
                                                                                                                    (395, 427, 2, 5),
                                                                                                                    (301, 428, 2, 8),
                                                                                                                    (458, 429, 2, 8),
                                                                                                                    (430, 430, 2, 8),
                                                                                                                    (424, 431, 2, 8),
                                                                                                                    (549, 432, 2, 8),
                                                                                                                    (496, 433, 2, 8),
                                                                                                                    (357, 434, 2, 8),
                                                                                                                    (427, 435, 2, 8),
                                                                                                                    (507, 436, 2, 5),
                                                                                                                    (446, 437, 2, 5),
                                                                                                                    (262, 438, 2, 5),
                                                                                                                    (302, 439, 2, 5),
                                                                                                                    (275, 440, 2, 5),
                                                                                                                    (497, 441, 2, 0),
                                                                                                                    (445, 442, 2, 0),
                                                                                                                    (506, 443, 2, 8),
                                                                                                                    (543, 444, 2, 8),
                                                                                                                    (272, 445, 2, 5),
                                                                                                                    (413, 446, 2, 5),
                                                                                                                    (429, 447, 2, 5),
                                                                                                                    (443, 448, 2, 5),
                                                                                                                    (268, 449, 2, 8),
                                                                                                                    (490, 450, 2, 8),
                                                                                                                    (411, 451, 2, 8),
                                                                                                                    (288, 452, 2, 8),
                                                                                                                    (409, 453, 2, 5),
                                                                                                                    (400, 454, 2, 5),
                                                                                                                    (408, 455, 2, 5),
                                                                                                                    (298, 456, 2, 5),
                                                                                                                    (502, 457, 2, 5),
                                                                                                                    (505, 458, 2, 5),
                                                                                                                    (394, 459, 2, 5),
                                                                                                                    (421, 460, 2, 5),
                                                                                                                    (503, 461, 2, 5),
                                                                                                                    (426, 462, 2, 5),
                                                                                                                    (403, 464, 2, 5),
                                                                                                                    (472, 465, 2, 8),
                                                                                                                    (265, 466, 2, 6),
                                                                                                                    (361, 467, 2, 5),
                                                                                                                    (563, 468, 2, 5),
                                                                                                                    (415, 469, 2, 7),
                                                                                                                    (565, 470, 2, 7),
                                                                                                                    (475, 471, 2, 8),
                                                                                                                    (511, 472, 2, 8),
                                                                                                                    (266, 473, 2, 8),
                                                                                                                    (512, 474, 2, 8),
                                                                                                                    (332, 475, 2, 8),
                                                                                                                    (542, 476, 2, 8),
                                                                                                                    (539, 477, 2, 8),
                                                                                                                    (473, 478, 2, 8),
                                                                                                                    (513, 479, 2, 8),
                                                                                                                    (264, 480, 2, 8),
                                                                                                                    (365, 481, 2, 8),
                                                                                                                    (559, 482, 2, 0),
                                                                                                                    (483, 483, 2, 0),
                                                                                                                    (324, 484, 2, 8),
                                                                                                                    (388, 485, 2, 0),
                                                                                                                    (331, 486, 2, 8),
                                                                                                                    (521, 487, 2, 0),
                                                                                                                    (498, 488, 2, 8),
                                                                                                                    (347, 489, 2, 8),
                                                                                                                    (321, 490, 2, 8),
                                                                                                                    (480, 491, 2, 0),
                                                                                                                    (529, 492, 2, 8),
                                                                                                                    (389, 493, 2, 6),
                                                                                                                    (372, 494, 2, 6),
                                                                                                                    (471, 495, 2, 8),
                                                                                                                    (404, 496, 2, 8),
                                                                                                                    (525, 497, 2, 6),
                                                                                                                    (309, 498, 2, 6),
                                                                                                                    (374, 499, 2, 6),
                                                                                                                    (364, 500, 2, 6),
                                                                                                                    (481, 501, 2, 5),
                                                                                                                    (373, 502, 2, 5),
                                                                                                                    (469, 503, 2, 5),
                                                                                                                    (406, 504, 2, 5),
                                                                                                                    (390, 505, 2, 0),
                                                                                                                    (311, 506, 2, 8),
                                                                                                                    (466, 507, 2, 5),
                                                                                                                    (363, 508, 2, 5),
                                                                                                                    (519, 509, 2, 8),
                                                                                                                    (310, 510, 2, 8),
                                                                                                                    (484, 511, 2, 6),
                                                                                                                    (325, 512, 2, 8),
                                                                                                                    (392, 513, 2, 7),
                                                                                                                    (308, 514, 2, 6),
                                                                                                                    (532, 515, 2, 6),
                                                                                                                    (376, 516, 2, 8),
                                                                                                                    (499, 517, 2, 8),
                                                                                                                    (303, 518, 2, 8),
                                                                                                                    (371, 519, 2, 5),
                                                                                                                    (557, 520, 2, 5),
                                                                                                                    (405, 521, 2, 8),
                                                                                                                    (349, 522, 2, 0),
                                                                                                                    (312, 523, 2, 8),
                                                                                                                    (362, 525, 2, 5),
                                                                                                                    (554, 526, 2, 5),
                                                                                                                    (366, 527, 2, 6),
                                                                                                                    (556, 528, 2, 6),
                                                                                                                    (428, 529, 2, 0),
                                                                                                                    (283, 530, 2, 8),
                                                                                                                    (377, 531, 2, 5),
                                                                                                                    (278, 532, 2, 5),
                                                                                                                    (291, 533, 2, 8),
                                                                                                                    (294, 534, 2, 5),
                                                                                                                    (393, 535, 2, 5),
                                                                                                                    (305, 536, 2, 8),
                                                                                                                    (419, 537, 2, 6),
                                                                                                                    (320, 538, 2, 8),
                                                                                                                    (416, 539, 2, 8),
                                                                                                                    (476, 540, 2, 6),
                                                                                                                    (397, 541, 2, 0),
                                                                                                                    (330, 542, 2, 6),
                                                                                                                    (304, 543, 2, 0),
                                                                                                                    (564, 544, 2, 0),
                                                                                                                    (391, 545, 2, 8),
                                                                                                                    (558, 546, 2, 0),
                                                                                                                    (474, 547, 2, 8),
                                                                                                                    (566, 548, 2, 8),
                                                                                                                    (468, 549, 2, 8),
                                                                                                                    (540, 550, 2, 8),
                                                                                                                    (402, 551, 2, 8),
                                                                                                                    (375, 552, 2, 6),
                                                                                                                    (418, 553, 2, 6),
                                                                                                                    (384, 554, 2, 6),
                                                                                                                    (533, 555, 2, 6),
                                                                                                                    (495, 556, 2, 5),
                                                                                                                    (335, 557, 2, 5),
                                                                                                                    (441, 558, 2, 7),
                                                                                                                    (541, 559, 2, 8),
                                                                                                                    (548, 561, 2, 8),
                                                                                                                    (531, 562, 2, 8),
                                                                                                                    (538, 563, 2, 5),
                                                                                                                    (544, 564, 2, 5),
                                                                                                                    (555, 565, 2, 8),
                                                                                                                    (560, 566, 2, 8),
                                                                                                                    (547, 567, 2, 6),
                                                                                                                    (561, 568, 2, 0),
                                                                                                                    (550, 570, 2, 8),
                                                                                                                    (545, 571, 2, 8),
                                                                                                                    (562, 572, 2, 0),
                                                                                                                    (569, 573, 2, 8),
                                                                                                                    (553, 574, 2, 8),
                                                                                                                    (546, 575, 2, 6),
                                                                                                                    (568, 576, 2, 8),
                                                                                                                    (567, 577, 2, 8),
                                                                                                                    (618, 578, 3, 12),
                                                                                                                    (660, 579, 3, 12),
                                                                                                                    (663, 580, 3, 12),
                                                                                                                    (675, 581, 3, 9),
                                                                                                                    (669, 582, 3, 9),
                                                                                                                    (598, 583, 3, 9),
                                                                                                                    (596, 584, 3, 9),
                                                                                                                    (597, 585, 3, 12),
                                                                                                                    (662, 586, 3, 9),
                                                                                                                    (664, 587, 3, 0),
                                                                                                                    (620, 588, 3, 9),
                                                                                                                    (661, 589, 3, 12),
                                                                                                                    (665, 590, 3, 12),
                                                                                                                    (611, 591, 3, 10),
                                                                                                                    (683, 592, 3, 9),
                                                                                                                    (622, 593, 3, 9),
                                                                                                                    (666, 594, 3, 9),
                                                                                                                    (648, 595, 3, 9),
                                                                                                                    (613, 596, 3, 9),
                                                                                                                    (659, 597, 3, 12),
                                                                                                                    (624, 598, 3, 9),
                                                                                                                    (610, 599, 3, 10),
                                                                                                                    (612, 600, 3, 9),
                                                                                                                    (621, 601, 3, 12),
                                                                                                                    (647, 602, 3, 0),
                                                                                                                    (615, 603, 3, 10),
                                                                                                                    (616, 604, 3, 10),
                                                                                                                    (645, 605, 3, 12),
                                                                                                                    (655, 606, 3, 12),
                                                                                                                    (638, 607, 3, 9),
                                                                                                                    (656, 608, 3, 9),
                                                                                                                    (623, 609, 3, 9),
                                                                                                                    (646, 610, 3, 9),
                                                                                                                    (627, 611, 3, 9),
                                                                                                                    (651, 612, 3, 10),
                                                                                                                    (685, 613, 3, 0),
                                                                                                                    (594, 614, 3, 12),
                                                                                                                    (684, 615, 3, 0),
                                                                                                                    (686, 616, 3, 12),
                                                                                                                    (576, 617, 3, 12),
                                                                                                                    (634, 618, 3, 9),
                                                                                                                    (572, 619, 3, 12),
                                                                                                                    (633, 620, 3, 12),
                                                                                                                    (631, 621, 3, 12),
                                                                                                                    (577, 622, 3, 12),
                                                                                                                    (574, 623, 3, 12),
                                                                                                                    (630, 624, 3, 11),
                                                                                                                    (639, 625, 3, 10),
                                                                                                                    (575, 627, 3, 0),
                                                                                                                    (573, 628, 3, 12),
                                                                                                                    (570, 630, 3, 12),
                                                                                                                    (607, 632, 3, 9),
                                                                                                                    (609, 633, 3, 9),
                                                                                                                    (640, 634, 3, 12),
                                                                                                                    (585, 635, 3, 12),
                                                                                                                    (602, 636, 3, 12),
                                                                                                                    (635, 637, 3, 12),
                                                                                                                    (658, 638, 3, 12),
                                                                                                                    (584, 639, 3, 12),
                                                                                                                    (595, 640, 3, 0),
                                                                                                                    (628, 641, 3, 12),
                                                                                                                    (641, 642, 3, 12),
                                                                                                                    (593, 645, 3, 12),
                                                                                                                    (580, 646, 3, 12),
                                                                                                                    (697, 647, 3, 12),
                                                                                                                    (636, 648, 3, 12),
                                                                                                                    (693, 649, 3, 9),
                                                                                                                    (619, 650, 3, 12),
                                                                                                                    (571, 651, 3, 0),
                                                                                                                    (694, 652, 3, 9),
                                                                                                                    (696, 653, 3, 9),
                                                                                                                    (600, 654, 3, 12),
                                                                                                                    (599, 655, 3, 12),
                                                                                                                    (668, 656, 3, 12),
                                                                                                                    (606, 657, 3, 9),
                                                                                                                    (681, 658, 3, 0),
                                                                                                                    (578, 659, 3, 12),
                                                                                                                    (678, 660, 3, 0),
                                                                                                                    (677, 661, 3, 12),
                                                                                                                    (579, 662, 3, 12),
                                                                                                                    (582, 664, 3, 12),
                                                                                                                    (581, 665, 3, 12),
                                                                                                                    (687, 670, 3, 10),
                                                                                                                    (644, 671, 3, 9),
                                                                                                                    (671, 672, 3, 9),
                                                                                                                    (673, 673, 3, 12),
                                                                                                                    (670, 674, 3, 12),
                                                                                                                    (682, 675, 3, 9),
                                                                                                                    (626, 676, 3, 12),
                                                                                                                    (617, 677, 3, 9),
                                                                                                                    (679, 678, 3, 9),
                                                                                                                    (680, 679, 3, 9),
                                                                                                                    (676, 680, 3, 9),
                                                                                                                    (625, 681, 3, 9),
                                                                                                                    (632, 682, 3, 12),
                                                                                                                    (643, 683, 3, 9),
                                                                                                                    (653, 684, 3, 9),
                                                                                                                    (605, 685, 3, 9),
                                                                                                                    (652, 686, 3, 9),
                                                                                                                    (587, 687, 3, 12),
                                                                                                                    (590, 688, 3, 12),
                                                                                                                    (650, 689, 3, 12),
                                                                                                                    (654, 690, 3, 9),
                                                                                                                    (649, 691, 3, 12),
                                                                                                                    (592, 692, 3, 9),
                                                                                                                    (642, 693, 3, 9),
                                                                                                                    (604, 694, 3, 9),
                                                                                                                    (601, 695, 3, 9),
                                                                                                                    (603, 696, 3, 9),
                                                                                                                    (586, 697, 3, 9),
                                                                                                                    (588, 699, 3, 9),
                                                                                                                    (591, 700, 3, 9),
                                                                                                                    (589, 701, 3, 12),
                                                                                                                    (674, 702, 3, 9),
                                                                                                                    (637, 703, 3, 12),
                                                                                                                    (667, 704, 3, 12),
                                                                                                                    (614, 705, 3, 9),
                                                                                                                    (657, 706, 3, 9),
                                                                                                                    (629, 707, 3, 0),
                                                                                                                    (608, 708, 3, 9),
                                                                                                                    (695, 709, 3, 0),
                                                                                                                    (691, 710, 3, 9),
                                                                                                                    (689, 711, 3, 9),
                                                                                                                    (690, 712, 3, 9),
                                                                                                                    (692, 713, 3, 9),
                                                                                                                    (688, 714, 3, 11),
                                                                                                                    (698, 715, 3, 12),
                                                                                                                    (1004, 716, 4, 16),
                                                                                                                    (1431, 717, 4, NULL),
                                                                                                                    (1116, 718, 4, 16),
                                                                                                                    (1131, 719, 4, NULL),
                                                                                                                    (1386, 720, 4, 16),
                                                                                                                    (1421, 721, 4, 16),
                                                                                                                    (1198, 722, 4, 16),
                                                                                                                    (1500, 723, 4, 16),
                                                                                                                    (1195, 724, 4, 16),
                                                                                                                    (1357, 725, 4, NULL),
                                                                                                                    (1387, 726, 4, 16),
                                                                                                                    (1479, 727, 4, NULL),
                                                                                                                    (1115, 728, 4, 16),
                                                                                                                    (1427, 729, 4, NULL),
                                                                                                                    (1390, 730, 4, 16),
                                                                                                                    (902, 731, 4, 16),
                                                                                                                    (1503, 732, 4, 14),
                                                                                                                    (1001, 733, 4, 14),
                                                                                                                    (1504, 734, 4, 14),
                                                                                                                    (1496, 735, 4, 14),
                                                                                                                    (764, 736, 4, 16),
                                                                                                                    (1491, 737, 4, NULL),
                                                                                                                    (765, 738, 4, 16),
                                                                                                                    (1493, 739, 4, NULL),
                                                                                                                    (1315, 740, 4, 14),
                                                                                                                    (732, 741, 4, 15),
                                                                                                                    (1308, 742, 4, 16),
                                                                                                                    (1178, 743, 4, 16),
                                                                                                                    (1502, 744, 4, 13),
                                                                                                                    (1372, 745, 4, 13),
                                                                                                                    (1420, 746, 4, 16),
                                                                                                                    (1477, 747, 4, 16),
                                                                                                                    (966, 748, 4, 16),
                                                                                                                    (1466, 749, 4, 16),
                                                                                                                    (1211, 750, 4, 16),
                                                                                                                    (1112, 751, 4, NULL),
                                                                                                                    (1459, 752, 4, 16),
                                                                                                                    (1395, 753, 4, NULL),
                                                                                                                    (1463, 754, 4, 13),
                                                                                                                    (1255, 755, 4, 13),
                                                                                                                    (1295, 756, 4, 14),
                                                                                                                    (1254, 757, 4, 14),
                                                                                                                    (1293, 758, 4, 13),
                                                                                                                    (1253, 759, 4, 13),
                                                                                                                    (1296, 760, 4, 16),
                                                                                                                    (1507, 761, 4, NULL),
                                                                                                                    (1392, 762, 4, 13),
                                                                                                                    (1498, 763, 4, 13),
                                                                                                                    (1381, 764, 4, 13),
                                                                                                                    (830, 765, 4, 13),
                                                                                                                    (1111, 766, 4, 13),
                                                                                                                    (1216, 767, 4, 13),
                                                                                                                    (1113, 768, 4, NULL),
                                                                                                                    (961, 769, 4, 15),
                                                                                                                    (1243, 770, 4, 13),
                                                                                                                    (1419, 771, 4, 13),
                                                                                                                    (1303, 772, 4, 16),
                                                                                                                    (1497, 773, 4, 15),
                                                                                                                    (755, 774, 4, 13),
                                                                                                                    (965, 775, 4, 13),
                                                                                                                    (703, 776, 4, 13),
                                                                                                                    (1221, 777, 4, 13),
                                                                                                                    (1385, 778, 4, NULL),
                                                                                                                    (1118, 779, 4, 16),
                                                                                                                    (1380, 780, 4, NULL),
                                                                                                                    (1117, 781, 4, 16),
                                                                                                                    (1394, 782, 4, NULL),
                                                                                                                    (1389, 783, 4, 16),
                                                                                                                    (1256, 784, 4, NULL),
                                                                                                                    (1391, 785, 4, 16),
                                                                                                                    (925, 786, 4, NULL),
                                                                                                                    (1467, 787, 4, 16),
                                                                                                                    (1328, 788, 4, 16),
                                                                                                                    (1289, 789, 4, 16),
                                                                                                                    (1129, 790, 4, NULL),
                                                                                                                    (898, 791, 4, 16),
                                                                                                                    (921, 792, 4, 16),
                                                                                                                    (1077, 793, 4, NULL),
                                                                                                                    (1094, 795, 4, 16),
                                                                                                                    (914, 797, 4, 16),
                                                                                                                    (754, 798, 4, 13),
                                                                                                                    (923, 799, 4, 13),
                                                                                                                    (891, 800, 4, 13),
                                                                                                                    (1155, 802, 4, 16),
                                                                                                                    (803, 804, 4, 13),
                                                                                                                    (1126, 805, 4, 13),
                                                                                                                    (761, 806, 4, 16),
                                                                                                                    (905, 807, 4, 16),
                                                                                                                    (758, 808, 4, 13),
                                                                                                                    (799, 809, 4, 13),
                                                                                                                    (1281, 810, 4, 13),
                                                                                                                    (1280, 811, 4, 13),
                                                                                                                    (1229, 812, 4, NULL),
                                                                                                                    (1279, 813, 4, 16),
                                                                                                                    (1275, 814, 4, 16),
                                                                                                                    (1187, 815, 4, 16),
                                                                                                                    (1213, 816, 4, 13),
                                                                                                                    (1127, 817, 4, 16),
                                                                                                                    (977, 818, 4, 16),
                                                                                                                    (1125, 819, 4, 16),
                                                                                                                    (979, 820, 4, 16),
                                                                                                                    (1124, 821, 4, 16),
                                                                                                                    (1090, 822, 4, 13),
                                                                                                                    (1085, 824, 4, 16),
                                                                                                                    (828, 826, 4, 16),
                                                                                                                    (821, 828, 4, 13),
                                                                                                                    (760, 830, 4, 16),
                                                                                                                    (1078, 831, 4, NULL),
                                                                                                                    (987, 832, 4, 16),
                                                                                                                    (1285, 834, 4, 13),
                                                                                                                    (1247, 836, 4, 13),
                                                                                                                    (1261, 838, 4, 13),
                                                                                                                    (1262, 840, 4, 13),
                                                                                                                    (769, 841, 4, 13),
                                                                                                                    (973, 842, 4, 16),
                                                                                                                    (768, 843, 4, 13),
                                                                                                                    (975, 844, 4, 13),
                                                                                                                    (1320, 845, 4, 13),
                                                                                                                    (1093, 846, 4, 16),
                                                                                                                    (1515, 847, 4, NULL),
                                                                                                                    (1177, 849, 4, 13),
                                                                                                                    (822, 850, 4, 13),
                                                                                                                    (1175, 851, 4, 13),
                                                                                                                    (824, 852, 4, 13),
                                                                                                                    (1165, 853, 4, 13),
                                                                                                                    (825, 854, 4, 13),
                                                                                                                    (1468, 855, 4, 13),
                                                                                                                    (767, 856, 4, 16),
                                                                                                                    (1319, 858, 4, 16),
                                                                                                                    (1163, 860, 4, 16),
                                                                                                                    (901, 861, 4, 16),
                                                                                                                    (810, 862, 4, 16),
                                                                                                                    (895, 863, 4, 14),
                                                                                                                    (1184, 864, 4, 15),
                                                                                                                    (1160, 865, 4, 15),
                                                                                                                    (1449, 866, 4, 16),
                                                                                                                    (1170, 867, 4, 16),
                                                                                                                    (1185, 868, 4, 15),
                                                                                                                    (1161, 869, 4, 15),
                                                                                                                    (1173, 870, 4, 14),
                                                                                                                    (820, 871, 4, 13),
                                                                                                                    (1171, 872, 4, 13),
                                                                                                                    (802, 873, 4, 13),
                                                                                                                    (1174, 874, 4, 16),
                                                                                                                    (823, 875, 4, 16),
                                                                                                                    (1075, 876, 4, 13),
                                                                                                                    (762, 877, 4, 13),
                                                                                                                    (1073, 878, 4, NULL),
                                                                                                                    (763, 879, 4, 16),
                                                                                                                    (1516, 880, 4, NULL),
                                                                                                                    (1284, 881, 4, NULL),
                                                                                                                    (1282, 883, 4, 13),
                                                                                                                    (1222, 885, 4, 16),
                                                                                                                    (1201, 887, 4, 16),
                                                                                                                    (978, 889, 4, 16),
                                                                                                                    (976, 891, 4, 13),
                                                                                                                    (1086, 893, 4, 14),
                                                                                                                    (941, 894, 4, 13),
                                                                                                                    (1398, 895, 4, 14),
                                                                                                                    (944, 896, 4, 16),
                                                                                                                    (919, 897, 4, NULL),
                                                                                                                    (1095, 898, 4, 13),
                                                                                                                    (728, 899, 4, 13),
                                                                                                                    (780, 900, 4, NULL),
                                                                                                                    (1267, 901, 4, 16),
                                                                                                                    (1097, 902, 4, NULL),
                                                                                                                    (1240, 903, 4, 16),
                                                                                                                    (949, 904, 4, 16),
                                                                                                                    (937, 906, 4, NULL),
                                                                                                                    (1070, 907, 4, 16),
                                                                                                                    (774, 908, 4, 13),
                                                                                                                    (1413, 909, 4, 13),
                                                                                                                    (946, 910, 4, 13),
                                                                                                                    (922, 911, 4, 13),
                                                                                                                    (1181, 912, 4, 13),
                                                                                                                    (1123, 913, 4, 13),
                                                                                                                    (1088, 914, 4, 13),
                                                                                                                    (994, 915, 4, 13),
                                                                                                                    (913, 916, 4, 13),
                                                                                                                    (1217, 917, 4, 13),
                                                                                                                    (1518, 919, 4, 16),
                                                                                                                    (1522, 920, 4, 16),
                                                                                                                    (1521, 921, 4, 16),
                                                                                                                    (1236, 922, 4, 13),
                                                                                                                    (1189, 923, 4, 13),
                                                                                                                    (786, 924, 4, 13),
                                                                                                                    (1212, 925, 4, 13),
                                                                                                                    (729, 926, 4, 13),
                                                                                                                    (1096, 927, 4, 13),
                                                                                                                    (1310, 928, 4, 16),
                                                                                                                    (933, 929, 4, 16),
                                                                                                                    (1238, 930, 4, 13),
                                                                                                                    (951, 931, 4, 13),
                                                                                                                    (1418, 932, 4, 16),
                                                                                                                    (1182, 933, 4, 16),
                                                                                                                    (1092, 934, 4, 13),
                                                                                                                    (779, 935, 4, 13),
                                                                                                                    (772, 936, 4, 13),
                                                                                                                    (1272, 937, 4, 13),
                                                                                                                    (1047, 938, 4, 16),
                                                                                                                    (943, 939, 4, 16),
                                                                                                                    (723, 940, 4, 13),
                                                                                                                    (773, 941, 4, 13),
                                                                                                                    (1416, 942, 4, 13),
                                                                                                                    (888, 943, 4, 13),
                                                                                                                    (1239, 944, 4, 13),
                                                                                                                    (811, 945, 4, 13),
                                                                                                                    (1237, 946, 4, 13),
                                                                                                                    (1268, 947, 4, 13),
                                                                                                                    (790, 948, 4, 16),
                                                                                                                    (1396, 949, 4, 16),
                                                                                                                    (818, 950, 4, 16),
                                                                                                                    (915, 951, 4, 16),
                                                                                                                    (1514, 952, 4, 16),
                                                                                                                    (1089, 953, 4, 16),
                                                                                                                    (1304, 954, 4, 16),
                                                                                                                    (945, 955, 4, NULL),
                                                                                                                    (1270, 956, 4, 13),
                                                                                                                    (997, 957, 4, 13),
                                                                                                                    (1404, 958, 4, 13),
                                                                                                                    (1002, 959, 4, 13),
                                                                                                                    (974, 960, 4, 13),
                                                                                                                    (1513, 961, 4, NULL),
                                                                                                                    (1180, 962, 4, 16),
                                                                                                                    (1517, 963, 4, 16),
                                                                                                                    (1162, 964, 4, 16),
                                                                                                                    (1274, 965, 4, 13),
                                                                                                                    (1172, 966, 4, 16),
                                                                                                                    (1080, 967, 4, NULL),
                                                                                                                    (1412, 969, 4, 16),
                                                                                                                    (1009, 970, 4, 13),
                                                                                                                    (1241, 971, 4, 14),
                                                                                                                    (1156, 973, 4, 16),
                                                                                                                    (1470, 974, 4, 16),
                                                                                                                    (1183, 975, 4, 13),
                                                                                                                    (1452, 976, 4, 16),
                                                                                                                    (1402, 977, 4, 16),
                                                                                                                    (1451, 978, 4, 16),
                                                                                                                    (1403, 979, 4, 14),
                                                                                                                    (1200, 980, 4, 16),
                                                                                                                    (1203, 981, 4, 16),
                                                                                                                    (1204, 982, 4, 14),
                                                                                                                    (1209, 983, 4, 13),
                                                                                                                    (1215, 984, 4, 13),
                                                                                                                    (1218, 985, 4, 13),
                                                                                                                    (1512, 986, 4, 13),
                                                                                                                    (1519, 987, 4, 14),
                                                                                                                    (1520, 988, 4, 14),
                                                                                                                    (1762, 989, 5, 20),
                                                                                                                    (2013, 990, 5, NULL),
                                                                                                                    (1849, 991, 5, 20),
                                                                                                                    (2441, 992, 5, 20),
                                                                                                                    (1857, 993, 5, 18),
                                                                                                                    (2120, 994, 5, 18),
                                                                                                                    (2542, 995, 5, 17),
                                                                                                                    (2324, 996, 5, 17),
                                                                                                                    (2538, 997, 5, 18),
                                                                                                                    (2537, 998, 5, 18),
                                                                                                                    (2436, 999, 5, 18),
                                                                                                                    (2315, 1000, 5, 18),
                                                                                                                    (2532, 1001, 5, 20),
                                                                                                                    (2531, 1002, 5, 20),
                                                                                                                    (2399, 1003, 5, 18),
                                                                                                                    (1937, 1004, 5, 18),
                                                                                                                    (2320, 1005, 5, 17),
                                                                                                                    (1940, 1006, 5, 17),
                                                                                                                    (2314, 1007, 5, NULL),
                                                                                                                    (2386, 1008, 5, 20),
                                                                                                                    (2109, 1009, 5, 17),
                                                                                                                    (2539, 1010, 5, 17),
                                                                                                                    (2530, 1011, 5, NULL),
                                                                                                                    (2529, 1012, 5, 20),
                                                                                                                    (2316, 1013, 5, 17),
                                                                                                                    (2303, 1014, 5, 17),
                                                                                                                    (2156, 1015, 5, 17),
                                                                                                                    (1763, 1016, 5, 17),
                                                                                                                    (2543, 1017, 5, NULL),
                                                                                                                    (2356, 1018, 5, NULL),
                                                                                                                    (2134, 1019, 5, NULL),
                                                                                                                    (1768, 1020, 5, 20),
                                                                                                                    (2036, 1021, 5, NULL),
                                                                                                                    (2540, 1022, 5, 20),
                                                                                                                    (2076, 1023, 5, 20),
                                                                                                                    (2101, 1024, 5, 20),
                                                                                                                    (2517, 1025, 5, 20),
                                                                                                                    (1576, 1026, 5, NULL),
                                                                                                                    (1903, 1027, 5, 20),
                                                                                                                    (2518, 1028, 5, NULL),
                                                                                                                    (2420, 1029, 5, 20),
                                                                                                                    (1769, 1030, 5, 20),
                                                                                                                    (1904, 1031, 5, 20),
                                                                                                                    (2440, 1032, 5, 20),
                                                                                                                    (2536, 1033, 5, 20),
                                                                                                                    (2535, 1034, 5, 20),
                                                                                                                    (2534, 1035, 5, 20),
                                                                                                                    (2533, 1036, 5, 20),
                                                                                                                    (2235, 1037, 5, NULL),
                                                                                                                    (1964, 1038, 5, 20),
                                                                                                                    (2560, 1039, 5, 17),
                                                                                                                    (2559, 1040, 5, 17),
                                                                                                                    (2376, 1041, 5, 17),
                                                                                                                    (1880, 1042, 5, 17),
                                                                                                                    (2367, 1043, 5, 17),
                                                                                                                    (2365, 1044, 5, 17),
                                                                                                                    (1812, 1045, 5, 20),
                                                                                                                    (2561, 1046, 5, NULL),
                                                                                                                    (2565, 1047, 5, 20),
                                                                                                                    (2566, 1048, 5, 20),
                                                                                                                    (1750, 1049, 5, 17),
                                                                                                                    (2362, 1050, 5, 17),
                                                                                                                    (2378, 1051, 5, 17),
                                                                                                                    (2282, 1052, 5, 17),
                                                                                                                    (2375, 1053, 5, NULL),
                                                                                                                    (2337, 1054, 5, 20),
                                                                                                                    (1915, 1055, 5, 20),
                                                                                                                    (2140, 1056, 5, 20),
                                                                                                                    (1918, 1057, 5, 17),
                                                                                                                    (1704, 1058, 5, 17),
                                                                                                                    (1680, 1059, 5, NULL),
                                                                                                                    (1615, 1061, 5, 17),
                                                                                                                    (2372, 1062, 5, 17),
                                                                                                                    (1678, 1063, 5, NULL),
                                                                                                                    (2562, 1064, 5, 20),
                                                                                                                    (1967, 1065, 5, 17),
                                                                                                                    (1775, 1066, 5, 17),
                                                                                                                    (1619, 1067, 5, 20),
                                                                                                                    (1810, 1068, 5, 20),
                                                                                                                    (2243, 1069, 5, NULL),
                                                                                                                    (1755, 1070, 5, 20),
                                                                                                                    (1910, 1071, 5, 17),
                                                                                                                    (2373, 1072, 5, 17),
                                                                                                                    (2047, 1073, 5, 17),
                                                                                                                    (2570, 1074, 5, 17),
                                                                                                                    (1737, 1075, 5, 17),
                                                                                                                    (1629, 1076, 5, NULL),
                                                                                                                    (1537, 1077, 5, 20),
                                                                                                                    (2383, 1078, 5, 20),
                                                                                                                    (1552, 1079, 5, 20),
                                                                                                                    (2049, 1081, 5, 20),
                                                                                                                    (2558, 1083, 5, NULL),
                                                                                                                    (2557, 1084, 5, 20),
                                                                                                                    (2019, 1085, 5, 17),
                                                                                                                    (2142, 1086, 5, 17),
                                                                                                                    (1738, 1087, 5, 17),
                                                                                                                    (1804, 1088, 5, 17),
                                                                                                                    (2300, 1089, 5, 17),
                                                                                                                    (2117, 1090, 5, 17),
                                                                                                                    (1553, 1091, 5, 20),
                                                                                                                    (1779, 1092, 5, 20),
                                                                                                                    (1753, 1093, 5, 20),
                                                                                                                    (2099, 1095, 5, NULL),
                                                                                                                    (1759, 1096, 5, 20),
                                                                                                                    (1925, 1097, 5, 20),
                                                                                                                    (2349, 1098, 5, 20),
                                                                                                                    (1916, 1099, 5, 20),
                                                                                                                    (2366, 1100, 5, 20),
                                                                                                                    (1902, 1101, 5, 17),
                                                                                                                    (2034, 1102, 5, 17),
                                                                                                                    (2340, 1103, 5, 20),
                                                                                                                    (2370, 1104, 5, 20),
                                                                                                                    (1901, 1105, 5, 17),
                                                                                                                    (2379, 1106, 5, 17),
                                                                                                                    (1899, 1107, 5, 20),
                                                                                                                    (1749, 1108, 5, 20),
                                                                                                                    (2573, 1109, 5, 20),
                                                                                                                    (2574, 1110, 5, 20),
                                                                                                                    (2338, 1111, 5, 20),
                                                                                                                    (2021, 1112, 5, 20),
                                                                                                                    (2510, 1113, 5, 17),
                                                                                                                    (2279, 1114, 5, 20),
                                                                                                                    (2348, 1115, 5, 20),
                                                                                                                    (1811, 1116, 5, 20),
                                                                                                                    (2347, 1117, 5, 17),
                                                                                                                    (1684, 1118, 5, 17),
                                                                                                                    (2339, 1119, 5, 17),
                                                                                                                    (2382, 1120, 5, 17),
                                                                                                                    (1782, 1121, 5, 20),
                                                                                                                    (1906, 1122, 5, 20),
                                                                                                                    (1900, 1123, 5, NULL),
                                                                                                                    (1748, 1124, 5, 20),
                                                                                                                    (2363, 1125, 5, 17),
                                                                                                                    (1545, 1126, 5, 17),
                                                                                                                    (1895, 1127, 5, 17),
                                                                                                                    (2125, 1128, 5, 17),
                                                                                                                    (2242, 1129, 5, 17),
                                                                                                                    (2351, 1130, 5, 17),
                                                                                                                    (2552, 1131, 5, 20),
                                                                                                                    (2551, 1132, 5, NULL),
                                                                                                                    (2352, 1133, 5, 17),
                                                                                                                    (1637, 1134, 5, 17),
                                                                                                                    (1792, 1135, 5, 20),
                                                                                                                    (1675, 1136, 5, 20),
                                                                                                                    (2255, 1137, 5, 20),
                                                                                                                    (2321, 1138, 5, 20),
                                                                                                                    (1608, 1139, 5, 17),
                                                                                                                    (2353, 1140, 5, 17),
                                                                                                                    (2556, 1141, 5, 20),
                                                                                                                    (2555, 1142, 5, 20),
                                                                                                                    (2259, 1143, 5, 20),
                                                                                                                    (2211, 1144, 5, 19),
                                                                                                                    (2550, 1145, 5, 20),
                                                                                                                    (2549, 1146, 5, 20),
                                                                                                                    (2269, 1147, 5, 17),
                                                                                                                    (1654, 1148, 5, 17),
                                                                                                                    (2499, 1149, 5, 17),
                                                                                                                    (2261, 1150, 5, 17),
                                                                                                                    (2252, 1151, 5, 17),
                                                                                                                    (1893, 1152, 5, 17),
                                                                                                                    (2288, 1153, 5, 17),
                                                                                                                    (2273, 1154, 5, 17),
                                                                                                                    (1824, 1155, 5, 17),
                                                                                                                    (2063, 1156, 5, 17),
                                                                                                                    (1578, 1157, 5, 17),
                                                                                                                    (2541, 1158, 5, 17),
                                                                                                                    (2509, 1159, 5, 20),
                                                                                                                    (1976, 1160, 5, 20),
                                                                                                                    (2214, 1161, 5, 20),
                                                                                                                    (2241, 1162, 5, 20),
                                                                                                                    (1936, 1163, 5, 17),
                                                                                                                    (2126, 1164, 5, 17),
                                                                                                                    (1969, 1165, 5, 17),
                                                                                                                    (2471, 1166, 5, 17),
                                                                                                                    (2503, 1167, 5, 17),
                                                                                                                    (2415, 1168, 5, 17),
                                                                                                                    (1818, 1169, 5, 17),
                                                                                                                    (1898, 1170, 5, 17),
                                                                                                                    (2354, 1171, 5, 20),
                                                                                                                    (1974, 1172, 5, 20),
                                                                                                                    (1934, 1173, 5, 17),
                                                                                                                    (2240, 1174, 5, 17),
                                                                                                                    (2031, 1175, 5, 17),
                                                                                                                    (1674, 1176, 5, 17),
                                                                                                                    (2392, 1177, 5, NULL),
                                                                                                                    (2143, 1178, 5, 20),
                                                                                                                    (2254, 1179, 5, 17),
                                                                                                                    (2275, 1180, 5, 17),
                                                                                                                    (1864, 1181, 5, 20),
                                                                                                                    (2319, 1182, 5, 20),
                                                                                                                    (2312, 1183, 5, 17),
                                                                                                                    (2229, 1184, 5, 17),
                                                                                                                    (2251, 1185, 5, NULL),
                                                                                                                    (2350, 1186, 5, 20),
                                                                                                                    (2270, 1188, 5, 20),
                                                                                                                    (2397, 1189, 5, 17),
                                                                                                                    (2306, 1190, 5, 17),
                                                                                                                    (2393, 1191, 5, 17),
                                                                                                                    (2390, 1193, 5, 20),
                                                                                                                    (1659, 1194, 5, 20),
                                                                                                                    (2394, 1195, 5, 17),
                                                                                                                    (2257, 1196, 5, 17),
                                                                                                                    (2204, 1197, 5, 20),
                                                                                                                    (1894, 1198, 5, 20),
                                                                                                                    (2508, 1199, 5, 20),
                                                                                                                    (2064, 1200, 5, 20),
                                                                                                                    (2395, 1201, 5, 17),
                                                                                                                    (2129, 1202, 5, 17),
                                                                                                                    (2554, 1203, 5, 20),
                                                                                                                    (2553, 1204, 5, NULL),
                                                                                                                    (2189, 1205, 5, 17),
                                                                                                                    (2516, 1206, 5, 17),
                                                                                                                    (2127, 1208, 5, 17),
                                                                                                                    (2292, 1209, 5, 17),
                                                                                                                    (2417, 1210, 5, 17),
                                                                                                                    (1874, 1211, 5, 20),
                                                                                                                    (1802, 1212, 5, 20),
                                                                                                                    (2002, 1213, 5, 20),
                                                                                                                    (1913, 1214, 5, 20),
                                                                                                                    (2000, 1215, 5, 20),
                                                                                                                    (2548, 1216, 5, NULL),
                                                                                                                    (2276, 1217, 5, 20),
                                                                                                                    (1793, 1218, 5, 20),
                                                                                                                    (1785, 1219, 5, 19),
                                                                                                                    (1843, 1220, 5, 19),
                                                                                                                    (1783, 1221, 5, 20),
                                                                                                                    (1956, 1222, 5, 20),
                                                                                                                    (2274, 1223, 5, 19),
                                                                                                                    (1611, 1224, 5, 19),
                                                                                                                    (1892, 1225, 5, 17),
                                                                                                                    (1794, 1226, 5, 17),
                                                                                                                    (2547, 1227, 5, 17),
                                                                                                                    (2177, 1228, 5, 17),
                                                                                                                    (1929, 1229, 5, 20),
                                                                                                                    (2209, 1230, 5, 20),
                                                                                                                    (1706, 1231, 5, 17),
                                                                                                                    (2112, 1232, 5, 17),
                                                                                                                    (1672, 1233, 5, 20),
                                                                                                                    (1825, 1234, 5, 20),
                                                                                                                    (1719, 1235, 5, 20),
                                                                                                                    (1897, 1236, 5, 20),
                                                                                                                    (1923, 1237, 5, 17),
                                                                                                                    (2213, 1238, 5, 18),
                                                                                                                    (1924, 1239, 5, 18),
                                                                                                                    (2391, 1241, 5, 17),
                                                                                                                    (1671, 1242, 5, 20),
                                                                                                                    (1795, 1243, 5, 20),
                                                                                                                    (2317, 1244, 5, 17),
                                                                                                                    (1842, 1245, 5, 17),
                                                                                                                    (2544, 1247, 5, 20),
                                                                                                                    (1716, 1248, 5, 17),
                                                                                                                    (1931, 1249, 5, 17),
                                                                                                                    (1717, 1250, 5, 17),
                                                                                                                    (2248, 1251, 5, 17),
                                                                                                                    (1819, 1252, 5, 17),
                                                                                                                    (2179, 1253, 5, 17),
                                                                                                                    (1714, 1254, 5, 17),
                                                                                                                    (1620, 1255, 5, 18),
                                                                                                                    (1993, 1256, 5, 17),
                                                                                                                    (2198, 1257, 5, 17),
                                                                                                                    (1772, 1258, 5, 17),
                                                                                                                    (1865, 1259, 5, 17),
                                                                                                                    (1921, 1260, 5, 20),
                                                                                                                    (1801, 1261, 5, 20),
                                                                                                                    (2405, 1262, 5, 20),
                                                                                                                    (2567, 1263, 5, 20),
                                                                                                                    (2563, 1264, 5, 19),
                                                                                                                    (2062, 1266, 5, 17),
                                                                                                                    (1803, 1267, 5, 17),
                                                                                                                    (2454, 1268, 5, 17),
                                                                                                                    (1965, 1269, 5, 20),
                                                                                                                    (2037, 1270, 5, 20),
                                                                                                                    (2192, 1271, 5, 20),
                                                                                                                    (2196, 1272, 5, 20),
                                                                                                                    (2564, 1273, 5, 19),
                                                                                                                    (1870, 1274, 5, 17),
                                                                                                                    (2545, 1275, 5, 20),
                                                                                                                    (2045, 1276, 5, 17),
                                                                                                                    (1952, 1277, 5, 20),
                                                                                                                    (2323, 1278, 5, 17),
                                                                                                                    (2523, 1279, 5, 20),
                                                                                                                    (2525, 1280, 5, 20),
                                                                                                                    (3422, 1281, 6, 23),
                                                                                                                    (3235, 1282, 6, 22),
                                                                                                                    (3103, 1283, 6, 23),
                                                                                                                    (2594, 1284, 6, 23),
                                                                                                                    (3420, 1285, 6, 23),
                                                                                                                    (3583, 1286, 6, 24),
                                                                                                                    (2843, 1287, 6, 21),
                                                                                                                    (2651, 1288, 6, 21),
                                                                                                                    (3338, 1289, 6, 24),
                                                                                                                    (2984, 1290, 6, NULL),
                                                                                                                    (3209, 1291, 6, 21),
                                                                                                                    (3137, 1292, 6, 21),
                                                                                                                    (3303, 1293, 6, 24),
                                                                                                                    (2831, 1294, 6, 24),
                                                                                                                    (2783, 1295, 6, 21),
                                                                                                                    (3204, 1296, 6, 21),
                                                                                                                    (3300, 1297, 6, 21),
                                                                                                                    (2767, 1298, 6, 21),
                                                                                                                    (3517, 1299, 6, 21),
                                                                                                                    (2595, 1300, 6, 21),
                                                                                                                    (3516, 1301, 6, 24),
                                                                                                                    (3508, 1302, 6, 24),
                                                                                                                    (3502, 1303, 6, 21),
                                                                                                                    (3474, 1304, 6, 21),
                                                                                                                    (3749, 1305, 6, 24),
                                                                                                                    (2837, 1306, 6, 24),
                                                                                                                    (3514, 1307, 6, 24),
                                                                                                                    (3892, 1308, 6, NULL),
                                                                                                                    (3417, 1309, 6, NULL),
                                                                                                                    (3885, 1310, 6, 24),
                                                                                                                    (3159, 1311, 6, 21),
                                                                                                                    (3207, 1312, 6, 21),
                                                                                                                    (3845, 1313, 6, 21),
                                                                                                                    (3320, 1314, 6, 21),
                                                                                                                    (3156, 1315, 6, 21),
                                                                                                                    (3670, 1316, 6, 21),
                                                                                                                    (3123, 1317, 6, 24),
                                                                                                                    (3783, 1318, 6, 24),
                                                                                                                    (3696, 1319, 6, 21),
                                                                                                                    (2841, 1320, 6, 21),
                                                                                                                    (3881, 1321, 6, 21),
                                                                                                                    (3880, 1322, 6, 21),
                                                                                                                    (3416, 1323, 6, 21),
                                                                                                                    (2696, 1324, 6, 21),
                                                                                                                    (3578, 1325, 6, NULL),
                                                                                                                    (2909, 1326, 6, 24),
                                                                                                                    (2910, 1327, 6, 21),
                                                                                                                    (2836, 1328, 6, 21),
                                                                                                                    (3127, 1329, 6, 21),
                                                                                                                    (3295, 1330, 6, 21),
                                                                                                                    (3160, 1331, 6, 24),
                                                                                                                    (3669, 1332, 6, 24),
                                                                                                                    (3853, 1333, 6, 21),
                                                                                                                    (3475, 1334, 6, 21),
                                                                                                                    (3113, 1335, 6, 21),
                                                                                                                    (2842, 1336, 6, 21),
                                                                                                                    (2587, 1337, 6, 21),
                                                                                                                    (3875, 1338, 6, 21),
                                                                                                                    (3157, 1339, 6, 24),
                                                                                                                    (3874, 1340, 6, 24),
                                                                                                                    (2586, 1341, 6, 21),
                                                                                                                    (2835, 1342, 6, 21),
                                                                                                                    (3478, 1343, 6, 24),
                                                                                                                    (3294, 1344, 6, 24),
                                                                                                                    (3162, 1345, 6, 24),
                                                                                                                    (3667, 1346, 6, 24),
                                                                                                                    (3748, 1347, 6, 21),
                                                                                                                    (3656, 1348, 6, 21),
                                                                                                                    (2634, 1349, 6, 21),
                                                                                                                    (2844, 1350, 6, 21),
                                                                                                                    (2916, 1351, 6, 24),
                                                                                                                    (3893, 1352, 6, NULL),
                                                                                                                    (3879, 1353, 6, 21),
                                                                                                                    (3065, 1354, 6, 21),
                                                                                                                    (3176, 1355, 6, 24),
                                                                                                                    (2708, 1356, 6, 24),
                                                                                                                    (3511, 1357, 6, 24),
                                                                                                                    (2838, 1358, 6, 24),
                                                                                                                    (2698, 1359, 6, 21),
                                                                                                                    (3323, 1360, 6, 21),
                                                                                                                    (3887, 1361, 6, 21),
                                                                                                                    (3888, 1362, 6, 21),
                                                                                                                    (3595, 1363, 6, 24),
                                                                                                                    (3134, 1364, 6, 24),
                                                                                                                    (3175, 1365, 6, 23),
                                                                                                                    (2912, 1367, 6, 24),
                                                                                                                    (3584, 1368, 6, 24),
                                                                                                                    (2915, 1369, 6, 21),
                                                                                                                    (3657, 1370, 6, 21),
                                                                                                                    (2701, 1371, 6, NULL),
                                                                                                                    (2692, 1372, 6, 24),
                                                                                                                    (3598, 1373, 6, 21),
                                                                                                                    (2986, 1374, 6, 21),
                                                                                                                    (3883, 1375, 6, NULL),
                                                                                                                    (3884, 1376, 6, NULL),
                                                                                                                    (3587, 1377, 6, 21),
                                                                                                                    (2758, 1378, 6, 21),
                                                                                                                    (3849, 1379, 6, 21),
                                                                                                                    (3237, 1380, 6, 21),
                                                                                                                    (3039, 1381, 6, NULL),
                                                                                                                    (3889, 1383, 6, 24),
                                                                                                                    (3582, 1384, 6, 24),
                                                                                                                    (3890, 1385, 6, 24),
                                                                                                                    (3606, 1386, 6, 24),
                                                                                                                    (3593, 1387, 6, 21),
                                                                                                                    (2700, 1388, 6, 21),
                                                                                                                    (3645, 1389, 6, 21),
                                                                                                                    (2985, 1390, 6, 21),
                                                                                                                    (3689, 1391, 6, 24),
                                                                                                                    (3854, 1392, 6, NULL),
                                                                                                                    (3844, 1393, 6, 24),
                                                                                                                    (2757, 1394, 6, 23),
                                                                                                                    (3037, 1395, 6, NULL),
                                                                                                                    (2596, 1396, 6, 24),
                                                                                                                    (3917, 1397, 6, 21),
                                                                                                                    (3208, 1398, 6, 21),
                                                                                                                    (3354, 1399, 6, 21),
                                                                                                                    (2727, 1400, 6, 21),
                                                                                                                    (3116, 1401, 6, 21),
                                                                                                                    (3523, 1402, 6, 21),
                                                                                                                    (3644, 1403, 6, 21),
                                                                                                                    (3660, 1404, 6, 21),
                                                                                                                    (3641, 1405, 6, 24),
                                                                                                                    (2940, 1406, 6, 24),
                                                                                                                    (2913, 1407, 6, 21),
                                                                                                                    (3336, 1408, 6, 21),
                                                                                                                    (3850, 1409, 6, 24),
                                                                                                                    (3891, 1410, 6, 24),
                                                                                                                    (3038, 1411, 6, 21),
                                                                                                                    (3213, 1412, 6, 21),
                                                                                                                    (3592, 1413, 6, 24),
                                                                                                                    (2806, 1414, 6, 24),
                                                                                                                    (2597, 1415, 6, 24),
                                                                                                                    (3580, 1416, 6, 24),
                                                                                                                    (3192, 1417, 6, 24),
                                                                                                                    (2981, 1418, 6, NULL),
                                                                                                                    (2725, 1419, 6, 24),
                                                                                                                    (3135, 1420, 6, 24),
                                                                                                                    (3384, 1421, 6, 24),
                                                                                                                    (2996, 1422, 6, NULL),
                                                                                                                    (2894, 1423, 6, 24),
                                                                                                                    (2624, 1424, 6, 24),
                                                                                                                    (3189, 1425, 6, 24),
                                                                                                                    (3658, 1426, 6, 24),
                                                                                                                    (2726, 1427, 6, 21),
                                                                                                                    (2694, 1428, 6, 21),
                                                                                                                    (3188, 1429, 6, 24),
                                                                                                                    (2798, 1430, 6, 23),
                                                                                                                    (3497, 1431, 6, 21),
                                                                                                                    (3851, 1432, 6, 21),
                                                                                                                    (2799, 1433, 6, 21),
                                                                                                                    (2729, 1434, 6, 21),
                                                                                                                    (3358, 1435, 6, NULL),
                                                                                                                    (2755, 1437, 6, 24),
                                                                                                                    (3066, 1438, 6, 23),
                                                                                                                    (3352, 1440, 6, 23),
                                                                                                                    (3877, 1442, 6, 24),
                                                                                                                    (2982, 1443, 6, 24),
                                                                                                                    (3351, 1444, 6, 23),
                                                                                                                    (2995, 1445, 6, NULL),
                                                                                                                    (3334, 1446, 6, 24),
                                                                                                                    (3130, 1447, 6, 24),
                                                                                                                    (3327, 1448, 6, 24),
                                                                                                                    (2732, 1449, 6, 24),
                                                                                                                    (3214, 1450, 6, 24),
                                                                                                                    (3318, 1451, 6, 21),
                                                                                                                    (3322, 1452, 6, 21),
                                                                                                                    (3234, 1453, 6, 24),
                                                                                                                    (2787, 1454, 6, 24),
                                                                                                                    (3082, 1455, 6, 22),
                                                                                                                    (3321, 1456, 6, 22),
                                                                                                                    (2797, 1457, 6, 22),
                                                                                                                    (3668, 1458, 6, 22),
                                                                                                                    (3673, 1460, 6, 24),
                                                                                                                    (3651, 1461, 6, 21),
                                                                                                                    (3504, 1462, 6, 21),
                                                                                                                    (3070, 1463, 6, 21),
                                                                                                                    (3692, 1464, 6, 21),
                                                                                                                    (2741, 1465, 6, 24),
                                                                                                                    (3886, 1466, 6, 24),
                                                                                                                    (3882, 1467, 6, 24),
                                                                                                                    (2941, 1468, 6, 24),
                                                                                                                    (3873, 1469, 6, NULL),
                                                                                                                    (3339, 1470, 6, 24),
                                                                                                                    (3106, 1471, 6, 21),
                                                                                                                    (2881, 1472, 6, 21),
                                                                                                                    (3349, 1473, 6, NULL),
                                                                                                                    (2948, 1474, 6, 24),
                                                                                                                    (3909, 1475, 6, 24),
                                                                                                                    (3910, 1476, 6, NULL),
                                                                                                                    (2958, 1477, 6, 24),
                                                                                                                    (3828, 1478, 6, NULL),
                                                                                                                    (3486, 1479, 6, 21),
                                                                                                                    (3273, 1480, 6, 21),
                                                                                                                    (3957, 1481, 6, NULL),
                                                                                                                    (3462, 1482, 6, 24),
                                                                                                                    (3051, 1483, 6, 24),
                                                                                                                    (3924, 1484, 6, 24),
                                                                                                                    (3949, 1485, 6, NULL),
                                                                                                                    (3948, 1486, 6, NULL),
                                                                                                                    (3947, 1487, 6, 24),
                                                                                                                    (3069, 1489, 6, 24),
                                                                                                                    (3855, 1490, 6, 24),
                                                                                                                    (3178, 1491, 6, NULL),
                                                                                                                    (3560, 1492, 6, NULL),
                                                                                                                    (2660, 1493, 6, 21),
                                                                                                                    (3758, 1494, 6, 21),
                                                                                                                    (3901, 1495, 6, NULL),
                                                                                                                    (3900, 1496, 6, 24),
                                                                                                                    (3953, 1497, 6, 24),
                                                                                                                    (3954, 1498, 6, 24),
                                                                                                                    (2662, 1499, 6, 21),
                                                                                                                    (3521, 1500, 6, 21),
                                                                                                                    (3623, 1501, 6, NULL),
                                                                                                                    (3080, 1502, 6, 24),
                                                                                                                    (3955, 1503, 6, 24),
                                                                                                                    (3956, 1504, 6, 24),
                                                                                                                    (3180, 1505, 6, NULL),
                                                                                                                    (3675, 1506, 6, 24),
                                                                                                                    (2667, 1507, 6, 24),
                                                                                                                    (3950, 1508, 6, NULL),
                                                                                                                    (3650, 1510, 6, NULL),
                                                                                                                    (3796, 1511, 6, 24),
                                                                                                                    (2960, 1512, 6, 24),
                                                                                                                    (2743, 1513, 6, NULL),
                                                                                                                    (3107, 1514, 6, 24),
                                                                                                                    (2717, 1515, 6, 24),
                                                                                                                    (3348, 1516, 6, 24),
                                                                                                                    (3916, 1517, 6, 24),
                                                                                                                    (3915, 1518, 6, 24),
                                                                                                                    (3626, 1519, 6, 24),
                                                                                                                    (3859, 1520, 6, 24),
                                                                                                                    (2879, 1522, 6, 24),
                                                                                                                    (3798, 1523, 6, 24),
                                                                                                                    (3444, 1524, 6, 23),
                                                                                                                    (3589, 1525, 6, 24),
                                                                                                                    (3182, 1526, 6, 24),
                                                                                                                    (3608, 1527, 6, 24),
                                                                                                                    (2957, 1528, 6, 24),
                                                                                                                    (2967, 1529, 6, 21),
                                                                                                                    (3894, 1530, 6, 21),
                                                                                                                    (3861, 1532, 6, 24),
                                                                                                                    (2679, 1533, 6, 24),
                                                                                                                    (3619, 1534, 6, NULL),
                                                                                                                    (3902, 1535, 6, 24),
                                                                                                                    (3903, 1536, 6, 24),
                                                                                                                    (3896, 1537, 6, 21),
                                                                                                                    (3897, 1538, 6, 21),
                                                                                                                    (2961, 1539, 6, 24),
                                                                                                                    (3350, 1540, 6, 24),
                                                                                                                    (3908, 1541, 6, NULL),
                                                                                                                    (3907, 1542, 6, 24),
                                                                                                                    (3923, 1543, 6, 24),
                                                                                                                    (3922, 1544, 6, 24),
                                                                                                                    (3952, 1545, 6, 24),
                                                                                                                    (3951, 1546, 6, NULL),
                                                                                                                    (2681, 1547, 6, 21),
                                                                                                                    (3006, 1548, 6, 21),
                                                                                                                    (3190, 1549, 6, 21),
                                                                                                                    (3109, 1550, 6, 21),
                                                                                                                    (3556, 1551, 6, 24),
                                                                                                                    (3797, 1553, 6, 21),
                                                                                                                    (3524, 1554, 6, 21),
                                                                                                                    (2719, 1555, 6, 24),
                                                                                                                    (2965, 1556, 6, 24),
                                                                                                                    (3643, 1557, 6, 24),
                                                                                                                    (3490, 1558, 6, 24),
                                                                                                                    (3755, 1559, 6, 23),
                                                                                                                    (3576, 1560, 6, NULL),
                                                                                                                    (2747, 1561, 6, NULL),
                                                                                                                    (2873, 1562, 6, 24),
                                                                                                                    (2955, 1563, 6, 24),
                                                                                                                    (3575, 1564, 6, NULL),
                                                                                                                    (3574, 1565, 6, 21),
                                                                                                                    (3110, 1566, 6, 21),
                                                                                                                    (3396, 1567, 6, 24),
                                                                                                                    (3441, 1568, 6, 24),
                                                                                                                    (3716, 1569, 6, 21),
                                                                                                                    (3858, 1570, 6, 21),
                                                                                                                    (3463, 1571, 6, 24),
                                                                                                                    (2944, 1572, 6, 24),
                                                                                                                    (3757, 1574, 6, 21),
                                                                                                                    (3092, 1575, 6, 24),
                                                                                                                    (3187, 1576, 6, 24),
                                                                                                                    (3284, 1577, 6, 23),
                                                                                                                    (2680, 1578, 6, 24),
                                                                                                                    (3622, 1579, 6, 21),
                                                                                                                    (3064, 1580, 6, 21),
                                                                                                                    (3921, 1581, 6, 24),
                                                                                                                    (3920, 1582, 6, 24),
                                                                                                                    (3564, 1583, 6, NULL),
                                                                                                                    (3004, 1584, 6, 24),
                                                                                                                    (3905, 1585, 6, 24),
                                                                                                                    (3904, 1586, 6, NULL),
                                                                                                                    (3914, 1587, 6, NULL),
                                                                                                                    (3913, 1588, 6, 24),
                                                                                                                    (3547, 1589, 6, NULL),
                                                                                                                    (3372, 1590, 6, 24),
                                                                                                                    (3179, 1591, 6, 24),
                                                                                                                    (3184, 1592, 6, 24),
                                                                                                                    (3898, 1593, 6, 24),
                                                                                                                    (3899, 1594, 6, 24),
                                                                                                                    (3373, 1596, 6, 24),
                                                                                                                    (3272, 1597, 6, 24),
                                                                                                                    (2943, 1598, 6, 24),
                                                                                                                    (3559, 1599, 6, 21),
                                                                                                                    (3183, 1600, 6, 21),
                                                                                                                    (3912, 1601, 6, NULL),
                                                                                                                    (3911, 1602, 6, 24),
                                                                                                                    (3918, 1603, 6, 24),
                                                                                                                    (3919, 1604, 6, 24),
                                                                                                                    (3617, 1605, 6, 24),
                                                                                                                    (3377, 1606, 6, 24),
                                                                                                                    (2639, 1607, 6, 24),
                                                                                                                    (3200, 1608, 6, 24),
                                                                                                                    (3940, 1609, 6, NULL),
                                                                                                                    (3939, 1610, 6, 24),
                                                                                                                    (2636, 1611, 6, NULL),
                                                                                                                    (3735, 1612, 6, 24),
                                                                                                                    (2937, 1613, 6, 23),
                                                                                                                    (3841, 1614, 6, 23),
                                                                                                                    (3719, 1615, 6, 24),
                                                                                                                    (3742, 1616, 6, 24),
                                                                                                                    (2938, 1617, 6, 24),
                                                                                                                    (3562, 1618, 6, 24),
                                                                                                                    (3932, 1619, 6, 22),
                                                                                                                    (3933, 1620, 6, 22),
                                                                                                                    (3665, 1622, 6, 21),
                                                                                                                    (3739, 1623, 6, 22),
                                                                                                                    (3741, 1624, 6, 22),
                                                                                                                    (3780, 1625, 6, 21),
                                                                                                                    (3737, 1627, 6, 21),
                                                                                                                    (3440, 1628, 6, 21),
                                                                                                                    (3382, 1629, 6, 22),
                                                                                                                    (2642, 1630, 6, 21),
                                                                                                                    (3702, 1631, 6, 24),
                                                                                                                    (3230, 1632, 6, 24),
                                                                                                                    (3429, 1633, 6, 21),
                                                                                                                    (3931, 1635, 6, 23),
                                                                                                                    (3567, 1636, 6, 23),
                                                                                                                    (2914, 1637, 6, 22),
                                                                                                                    (2640, 1638, 6, 22),
                                                                                                                    (3927, 1639, 6, 24),
                                                                                                                    (3928, 1640, 6, 24),
                                                                                                                    (3704, 1641, 6, 21),
                                                                                                                    (3674, 1642, 6, 21),
                                                                                                                    (3557, 1643, 6, 24),
                                                                                                                    (3754, 1644, 6, 24),
                                                                                                                    (2977, 1645, 6, 24),
                                                                                                                    (3561, 1646, 6, 24),
                                                                                                                    (3199, 1647, 6, 24),
                                                                                                                    (3936, 1649, 6, 24),
                                                                                                                    (3616, 1650, 6, 24),
                                                                                                                    (2964, 1651, 6, 24),
                                                                                                                    (3255, 1652, 6, 24),
                                                                                                                    (3701, 1653, 6, 22),
                                                                                                                    (3262, 1654, 6, 22),
                                                                                                                    (3376, 1655, 6, 21),
                                                                                                                    (3775, 1656, 6, 21),
                                                                                                                    (3381, 1657, 6, 24),
                                                                                                                    (3558, 1659, 6, 24),
                                                                                                                    (3442, 1660, 6, 24),
                                                                                                                    (3431, 1661, 6, 21),
                                                                                                                    (3830, 1662, 6, 21),
                                                                                                                    (3782, 1663, 6, NULL),
                                                                                                                    (3743, 1664, 6, 24),
                                                                                                                    (2920, 1665, 6, 24),
                                                                                                                    (3740, 1666, 6, 24),
                                                                                                                    (3219, 1667, 6, 24),
                                                                                                                    (3779, 1668, 6, NULL),
                                                                                                                    (3550, 1669, 6, 21),
                                                                                                                    (2864, 1670, 6, 21),
                                                                                                                    (3528, 1671, 6, 24),
                                                                                                                    (2871, 1672, 6, NULL),
                                                                                                                    (3551, 1673, 6, 24),
                                                                                                                    (3778, 1674, 6, NULL),
                                                                                                                    (2684, 1675, 6, 21),
                                                                                                                    (3638, 1676, 6, 21),
                                                                                                                    (3926, 1677, 6, 24),
                                                                                                                    (3925, 1678, 6, 24),
                                                                                                                    (3935, 1679, 6, 24),
                                                                                                                    (3934, 1680, 6, 24),
                                                                                                                    (2683, 1681, 6, 24),
                                                                                                                    (2933, 1682, 6, 24),
                                                                                                                    (3958, 1683, 6, 24),
                                                                                                                    (3941, 1684, 6, NULL),
                                                                                                                    (3761, 1685, 6, 24),
                                                                                                                    (3662, 1686, 6, NULL),
                                                                                                                    (3777, 1687, 6, 24),
                                                                                                                    (3663, 1688, 6, NULL),
                                                                                                                    (2936, 1690, 6, 24),
                                                                                                                    (2926, 1691, 6, 24),
                                                                                                                    (3629, 1692, 6, 24),
                                                                                                                    (3563, 1693, 6, 22),
                                                                                                                    (3229, 1694, 6, 22),
                                                                                                                    (3252, 1695, 6, 24),
                                                                                                                    (3231, 1696, 6, 24),
                                                                                                                    (3698, 1697, 6, NULL),
                                                                                                                    (3732, 1698, 6, 24),
                                                                                                                    (3577, 1699, 6, NULL),
                                                                                                                    (3258, 1700, 6, 24),
                                                                                                                    (3942, 1701, 6, 24),
                                                                                                                    (3943, 1702, 6, 24),
                                                                                                                    (3876, 1703, 6, 24),
                                                                                                                    (3501, 1704, 6, 24),
                                                                                                                    (3512, 1705, 6, 24),
                                                                                                                    (3878, 1706, 6, 24),
                                                                                                                    (3539, 1707, 6, 24),
                                                                                                                    (2754, 1708, 6, 24),
                                                                                                                    (3566, 1710, 6, 21),
                                                                                                                    (3945, 1711, 6, NULL),
                                                                                                                    (3944, 1712, 6, 24),
                                                                                                                    (3929, 1713, 6, 24),
                                                                                                                    (3930, 1715, 6, 24),
                                                                                                                    (3937, 1716, 6, 24),
                                                                                                                    (3759, 1717, 6, 21),
                                                                                                                    (3906, 1718, 6, NULL),
                                                                                                                    (3707, 1721, 6, NULL),
                                                                                                                    (3708, 1722, 6, NULL),
                                                                                                                    (3771, 1723, 6, 24),
                                                                                                                    (3765, 1724, 6, 24),
                                                                                                                    (3762, 1725, 6, 21),
                                                                                                                    (3872, 1726, 6, 24),
                                                                                                                    (3946, 1727, 6, 24),
                                                                                                                    (3865, 1728, 6, 21),
                                                                                                                    (3870, 1729, 6, 21),
                                                                                                                    (3987, 1731, 7, 25),
                                                                                                                    (4949, 1732, 7, 25),
                                                                                                                    (3985, 1733, 7, 25),
                                                                                                                    (4230, 1734, 7, 25),
                                                                                                                    (3988, 1735, 7, 28),
                                                                                                                    (4982, 1736, 7, 26),
                                                                                                                    (4662, 1737, 7, 28),
                                                                                                                    (4155, 1738, 7, 28),
                                                                                                                    (4659, 1739, 7, 28),
                                                                                                                    (4151, 1740, 7, 28),
                                                                                                                    (5080, 1741, 7, NULL),
                                                                                                                    (5081, 1742, 7, 28),
                                                                                                                    (4095, 1743, 7, 28),
                                                                                                                    (4176, 1744, 7, 28),
                                                                                                                    (4170, 1745, 7, 25),
                                                                                                                    (4211, 1746, 7, 25),
                                                                                                                    (4660, 1747, 7, 25),
                                                                                                                    (4572, 1748, 7, 25),
                                                                                                                    (3966, 1749, 7, 25),
                                                                                                                    (4346, 1750, 7, 25),
                                                                                                                    (4644, 1751, 7, 25),
                                                                                                                    (4101, 1752, 7, 25),
                                                                                                                    (4611, 1753, 7, 25),
                                                                                                                    (4734, 1754, 7, 25),
                                                                                                                    (4237, 1755, 7, 28),
                                                                                                                    (4210, 1756, 7, NULL),
                                                                                                                    (4222, 1757, 7, 25),
                                                                                                                    (4980, 1758, 7, 25),
                                                                                                                    (5075, 1759, 7, 28),
                                                                                                                    (4233, 1760, 7, 28),
                                                                                                                    (4761, 1761, 7, 25),
                                                                                                                    (4732, 1762, 7, 25),
                                                                                                                    (4207, 1763, 7, 25),
                                                                                                                    (4979, 1764, 7, 25),
                                                                                                                    (4205, 1765, 7, NULL),
                                                                                                                    (4496, 1766, 7, 28),
                                                                                                                    (4542, 1767, 7, 28),
                                                                                                                    (5088, 1768, 7, 28),
                                                                                                                    (5082, 1769, 7, 25),
                                                                                                                    (3960, 1770, 7, 25),
                                                                                                                    (3983, 1771, 7, NULL),
                                                                                                                    (4923, 1772, 7, 28),
                                                                                                                    (5091, 1773, 7, 28),
                                                                                                                    (5092, 1774, 7, 28),
                                                                                                                    (4219, 1775, 7, 25),
                                                                                                                    (4698, 1776, 7, 25),
                                                                                                                    (4238, 1777, 7, 28),
                                                                                                                    (4175, 1778, 7, 28),
                                                                                                                    (4373, 1779, 7, 25),
                                                                                                                    (4709, 1780, 7, 25),
                                                                                                                    (4168, 1781, 7, 25),
                                                                                                                    (3972, 1782, 7, 25),
                                                                                                                    (4481, 1783, 7, 28),
                                                                                                                    (4015, 1784, 7, 28),
                                                                                                                    (4099, 1785, 7, 25),
                                                                                                                    (4922, 1786, 7, 25),
                                                                                                                    (4214, 1787, 7, 25),
                                                                                                                    (4712, 1788, 7, 25),
                                                                                                                    (4166, 1789, 7, 25),
                                                                                                                    (4242, 1790, 7, 25),
                                                                                                                    (4169, 1791, 7, 28),
                                                                                                                    (4692, 1792, 7, 28),
                                                                                                                    (4847, 1793, 7, 28),
                                                                                                                    (4355, 1794, 7, NULL),
                                                                                                                    (3984, 1795, 7, NULL),
                                                                                                                    (4924, 1796, 7, 28),
                                                                                                                    (4268, 1797, 7, 25),
                                                                                                                    (4399, 1798, 7, 25),
                                                                                                                    (4933, 1799, 7, 28),
                                                                                                                    (4177, 1800, 7, 28),
                                                                                                                    (4765, 1801, 7, 28),
                                                                                                                    (4043, 1802, 7, 28),
                                                                                                                    (5087, 1803, 7, 28),
                                                                                                                    (4416, 1804, 7, 28),
                                                                                                                    (4654, 1805, 7, 25),
                                                                                                                    (4017, 1806, 7, 25),
                                                                                                                    (4848, 1807, 7, 28),
                                                                                                                    (4430, 1808, 7, NULL),
                                                                                                                    (5076, 1809, 7, 25),
                                                                                                                    (4737, 1810, 7, 25),
                                                                                                                    (5077, 1811, 7, 28),
                                                                                                                    (4178, 1812, 7, 28),
                                                                                                                    (4107, 1813, 7, 25),
                                                                                                                    (4352, 1814, 7, 25),
                                                                                                                    (4861, 1815, 7, 26),
                                                                                                                    (4405, 1816, 7, NULL),
                                                                                                                    (5089, 1817, 7, 25),
                                                                                                                    (4348, 1818, 7, 25),
                                                                                                                    (3968, 1819, 7, 25),
                                                                                                                    (4410, 1820, 7, 25),
                                                                                                                    (4769, 1821, 7, 28),
                                                                                                                    (4042, 1822, 7, 28),
                                                                                                                    (4220, 1823, 7, 25),
                                                                                                                    (4726, 1824, 7, 25),
                                                                                                                    (5079, 1825, 7, 25),
                                                                                                                    (5078, 1826, 7, 25),
                                                                                                                    (4865, 1827, 7, 25),
                                                                                                                    (4796, 1828, 7, 25),
                                                                                                                    (5085, 1829, 7, 28),
                                                                                                                    (5086, 1830, 7, 28),
                                                                                                                    (4935, 1831, 7, 28),
                                                                                                                    (4022, 1832, 7, 28),
                                                                                                                    (4953, 1833, 7, 28),
                                                                                                                    (5016, 1834, 7, 28),
                                                                                                                    (5074, 1835, 7, 27),
                                                                                                                    (4832, 1836, 7, 27),
                                                                                                                    (4000, 1837, 7, NULL),
                                                                                                                    (4314, 1838, 7, 28),
                                                                                                                    (3999, 1839, 7, NULL),
                                                                                                                    (4392, 1840, 7, NULL),
                                                                                                                    (4954, 1841, 7, 28),
                                                                                                                    (4831, 1842, 7, NULL),
                                                                                                                    (4786, 1843, 7, 28),
                                                                                                                    (4309, 1844, 7, NULL),
                                                                                                                    (4833, 1845, 7, 28),
                                                                                                                    (4266, 1846, 7, 28),
                                                                                                                    (3971, 1847, 7, 25),
                                                                                                                    (4435, 1848, 7, 25),
                                                                                                                    (4227, 1849, 7, 28),
                                                                                                                    (4262, 1850, 7, 28),
                                                                                                                    (4244, 1851, 7, 26),
                                                                                                                    (4273, 1852, 7, 26),
                                                                                                                    (4224, 1853, 7, 28),
                                                                                                                    (3970, 1854, 7, 28),
                                                                                                                    (4926, 1855, 7, 28),
                                                                                                                    (5090, 1856, 7, 28),
                                                                                                                    (4431, 1857, 7, 25),
                                                                                                                    (4501, 1858, 7, 25),
                                                                                                                    (4322, 1859, 7, 28),
                                                                                                                    (4845, 1860, 7, 28),
                                                                                                                    (4921, 1861, 7, 25),
                                                                                                                    (4506, 1862, 7, 25),
                                                                                                                    (5093, 1863, 7, 25),
                                                                                                                    (4461, 1864, 7, 25),
                                                                                                                    (4174, 1865, 7, 28),
                                                                                                                    (4930, 1866, 7, NULL),
                                                                                                                    (4597, 1867, 7, 28),
                                                                                                                    (4232, 1868, 7, 28),
                                                                                                                    (4596, 1869, 7, 25),
                                                                                                                    (4578, 1870, 7, 25),
                                                                                                                    (5129, 1871, 7, 28),
                                                                                                                    (5128, 1872, 7, 28),
                                                                                                                    (5117, 1873, 7, 27),
                                                                                                                    (5116, 1874, 7, 27),
                                                                                                                    (4639, 1875, 7, NULL),
                                                                                                                    (4673, 1876, 7, 28),
                                                                                                                    (5107, 1877, 7, NULL),
                                                                                                                    (4782, 1878, 7, NULL),
                                                                                                                    (4536, 1879, 7, 28),
                                                                                                                    (4904, 1880, 7, 27),
                                                                                                                    (4005, 1881, 7, 25),
                                                                                                                    (4680, 1882, 7, 25),
                                                                                                                    (4800, 1883, 7, 28),
                                                                                                                    (4987, 1884, 7, 28),
                                                                                                                    (4907, 1885, 7, 27),
                                                                                                                    (4740, 1886, 7, 27),
                                                                                                                    (4423, 1887, 7, 26),
                                                                                                                    (5004, 1888, 7, 26),
                                                                                                                    (4779, 1889, 7, 26),
                                                                                                                    (4580, 1890, 7, 26),
                                                                                                                    (4345, 1891, 7, 28),
                                                                                                                    (4641, 1892, 7, 28),
                                                                                                                    (4422, 1893, 7, 28),
                                                                                                                    (4941, 1894, 7, NULL),
                                                                                                                    (4798, 1895, 7, 28),
                                                                                                                    (4784, 1896, 7, NULL),
                                                                                                                    (5110, 1897, 7, 28),
                                                                                                                    (4635, 1898, 7, 28),
                                                                                                                    (5125, 1899, 7, 28),
                                                                                                                    (5124, 1900, 7, NULL),
                                                                                                                    (4808, 1901, 7, 28),
                                                                                                                    (4179, 1902, 7, 28),
                                                                                                                    (4550, 1903, 7, 28),
                                                                                                                    (4006, 1904, 7, 28),
                                                                                                                    (4783, 1905, 7, 27),
                                                                                                                    (4803, 1906, 7, 27),
                                                                                                                    (4940, 1907, 7, NULL),
                                                                                                                    (4556, 1908, 7, 28),
                                                                                                                    (5007, 1909, 7, 28),
                                                                                                                    (5020, 1910, 7, 28),
                                                                                                                    (4447, 1911, 7, 28),
                                                                                                                    (4547, 1912, 7, 26),
                                                                                                                    (4785, 1913, 7, NULL),
                                                                                                                    (4344, 1914, 7, 28),
                                                                                                                    (4938, 1915, 7, NULL),
                                                                                                                    (4044, 1916, 7, 28),
                                                                                                                    (4677, 1917, 7, 28),
                                                                                                                    (5115, 1918, 7, NULL),
                                                                                                                    (4397, 1919, 7, 28),
                                                                                                                    (4780, 1920, 7, 28),
                                                                                                                    (4009, 1921, 7, 28),
                                                                                                                    (4827, 1922, 7, NULL),
                                                                                                                    (5030, 1923, 7, 26),
                                                                                                                    (4515, 1924, 7, 26),
                                                                                                                    (5109, 1925, 7, 28),
                                                                                                                    (5108, 1926, 7, 28),
                                                                                                                    (5031, 1927, 7, 28),
                                                                                                                    (4801, 1928, 7, 28),
                                                                                                                    (4521, 1929, 7, 25),
                                                                                                                    (4828, 1930, 7, 25),
                                                                                                                    (4546, 1931, 7, 26),
                                                                                                                    (4389, 1932, 7, 26),
                                                                                                                    (4559, 1933, 7, 26),
                                                                                                                    (5005, 1934, 7, 26),
                                                                                                                    (4301, 1935, 7, 26),
                                                                                                                    (4482, 1936, 7, 26),
                                                                                                                    (4519, 1937, 7, 25),
                                                                                                                    (4003, 1938, 7, 25),
                                                                                                                    (4586, 1939, 7, 28),
                                                                                                                    (4196, 1940, 7, 28),
                                                                                                                    (4085, 1941, 7, 28),
                                                                                                                    (4939, 1942, 7, 28),
                                                                                                                    (5106, 1943, 7, 28),
                                                                                                                    (5105, 1944, 7, 28),
                                                                                                                    (4010, 1945, 7, 26),
                                                                                                                    (4902, 1946, 7, 27),
                                                                                                                    (4716, 1947, 7, 28),
                                                                                                                    (4349, 1948, 7, 28),
                                                                                                                    (4313, 1949, 7, 25),
                                                                                                                    (5045, 1950, 7, 25),
                                                                                                                    (4706, 1951, 7, 25),
                                                                                                                    (4462, 1952, 7, 25),
                                                                                                                    (4215, 1953, 7, 28),
                                                                                                                    (5104, 1954, 7, 28),
                                                                                                                    (5131, 1955, 7, 28),
                                                                                                                    (5130, 1956, 7, 28),
                                                                                                                    (4039, 1957, 7, 28),
                                                                                                                    (4735, 1958, 7, 28),
                                                                                                                    (4031, 1959, 7, 28),
                                                                                                                    (4522, 1960, 7, 28),
                                                                                                                    (4728, 1961, 7, 28),
                                                                                                                    (4731, 1962, 7, 28),
                                                                                                                    (4724, 1963, 7, 28),
                                                                                                                    (4136, 1964, 7, 28),
                                                                                                                    (4041, 1965, 7, 28),
                                                                                                                    (4135, 1966, 7, 28),
                                                                                                                    (4036, 1967, 7, 28),
                                                                                                                    (4402, 1968, 7, 28),
                                                                                                                    (4027, 1969, 7, 28),
                                                                                                                    (4864, 1970, 7, 28),
                                                                                                                    (4358, 1971, 7, 25),
                                                                                                                    (4880, 1972, 7, 25),
                                                                                                                    (4415, 1973, 7, 25),
                                                                                                                    (4092, 1974, 7, 25),
                                                                                                                    (4464, 1975, 7, 25),
                                                                                                                    (4474, 1976, 7, 25),
                                                                                                                    (4463, 1977, 7, 28),
                                                                                                                    (4109, 1978, 7, NULL),
                                                                                                                    (5049, 1979, 7, 25),
                                                                                                                    (4934, 1980, 7, 25),
                                                                                                                    (4969, 1981, 7, 28),
                                                                                                                    (4656, 1982, 7, 28),
                                                                                                                    (4153, 1983, 7, 28),
                                                                                                                    (4932, 1984, 7, 28),
                                                                                                                    (5068, 1985, 7, 28),
                                                                                                                    (5073, 1986, 7, 28),
                                                                                                                    (5046, 1987, 7, 28),
                                                                                                                    (4931, 1988, 7, 28),
                                                                                                                    (4685, 1989, 7, 28),
                                                                                                                    (4856, 1990, 7, 28),
                                                                                                                    (4627, 1991, 7, 28),
                                                                                                                    (4657, 1992, 7, 28),
                                                                                                                    (4618, 1993, 7, 28),
                                                                                                                    (4977, 1994, 7, NULL),
                                                                                                                    (4684, 1995, 7, 28),
                                                                                                                    (4158, 1996, 7, 25),
                                                                                                                    (5041, 1997, 7, NULL),
                                                                                                                    (4854, 1998, 7, 28),
                                                                                                                    (5060, 1999, 7, 25),
                                                                                                                    (4950, 2000, 7, 25),
                                                                                                                    (5067, 2001, 7, 28),
                                                                                                                    (4689, 2002, 7, 28),
                                                                                                                    (5122, 2003, 7, 25),
                                                                                                                    (5123, 2004, 7, 25),
                                                                                                                    (4687, 2005, 7, 28),
                                                                                                                    (4630, 2006, 7, NULL),
                                                                                                                    (5100, 2007, 7, NULL),
                                                                                                                    (4849, 2008, 7, 28),
                                                                                                                    (5054, 2009, 7, NULL),
                                                                                                                    (4719, 2010, 7, 28),
                                                                                                                    (4789, 2011, 7, 25),
                                                                                                                    (4710, 2012, 7, 25),
                                                                                                                    (5102, 2013, 7, 25),
                                                                                                                    (5103, 2014, 7, 25),
                                                                                                                    (5047, 2015, 7, NULL),
                                                                                                                    (4455, 2016, 7, 28),
                                                                                                                    (5069, 2017, 7, 28),
                                                                                                                    (4942, 2018, 7, 28),
                                                                                                                    (4362, 2019, 7, 25),
                                                                                                                    (4663, 2020, 7, 25),
                                                                                                                    (5099, 2021, 7, 28),
                                                                                                                    (5098, 2022, 7, 28),
                                                                                                                    (4951, 2023, 7, 28),
                                                                                                                    (4694, 2024, 7, 28),
                                                                                                                    (5120, 2025, 7, 28),
                                                                                                                    (5121, 2026, 7, NULL),
                                                                                                                    (4460, 2027, 7, 28),
                                                                                                                    (4764, 2028, 7, 28),
                                                                                                                    (4563, 2029, 7, NULL),
                                                                                                                    (5032, 2030, 7, NULL),
                                                                                                                    (4894, 2031, 7, 25),
                                                                                                                    (4156, 2032, 7, 25),
                                                                                                                    (4571, 2033, 7, NULL),
                                                                                                                    (4768, 2034, 7, 28),
                                                                                                                    (4068, 2035, 7, 25),
                                                                                                                    (4619, 2036, 7, 25),
                                                                                                                    (4736, 2037, 7, 28),
                                                                                                                    (4573, 2038, 7, NULL),
                                                                                                                    (4729, 2039, 7, 28),
                                                                                                                    (4007, 2040, 7, NULL),
                                                                                                                    (5097, 2041, 7, 28),
                                                                                                                    (5096, 2042, 7, 28),
                                                                                                                    (4500, 2043, 7, 28),
                                                                                                                    (4187, 2044, 7, NULL),
                                                                                                                    (4889, 2045, 7, 27),
                                                                                                                    (4913, 2046, 7, 27),
                                                                                                                    (4887, 2047, 7, 25),
                                                                                                                    (4060, 2048, 7, 25),
                                                                                                                    (4456, 2049, 7, 28),
                                                                                                                    (4696, 2050, 7, 28),
                                                                                                                    (4137, 2051, 7, 25),
                                                                                                                    (4700, 2052, 7, 25),
                                                                                                                    (4138, 2053, 7, 28),
                                                                                                                    (4704, 2054, 7, 28),
                                                                                                                    (4059, 2055, 7, 28),
                                                                                                                    (4714, 2056, 7, 28),
                                                                                                                    (4065, 2057, 7, 28),
                                                                                                                    (4717, 2058, 7, 28),
                                                                                                                    (5118, 2059, 7, NULL),
                                                                                                                    (5119, 2060, 7, 28),
                                                                                                                    (4722, 2061, 7, 28),
                                                                                                                    (4209, 2062, 7, NULL),
                                                                                                                    (5095, 2063, 7, 25),
                                                                                                                    (5094, 2064, 7, 25),
                                                                                                                    (4975, 2065, 7, NULL),
                                                                                                                    (4216, 2066, 7, NULL),
                                                                                                                    (4396, 2067, 7, 28),
                                                                                                                    (4147, 2068, 7, 28),
                                                                                                                    (5101, 2069, 7, 28),
                                                                                                                    (4686, 2070, 7, 28),
                                                                                                                    (4459, 2071, 7, 28),
                                                                                                                    (4730, 2072, 7, 28),
                                                                                                                    (5061, 2073, 7, NULL),
                                                                                                                    (4420, 2074, 7, 28),
                                                                                                                    (5042, 2075, 7, 25),
                                                                                                                    (4053, 2076, 7, 25),
                                                                                                                    (4351, 2077, 7, 28),
                                                                                                                    (4549, 2078, 7, 28),
                                                                                                                    (4834, 2079, 7, 25),
                                                                                                                    (4720, 2080, 7, 25),
                                                                                                                    (4019, 2081, 7, 25),
                                                                                                                    (4221, 2082, 7, 25),
                                                                                                                    (4429, 2083, 7, 25),
                                                                                                                    (4008, 2084, 7, 25),
                                                                                                                    (4919, 2085, 7, 28),
                                                                                                                    (4721, 2086, 7, 28),
                                                                                                                    (4947, 2087, 7, NULL),
                                                                                                                    (4955, 2088, 7, NULL),
                                                                                                                    (4989, 2089, 7, 28),
                                                                                                                    (4212, 2090, 7, 28),
                                                                                                                    (4048, 2091, 7, 28),
                                                                                                                    (4234, 2092, 7, 28),
                                                                                                                    (4046, 2093, 7, 25),
                                                                                                                    (4235, 2094, 7, 25),
                                                                                                                    (4081, 2095, 7, 28),
                                                                                                                    (4857, 2096, 7, 28),
                                                                                                                    (4408, 2097, 7, 25),
                                                                                                                    (4264, 2098, 7, 25),
                                                                                                                    (5126, 2099, 7, 28),
                                                                                                                    (5127, 2100, 7, NULL),
                                                                                                                    (4434, 2101, 7, 28),
                                                                                                                    (4204, 2102, 7, 28),
                                                                                                                    (5113, 2103, 7, 27),
                                                                                                                    (5114, 2104, 7, 27),
                                                                                                                    (4952, 2105, 7, 28),
                                                                                                                    (5083, 2106, 7, 28),
                                                                                                                    (4787, 2107, 7, 28),
                                                                                                                    (5084, 2108, 7, 28),
                                                                                                                    (5050, 2109, 7, 25),
                                                                                                                    (4738, 2110, 7, 25),
                                                                                                                    (4707, 2111, 7, 28),
                                                                                                                    (4613, 2112, 7, NULL),
                                                                                                                    (4882, 2113, 7, 28),
                                                                                                                    (5009, 2114, 7, 27),
                                                                                                                    (5112, 2115, 7, NULL),
                                                                                                                    (5111, 2116, 7, 28),
                                                                                                                    (5152, 2118, 8, 32),
                                                                                                                    (5334, 2119, 8, 32),
                                                                                                                    (5154, 2120, 8, NULL),
                                                                                                                    (5412, 2121, 8, 32),
                                                                                                                    (6016, 2122, 8, 32),
                                                                                                                    (5337, 2123, 8, 32),
                                                                                                                    (5437, 2124, 8, NULL),
                                                                                                                    (5303, 2125, 8, 32),
                                                                                                                    (6032, 2126, 8, 32),
                                                                                                                    (5307, 2127, 8, 32),
                                                                                                                    (5454, 2128, 8, 29),
                                                                                                                    (5301, 2129, 8, 29),
                                                                                                                    (5453, 2130, 8, 29),
                                                                                                                    (5410, 2131, 8, 29),
                                                                                                                    (5647, 2133, 8, NULL),
                                                                                                                    (5645, 2134, 8, NULL),
                                                                                                                    (5853, 2135, 8, 32),
                                                                                                                    (6017, 2136, 8, 29),
                                                                                                                    (5306, 2137, 8, 29),
                                                                                                                    (5455, 2138, 8, 32),
                                                                                                                    (5858, 2139, 8, 32),
                                                                                                                    (5155, 2140, 8, NULL),
                                                                                                                    (5880, 2141, 8, 30),
                                                                                                                    (5143, 2143, 8, 32),
                                                                                                                    (5347, 2144, 8, 32),
                                                                                                                    (6102, 2145, 8, NULL),
                                                                                                                    (6101, 2146, 8, 32),
                                                                                                                    (5328, 2147, 8, 29),
                                                                                                                    (6086, 2148, 8, NULL),
                                                                                                                    (6085, 2149, 8, 32),
                                                                                                                    (5159, 2150, 8, NULL),
                                                                                                                    (5483, 2151, 8, 32),
                                                                                                                    (5166, 2152, 8, 30),
                                                                                                                    (5493, 2153, 8, 30),
                                                                                                                    (5168, 2154, 8, 29),
                                                                                                                    (5497, 2155, 8, 29),
                                                                                                                    (5146, 2156, 8, 32),
                                                                                                                    (5346, 2157, 8, 32),
                                                                                                                    (6058, 2158, 8, 32),
                                                                                                                    (6059, 2159, 8, 32),
                                                                                                                    (6077, 2160, 8, 29),
                                                                                                                    (6076, 2161, 8, 29),
                                                                                                                    (5397, 2162, 8, 32),
                                                                                                                    (6078, 2163, 8, 32),
                                                                                                                    (6081, 2164, 8, 32),
                                                                                                                    (6082, 2165, 8, NULL),
                                                                                                                    (6066, 2166, 8, 32),
                                                                                                                    (5612, 2167, 8, 32),
                                                                                                                    (5567, 2168, 8, 32),
                                                                                                                    (5861, 2169, 8, 32),
                                                                                                                    (6080, 2170, 8, 32),
                                                                                                                    (6079, 2171, 8, 32),
                                                                                                                    (5607, 2173, 8, NULL),
                                                                                                                    (5187, 2174, 8, NULL),
                                                                                                                    (5815, 2175, 8, 32),
                                                                                                                    (5186, 2176, 8, 29),
                                                                                                                    (5636, 2177, 8, 29),
                                                                                                                    (5185, 2178, 8, 29),
                                                                                                                    (6067, 2179, 8, 29),
                                                                                                                    (5501, 2180, 8, 32),
                                                                                                                    (5854, 2181, 8, 32),
                                                                                                                    (5503, 2182, 8, 32),
                                                                                                                    (5191, 2183, 8, 32),
                                                                                                                    (5457, 2184, 8, 29),
                                                                                                                    (5697, 2185, 8, 29),
                                                                                                                    (5715, 2186, 8, 32),
                                                                                                                    (5875, 2187, 8, 32),
                                                                                                                    (6088, 2188, 8, 32),
                                                                                                                    (6087, 2189, 8, NULL),
                                                                                                                    (5324, 2190, 8, 32),
                                                                                                                    (5495, 2191, 8, NULL),
                                                                                                                    (5311, 2192, 8, 32),
                                                                                                                    (6042, 2193, 8, NULL),
                                                                                                                    (6092, 2194, 8, 30),
                                                                                                                    (6091, 2195, 8, 30),
                                                                                                                    (5899, 2196, 8, NULL),
                                                                                                                    (5510, 2197, 8, 32),
                                                                                                                    (5896, 2198, 8, 32),
                                                                                                                    (5987, 2200, 8, 30),
                                                                                                                    (6094, 2201, 8, 31),
                                                                                                                    (6093, 2202, 8, 31),
                                                                                                                    (5349, 2204, 8, 32),
                                                                                                                    (5407, 2209, 8, 29),
                                                                                                                    (5986, 2210, 8, 29),
                                                                                                                    (5389, 2211, 8, 32),
                                                                                                                    (5674, 2212, 8, 32),
                                                                                                                    (5218, 2213, 8, 29),
                                                                                                                    (5711, 2214, 8, 29),
                                                                                                                    (5725, 2215, 8, 32),
                                                                                                                    (5724, 2216, 8, 32),
                                                                                                                    (5312, 2217, 8, 32),
                                                                                                                    (5220, 2219, 8, NULL),
                                                                                                                    (5640, 2220, 8, 32),
                                                                                                                    (5937, 2221, 8, 32),
                                                                                                                    (5355, 2222, 8, 32),
                                                                                                                    (6089, 2223, 8, 32),
                                                                                                                    (6090, 2224, 8, 32),
                                                                                                                    (5222, 2225, 8, 30),
                                                                                                                    (5957, 2226, 8, 30),
                                                                                                                    (5948, 2227, 8, 30),
                                                                                                                    (6043, 2228, 8, NULL),
                                                                                                                    (5401, 2229, 8, 30),
                                                                                                                    (5706, 2230, 8, 30),
                                                                                                                    (6099, 2231, 8, 32),
                                                                                                                    (6100, 2232, 8, 32),
                                                                                                                    (6095, 2233, 8, 32),
                                                                                                                    (5527, 2234, 8, 32),
                                                                                                                    (5726, 2235, 8, 32),
                                                                                                                    (5915, 2236, 8, NULL),
                                                                                                                    (5739, 2237, 8, 32),
                                                                                                                    (5912, 2238, 8, NULL),
                                                                                                                    (6041, 2239, 8, NULL),
                                                                                                                    (5935, 2240, 8, 31),
                                                                                                                    (6007, 2241, 8, 32),
                                                                                                                    (5269, 2242, 8, 32),
                                                                                                                    (5892, 2243, 8, NULL),
                                                                                                                    (5394, 2244, 8, 32),
                                                                                                                    (6006, 2245, 8, 32),
                                                                                                                    (5427, 2246, 8, NULL),
                                                                                                                    (6037, 2247, 8, 32),
                                                                                                                    (5665, 2248, 8, 32),
                                                                                                                    (5793, 2251, 8, 32),
                                                                                                                    (5310, 2252, 8, 32),
                                                                                                                    (5996, 2253, 8, 32),
                                                                                                                    (5675, 2254, 8, 32),
                                                                                                                    (5958, 2255, 8, 30),
                                                                                                                    (5264, 2257, 8, 32),
                                                                                                                    (5676, 2258, 8, 32),
                                                                                                                    (5456, 2260, 8, 30),
                                                                                                                    (5809, 2261, 8, 29),
                                                                                                                    (5944, 2262, 8, 29),
                                                                                                                    (5299, 2264, 8, NULL),
                                                                                                                    (5789, 2266, 8, 32),
                                                                                                                    (5799, 2267, 8, 32),
                                                                                                                    (5340, 2268, 8, 32),
                                                                                                                    (5327, 2269, 8, 32),
                                                                                                                    (5403, 2270, 8, 32),
                                                                                                                    (5537, 2271, 8, 32),
                                                                                                                    (5254, 2272, 8, NULL),
                                                                                                                    (5488, 2273, 8, 29),
                                                                                                                    (5258, 2274, 8, 29),
                                                                                                                    (5555, 2275, 8, 29),
                                                                                                                    (5496, 2276, 8, 29),
                                                                                                                    (5885, 2277, 8, 29),
                                                                                                                    (5409, 2278, 8, 29),
                                                                                                                    (5235, 2279, 8, 32),
                                                                                                                    (5278, 2280, 8, 32),
                                                                                                                    (5884, 2281, 8, 29),
                                                                                                                    (5590, 2282, 8, 29),
                                                                                                                    (5750, 2283, 8, 29),
                                                                                                                    (5255, 2284, 8, 29),
                                                                                                                    (5619, 2285, 8, 32),
                                                                                                                    (5442, 2286, 8, 32),
                                                                                                                    (5648, 2287, 8, 29),
                                                                                                                    (5917, 2288, 8, 29),
                                                                                                                    (5704, 2289, 8, 29),
                                                                                                                    (5755, 2291, 8, 32),
                                                                                                                    (5822, 2292, 8, 32),
                                                                                                                    (5530, 2293, 8, 32),
                                                                                                                    (5940, 2294, 8, 32),
                                                                                                                    (5886, 2295, 8, 29),
                                                                                                                    (5866, 2297, 8, 29),
                                                                                                                    (5790, 2298, 8, 29),
                                                                                                                    (5532, 2299, 8, 29),
                                                                                                                    (5921, 2300, 8, 29),
                                                                                                                    (5864, 2301, 8, 29),
                                                                                                                    (5787, 2302, 8, 29),
                                                                                                                    (5545, 2303, 8, 32),
                                                                                                                    (5297, 2304, 8, 32),
                                                                                                                    (6050, 2305, 8, 32),
                                                                                                                    (6049, 2306, 8, 32),
                                                                                                                    (5657, 2307, 8, 29),
                                                                                                                    (5268, 2308, 8, 29),
                                                                                                                    (5839, 2309, 8, NULL),
                                                                                                                    (5459, 2310, 8, 32),
                                                                                                                    (5295, 2311, 8, 29),
                                                                                                                    (5523, 2312, 8, 29),
                                                                                                                    (6084, 2313, 8, 30),
                                                                                                                    (6083, 2314, 8, 30),
                                                                                                                    (5836, 2315, 8, NULL),
                                                                                                                    (5923, 2316, 8, 32),
                                                                                                                    (5751, 2317, 8, 32),
                                                                                                                    (5266, 2318, 8, 32),
                                                                                                                    (5703, 2319, 8, 32),
                                                                                                                    (5823, 2320, 8, 32),
                                                                                                                    (5226, 2321, 8, 29),
                                                                                                                    (5420, 2322, 8, 29),
                                                                                                                    (5294, 2323, 8, 29),
                                                                                                                    (5267, 2324, 8, 29),
                                                                                                                    (5942, 2326, 8, 29),
                                                                                                                    (5479, 2328, 8, 29),
                                                                                                                    (5653, 2330, 8, 29),
                                                                                                                    (5626, 2331, 8, 32),
                                                                                                                    (5569, 2332, 8, 32),
                                                                                                                    (5655, 2333, 8, 29),
                                                                                                                    (5845, 2334, 8, 29),
                                                                                                                    (5534, 2335, 8, 32),
                                                                                                                    (5298, 2338, 8, 32),
                                                                                                                    (5575, 2339, 8, 29),
                                                                                                                    (5792, 2340, 8, 29),
                                                                                                                    (5556, 2341, 8, 32),
                                                                                                                    (5779, 2342, 8, 32),
                                                                                                                    (5490, 2343, 8, 29),
                                                                                                                    (5847, 2345, 8, 29),
                                                                                                                    (5589, 2346, 8, 32),
                                                                                                                    (5830, 2347, 8, 32),
                                                                                                                    (5500, 2348, 8, 29),
                                                                                                                    (5661, 2349, 8, 29),
                                                                                                                    (5302, 2350, 8, 32),
                                                                                                                    (5863, 2351, 8, 32),
                                                                                                                    (5308, 2352, 8, 32),
                                                                                                                    (5829, 2353, 8, 32),
                                                                                                                    (5478, 2354, 8, 29),
                                                                                                                    (5846, 2355, 8, 29),
                                                                                                                    (5852, 2356, 8, NULL),
                                                                                                                    (5314, 2357, 8, 32),
                                                                                                                    (5476, 2358, 8, 29),
                                                                                                                    (5844, 2359, 8, 29),
                                                                                                                    (5475, 2360, 8, NULL),
                                                                                                                    (5666, 2361, 8, NULL),
                                                                                                                    (5855, 2362, 8, NULL),
                                                                                                                    (5797, 2363, 8, 32),
                                                                                                                    (5499, 2364, 8, 32),
                                                                                                                    (5865, 2366, 8, 29),
                                                                                                                    (5452, 2367, 8, 29),
                                                                                                                    (5508, 2368, 8, 29),
                                                                                                                    (5596, 2369, 8, 29),
                                                                                                                    (5828, 2370, 8, 29),
                                                                                                                    (5618, 2371, 8, 32),
                                                                                                                    (5976, 2372, 8, 32),
                                                                                                                    (5993, 2373, 8, 32),
                                                                                                                    (5549, 2374, 8, 32),
                                                                                                                    (5639, 2376, 8, 32),
                                                                                                                    (5430, 2377, 8, 29),
                                                                                                                    (5506, 2378, 8, 29),
                                                                                                                    (5435, 2379, 8, 32),
                                                                                                                    (5341, 2382, 8, NULL),
                                                                                                                    (6073, 2383, 8, 32),
                                                                                                                    (5400, 2384, 8, 29),
                                                                                                                    (5201, 2385, 8, 29),
                                                                                                                    (5563, 2386, 8, 29),
                                                                                                                    (5561, 2387, 8, 29),
                                                                                                                    (5960, 2388, 8, 32),
                                                                                                                    (6031, 2389, 8, 32),
                                                                                                                    (6065, 2390, 8, NULL),
                                                                                                                    (6064, 2391, 8, 32),
                                                                                                                    (5954, 2392, 8, 32),
                                                                                                                    (5654, 2393, 8, NULL),
                                                                                                                    (5732, 2394, 8, 32),
                                                                                                                    (5197, 2395, 8, NULL),
                                                                                                                    (5835, 2396, 8, 32),
                                                                                                                    (5736, 2397, 8, 32),
                                                                                                                    (5871, 2398, 8, 32),
                                                                                                                    (5767, 2399, 8, 32),
                                                                                                                    (5705, 2400, 8, 32),
                                                                                                                    (6075, 2401, 8, 32),
                                                                                                                    (5428, 2403, 8, 29),
                                                                                                                    (5517, 2404, 8, 29),
                                                                                                                    (5447, 2405, 8, 32),
                                                                                                                    (5259, 2406, 8, 32),
                                                                                                                    (5840, 2407, 8, 32),
                                                                                                                    (5805, 2409, 8, 29),
                                                                                                                    (5635, 2410, 8, 32),
                                                                                                                    (6014, 2411, 8, 29),
                                                                                                                    (5643, 2412, 8, 29),
                                                                                                                    (5713, 2413, 8, 32),
                                                                                                                    (6069, 2415, 8, 32),
                                                                                                                    (6068, 2416, 8, 32),
                                                                                                                    (5834, 2417, 8, 32),
                                                                                                                    (5745, 2418, 8, 32),
                                                                                                                    (5978, 2419, 8, 32),
                                                                                                                    (5240, 2420, 8, 32),
                                                                                                                    (5445, 2421, 8, 32),
                                                                                                                    (5372, 2422, 8, NULL),
                                                                                                                    (5432, 2423, 8, 29),
                                                                                                                    (5425, 2424, 8, 29),
                                                                                                                    (5482, 2425, 8, 32),
                                                                                                                    (5652, 2426, 8, 32),
                                                                                                                    (5734, 2427, 8, 29),
                                                                                                                    (6010, 2429, 8, 32),
                                                                                                                    (5995, 2430, 8, NULL),
                                                                                                                    (5802, 2431, 8, 32),
                                                                                                                    (5953, 2433, 8, 32),
                                                                                                                    (5448, 2435, 8, 32),
                                                                                                                    (5621, 2436, 8, 32),
                                                                                                                    (5330, 2437, 8, 29),
                                                                                                                    (5547, 2438, 8, 29),
                                                                                                                    (5421, 2440, 8, 29),
                                                                                                                    (6011, 2441, 8, 32),
                                                                                                                    (6005, 2442, 8, 32),
                                                                                                                    (5321, 2443, 8, NULL),
                                                                                                                    (6054, 2444, 8, 32),
                                                                                                                    (6053, 2445, 8, 32),
                                                                                                                    (6072, 2446, 8, 31),
                                                                                                                    (6060, 2447, 8, 32),
                                                                                                                    (6061, 2448, 8, NULL),
                                                                                                                    (6056, 2449, 8, 32),
                                                                                                                    (6055, 2450, 8, 32),
                                                                                                                    (5320, 2451, 8, NULL),
                                                                                                                    (5279, 2452, 8, 32),
                                                                                                                    (5709, 2453, 8, 32),
                                                                                                                    (5832, 2455, 8, 32),
                                                                                                                    (5902, 2456, 8, NULL),
                                                                                                                    (6063, 2457, 8, 32),
                                                                                                                    (6062, 2458, 8, 32),
                                                                                                                    (6051, 2459, 8, 31),
                                                                                                                    (6052, 2460, 8, 31),
                                                                                                                    (5436, 2461, 8, NULL),
                                                                                                                    (5381, 2462, 8, 32),
                                                                                                                    (5710, 2465, 8, 32),
                                                                                                                    (5449, 2466, 8, 32),
                                                                                                                    (5753, 2467, 8, NULL),
                                                                                                                    (5359, 2468, 8, 29),
                                                                                                                    (6071, 2469, 8, 32),
                                                                                                                    (6070, 2470, 8, 32),
                                                                                                                    (5634, 2471, 8, 32),
                                                                                                                    (5281, 2472, 8, 32),
                                                                                                                    (5722, 2473, 8, NULL),
                                                                                                                    (5841, 2474, 8, 32),
                                                                                                                    (5687, 2475, 8, 32),
                                                                                                                    (5451, 2476, 8, 32),
                                                                                                                    (5633, 2477, 8, 31),
                                                                                                                    (5280, 2478, 8, 31),
                                                                                                                    (5415, 2479, 8, 32),
                                                                                                                    (5419, 2480, 8, 29),
                                                                                                                    (5904, 2481, 8, 29),
                                                                                                                    (5897, 2482, 8, 29),
                                                                                                                    (5760, 2483, 8, 30),
                                                                                                                    (5551, 2484, 8, NULL),
                                                                                                                    (6003, 2485, 8, 30),
                                                                                                                    (5906, 2486, 8, 29),
                                                                                                                    (5791, 2487, 8, 29),
                                                                                                                    (5931, 2488, 8, 32),
                                                                                                                    (5481, 2489, 8, 31),
                                                                                                                    (5509, 2490, 8, 32),
                                                                                                                    (5873, 2492, 8, 29),
                                                                                                                    (5924, 2493, 8, 32),
                                                                                                                    (5641, 2494, 8, 29),
                                                                                                                    (5860, 2495, 8, 29),
                                                                                                                    (5614, 2496, 8, 32),
                                                                                                                    (5564, 2497, 8, 32),
                                                                                                                    (5570, 2498, 8, 32),
                                                                                                                    (5552, 2499, 8, 31),
                                                                                                                    (5576, 2500, 8, 32),
                                                                                                                    (5862, 2501, 8, 32),
                                                                                                                    (6096, 2502, 8, 31),
                                                                                                                    (6097, 2503, 8, 32),
                                                                                                                    (6021, 2504, 8, NULL),
                                                                                                                    (5544, 2505, 8, 32),
                                                                                                                    (6039, 2506, 8, 32),
                                                                                                                    (5631, 2508, 8, 32),
                                                                                                                    (5689, 2512, 8, 32),
                                                                                                                    (5577, 2513, 8, 32),
                                                                                                                    (5550, 2514, 8, 29),
                                                                                                                    (5616, 2515, 8, 29),
                                                                                                                    (5627, 2516, 8, 29),
                                                                                                                    (5756, 2517, 8, 29),
                                                                                                                    (5851, 2518, 8, NULL),
                                                                                                                    (5988, 2519, 8, 32),
                                                                                                                    (5963, 2520, 8, 32),
                                                                                                                    (5877, 2521, 8, 30),
                                                                                                                    (6027, 2522, 8, NULL),
                                                                                                                    (5894, 2523, 8, 32),
                                                                                                                    (5926, 2524, 8, NULL),
                                                                                                                    (5919, 2525, 8, 32),
                                                                                                                    (5930, 2526, 8, 32),
                                                                                                                    (6008, 2528, 8, 29),
                                                                                                                    (5971, 2529, 8, NULL),
                                                                                                                    (5989, 2530, 8, 32),
                                                                                                                    (6046, 2531, 8, 32),
                                                                                                                    (6028, 2533, 8, NULL),
                                                                                                                    (6074, 2535, 8, 32),
                                                                                                                    (6036, 2536, 8, 32),
                                                                                                                    (6048, 2537, 8, 29),
                                                                                                                    (6105, 2538, 8, 30),
                                                                                                                    (6110, 2539, 8, 30),
                                                                                                                    (6832, 2541, 9, 33),
                                                                                                                    (6113, 2542, 9, 33),
                                                                                                                    (6427, 2543, 9, 36),
                                                                                                                    (6980, 2544, 9, 36),
                                                                                                                    (6780, 2545, 9, 35),
                                                                                                                    (6118, 2546, 9, 35),
                                                                                                                    (6819, 2547, 9, 36),
                                                                                                                    (6142, 2548, 9, 36),
                                                                                                                    (6128, 2549, 9, 36),
                                                                                                                    (6235, 2550, 9, 36),
                                                                                                                    (7012, 2551, 9, 33),
                                                                                                                    (7055, 2552, 9, 33),
                                                                                                                    (7187, 2553, 9, 36),
                                                                                                                    (7186, 2554, 9, 36),
                                                                                                                    (7177, 2555, 9, 36),
                                                                                                                    (7176, 2556, 9, 36),
                                                                                                                    (6989, 2557, 9, 34),
                                                                                                                    (6781, 2558, 9, 34),
                                                                                                                    (7180, 2559, 9, 34),
                                                                                                                    (7181, 2560, 9, 34),
                                                                                                                    (7179, 2561, 9, 36),
                                                                                                                    (7178, 2562, 9, 36),
                                                                                                                    (6992, 2563, 9, 33),
                                                                                                                    (6827, 2564, 9, 33),
                                                                                                                    (6846, 2565, 9, 36),
                                                                                                                    (6388, 2566, 9, 36),
                                                                                                                    (6644, 2567, 9, 36),
                                                                                                                    (6952, 2568, 9, 36),
                                                                                                                    (6761, 2569, 9, 33),
                                                                                                                    (7072, 2570, 9, 36),
                                                                                                                    (6391, 2571, 9, 36),
                                                                                                                    (7032, 2572, 9, 36),
                                                                                                                    (6641, 2573, 9, 36),
                                                                                                                    (6410, 2574, 9, NULL),
                                                                                                                    (6392, 2575, 9, 34),
                                                                                                                    (7031, 2576, 9, 34),
                                                                                                                    (7185, 2579, 9, NULL),
                                                                                                                    (7184, 2580, 9, NULL),
                                                                                                                    (7182, 2581, 9, 34),
                                                                                                                    (7183, 2582, 9, 34),
                                                                                                                    (7191, 2583, 9, 36),
                                                                                                                    (7192, 2584, 9, 36),
                                                                                                                    (7189, 2585, 9, 36),
                                                                                                                    (7190, 2586, 9, 36),
                                                                                                                    (7076, 2587, 9, 36),
                                                                                                                    (6751, 2588, 9, NULL),
                                                                                                                    (7122, 2589, 9, 36),
                                                                                                                    (6129, 2590, 9, 36),
                                                                                                                    (7120, 2591, 9, 36),
                                                                                                                    (6130, 2592, 9, 36),
                                                                                                                    (6741, 2593, 9, 34),
                                                                                                                    (6775, 2594, 9, 34),
                                                                                                                    (6576, 2595, 9, 34),
                                                                                                                    (7071, 2596, 9, 34),
                                                                                                                    (6238, 2597, 9, 33),
                                                                                                                    (6499, 2598, 9, 33),
                                                                                                                    (6729, 2599, 9, 36),
                                                                                                                    (6643, 2600, 9, 36),
                                                                                                                    (6599, 2601, 9, 36),
                                                                                                                    (7050, 2602, 9, 36),
                                                                                                                    (6241, 2603, 9, 36),
                                                                                                                    (6933, 2604, 9, NULL),
                                                                                                                    (7214, 2605, 9, 36),
                                                                                                                    (6271, 2608, 9, NULL),
                                                                                                                    (6955, 2610, 9, 33),
                                                                                                                    (6439, 2611, 9, 36),
                                                                                                                    (7089, 2612, 9, 36),
                                                                                                                    (6441, 2613, 9, 33),
                                                                                                                    (6655, 2614, 9, 33),
                                                                                                                    (6326, 2615, 9, 33),
                                                                                                                    (7216, 2617, 9, NULL),
                                                                                                                    (6317, 2618, 9, 36),
                                                                                                                    (6654, 2619, 9, NULL),
                                                                                                                    (6480, 2620, 9, 36),
                                                                                                                    (6538, 2621, 9, 36),
                                                                                                                    (7211, 2622, 9, 36),
                                                                                                                    (7212, 2623, 9, 36),
                                                                                                                    (6459, 2624, 9, 36),
                                                                                                                    (7127, 2625, 9, 36),
                                                                                                                    (6926, 2626, 9, 36),
                                                                                                                    (6848, 2627, 9, 36),
                                                                                                                    (6330, 2628, 9, 33),
                                                                                                                    (6555, 2630, 9, NULL),
                                                                                                                    (6553, 2632, 9, 33),
                                                                                                                    (7095, 2633, 9, 33),
                                                                                                                    (7210, 2634, 9, 36),
                                                                                                                    (6552, 2636, 9, 36),
                                                                                                                    (7088, 2637, 9, 36),
                                                                                                                    (6915, 2638, 9, 33),
                                                                                                                    (6529, 2640, 9, 36),
                                                                                                                    (6734, 2642, 9, 36),
                                                                                                                    (6759, 2643, 9, NULL),
                                                                                                                    (6463, 2644, 9, 33),
                                                                                                                    (6516, 2645, 9, 33),
                                                                                                                    (6717, 2646, 9, 36),
                                                                                                                    (6710, 2647, 9, 36),
                                                                                                                    (6804, 2649, 9, 36),
                                                                                                                    (6711, 2650, 9, 36),
                                                                                                                    (6368, 2651, 9, 36),
                                                                                                                    (6328, 2652, 9, 36),
                                                                                                                    (7124, 2654, 9, 36),
                                                                                                                    (7143, 2655, 9, NULL),
                                                                                                                    (7053, 2656, 9, 33),
                                                                                                                    (6367, 2657, 9, 33),
                                                                                                                    (6844, 2660, 9, NULL),
                                                                                                                    (6958, 2661, 9, 36),
                                                                                                                    (7048, 2662, 9, 33),
                                                                                                                    (6527, 2664, 9, 36),
                                                                                                                    (6845, 2666, 9, 33),
                                                                                                                    (6514, 2667, 9, 33),
                                                                                                                    (6618, 2668, 9, 36),
                                                                                                                    (6366, 2669, 9, 36),
                                                                                                                    (6492, 2670, 9, 36),
                                                                                                                    (6619, 2672, 9, 36),
                                                                                                                    (6906, 2673, 9, 36),
                                                                                                                    (6703, 2674, 9, 36),
                                                                                                                    (6151, 2675, 9, 36),
                                                                                                                    (6809, 2676, 9, NULL),
                                                                                                                    (7097, 2677, 9, 36),
                                                                                                                    (6812, 2678, 9, 36),
                                                                                                                    (6349, 2679, 9, 36),
                                                                                                                    (6970, 2680, 9, 36),
                                                                                                                    (6556, 2681, 9, NULL),
                                                                                                                    (6975, 2682, 9, 33),
                                                                                                                    (6125, 2683, 9, 33),
                                                                                                                    (6234, 2684, 9, NULL),
                                                                                                                    (6354, 2685, 9, NULL),
                                                                                                                    (6231, 2686, 9, NULL),
                                                                                                                    (6909, 2687, 9, 36),
                                                                                                                    (6858, 2688, 9, 36),
                                                                                                                    (7087, 2689, 9, 36),
                                                                                                                    (6859, 2690, 9, 36),
                                                                                                                    (7217, 2691, 9, NULL),
                                                                                                                    (6680, 2692, 9, 36),
                                                                                                                    (6536, 2694, 9, 33),
                                                                                                                    (6625, 2696, 9, 36),
                                                                                                                    (6954, 2697, 9, 36),
                                                                                                                    (7226, 2698, 9, 36),
                                                                                                                    (7227, 2699, 9, 36),
                                                                                                                    (6959, 2700, 9, 33),
                                                                                                                    (6898, 2701, 9, 33),
                                                                                                                    (7220, 2702, 9, 36),
                                                                                                                    (7219, 2703, 9, NULL),
                                                                                                                    (7215, 2704, 9, 36),
                                                                                                                    (7199, 2708, 9, 36),
                                                                                                                    (7200, 2709, 9, 36),
                                                                                                                    (6379, 2710, 9, 36),
                                                                                                                    (6364, 2711, 9, 36),
                                                                                                                    (6660, 2712, 9, 36),
                                                                                                                    (6270, 2713, 9, NULL),
                                                                                                                    (6377, 2714, 9, 36),
                                                                                                                    (6272, 2715, 9, 36),
                                                                                                                    (6964, 2716, 9, 33),
                                                                                                                    (6362, 2717, 9, 33),
                                                                                                                    (7197, 2718, 9, 36),
                                                                                                                    (7196, 2719, 9, NULL),
                                                                                                                    (6580, 2720, 9, 33),
                                                                                                                    (6477, 2721, 9, 33),
                                                                                                                    (6348, 2722, 9, 36),
                                                                                                                    (6443, 2723, 9, 36),
                                                                                                                    (6438, 2724, 9, 36),
                                                                                                                    (6962, 2725, 9, 36),
                                                                                                                    (6467, 2726, 9, 36),
                                                                                                                    (6312, 2727, 9, 36),
                                                                                                                    (6229, 2729, 9, NULL),
                                                                                                                    (7201, 2730, 9, 33),
                                                                                                                    (6554, 2731, 9, 33),
                                                                                                                    (6445, 2732, 9, 33),
                                                                                                                    (6382, 2733, 9, 33),
                                                                                                                    (6798, 2734, 9, 33),
                                                                                                                    (6928, 2735, 9, 33),
                                                                                                                    (6912, 2736, 9, 33),
                                                                                                                    (6715, 2737, 9, 33),
                                                                                                                    (6223, 2739, 9, NULL),
                                                                                                                    (6533, 2740, 9, 33),
                                                                                                                    (7109, 2742, 9, 33),
                                                                                                                    (6610, 2743, 9, 33),
                                                                                                                    (6421, 2744, 9, 33),
                                                                                                                    (6481, 2746, 9, 36),
                                                                                                                    (6739, 2747, 9, 36),
                                                                                                                    (6724, 2748, 9, 33),
                                                                                                                    (6531, 2749, 9, 36),
                                                                                                                    (6825, 2750, 9, NULL),
                                                                                                                    (6333, 2751, 9, 33),
                                                                                                                    (6464, 2752, 9, 36),
                                                                                                                    (6442, 2753, 9, 36),
                                                                                                                    (6504, 2754, 9, 36),
                                                                                                                    (6608, 2755, 9, 36),
                                                                                                                    (6332, 2756, 9, 33),
                                                                                                                    (6950, 2758, 9, 33),
                                                                                                                    (6534, 2759, 9, 33),
                                                                                                                    (6301, 2760, 9, 33),
                                                                                                                    (6783, 2761, 9, 36),
                                                                                                                    (7103, 2762, 9, 35),
                                                                                                                    (6322, 2764, 9, 33),
                                                                                                                    (6772, 2765, 9, 34),
                                                                                                                    (6620, 2766, 9, 36),
                                                                                                                    (6350, 2767, 9, NULL),
                                                                                                                    (6626, 2768, 9, 36),
                                                                                                                    (6929, 2769, 9, 33),
                                                                                                                    (7131, 2770, 9, 33),
                                                                                                                    (6918, 2772, 9, 33),
                                                                                                                    (6925, 2774, 9, 36),
                                                                                                                    (7155, 2776, 9, 33),
                                                                                                                    (6507, 2777, 9, 33),
                                                                                                                    (7001, 2778, 9, 33),
                                                                                                                    (6508, 2779, 9, 36),
                                                                                                                    (6882, 2780, 9, 36),
                                                                                                                    (7202, 2781, 9, 36),
                                                                                                                    (6487, 2782, 9, 36),
                                                                                                                    (6778, 2783, 9, 33),
                                                                                                                    (6658, 2784, 9, 33),
                                                                                                                    (6728, 2785, 9, 33),
                                                                                                                    (6813, 2786, 9, 33),
                                                                                                                    (6353, 2787, 9, NULL),
                                                                                                                    (6440, 2788, 9, 36),
                                                                                                                    (7108, 2789, 9, 33),
                                                                                                                    (7148, 2790, 9, 33),
                                                                                                                    (7205, 2791, 9, NULL),
                                                                                                                    (6532, 2792, 9, NULL),
                                                                                                                    (7081, 2793, 9, NULL),
                                                                                                                    (6657, 2794, 9, 36),
                                                                                                                    (7084, 2795, 9, NULL),
                                                                                                                    (6967, 2796, 9, 36),
                                                                                                                    (6191, 2797, 9, 36),
                                                                                                                    (7198, 2798, 9, 36),
                                                                                                                    (6889, 2799, 9, 36),
                                                                                                                    (6190, 2801, 9, 36),
                                                                                                                    (7195, 2802, 9, NULL),
                                                                                                                    (6894, 2803, 9, 36),
                                                                                                                    (6737, 2804, 9, 36),
                                                                                                                    (6299, 2805, 9, 36),
                                                                                                                    (7039, 2806, 9, 36),
                                                                                                                    (7086, 2807, 9, 35),
                                                                                                                    (6822, 2808, 9, NULL),
                                                                                                                    (7221, 2809, 9, NULL),
                                                                                                                    (7222, 2810, 9, 36),
                                                                                                                    (6892, 2811, 9, 36),
                                                                                                                    (6431, 2812, 9, 36),
                                                                                                                    (6302, 2813, 9, 36),
                                                                                                                    (6163, 2814, 9, NULL),
                                                                                                                    (7082, 2815, 9, 33),
                                                                                                                    (6414, 2816, 9, 33),
                                                                                                                    (6896, 2817, 9, 33),
                                                                                                                    (7034, 2818, 9, 33),
                                                                                                                    (7194, 2819, 9, 35),
                                                                                                                    (7193, 2820, 9, 35),
                                                                                                                    (6308, 2821, 9, 33),
                                                                                                                    (6267, 2822, 9, 33),
                                                                                                                    (6740, 2825, 9, 36),
                                                                                                                    (7203, 2827, 9, 33),
                                                                                                                    (7204, 2828, 9, 33),
                                                                                                                    (6720, 2829, 9, 36),
                                                                                                                    (6897, 2830, 9, NULL),
                                                                                                                    (7070, 2831, 9, NULL),
                                                                                                                    (6304, 2832, 9, 36),
                                                                                                                    (6159, 2833, 9, NULL),
                                                                                                                    (6968, 2834, 9, 36),
                                                                                                                    (6381, 2836, 9, 36),
                                                                                                                    (6934, 2837, 9, 33),
                                                                                                                    (6359, 2839, 9, 33),
                                                                                                                    (6607, 2841, 9, 36),
                                                                                                                    (7080, 2842, 9, NULL),
                                                                                                                    (6678, 2843, 9, 33),
                                                                                                                    (6189, 2844, 9, 33),
                                                                                                                    (6237, 2845, 9, 36),
                                                                                                                    (6501, 2846, 9, 36),
                                                                                                                    (6677, 2847, 9, NULL),
                                                                                                                    (6561, 2849, 9, 36),
                                                                                                                    (6334, 2851, 9, 36),
                                                                                                                    (6483, 2852, 9, 36),
                                                                                                                    (6486, 2853, 9, 36),
                                                                                                                    (6971, 2854, 9, 36),
                                                                                                                    (6352, 2855, 9, NULL),
                                                                                                                    (6199, 2856, 9, 36),
                                                                                                                    (7218, 2857, 9, 36),
                                                                                                                    (7223, 2858, 9, 36),
                                                                                                                    (6319, 2859, 9, 33),
                                                                                                                    (7125, 2860, 9, 33),
                                                                                                                    (6517, 2862, 9, 33),
                                                                                                                    (6919, 2863, 9, 33),
                                                                                                                    (6997, 2864, 9, 33),
                                                                                                                    (6363, 2865, 9, 36),
                                                                                                                    (6760, 2866, 9, 36),
                                                                                                                    (7209, 2867, 9, NULL),
                                                                                                                    (6986, 2868, 9, 36),
                                                                                                                    (6829, 2869, 9, 33),
                                                                                                                    (6802, 2870, 9, 36),
                                                                                                                    (6587, 2871, 9, 33),
                                                                                                                    (7207, 2872, 9, 33),
                                                                                                                    (6590, 2873, 9, 33),
                                                                                                                    (6490, 2874, 9, 36),
                                                                                                                    (6733, 2879, 9, NULL),
                                                                                                                    (6513, 2880, 9, 36),
                                                                                                                    (6806, 2881, 9, 36),
                                                                                                                    (6520, 2882, 9, 33),
                                                                                                                    (6524, 2885, 9, 33),
                                                                                                                    (6974, 2887, 9, 33),
                                                                                                                    (7206, 2888, 9, 33),
                                                                                                                    (7102, 2889, 9, 33),
                                                                                                                    (6982, 2890, 9, 33),
                                                                                                                    (6991, 2891, 9, 36),
                                                                                                                    (6911, 2892, 9, 33),
                                                                                                                    (6603, 2894, 9, NULL),
                                                                                                                    (6600, 2895, 9, NULL),
                                                                                                                    (6957, 2896, 9, 33),
                                                                                                                    (6969, 2897, 9, 33),
                                                                                                                    (6601, 2898, 9, 33),
                                                                                                                    (7208, 2899, 9, 36),
                                                                                                                    (6987, 2900, 9, NULL),
                                                                                                                    (7098, 2901, 9, NULL),
                                                                                                                    (7094, 2902, 9, NULL),
                                                                                                                    (7092, 2903, 9, 33),
                                                                                                                    (7117, 2904, 9, NULL),
                                                                                                                    (7118, 2905, 9, 33),
                                                                                                                    (7116, 2906, 9, 36),
                                                                                                                    (7115, 2907, 9, NULL),
                                                                                                                    (6916, 2908, 9, NULL),
                                                                                                                    (6931, 2909, 9, NULL),
                                                                                                                    (7213, 2910, 9, 36),
                                                                                                                    (6899, 2911, 9, NULL);


INSERT INTO `joven_evaluacion_planilla_cargos` (`id`, `nombre`) VALUES
                                                                                (1, 'Prof. Titular Ord.'),
                                                                                (2, 'Prof. Titular Int.'),
                                                                                (3, 'Prof. Asociado Ord.'),
                                                                                (4, 'Prof. Asociado Int.'),
                                                                                (5, 'Prof. Adjunto Ord.'),
                                                                                (6, 'Prof. Adjunto Int.'),
                                                                                (7, 'JTP Ord.'),
                                                                                (8, 'JTP Int.'),
                                                                                (9, 'Ayud. Diplomado Ord.'),
                                                                                (10, 'Ayud. Diplomado Int.'),
                                                                                (11, 'Ayud. Alumno Ord.'),
                                                                                (12, 'Ayud. Alumno Int.');


INSERT INTO `joven_evaluacion_planilla_cargo_maxs` (`joven_evaluacion_planilla_id`, `id`, `joven_evaluacion_planilla_cargo_id`, `maximo`) VALUES
                                                                                                           (1, 1, 1, 10),
                                                                                                           (1, 2, 2, 7),
                                                                                                           (1, 3, 3, 9),
                                                                                                           (1, 4, 4, 6),
                                                                                                           (1, 5, 5, 8),
                                                                                                           (1, 6, 6, 5),
                                                                                                           (1, 7, 7, 6),
                                                                                                           (1, 8, 8, 4),
                                                                                                           (1, 9, 9, 4),
                                                                                                           (1, 10, 10, 2),
                                                                                                           (1, 11, 11, 2),
                                                                                                           (1, 12, 12, 1),
                                                                                                           (2, 13, 1, 10),
                                                                                                           (2, 14, 2, 7),
                                                                                                           (2, 15, 3, 9),
                                                                                                           (2, 16, 4, 6),
                                                                                                           (2, 17, 5, 8),
                                                                                                           (2, 18, 6, 5),
                                                                                                           (2, 19, 7, 6),
                                                                                                           (2, 20, 8, 4),
                                                                                                           (2, 21, 9, 4),
                                                                                                           (2, 22, 10, 2),
                                                                                                           (3, 23, 1, 10),
                                                                                                           (3, 24, 2, 7),
                                                                                                           (3, 25, 3, 9),
                                                                                                           (3, 26, 4, 6),
                                                                                                           (3, 27, 5, 8),
                                                                                                           (3, 28, 6, 5),
                                                                                                           (3, 29, 7, 6),
                                                                                                           (3, 30, 8, 4),
                                                                                                           (3, 31, 9, 4),
                                                                                                           (3, 32, 10, 2),
                                                                                                           (4, 33, 1, 10),
                                                                                                           (4, 34, 2, 7),
                                                                                                           (4, 35, 3, 9),
                                                                                                           (4, 36, 4, 6),
                                                                                                           (4, 37, 5, 8),
                                                                                                           (4, 38, 6, 5),
                                                                                                           (4, 39, 7, 6),
                                                                                                           (4, 40, 8, 4),
                                                                                                           (4, 41, 9, 4),
                                                                                                           (4, 42, 10, 2),
                                                                                                           (5, 43, 1, 10),
                                                                                                           (5, 44, 2, 7),
                                                                                                           (5, 45, 3, 9),
                                                                                                           (5, 46, 4, 6),
                                                                                                           (5, 47, 5, 8),
                                                                                                           (5, 48, 6, 5),
                                                                                                           (5, 49, 7, 6),
                                                                                                           (5, 50, 8, 4),
                                                                                                           (5, 51, 9, 4),
                                                                                                           (5, 52, 10, 2),
                                                                                                           (6, 53, 1, 10),
                                                                                                           (6, 54, 2, 7),
                                                                                                           (6, 55, 3, 9),
                                                                                                           (6, 56, 4, 6),
                                                                                                           (6, 57, 5, 8),
                                                                                                           (6, 58, 6, 5),
                                                                                                           (6, 59, 7, 6),
                                                                                                           (6, 60, 8, 4),
                                                                                                           (6, 61, 9, 4),
                                                                                                           (6, 62, 10, 2),
                                                                                                           (7, 63, 1, 8),
                                                                                                           (7, 64, 2, 8),
                                                                                                           (7, 65, 3, 8),
                                                                                                           (7, 66, 4, 8),
                                                                                                           (7, 67, 5, 8),
                                                                                                           (7, 68, 6, 8),
                                                                                                           (7, 69, 7, 6),
                                                                                                           (7, 70, 8, 4),
                                                                                                           (7, 71, 9, 4),
                                                                                                           (7, 72, 10, 2),
                                                                                                           (8, 73, 1, 8),
                                                                                                           (8, 74, 2, 8),
                                                                                                           (8, 75, 3, 8),
                                                                                                           (8, 76, 4, 8),
                                                                                                           (8, 77, 5, 8),
                                                                                                           (8, 78, 6, 8),
                                                                                                           (8, 79, 7, 6),
                                                                                                           (8, 80, 8, 4),
                                                                                                           (8, 81, 9, 4),
                                                                                                           (8, 82, 10, 2),
                                                                                                           (9, 83, 1, 8),
                                                                                                           (9, 84, 2, 8),
                                                                                                           (9, 85, 3, 8),
                                                                                                           (9, 86, 4, 8),
                                                                                                           (9, 87, 5, 8),
                                                                                                           (9, 88, 6, 8),
                                                                                                           (9, 89, 7, 6),
                                                                                                           (9, 90, 8, 4),
                                                                                                           (9, 91, 9, 4),
                                                                                                           (9, 92, 10, 2),
                                                                                                           (10, 93, 1, 8),
                                                                                                           (10, 94, 2, 8),
                                                                                                           (10, 95, 3, 8),
                                                                                                           (10, 96, 4, 8),
                                                                                                           (10, 97, 5, 8),
                                                                                                           (10, 98, 6, 8),
                                                                                                           (10, 99, 7, 6),
                                                                                                           (10, 100, 8, 4),
                                                                                                           (10, 101, 9, 4),
                                                                                                           (10, 102, 10, 2),
                                                                                                           (11, 103, 1, 8),
                                                                                                           (11, 104, 2, 8),
                                                                                                           (11, 105, 3, 8),
                                                                                                           (11, 106, 4, 8),
                                                                                                           (11, 107, 5, 8),
                                                                                                           (11, 108, 6, 8),
                                                                                                           (11, 109, 7, 6),
                                                                                                           (11, 110, 8, 4),
                                                                                                           (11, 111, 9, 4),
                                                                                                           (11, 112, 10, 2),
                                                                                                           (12, 113, 1, 8),
                                                                                                           (12, 114, 2, 8),
                                                                                                           (12, 115, 3, 8),
                                                                                                           (12, 116, 4, 8),
                                                                                                           (12, 117, 5, 8),
                                                                                                           (12, 118, 6, 8),
                                                                                                           (12, 119, 7, 6),
                                                                                                           (12, 120, 8, 4),
                                                                                                           (12, 121, 9, 4),
                                                                                                           (12, 122, 10, 2),
                                                                                                           (13, 123, 1, 8),
                                                                                                           (13, 124, 2, 8),
                                                                                                           (13, 125, 3, 8),
                                                                                                           (13, 126, 4, 8),
                                                                                                           (13, 127, 5, 8),
                                                                                                           (13, 128, 6, 8),
                                                                                                           (13, 129, 7, 6),
                                                                                                           (13, 130, 8, 4),
                                                                                                           (13, 131, 9, 4),
                                                                                                           (13, 132, 10, 2);


INSERT INTO `evaluacion_subgrupos` (`id`, `nombre`, `pdf`) VALUES
                                                                    (1, 'Miembro de jurados', 'Miembro de jurados (tesis, tesinas, concursos)'),
                                                                    (2, 'Conferencias de divulgación / seminarios / cursos (debidamente acreditados)', 'Seminarios/cursos/conferencias dictadas'),
                                                                    (3, 'Participación/colaboración en la organización de reuniones científicas de la especialidad', 'Participación/colaboración en la organización de reuniones científicas de la especialidad'),
                                                                    (4, 'Publicaciones o Trabajos aceptados para su publicación (libros y capítulos de libros publicados por editoriales reconocidas, revistas con referato de nivel internacional, actas de congresos con comité editorial)', 'Publicaciones o Trab. aceptados para su pub. (libros, cap. de libros, revistas, actas de congresos)'),
                                                                    (5, 'Producción artística acreditada', 'Producción artística'),
                                                                    (6, 'Patentes/Transferencia/Extensión', 'Patentes/Transferencia/Extensión Universitaria'),
                                                                    (7, 'Participación en actividades de formación de recursos humanos en investigación', 'Participación en actividades de formación de rec. humanos relacionados'),
                                                                    (8, 'Participación en reuniones científicas de la especialidad', 'Participación en reuniones científicas de la especialidad'),
                                                                    (9, 'Publicaciones o Trabajos aceptados para su publicación (libros y capítulos de libros publicados por editoriales reconocidas, artículos en revistas científicas y en actas de congresos con comité editorial)', 'Publicaciones o Trabajos aceptados para su publicación (libros y capítulos de libros publicados por editoriales reconocidas, artículos en revistas científicas y en actas de congresos con comité editorial)'),
                                                                    (10, 'Otras actividades', 'Otras actividades'),
                                                                    (11, 'Lugar de trabajo', 'Lugar de trabajo'),
                                                                    (12, 'Actividades de evaluación', 'Actividades de evaluación'),
                                                                    (13, 'Participación en reuniones científicas de la especialidad y trabajos publicados en actas de congreso', 'Participación en reuniones científicas de la especialidad y trabajos publicados en actas de congreso'),
                                                                    (14, 'Publicaciones o Trabajos aceptados para su publicación (libros y capítulos de libros publicados por editoriales reconocidas, artículos en revistas científicas)', 'Publicaciones o Trabajos aceptados para su publicación (libros y capítulos de libros publicados por editoriales reconocidas, artículos en revistas científicas)'),
                                                                    (15, 'Producción tecnológica', 'Producción tecnológica'),
                                                                    (16, 'Extensión', 'Extensión'),
                                                                    (17, 'Período anterior', 'Período anterior'),
                                                                    (18, 'Trabajos publicados en actas de congreso con referato', 'Trabajos publicados en actas de congreso con referato'),
                                                                    (19, 'Otros antecedentes', 'Otros antecedentes'),
                                                                    (20, 'Desarrollos tecnológicos, organizacionales y socio-comunitarios', 'Desarrollos tecnológicos, organizacionales y socio-comunitarios'),
                                                                    (21, 'Trabajos publicados en actas de congreso', 'Trabajos publicados en actas de congreso');

SET FOREIGN_KEY_CHECKS=0;
INSERT INTO `joven_evaluacion_planilla_otros` (`id`, `nombre`, `evaluacion_subgrupo_id`) VALUES
                                                                                                 (1, 'Actividades de Gestión.', NULL),
                                                                                                 (2, 'de tesis de maestría o doctorado.', 1),
                                                                                                 (3, 'de trabajo final o especialización.', 1),
                                                                                                 (4, 'de concurso.', 1),
                                                                                                 (5, 'Miembro de comité científico o editorial, referee de publicaciones, evaluador de proyectos.', 0),
                                                                                                 (6, 'No obtuvo subsidio en el año anterior', NULL),
                                                                                                 (7, 'Actividades de Gestión Universitaria', NULL),
                                                                                                 (8, 'Evaluación de tesis de maestría o doctorado', 12),
                                                                                                 (9, 'Evaluación de trabajo final o especialización', 12),
                                                                                                 (10, 'Jurado de concursos', 12),
                                                                                                 (11, 'Evaluación de trabajos en revistas de CyT', 12);

SET FOREIGN_KEY_CHECKS=1;


INSERT INTO `joven_evaluacion_planilla_otro_maxs` (`joven_evaluacion_planilla_id`, `id`, `joven_evaluacion_planilla_otro_id`, `maximo`, `evaluacion_grupo_id`, `minimo`, `tope`) VALUES
                                                                                                                                                     (1, 1, 1, 0, 32, 0, 2),
                                                                                                                                                     (1, 2, 2, 3, 32, 3, 0),
                                                                                                                                                     (1, 3, 3, 2, 32, 2, 0),
                                                                                                                                                     (1, 4, 4, 1, 32, 1, 0),
                                                                                                                                                     (1, 5, 5, 0, 32, 0, 3),
                                                                                                                                                     (2, 6, 1, 0, 32, 0, 2),
                                                                                                                                                     (2, 7, 2, 3, 32, 3, 0),
                                                                                                                                                     (2, 8, 3, 2, 32, 2, 0),
                                                                                                                                                     (2, 9, 4, 1, 32, 1, 0),
                                                                                                                                                     (2, 10, 5, 0, 32, 0, 3),
                                                                                                                                                     (2, 11, 6, 2, 32, 2, 2),
                                                                                                                                                     (3, 12, 1, 0, 32, 0, 2),
                                                                                                                                                     (3, 13, 2, 3, 32, 3, 0),
                                                                                                                                                     (3, 14, 3, 2, 32, 2, 0),
                                                                                                                                                     (3, 15, 4, 1, 32, 1, 0),
                                                                                                                                                     (3, 16, 5, 0, 32, 0, 3),
                                                                                                                                                     (3, 17, 6, 2, 32, 2, 2),
                                                                                                                                                     (4, 18, 7, 0, 32, 0, 2),
                                                                                                                                                     (4, 19, 2, 3, 32, 3, 0),
                                                                                                                                                     (4, 20, 3, 2, 32, 2, 0),
                                                                                                                                                     (4, 21, 4, 1, 32, 1, 0),
                                                                                                                                                     (4, 22, 6, 2, 32, 2, 2),
                                                                                                                                                     (5, 23, 7, 0, 32, 0, 2),
                                                                                                                                                     (5, 24, 2, 3, 32, 3, 0),
                                                                                                                                                     (5, 25, 3, 2, 32, 2, 0),
                                                                                                                                                     (5, 26, 4, 1, 32, 1, 0),
                                                                                                                                                     (5, 27, 6, 2, 32, 2, 2),
                                                                                                                                                     (6, 28, 7, 0, 32, 0, 2),
                                                                                                                                                     (6, 29, 2, 3, 32, 3, 0),
                                                                                                                                                     (6, 30, 3, 2, 32, 2, 0),
                                                                                                                                                     (6, 31, 4, 1, 32, 1, 0),
                                                                                                                                                     (6, 32, 6, 2, 32, 2, 2),
                                                                                                                                                     (7, 33, 7, 0, 49, 0, 2),
                                                                                                                                                     (7, 34, 8, 3, 48, 3, 0),
                                                                                                                                                     (7, 35, 9, 2, 48, 2, 0),
                                                                                                                                                     (7, 36, 10, 1, 48, 1, 0),
                                                                                                                                                     (7, 37, 11, 1, 48, 1, 0),
                                                                                                                                                     (8, 38, 7, 0, 49, 0, 2),
                                                                                                                                                     (8, 39, 8, 3, 48, 3, 0),
                                                                                                                                                     (8, 40, 9, 2, 48, 2, 0),
                                                                                                                                                     (8, 41, 10, 1, 48, 1, 0),
                                                                                                                                                     (8, 42, 11, 1, 48, 1, 0),
                                                                                                                                                     (9, 43, 7, 0, 49, 0, 2),
                                                                                                                                                     (9, 44, 8, 3, 48, 3, 0),
                                                                                                                                                     (9, 45, 9, 2, 48, 2, 0),
                                                                                                                                                     (9, 46, 10, 1, 48, 1, 0),
                                                                                                                                                     (9, 47, 11, 1, 48, 1, 0),
                                                                                                                                                     (10, 48, 7, 0, 49, 0, 2),
                                                                                                                                                     (10, 49, 8, 3, 48, 3, 0),
                                                                                                                                                     (10, 50, 9, 2, 48, 2, 0),
                                                                                                                                                     (10, 51, 10, 1, 48, 1, 0),
                                                                                                                                                     (10, 52, 11, 1, 48, 1, 0),
                                                                                                                                                     (11, 53, 7, 0, 49, 0, 2),
                                                                                                                                                     (11, 54, 8, 3, 48, 3, 0),
                                                                                                                                                     (11, 55, 9, 2, 48, 2, 0),
                                                                                                                                                     (11, 56, 10, 1, 48, 1, 0),
                                                                                                                                                     (11, 57, 11, 1, 48, 1, 0),
                                                                                                                                                     (12, 58, 7, 0, 49, 0, 2),
                                                                                                                                                     (12, 59, 8, 3, 48, 3, 0),
                                                                                                                                                     (12, 60, 9, 2, 48, 2, 0),
                                                                                                                                                     (12, 61, 10, 1, 48, 1, 0),
                                                                                                                                                     (12, 62, 11, 1, 48, 1, 0),
                                                                                                                                                     (13, 63, 7, 0, 49, 0, 2),
                                                                                                                                                     (13, 64, 8, 3, 48, 3, 0),
                                                                                                                                                     (13, 65, 9, 2, 48, 2, 0),
                                                                                                                                                     (13, 66, 10, 1, 48, 1, 0),
                                                                                                                                                     (13, 67, 11, 1, 48, 1, 0);
SET FOREIGN_KEY_CHECKS=0;
INSERT INTO `joven_evaluacion_planilla_produccions` (`id`, `nombre`, `evaluacion_subgrupo_id`) VALUES
                                                                                                                (1, 'Cursos o seminarios (como expositor): #puntaje# cada diez horas  ', 2),
                                                                                                                (2, 'Conferencias: #puntaje# cada conferencia', 2),
                                                                                                                (3, 'participación en reuniones sin presentación de trabajos: #puntaje# cada una', 3),
                                                                                                                (4, 'presentaciones murales: #puntaje# cada una', 3),
                                                                                                                (5, 'presentaciones orales: #puntaje# cada una', 3),
                                                                                                                (6, 'conferencias plenarias: #puntaje# cada una', 3),
                                                                                                                (7, 'colaboración en la organización: #puntaje# cada una', 3),
                                                                                                                (8, 'Autoría de libros publicados por editorial reconocida', 4),
                                                                                                                (9, 'Capítulos de libros publicados por editorial reconocida (excluidos actas o proceedings)', 4),
                                                                                                                (10, 'Artículos en revistas indexadas con referato de nivel internacional', 4),
                                                                                                                (11, 'Trabajos completos publicados en actas de congresos con referato', 4),
                                                                                                                (12, 'Reseñas, comentarios bibliográficos, notas críticas publicadas en revistas de la especialidad con referato', 4),
                                                                                                                (14, 'Exposiciones o presentaciones en ámbitos reconocidos', 5),
                                                                                                                (15, 'patentes registradas', 6),
                                                                                                                (16, 'transferencias debidamente documentadas', 6),
                                                                                                                (17, 'extensión debidamente documentada', 6),
                                                                                                                (18, 'Dirección o codirección de trabajos finales de grado aprobados', 7),
                                                                                                                (19, 'Dirección o codirección de trabajos finales de especialización aprobados', 7),
                                                                                                                (20, 'Dirección o codirección de tesis de maestrías aprobadas', 7),
                                                                                                                (21, 'Dirección o codirección de tesis de doctorado aprobadas', 7),
                                                                                                                (22, 'Trabajos profesionales (debidamente acreditados)', NULL),
                                                                                                                (23, 'U. de Inv. formalmente reconocida y aprob. por el C. Superior', NULL),
                                                                                                                (24, 'presentaciones poster: #puntaje# cada una', 8),
                                                                                                                (25, 'presentaciones orales: #puntaje# cada una', 8),
                                                                                                                (26, 'conferencias: #puntaje# cada una', 8),
                                                                                                                (27, 'colaboración en la organización: #puntaje# cada una', 8),
                                                                                                                (28, 'Autoría de libros publicados por editorial reconocida', 9),
                                                                                                                (29, 'Capítulos de libros publicados por editorial reconocida (excluidos actas o proceedings)', 9),
                                                                                                                (30, 'Artículos en revistas con referato', 9),
                                                                                                                (31, 'Trabajos completos publicados en actas de congresos con referato', 9),
                                                                                                                (32, 'transferencias o innovaciones tecnológicas documentadas', 6),
                                                                                                                (33, 'actividades de extensión documentada', 6),
                                                                                                                (34, 'Otros', 10),
                                                                                                                (35, 'U. de Inv. formalmente reconocida y aprob. por el C. Superior', 11),
                                                                                                                (36, 'Dirección de trabajos finales de grado aprobados', 7),
                                                                                                                (37, 'Dirección o codirección de trabajos finales de especialización o tesis de maestría aprobados', 7),
                                                                                                                (38, 'Dirección o codirección de tesis de doctorado aprobadas', 7),
                                                                                                                (39, 'Dirección de Becas de grado (Entrenamiento CIC - Vocaciones Científicas CIN)', 7),
                                                                                                                (40, 'Trabajos publicados en actas de congreso', 13),
                                                                                                                (41, 'Participación u organización de eventos de CyT', 13),
                                                                                                                (42, 'Autoría de libros publicados por editorial reconocida', 14),
                                                                                                                (43, 'Capítulos de libros publicados por editorial reconocida (excluidos actas o proceedings)', 14),
                                                                                                                (44, 'Artículos en revistas con referato', 14),
                                                                                                                (45, 'Patentes registradas', 15),
                                                                                                                (46, 'Otras producciones tecnológicas con titulo de propiedad intelectual (Modelo de utilidad -Derecho de obtentor (Variedades vegetales) -Derecho de autor de producciones tecnológicas -Modelo industrial -Diseño industrial, Marca de servicios o producto)', 15),
                                                                                                                (47, 'Servicios científicos tecnológicos e informes técnicos', 15),
                                                                                                                (48, 'Actividades de extensión documentada', 16),
                                                                                                                (49, 'Otros actividades (Participación en Proyectos de Investigación, membresías en asociación de CyT y/o profesionales y participación de redes temáticas)', NULL),
                                                                                                                (50, 'U. de Inv. formalmente reconocida y aprobada por el C. Superior (Ord. 284)', 11),
                                                                                                                (51, 'Trabajo completo', 18),
                                                                                                                (52, 'Resumen', 18),
                                                                                                                (53, 'Participación de redes temáticas o institucionales)', NULL),
                                                                                                                (54, 'Artículo completo - Artículo breve', 18),
                                                                                                                (55, 'Patentes registradas', 20),
                                                                                                                (56, 'Otras producciones tecnológicas con título de propiedad intelectual (Modelo de utilidad -Derecho de obtentor (Variedades vegetales) -Derechos de autor de software, multimedia y páginas web -Modelo o diseño industrial -Esquemas de circuitos integrados -Marca de servicio o producto -Derechos de autor en obras inéditas y publicadas)', 20),
                                                                                                                (57, 'Servicios científicos tecnológicos e informes técnicos', 20),
                                                                                                                (58, 'Participación de redes temáticas o institucionales', 19),
                                                                                                                (59, 'Resumen', 21),
                                                                                                                (60, 'Artículo completo - Artículo breve', 21),
                                                                                                                (61, 'Artículos en revistas', 14);

SET FOREIGN_KEY_CHECKS=1;

INSERT INTO `joven_evaluacion_planilla_produccion_maxs` (`joven_evaluacion_planilla_id`, `id`, `joven_evaluacion_planilla_produccion_id`, `maximo`, `evaluacion_grupo_id`, `minimo`, `tope`) VALUES
                                                                                                                                                                    (1, 1, 1, 1, 33, 1, 12),
                                                                                                                                                                    (1, 2, 2, 1, 33, 1, 3),
                                                                                                                                                                    (1, 3, 3, 0.1, 33, 0.1, 1),
                                                                                                                                                                    (1, 4, 4, 0.5, 33, 0.5, 2),
                                                                                                                                                                    (1, 5, 5, 1, 33, 1, 5),
                                                                                                                                                                    (1, 6, 6, 5, 33, 5, 10),
                                                                                                                                                                    (1, 7, 7, 0.5, 33, 0.5, 1),
                                                                                                                                                                    (1, 8, 8, 20, 33, 20, 0),
                                                                                                                                                                    (1, 9, 9, 12, 33, 12, 0),
                                                                                                                                                                    (1, 10, 10, 10, 33, 10, 0),
                                                                                                                                                                    (1, 11, 11, 3, 33, 3, 0),
                                                                                                                                                                    (1, 12, 12, 1, 33, 1, 0),
                                                                                                                                                                    (1, 14, 14, 9, 33, 9, 0),
                                                                                                                                                                    (1, 15, 15, 8, 33, 0, 0),
                                                                                                                                                                    (1, 16, 16, 6, 33, 0, 0),
                                                                                                                                                                    (1, 17, 17, 2, 33, 0, 0),
                                                                                                                                                                    (1, 18, 18, 5, 33, 5, 0),
                                                                                                                                                                    (1, 19, 19, 7, 33, 7, 0),
                                                                                                                                                                    (1, 20, 20, 10, 33, 10, 0),
                                                                                                                                                                    (1, 21, 21, 20, 33, 20, 0),
                                                                                                                                                                    (1, 22, 22, 0, 33, 0, 3),
                                                                                                                                                                    (2, 23, 1, 1, 35, 1, 12),
                                                                                                                                                                    (2, 24, 2, 1, 35, 1, 3),
                                                                                                                                                                    (2, 25, 3, 0.1, 35, 0.1, 1),
                                                                                                                                                                    (2, 26, 4, 0.5, 35, 0.5, 2),
                                                                                                                                                                    (2, 27, 5, 1, 35, 1, 5),
                                                                                                                                                                    (2, 28, 6, 5, 35, 5, 10),
                                                                                                                                                                    (2, 29, 7, 0.5, 35, 0.5, 1),
                                                                                                                                                                    (2, 30, 8, 20, 35, 20, 0),
                                                                                                                                                                    (2, 31, 9, 12, 35, 12, 0),
                                                                                                                                                                    (2, 32, 10, 10, 35, 10, 0),
                                                                                                                                                                    (2, 33, 11, 3, 35, 3, 0),
                                                                                                                                                                    (2, 34, 12, 1, 35, 1, 0),
                                                                                                                                                                    (2, 35, 14, 9, 35, 9, 0),
                                                                                                                                                                    (2, 36, 15, 8, 35, 0, 0),
                                                                                                                                                                    (2, 37, 16, 6, 35, 0, 0),
                                                                                                                                                                    (2, 38, 17, 2, 35, 0, 0),
                                                                                                                                                                    (2, 39, 18, 5, 35, 5, 0),
                                                                                                                                                                    (2, 40, 19, 7, 35, 7, 0),
                                                                                                                                                                    (2, 41, 20, 10, 35, 10, 0),
                                                                                                                                                                    (2, 42, 21, 20, 35, 20, 0),
                                                                                                                                                                    (2, 43, 22, 0, 35, 0, 3),
                                                                                                                                                                    (2, 44, 23, 3, 35, 3, 3),
                                                                                                                                                                    (3, 45, 1, 1, 35, 1, 12),
                                                                                                                                                                    (3, 46, 2, 1, 35, 1, 3),
                                                                                                                                                                    (3, 47, 3, 0.1, 35, 0.1, 1),
                                                                                                                                                                    (3, 48, 4, 0.5, 35, 0.5, 2),
                                                                                                                                                                    (3, 49, 5, 1, 35, 1, 5),
                                                                                                                                                                    (3, 50, 6, 5, 35, 5, 10),
                                                                                                                                                                    (3, 51, 7, 0.5, 35, 0.5, 1),
                                                                                                                                                                    (3, 52, 8, 20, 35, 20, 0),
                                                                                                                                                                    (3, 53, 9, 12, 35, 12, 0),
                                                                                                                                                                    (3, 54, 10, 10, 35, 10, 0),
                                                                                                                                                                    (3, 55, 11, 3, 35, 3, 0),
                                                                                                                                                                    (3, 56, 12, 1, 35, 1, 0),
                                                                                                                                                                    (3, 57, 14, 9, 35, 9, 0),
                                                                                                                                                                    (3, 58, 15, 8, 35, 0, 0),
                                                                                                                                                                    (3, 59, 16, 6, 35, 0, 0),
                                                                                                                                                                    (3, 60, 17, 2, 35, 0, 0),
                                                                                                                                                                    (3, 61, 18, 5, 35, 5, 0),
                                                                                                                                                                    (3, 62, 19, 7, 35, 7, 0),
                                                                                                                                                                    (3, 63, 20, 10, 35, 10, 0),
                                                                                                                                                                    (3, 64, 21, 20, 35, 20, 0),
                                                                                                                                                                    (3, 65, 22, 0, 35, 0, 1),
                                                                                                                                                                    (3, 66, 23, 1, 35, 1, 1),
                                                                                                                                                                    (4, 67, 18, 5, 35, 5, 0),
                                                                                                                                                                    (4, 68, 19, 7, 35, 7, 0),
                                                                                                                                                                    (4, 69, 20, 10, 35, 10, 0),
                                                                                                                                                                    (4, 70, 21, 20, 35, 20, 0),
                                                                                                                                                                    (4, 71, 24, 0.5, 35, 0.5, 2),
                                                                                                                                                                    (4, 72, 25, 1, 35, 1, 5),
                                                                                                                                                                    (4, 73, 26, 3, 35, 3, 6),
                                                                                                                                                                    (4, 74, 27, 0.5, 35, 0.5, 1),
                                                                                                                                                                    (4, 75, 28, 20, 35, 20, 0),
                                                                                                                                                                    (4, 76, 29, 8, 35, 8, 0),
                                                                                                                                                                    (4, 77, 30, 10, 35, 10, 0),
                                                                                                                                                                    (4, 78, 31, 5, 35, 5, 0),
                                                                                                                                                                    (4, 79, 14, 10, 35, 10, 0),
                                                                                                                                                                    (4, 80, 15, 10, 35, 0, 0),
                                                                                                                                                                    (4, 81, 32, 6, 35, 0, 0),
                                                                                                                                                                    (4, 82, 33, 2, 35, 0, 0),
                                                                                                                                                                    (4, 83, 34, 0, 35, 0, 2),
                                                                                                                                                                    (4, 84, 35, 1, 35, 1, 1),
                                                                                                                                                                    (5, 85, 18, 5, 35, 5, 0),
                                                                                                                                                                    (5, 86, 19, 7, 35, 7, 0),
                                                                                                                                                                    (5, 87, 20, 10, 35, 10, 0),
                                                                                                                                                                    (5, 88, 21, 20, 35, 20, 0),
                                                                                                                                                                    (5, 89, 24, 0.5, 35, 0.5, 2),
                                                                                                                                                                    (5, 90, 25, 1, 35, 1, 5),
                                                                                                                                                                    (5, 91, 26, 3, 35, 3, 6),
                                                                                                                                                                    (5, 92, 27, 0.5, 35, 0.5, 1),
                                                                                                                                                                    (5, 93, 28, 20, 35, 20, 0),
                                                                                                                                                                    (5, 94, 29, 8, 35, 8, 0),
                                                                                                                                                                    (5, 95, 30, 10, 35, 10, 0),
                                                                                                                                                                    (5, 96, 31, 5, 35, 5, 0),
                                                                                                                                                                    (5, 97, 14, 10, 35, 10, 0),
                                                                                                                                                                    (5, 98, 15, 10, 35, 0, 0),
                                                                                                                                                                    (5, 99, 32, 6, 35, 0, 0),
                                                                                                                                                                    (5, 100, 33, 2, 35, 0, 0),
                                                                                                                                                                    (5, 101, 34, 0, 35, 0, 2),
                                                                                                                                                                    (5, 102, 35, 1, 35, 1, 1),
                                                                                                                                                                    (6, 103, 18, 5, 35, 5, 0),
                                                                                                                                                                    (6, 104, 19, 7, 35, 7, 0),
                                                                                                                                                                    (6, 105, 20, 10, 35, 10, 0),
                                                                                                                                                                    (6, 106, 21, 20, 35, 20, 0),
                                                                                                                                                                    (6, 107, 24, 0.5, 35, 0.5, 2),
                                                                                                                                                                    (6, 108, 25, 1, 35, 1, 5),
                                                                                                                                                                    (6, 109, 26, 3, 35, 3, 6),
                                                                                                                                                                    (6, 110, 27, 0.5, 35, 0.5, 1),
                                                                                                                                                                    (6, 111, 28, 20, 35, 20, 0),
                                                                                                                                                                    (6, 112, 29, 8, 35, 8, 0),
                                                                                                                                                                    (6, 113, 30, 10, 35, 10, 0),
                                                                                                                                                                    (6, 114, 31, 5, 35, 5, 0),
                                                                                                                                                                    (6, 115, 14, 10, 35, 10, 0),
                                                                                                                                                                    (6, 116, 15, 10, 35, 0, 0),
                                                                                                                                                                    (6, 117, 32, 6, 35, 0, 0),
                                                                                                                                                                    (6, 118, 33, 2, 35, 0, 0),
                                                                                                                                                                    (6, 119, 34, 0, 35, 0, 2),
                                                                                                                                                                    (6, 120, 35, 1, 35, 1, 1),
                                                                                                                                                                    (7, 121, 36, 2, 50, 2, 0),
                                                                                                                                                                    (7, 122, 37, 7, 50, 7, 0),
                                                                                                                                                                    (7, 123, 38, 12, 50, 12, 0),
                                                                                                                                                                    (7, 124, 39, 4, 50, 4, 0),
                                                                                                                                                                    (7, 125, 40, 1, 51, 1, 0),
                                                                                                                                                                    (7, 126, 41, 1, 51, 1, 0),
                                                                                                                                                                    (7, 127, 42, 20, 52, 20, 0),
                                                                                                                                                                    (7, 128, 43, 8, 52, 8, 0),
                                                                                                                                                                    (7, 129, 44, 10, 52, 10, 0),
                                                                                                                                                                    (7, 130, 14, 10, 53, 10, 40),
                                                                                                                                                                    (7, 131, 45, 10, 54, 0, 0),
                                                                                                                                                                    (7, 132, 46, 5, 54, 0, 0),
                                                                                                                                                                    (7, 133, 47, 3, 54, 3, 15),
                                                                                                                                                                    (7, 134, 48, 3, 53, 3, 9),
                                                                                                                                                                    (7, 135, 49, 0, 53, 0, 2),
                                                                                                                                                                    (7, 136, 50, 1, 53, 1, 1),
                                                                                                                                                                    (8, 137, 36, 2, 50, 2, 0),
                                                                                                                                                                    (8, 138, 37, 7, 50, 7, 0),
                                                                                                                                                                    (8, 139, 38, 12, 50, 12, 0),
                                                                                                                                                                    (8, 140, 39, 4, 50, 4, 0),
                                                                                                                                                                    (8, 141, 51, 2, 51, 2, 0),
                                                                                                                                                                    (8, 142, 52, 0.5, 51, 0.5, 0),
                                                                                                                                                                    (8, 143, 42, 20, 52, 20, 0),
                                                                                                                                                                    (8, 144, 43, 8, 52, 8, 0),
                                                                                                                                                                    (8, 145, 44, 10, 52, 10, 0),
                                                                                                                                                                    (8, 146, 14, 10, 53, 10, 40),
                                                                                                                                                                    (8, 147, 45, 10, 54, 0, 0),
                                                                                                                                                                    (8, 148, 46, 5, 54, 0, 0),
                                                                                                                                                                    (8, 149, 47, 3, 54, 0, 15),
                                                                                                                                                                    (8, 150, 48, 3, 53, 0, 9),
                                                                                                                                                                    (8, 151, 53, 0, 53, 0, 1),
                                                                                                                                                                    (8, 152, 50, 2, 53, 2, 2),
                                                                                                                                                                    (9, 153, 36, 2, 81, 2, 0),
                                                                                                                                                                    (9, 154, 37, 7, 81, 7, 0),
                                                                                                                                                                    (9, 155, 38, 12, 81, 12, 0),
                                                                                                                                                                    (9, 156, 39, 2, 81, 2, 0),
                                                                                                                                                                    (9, 157, 54, 2, 82, 2, 0),
                                                                                                                                                                    (9, 158, 52, 0.5, 82, 0.5, 0),
                                                                                                                                                                    (9, 159, 42, 20, 83, 20, 0),
                                                                                                                                                                    (9, 160, 43, 8, 83, 8, 0),
                                                                                                                                                                    (9, 161, 44, 10, 83, 10, 0),
                                                                                                                                                                    (9, 162, 14, 10, 84, 10, 40),
                                                                                                                                                                    (9, 163, 55, 10, 85, 0, 0),
                                                                                                                                                                    (9, 164, 56, 5, 85, 0, 0),
                                                                                                                                                                    (9, 165, 57, 3, 85, 0, 15),
                                                                                                                                                                    (9, 166, 48, 3, 84, 0, 9),
                                                                                                                                                                    (9, 167, 58, 0, 84, 0, 1),
                                                                                                                                                                    (9, 168, 50, 2, 84, 2, 2),
                                                                                                                                                                    (10, 169, 36, 2, 81, 2, 0),
                                                                                                                                                                    (10, 170, 37, 7, 81, 7, 0),
                                                                                                                                                                    (10, 171, 38, 12, 81, 12, 0),
                                                                                                                                                                    (10, 172, 39, 2, 81, 2, 0),
                                                                                                                                                                    (10, 173, 54, 2, 82, 2, 0),
                                                                                                                                                                    (10, 174, 52, 0.5, 82, 0.5, 0),
                                                                                                                                                                    (10, 175, 42, 20, 83, 20, 0),
                                                                                                                                                                    (10, 176, 43, 8, 83, 8, 0),
                                                                                                                                                                    (10, 177, 44, 10, 83, 10, 0),
                                                                                                                                                                    (10, 178, 14, 10, 84, 10, 40),
                                                                                                                                                                    (10, 179, 55, 10, 85, 0, 0),
                                                                                                                                                                    (10, 180, 56, 5, 85, 0, 0),
                                                                                                                                                                    (10, 181, 57, 3, 85, 0, 15),
                                                                                                                                                                    (10, 182, 48, 3, 84, 0, 9),
                                                                                                                                                                    (10, 183, 58, 0, 84, 0, 1),
                                                                                                                                                                    (10, 184, 50, 2, 84, 2, 2),
                                                                                                                                                                    (11, 185, 36, 2, 81, 2, 0),
                                                                                                                                                                    (11, 186, 37, 7, 81, 7, 0),
                                                                                                                                                                    (11, 187, 38, 12, 81, 12, 0),
                                                                                                                                                                    (11, 188, 39, 2, 81, 2, 0),
                                                                                                                                                                    (11, 189, 54, 2, 82, 2, 0),
                                                                                                                                                                    (11, 190, 52, 0.5, 82, 0.5, 0),
                                                                                                                                                                    (11, 191, 42, 20, 83, 20, 0),
                                                                                                                                                                    (11, 192, 43, 8, 83, 8, 0),
                                                                                                                                                                    (11, 193, 44, 10, 83, 10, 0),
                                                                                                                                                                    (11, 194, 14, 10, 84, 10, 40),
                                                                                                                                                                    (11, 195, 55, 10, 85, 0, 0),
                                                                                                                                                                    (11, 196, 56, 5, 85, 0, 0),
                                                                                                                                                                    (11, 197, 57, 3, 85, 0, 15),
                                                                                                                                                                    (11, 198, 48, 3, 84, 0, 9),
                                                                                                                                                                    (11, 199, 58, 0, 84, 0, 1),
                                                                                                                                                                    (11, 200, 50, 2, 84, 2, 2),
                                                                                                                                                                    (12, 201, 36, 2, 81, 2, 0),
                                                                                                                                                                    (12, 202, 37, 7, 81, 7, 0),
                                                                                                                                                                    (12, 203, 38, 12, 81, 12, 0),
                                                                                                                                                                    (12, 204, 39, 2, 81, 2, 0),
                                                                                                                                                                    (12, 205, 54, 2, 82, 2, 0),
                                                                                                                                                                    (12, 206, 52, 0.5, 82, 0.5, 0),
                                                                                                                                                                    (12, 207, 42, 20, 83, 20, 0),
                                                                                                                                                                    (12, 208, 43, 8, 83, 8, 0),
                                                                                                                                                                    (12, 209, 44, 10, 83, 10, 0),
                                                                                                                                                                    (12, 210, 14, 10, 84, 10, 40),
                                                                                                                                                                    (12, 211, 55, 10, 85, 0, 0),
                                                                                                                                                                    (12, 212, 56, 5, 85, 0, 0),
                                                                                                                                                                    (12, 213, 57, 3, 85, 0, 15),
                                                                                                                                                                    (12, 214, 48, 3, 84, 0, 9),
                                                                                                                                                                    (12, 215, 58, 0, 84, 0, 1),
                                                                                                                                                                    (12, 216, 50, 2, 84, 2, 2),
                                                                                                                                                                    (13, 217, 36, 2, 81, 2, 0),
                                                                                                                                                                    (13, 218, 37, 7, 81, 7, 0),
                                                                                                                                                                    (13, 219, 38, 12, 81, 12, 0),
                                                                                                                                                                    (13, 220, 39, 2, 81, 2, 0),
                                                                                                                                                                    (13, 221, 60, 2, 82, 2, 0),
                                                                                                                                                                    (13, 222, 59, 0.5, 82, 0.5, 0),
                                                                                                                                                                    (13, 223, 42, 20, 83, 20, 0),
                                                                                                                                                                    (13, 224, 43, 8, 83, 8, 0),
                                                                                                                                                                    (13, 225, 61, 10, 83, 10, 0),
                                                                                                                                                                    (13, 226, 14, 10, 84, 10, 40),
                                                                                                                                                                    (13, 227, 55, 10, 85, 0, 0),
                                                                                                                                                                    (13, 228, 56, 5, 85, 0, 0),
                                                                                                                                                                    (13, 229, 57, 3, 85, 0, 15),
                                                                                                                                                                    (13, 230, 48, 3, 84, 0, 9),
                                                                                                                                                                    (13, 231, 58, 0, 84, 0, 1),
SET FOREIGN_KEY_CHECKS=0;                                                                                                                                                                    (13, 232, 50, 2, 84, 2, 2);
INSERT INTO `joven_evaluacion_unidad_aprobadas` (`id`, `unidad_id`, `periodo_id`) VALUES
                                                                                  (260, 1874, 5),
                                                                                  (377, 1874, 6),
                                                                                  (523, 1874, 7),
                                                                                  (684, 1874, 8),
                                                                                  (852, 1874, 9),
                                                                                  (1027, 1874, 10),
                                                                                  (1205, 1874, 12),
                                                                                  (1395, 1874, 13),
                                                                                  (1585, 1874, 14),
                                                                                  (1776, 1874, 15),
                                                                                  (1975, 1875, 15),
                                                                                  (40, 1899, 3),
                                                                                  (103, 1899, 4),
                                                                                  (261, 1899, 5),
                                                                                  (378, 1899, 6),
                                                                                  (524, 1899, 7),
                                                                                  (685, 1899, 8),
                                                                                  (853, 1899, 9),
                                                                                  (1028, 1899, 10),
                                                                                  (1206, 1899, 12),
                                                                                  (1396, 1899, 13),
                                                                                  (1586, 1899, 14),
                                                                                  (1777, 1899, 15),
                                                                                  (674, 5372, 7),
                                                                                  (686, 5372, 8),
                                                                                  (1003, 5372, 9),
                                                                                  (1178, 5372, 10),
                                                                                  (1356, 5372, 12),
                                                                                  (1546, 5372, 13),
                                                                                  (1736, 5372, 14),
                                                                                  (1927, 5372, 15),
                                                                                  (1973, 5376, 15),
                                                                                  (1391, 5378, 12),
                                                                                  (1581, 5378, 13),
                                                                                  (1771, 5378, 14),
                                                                                  (1962, 5378, 15),
                                                                                  (27, 5380, 3),
                                                                                  (90, 5380, 4),
                                                                                  (262, 5380, 5),
                                                                                  (379, 5380, 6),
                                                                                  (525, 5380, 7),
                                                                                  (687, 5380, 8),
                                                                                  (854, 5380, 9),
                                                                                  (1029, 5380, 10),
                                                                                  (1207, 5380, 12),
                                                                                  (1397, 5380, 13),
                                                                                  (1587, 5380, 14),
                                                                                  (1778, 5380, 15),
                                                                                  (29, 5381, 3),
                                                                                  (92, 5381, 4),
                                                                                  (263, 5381, 5),
                                                                                  (380, 5381, 6),
                                                                                  (526, 5381, 7),
                                                                                  (688, 5381, 8),
                                                                                  (855, 5381, 9),
                                                                                  (1030, 5381, 10),
                                                                                  (1208, 5381, 12),
                                                                                  (1398, 5381, 13),
                                                                                  (1588, 5381, 14),
                                                                                  (1779, 5381, 15),
                                                                                  (1392, 5382, 12),
                                                                                  (1582, 5382, 13),
                                                                                  (1772, 5382, 14),
                                                                                  (1963, 5382, 15),
                                                                                  (32, 5383, 3),
                                                                                  (95, 5383, 4),
                                                                                  (264, 5383, 5),
                                                                                  (381, 5383, 6),
                                                                                  (527, 5383, 7),
                                                                                  (689, 5383, 8),
                                                                                  (856, 5383, 9),
                                                                                  (1031, 5383, 10),
                                                                                  (1209, 5383, 12),
                                                                                  (1399, 5383, 13),
                                                                                  (1589, 5383, 14),
                                                                                  (1780, 5383, 15),
                                                                                  (673, 5384, 7),
                                                                                  (690, 5384, 8),
                                                                                  (1002, 5384, 9),
                                                                                  (1177, 5384, 10),
                                                                                  (1355, 5384, 12),
                                                                                  (1545, 5384, 13),
                                                                                  (1735, 5384, 14),
                                                                                  (1926, 5384, 15),
                                                                                  (26, 5415, 3),
                                                                                  (89, 5415, 4),
                                                                                  (265, 5415, 5),
                                                                                  (382, 5415, 6),
                                                                                  (528, 5415, 7),
                                                                                  (691, 5415, 8),
                                                                                  (857, 5415, 9),
                                                                                  (1032, 5415, 10),
                                                                                  (1210, 5415, 12),
                                                                                  (1400, 5415, 13),
                                                                                  (1590, 5415, 14),
                                                                                  (1781, 5415, 15),
                                                                                  (36, 5416, 3),
                                                                                  (99, 5416, 4),
                                                                                  (266, 5416, 5),
                                                                                  (383, 5416, 6),
                                                                                  (529, 5416, 7),
                                                                                  (692, 5416, 8),
                                                                                  (858, 5416, 9),
                                                                                  (1033, 5416, 10),
                                                                                  (1211, 5416, 12),
                                                                                  (1401, 5416, 13),
                                                                                  (1591, 5416, 14),
                                                                                  (1782, 5416, 15),
                                                                                  (20, 5419, 3),
                                                                                  (83, 5419, 4),
                                                                                  (267, 5419, 5),
                                                                                  (384, 5419, 6),
                                                                                  (530, 5419, 7),
                                                                                  (693, 5419, 8),
                                                                                  (859, 5419, 9),
                                                                                  (1034, 5419, 10),
                                                                                  (1212, 5419, 12),
                                                                                  (1402, 5419, 13),
                                                                                  (1592, 5419, 14),
                                                                                  (1783, 5419, 15),
                                                                                  (23, 5420, 3),
                                                                                  (86, 5420, 4),
                                                                                  (268, 5420, 5),
                                                                                  (385, 5420, 6),
                                                                                  (531, 5420, 7),
                                                                                  (694, 5420, 8),
                                                                                  (860, 5420, 9),
                                                                                  (1035, 5420, 10),
                                                                                  (1213, 5420, 12),
                                                                                  (1403, 5420, 13),
                                                                                  (1593, 5420, 14),
                                                                                  (1784, 5420, 15),
                                                                                  (21, 5421, 3),
                                                                                  (84, 5421, 4),
                                                                                  (269, 5421, 5),
                                                                                  (386, 5421, 6),
                                                                                  (532, 5421, 7),
                                                                                  (695, 5421, 8),
                                                                                  (861, 5421, 9),
                                                                                  (1036, 5421, 10),
                                                                                  (1214, 5421, 12),
                                                                                  (1404, 5421, 13),
                                                                                  (1594, 5421, 14),
                                                                                  (1785, 5421, 15),
                                                                                  (28, 5422, 3),
                                                                                  (91, 5422, 4),
                                                                                  (270, 5422, 5),
                                                                                  (387, 5422, 6),
                                                                                  (533, 5422, 7),
                                                                                  (696, 5422, 8),
                                                                                  (862, 5422, 9),
                                                                                  (1037, 5422, 10),
                                                                                  (1215, 5422, 12),
                                                                                  (1405, 5422, 13),
                                                                                  (1595, 5422, 14),
                                                                                  (1786, 5422, 15),
                                                                                  (24, 5423, 3),
                                                                                  (87, 5423, 4),
                                                                                  (271, 5423, 5),
                                                                                  (388, 5423, 6),
                                                                                  (534, 5423, 7),
                                                                                  (697, 5423, 8),
                                                                                  (863, 5423, 9),
                                                                                  (1038, 5423, 10),
                                                                                  (1216, 5423, 12),
                                                                                  (1406, 5423, 13),
                                                                                  (1596, 5423, 14),
                                                                                  (1787, 5423, 15),
                                                                                  (25, 5424, 3),
                                                                                  (88, 5424, 4),
                                                                                  (272, 5424, 5),
                                                                                  (389, 5424, 6),
                                                                                  (535, 5424, 7),
                                                                                  (698, 5424, 8),
                                                                                  (864, 5424, 9),
                                                                                  (1039, 5424, 10),
                                                                                  (1217, 5424, 12),
                                                                                  (1407, 5424, 13),
                                                                                  (1597, 5424, 14),
                                                                                  (1788, 5424, 15),
                                                                                  (33, 5425, 3),
                                                                                  (96, 5425, 4),
                                                                                  (273, 5425, 5),
                                                                                  (390, 5425, 6),
                                                                                  (536, 5425, 7),
                                                                                  (699, 5425, 8),
                                                                                  (865, 5425, 9),
                                                                                  (1040, 5425, 10),
                                                                                  (1218, 5425, 12),
                                                                                  (1408, 5425, 13),
                                                                                  (1598, 5425, 14),
                                                                                  (1789, 5425, 15),
                                                                                  (22, 5426, 3),
                                                                                  (85, 5426, 4),
                                                                                  (274, 5426, 5),
                                                                                  (391, 5426, 6),
                                                                                  (537, 5426, 7),
                                                                                  (700, 5426, 8),
                                                                                  (866, 5426, 9),
                                                                                  (1041, 5426, 10),
                                                                                  (1219, 5426, 12),
                                                                                  (1409, 5426, 13),
                                                                                  (1599, 5426, 14),
                                                                                  (1790, 5426, 15),
                                                                                  (49, 5738, 3),
                                                                                  (112, 5738, 4),
                                                                                  (275, 5738, 5),
                                                                                  (392, 5738, 6),
                                                                                  (538, 5738, 7),
                                                                                  (701, 5738, 8),
                                                                                  (867, 5738, 9),
                                                                                  (1042, 5738, 10),
                                                                                  (1220, 5738, 12),
                                                                                  (1410, 5738, 13),
                                                                                  (1600, 5738, 14),
                                                                                  (1791, 5738, 15),
                                                                                  (52, 5739, 3),
                                                                                  (115, 5739, 4),
                                                                                  (276, 5739, 5),
                                                                                  (393, 5739, 6),
                                                                                  (539, 5739, 7),
                                                                                  (702, 5739, 8),
                                                                                  (868, 5739, 9),
                                                                                  (1043, 5739, 10),
                                                                                  (1221, 5739, 12),
                                                                                  (1411, 5739, 13),
                                                                                  (1601, 5739, 14),
                                                                                  (1792, 5739, 15),
                                                                                  (58, 6292, 3),
                                                                                  (121, 6292, 4),
                                                                                  (277, 6292, 5),
                                                                                  (394, 6292, 6),
                                                                                  (540, 6292, 7),
                                                                                  (703, 6292, 8),
                                                                                  (869, 6292, 9),
                                                                                  (1044, 6292, 10),
                                                                                  (1222, 6292, 12),
                                                                                  (1412, 6292, 13),
                                                                                  (1602, 6292, 14),
                                                                                  (1793, 6292, 15),
                                                                                  (55, 6302, 3),
                                                                                  (118, 6302, 4),
                                                                                  (278, 6302, 5),
                                                                                  (395, 6302, 6),
                                                                                  (541, 6302, 7),
                                                                                  (704, 6302, 8),
                                                                                  (870, 6302, 9),
                                                                                  (1045, 6302, 10),
                                                                                  (1223, 6302, 12),
                                                                                  (1413, 6302, 13),
                                                                                  (1603, 6302, 14),
                                                                                  (1794, 6302, 15),
                                                                                  (57, 6303, 3),
                                                                                  (120, 6303, 4),
                                                                                  (279, 6303, 5),
                                                                                  (396, 6303, 6),
                                                                                  (542, 6303, 7),
                                                                                  (705, 6303, 8),
                                                                                  (871, 6303, 9),
                                                                                  (1046, 6303, 10),
                                                                                  (1224, 6303, 12),
                                                                                  (1414, 6303, 13),
                                                                                  (1604, 6303, 14),
                                                                                  (1795, 6303, 15),
                                                                                  (51, 6325, 3),
                                                                                  (114, 6325, 4),
                                                                                  (280, 6325, 5),
                                                                                  (397, 6325, 6),
                                                                                  (543, 6325, 7),
                                                                                  (706, 6325, 8),
                                                                                  (872, 6325, 9),
                                                                                  (1047, 6325, 10),
                                                                                  (1225, 6325, 12),
                                                                                  (1415, 6325, 13),
                                                                                  (1605, 6325, 14),
                                                                                  (1796, 6325, 15),
                                                                                  (60, 6995, 3),
                                                                                  (123, 6995, 4),
                                                                                  (281, 6995, 5),
                                                                                  (398, 6995, 6),
                                                                                  (544, 6995, 7),
                                                                                  (707, 6995, 8),
                                                                                  (873, 6995, 9),
                                                                                  (1048, 6995, 10),
                                                                                  (1226, 6995, 12),
                                                                                  (1416, 6995, 13),
                                                                                  (1606, 6995, 14),
                                                                                  (1797, 6995, 15),
                                                                                  (282, 7790, 5),
                                                                                  (399, 7790, 6),
                                                                                  (545, 7790, 7),
                                                                                  (708, 7790, 8),
                                                                                  (874, 7790, 9),
                                                                                  (1049, 7790, 10),
                                                                                  (1227, 7790, 12),
                                                                                  (1417, 7790, 13),
                                                                                  (1607, 7790, 14),
                                                                                  (1798, 7790, 15),
                                                                                  (158, 7835, 4),
                                                                                  (283, 7835, 5),
                                                                                  (400, 7835, 6),
                                                                                  (546, 7835, 7),
                                                                                  (709, 7835, 8),
                                                                                  (875, 7835, 9),
                                                                                  (1050, 7835, 10),
                                                                                  (1228, 7835, 12),
                                                                                  (1418, 7835, 13),
                                                                                  (1608, 7835, 14),
                                                                                  (1799, 7835, 15),
                                                                                  (56, 8017, 3),
                                                                                  (119, 8017, 4),
                                                                                  (284, 8017, 5),
                                                                                  (401, 8017, 6),
                                                                                  (547, 8017, 7),
                                                                                  (710, 8017, 8),
                                                                                  (876, 8017, 9),
                                                                                  (1051, 8017, 10),
                                                                                  (1229, 8017, 12),
                                                                                  (1419, 8017, 13),
                                                                                  (1609, 8017, 14),
                                                                                  (1800, 8017, 15),
                                                                                  (62, 8378, 3),
                                                                                  (125, 8378, 4),
                                                                                  (285, 8378, 5),
                                                                                  (402, 8378, 6),
                                                                                  (548, 8378, 7),
                                                                                  (711, 8378, 8),
                                                                                  (877, 8378, 9),
                                                                                  (1052, 8378, 10),
                                                                                  (1230, 8378, 12),
                                                                                  (1420, 8378, 13),
                                                                                  (1610, 8378, 14),
                                                                                  (1801, 8378, 15),
                                                                                  (845, 9145, 8),
                                                                                  (1018, 9145, 9),
                                                                                  (1193, 9145, 10),
                                                                                  (1371, 9145, 12),
                                                                                  (1561, 9145, 13),
                                                                                  (1751, 9145, 14),
                                                                                  (1942, 9145, 15),
                                                                                  (150, 10311, 4),
                                                                                  (286, 10311, 5),
                                                                                  (403, 10311, 6),
                                                                                  (549, 10311, 7),
                                                                                  (712, 10311, 8),
                                                                                  (878, 10311, 9),
                                                                                  (1053, 10311, 10),
                                                                                  (1231, 10311, 12),
                                                                                  (1421, 10311, 13),
                                                                                  (1611, 10311, 14),
                                                                                  (1802, 10311, 15),
                                                                                  (142, 11097, 4),
                                                                                  (287, 11097, 5),
                                                                                  (404, 11097, 6),
                                                                                  (550, 11097, 7),
                                                                                  (713, 11097, 8),
                                                                                  (879, 11097, 9),
                                                                                  (1054, 11097, 10),
                                                                                  (1232, 11097, 12),
                                                                                  (1422, 11097, 13),
                                                                                  (1612, 11097, 14),
                                                                                  (1803, 11097, 15),
                                                                                  (1, 11992, 3),
                                                                                  (64, 11992, 4),
                                                                                  (288, 11992, 5),
                                                                                  (405, 11992, 6),
                                                                                  (551, 11992, 7),
                                                                                  (714, 11992, 8),
                                                                                  (880, 11992, 9),
                                                                                  (1055, 11992, 10),
                                                                                  (1233, 11992, 12),
                                                                                  (1423, 11992, 13),
                                                                                  (1613, 11992, 14),
                                                                                  (1804, 11992, 15),
                                                                                  (157, 12366, 4),
                                                                                  (289, 12366, 5),
                                                                                  (406, 12366, 6),
                                                                                  (552, 12366, 7),
                                                                                  (715, 12366, 8),
                                                                                  (881, 12366, 9),
                                                                                  (1056, 12366, 10),
                                                                                  (1234, 12366, 12),
                                                                                  (1424, 12366, 13),
                                                                                  (1614, 12366, 14),
                                                                                  (1805, 12366, 15),
                                                                                  (151, 12706, 4),
                                                                                  (290, 12706, 5),
                                                                                  (407, 12706, 6),
                                                                                  (553, 12706, 7),
                                                                                  (716, 12706, 8),
                                                                                  (882, 12706, 9),
                                                                                  (1057, 12706, 10),
                                                                                  (1235, 12706, 12),
                                                                                  (1425, 12706, 13),
                                                                                  (1615, 12706, 14),
                                                                                  (1806, 12706, 15),
                                                                                  (59, 12928, 3),
                                                                                  (122, 12928, 4),
                                                                                  (291, 12928, 5),
                                                                                  (408, 12928, 6),
                                                                                  (554, 12928, 7),
                                                                                  (717, 12928, 8),
                                                                                  (883, 12928, 9),
                                                                                  (1058, 12928, 10),
                                                                                  (1236, 12928, 12),
                                                                                  (1426, 12928, 13),
                                                                                  (1616, 12928, 14),
                                                                                  (1807, 12928, 15),
                                                                                  (134, 12992, 4),
                                                                                  (292, 12992, 5),
                                                                                  (409, 12992, 6),
                                                                                  (555, 12992, 7),
                                                                                  (718, 12992, 8),
                                                                                  (884, 12992, 9),
                                                                                  (1059, 12992, 10),
                                                                                  (1237, 12992, 12),
                                                                                  (1427, 12992, 13),
                                                                                  (1617, 12992, 14),
                                                                                  (1808, 12992, 15),
                                                                                  (153, 13029, 4),
                                                                                  (293, 13029, 5),
                                                                                  (410, 13029, 6),
                                                                                  (556, 13029, 7),
                                                                                  (719, 13029, 8),
                                                                                  (885, 13029, 9),
                                                                                  (1060, 13029, 10),
                                                                                  (1238, 13029, 12),
                                                                                  (1428, 13029, 13),
                                                                                  (1618, 13029, 14),
                                                                                  (1809, 13029, 15),
                                                                                  (31, 13074, 3),
                                                                                  (94, 13074, 4),
                                                                                  (294, 13074, 5),
                                                                                  (411, 13074, 6),
                                                                                  (557, 13074, 7),
                                                                                  (720, 13074, 8),
                                                                                  (886, 13074, 9),
                                                                                  (1061, 13074, 10),
                                                                                  (1239, 13074, 12),
                                                                                  (1429, 13074, 13),
                                                                                  (1619, 13074, 14),
                                                                                  (1810, 13074, 15),
                                                                                  (34, 13078, 3),
                                                                                  (97, 13078, 4),
                                                                                  (295, 13078, 5),
                                                                                  (412, 13078, 6),
                                                                                  (558, 13078, 7),
                                                                                  (721, 13078, 8),
                                                                                  (887, 13078, 9),
                                                                                  (1062, 13078, 10),
                                                                                  (1240, 13078, 12),
                                                                                  (1430, 13078, 13),
                                                                                  (1620, 13078, 14),
                                                                                  (1811, 13078, 15),
                                                                                  (156, 13086, 4),
                                                                                  (296, 13086, 5),
                                                                                  (413, 13086, 6),
                                                                                  (559, 13086, 7),
                                                                                  (722, 13086, 8),
                                                                                  (888, 13086, 9),
                                                                                  (1063, 13086, 10),
                                                                                  (1241, 13086, 12),
                                                                                  (1431, 13086, 13),
                                                                                  (1621, 13086, 14),
                                                                                  (1812, 13086, 15),
                                                                                  (159, 13160, 4),
                                                                                  (297, 13160, 5),
                                                                                  (414, 13160, 6),
                                                                                  (560, 13160, 7),
                                                                                  (723, 13160, 8),
                                                                                  (889, 13160, 9),
                                                                                  (1064, 13160, 10),
                                                                                  (1242, 13160, 12),
                                                                                  (1432, 13160, 13),
                                                                                  (1622, 13160, 14),
                                                                                  (1813, 13160, 15),
                                                                                  (129, 13170, 4),
                                                                                  (140, 13170, 4),
                                                                                  (298, 13170, 5),
                                                                                  (415, 13170, 6),
                                                                                  (561, 13170, 7),
                                                                                  (724, 13170, 8),
                                                                                  (890, 13170, 9),
                                                                                  (1065, 13170, 10),
                                                                                  (1243, 13170, 12),
                                                                                  (1433, 13170, 13),
                                                                                  (1623, 13170, 14),
                                                                                  (1814, 13170, 15),
                                                                                  (416, 13177, 6),
                                                                                  (562, 13177, 7),
                                                                                  (725, 13177, 8),
                                                                                  (891, 13177, 9),
                                                                                  (1066, 13177, 10),
                                                                                  (1244, 13177, 12),
                                                                                  (1434, 13177, 13),
                                                                                  (1624, 13177, 14),
                                                                                  (1815, 13177, 15),
                                                                                  (139, 13209, 4),
                                                                                  (299, 13209, 5),
                                                                                  (417, 13209, 6),
                                                                                  (563, 13209, 7),
                                                                                  (726, 13209, 8),
                                                                                  (892, 13209, 9),
                                                                                  (1067, 13209, 10),
                                                                                  (1245, 13209, 12),
                                                                                  (1435, 13209, 13),
                                                                                  (1625, 13209, 14),
                                                                                  (1816, 13209, 15),
                                                                                  (300, 13865, 5),
                                                                                  (418, 13865, 6),
                                                                                  (564, 13865, 7),
                                                                                  (727, 13865, 8),
                                                                                  (893, 13865, 9),
                                                                                  (1068, 13865, 10),
                                                                                  (1246, 13865, 12),
                                                                                  (1436, 13865, 13),
                                                                                  (1626, 13865, 14),
                                                                                  (1817, 13865, 15),
                                                                                  (63, 13942, 3),
                                                                                  (126, 13942, 4),
                                                                                  (301, 13942, 5),
                                                                                  (419, 13942, 6),
                                                                                  (565, 13942, 7),
                                                                                  (728, 13942, 8),
                                                                                  (894, 13942, 9),
                                                                                  (1069, 13942, 10),
                                                                                  (1247, 13942, 12),
                                                                                  (1437, 13942, 13),
                                                                                  (1627, 13942, 14),
                                                                                  (1818, 13942, 15),
                                                                                  (1971, 14038, 15),
                                                                                  (149, 14050, 4),
                                                                                  (302, 14050, 5),
                                                                                  (420, 14050, 6),
                                                                                  (566, 14050, 7),
                                                                                  (729, 14050, 8),
                                                                                  (895, 14050, 9),
                                                                                  (1070, 14050, 10),
                                                                                  (1248, 14050, 12),
                                                                                  (1438, 14050, 13),
                                                                                  (1628, 14050, 14),
                                                                                  (1819, 14050, 15),
                                                                                  (160, 14102, 4),
                                                                                  (303, 14102, 5),
                                                                                  (421, 14102, 6),
                                                                                  (567, 14102, 7),
                                                                                  (730, 14102, 8),
                                                                                  (896, 14102, 9),
                                                                                  (1071, 14102, 10),
                                                                                  (1249, 14102, 12),
                                                                                  (1439, 14102, 13),
                                                                                  (1629, 14102, 14),
                                                                                  (1820, 14102, 15),
                                                                                  (146, 14122, 4),
                                                                                  (304, 14122, 5),
                                                                                  (422, 14122, 6),
                                                                                  (568, 14122, 7),
                                                                                  (731, 14122, 8),
                                                                                  (897, 14122, 9),
                                                                                  (1072, 14122, 10),
                                                                                  (1250, 14122, 12),
                                                                                  (1440, 14122, 13),
                                                                                  (1630, 14122, 14),
                                                                                  (1821, 14122, 15),
                                                                                  (131, 14330, 4),
                                                                                  (305, 14330, 5),
                                                                                  (423, 14330, 6),
                                                                                  (569, 14330, 7),
                                                                                  (732, 14330, 8),
                                                                                  (898, 14330, 9),
                                                                                  (1073, 14330, 10),
                                                                                  (1251, 14330, 12),
                                                                                  (1441, 14330, 13),
                                                                                  (1631, 14330, 14),
                                                                                  (1822, 14330, 15),
                                                                                  (5, 14536, 3),
                                                                                  (68, 14536, 4),
                                                                                  (306, 14536, 5),
                                                                                  (424, 14536, 6),
                                                                                  (570, 14536, 7),
                                                                                  (733, 14536, 8),
                                                                                  (899, 14536, 9),
                                                                                  (1074, 14536, 10),
                                                                                  (1252, 14536, 12),
                                                                                  (1442, 14536, 13),
                                                                                  (1632, 14536, 14),
                                                                                  (1823, 14536, 15),
                                                                                  (136, 20009, 4),
                                                                                  (307, 20009, 5),
                                                                                  (425, 20009, 6),
                                                                                  (571, 20009, 7),
                                                                                  (734, 20009, 8),
                                                                                  (900, 20009, 9),
                                                                                  (1075, 20009, 10),
                                                                                  (1253, 20009, 12),
                                                                                  (1443, 20009, 13),
                                                                                  (1633, 20009, 14),
                                                                                  (1824, 20009, 15),
                                                                                  (133, 20010, 4),
                                                                                  (308, 20010, 5),
                                                                                  (426, 20010, 6),
                                                                                  (572, 20010, 7),
                                                                                  (735, 20010, 8),
                                                                                  (901, 20010, 9),
                                                                                  (1076, 20010, 10),
                                                                                  (1254, 20010, 12),
                                                                                  (1444, 20010, 13),
                                                                                  (1634, 20010, 14),
                                                                                  (1825, 20010, 15),
                                                                                  (14, 20012, 3),
                                                                                  (77, 20012, 4),
                                                                                  (309, 20012, 5),
                                                                                  (427, 20012, 6),
                                                                                  (573, 20012, 7),
                                                                                  (736, 20012, 8),
                                                                                  (902, 20012, 9),
                                                                                  (1077, 20012, 10),
                                                                                  (1255, 20012, 12),
                                                                                  (1445, 20012, 13),
                                                                                  (1635, 20012, 14),
                                                                                  (1826, 20012, 15),
                                                                                  (19, 20013, 3),
                                                                                  (82, 20013, 4),
                                                                                  (310, 20013, 5),
                                                                                  (428, 20013, 6),
                                                                                  (574, 20013, 7),
                                                                                  (737, 20013, 8),
                                                                                  (903, 20013, 9),
                                                                                  (1078, 20013, 10),
                                                                                  (1256, 20013, 12),
                                                                                  (1446, 20013, 13),
                                                                                  (1636, 20013, 14),
                                                                                  (1827, 20013, 15),
                                                                                  (1974, 20151, 15),
                                                                                  (676, 20216, 7),
                                                                                  (738, 20216, 8),
                                                                                  (1005, 20216, 9),
                                                                                  (1180, 20216, 10),
                                                                                  (1358, 20216, 12),
                                                                                  (1548, 20216, 13),
                                                                                  (1738, 20216, 14),
                                                                                  (1929, 20216, 15),
                                                                                  (154, 20260, 4),
                                                                                  (311, 20260, 5),
                                                                                  (429, 20260, 6),
                                                                                  (575, 20260, 7),
                                                                                  (739, 20260, 8),
                                                                                  (904, 20260, 9),
                                                                                  (1079, 20260, 10),
                                                                                  (1257, 20260, 12),
                                                                                  (1447, 20260, 13),
                                                                                  (1637, 20260, 14),
                                                                                  (1828, 20260, 15),
                                                                                  (18, 20408, 3),
                                                                                  (81, 20408, 4),
                                                                                  (312, 20408, 5),
                                                                                  (430, 20408, 6),
                                                                                  (576, 20408, 7),
                                                                                  (740, 20408, 8),
                                                                                  (905, 20408, 9),
                                                                                  (1080, 20408, 10),
                                                                                  (1258, 20408, 12),
                                                                                  (1448, 20408, 13),
                                                                                  (1638, 20408, 14),
                                                                                  (1829, 20408, 15),
                                                                                  (15, 20461, 3),
                                                                                  (78, 20461, 4),
                                                                                  (313, 20461, 5),
                                                                                  (431, 20461, 6),
                                                                                  (577, 20461, 7),
                                                                                  (741, 20461, 8),
                                                                                  (906, 20461, 9),
                                                                                  (1081, 20461, 10),
                                                                                  (1259, 20461, 12),
                                                                                  (1449, 20461, 13),
                                                                                  (1639, 20461, 14),
                                                                                  (1830, 20461, 15),
                                                                                  (35, 21075, 3),
                                                                                  (98, 21075, 4),
                                                                                  (314, 21075, 5),
                                                                                  (432, 21075, 6),
                                                                                  (578, 21075, 7),
                                                                                  (742, 21075, 8),
                                                                                  (907, 21075, 9),
                                                                                  (1082, 21075, 10),
                                                                                  (1260, 21075, 12),
                                                                                  (1450, 21075, 13),
                                                                                  (1640, 21075, 14),
                                                                                  (1831, 21075, 15),
                                                                                  (30, 21076, 3),
                                                                                  (93, 21076, 4),
                                                                                  (315, 21076, 5),
                                                                                  (433, 21076, 6),
                                                                                  (579, 21076, 7),
                                                                                  (743, 21076, 8),
                                                                                  (908, 21076, 9),
                                                                                  (1083, 21076, 10),
                                                                                  (1261, 21076, 12),
                                                                                  (1451, 21076, 13),
                                                                                  (1641, 21076, 14),
                                                                                  (1832, 21076, 15),
                                                                                  (3, 21594, 3),
                                                                                  (66, 21594, 4),
                                                                                  (316, 21594, 5),
                                                                                  (434, 21594, 6),
                                                                                  (580, 21594, 7),
                                                                                  (744, 21594, 8),
                                                                                  (909, 21594, 9),
                                                                                  (1084, 21594, 10),
                                                                                  (1262, 21594, 12),
                                                                                  (1452, 21594, 13),
                                                                                  (1642, 21594, 14),
                                                                                  (1833, 21594, 15),
                                                                                  (16, 22104, 3),
                                                                                  (79, 22104, 4),
                                                                                  (317, 22104, 5),
                                                                                  (435, 22104, 6),
                                                                                  (581, 22104, 7),
                                                                                  (745, 22104, 8),
                                                                                  (910, 22104, 9),
                                                                                  (1085, 22104, 10),
                                                                                  (1263, 22104, 12),
                                                                                  (1453, 22104, 13),
                                                                                  (1643, 22104, 14),
                                                                                  (1834, 22104, 15),
                                                                                  (37, 22126, 3),
                                                                                  (100, 22126, 4),
                                                                                  (318, 22126, 5),
                                                                                  (436, 22126, 6),
                                                                                  (582, 22126, 7),
                                                                                  (746, 22126, 8),
                                                                                  (911, 22126, 9),
                                                                                  (1086, 22126, 10),
                                                                                  (1264, 22126, 12),
                                                                                  (1454, 22126, 13),
                                                                                  (1644, 22126, 14),
                                                                                  (1835, 22126, 15),
                                                                                  (9, 22246, 3),
                                                                                  (72, 22246, 4),
                                                                                  (319, 22246, 5),
                                                                                  (437, 22246, 6),
                                                                                  (583, 22246, 7),
                                                                                  (747, 22246, 8),
                                                                                  (912, 22246, 9),
                                                                                  (1087, 22246, 10),
                                                                                  (1265, 22246, 12),
                                                                                  (1455, 22246, 13),
                                                                                  (1645, 22246, 14),
                                                                                  (1836, 22246, 15),
                                                                                  (6, 22262, 3),
                                                                                  (69, 22262, 4),
                                                                                  (320, 22262, 5),
                                                                                  (438, 22262, 6),
                                                                                  (584, 22262, 7),
                                                                                  (748, 22262, 8),
                                                                                  (913, 22262, 9),
                                                                                  (1088, 22262, 10),
                                                                                  (1266, 22262, 12),
                                                                                  (1456, 22262, 13),
                                                                                  (1646, 22262, 14),
                                                                                  (1837, 22262, 15),
                                                                                  (11, 22347, 3),
                                                                                  (74, 22347, 4),
                                                                                  (321, 22347, 5),
                                                                                  (439, 22347, 6),
                                                                                  (585, 22347, 7),
                                                                                  (749, 22347, 8),
                                                                                  (914, 22347, 9),
                                                                                  (1089, 22347, 10),
                                                                                  (1267, 22347, 12),
                                                                                  (1457, 22347, 13),
                                                                                  (1647, 22347, 14),
                                                                                  (1838, 22347, 15),
                                                                                  (7, 22514, 3),
                                                                                  (70, 22514, 4),
                                                                                  (322, 22514, 5),
                                                                                  (440, 22514, 6),
                                                                                  (586, 22514, 7),
                                                                                  (750, 22514, 8),
                                                                                  (915, 22514, 9),
                                                                                  (1090, 22514, 10),
                                                                                  (1268, 22514, 12),
                                                                                  (1458, 22514, 13),
                                                                                  (1648, 22514, 14),
                                                                                  (1839, 22514, 15),
                                                                                  (8, 22515, 3),
                                                                                  (71, 22515, 4),
                                                                                  (323, 22515, 5),
                                                                                  (441, 22515, 6),
                                                                                  (587, 22515, 7),
                                                                                  (751, 22515, 8),
                                                                                  (916, 22515, 9),
                                                                                  (1091, 22515, 10),
                                                                                  (1269, 22515, 12),
                                                                                  (1459, 22515, 13),
                                                                                  (1649, 22515, 14),
                                                                                  (1840, 22515, 15),
                                                                                  (10, 22516, 3),
                                                                                  (73, 22516, 4),
                                                                                  (324, 22516, 5),
                                                                                  (442, 22516, 6),
                                                                                  (588, 22516, 7),
                                                                                  (752, 22516, 8),
                                                                                  (917, 22516, 9),
                                                                                  (1092, 22516, 10),
                                                                                  (1270, 22516, 12),
                                                                                  (1460, 22516, 13),
                                                                                  (1650, 22516, 14),
                                                                                  (1841, 22516, 15),
                                                                                  (12, 22518, 3),
                                                                                  (75, 22518, 4),
                                                                                  (325, 22518, 5),
                                                                                  (443, 22518, 6),
                                                                                  (589, 22518, 7),
                                                                                  (753, 22518, 8),
                                                                                  (918, 22518, 9),
                                                                                  (1093, 22518, 10),
                                                                                  (1271, 22518, 12),
                                                                                  (1461, 22518, 13),
                                                                                  (1651, 22518, 14),
                                                                                  (1842, 22518, 15),
                                                                                  (13, 22519, 3),
                                                                                  (76, 22519, 4),
                                                                                  (326, 22519, 5),
                                                                                  (444, 22519, 6),
                                                                                  (590, 22519, 7),
                                                                                  (754, 22519, 8),
                                                                                  (919, 22519, 9),
                                                                                  (1094, 22519, 10),
                                                                                  (1272, 22519, 12),
                                                                                  (1462, 22519, 13),
                                                                                  (1652, 22519, 14),
                                                                                  (1843, 22519, 15),
                                                                                  (327, 110129, 5),
                                                                                  (445, 110129, 6),
                                                                                  (591, 110129, 7),
                                                                                  (755, 110129, 8),
                                                                                  (920, 110129, 9),
                                                                                  (1095, 110129, 10),
                                                                                  (1273, 110129, 12),
                                                                                  (1463, 110129, 13),
                                                                                  (1653, 110129, 14),
                                                                                  (1844, 110129, 15),
                                                                                  (328, 110130, 5),
                                                                                  (446, 110130, 6),
                                                                                  (592, 110130, 7),
                                                                                  (756, 110130, 8),
                                                                                  (921, 110130, 9),
                                                                                  (1096, 110130, 10),
                                                                                  (1274, 110130, 12),
                                                                                  (1464, 110130, 13),
                                                                                  (1654, 110130, 14),
                                                                                  (1845, 110130, 15),
                                                                                  (374, 110131, 5),
                                                                                  (447, 110131, 6),
                                                                                  (593, 110131, 7),
                                                                                  (757, 110131, 8),
                                                                                  (922, 110131, 9),
                                                                                  (1097, 110131, 10),
                                                                                  (1275, 110131, 12),
                                                                                  (1465, 110131, 13),
                                                                                  (1655, 110131, 14),
                                                                                  (1846, 110131, 15),
                                                                                  (161, 110332, 4),
                                                                                  (329, 110332, 5),
                                                                                  (448, 110332, 6),
                                                                                  (594, 110332, 7),
                                                                                  (758, 110332, 8),
                                                                                  (923, 110332, 9),
                                                                                  (1098, 110332, 10),
                                                                                  (1276, 110332, 12),
                                                                                  (1466, 110332, 13),
                                                                                  (1656, 110332, 14),
                                                                                  (1847, 110332, 15),
                                                                                  (330, 110334, 5),
                                                                                  (449, 110334, 6),
                                                                                  (595, 110334, 7),
                                                                                  (759, 110334, 8),
                                                                                  (924, 110334, 9),
                                                                                  (1099, 110334, 10),
                                                                                  (1277, 110334, 12),
                                                                                  (1467, 110334, 13),
                                                                                  (1657, 110334, 14),
                                                                                  (1848, 110334, 15),
                                                                                  (675, 110335, 7),
                                                                                  (760, 110335, 8),
                                                                                  (1004, 110335, 9),
                                                                                  (1179, 110335, 10),
                                                                                  (1357, 110335, 12),
                                                                                  (1547, 110335, 13),
                                                                                  (1737, 110335, 14),
                                                                                  (1928, 110335, 15),
                                                                                  (147, 110505, 4),
                                                                                  (331, 110505, 5),
                                                                                  (450, 110505, 6),
                                                                                  (596, 110505, 7),
                                                                                  (761, 110505, 8),
                                                                                  (925, 110505, 9),
                                                                                  (1100, 110505, 10),
                                                                                  (1278, 110505, 12),
                                                                                  (1468, 110505, 13),
                                                                                  (1658, 110505, 14),
                                                                                  (1849, 110505, 15),
                                                                                  (332, 110524, 5),
                                                                                  (451, 110524, 6),
                                                                                  (597, 110524, 7),
                                                                                  (762, 110524, 8),
                                                                                  (926, 110524, 9),
                                                                                  (1101, 110524, 10),
                                                                                  (1279, 110524, 12),
                                                                                  (1469, 110524, 13),
                                                                                  (1659, 110524, 14),
                                                                                  (1850, 110524, 15),
                                                                                  (372, 110525, 5),
                                                                                  (452, 110525, 6),
                                                                                  (598, 110525, 7),
                                                                                  (763, 110525, 8),
                                                                                  (927, 110525, 9),
                                                                                  (1102, 110525, 10),
                                                                                  (1280, 110525, 12),
                                                                                  (1470, 110525, 13),
                                                                                  (1660, 110525, 14),
                                                                                  (1851, 110525, 15),
                                                                                  (373, 110526, 5),
                                                                                  (453, 110526, 6),
                                                                                  (599, 110526, 7),
                                                                                  (764, 110526, 8),
                                                                                  (928, 110526, 9),
                                                                                  (1103, 110526, 10),
                                                                                  (1281, 110526, 12),
                                                                                  (1471, 110526, 13),
                                                                                  (1661, 110526, 14),
                                                                                  (1852, 110526, 15),
                                                                                  (1025, 110544, 9),
                                                                                  (1200, 110544, 10),
                                                                                  (1378, 110544, 12),
                                                                                  (1568, 110544, 13),
                                                                                  (1758, 110544, 14),
                                                                                  (1949, 110544, 15),
                                                                                  (143, 110603, 4),
                                                                                  (333, 110603, 5),
                                                                                  (454, 110603, 6),
                                                                                  (600, 110603, 7),
                                                                                  (765, 110603, 8),
                                                                                  (929, 110603, 9),
                                                                                  (1104, 110603, 10),
                                                                                  (1282, 110603, 12),
                                                                                  (1472, 110603, 13),
                                                                                  (1662, 110603, 14),
                                                                                  (1853, 110603, 15),
                                                                                  (141, 110620, 4),
                                                                                  (334, 110620, 5),
                                                                                  (455, 110620, 6),
                                                                                  (601, 110620, 7),
                                                                                  (766, 110620, 8),
                                                                                  (930, 110620, 9),
                                                                                  (1105, 110620, 10),
                                                                                  (1283, 110620, 12),
                                                                                  (1473, 110620, 13),
                                                                                  (1663, 110620, 14),
                                                                                  (1854, 110620, 15),
                                                                                  (137, 110621, 4),
                                                                                  (335, 110621, 5),
                                                                                  (456, 110621, 6),
                                                                                  (602, 110621, 7),
                                                                                  (767, 110621, 8),
                                                                                  (931, 110621, 9),
                                                                                  (1106, 110621, 10),
                                                                                  (1284, 110621, 12),
                                                                                  (1474, 110621, 13),
                                                                                  (1664, 110621, 14),
                                                                                  (1855, 110621, 15),
                                                                                  (47, 110633, 3),
                                                                                  (110, 110633, 4),
                                                                                  (336, 110633, 5),
                                                                                  (457, 110633, 6),
                                                                                  (603, 110633, 7),
                                                                                  (768, 110633, 8),
                                                                                  (932, 110633, 9),
                                                                                  (1107, 110633, 10),
                                                                                  (1285, 110633, 12),
                                                                                  (1475, 110633, 13),
                                                                                  (1665, 110633, 14),
                                                                                  (1856, 110633, 15),
                                                                                  (138, 110634, 4),
                                                                                  (337, 110634, 5),
                                                                                  (458, 110634, 6),
                                                                                  (604, 110634, 7),
                                                                                  (769, 110634, 8),
                                                                                  (933, 110634, 9),
                                                                                  (1108, 110634, 10),
                                                                                  (1286, 110634, 12),
                                                                                  (1476, 110634, 13),
                                                                                  (1666, 110634, 14),
                                                                                  (1857, 110634, 15),
                                                                                  (144, 110635, 4),
                                                                                  (338, 110635, 5),
                                                                                  (459, 110635, 6),
                                                                                  (605, 110635, 7),
                                                                                  (770, 110635, 8),
                                                                                  (934, 110635, 9),
                                                                                  (1109, 110635, 10),
                                                                                  (1287, 110635, 12),
                                                                                  (1477, 110635, 13),
                                                                                  (1667, 110635, 14),
                                                                                  (1858, 110635, 15),
                                                                                  (145, 110636, 4),
                                                                                  (339, 110636, 5),
                                                                                  (460, 110636, 6),
                                                                                  (606, 110636, 7),
                                                                                  (771, 110636, 8),
                                                                                  (935, 110636, 9),
                                                                                  (1110, 110636, 10),
                                                                                  (1288, 110636, 12),
                                                                                  (1478, 110636, 13),
                                                                                  (1668, 110636, 14),
                                                                                  (1859, 110636, 15),
                                                                                  (848, 110716, 8),
                                                                                  (1019, 110716, 9),
                                                                                  (1194, 110716, 10),
                                                                                  (1372, 110716, 12),
                                                                                  (1562, 110716, 13),
                                                                                  (1752, 110716, 14),
                                                                                  (1943, 110716, 15),
                                                                                  (849, 110717, 8),
                                                                                  (1020, 110717, 9),
                                                                                  (1195, 110717, 10),
                                                                                  (1373, 110717, 12),
                                                                                  (1563, 110717, 13),
                                                                                  (1753, 110717, 14),
                                                                                  (1944, 110717, 15),
                                                                                  (1972, 110910, 15),
                                                                                  (130, 111012, 4),
                                                                                  (340, 111012, 5),
                                                                                  (461, 111012, 6),
                                                                                  (607, 111012, 7),
                                                                                  (772, 111012, 8),
                                                                                  (936, 111012, 9),
                                                                                  (1111, 111012, 10),
                                                                                  (1289, 111012, 12),
                                                                                  (1479, 111012, 13),
                                                                                  (1669, 111012, 14),
                                                                                  (1860, 111012, 15),
                                                                                  (844, 111023, 8),
                                                                                  (132, 111027, 4),
                                                                                  (341, 111027, 5),
                                                                                  (462, 111027, 6),
                                                                                  (608, 111027, 7),
                                                                                  (773, 111027, 8),
                                                                                  (937, 111027, 9),
                                                                                  (1112, 111027, 10),
                                                                                  (1290, 111027, 12),
                                                                                  (1480, 111027, 13),
                                                                                  (1670, 111027, 14),
                                                                                  (1861, 111027, 15),
                                                                                  (42, 111108, 3),
                                                                                  (105, 111108, 4),
                                                                                  (342, 111108, 5),
                                                                                  (463, 111108, 6),
                                                                                  (609, 111108, 7),
                                                                                  (774, 111108, 8),
                                                                                  (938, 111108, 9),
                                                                                  (1113, 111108, 10),
                                                                                  (1291, 111108, 12),
                                                                                  (1481, 111108, 13),
                                                                                  (1671, 111108, 14),
                                                                                  (1862, 111108, 15),
                                                                                  (45, 111120, 3),
                                                                                  (108, 111120, 4),
                                                                                  (343, 111120, 5),
                                                                                  (464, 111120, 6),
                                                                                  (610, 111120, 7),
                                                                                  (775, 111120, 8),
                                                                                  (939, 111120, 9),
                                                                                  (1114, 111120, 10),
                                                                                  (1292, 111120, 12),
                                                                                  (1482, 111120, 13),
                                                                                  (1672, 111120, 14),
                                                                                  (1863, 111120, 15),
                                                                                  (127, 111122, 4),
                                                                                  (344, 111122, 5),
                                                                                  (465, 111122, 6),
                                                                                  (611, 111122, 7),
                                                                                  (776, 111122, 8),
                                                                                  (940, 111122, 9),
                                                                                  (1115, 111122, 10),
                                                                                  (1293, 111122, 12),
                                                                                  (1483, 111122, 13),
                                                                                  (1673, 111122, 14),
                                                                                  (1864, 111122, 15),
                                                                                  (345, 111123, 5),
                                                                                  (466, 111123, 6),
                                                                                  (612, 111123, 7),
                                                                                  (777, 111123, 8),
                                                                                  (941, 111123, 9),
                                                                                  (1116, 111123, 10),
                                                                                  (1294, 111123, 12),
                                                                                  (1484, 111123, 13),
                                                                                  (1674, 111123, 14),
                                                                                  (1865, 111123, 15),
                                                                                  (346, 111124, 5),
                                                                                  (467, 111124, 6),
                                                                                  (613, 111124, 7),
                                                                                  (778, 111124, 8),
                                                                                  (942, 111124, 9),
                                                                                  (1117, 111124, 10),
                                                                                  (1295, 111124, 12),
                                                                                  (1485, 111124, 13),
                                                                                  (1675, 111124, 14),
                                                                                  (1866, 111124, 15),
                                                                                  (347, 111126, 5),
                                                                                  (468, 111126, 6),
                                                                                  (614, 111126, 7),
                                                                                  (779, 111126, 8),
                                                                                  (943, 111126, 9),
                                                                                  (1118, 111126, 10),
                                                                                  (1296, 111126, 12),
                                                                                  (1486, 111126, 13),
                                                                                  (1676, 111126, 14),
                                                                                  (1867, 111126, 15),
                                                                                  (348, 111128, 5),
                                                                                  (469, 111128, 6),
                                                                                  (615, 111128, 7),
                                                                                  (780, 111128, 8),
                                                                                  (944, 111128, 9),
                                                                                  (1119, 111128, 10),
                                                                                  (1297, 111128, 12),
                                                                                  (1487, 111128, 13),
                                                                                  (1677, 111128, 14),
                                                                                  (1868, 111128, 15),
                                                                                  (349, 111130, 5),
                                                                                  (470, 111130, 6),
                                                                                  (616, 111130, 7),
                                                                                  (781, 111130, 8),
                                                                                  (945, 111130, 9),
                                                                                  (1120, 111130, 10),
                                                                                  (1298, 111130, 12),
                                                                                  (1488, 111130, 13),
                                                                                  (1678, 111130, 14),
                                                                                  (1869, 111130, 15),
                                                                                  (375, 111131, 5),
                                                                                  (471, 111131, 6),
                                                                                  (617, 111131, 7),
                                                                                  (782, 111131, 8),
                                                                                  (946, 111131, 9),
                                                                                  (1121, 111131, 10),
                                                                                  (1299, 111131, 12),
                                                                                  (1489, 111131, 13),
                                                                                  (1679, 111131, 14),
                                                                                  (1870, 111131, 15),
                                                                                  (1023, 111132, 9),
                                                                                  (1198, 111132, 10),
                                                                                  (1376, 111132, 12),
                                                                                  (1566, 111132, 13),
                                                                                  (1756, 111132, 14),
                                                                                  (1947, 111132, 15),
                                                                                  (1024, 111133, 9),
                                                                                  (1199, 111133, 10),
                                                                                  (1377, 111133, 12),
                                                                                  (1567, 111133, 13),
                                                                                  (1757, 111133, 14),
                                                                                  (1948, 111133, 15),
                                                                                  (1202, 111134, 10),
                                                                                  (1380, 111134, 12),
                                                                                  (1570, 111134, 13),
                                                                                  (1760, 111134, 14),
                                                                                  (1951, 111134, 15),
                                                                                  (128, 111228, 4),
                                                                                  (472, 111228, 6),
                                                                                  (618, 111228, 7),
                                                                                  (783, 111228, 8),
                                                                                  (947, 111228, 9),
                                                                                  (1122, 111228, 10),
                                                                                  (1300, 111228, 12),
                                                                                  (1490, 111228, 13),
                                                                                  (1680, 111228, 14),
                                                                                  (1871, 111228, 15),
                                                                                  (43, 111233, 3),
                                                                                  (106, 111233, 4),
                                                                                  (351, 111233, 5),
                                                                                  (473, 111233, 6),
                                                                                  (619, 111233, 7),
                                                                                  (784, 111233, 8),
                                                                                  (948, 111233, 9),
                                                                                  (1123, 111233, 10),
                                                                                  (1301, 111233, 12),
                                                                                  (1491, 111233, 13),
                                                                                  (1681, 111233, 14),
                                                                                  (1872, 111233, 15),
                                                                                  (44, 111234, 3),
                                                                                  (107, 111234, 4),
                                                                                  (352, 111234, 5),
                                                                                  (474, 111234, 6),
                                                                                  (620, 111234, 7),
                                                                                  (785, 111234, 8),
                                                                                  (949, 111234, 9),
                                                                                  (1124, 111234, 10),
                                                                                  (1302, 111234, 12),
                                                                                  (1492, 111234, 13),
                                                                                  (1682, 111234, 14),
                                                                                  (1873, 111234, 15),
                                                                                  (353, 111236, 5),
                                                                                  (475, 111236, 6),
                                                                                  (621, 111236, 7),
                                                                                  (786, 111236, 8),
                                                                                  (950, 111236, 9),
                                                                                  (1125, 111236, 10),
                                                                                  (1303, 111236, 12),
                                                                                  (1493, 111236, 13),
                                                                                  (1683, 111236, 14),
                                                                                  (1874, 111236, 15),
                                                                                  (135, 111237, 4),
                                                                                  (354, 111237, 5),
                                                                                  (476, 111237, 6),
                                                                                  (622, 111237, 7),
                                                                                  (787, 111237, 8),
                                                                                  (951, 111237, 9),
                                                                                  (1126, 111237, 10),
                                                                                  (1304, 111237, 12),
                                                                                  (1494, 111237, 13),
                                                                                  (1684, 111237, 14),
                                                                                  (1875, 111237, 15),
                                                                                  (376, 111238, 5),
                                                                                  (477, 111238, 6),
                                                                                  (623, 111238, 7),
                                                                                  (788, 111238, 8),
                                                                                  (952, 111238, 9),
                                                                                  (1127, 111238, 10),
                                                                                  (1305, 111238, 12),
                                                                                  (1495, 111238, 13),
                                                                                  (1685, 111238, 14),
                                                                                  (1876, 111238, 15),
                                                                                  (1201, 111240, 10),
                                                                                  (1379, 111240, 12),
                                                                                  (1569, 111240, 13),
                                                                                  (1759, 111240, 14),
                                                                                  (1950, 111240, 15),
                                                                                  (38, 111324, 3),
                                                                                  (101, 111324, 4),
                                                                                  (355, 111324, 5),
                                                                                  (478, 111324, 6),
                                                                                  (624, 111324, 7),
                                                                                  (789, 111324, 8),
                                                                                  (953, 111324, 9),
                                                                                  (1128, 111324, 10),
                                                                                  (1306, 111324, 12),
                                                                                  (1496, 111324, 13),
                                                                                  (1686, 111324, 14),
                                                                                  (1877, 111324, 15),
                                                                                  (39, 111414, 3),
                                                                                  (102, 111414, 4),
                                                                                  (356, 111414, 5),
                                                                                  (479, 111414, 6),
                                                                                  (625, 111414, 7),
                                                                                  (790, 111414, 8),
                                                                                  (954, 111414, 9),
                                                                                  (1129, 111414, 10),
                                                                                  (1307, 111414, 12),
                                                                                  (1497, 111414, 13),
                                                                                  (1687, 111414, 14),
                                                                                  (1878, 111414, 15),
                                                                                  (46, 111415, 3),
                                                                                  (109, 111415, 4),
                                                                                  (357, 111415, 5),
                                                                                  (480, 111415, 6),
                                                                                  (626, 111415, 7),
                                                                                  (791, 111415, 8),
                                                                                  (955, 111415, 9),
                                                                                  (1130, 111415, 10),
                                                                                  (1308, 111415, 12),
                                                                                  (1498, 111415, 13),
                                                                                  (1688, 111415, 14),
                                                                                  (1879, 111415, 15),
                                                                                  (41, 111611, 3),
                                                                                  (104, 111611, 4),
                                                                                  (358, 111611, 5),
                                                                                  (481, 111611, 6),
                                                                                  (627, 111611, 7),
                                                                                  (792, 111611, 8),
                                                                                  (956, 111611, 9),
                                                                                  (1131, 111611, 10),
                                                                                  (1309, 111611, 12),
                                                                                  (1499, 111611, 13),
                                                                                  (1689, 111611, 14),
                                                                                  (1880, 111611, 15),
                                                                                  (17, 111712, 3),
                                                                                  (80, 111712, 4),
                                                                                  (359, 111712, 5),
                                                                                  (482, 111712, 6),
                                                                                  (628, 111712, 7),
                                                                                  (793, 111712, 8),
                                                                                  (957, 111712, 9),
                                                                                  (1132, 111712, 10),
                                                                                  (1310, 111712, 12),
                                                                                  (1500, 111712, 13),
                                                                                  (1690, 111712, 14),
                                                                                  (1881, 111712, 15),
                                                                                  (4, 111720, 3),
                                                                                  (67, 111720, 4),
                                                                                  (360, 111720, 5),
                                                                                  (483, 111720, 6),
                                                                                  (629, 111720, 7),
                                                                                  (794, 111720, 8),
                                                                                  (958, 111720, 9),
                                                                                  (1133, 111720, 10),
                                                                                  (1311, 111720, 12),
                                                                                  (1501, 111720, 13),
                                                                                  (1691, 111720, 14),
                                                                                  (1882, 111720, 15),
                                                                                  (152, 111827, 4),
                                                                                  (361, 111827, 5),
                                                                                  (484, 111827, 6),
                                                                                  (630, 111827, 7),
                                                                                  (795, 111827, 8),
                                                                                  (959, 111827, 9),
                                                                                  (1134, 111827, 10),
                                                                                  (1312, 111827, 12),
                                                                                  (1502, 111827, 13),
                                                                                  (1692, 111827, 14),
                                                                                  (1883, 111827, 15),
                                                                                  (148, 111839, 4),
                                                                                  (362, 111839, 5),
                                                                                  (485, 111839, 6),
                                                                                  (631, 111839, 7),
                                                                                  (796, 111839, 8),
                                                                                  (960, 111839, 9),
                                                                                  (1135, 111839, 10),
                                                                                  (1313, 111839, 12),
                                                                                  (1503, 111839, 13),
                                                                                  (1693, 111839, 14),
                                                                                  (1884, 111839, 15),
                                                                                  (48, 111849, 3),
                                                                                  (111, 111849, 4),
                                                                                  (363, 111849, 5),
                                                                                  (486, 111849, 6),
                                                                                  (632, 111849, 7),
                                                                                  (797, 111849, 8),
                                                                                  (961, 111849, 9),
                                                                                  (1136, 111849, 10),
                                                                                  (1314, 111849, 12),
                                                                                  (1504, 111849, 13),
                                                                                  (1694, 111849, 14),
                                                                                  (1885, 111849, 15),
                                                                                  (50, 111850, 3),
                                                                                  (113, 111850, 4),
                                                                                  (364, 111850, 5),
                                                                                  (487, 111850, 6),
                                                                                  (633, 111850, 7),
                                                                                  (798, 111850, 8),
                                                                                  (962, 111850, 9),
                                                                                  (1137, 111850, 10),
                                                                                  (1315, 111850, 12),
                                                                                  (1505, 111850, 13),
                                                                                  (1695, 111850, 14),
                                                                                  (1886, 111850, 15),
                                                                                  (53, 111851, 3),
                                                                                  (116, 111851, 4),
                                                                                  (365, 111851, 5),
                                                                                  (488, 111851, 6),
                                                                                  (634, 111851, 7),
                                                                                  (799, 111851, 8),
                                                                                  (963, 111851, 9),
                                                                                  (1138, 111851, 10),
                                                                                  (1316, 111851, 12),
                                                                                  (1506, 111851, 13),
                                                                                  (1696, 111851, 14),
                                                                                  (1887, 111851, 15),
                                                                                  (54, 111852, 3),
                                                                                  (117, 111852, 4),
                                                                                  (366, 111852, 5),
                                                                                  (489, 111852, 6),
                                                                                  (635, 111852, 7),
                                                                                  (800, 111852, 8),
                                                                                  (964, 111852, 9),
                                                                                  (1139, 111852, 10),
                                                                                  (1317, 111852, 12),
                                                                                  (1507, 111852, 13),
                                                                                  (1697, 111852, 14),
                                                                                  (1888, 111852, 15),
                                                                                  (61, 111853, 3),
                                                                                  (124, 111853, 4),
                                                                                  (367, 111853, 5),
                                                                                  (490, 111853, 6),
                                                                                  (636, 111853, 7),
                                                                                  (801, 111853, 8),
                                                                                  (965, 111853, 9),
                                                                                  (1140, 111853, 10),
                                                                                  (1318, 111853, 12),
                                                                                  (1508, 111853, 13),
                                                                                  (1698, 111853, 14),
                                                                                  (1889, 111853, 15),
                                                                                  (155, 111862, 4),
                                                                                  (368, 111862, 5),
                                                                                  (491, 111862, 6),
                                                                                  (637, 111862, 7),
                                                                                  (802, 111862, 8),
                                                                                  (966, 111862, 9),
                                                                                  (1141, 111862, 10),
                                                                                  (1319, 111862, 12),
                                                                                  (1509, 111862, 13),
                                                                                  (1699, 111862, 14),
                                                                                  (1890, 111862, 15),
                                                                                  (521, 111863, 6),
                                                                                  (667, 111863, 7),
                                                                                  (803, 111863, 8),
                                                                                  (996, 111863, 9),
                                                                                  (1171, 111863, 10),
                                                                                  (1349, 111863, 12),
                                                                                  (1539, 111863, 13),
                                                                                  (1729, 111863, 14),
                                                                                  (1920, 111863, 15),
                                                                                  (1026, 111864, 9),
                                                                                  (1203, 111864, 10),
                                                                                  (1381, 111864, 12),
                                                                                  (1571, 111864, 13),
                                                                                  (1761, 111864, 14),
                                                                                  (1952, 111864, 15),
                                                                                  (2, 900003, 3),
                                                                                  (65, 900003, 4),
                                                                                  (369, 900003, 5),
                                                                                  (492, 900003, 6),
                                                                                  (638, 900003, 7),
                                                                                  (804, 900003, 8),
                                                                                  (967, 900003, 9),
                                                                                  (1142, 900003, 10),
                                                                                  (1320, 900003, 12),
                                                                                  (1510, 900003, 13),
                                                                                  (1700, 900003, 14),
                                                                                  (1891, 900003, 15),
                                                                                  (370, 900007, 5),
                                                                                  (493, 900007, 6),
                                                                                  (639, 900007, 7),
                                                                                  (805, 900007, 8),
                                                                                  (968, 900007, 9),
                                                                                  (1143, 900007, 10),
                                                                                  (1321, 900007, 12),
                                                                                  (1511, 900007, 13),
                                                                                  (1701, 900007, 14),
                                                                                  (1892, 900007, 15),
                                                                                  (371, 900008, 5),
                                                                                  (494, 900008, 6),
                                                                                  (640, 900008, 7),
                                                                                  (806, 900008, 8),
                                                                                  (969, 900008, 9),
                                                                                  (1144, 900008, 10),
                                                                                  (1322, 900008, 12),
                                                                                  (1512, 900008, 13),
                                                                                  (1702, 900008, 14),
                                                                                  (1893, 900008, 15),
                                                                                  (495, 900009, 6),
                                                                                  (641, 900009, 7),
                                                                                  (807, 900009, 8),
                                                                                  (970, 900009, 9),
                                                                                  (1145, 900009, 10),
                                                                                  (1323, 900009, 12),
                                                                                  (1513, 900009, 13),
                                                                                  (1703, 900009, 14),
                                                                                  (1894, 900009, 15),
                                                                                  (496, 900010, 6),
                                                                                  (642, 900010, 7),
                                                                                  (808, 900010, 8),
                                                                                  (971, 900010, 9),
                                                                                  (1146, 900010, 10),
                                                                                  (1324, 900010, 12),
                                                                                  (1514, 900010, 13),
                                                                                  (1704, 900010, 14),
                                                                                  (1895, 900010, 15),
                                                                                  (497, 900011, 6),
                                                                                  (643, 900011, 7),
                                                                                  (809, 900011, 8),
                                                                                  (972, 900011, 9),
                                                                                  (1147, 900011, 10),
                                                                                  (1325, 900011, 12),
                                                                                  (1515, 900011, 13),
                                                                                  (1705, 900011, 14),
                                                                                  (1896, 900011, 15),
                                                                                  (498, 900012, 6),
                                                                                  (644, 900012, 7),
                                                                                  (810, 900012, 8),
                                                                                  (973, 900012, 9),
                                                                                  (1148, 900012, 10),
                                                                                  (1326, 900012, 12),
                                                                                  (1516, 900012, 13),
                                                                                  (1706, 900012, 14),
                                                                                  (1897, 900012, 15),
                                                                                  (499, 900013, 6),
                                                                                  (645, 900013, 7),
                                                                                  (811, 900013, 8),
                                                                                  (974, 900013, 9),
                                                                                  (1149, 900013, 10),
                                                                                  (1327, 900013, 12),
                                                                                  (1517, 900013, 13),
                                                                                  (1707, 900013, 14),
                                                                                  (1898, 900013, 15),
                                                                                  (500, 900014, 6),
                                                                                  (646, 900014, 7),
                                                                                  (812, 900014, 8),
                                                                                  (975, 900014, 9),
                                                                                  (1150, 900014, 10),
                                                                                  (1328, 900014, 12),
                                                                                  (1518, 900014, 13),
                                                                                  (1708, 900014, 14),
                                                                                  (1899, 900014, 15),
                                                                                  (501, 900015, 6),
                                                                                  (647, 900015, 7),
                                                                                  (813, 900015, 8),
                                                                                  (976, 900015, 9),
                                                                                  (1151, 900015, 10),
                                                                                  (1329, 900015, 12),
                                                                                  (1519, 900015, 13),
                                                                                  (1709, 900015, 14),
                                                                                  (1900, 900015, 15),
                                                                                  (502, 900016, 6),
                                                                                  (648, 900016, 7),
                                                                                  (814, 900016, 8),
                                                                                  (977, 900016, 9),
                                                                                  (1152, 900016, 10),
                                                                                  (1330, 900016, 12),
                                                                                  (1520, 900016, 13),
                                                                                  (1710, 900016, 14),
                                                                                  (1901, 900016, 15),
                                                                                  (503, 900017, 6),
                                                                                  (649, 900017, 7),
                                                                                  (815, 900017, 8),
                                                                                  (978, 900017, 9),
                                                                                  (1153, 900017, 10),
                                                                                  (1331, 900017, 12),
                                                                                  (1521, 900017, 13),
                                                                                  (1711, 900017, 14),
                                                                                  (1902, 900017, 15),
                                                                                  (504, 900018, 6),
                                                                                  (650, 900018, 7),
                                                                                  (816, 900018, 8),
                                                                                  (979, 900018, 9),
                                                                                  (1154, 900018, 10),
                                                                                  (1332, 900018, 12),
                                                                                  (1522, 900018, 13),
                                                                                  (1712, 900018, 14),
                                                                                  (1903, 900018, 15),
                                                                                  (505, 900019, 6),
                                                                                  (651, 900019, 7),
                                                                                  (817, 900019, 8),
                                                                                  (980, 900019, 9),
                                                                                  (1155, 900019, 10),
                                                                                  (1333, 900019, 12),
                                                                                  (1523, 900019, 13),
                                                                                  (1713, 900019, 14),
                                                                                  (1904, 900019, 15),
                                                                                  (506, 900020, 6),
                                                                                  (652, 900020, 7),
                                                                                  (818, 900020, 8),
                                                                                  (981, 900020, 9),
                                                                                  (1156, 900020, 10),
                                                                                  (1334, 900020, 12),
                                                                                  (1524, 900020, 13),
                                                                                  (1714, 900020, 14),
                                                                                  (1905, 900020, 15),
                                                                                  (507, 900021, 6),
                                                                                  (653, 900021, 7),
                                                                                  (819, 900021, 8),
                                                                                  (982, 900021, 9),
                                                                                  (1157, 900021, 10),
                                                                                  (1335, 900021, 12),
                                                                                  (1525, 900021, 13),
                                                                                  (1715, 900021, 14),
                                                                                  (1906, 900021, 15),
                                                                                  (508, 900022, 6),
                                                                                  (654, 900022, 7),
                                                                                  (820, 900022, 8),
                                                                                  (983, 900022, 9),
                                                                                  (1158, 900022, 10),
                                                                                  (1336, 900022, 12),
                                                                                  (1526, 900022, 13),
                                                                                  (1716, 900022, 14),
                                                                                  (1907, 900022, 15),
                                                                                  (509, 900023, 6),
                                                                                  (655, 900023, 7),
                                                                                  (821, 900023, 8),
                                                                                  (984, 900023, 9),
                                                                                  (1159, 900023, 10),
                                                                                  (1337, 900023, 12),
                                                                                  (1527, 900023, 13),
                                                                                  (1717, 900023, 14),
                                                                                  (1908, 900023, 15),
                                                                                  (510, 900024, 6),
                                                                                  (656, 900024, 7),
                                                                                  (822, 900024, 8),
                                                                                  (985, 900024, 9),
                                                                                  (1160, 900024, 10),
                                                                                  (1338, 900024, 12),
                                                                                  (1528, 900024, 13),
                                                                                  (1718, 900024, 14),
                                                                                  (1909, 900024, 15),
                                                                                  (511, 900025, 6),
                                                                                  (657, 900025, 7),
                                                                                  (823, 900025, 8),
                                                                                  (986, 900025, 9),
                                                                                  (1161, 900025, 10),
                                                                                  (1339, 900025, 12),
                                                                                  (1529, 900025, 13),
                                                                                  (1719, 900025, 14),
                                                                                  (1910, 900025, 15),
                                                                                  (512, 900026, 6),
                                                                                  (658, 900026, 7),
                                                                                  (824, 900026, 8),
                                                                                  (987, 900026, 9),
                                                                                  (1162, 900026, 10),
                                                                                  (1340, 900026, 12),
                                                                                  (1530, 900026, 13),
                                                                                  (1720, 900026, 14),
                                                                                  (1911, 900026, 15),
                                                                                  (513, 900027, 6),
                                                                                  (659, 900027, 7),
                                                                                  (825, 900027, 8),
                                                                                  (988, 900027, 9),
                                                                                  (1163, 900027, 10),
                                                                                  (1341, 900027, 12),
                                                                                  (1531, 900027, 13),
                                                                                  (1721, 900027, 14),
                                                                                  (1912, 900027, 15),
                                                                                  (514, 900028, 6),
                                                                                  (660, 900028, 7),
                                                                                  (826, 900028, 8),
                                                                                  (989, 900028, 9),
                                                                                  (1164, 900028, 10),
                                                                                  (1342, 900028, 12),
                                                                                  (1532, 900028, 13),
                                                                                  (1722, 900028, 14),
                                                                                  (1913, 900028, 15),
                                                                                  (515, 900029, 6),
                                                                                  (661, 900029, 7),
                                                                                  (827, 900029, 8),
                                                                                  (990, 900029, 9),
                                                                                  (1165, 900029, 10),
                                                                                  (1343, 900029, 12),
                                                                                  (1533, 900029, 13),
                                                                                  (1723, 900029, 14),
                                                                                  (1914, 900029, 15),
                                                                                  (516, 900030, 6),
                                                                                  (662, 900030, 7),
                                                                                  (828, 900030, 8),
                                                                                  (991, 900030, 9),
                                                                                  (1166, 900030, 10),
                                                                                  (1344, 900030, 12),
                                                                                  (1534, 900030, 13),
                                                                                  (1724, 900030, 14),
                                                                                  (1915, 900030, 15),
                                                                                  (517, 900031, 6),
                                                                                  (663, 900031, 7),
                                                                                  (829, 900031, 8),
                                                                                  (992, 900031, 9),
                                                                                  (1167, 900031, 10),
                                                                                  (1345, 900031, 12),
                                                                                  (1535, 900031, 13),
                                                                                  (1725, 900031, 14),
                                                                                  (1916, 900031, 15),
                                                                                  (518, 900032, 6),
                                                                                  (664, 900032, 7),
                                                                                  (830, 900032, 8),
                                                                                  (993, 900032, 9),
                                                                                  (1168, 900032, 10),
                                                                                  (1346, 900032, 12),
                                                                                  (1536, 900032, 13),
                                                                                  (1726, 900032, 14),
                                                                                  (1917, 900032, 15),
                                                                                  (519, 900033, 6),
                                                                                  (665, 900033, 7),
                                                                                  (831, 900033, 8),
                                                                                  (994, 900033, 9),
                                                                                  (1169, 900033, 10),
                                                                                  (1347, 900033, 12),
                                                                                  (1537, 900033, 13),
                                                                                  (1727, 900033, 14),
                                                                                  (1918, 900033, 15),
                                                                                  (520, 900034, 6),
                                                                                  (666, 900034, 7),
                                                                                  (832, 900034, 8),
                                                                                  (995, 900034, 9),
                                                                                  (1170, 900034, 10),
                                                                                  (1348, 900034, 12),
                                                                                  (1538, 900034, 13),
                                                                                  (1728, 900034, 14),
                                                                                  (1919, 900034, 15),
                                                                                  (522, 900035, 6),
                                                                                  (668, 900035, 7),
                                                                                  (833, 900035, 8),
                                                                                  (997, 900035, 9),
                                                                                  (1172, 900035, 10),
                                                                                  (1350, 900035, 12),
                                                                                  (1540, 900035, 13),
                                                                                  (1730, 900035, 14),
                                                                                  (1921, 900035, 15),
                                                                                  (669, 900036, 7),
                                                                                  (834, 900036, 8),
                                                                                  (998, 900036, 9),
                                                                                  (1173, 900036, 10),
                                                                                  (1351, 900036, 12),
                                                                                  (1541, 900036, 13),
                                                                                  (1731, 900036, 14),
                                                                                  (1922, 900036, 15),
                                                                                  (670, 900037, 7),
                                                                                  (835, 900037, 8),
                                                                                  (999, 900037, 9),
                                                                                  (1174, 900037, 10),
                                                                                  (1352, 900037, 12),
                                                                                  (1542, 900037, 13),
                                                                                  (1732, 900037, 14),
                                                                                  (1923, 900037, 15),
                                                                                  (671, 900038, 7),
                                                                                  (836, 900038, 8),
                                                                                  (1000, 900038, 9),
                                                                                  (1175, 900038, 10),
                                                                                  (1353, 900038, 12),
                                                                                  (1543, 900038, 13),
                                                                                  (1733, 900038, 14),
                                                                                  (1924, 900038, 15),
                                                                                  (672, 900039, 7),
                                                                                  (837, 900039, 8),
                                                                                  (1001, 900039, 9),
                                                                                  (1176, 900039, 10),
                                                                                  (1354, 900039, 12),
                                                                                  (1544, 900039, 13),
                                                                                  (1734, 900039, 14),
                                                                                  (1925, 900039, 15),
                                                                                  (677, 900040, 7),
                                                                                  (838, 900040, 8),
                                                                                  (1006, 900040, 9),
                                                                                  (1181, 900040, 10),
                                                                                  (1359, 900040, 12),
                                                                                  (1549, 900040, 13),
                                                                                  (1739, 900040, 14),
                                                                                  (1930, 900040, 15),
                                                                                  (678, 900041, 7),
                                                                                  (839, 900041, 8),
                                                                                  (1007, 900041, 9),
                                                                                  (1182, 900041, 10),
                                                                                  (1360, 900041, 12),
                                                                                  (1550, 900041, 13),
                                                                                  (1740, 900041, 14),
                                                                                  (1931, 900041, 15),
                                                                                  (680, 900042, 7),
                                                                                  (840, 900042, 8),
                                                                                  (1008, 900042, 9),
                                                                                  (1183, 900042, 10),
                                                                                  (1361, 900042, 12),
                                                                                  (1551, 900042, 13),
                                                                                  (1741, 900042, 14),
                                                                                  (1932, 900042, 15),
                                                                                  (681, 900043, 7),
                                                                                  (841, 900043, 8),
                                                                                  (1009, 900043, 9),
                                                                                  (1184, 900043, 10),
                                                                                  (1362, 900043, 12),
                                                                                  (1552, 900043, 13),
                                                                                  (1742, 900043, 14),
                                                                                  (1933, 900043, 15),
                                                                                  (682, 900044, 7),
                                                                                  (842, 900044, 8),
                                                                                  (1010, 900044, 9),
                                                                                  (1185, 900044, 10),
                                                                                  (1363, 900044, 12),
                                                                                  (1553, 900044, 13),
                                                                                  (1743, 900044, 14),
                                                                                  (1934, 900044, 15),
                                                                                  (683, 900045, 7),
                                                                                  (843, 900045, 8),
                                                                                  (1011, 900045, 9),
                                                                                  (1186, 900045, 10),
                                                                                  (1364, 900045, 12),
                                                                                  (1554, 900045, 13),
                                                                                  (1744, 900045, 14),
                                                                                  (1935, 900045, 15),
                                                                                  (846, 900046, 8),
                                                                                  (1012, 900046, 9),
                                                                                  (1187, 900046, 10),
                                                                                  (1365, 900046, 12),
                                                                                  (1555, 900046, 13),
                                                                                  (1745, 900046, 14),
                                                                                  (1936, 900046, 15),
                                                                                  (847, 900047, 8),
                                                                                  (1013, 900047, 9),
                                                                                  (1188, 900047, 10),
                                                                                  (1366, 900047, 12),
                                                                                  (1556, 900047, 13),
                                                                                  (1746, 900047, 14),
                                                                                  (1937, 900047, 15),
                                                                                  (850, 900048, 8),
                                                                                  (1014, 900048, 9),
                                                                                  (1189, 900048, 10),
                                                                                  (1367, 900048, 12),
                                                                                  (1557, 900048, 13),
                                                                                  (1747, 900048, 14),
                                                                                  (1938, 900048, 15),
                                                                                  (851, 900049, 8),
                                                                                  (1015, 900049, 9),
                                                                                  (1190, 900049, 10),
                                                                                  (1368, 900049, 12),
                                                                                  (1558, 900049, 13),
                                                                                  (1748, 900049, 14),
                                                                                  (1939, 900049, 15),
                                                                                  (1016, 900050, 9),
                                                                                  (1191, 900050, 10),
                                                                                  (1369, 900050, 12),
                                                                                  (1559, 900050, 13),
                                                                                  (1749, 900050, 14),
                                                                                  (1940, 900050, 15),
                                                                                  (1017, 900051, 9),
                                                                                  (1192, 900051, 10),
                                                                                  (1370, 900051, 12),
                                                                                  (1560, 900051, 13),
                                                                                  (1750, 900051, 14),
                                                                                  (1941, 900051, 15),
                                                                                  (1021, 900052, 9),
                                                                                  (1196, 900052, 10),
                                                                                  (1374, 900052, 12),
                                                                                  (1564, 900052, 13),
                                                                                  (1754, 900052, 14),
                                                                                  (1945, 900052, 15),
                                                                                  (1022, 900053, 9),
                                                                                  (1197, 900053, 10),
                                                                                  (1375, 900053, 12),
                                                                                  (1565, 900053, 13),
                                                                                  (1755, 900053, 14),
                                                                                  (1946, 900053, 15),
                                                                                  (1204, 900054, 10),
                                                                                  (1382, 900054, 12),
                                                                                  (1572, 900054, 13),
                                                                                  (1762, 900054, 14),
                                                                                  (1953, 900054, 15),
                                                                                  (1383, 900055, 12),
                                                                                  (1573, 900055, 13),
                                                                                  (1763, 900055, 14),
                                                                                  (1954, 900055, 15),
                                                                                  (1384, 900056, 12),
                                                                                  (1574, 900056, 13),
                                                                                  (1764, 900056, 14),
                                                                                  (1955, 900056, 15),
                                                                                  (1385, 900057, 12),
                                                                                  (1575, 900057, 13),
                                                                                  (1765, 900057, 14),
                                                                                  (1956, 900057, 15),
                                                                                  (1386, 900058, 12),
                                                                                  (1576, 900058, 13),
                                                                                  (1766, 900058, 14),
                                                                                  (1957, 900058, 15),
                                                                                  (1387, 900059, 12),
                                                                                  (1577, 900059, 13),
                                                                                  (1767, 900059, 14),
                                                                                  (1958, 900059, 15),
                                                                                  (1388, 900060, 12),
                                                                                  (1578, 900060, 13),
                                                                                  (1768, 900060, 14),
                                                                                  (1959, 900060, 15),
                                                                                  (1389, 900061, 12),
                                                                                  (1579, 900061, 13),
                                                                                  (1769, 900061, 14),
                                                                                  (1960, 900061, 15),
                                                                                  (1390, 900062, 12),
                                                                                  (1580, 900062, 13),
                                                                                  (1770, 900062, 14),
                                                                                  (1961, 900062, 15),
                                                                                  (1393, 900066, 12),
                                                                                  (1583, 900066, 13),
                                                                                  (1773, 900066, 14),
                                                                                  (1964, 900066, 15),
                                                                                  (1394, 900067, 12),
                                                                                  (1584, 900067, 13),
                                                                                  (1774, 900067, 14),
                                                                                  (1965, 900067, 15),
                                                                                  (1775, 900068, 14),
                                                                                  (1980, 900068, 15),
                                                                                  (1968, 900069, 15),
                                                                                  (1969, 900080, 15),
                                                                                  (1979, 900098, 15),
                                                                                  (1978, 900099, 15),
                                                                                  (1967, 900112, 15),
                                                                                  (1966, 900113, 15),
                                                                                  (1970, 900114, 15),
                                                                                  (1976, 900115, 15),
                                                                                  (1977, 900116, 15);
SET FOREIGN_KEY_CHECKS=1;

INSERT INTO `joven_evaluacion_planilla_anteriors` (`id`, `nombre`, `evaluacion_subgrupo_id`) VALUES
    (1, 'No obtuvo subsidio en el año anterior', 17);

INSERT INTO `joven_evaluacion_planilla_anterior_maxs` (`joven_evaluacion_planilla_id`, `id`, `joven_evaluacion_planilla_anterior_id`, `maximo`, `evaluacion_grupo_id`, `minimo`, `tope`) VALUES
                                                                                                                                                              (7, 7, 1, 2, 53, 2, 2),
                                                                                                                                                              (8, 8, 1, 2, 53, 2, 2),
                                                                                                                                                              (9, 9, 1, 5, 84, 5, 5),
                                                                                                                                                              (10, 10, 1, 5, 84, 5, 5),
                                                                                                                                                              (11, 11, 1, 5, 84, 5, 5),
                                                                                                                                                              (12, 12, 1, 5, 84, 5, 5),
                                                                                                                                                              (13, 13, 1, 5, 84, 5, 5);


SET FOREIGN_KEY_CHECKS=0;
INSERT INTO `joven_evaluacion_planilla_justificacions` (`id`, `nombre`, `evaluacion_subgrupo_id`) VALUES
                                                                                                                         (1, 'Se evaluará la relación entre lo solicitado y la relevancia de la investigación que se pretende promover.', NULL),
                                                                                                                         (2, 'Se evaluará la justificación de los fondos solicitados y su relación con las actividades de I+D planteadas en el marco del proyecto acreditado.', NULL);
SET FOREIGN_KEY_CHECKS=1;

INSERT INTO `joven_evaluacion_planilla_justificacion_maxs` (`joven_evaluacion_planilla_id`, `id`, `joven_evaluacion_planilla_justificacion_id`, `maximo`, `evaluacion_grupo_id`, `minimo`, `tope`) VALUES
                                                                                                                                                                             (1, 1, 1, 0, 34, 0, 8),
                                                                                                                                                                             (2, 2, 1, 0, 36, 0, 15),
                                                                                                                                                                             (3, 3, 1, 0, 36, 0, 15),
                                                                                                                                                                             (4, 4, 2, 0, 36, 0, 15),
                                                                                                                                                                             (5, 5, 2, 0, 36, 0, 15),
                                                                                                                                                                             (6, 6, 2, 0, 36, 0, 15),
                                                                                                                                                                             (7, 7, 2, 0, 55, 0, 15),
                                                                                                                                                                             (8, 8, 2, 0, 55, 0, 15),
                                                                                                                                                                             (9, 9, 2, 0, 55, 0, 15),
                                                                                                                                                                             (10, 10, 2, 0, 55, 0, 15),
                                                                                                                                                                             (11, 11, 2, 0, 55, 0, 15),
                                                                                                                                                                             (12, 12, 2, 0, 55, 0, 15),
                                                                                                                                                                             (13, 13, 2, 0, 55, 0, 15);




############### Jovenes con los campos text

SELECT solicitudjovenes.`cd_solicitud` as id,
       solicitudjovenes.ds_observaciones as observaciones, solicitudjovenes.ds_justificacion as justificacion, solicitudjovenes.ds_objetivo as objetivo
FROM solicitudjovenes;


############### Jovenes sin los campos text
SELECT solicitudjovenes.`cd_solicitud` as id,solicitudjovenes.`cd_docente` as investigador_id,solicitudjovenes.`cd_periodo` as periodo_id, estado.ds_estado as estado, solicitudjovenes.ds_mail as email,
       solicitudjovenes.bl_notificacion as notificacion, solicitudjovenes.nu_telefono as telefono, CASE solicitudjovenes.dt_fecha
                                                                                                       WHEN '0000-00-00' THEN null
                                                                                                       ELSE solicitudjovenes.dt_fecha END as fecha,
       CASE solicitudjovenes.dt_nacimiento WHEN '0000-00-00' THEN null ELSE solicitudjovenes.dt_nacimiento END as nacimiento, solicitudjovenes.ds_calle as calle,
       solicitudjovenes.nu_nro as nro, solicitudjovenes.nu_piso as piso, solicitudjovenes.ds_depto as depto, solicitudjovenes.nu_cp as cp, solicitudjovenes.cd_titulogrado as titulo_id,
       CASE solicitudjovenes.dt_egresogrado WHEN '0000-00-00' THEN null ELSE solicitudjovenes.dt_egresogrado END as egresogrado,
       solicitudjovenes.cd_tituloposgrado as tituloposgrado_id, CASE solicitudjovenes.dt_egresoposgrado WHEN '0000-00-00' THEN null ELSE solicitudjovenes.dt_egresoposgrado END as egresoposgrado,
       solicitudjovenes.bl_doctorado as doctorado, solicitudjovenes.cd_unidad as unidad_id,
       CASE solicitudjovenes.`cd_cargo`
           WHEN '6' THEN null
           ELSE solicitudjovenes.cd_cargo END as cargo_id,
       CASE deddoc.`ds_deddoc`
           WHEN 's/d' THEN null
           WHEN 'SI-1' THEN 'Simple'
           WHEN 'SE-1' THEN 'Semi Exclusiva'
           ELSE deddoc.ds_deddoc END as deddoc, CASE solicitudjovenes.`cd_facultad`
                                                    WHEN '574' THEN null
                                                    ELSE cd_facultad END as facultad_id, solicitudjovenes.cd_facultadplanilla as facultadplanilla_id, solicitudjovenes.bl_director as director,
       CASE solicitudjovenes.`cd_carrerainv`
           WHEN '11' THEN null
           WHEN '10' THEN null
           ELSE solicitudjovenes.cd_carrerainv END as carrerainv_id,
       CASE solicitudjovenes.`cd_organismo`
           WHEN '7' THEN null
           else solicitudjovenes.cd_organismo END as organismo_id, CASE solicitudjovenes.dt_ingreso WHEN '0000-00-00' THEN null ELSE solicitudjovenes.dt_ingreso END as ingreso_carrera,
       solicitudjovenes.cd_unidadcarrera as unidadcarrera_id, solicitudjovenes.cd_unidadbeca as unidadbeca_id, solicitudjovenes.nu_puntaje as puntaje, solicitudjovenes.nu_diferencia as diferencia,
       solicitudjovenes.ds_curriculum as curriculum,
       solicitudjovenes.ds_disciplina as disciplina
FROM solicitudjovenes
         LEFT JOIN cyt_solicitudjovenes_estado ON solicitudjovenes.cd_solicitud = cyt_solicitudjovenes_estado.solicitud_oid AND cyt_solicitudjovenes_estado.fechaHasta is NULL
         LEFT JOIN estado ON cyt_solicitudjovenes_estado.estado_oid = estado.cd_estado
         LEFT JOIN `deddoc` ON `solicitudjovenes`.`cd_deddoc` = `deddoc`.`cd_deddoc`;


######################### jovenes estados
SELECT cyt_solicitudjovenes_estado.solicitud_oid as joven_id,
       CASE cyt_solicitudjovenes_estado.user_oid
           WHEN 1 THEN '2'
           ELSE NULL END as user_id, cyt_user.ds_name as user_name,

       CASE cyt_solicitudjovenes_estado.estado_oid
           WHEN 1 THEN 'Creada'
           WHEN 2 THEN 'Recibida'
           WHEN 3 THEN 'Admitida'
           WHEN 4 THEN 'No Admitida'
           WHEN 5 THEN 'Otorgada-No-Rendida'
           WHEN 6 THEN 'En evaluación'
           WHEN 7 THEN 'No otorgada'
           WHEN 8 THEN 'Evaluada'
           WHEN 9 THEN 'Otorgada-Rendida'
           WHEN 10 THEN 'Otorgada-Renunciada'
           WHEN 11 THEN 'Retirada'
           WHEN 12 THEN 'Otorgada-Devuelta'

           END as estado,

       cyt_solicitudjovenes_estado.fechaDesde as desde, cyt_solicitudjovenes_estado.fechaHasta as hasta, cyt_solicitudjovenes_estado.motivo as comentarios
FROM cyt_solicitudjovenes_estado
         LEFT JOIN cyt_user ON cyt_solicitudjovenes_estado.user_oid = cyt_user.oid;





############### Jovenes becas actuales
SELECT solicitudjovenes.`cd_solicitud` as joven_id,CASE solicitudjovenes.ds_orgbeca
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

                                                       WHEN 'UNLP-CIN' THEN 'CIN'
                                                       WHEN 'CONSEJO INTERUNIVERSITARIO NACIONAL' THEN 'CIN'
                                                       WHEN 'COPNICET' THEN 'CONICET'
                                                       WHEN 'CIC PBA' THEN 'CIC'
                                                       WHEN 'CIC. PBA' THEN 'CIC'
                                                       WHEN 'DAAD' THEN 'OTRA'
                                                       WHEN 'COMISIÓN DE INVESTIGACIONES CIENTÍFICAS (CIC)' THEN 'CIC'
                                                       WHEN 'CONSEJO NAC.DE INVEST.CIENTIF.Y TECNICAS' THEN 'CONICET'
                                                       WHEN 'CICBA' THEN 'CIC'
                                                       WHEN 'Telefónica' THEN 'OTRA'
                                                       WHEN 'FONCIT' THEN 'OTRA'
                                                       ELSE solicitudjovenes.ds_orgbeca

    END AS institucion,
       CASE solicitudjovenes.ds_tipobeca
           WHEN 'POSTGRADO/DOCTORADO' THEN 'Beca doctoral'
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
           WHEN 'Postdoctorado' THEN 'Beca posdoctoral'
           WHEN 'BECA INTERNA DE POSTGRADO TIPO II' THEN 'TIPO II'
           WHEN 'POSGRADO TIPO I +POSGRADO TIPO II' THEN 'TIPO II'
           WHEN 'BECA INTERNA POSTGRADO TIPO II' THEN 'TIPO II'
           WHEN 'BECA DE POSGRADO INTERNA TIPO II' THEN 'TIPO II'
           WHEN 'BECA DE DOCTORADO TIPO II' THEN 'TIPO II'
           WHEN 'BECA DE RETENCIÓN DE POSGRADUADOS' THEN 'RETENCION DE POSTGRADUADO'
           WHEN 'RETENCIÓN DE POSTGRADUADOS FORMADOS POR LA UNLP' THEN 'RETENCION DE POSTGRADUADO'
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
           WHEN 'Inicial de Doctorado' THEN 'Beca inicial'
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
           WHEN 'POSTGRADO DOCTORAL (TIPO II)' THEN 'TIPO II'
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
           WHEN 'POSGRADO DE TIPO II' THEN 'TIPO II'
           WHEN 'A' THEN 'TIPO A'
           WHEN 'Cofinanciadas CIC-UNLP' THEN 'Beca Cofinanciada (UNLP-CIC)'
           WHEN 'Beca Doctoral Cofinanciada UNLP-CIC' THEN 'Beca Cofinanciada (UNLP-CIC)'
           ELSE solicitudjovenes.ds_tipobeca
           END AS beca, dt_becadesde as desde, dt_becahasta as hasta, bl_unlp as unlp, solicitudjovenes.cd_unidadbeca as unidad, '0' as agregada, '1' as actual, CONCAT(solicitudjovenes.ds_orgbeca,' - ',solicitudjovenes.ds_tipobeca) as original
FROM solicitudjovenes
WHERE (bl_becario = 1)
  AND ds_tipobeca != 'No declarado'
    AND ds_tipobeca != ''
    AND ds_tipobeca IS NOT NULL
    AND ds_orgbeca IS NOT NULL
ORDER BY ds_tipobeca;

############### Jovenes becas anteriores
SELECT solicitudjovenes.`cd_solicitud` as joven_id,CASE solicitudjovenesbeca.bl_unlp WHEN '1' THEN 'UNLP' ELSE
                                                    CASE
                                                        WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%CIN%' THEN 'CIN'
                                                    ELSE
                                                        CASE
                                                            WHEN solicitudjovenesbeca.ds_tipobeca = 'I' THEN 'CONICET'
                                                            WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%ESTÍMULO A LAS VOCACIONES CIENTÍFICAS%' THEN 'CIN'
                                                            WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%ESTIMULO A LA VOCACION CIENTIFICA%' THEN 'CIN'
                                                            WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%Estímulo a la vocación cientifica%' THEN 'CIN'
                                                            WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%estímulo a las investigaciones científicas%' THEN 'CIN'
                                                            WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%Estímulo a la Voacación Científica%' THEN 'CIN'
                                                            WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%EVC%' THEN 'CIN'
                                                           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%ANPCyT%' THEN 'ANPCyT'
                                                           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%INICIAL%' THEN 'ANPCyT'
                                                           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%AGENCIA%' THEN 'ANPCyT'
                                                           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%CIC%' THEN 'CIC'

                                                           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%COMISIÓN DE INVESTIGACIONES CIENTÍFICAS DE LA PROVINCIA DE BUENOS AIRES%' THEN 'CIC'

                                                           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%ENTRENAMIENTO%' THEN 'CIC'
                                                            WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%ENTREMANIENTO%' THEN 'CIC'
                                                           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%CONICET%' THEN 'CONICET'
                                                           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%TIPO I%' THEN 'CONICET'
                                                           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%TIPO  I%' THEN 'CONICET'
                                                            WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%PGTI%' THEN 'CONICET'
                                                           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%TIPO 1%' THEN 'CONICET'
                                                           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%TIPO 2%' THEN 'CONICET'
                                                           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%UNLP%' THEN 'UNLP'
                                                           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%CIN%' THEN 'CIN'
                                                           ELSE
                                                               CASE
                                                                   WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%ESTUDIO%' THEN 'CIC'
                                                               ELSE
                                                                   'OTRA'
                                                            END
                                                        END
    END END AS institucion,
       CASE
           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%TIPO II%' THEN 'TIPO II'
           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%TIPOII%' THEN 'TIPO II'
           WHEN solicitudjovenesbeca.ds_tipobeca = 'I' THEN 'TIPO I'
           WHEN solicitudjovenesbeca.ds_tipobeca = 'a' THEN 'TIPO A'
           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%ESTÍMULO A LAS VOCACIONES CIENTÍFICAS%' THEN 'EVC'
           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%ESTIMULO A LA VOCACION CIENTIFICA%' THEN 'EVC'
           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%Estímulo a la vocación cientifica%' THEN 'EVC'
           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%estímulo a las investigaciones científicas%' THEN 'EVC'
           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%Estímulos a las Vocaciones Científicas%' THEN 'EVC'
           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%Estímulo a Vocaciones Científicas%' THEN 'EVC'
           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%Vocaciones Científicas%' THEN 'EVC'
           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%Estímulo a la Voacación Científica%' THEN 'EVC'
           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%EVC%' THEN 'EVC'
           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%CIN%' THEN 'EVC'
           WHEN solicitudjovenesbeca.ds_tipobeca = 'TIPO A' THEN 'TIPO A'
           WHEN solicitudjovenesbeca.ds_tipobeca = 'TIPO A - Maestría' THEN 'Tipo A - Maestría'
           WHEN solicitudjovenesbeca.ds_tipobeca = 'Beca tipo A' THEN 'TIPO A'
           WHEN solicitudjovenesbeca.ds_tipobeca = 'TIPO B' THEN 'TIPO B'
           WHEN solicitudjovenesbeca.ds_tipobeca = 'Tipo B' THEN 'TIPO B'
           WHEN solicitudjovenesbeca.ds_tipobeca = 'TIPO B (MAESTRíA)' THEN 'TIPO B (MAESTRÍA)'
           WHEN solicitudjovenesbeca.ds_tipobeca = 'RETENCIÓN BECARIOS' THEN 'RETENCION DE POSTGRADUADO'
           WHEN solicitudjovenesbeca.ds_tipobeca = 'RETENCION DE POSTGRADUADO' THEN 'RETENCION DE POSTGRADUADO'
           WHEN solicitudjovenesbeca.ds_tipobeca = 'Beca Cofinanciada (UNLP-CIC)' THEN 'Beca Cofinanciada (UNLP-CIC)'
           ELSE
           CASE
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%TIPO I%' THEN 'TIPO I'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%TIPO  I%' THEN 'TIPO I'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%Tpo 1%' THEN 'TIPO I'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%TIPO 1%' THEN 'TIPO I'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%PGT1%' THEN 'TIPO I'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%PG TI%' THEN 'TIPO I'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%PGTI%' THEN 'TIPO I'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%I GRADO%' THEN 'TIPO I'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%I (CONICET)%' THEN 'TIPO I'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%I - CONICET%' THEN 'TIPO I'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%TIPO 2%' THEN 'TIPO II'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%TIPO2%' THEN 'TIPO II'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%FORMACIÓN SUPERIOR%' THEN 'Formación Superior'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%ENTRENAMIENTO%' THEN 'Beca de entrenamiento'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%Entreamiento%' THEN 'Beca de entrenamiento'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%ENTREMANIENTO%' THEN 'Beca de entrenamiento'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%PERFECCIONAMIENTO%' THEN 'BECA DE PERFECCIONAMIENTO'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%PERFECCIONAMINETO%' THEN 'BECA DE PERFECCIONAMIENTO'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%INICIACIóN%' THEN 'Iniciación'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%INICIAL%' THEN 'Beca inicial'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%INCIAL%' THEN 'Beca inicial'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%Inical%' THEN 'Beca inicial'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%ESTUDIO%' THEN 'Beca inicial'
               ELSE
                    CASE
                        WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%POSDOCTORAL%' THEN 'Beca posdoctoral'
                        WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%POSDOCTORADO%' THEN 'Beca posdoctoral'
                        WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%POSTDOCTORAL%' THEN 'Beca posdoctoral'
                        WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%POSTDOCTORAL%' THEN 'Beca posdoctoral'
                        WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%POSDOCT.%' THEN 'Beca posdoctoral'
                        WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%Postdoc%' THEN 'Beca posdoctoral'
                        WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%POSDTOCTORAL%' THEN 'Beca posdoctoral'
                        WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%POSTOCTORAL%' THEN 'Beca posdoctoral'
                        WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%POSDOC%' THEN 'Beca posdoctoral'
                    ELSE
                        CASE
                            WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%DOCTORAL%' THEN 'Beca doctoral'
                            WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%Doctotral%' THEN 'Beca doctoral'
                            WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%Doctoado%' THEN 'Beca doctoral'
                            WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%POSGRADO%' THEN 'Beca doctoral'
                            WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%POST-GRADO%' THEN 'Beca doctoral'
                            WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%POSTGRADO%' THEN 'Beca doctoral'
                            WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%DOCTORADO%' THEN 'Beca doctoral'
                            WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%Doctotal%' THEN 'Beca doctoral'
                            WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%SUPERIOR%' THEN 'Beca superior'
                            WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%INICIO%' THEN 'Beca inicial'
                        END
                    END
            END
           END AS beca, dt_desde as desde, dt_hasta as hasta, solicitudjovenesbeca.bl_unlp as unlp, bl_agregado as agregada, '0' as actual, solicitudjovenesbeca.ds_tipobeca as original
FROM solicitudjovenesbeca
    INNER JOIN solicitudjovenes ON solicitudjovenesbeca.cd_solicitud = solicitudjovenes.cd_solicitud;

############################jovenes proyectos actuales#####################################
SELECT
    solicitudjovenesproyecto.cd_solicitud as joven_id, solicitudjovenesproyecto.cd_proyecto as proyecto_id, solicitudjovenesproyecto.dt_hasta as hasta,
    solicitudjovenesproyecto.dt_desde as desde, solicitudjovenesproyecto.ds_codigo as codigo, solicitudjovenesproyecto.ds_director as director,
    solicitudjovenesproyecto.ds_titulo as titulo, solicitudjovenesproyecto.bl_agregado as agregado, "1" as actual
FROM
    solicitudjovenesproyecto
        JOIN
    solicitudjovenes ON solicitudjovenesproyecto.cd_solicitud = solicitudjovenes.cd_solicitud
        JOIN
        periodo ON solicitudjovenes.cd_periodo = periodo.cd_periodo
WHERE



periodo.ds_periodo BETWEEN YEAR(solicitudjovenesproyecto.dt_desde) AND IFNULL(YEAR(solicitudjovenesproyecto.dt_hasta), YEAR(solicitudjovenesproyecto.dt_desde));

############################jovenes proyectos anteriores#####################################
SELECT
    solicitudjovenesproyecto.cd_solicitud as joven_id, solicitudjovenesproyecto.cd_proyecto as proyecto_id, solicitudjovenesproyecto.dt_hasta as hasta,
    solicitudjovenesproyecto.dt_desde as desde, solicitudjovenesproyecto.ds_codigo as codigo, solicitudjovenesproyecto.ds_director as director,
    solicitudjovenesproyecto.ds_titulo as titulo, solicitudjovenesproyecto.bl_agregado as agregado, "0" as actual
FROM
    solicitudjovenesproyecto
        JOIN
    solicitudjovenes ON solicitudjovenesproyecto.cd_solicitud = solicitudjovenes.cd_solicitud
        JOIN
    periodo ON solicitudjovenes.cd_periodo = periodo.cd_periodo
WHERE periodo.ds_periodo < YEAR(solicitudjovenesproyecto.dt_desde) OR
    periodo.ds_periodo > IFNULL(YEAR(solicitudjovenesproyecto.dt_hasta), YEAR(solicitudjovenesproyecto.dt_desde));

############################jovenes presupuestos#####################################
SELECT presupuestojovenes.cd_solicitud as joven_id, presupuestojovenes.cd_tipopresupuesto as tipo_presupuesto_id, presupuestojovenes.ds_presupuesto as detalle,
       presupuestojovenes.nu_monto as monto, presupuestojovenes.dt_fecha as fecha
FROM `presupuestojovenes`;

############################jovenes evaluaciones#####################################
SELECT evaluacionjovenes.cd_evaluacion as id,evaluacionjovenes.cd_solicitud as joven_id,
       CASE evaluacionjovenes.cd_usuario
           WHEN 1 THEN '2'
           ELSE NULL END as user_id, cyt_user.ds_name as user_name, cyt_user.ds_username as user_cuil,

       CASE cyt_evaluacionjovenes_estado.estado_oid
           WHEN 1 THEN 'Creada'
           WHEN 2 THEN 'Recibida'
           WHEN 3 THEN 'Aceptada'
           WHEN 4 THEN 'Rechazada'

           WHEN 6 THEN 'En evaluación'

           WHEN 8 THEN 'Evaluada'


END as estado,

       evaluacionjovenes.dt_fecha as fecha, evaluacionjovenes.nu_puntaje as puntaje, evaluacionjovenes.bl_interno as interno, evaluacionjovenes.ds_observacion as observaciones
FROM evaluacionjovenes
         LEFT JOIN cyt_user ON evaluacionjovenes.cd_usuario = cyt_user.oid
        LEFT JOIN cyt_evaluacionjovenes_estado ON evaluacionjovenes.cd_evaluacion = cyt_evaluacionjovenes_estado.evaluacion_oid AND cyt_evaluacionjovenes_estado.fechaHasta is NULL;

######################### jovenes evaluaciones estados
SELECT cyt_evaluacionjovenes_estado.evaluacion_oid as joven_evaluacion_id,
       CASE cyt_evaluacionjovenes_estado.user_oid
           WHEN 1 THEN '2'
           ELSE NULL END as user_id, cyt_user.ds_name as user_name,

       CASE cyt_evaluacionjovenes_estado.estado_oid
           WHEN 1 THEN 'Creada'
           WHEN 2 THEN 'Recibida'
           WHEN 3 THEN 'Aceptada'
           WHEN 4 THEN 'Rechazada'

           WHEN 6 THEN 'En evaluación'

           WHEN 8 THEN 'Evaluada'

           END as estado,

       cyt_evaluacionjovenes_estado.fechaDesde as desde, cyt_evaluacionjovenes_estado.fechaHasta as hasta, cyt_evaluacionjovenes_estado.motivo as comentarios
FROM cyt_evaluacionjovenes_estado
         LEFT JOIN cyt_user ON cyt_evaluacionjovenes_estado.user_oid = cyt_user.oid;



